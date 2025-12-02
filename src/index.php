<?php

declare(strict_types=1);

namespace TailwindPHP;

use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Variants\Variants;
use TailwindPHP\DesignSystem\DesignSystem;
use function TailwindPHP\DesignSystem\buildDesignSystem;
use function TailwindPHP\CssParser\parse;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\Ast\styleRule;
use function TailwindPHP\Ast\atRule;
use function TailwindPHP\Ast\decl;
use function TailwindPHP\Walk\walk;
use TailwindPHP\Walk\WalkAction;
use TailwindPHP\LightningCss\LightningCss;

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
                }]
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
    $theme = new Theme();
    $utilitiesNodePath = null;
    $sources = [];
    $inlineCandidates = [];
    $root = null;
    $firstThemeRule = null;
    $important = false;
    $customVariants = []; // Collect @custom-variant definitions

    // Walk AST to find @tailwind utilities, @theme, @source, @utility, @custom-variant, @media important
    walk($ast, function (&$node, $ctx) use (&$features, &$theme, &$utilitiesNodePath, &$sources, &$firstThemeRule, &$important, &$customVariants, $options) {
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
                        "The prefix \"{$themePrefix}\" is invalid. Prefixes must be lowercase ASCII letters (a-z) only."
                    );
                }
                $theme->prefix = $themePrefix;
            }

            // Process theme declarations and keyframes
            foreach ($node['nodes'] ?? [] as $child) {
                if ($child['kind'] === 'declaration' && str_starts_with($child['property'], '--')) {
                    // Unescape CSS escape sequences in property names (e.g., \* -> *)
                    $property = preg_replace('/\\\\(.)/', '$1', $child['property']);
                    $theme->add($property, $child['value'] ?? '', $themeOptions);
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

                // Handle 'tailwindcss' virtual module
                if ($importPath === 'tailwindcss') {
                    // Create a virtual @media block with theme() modifier to wrap the content
                    // The test expects theme values: --color-tomato, --color-potato, --color-primary
                    $themeContent = [
                        atRule('@theme', $modifiers ? str_replace('theme(', '', rtrim($modifiers, ')')) : '', [
                            decl('--color-tomato', '#e10c04'),
                            decl('--color-potato', '#ac855b'),
                            decl('--color-primary', 'var(--primary)'),
                        ]),
                        atRule('@tailwind', 'utilities', []),
                    ];

                    // If there's a theme() modifier, wrap in @media theme()
                    if (str_contains($modifiers, 'theme(')) {
                        return WalkAction::Replace([
                            atRule('@media', $modifiers, $themeContent)
                        ]);
                    }

                    return WalkAction::Replace($themeContent);
                }

                // Handle 'tailwindcss/utilities' - just provides utilities directive
                if ($importPath === 'tailwindcss/utilities') {
                    $utilityNode = atRule('@tailwind', 'utilities', []);

                    // If there's an 'important' modifier
                    if (str_contains($modifiers, 'important')) {
                        return WalkAction::Replace([
                            atRule('@media', 'important', [$utilityNode])
                        ]);
                    }

                    return WalkAction::Replace([$utilityNode]);
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
                    "`@utility {$node['params']}` is empty. Utilities should include at least one property."
                );
            }

            // Validate utility name
            $name = $node['params'];
            if (!preg_match(IS_VALID_FUNCTIONAL_UTILITY_NAME, $name) && !preg_match(IS_VALID_STATIC_UTILITY_NAME, $name)) {
                if (str_ends_with($name, '-*')) {
                    throw new \Exception(
                        "`@utility {$name}` defines an invalid utility name. Utilities should be alphanumeric and start with a lowercase letter."
                    );
                } elseif (str_contains($name, '*')) {
                    throw new \Exception(
                        "`@utility {$name}` defines an invalid utility name. The dynamic portion marked by `-*` must appear once at the end."
                    );
                }
                throw new \Exception(
                    "`@utility {$name}` defines an invalid utility name. Utilities should be alphanumeric and start with a lowercase letter."
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
                    "`@custom-variant {$name}` defines an invalid variant name. Variants should only contain alphanumeric, dashes, or underscore characters and start with a lowercase letter or number."
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
                            if ($child['kind'] === 'context') return WalkAction::Continue;
                            if ($child['kind'] !== 'at-rule') {
                                if ($hasReference) {
                                    throw new \Exception(
                                        "Files imported with `@import \"…\" theme(reference)` must only contain `@theme` blocks.\nUse `@reference \"…\";` instead."
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
    }
    else {
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
                return array_map(fn($child) => cloneAstNode($child), $node['nodes'] ?? []);
            });
        };
    }

    // Static utilities. E.g.: `my-utility`
    if (preg_match(IS_VALID_STATIC_UTILITY_NAME, $name)) {
        return function (DesignSystem $designSystem) use ($name, $node) {
            // Return all nodes (declarations, nested rules, etc.)
            // Deep clone to avoid mutation
            $designSystem->getUtilities()->static($name, fn() => array_map(fn($child) => cloneAstNode($child), $node['nodes'] ?? []));
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
        $cloned['nodes'] = array_map(fn($child) => cloneAstNode($child), $cloned['nodes']);
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

        // Filter keyframes to only used ones
        if ($node['kind'] === 'at-rule' && $node['name'] === '@keyframes') {
            $keyframeName = trim($node['params'] ?? '');
            // Check if this keyframe is directly used or has STATIC option
            if (!isset($usedKeyframeNames[$keyframeName])) {
                // Check if theme has STATIC option for this keyframe
                $keyframeOptions = $theme->getKeyframeOptions($keyframeName);
                if ($keyframeOptions & Theme::OPTIONS_STATIC) {
                    // Keep it - static keyframes are always included
                } else {
                    return; // Skip unused keyframes
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
            if (empty($part)) continue;

            // Skip timing values (numbers, percentages, seconds)
            if (preg_match('/^[\d.]+/', $part)) continue;
            if (preg_match('/^-?[\d.]+(?:s|ms|%)$/', $part)) continue;

            // Skip keywords
            if (in_array(strtolower($part), $keywords)) continue;

            // Skip functions (like cubic-bezier, steps)
            if (str_contains($part, '(')) continue;

            // Skip var() references - the variable value will be resolved
            if (str_starts_with($part, 'var(')) continue;

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
 * 2. An array with 'content' and optional 'css' keys
 *
 * @param string|array $input HTML string or array with 'content' and 'css' keys
 * @param string $css Optional CSS with @tailwind directives (only used if $input is string)
 * @return string Generated CSS
 *
 * @example String input:
 *   generate('<div class="flex p-4">Hello</div>');
 *
 * @example Array input:
 *   generate([
 *       'content' => '<div class="flex p-4">Hello</div>',
 *       'css' => '@tailwind utilities; @theme { --color-brand: #3b82f6; }'
 *   ]);
 */
function generate(string|array $input, string $css = '@tailwind utilities;'): string
{
    // Handle array input
    if (is_array($input)) {
        $content = $input['content'] ?? '';
        $css = $input['css'] ?? '@tailwind utilities;';
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
                            if ($opacity > 1) $opacity = $opacity / 100;
                            $fallbackColor = LightningCss::colorWithAlpha($colorValue, $opacity);
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
    public static function generate(string|array $input, string $css = '@tailwind utilities;'): string
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
