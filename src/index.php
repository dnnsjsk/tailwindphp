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
use function TailwindPHP\Ast\decl;
use function TailwindPHP\Walk\walk;
use TailwindPHP\Walk\WalkAction;
use TailwindPHP\LightningCss\LightningCss;

/**
 * TailwindPHP - CSS-first Tailwind CSS compiler for PHP.
 *
 * Port of: packages/tailwindcss/src/index.ts
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

    // Walk AST to find @tailwind utilities, @theme, @source, @media important
    walk($ast, function (&$node, $ctx) use (&$features, &$theme, &$utilitiesNodePath, &$sources, &$firstThemeRule, &$important, $options) {
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

            // Process theme declarations
            foreach ($node['nodes'] ?? [] as $child) {
                if ($child['kind'] === 'declaration' && str_starts_with($child['property'], '--')) {
                    $theme->add($child['property'], $child['value'] ?? '', $themeOptions);
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

        // Handle @media important
        if ($node['name'] === '@media') {
            $params = \TailwindPHP\Utils\segment($node['params'], ' ');
            $unknownParams = [];

            foreach ($params as $param) {
                if ($param === 'important') {
                    $important = true;
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
                    if ($value['options'] & Theme::OPTIONS_REFERENCE) {
                        continue;
                    }
                    $nodes[] = decl(\TailwindPHP\Utils\escape($key), $value['value']);
                }
                $node['nodes'] = $nodes;
                return WalkAction::Stop;
            }
            return WalkAction::Continue;
        });
    }

    // Build the design system
    $designSystem = buildDesignSystem($theme);

    // Set important flag on design system
    if ($important) {
        $designSystem->setImportant(true);
    }

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

    $transform = function (array $node, array &$parent) use (&$transform) {
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

        // Handle rules with children
        if (($node['kind'] === 'rule' || $node['kind'] === 'at-rule') && isset($node['nodes'])) {
            $children = [];
            foreach ($node['nodes'] as $child) {
                $transform($child, $children);
            }
            $node['nodes'] = $children;

            // Skip empty rules (no declarations or nested rules)
            if (empty($children)) {
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

    return $result;
}

/**
 * Simple entry point for generating CSS from HTML content.
 *
 * @param string $html HTML content to scan for classes
 * @param string $css Optional CSS with @tailwind directives
 * @return string Generated CSS
 */
function generate(string $html, string $css = '@tailwind utilities;'): string
{
    // Extract class names from HTML
    $candidates = extractCandidates($html);

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
 * Tailwind facade class for cleaner API.
 *
 * Usage:
 *   use TailwindPHP\Tailwind;
 *   $css = Tailwind::generate('<div class="flex p-4">Hello</div>');
 */
class Tailwind
{
    /**
     * Generate CSS from HTML containing Tailwind classes.
     */
    public static function generate(string $html, string $css = '@tailwind utilities;'): string
    {
        return generate($html, $css);
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
