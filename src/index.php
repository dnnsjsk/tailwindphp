<?php

declare(strict_types=1);

namespace TailwindPHP;

use function TailwindPHP\Ast\atRule;
use function TailwindPHP\Ast\decl;
use function TailwindPHP\Ast\styleRule;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\CssParser\parse;
use function TailwindPHP\DesignSystem\buildDesignSystem;

use TailwindPHP\DesignSystem\DesignSystem;
use TailwindPHP\LightningCss\LightningCss;
use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Variants\Variants;

use function TailwindPHP\Walk\walk;

use TailwindPHP\Walk\WalkAction;

/**
 * TailwindPHP - CSS-first Tailwind CSS compiler for PHP.
 *
 * Port of: packages/tailwindcss/src/index.ts
 *
 * @port-deviation:async TypeScript uses async/await throughout (parseCss, compile).
 * PHP uses synchronous execution since PHP doesn't have native async support.
 *
 * @port-deviation:sourcemaps TypeScript creates source maps via createSourceMap().
 * PHP omits source map generation entirely.
 *
 * @port-deviation:modules TypeScript has loadModule/loadStylesheet for dynamic imports.
 * PHP uses inline file loading via file_get_contents() or requires.
 *
 * @port-deviation:plugins TypeScript supports JS plugins via @plugin/@config directives.
 * PHP does not support JS plugins - all utilities are implemented in PHP directly.
 *
 * @port-deviation:lightningcss TypeScript uses lightningcss (Rust) for CSS transforms.
 * PHP uses LightningCss.php class for equivalent transformations in pure PHP.
 */

// Polyfill flags
const POLYFILL_NONE = 0;
const POLYFILL_AT_PROPERTY = 1 << 0;
const POLYFILL_COLOR_MIX = 1 << 1;
const POLYFILL_ALL = POLYFILL_AT_PROPERTY | POLYFILL_COLOR_MIX;

// Feature flags
const FEATURE_NONE = 0;
const FEATURE_AT_APPLY = 1 << 0;
const FEATURE_AT_IMPORT = 1 << 1;
const FEATURE_JS_PLUGIN_COMPAT = 1 << 2;
const FEATURE_THEME_FUNCTION = 1 << 3;
const FEATURE_UTILITIES = 1 << 4;
const FEATURE_VARIANTS = 1 << 5;
const FEATURE_AT_THEME = 1 << 6;

/** @var Theme|null Cached default theme instance */
$_defaultThemeCache = null;

/**
 * Load and parse the default Tailwind theme from theme.css.
 *
 * @return Theme
 */
function loadDefaultTheme(): Theme
{
    global $_defaultThemeCache;

    if ($_defaultThemeCache !== null) {
        // Return a clone so modifications don't affect the cached instance
        return clone $_defaultThemeCache;
    }

    $themePath = __DIR__ . '/../resources/theme.css';
    if (!file_exists($themePath)) {
        throw new \RuntimeException("Default theme file not found: {$themePath}");
    }

    $css = file_get_contents($themePath);
    $ast = parse($css);
    $theme = new Theme();

    // Walk AST to extract @theme declarations
    walk($ast, function (&$node) use ($theme) {
        if ($node['kind'] !== 'at-rule' || $node['name'] !== '@theme') {
            return WalkAction::Continue;
        }

        // Parse theme options from params (e.g., "default", "default inline reference")
        [$themeOptions, $themePrefix] = parseThemeOptions($node['params'] ?? '');

        // Process declarations and keyframes
        foreach ($node['nodes'] ?? [] as $child) {
            if ($child['kind'] === 'declaration' && str_starts_with($child['property'], '--')) {
                $property = preg_replace('/\\\\(.)/', '$1', $child['property']);
                $value = $child['value'] ?? '';
                $theme->add($property, $value, $themeOptions);
            } elseif ($child['kind'] === 'at-rule' && $child['name'] === '@keyframes') {
                $theme->addKeyframes($child, $themeOptions);
            }
        }

        return WalkAction::Continue;
    });

    $_defaultThemeCache = $theme;

    return clone $_defaultThemeCache;
}

/**
 * Compile CSS with Tailwind utilities.
 *
 * @param string $css Input CSS containing @tailwind directives
 * @param array $options Compilation options
 * @return array{build: callable, sources: array, root: mixed, features: int}
 */
function compile(string $css, array $options = []): array
{
    $ast = parse($css);

    return compileAst($ast, $options);
}

/**
 * Compile AST with Tailwind utilities.
 *
 * @param array $ast CSS AST
 * @param array $options Compilation options
 * @return array{build: callable, sources: array, root: mixed, features: int}
 */
function compileAst(array $ast, array $options = []): array
{
    $result = parseCss($ast, $options);

    $designSystem = $result['designSystem'];
    $sources = $result['sources'];
    $root = $result['root'];
    $utilitiesNodePath = $result['utilitiesNodePath'];
    $features = $result['features'];
    $inlineCandidates = $result['inlineCandidates'];

    // Substitute CSS functions (theme(), --theme(), --spacing(), --alpha())
    $features |= substituteFunctions($ast, $designSystem);

    // Process @apply directives first (this also handles @utility with @apply inside)
    // substituteAtApply will:
    // 1. Process @apply inside @utility definitions (topological order)
    // 2. Register @utility definitions with the design system
    // 3. Process remaining @apply rules
    $features |= substituteAtApply($ast, $designSystem);

    // Remove @utility nodes from AST (after @apply has processed them)
    walk($ast, function (&$node) {
        if ($node['kind'] !== 'at-rule') {
            return WalkAction::Continue;
        }

        if ($node['name'] === '@utility') {
            return WalkAction::Replace([]);
        }

        // @utility has to be top-level, so we don't need to traverse into nested trees
        return WalkAction::Skip;
    });

    $allValidCandidates = [];
    $compiled = null;
    $previousAstNodeCount = 0;

    foreach ($inlineCandidates as $candidate) {
        if (!$designSystem->hasInvalidCandidate($candidate)) {
            $allValidCandidates[$candidate] = true;
        }
    }

    // Helper to find and update the utilities context node
    $updateUtilitiesNode = function (array &$ast, array $newNodes) {
        walk($ast, function (&$node) use ($newNodes) {
            // Find the context node that was converted from @tailwind utilities
            if ($node['kind'] === 'context' && isset($node['context']) && is_array($node['context'])) {
                $node['nodes'] = $newNodes;

                return WalkAction::Stop;
            }

            return WalkAction::Continue;
        });
    };

    return [
        'sources' => $sources,
        'root' => $root,
        'features' => $features,
        'build' => function (array $newRawCandidates) use (
            &$allValidCandidates,
            &$compiled,
            &$previousAstNodeCount,
            $designSystem,
            $utilitiesNodePath,
            &$ast,
            $features,
            $options,
            $updateUtilitiesNode
        ) {
            if ($features === FEATURE_NONE) {
                return toCss($ast);
            }

            if ($utilitiesNodePath === null) {
                if ($compiled === null) {
                    $compiled = optimizeAst($ast, $designSystem, $options['polyfills'] ?? POLYFILL_ALL);
                }

                return toCss($compiled);
            }

            $didChange = false;

            // Add all new candidates unless we know they are invalid
            $prevSize = count($allValidCandidates);
            foreach ($newRawCandidates as $candidate) {
                if (!$designSystem->hasInvalidCandidate($candidate)) {
                    if (str_starts_with($candidate, '--')) {
                        $didMarkVariableAsUsed = $designSystem->getTheme()->markUsedVariable($candidate);
                        $didChange = $didChange || $didMarkVariableAsUsed;
                    } else {
                        $allValidCandidates[$candidate] = true;
                        $didChange = $didChange || (count($allValidCandidates) !== $prevSize);
                    }
                }
            }

            // If no new candidates were added, return cached result
            if (!$didChange) {
                if ($compiled === null) {
                    $compiled = optimizeAst($ast, $designSystem, $options['polyfills'] ?? POLYFILL_ALL);
                }

                return toCss($compiled);
            }

            $compileResult = \TailwindPHP\Compile\compileCandidates(
                array_keys($allValidCandidates),
                $designSystem,
                ['onInvalidCandidate' => function ($candidate) use ($designSystem) {
                    $designSystem->addInvalidCandidate($candidate);
                }],
            );

            $newNodes = $compileResult['astNodes'];

            // Apply CSS function substitution to compiled utilities (resolves theme() etc.)
            substituteFunctions($newNodes, $designSystem);

            // If no new nodes were generated, return cached result
            if ($previousAstNodeCount === count($newNodes)) {
                if ($compiled === null) {
                    $compiled = optimizeAst($ast, $designSystem, $options['polyfills'] ?? POLYFILL_ALL);
                }

                return toCss($compiled);
            }

            $previousAstNodeCount = count($newNodes);

            // Update the context node with the compiled utilities
            $updateUtilitiesNode($ast, $newNodes);

            $compiled = optimizeAst($ast, $designSystem, $options['polyfills'] ?? POLYFILL_ALL);

            return toCss($compiled);
        },
    ];
}

/**
 * Parse CSS and extract theme, utilities, variants, etc.
 *
 * @param array $ast CSS AST
 * @param array $options Parse options
 * @return array
 */
function parseCss(array &$ast, array $options = []): array
{
    $features = FEATURE_NONE;
    // Use default theme unless 'loadDefaultTheme' option is explicitly false
    $loadDefaultTheme = $options['loadDefaultTheme'] ?? true;
    $theme = $loadDefaultTheme ? loadDefaultTheme() : new Theme();
    $utilitiesNodePath = null;
    $sources = [];
    $inlineCandidates = [];
    $root = null;
    $firstThemeRule = null;
    $important = false;
    $customVariants = []; // Collect @custom-variant definitions
    $plugins = []; // Collect @plugin directives

    // Walk AST to find @tailwind utilities, @theme, @source, @utility, @custom-variant, @plugin, @media important
    walk($ast, function (&$node, $ctx) use (&$features, &$theme, &$utilitiesNodePath, &$sources, &$firstThemeRule, &$important, &$customVariants, &$plugins, $options) {
        if ($node['kind'] !== 'at-rule') {
            return WalkAction::Continue;
        }

        // Handle @tailwind utilities - can be nested (e.g., inside #app {})
        if ($node['name'] === '@tailwind' &&
            ($node['params'] === 'utilities' || str_starts_with($node['params'], 'utilities'))
        ) {
            // Any additional @tailwind utilities nodes can be removed
            if ($utilitiesNodePath !== null) {
                return WalkAction::Replace([]);
            }

            // Store the path to this node for later modification
            $utilitiesNodePath = $ctx->path();
            $utilitiesNodePath[] = $node; // Add current node to path
            $features |= FEATURE_UTILITIES;

            // Convert the @tailwind node to a context node in place
            // This is how the TypeScript does it - mutate in place
            $node['kind'] = 'context';
            $node['context'] = [];
            $node['nodes'] = [];
            unset($node['name']);
            unset($node['params']);

            return WalkAction::Skip;
        }

        // Handle @theme
        if ($node['name'] === '@theme') {
            $features |= FEATURE_AT_THEME;

            [$themeOptions, $themePrefix] = parseThemeOptions($node['params']);

            // Validate and apply prefix
            if ($themePrefix !== null) {
                if (!preg_match(IS_VALID_PREFIX, $themePrefix)) {
                    throw new \Exception(
                        "The prefix \"{$themePrefix}\" is invalid. Prefixes must be lowercase ASCII letters (a-z) only.",
                    );
                }
                $theme->prefix = $themePrefix;
            }

            // Process theme declarations and keyframes
            foreach ($node['nodes'] ?? [] as $child) {
                if ($child['kind'] === 'declaration' && str_starts_with($child['property'], '--')) {
                    // Unescape CSS escape sequences in property names (e.g., \* -> *)
                    $property = preg_replace('/\\\\(.)/', '$1', $child['property']);
                    $value = $child['value'] ?? '';

                    // Store the raw value including --theme() calls
                    // They will be resolved later by substituteFunctions()
                    $theme->add($property, $value, $themeOptions);
                } elseif ($child['kind'] === 'at-rule' && $child['name'] === '@keyframes') {
                    $theme->addKeyframes($child, $themeOptions);
                }
            }

            // Keep a reference to the first @theme rule to update with the full
            // theme later, and delete any other @theme rules.
            if ($firstThemeRule === null) {
                $firstThemeRule = styleRule(':root, :host', []);

                return WalkAction::ReplaceSkip($firstThemeRule);
            } else {
                return WalkAction::ReplaceSkip([]);
            }
        }

        // Handle @source
        if ($node['name'] === '@source') {
            $path = trim($node['params'], "\"'");
            $sources[] = [
                'base' => $options['base'] ?? '',
                'pattern' => $path,
                'negated' => false,
            ];

            return WalkAction::ReplaceSkip([]);
        }

        // Handle @import - especially for 'tailwindcss' module
        if ($node['name'] === '@import') {
            $params = $node['params'];
            // Parse the import path and modifiers
            // e.g., "'tailwindcss' theme(inline)" or "'tailwindcss/utilities' important"
            preg_match('/^["\']([^"\']+)["\']\s*(.*)$/', $params, $matches);

            if ($matches) {
                $importPath = $matches[1];
                $modifiers = trim($matches[2] ?? '');

                // Handle 'tailwindcss' virtual module - full Tailwind CSS (theme + preflight + utilities)
                if ($importPath === 'tailwindcss') {
                    // Load theme.css
                    $themeCss = file_get_contents(__DIR__.'/../resources/theme.css');
                    $themeAst = parse($themeCss);

                    // Load preflight.css
                    $preflightCss = file_get_contents(__DIR__.'/../resources/preflight.css');
                    $preflightAst = parse($preflightCss);

                    // Create utilities node
                    $utilitiesNode = atRule('@tailwind', 'utilities', []);

                    // Apply modifiers to theme if present
                    if (str_contains($modifiers, 'theme(')) {
                        $themeAst = [atRule('@media', $modifiers, $themeAst)];
                    }

                    // Combine: theme + preflight + utilities
                    $fullContent = array_merge($themeAst, $preflightAst, [$utilitiesNode]);

                    return WalkAction::Replace($fullContent);
                }

                // Handle 'tailwindcss/theme' or 'tailwindcss/theme.css' - theme variables
                if ($importPath === 'tailwindcss/theme' || $importPath === 'tailwindcss/theme.css') {
                    $themeCss = file_get_contents(__DIR__.'/../resources/theme.css');
                    $themeAst = parse($themeCss);

                    // Check for prefix() modifier
                    if (preg_match('/prefix\(([^)]+)\)/', $modifiers, $prefixMatch)) {
                        $themeAst = [atRule('@media', 'prefix('.$prefixMatch[1].')', $themeAst)];
                    }

                    // Check for theme(static) modifier - theme values always included
                    if (str_contains($modifiers, 'theme(static)')) {
                        $themeAst = [atRule('@media', 'theme(static)', $themeAst)];
                    }

                    // Check for theme(inline) modifier - theme values inlined, not as variables
                    if (str_contains($modifiers, 'theme(inline)')) {
                        $themeAst = [atRule('@media', 'theme(inline)', $themeAst)];
                    }

                    // source(none) is a no-op in TailwindPHP since we don't do file scanning
                    // It's accepted for compatibility with official Tailwind CSS syntax

                    // Check for layer() modifier
                    if (preg_match('/layer\(([^)]+)\)/', $modifiers, $layerMatch)) {
                        return WalkAction::Replace([
                            atRule('@layer', $layerMatch[1], $themeAst),
                        ]);
                    }

                    return WalkAction::Replace($themeAst);
                }

                // Handle 'tailwindcss/utilities' or 'tailwindcss/utilities.css'
                if ($importPath === 'tailwindcss/utilities' || $importPath === 'tailwindcss/utilities.css') {
                    $utilityNode = atRule('@tailwind', 'utilities', []);

                    // If there's an 'important' modifier
                    if (str_contains($modifiers, 'important')) {
                        $utilityNode = atRule('@media', 'important', [$utilityNode]);
                    }

                    // If there's a prefix() modifier, wrap in @media prefix()
                    if (preg_match('/prefix\(([^)]+)\)/', $modifiers, $prefixMatch)) {
                        $utilityNode = atRule('@media', 'prefix('.$prefixMatch[1].')', [$utilityNode]);
                    }

                    // If there's a layer() modifier, wrap in @layer
                    if (preg_match('/layer\(([^)]+)\)/', $modifiers, $layerMatch)) {
                        return WalkAction::Replace([
                            atRule('@layer', $layerMatch[1], [$utilityNode]),
                        ]);
                    }

                    return WalkAction::Replace([$utilityNode]);
                }

                // Handle 'tailwindcss/preflight' - CSS reset/base styles
                if ($importPath === 'tailwindcss/preflight' || $importPath === 'tailwindcss/preflight.css') {
                    $preflightCss = file_get_contents(__DIR__.'/../resources/preflight.css');
                    $preflightAst = parse($preflightCss);

                    // Check for layer(base) modifier
                    if (str_contains($modifiers, 'layer(base)')) {
                        return WalkAction::Replace([
                            atRule('@layer', 'base', $preflightAst),
                        ]);
                    }

                    return WalkAction::Replace($preflightAst);
                }
            }

            // For other imports, leave as-is (will be output in final CSS)
            return WalkAction::Continue;
        }

        // Handle @utility - validate name but don't register yet
        // Registration happens AFTER @apply processing in compileAst
        if ($node['name'] === '@utility') {
            if ($ctx->parent !== null) {
                throw new \Exception('`@utility` cannot be nested.');
            }

            if (empty($node['nodes'])) {
                throw new \Exception(
                    "`@utility {$node['params']}` is empty. Utilities should include at least one property.",
                );
            }

            // Validate utility name
            $name = $node['params'];
            if (!preg_match(IS_VALID_FUNCTIONAL_UTILITY_NAME, $name) && !preg_match(IS_VALID_STATIC_UTILITY_NAME, $name)) {
                if (str_ends_with($name, '-*')) {
                    throw new \Exception(
                        "`@utility {$name}` defines an invalid utility name. Utilities should be alphanumeric and start with a lowercase letter.",
                    );
                } elseif (str_contains($name, '*')) {
                    throw new \Exception(
                        "`@utility {$name}` defines an invalid utility name. The dynamic portion marked by `-*` must appear once at the end.",
                    );
                }
                throw new \Exception(
                    "`@utility {$name}` defines an invalid utility name. Utilities should be alphanumeric and start with a lowercase letter.",
                );
            }

            // Mark as having custom utilities feature
            $features |= FEATURE_AT_APPLY;

            // Don't remove or register yet - will be done after @apply processing
            return WalkAction::Skip;
        }

        // Handle @custom-variant
        if ($node['name'] === '@custom-variant') {
            if ($ctx->parent !== null) {
                throw new \Exception('`@custom-variant` cannot be nested.');
            }

            $params = $node['params'] ?? '';
            $parts = \TailwindPHP\Utils\segment($params, ' ');
            $name = $parts[0] ?? '';
            $selector = isset($parts[1]) ? implode(' ', array_slice($parts, 1)) : null;

            if (!preg_match(\TailwindPHP\Variants\IS_VALID_VARIANT_NAME, $name)) {
                throw new \Exception(
                    "`@custom-variant {$name}` defines an invalid variant name. Variants should only contain alphanumeric, dashes, or underscore characters and start with a lowercase letter or number.",
                );
            }

            $nodes = $node['nodes'] ?? [];
            if (count($nodes) > 0 && $selector) {
                throw new \Exception("`@custom-variant {$name}` cannot have both a selector and a body.");
            }

            // Store for later registration
            $customVariants[] = [
                'name' => $name,
                'selector' => $selector,
                'nodes' => $nodes,
            ];

            $features |= FEATURE_VARIANTS;

            return WalkAction::ReplaceSkip([]);
        }

        // Handle @plugin
        if ($node['name'] === '@plugin') {
            if ($ctx->parent !== null) {
                throw new \Exception('`@plugin` cannot be nested.');
            }

            // Extract plugin name from params (remove quotes)
            $pluginName = trim($node['params'], "\"'");

            if (empty($pluginName)) {
                throw new \Exception('`@plugin` requires a plugin name.');
            }

            // Parse any options from nested declarations
            $pluginOptions = [];
            foreach ($node['nodes'] ?? [] as $child) {
                if ($child['kind'] === 'declaration') {
                    $pluginOptions[$child['property']] = parsePluginOptionValue($child['value'] ?? '');
                }
            }

            $plugins[] = [
                'name' => $pluginName,
                'options' => $pluginOptions,
            ];

            $features |= FEATURE_JS_PLUGIN_COMPAT;

            return WalkAction::ReplaceSkip([]);
        }

        // Handle @media important, @media theme(...), @media prefix(...)
        if ($node['name'] === '@media') {
            $params = \TailwindPHP\Utils\segment($node['params'], ' ');
            $unknownParams = [];

            foreach ($params as $param) {
                if ($param === 'important') {
                    $important = true;
                }
                // Handle @media theme(…)
                // We support `@import "tailwindcss" theme(reference)` as a way to
                // import an external theme file as a reference, which becomes `@media
                // theme(reference) { … }` when the `@import` is processed.
                elseif (str_starts_with($param, 'theme(')) {
                    $themeParams = substr($param, 6, -1); // extract from theme(...)
                    $hasReference = str_contains($themeParams, 'reference');

                    // Walk children and append theme params to @theme blocks
                    if (isset($node['nodes'])) {
                        walk($node['nodes'], function (&$child) use ($themeParams, $hasReference) {
                            if ($child['kind'] === 'context') {
                                return WalkAction::Continue;
                            }
                            if ($child['kind'] !== 'at-rule') {
                                if ($hasReference) {
                                    throw new \Exception(
                                        "Files imported with `@import \"…\" theme(reference)` must only contain `@theme` blocks.\nUse `@reference \"…\";` instead.",
                                    );
                                }

                                return WalkAction::Continue;
                            }

                            if ($child['name'] === '@theme') {
                                $child['params'] = trim($child['params'] . ' ' . $themeParams);

                                return WalkAction::Skip;
                            }

                            return WalkAction::Continue;
                        });
                    }
                }
                // Handle @media prefix(…)
                // We support `@import "tailwindcss/theme" prefix(tw)` as a way to
                // prefix theme variables, which becomes `@media prefix(tw) { … }`
                elseif (str_starts_with($param, 'prefix(')) {
                    $prefixValue = substr($param, 7, -1); // extract from prefix(...)

                    // Walk children and append prefix to @theme blocks
                    if (isset($node['nodes'])) {
                        walk($node['nodes'], function (&$child) use ($prefixValue, $theme) {
                            if ($child['kind'] === 'context') {
                                return WalkAction::Continue;
                            }
                            if ($child['kind'] !== 'at-rule') {
                                return WalkAction::Continue;
                            }

                            if ($child['name'] === '@theme') {
                                $child['params'] = trim($child['params'] . ' prefix(' . $prefixValue . ')');

                                return WalkAction::Skip;
                            }

                            return WalkAction::Continue;
                        });
                    }
                    // Also set the prefix on the theme directly for utility generation
                    if (!empty($prefixValue) && preg_match(IS_VALID_PREFIX, $prefixValue)) {
                        $theme->prefix = $prefixValue;
                    }
                } else {
                    $unknownParams[] = $param;
                }
            }

            if (count($unknownParams) > 0) {
                $node['params'] = implode(' ', $unknownParams);
            } elseif (count($params) > 0) {
                // All params were recognized, replace @media with its children
                return WalkAction::Replace($node['nodes'] ?? []);
            }
        }

        return WalkAction::Continue;
    });

    // Populate the first theme rule with theme values
    if ($firstThemeRule !== null) {
        walk($ast, function (&$node) use ($theme) {
            if ($node['kind'] === 'rule' && $node['selector'] === ':root, :host') {
                $nodes = [];
                foreach ($theme->entries() as [$key, $value]) {
                    // Skip REFERENCE and INLINE values - they don't get output as CSS variables
                    if ($value['options'] & (Theme::OPTIONS_REFERENCE | Theme::OPTIONS_INLINE)) {
                        continue;
                    }
                    // Skip values that are 'initial' - they act as markers for fallback injection
                    if ($value['value'] === 'initial') {
                        continue;
                    }
                    // Skip values that contain --theme() calls resolving to 'initial'
                    // These are markers for fallback injection, not actual CSS values
                    if (str_contains($value['value'], '--theme(') && themeValueResolvesToInitial($value['value'], $theme)) {
                        continue;
                    }
                    $nodes[] = decl(\TailwindPHP\Utils\escape($key), $value['value']);
                }
                $node['nodes'] = $nodes;

                return WalkAction::Stop;
            }

            return WalkAction::Continue;
        });

        // Add keyframes to the AST (they get hoisted to top level during output)
        foreach ($theme->getKeyframes() as $keyframes) {
            $ast[] = $keyframes;
        }
    }

    // Build the design system
    $designSystem = buildDesignSystem($theme);

    // Set important flag on design system
    if ($important) {
        $designSystem->setImportant(true);
    }

    // Apply plugins
    if (!empty($plugins)) {
        $pluginManager = getPluginManager();
        // Pass theme config from options for plugins to access via theme('...')
        $themeConfig = $options['theme'] ?? [];
        $api = $pluginManager->createAPI(
            $theme,
            $designSystem->getUtilities(),
            $designSystem->getVariants(),
            ['theme' => $themeConfig],
        );

        foreach ($plugins as $pluginRef) {
            $pluginName = $pluginRef['name'];
            $pluginOptions = $pluginRef['options'];

            if (!$pluginManager->has($pluginName)) {
                throw new \Exception("Plugin \"{$pluginName}\" is not registered. Make sure the plugin is installed and registered.");
            }

            // Apply theme extensions first
            // Note: Complex nested theme extensions (like typography's styles) are skipped
            // The actual plugin functionality comes from addComponents/addUtilities
            $themeExtensions = $pluginManager->getThemeExtensions($pluginName, $pluginOptions);
            foreach ($themeExtensions as $namespace => $values) {
                if (!is_array($values)) {
                    continue;
                }
                $themeNamespace = '--' . strtolower(preg_replace('/([A-Z])/', '-$1', $namespace));
                foreach ($values as $key => $value) {
                    // Only add simple string values to theme
                    if (!is_string($value)) {
                        continue;
                    }
                    if ($key === 'DEFAULT') {
                        $theme->add($themeNamespace, $value);
                    } else {
                        $theme->add("{$themeNamespace}-{$key}", $value);
                    }
                }
            }

            // Execute the plugin
            $pluginManager->execute($pluginName, $api, $pluginOptions);
        }
    }

    // Register custom variants
    foreach ($customVariants as $customVariant) {
        registerCustomVariant($designSystem, $customVariant['name'], $customVariant['selector'], $customVariant['nodes']);
    }

    // Note: @utility registration and removal is deferred to compileAst
    // This is because @apply inside @utility needs to be processed first

    return [
        'designSystem' => $designSystem,
        'ast' => $ast,
        'sources' => $sources,
        'root' => $root,
        'utilitiesNodePath' => $utilitiesNodePath,
        'features' => $features,
        'inlineCandidates' => $inlineCandidates,
    ];
}

const IS_VALID_PREFIX = '/^[a-z]+$/';

/**
 * Parse @theme options from params string.
 *
 * @param string $params
 * @return array{0: int, 1: string|null} [options flags, prefix]
 */
function parseThemeOptions(string $params): array
{
    $options = Theme::OPTIONS_NONE;
    $prefix = null;

    foreach (\TailwindPHP\Utils\segment($params, ' ') as $option) {
        if ($option === 'reference') {
            $options |= Theme::OPTIONS_REFERENCE;
        } elseif ($option === 'inline') {
            $options |= Theme::OPTIONS_INLINE;
        } elseif ($option === 'default') {
            $options |= Theme::OPTIONS_DEFAULT;
        } elseif ($option === 'static') {
            $options |= Theme::OPTIONS_STATIC;
        } elseif (str_starts_with($option, 'prefix(') && str_ends_with($option, ')')) {
            $prefix = substr($option, 7, -1);
        }
    }

    return [$options, $prefix];
}

/**
 * Register a custom variant with the design system.
 *
 * @param DesignSystem\DesignSystem $designSystem The design system
 * @param string $name The variant name
 * @param string|null $selector The selector (for simple variants like "&:hover")
 * @param array $nodes The AST nodes (for complex variants with @slot)
 */
function registerCustomVariant($designSystem, string $name, ?string $selector, array $nodes): void
{
    $variants = $designSystem->getVariants();

    // Simple selector-based variant: @custom-variant hocus (&:hover, &:focus);
    if ($selector !== null && empty($nodes)) {
        if (!str_starts_with($selector, '(') || !str_ends_with($selector, ')')) {
            throw new \Exception("`@custom-variant {$name}` selector must be wrapped in parentheses.");
        }

        // Parse selectors from "(sel1, sel2, ...)"
        $selectorContent = substr($selector, 1, -1);
        $selectors = array_map('trim', \TailwindPHP\Utils\segment($selectorContent, ','));

        if (empty($selectors) || in_array('', $selectors, true)) {
            throw new \Exception("`@custom-variant {$name} {$selector}` selector is invalid.");
        }

        $atRuleParams = [];
        $styleRuleSelectors = [];

        foreach ($selectors as $sel) {
            if (str_starts_with($sel, '@')) {
                $atRuleParams[] = $sel;
            } else {
                $styleRuleSelectors[] = $sel;
            }
        }

        // Build the variant apply function
        $variants->static($name, function (&$r) use ($atRuleParams, $styleRuleSelectors) {
            // Wrap in style rule selectors first
            if (!empty($styleRuleSelectors)) {
                $r['nodes'] = [styleRule(implode(', ', $styleRuleSelectors), $r['nodes'])];
            }

            // Then wrap in at-rules
            foreach (array_reverse($atRuleParams) as $atRuleParam) {
                // Parse at-rule name and params
                if (preg_match('/^(@[a-z-]+)\s*(.*)$/', $atRuleParam, $m)) {
                    $r['nodes'] = [atRule($m[1], $m[2], $r['nodes'])];
                }
            }
        }, ['compounds' => \TailwindPHP\Variants\compoundsForSelectors($selectors)]);
    }
    // Body-based variant: @custom-variant hocus { &:hover, &:focus { @slot; } }
    elseif (!empty($nodes)) {
        $variants->fromAst($name, $nodes, $designSystem);
    } else {
        throw new \Exception("`@custom-variant {$name}` has no selector or body.");
    }
}

// Regex patterns for utility name validation
const IS_VALID_STATIC_UTILITY_NAME = '/^-?[a-z][a-zA-Z0-9\/%._-]*$/';
const IS_VALID_FUNCTIONAL_UTILITY_NAME = '/^-?[a-z][a-zA-Z0-9\/%._-]*-\*$/';

/**
 * Create a CSS utility from an @utility at-rule.
 *
 * @param array $node The @utility at-rule node
 * @return callable|null Returns a callback to register the utility, or null if invalid
 */
function createCssUtility(array $node): ?callable
{
    $name = $node['params'];

    // Functional utilities. E.g.: `tab-size-*`
    if (preg_match(IS_VALID_FUNCTIONAL_UTILITY_NAME, $name)) {
        // For now, just support static functional utilities (no --value/--modifier)
        return function (DesignSystem $designSystem) use ($name, $node) {
            $utilityName = substr($name, 0, -2); // Remove trailing -*

            $designSystem->getUtilities()->functional($utilityName, function (array $candidate) use ($node) {
                // A value is required for functional utilities
                if (!isset($candidate['value'])) {
                    return null;
                }

                // Return all nodes (declarations, nested rules, etc.)
                // Deep clone to avoid mutation
                return array_map(fn ($child) => cloneAstNode($child), $node['nodes'] ?? []);
            });
        };
    }

    // Static utilities. E.g.: `my-utility`
    if (preg_match(IS_VALID_STATIC_UTILITY_NAME, $name)) {
        return function (DesignSystem $designSystem) use ($name, $node) {
            // Return all nodes (declarations, nested rules, etc.)
            // Deep clone to avoid mutation
            $designSystem->getUtilities()->static($name, fn () => array_map(fn ($child) => cloneAstNode($child), $node['nodes'] ?? []));
        };
    }

    return null;
}

/**
 * Deep clone an AST node.
 *
 * @param array $node The node to clone
 * @return array Cloned node
 */
function cloneAstNode(array $node): array
{
    $cloned = $node;
    if (isset($cloned['nodes'])) {
        $cloned['nodes'] = array_map(fn ($child) => cloneAstNode($child), $cloned['nodes']);
    }

    return $cloned;
}

/**
 * Optimize AST by flattening context nodes and other optimizations.
 *
 * @param array $ast
 * @param DesignSystem $designSystem
 * @param int $polyfills
 * @return array
 */
function optimizeAst(array $ast, DesignSystem $designSystem, int $polyfills = POLYFILL_ALL): array
{
    $result = [];
    $usedVariables = [];
    $usedKeyframeNames = [];
    $theme = $designSystem->getTheme();
    $atRoots = []; // Collect at-root nodes to hoist
    $seenAtProperties = []; // Track seen @property rules to dedupe

    // First pass: collect used variables and keyframe names
    $collectUsed = function (array $node) use (&$collectUsed, &$usedVariables, &$usedKeyframeNames) {
        if ($node['kind'] === 'declaration') {
            $value = $node['value'] ?? '';
            // Extract variables from var() functions
            // The regex matches CSS custom property names which can contain
            // any character except whitespace, quotes, closing parens, or commas
            if (preg_match_all('/var\(\s*(--[^\s\)\'\",]+)/', $value, $matches)) {
                foreach ($matches[1] as $var) {
                    // Unescape the variable name (e.g., --width-1\/2 -> --width-1/2)
                    $usedVariables[preg_replace('/\\\\(.)/', '$1', $var)] = true;
                }
            }
            // Extract keyframe names from animation property
            if ($node['property'] === 'animation' || $node['property'] === 'animation-name') {
                foreach (extractKeyframeNames($value) as $name) {
                    $usedKeyframeNames[$name] = true;
                }
            }
        }
        foreach ($node['nodes'] ?? [] as $child) {
            $collectUsed($child);
        }
    };

    foreach ($ast as $node) {
        $collectUsed($node);
    }

    // Also mark theme variables that reference other variables
    // Iterate until no new variables are found
    do {
        $changed = false;
        foreach ($theme->entries() as [$key, $value]) {
            if (isset($usedVariables[$key])) {
                // Extract variables this value depends on
                if (preg_match_all('/var\(\s*(--[a-zA-Z0-9_-]+)/', $value['value'], $matches)) {
                    foreach ($matches[1] as $var) {
                        if (!isset($usedVariables[$var])) {
                            $usedVariables[$var] = true;
                            $changed = true;
                        }
                    }
                }
                // Extract keyframe names from animation values
                // Handle both prefixed (--tw-animate-foo) and non-prefixed (--animate-foo)
                if (preg_match('/^--(?:[a-z]+-)?animate/', $key)) {
                    foreach (extractKeyframeNames($value['value']) as $name) {
                        if (!isset($usedKeyframeNames[$name])) {
                            $usedKeyframeNames[$name] = true;
                            $changed = true;
                        }
                    }
                }
            }
        }
    } while ($changed);

    $transform = function (array $node, array &$parent) use (&$transform, $usedVariables, $usedKeyframeNames, $theme, &$atRoots, &$seenAtProperties) {
        // Handle context nodes - lift their children to parent
        if ($node['kind'] === 'context') {
            // Skip reference context nodes
            if (!empty($node['context']['reference'])) {
                return;
            }
            // Recursively process children
            foreach ($node['nodes'] ?? [] as $child) {
                $transform($child, $parent);
            }

            return;
        }

        // Handle at-root nodes - hoist their children to top level
        if ($node['kind'] === 'at-root') {
            foreach ($node['nodes'] ?? [] as $child) {
                $newParent = [];
                $transform($child, $newParent);
                foreach ($newParent as $hoistedNode) {
                    // Collect @property rules separately
                    if ($hoistedNode['kind'] === 'at-rule' && $hoistedNode['name'] === '@property') {
                        $propName = trim($hoistedNode['params'] ?? '');
                        if (!isset($seenAtProperties[$propName])) {
                            $seenAtProperties[$propName] = true;
                            $atRoots[] = $hoistedNode;
                        }
                    } else {
                        $atRoots[] = $hoistedNode;
                    }
                }
            }

            return;
        }

        // Skip --tw-sort declarations (internal sorting only)
        if ($node['kind'] === 'declaration' && ($node['property'] ?? '') === '--tw-sort') {
            return;
        }

        // Filter :root, :host declarations to only used variables
        if ($node['kind'] === 'rule' && $node['selector'] === ':root, :host') {
            $filteredNodes = [];
            foreach ($node['nodes'] ?? [] as $child) {
                if ($child['kind'] === 'declaration') {
                    $prop = $child['property'] ?? '';
                    // Unescape the property for comparison (AST has escaped names, usedVariables has unescaped)
                    $unescapedProp = preg_replace('/\\\\(.)/', '$1', $prop);
                    // Check if this variable is used or has STATIC option
                    if (isset($usedVariables[$unescapedProp])) {
                        $filteredNodes[] = $child;
                    } elseif (str_starts_with($prop, '--')) {
                        // Check theme options for STATIC (use unescaped for theme lookup)
                        $options = $theme->getOptions($unescapedProp);
                        if ($options & Theme::OPTIONS_STATIC) {
                            $filteredNodes[] = $child;
                        }
                    }
                } else {
                    $filteredNodes[] = $child;
                }
            }
            if (empty($filteredNodes)) {
                return; // Skip empty :root, :host
            }
            $node['nodes'] = $filteredNodes;
            $parent[] = $node;

            return;
        }

        // Filter keyframes to only used ones (but only for theme keyframes)
        if ($node['kind'] === 'at-rule' && $node['name'] === '@keyframes') {
            $keyframeName = trim($node['params'] ?? '');
            // Only filter keyframes that came from @theme
            // Keyframes defined outside @theme are always preserved
            $isThemeKeyframe = $theme->hasKeyframe($keyframeName);
            if ($isThemeKeyframe && !isset($usedKeyframeNames[$keyframeName])) {
                // Check if theme has STATIC option for this keyframe
                $keyframeOptions = $theme->getKeyframeOptions($keyframeName);
                if ($keyframeOptions & Theme::OPTIONS_STATIC) {
                    // Keep it - static keyframes are always included
                } else {
                    return; // Skip unused theme keyframes
                }
            }
        }

        // Handle rules with children
        if (($node['kind'] === 'rule' || $node['kind'] === 'at-rule') && isset($node['nodes'])) {
            $children = [];
            foreach ($node['nodes'] as $child) {
                $transform($child, $children);
            }
            $node['nodes'] = $children;

            // Skip empty rules (no declarations or nested rules)
            // But keep @layer, @charset, @custom-media, @namespace, @import (they can be empty)
            $name = $node['name'] ?? '';
            if (empty($children) && !in_array($name, ['@layer', '@charset', '@custom-media', '@namespace', '@import'])) {
                return;
            }
        }

        $parent[] = $node;
    };

    foreach ($ast as $node) {
        $transform($node, $result);
    }

    // Transform CSS nesting (flatten & selectors, hoist @media)
    $result = LightningCss::transformNesting($result);

    // Add vendor prefixes to declarations that need them
    $result = LightningCss::addVendorPrefixes($result);

    // Apply LightningCSS value optimizations to all declarations
    $optimizeValues = function (array &$node) use (&$optimizeValues): void {
        if ($node['kind'] === 'declaration' && isset($node['value'])) {
            $node['value'] = LightningCss::optimizeValue($node['value'], $node['property'] ?? '');
        }
        if (isset($node['nodes'])) {
            foreach ($node['nodes'] as &$child) {
                $optimizeValues($child);
            }
        }
    };

    foreach ($result as &$node) {
        $optimizeValues($node);
    }

    // Apply color-mix polyfill - convert color-mix with variables to @supports fallback
    if ($polyfills & POLYFILL_COLOR_MIX) {
        $result = applyColorMixPolyfill($result, $designSystem);
    }

    // Process atRoots - separate @property rules from others
    $atPropertyRules = [];
    $otherAtRoots = [];
    foreach ($atRoots as $atRoot) {
        if ($atRoot['kind'] === 'at-rule' && $atRoot['name'] === '@property') {
            $atPropertyRules[] = $atRoot;
        } else {
            $otherAtRoots[] = $atRoot;
        }
    }

    // If we have @property rules, wrap their fallbacks in @layer properties + @supports
    if (!empty($atPropertyRules) && ($polyfills & POLYFILL_AT_PROPERTY)) {
        // Extract initial values for fallback declarations
        $fallbackDeclarations = [];
        foreach ($atPropertyRules as $property) {
            $propName = trim($property['params'] ?? '');
            $initialValue = null;
            $syntax = null;
            foreach ($property['nodes'] ?? [] as $decl) {
                if ($decl['kind'] === 'declaration') {
                    if ($decl['property'] === 'initial-value') {
                        $initialValue = $decl['value'] ?? '';
                    } elseif ($decl['property'] === 'syntax') {
                        $syntax = $decl['value'] ?? '';
                    }
                }
            }

            // For <length> syntax with bare "0", add "px" unit in fallback
            // This is because @property strips units from zero but fallbacks need them
            $fallbackValue = $initialValue ?? 'initial';
            if ($fallbackValue === '0' && $syntax === '"<length>"') {
                $fallbackValue = '0px';
            }

            $fallbackDeclarations[] = decl($propName, $fallbackValue);
        }

        if (!empty($fallbackDeclarations)) {
            // Create @layer properties with @supports fallback
            // @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color:rgb(from red r g b))))
            // Note: Extra parens around -webkit-hyphens test for specificity
            $supportsCondition = '(((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b))))';
            $universalSelector = '*, :before, :after, ::backdrop';

            $fallbackRule = Ast\styleRule($universalSelector, $fallbackDeclarations);
            $supportsRule = Ast\atRule('@supports', $supportsCondition, [$fallbackRule]);
            $layerProperties = Ast\atRule('@layer', 'properties', [$supportsRule]);

            // Prepend to result
            array_unshift($result, $layerProperties);
        }
    }

    // Append other atRoots (non-@property) and @property rules
    $result = array_merge($result, $otherAtRoots, $atPropertyRules);

    // Merge adjacent rules with same declarations (selector merging)
    // Process @custom-media definitions and substitute them
    $result = LightningCss::processCustomMedia($result);

    // Transform media query range syntax (width >= X → min-width: X)
    $result = LightningCss::processQueryRangeSyntax($result);

    $result = LightningCss::mergeRulesWithSameDeclarations($result);

    return $result;
}

/**
 * Extract keyframe names from an animation CSS value.
 *
 * @param string $value Animation value like "spin 1s infinite, fade 2s"
 * @return array<string> List of keyframe names
 */
function extractKeyframeNames(string $value): array
{
    $names = [];
    // Animation value format: name duration timing-function delay iteration-count direction fill-mode play-state
    // Keyframe name is a custom identifier (not a keyword)
    $keywords = ['none', 'infinite', 'normal', 'reverse', 'alternate', 'alternate-reverse',
        'forwards', 'backwards', 'both', 'running', 'paused', 'ease', 'ease-in', 'ease-out',
        'ease-in-out', 'linear', 'step-start', 'step-end', 'initial', 'inherit'];

    // Split by comma for multiple animations
    $animations = preg_split('/\s*,\s*/', $value);

    foreach ($animations as $animation) {
        // Split by whitespace
        $parts = preg_split('/\s+/', trim($animation));
        foreach ($parts as $part) {
            $part = trim($part);
            if (empty($part)) {
                continue;
            }

            // Skip timing values (numbers, percentages, seconds)
            if (preg_match('/^[\d.]+/', $part)) {
                continue;
            }
            if (preg_match('/^-?[\d.]+(?:s|ms|%)$/', $part)) {
                continue;
            }

            // Skip keywords
            if (in_array(strtolower($part), $keywords)) {
                continue;
            }

            // Skip functions (like cubic-bezier, steps)
            if (str_contains($part, '(')) {
                continue;
            }

            // Skip var() references - the variable value will be resolved
            if (str_starts_with($part, 'var(')) {
                continue;
            }

            // This is likely a keyframe name
            $names[] = $part;
        }
    }

    return array_unique($names);
}

/**
 * Generate CSS from content containing Tailwind classes.
 *
 * Accepts either:
 * 1. A string (HTML content to scan for classes)
 * 2. An array with 'content' and optional 'css' key
 *
 * @param string|array $input HTML string or array with 'content' and 'css' keys
 * @param string $css Optional CSS with @import directives (only used if $input is string)
 * @return string Generated CSS
 *
 * @example String input (includes theme + preflight + utilities):
 *   generate('<div class="flex p-4">Hello</div>');
 *
 * @example Array input with custom CSS:
 *   generate([
 *       'content' => '<div class="flex p-4">Hello</div>',
 *       'css' => '@import "tailwindcss"; @theme { --color-brand: #3b82f6; }'
 *   ]);
 *
 * @example Without preflight (granular imports):
 *   generate([
 *       'content' => '<div class="flex p-4">Hello</div>',
 *       'css' => '
 *           @import "tailwindcss/theme.css" layer(theme);
 *           @import "tailwindcss/utilities.css" layer(utilities);
 *       '
 *   ]);
 */
function generate(string|array $input, string $css = '@import "tailwindcss";'): string
{
    // Handle array input
    if (is_array($input)) {
        $content = $input['content'] ?? '';
        $css = $input['css'] ?? '@import "tailwindcss";';
    } else {
        $content = $input;
    }

    // Extract class names from content
    $candidates = extractCandidates($content);

    // Compile
    $compiled = compile($css);

    return $compiled['build']($candidates);
}

/**
 * Extract class name candidates from HTML content.
 *
 * @param string $html
 * @return array<string>
 */
function extractCandidates(string $html): array
{
    $candidates = [];

    // Extract from class attributes
    if (preg_match_all('/class\s*=\s*["\']([^"\']+)["\']/', $html, $matches)) {
        foreach ($matches[1] as $classAttr) {
            foreach (preg_split('/\s+/', $classAttr) as $class) {
                $class = trim($class);
                if ($class !== '') {
                    $candidates[] = $class;
                }
            }
        }
    }

    // Also extract from className (JSX)
    if (preg_match_all('/className\s*=\s*["\']([^"\']+)["\']/', $html, $matches)) {
        foreach ($matches[1] as $classAttr) {
            foreach (preg_split('/\s+/', $classAttr) as $class) {
                $class = trim($class);
                if ($class !== '') {
                    $candidates[] = $class;
                }
            }
        }
    }

    return array_unique($candidates);
}

/**
 * Check if a theme value containing --theme() calls would resolve to 'initial'.
 *
 * This is used to determine if a theme value should be output as a CSS variable.
 * When the value would resolve to 'initial', it acts as a marker for fallback injection
 * and should not be output to the CSS.
 *
 * @param string $value The value string containing --theme() calls
 * @param Theme $theme The theme instance for lookups
 * @return bool True if the value resolves to 'initial'
 */
function themeValueResolvesToInitial(string $value, Theme $theme): bool
{
    // Simple regex to extract --theme() arguments
    if (!preg_match('/^--theme\(([^)]+)\)$/', trim($value), $match)) {
        return false;
    }

    $args = $match[1];

    // Parse the arguments
    $parts = [];
    $current = '';
    $depth = 0;
    for ($i = 0; $i < strlen($args); $i++) {
        $char = $args[$i];
        if ($char === '(') {
            $depth++;
        }
        if ($char === ')') {
            $depth--;
        }
        if ($char === ',' && $depth === 0) {
            $parts[] = trim($current);
            $current = '';
        } else {
            $current .= $char;
        }
    }
    if ($current !== '') {
        $parts[] = trim($current);
    }

    $path = $parts[0];
    $fallback = count($parts) > 1 ? trim(implode(', ', array_slice($parts, 1))) : null;

    // Handle 'inline' modifier
    if (str_ends_with($path, ' inline')) {
        $path = substr($path, 0, -7);
    }

    // The path should start with --
    if (!str_starts_with($path, '--')) {
        return false;
    }

    // Look up the value in the theme (without prefix - theme stores unprefixed)
    $themeValue = $theme->get([$path]);

    // If the referenced variable doesn't exist and the fallback is 'initial', then resolves to initial
    if ($themeValue === null && $fallback === 'initial') {
        return true;
    }

    return false;
}

/**
 * Resolve --theme() calls within a value string during @theme processing.
 *
 * This allows patterns like `--theme(--font-family, initial)` to resolve
 * to 'initial' when --font-family doesn't exist in the theme.
 *
 * @param string $value The value string containing --theme() calls
 * @param Theme $theme The theme instance for lookups
 * @return string The resolved value
 */
function resolveThemeCallsInValue(string $value, Theme $theme): string
{
    // Match --theme(path[, fallback]) patterns
    // This is a simplified regex that handles basic cases
    if (!preg_match_all('/--theme\(([^)]+)\)/', $value, $matches, PREG_SET_ORDER)) {
        return $value;
    }

    foreach ($matches as $match) {
        $fullMatch = $match[0];
        $args = $match[1];

        // Parse the arguments - split on comma, but be careful with nested parens
        $parts = [];
        $current = '';
        $depth = 0;
        for ($i = 0; $i < strlen($args); $i++) {
            $char = $args[$i];
            if ($char === '(') {
                $depth++;
            }
            if ($char === ')') {
                $depth--;
            }
            if ($char === ',' && $depth === 0) {
                $parts[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }
        if ($current !== '') {
            $parts[] = trim($current);
        }

        $path = $parts[0];
        $fallback = count($parts) > 1 ? implode(', ', array_slice($parts, 1)) : null;

        // Handle 'inline' modifier
        $inline = false;
        if (str_ends_with($path, ' inline')) {
            $inline = true;
            $path = substr($path, 0, -7);
        }

        // The path should start with --
        if (!str_starts_with($path, '--')) {
            continue;
        }

        // Try to get the value from the theme
        $prefix = $theme->getPrefix();
        $prefixedPath = $path;
        if ($prefix !== null && str_starts_with($path, '--')) {
            $prefixedPath = '--' . $prefix . '-' . substr($path, 2);
        }

        $themeValue = $theme->get([$prefixedPath]) ?? $theme->get([$path]);

        if ($themeValue === null) {
            // Value doesn't exist - use fallback
            if ($fallback !== null) {
                $value = str_replace($fullMatch, $fallback, $value);
            }
            // If no fallback, leave as-is (will cause error or be handled later)
        } else {
            // Value exists
            if ($inline) {
                // Return the actual value
                $value = str_replace($fullMatch, $themeValue, $value);
            } else {
                // Return var() reference
                $value = str_replace($fullMatch, "var({$prefixedPath})", $value);
            }
        }
    }

    return $value;
}

/**
 * Apply color-mix polyfill to AST.
 *
 * When color-mix() contains CSS variables (like var(--opacity)), browsers that don't
 * support color-mix need a fallback. This creates:
 * 1. A fallback declaration with just the base color
 * 2. An @supports block with the color-mix version
 *
 * @param array $ast The AST to process
 * @param DesignSystem $designSystem The design system for theme lookups
 * @return array Modified AST with polyfill applied
 */
function applyColorMixPolyfill(array $ast, DesignSystem $designSystem): array
{
    $result = [];

    foreach ($ast as $node) {
        if ($node['kind'] === 'rule') {
            // Process each declaration in the rule
            $newNodes = [];
            $supportsDeclarations = [];

            foreach ($node['nodes'] ?? [] as $decl) {
                if ($decl['kind'] === 'declaration' && isset($decl['value'])) {
                    $value = $decl['value'];

                    // Check if this declaration has color-mix that needs polyfill
                    // Pattern 1: color-mix with var() in opacity position
                    // Pattern 2: color-mix with var() in color position (from --theme)
                    $needsPolyfill = false;
                    $fallbackColor = null;

                    // Pattern: color-mix(in oklab, COLOR VAR_OPACITY, transparent)
                    if (preg_match('/color-mix\s*\(\s*in\s+oklab\s*,\s*([^,]+)\s+var\s*\([^)]+\)\s*,\s*transparent\s*\)/i', $value, $match)) {
                        $needsPolyfill = true;
                        $fallbackColor = trim($match[1]);
                        $fallbackColor = LightningCss::optimizeValue($fallbackColor, $decl['property']);
                    }
                    // Pattern: color-mix(in oklab, var(--var) OPACITY%, transparent)
                    elseif (preg_match('/color-mix\s*\(\s*in\s+oklab\s*,\s*var\s*\(\s*([^)]+)\s*\)\s+(\d+(?:\.\d+)?%?)\s*,\s*transparent\s*\)/i', $value, $match)) {
                        $needsPolyfill = true;
                        $varName = '--' . ltrim(trim($match[1]), '-');
                        $opacityStr = $match[2];

                        // Get the color value from theme
                        $theme = $designSystem->getTheme();
                        $colorValue = $theme->get([$varName]);

                        if ($colorValue !== null) {
                            // Calculate hex with alpha
                            $opacity = floatval(rtrim($opacityStr, '%'));
                            if ($opacity > 1) {
                                $opacity = $opacity / 100;
                            }
                            $fallbackColor = LightningCss::colorWithAlpha($colorValue, $opacity);
                        } else {
                            // Unknown variable (arbitrary property) - fallback to just the variable
                            $fallbackColor = "var($varName)";
                        }
                    }

                    if ($needsPolyfill && $fallbackColor !== null) {
                        // Create fallback with the computed color
                        $fallbackDecl = [
                            'kind' => 'declaration',
                            'property' => $decl['property'],
                            'value' => $fallbackColor,
                            'important' => $decl['important'] ?? false,
                        ];
                        $newNodes[] = $fallbackDecl;

                        // Keep original for @supports
                        $supportsDeclarations[] = $decl;
                    } else {
                        $newNodes[] = $decl;
                    }
                } else {
                    $newNodes[] = $decl;
                }
            }

            // Add the rule with fallback declarations
            if (!empty($newNodes)) {
                $fallbackRule = $node;
                $fallbackRule['nodes'] = $newNodes;
                $result[] = $fallbackRule;
            }

            // Add @supports block if we have color-mix declarations
            if (!empty($supportsDeclarations)) {
                $supportsRule = [
                    'kind' => 'rule',
                    'selector' => $node['selector'],
                    'nodes' => $supportsDeclarations,
                ];
                $supports = Ast\atRule('@supports', '(color: color-mix(in lab, red, red))', [$supportsRule]);
                $result[] = $supports;
            }
        } elseif (isset($node['nodes'])) {
            // Recursively process nested nodes
            $node['nodes'] = applyColorMixPolyfill($node['nodes'], $designSystem);
            $result[] = $node;
        } else {
            $result[] = $node;
        }
    }

    return $result;
}

// =============================================================================
// Plugin System
// =============================================================================

use TailwindPHP\Plugin\PluginManager;

/** @var \TailwindPHP\Plugin\PluginManager|null Global plugin manager instance */
$_pluginManager = null;

/**
 * Get the global plugin manager instance.
 *
 * @return PluginManager
 */
function getPluginManager(): PluginManager
{
    global $_pluginManager;

    if ($_pluginManager === null) {
        $_pluginManager = new PluginManager();
    }

    return $_pluginManager;
}

/**
 * Register a plugin with the global plugin manager.
 *
 * @param \TailwindPHP\Plugin\PluginInterface $plugin The plugin to register
 */
function registerPlugin(\TailwindPHP\Plugin\PluginInterface $plugin): void
{
    getPluginManager()->register($plugin);
}

/**
 * Parse a plugin option value from CSS.
 *
 * Handles:
 * - Quoted strings: "value" or 'value'
 * - Numbers: 123, 1.5
 * - Booleans: true, false
 * - Lists: value1, value2 (space or comma separated)
 *
 * @param string $value The raw value string
 * @return mixed Parsed value
 */
function parsePluginOptionValue(string $value): mixed
{
    $value = trim($value);

    // Empty value
    if ($value === '') {
        return '';
    }

    // Quoted string
    if ((str_starts_with($value, '"') && str_ends_with($value, '"')) ||
        (str_starts_with($value, "'") && str_ends_with($value, "'"))) {
        return substr($value, 1, -1);
    }

    // Boolean
    if ($value === 'true') {
        return true;
    }
    if ($value === 'false') {
        return false;
    }

    // Number
    if (is_numeric($value)) {
        return str_contains($value, '.') ? (float) $value : (int) $value;
    }

    // Check for comma-separated list
    if (str_contains($value, ',')) {
        return array_map('trim', explode(',', $value));
    }

    // Plain string
    return $value;
}

/**
 * Tailwind facade class for cleaner API.
 *
 * Usage:
 *   use TailwindPHP\Tailwind;
 *   $css = Tailwind::generate('<div class="flex p-4">Hello</div>');
 */
class Tailwind
{
    /**
     * Generate CSS from content containing Tailwind classes.
     *
     * @param string|array $input HTML string or array with 'content' and 'css' keys
     * @param string $css Optional CSS (only used if $input is string)
     */
    public static function generate(string|array $input, string $css = '@import "tailwindcss";'): string
    {
        return generate($input, $css);
    }

    /**
     * Compile CSS with Tailwind utilities.
     */
    public static function compile(string $css, array $options = []): array
    {
        return compile($css, $options);
    }

    /**
     * Extract class name candidates from HTML content.
     */
    public static function extractCandidates(string $html): array
    {
        return extractCandidates($html);
    }
}

// =============================================================================
// Class Name Utilities
// =============================================================================
// PHP ports of popular Tailwind companion libraries (clsx, tailwind-merge).

require_once __DIR__ . '/_tailwindphp/lib/clsx/clsx.php';
require_once __DIR__ . '/_tailwindphp/lib/tailwind-merge/index.php';
require_once __DIR__ . '/_tailwindphp/lib/cva/cva.php';

/**
 * The ultimate class name utility: conditional classes + conflict resolution.
 *
 * This is the recommended way to work with Tailwind classes in PHP.
 * Combines conditional class construction with intelligent conflict resolution.
 *
 * @param mixed ...$inputs Class values (strings, arrays, conditionals)
 * @return string Merged class string with conflicts resolved
 *
 * @example
 * cn('px-2 py-1', 'px-4');                       // => 'py-1 px-4'
 * cn('text-red-500', ['text-blue-500' => true]); // => 'text-blue-500'
 * cn('hidden', ['block' => $isVisible]);         // => 'block' (if $isVisible)
 * cn('btn', 'btn-primary', ['btn-lg' => $large]);
 */
function cn(mixed ...$inputs): string
{
    return \TailwindPHP\Lib\TailwindMerge\cn(...$inputs);
}

/**
 * Merge Tailwind CSS classes, resolving conflicts.
 *
 * Later classes override earlier ones when they conflict.
 *
 * @param mixed ...$args Class values to merge
 * @return string Merged class string with conflicts resolved
 *
 * @example
 * merge('px-2 py-1', 'px-4');                      // => 'py-1 px-4'
 * merge('text-red-500', 'text-blue-500');          // => 'text-blue-500'
 * merge('hover:bg-red-500', 'hover:bg-blue-500');  // => 'hover:bg-blue-500'
 */
function merge(mixed ...$args): string
{
    return \TailwindPHP\Lib\TailwindMerge\twMerge(...$args);
}

/**
 * Join class names without conflict resolution.
 *
 * Use this when you know there are no conflicts for better performance.
 *
 * @param mixed ...$args Class values to join
 * @return string Joined class string
 *
 * @example
 * join('foo', 'bar');       // => 'foo bar'
 * join('foo', null, 'bar'); // => 'foo bar'
 */
function join(mixed ...$args): string
{
    return \TailwindPHP\Lib\TailwindMerge\twJoin(...$args);
}

// =============================================================================
// CVA (Class Variance Authority)
// =============================================================================
// PHP port of CVA for creating type-safe UI component variants.
// https://github.com/joe-bell/cva

/**
 * Create a class variance authority component.
 *
 * Provides a declarative API for managing component class variations
 * with base classes, variants, compound variants, and default variants.
 *
 * @param array|null $config Configuration with base, variants, compoundVariants, defaultVariants
 * @return callable A function that accepts a single props array and returns a class string
 *
 * @example
 * // Define component styles
 * $button = cva([
 *     'base' => 'btn font-semibold',
 *     'variants' => [
 *         'intent' => [
 *             'primary' => 'bg-blue-500 text-white',
 *             'secondary' => 'bg-gray-200 text-gray-800',
 *         ],
 *         'size' => [
 *             'sm' => 'text-sm px-2 py-1',
 *             'md' => 'text-base px-4 py-2',
 *         ],
 *     ],
 *     'defaultVariants' => [
 *         'intent' => 'primary',
 *         'size' => 'md',
 *     ],
 * ]);
 *
 * // React-style usage with single props object
 * $button();                                        // defaults applied
 * $button(['intent' => 'secondary']);               // override intent
 * $button(['size' => 'sm', 'class' => 'mt-4']);     // override + custom class
 *
 * // Use in a component function
 * function Button(array $props = []): string {
 *     static $styles = null;
 *     $styles ??= cva([...config...]);
 *     return '<button class="' . $styles($props) . '">' . ($props['children'] ?? '') . '</button>';
 * }
 */
function cva(?array $config = null): callable
{
    return \TailwindPHP\Lib\Cva\cva($config);
}

/**
 * Concatenate class values (similar to clsx).
 *
 * Flattens and joins class values, filtering out falsy values.
 *
 * @param mixed ...$inputs Class values (strings, arrays, null, false)
 * @return string Space-separated class string
 *
 * @example
 * cx('foo', 'bar');                    // => 'foo bar'
 * cx('foo', null, 'bar');              // => 'foo bar'
 * cx(['foo', 'bar']);                  // => 'foo bar'
 * cx(['foo', ['bar', ['baz']]]);       // => 'foo bar baz'
 */
function cx(mixed ...$inputs): string
{
    return \TailwindPHP\Lib\Cva\cx(...$inputs);
}

/**
 * Compose multiple CVA components into one.
 *
 * Merges variants from multiple components, allowing you to combine
 * reusable variant definitions.
 *
 * @param callable ...$components CVA component functions
 * @return callable A function that accepts merged props and returns a class string
 *
 * @example
 * $box = cva(['variants' => ['shadow' => ['sm' => 'shadow-sm', 'md' => 'shadow-md']]]);
 * $stack = cva(['variants' => ['gap' => ['1' => 'gap-1', '2' => 'gap-2']]]);
 * $card = compose($box, $stack);
 *
 * $card(['shadow' => 'md', 'gap' => '2']); // => 'shadow-md gap-2'
 */
function compose(callable ...$components): callable
{
    return \TailwindPHP\Lib\Cva\compose(...$components);
}
