<?php

declare(strict_types=1);

namespace TailwindPHP;

use TailwindPHP\Walk\WalkAction;
use function TailwindPHP\Walk\walk;
use function TailwindPHP\ValueParser\parse as parseValue;
use function TailwindPHP\ValueParser\toCss;
use function TailwindPHP\Utils\segment;

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

    // Check for modifier (opacity) e.g., "--color-red-500 / 50%"
    $modifier = null;
    $lastSlash = strrpos($path, '/');
    if ($lastSlash !== false) {
        $modifier = trim(substr($path, $lastSlash + 1));
        $path = trim(substr($path, 0, $lastSlash));
    }

    // Resolve the theme value - use get() to get the raw value
    $resolvedValue = $theme->get([$path]);

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
        return "color-mix(in srgb, {$resolvedValue} {$modifier}, transparent)";
    }

    return $resolvedValue;
}

/**
 * Handle --theme() function.
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

    $theme = $designSystem->getTheme();

    // Resolve the theme value
    if ($inline) {
        $resolvedValue = $theme->resolveValue($path, []);
    } else {
        $resolvedValue = $theme->resolve($path, []);
    }

    if ($resolvedValue === null) {
        if (!empty($fallback)) {
            return implode(', ', $fallback);
        }
        return null;
    }

    return $resolvedValue;
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

    // Use withAlpha utility if available
    return "color-mix(in srgb, {$color} {$alpha}, transparent)";
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
