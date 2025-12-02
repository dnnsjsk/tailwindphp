<?php

declare(strict_types=1);

namespace TailwindPHP;

use TailwindPHP\Walk\WalkAction;
use function TailwindPHP\Walk\walk;
use function TailwindPHP\ValueParser\parse as parseValue;
use function TailwindPHP\ValueParser\toCss;
use function TailwindPHP\Utils\segment;
use function TailwindPHP\Utils\toKeyPath;
use function TailwindPHP\Utilities\withAlpha;

/**
 * CSS Functions
 *
 * Port of: packages/tailwindcss/src/css-functions.ts
 *
 * @port-deviation:errors TypeScript throws descriptive errors for invalid function usage.
 * PHP returns null to skip invalid functions silently (more lenient behavior).
 *
 * @port-deviation:dispatch TypeScript uses CSS_FUNCTIONS object with dynamic dispatch.
 * PHP uses individual function handlers (handleAlpha, handleSpacing, etc.) for clarity.
 *
 * @port-deviation:fallback-injection TypeScript has injectFallbackForInitialFallback().
 * PHP omits this complexity - fallbacks are joined directly.
 *
 * Handles CSS function substitution for:
 * - --alpha(color / opacity)
 * - --spacing(multiplier)
 * - --theme(path)
 * - theme(path) (legacy)
 */

/**
 * Pattern to detect theme function invocations.
 */
const THEME_FUNCTION_INVOCATION = '/(?:--alpha|--spacing|--theme|theme)\(/';

/**
 * Substitute CSS functions in the AST.
 *
 * @param array &$ast CSS AST
 * @param object $designSystem Design system instance
 * @return int Features flags
 */
function substituteFunctions(array &$ast, object $designSystem): int
{
    $features = FEATURE_NONE;

    walk($ast, function (&$node) use ($designSystem, &$features) {
        // Find all declaration values
        if ($node['kind'] === 'declaration' && isset($node['value']) && preg_match(THEME_FUNCTION_INVOCATION, $node['value'])) {
            $features |= FEATURE_AT_THEME;
            $node['value'] = substituteFunctionsInValue($node['value'], $node, $designSystem);
            return WalkAction::Skip;
        }

        // Find at-rules
        if ($node['kind'] === 'at-rule') {
            $name = $node['name'] ?? '';
            if (
                ($name === '@media' || $name === '@custom-media' || $name === '@container' || $name === '@supports') &&
                isset($node['params']) && preg_match(THEME_FUNCTION_INVOCATION, $node['params'])
            ) {
                $features |= FEATURE_AT_THEME;
                $node['params'] = substituteFunctionsInValue($node['params'], $node, $designSystem);
            }
        }

        return WalkAction::Continue;
    });

    return $features;
}

/**
 * Substitute CSS functions in a value string.
 *
 * @param string $value Value string
 * @param array $source Source AST node
 * @param object $designSystem Design system instance
 * @return string Substituted value
 */
function substituteFunctionsInValue(string $value, array $source, object $designSystem): string
{
    $ast = parseValue($value);

    walk($ast, function (&$node, $ctx) use ($designSystem, $source) {
        if ($node['kind'] !== 'function') {
            return WalkAction::Continue;
        }

        $funcName = $node['value'];

        // Handle theme() function (legacy)
        if ($funcName === 'theme') {
            $result = handleLegacyTheme($node, $designSystem);
            if ($result !== null) {
                return WalkAction::Replace(parseValue($result));
            }
        }

        // Handle --theme() function
        if ($funcName === '--theme') {
            $result = handleTheme($node, $source, $designSystem);
            if ($result !== null) {
                return WalkAction::Replace(parseValue($result));
            }
        }

        // Handle --spacing() function
        if ($funcName === '--spacing') {
            $result = handleSpacing($node, $designSystem);
            if ($result !== null) {
                return WalkAction::Replace(parseValue($result));
            }
        }

        // Handle --alpha() function
        if ($funcName === '--alpha') {
            $result = handleAlpha($node, $designSystem);
            if ($result !== null) {
                return WalkAction::Replace(parseValue($result));
            }
        }

        return WalkAction::Continue;
    });

    return toCss($ast);
}

/**
 * Map v3 namespace names to v4 CSS variable namespaces.
 */
const OLD_TO_NEW_NAMESPACE = [
    'animation' => 'animate',
    'aspectRatio' => 'aspect',
    'borderRadius' => 'radius',
    'boxShadow' => 'shadow',
    'colors' => 'color',
    'containers' => 'container',
    'fontFamily' => 'font',
    'fontSize' => 'text',
    'letterSpacing' => 'tracking',
    'lineHeight' => 'leading',
    'maxWidth' => 'container',
    'screens' => 'breakpoint',
    'transitionTimingFunction' => 'ease',
];

/**
 * Convert a key path to a CSS property name.
 *
 * @param array $path Key path segments
 * @return string|null CSS property name (without --)
 */
function keyPathToCssProperty(array $path): ?string
{
    if (empty($path)) {
        return null;
    }

    // The legacy container component config should not be included in the Theme
    if ($path[0] === 'container') {
        return null;
    }

    // Map old v3 namespaces to new theme namespaces
    $ns = OLD_TO_NEW_NAMESPACE[$path[0]] ?? null;
    if ($ns !== null) {
        $path[0] = $ns;
    }

    // Convert path segments
    $result = array_map(function ($part, $idx) use ($path) {
        // Replace dots with underscores (for values like 2.5 -> 2_5)
        $part = str_replace('.', '_', $part);

        // Convert camelCase to kebab-case for the first segment (namespace)
        // and for certain special keys like lineHeight
        $shouldConvert = $idx === 0 || str_starts_with($part, '-') || $part === 'lineHeight';

        if ($shouldConvert) {
            $part = preg_replace('/([a-z])([A-Z])/', '$1-$2', $part);
            $part = strtolower($part);
        }

        return $part;
    }, $path, array_keys($path));

    // Remove the `DEFAULT` key at the end of a path
    if (end($result) === 'DEFAULT') {
        array_pop($result);
    }

    // Handle '1' as a special separator for nested tuple values
    // e.g., fontSize.xs.1.lineHeight -> text-xs--line-height
    $result = array_map(function ($part, $idx) use ($result) {
        return ($part === '1' && $idx !== count($result) - 1) ? '' : $part;
    }, $result, array_keys($result));

    return implode('-', $result);
}

/**
 * Handle legacy theme() function.
 *
 * @param array $node Function node
 * @param object $designSystem Design system instance
 * @return string|null Resolved value or null
 */
function handleLegacyTheme(array $node, object $designSystem): ?string
{
    $argsStr = toCss($node['nodes'] ?? []);
    $args = array_map('trim', segment(trim($argsStr), ','));

    if (empty($args) || $args[0] === '') {
        return null;
    }

    $path = eventuallyUnquote($args[0]);
    $fallback = array_slice($args, 1);

    $theme = $designSystem->getTheme();

    // Check for modifier (opacity) e.g., "colors.red.500 / 50%"
    $modifier = null;
    $lastSlash = strrpos($path, '/');
    if ($lastSlash !== false) {
        $modifier = trim(substr($path, $lastSlash + 1));
        $path = trim(substr($path, 0, $lastSlash));
    }

    // If path already starts with --, use it directly
    if (str_starts_with($path, '--')) {
        $cssVar = $path;
    } else {
        // Convert legacy dot notation to CSS variable name
        // e.g., "colors.red.500" -> "--color-red-500"
        $keyPath = toKeyPath($path);
        $cssProperty = keyPathToCssProperty($keyPath);

        if ($cssProperty === null) {
            return null;
        }

        $cssVar = '--' . $cssProperty;
    }

    // Resolve the theme value using resolveValue to get the raw value
    $resolvedValue = $theme->resolveValue(null, [$cssVar]);

    // Recursively resolve any nested theme() calls in the resolved value
    if ($resolvedValue !== null && str_contains($resolvedValue, 'theme(')) {
        $resolvedValue = resolveNestedThemeCalls($resolvedValue, $designSystem);
    }

    if ($resolvedValue === null) {
        if (!empty($fallback)) {
            // Recursively resolve any nested theme() calls in fallback
            $fallbackStr = implode(', ', $fallback);
            if (str_contains($fallbackStr, 'theme(')) {
                $fallbackAst = parseValue($fallbackStr);
                walk($fallbackAst, function (&$fNode) use ($designSystem) {
                    if ($fNode['kind'] === 'function' && $fNode['value'] === 'theme') {
                        $result = handleLegacyTheme($fNode, $designSystem);
                        if ($result !== null) {
                            return WalkAction::Replace(parseValue($result));
                        }
                    }
                    return WalkAction::Continue;
                });
                return toCss($fallbackAst);
            }
            return $fallbackStr;
        }
        // Return null to leave the theme() call as-is (or could throw error)
        return null;
    }

    // Apply opacity modifier if present
    if ($modifier !== null && $modifier !== '') {
        // Check if the modifier is a static value (can be inlined) or dynamic (contains var())
        // For static values, use inline mode to compute the actual oklab value
        // This enables proper stacking of opacity (50% on 50% = 25%)
        $isStaticOpacity = !str_contains($modifier, 'var(');
        return withAlpha($resolvedValue, $modifier, $isStaticOpacity);
    }

    return $resolvedValue;
}

/**
 * Handle --theme() function.
 *
 * The --theme() function resolves CSS variables from @theme blocks:
 * - --theme(--color-red-500) → var(--color-red-500)
 * - --theme(--color-red-500 inline) → red (the actual value)
 * - --theme(--color-red-500, fallback) → var(--color-red-500, fallback) or fallback if not found
 *
 * @param array $node Function node
 * @param array $source Source AST node
 * @param object $designSystem Design system instance
 * @return string|null Resolved value or null
 */
function handleTheme(array $node, array $source, object $designSystem): ?string
{
    $argsStr = toCss($node['nodes'] ?? []);
    $args = array_map('trim', segment(trim($argsStr), ','));

    if (empty($args) || $args[0] === '') {
        return null;
    }

    $path = $args[0];
    $fallback = array_slice($args, 1);

    if (!str_starts_with($path, '--')) {
        return null;
    }

    $inline = false;

    // Handle `--theme(… inline)` to force inline resolution
    if (str_ends_with($path, ' inline')) {
        $inline = true;
        $path = substr($path, 0, -7);
    }

    // If the --theme() function is used within an at-rule, always inline
    if (($source['kind'] ?? '') === 'at-rule') {
        $inline = true;
    }

    // Check for opacity modifier: --theme(--color-red-500/0.5) or --theme(--color-red-500/50)
    $opacity = null;
    $slashPos = strrpos($path, '/');
    if ($slashPos !== false) {
        $opacity = trim(substr($path, $slashPos + 1));
        $path = trim(substr($path, 0, $slashPos));
    }

    $theme = $designSystem->getTheme();
    $prefix = $theme->getPrefix();

    // Apply prefix to the variable name if one is set
    $prefixedPath = $path;
    if ($prefix !== null && str_starts_with($path, '--')) {
        // Convert --color-red-500 to --tw-color-red-500 (with prefix)
        $prefixedPath = '--' . $prefix . '-' . substr($path, 2);
    }

    // Get the actual value from the theme
    $value = $theme->get([$prefixedPath]) ?? $theme->get([$path]);

    if ($value === null) {
        // Value not found - use fallback if provided
        if (!empty($fallback)) {
            return implode(', ', $fallback);
        }
        return null;
    }

    // Apply opacity modifier if present
    if ($opacity !== null && $opacity !== '') {
        // Normalize opacity to a float (0-1 range)
        $opacityFloat = floatval($opacity);
        if ($opacityFloat > 1) {
            $opacityFloat = $opacityFloat / 100;
        }

        if ($inline) {
            // For inline, convert to oklab color without alpha channel
            return \TailwindPHP\LightningCss\LightningCss::colorToOklabWithOpacity($value, $opacityFloat);
        } else {
            // For non-inline, return color-mix with var() reference
            // Normalize opacity: 0.5 -> 50%, 50 -> 50%
            $opacityValue = $opacity;
            if (is_numeric($opacity) && floatval($opacity) <= 1) {
                $opacityValue = (floatval($opacity) * 100) . '%';
            } elseif (!str_ends_with($opacity, '%')) {
                $opacityValue = $opacity . '%';
            }
            return "color-mix(in oklab, var({$prefixedPath}) {$opacityValue}, transparent)";
        }
    }

    if ($inline) {
        // Return the actual value
        return $value;
    }

    // Return var() reference with optional fallback
    if (!empty($fallback)) {
        return "var({$prefixedPath}, " . implode(', ', $fallback) . ")";
    }
    return "var({$prefixedPath})";
}

/**
 * Handle --spacing() function.
 *
 * @param array $node Function node
 * @param object $designSystem Design system instance
 * @return string|null Resolved value or null
 */
function handleSpacing(array $node, object $designSystem): ?string
{
    $argsStr = trim(toCss($node['nodes'] ?? []));

    if ($argsStr === '') {
        return null;
    }

    $theme = $designSystem->getTheme();
    $multiplier = $theme->resolve(null, ['--spacing']);

    if ($multiplier === null) {
        return null;
    }

    return "calc({$multiplier} * {$argsStr})";
}

/**
 * Handle --alpha() function.
 *
 * @param array $node Function node
 * @param object $designSystem Design system instance
 * @return string|null Resolved value or null
 */
function handleAlpha(array $node, object $designSystem): ?string
{
    $argsStr = trim(toCss($node['nodes'] ?? []));

    $parts = segment($argsStr, '/');
    if (count($parts) !== 2) {
        return null;
    }

    $color = trim($parts[0]);
    $alpha = trim($parts[1]);

    if ($color === '' || $alpha === '') {
        return null;
    }

    // Use withAlpha utility which uses color-mix in oklab
    return withAlpha($color, $alpha);
}

/**
 * Eventually unquote a string value.
 *
 * @param string $value Value to unquote
 * @return string Unquoted value
 */
function eventuallyUnquote(string $value): string
{
    if (strlen($value) < 2) {
        return $value;
    }

    $first = $value[0];
    if ($first !== '"' && $first !== "'") {
        return $value;
    }

    $quoteChar = $first;
    $unquoted = '';

    for ($i = 1; $i < strlen($value) - 1; $i++) {
        $currentChar = $value[$i];
        $nextChar = $value[$i + 1] ?? '';

        if ($currentChar === '\\' && ($nextChar === $quoteChar || $nextChar === '\\')) {
            $unquoted .= $nextChar;
            $i++;
        } else {
            $unquoted .= $currentChar;
        }
    }

    return $unquoted;
}

/**
 * Recursively resolve nested theme() calls in a value.
 *
 * @param string $value Value that may contain theme() calls
 * @param object $designSystem Design system instance
 * @return string Value with theme() calls resolved
 */
function resolveNestedThemeCalls(string $value, object $designSystem): string
{
    if (!str_contains($value, 'theme(')) {
        return $value;
    }

    $ast = parseValue($value);

    walk($ast, function (&$node) use ($designSystem) {
        if ($node['kind'] !== 'function') {
            return WalkAction::Continue;
        }

        if ($node['value'] === 'theme') {
            $result = handleLegacyTheme($node, $designSystem);
            if ($result !== null) {
                return WalkAction::Replace(parseValue($result));
            }
        }

        return WalkAction::Continue;
    });

    return toCss($ast);
}
