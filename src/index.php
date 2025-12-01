<?php

declare(strict_types=1);

namespace TailwindPHP;

use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Variants\Variants;
use TailwindPHP\DesignSystem\DesignSystem;
use function TailwindPHP\DesignSystem\buildDesignSystem;
use function TailwindPHP\CssParser\parse;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\Walk\walk;

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
    $utilitiesNode = $result['utilitiesNode'];
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

    return [
        'sources' => $sources,
        'root' => $root,
        'features' => $features,
        'build' => function (array $newRawCandidates) use (
            &$allValidCandidates,
            &$compiled,
            &$previousAstNodeCount,
            $designSystem,
            $utilitiesNode,
            $ast,
            $features,
            $options
        ) {
            if ($features === FEATURE_NONE) {
                return toCss($ast);
            }

            if ($utilitiesNode === null) {
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
            $utilitiesNode['nodes'] = $newNodes;

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
function parseCss(array $ast, array $options = []): array
{
    $features = FEATURE_NONE;
    $theme = new Theme();
    $utilitiesNode = null;
    $sources = [];
    $inlineCandidates = [];
    $root = null;
    $firstThemeRule = null;

    // Walk the AST and process @theme, @tailwind, etc.
    walk($ast, function (&$node, $context) use (
        &$features,
        &$theme,
        &$utilitiesNode,
        &$sources,
        &$inlineCandidates,
        &$root,
        &$firstThemeRule
    ) {
        if ($node['kind'] !== 'at-rule') {
            return null;
        }

        // Handle @tailwind utilities
        if ($node['name'] === '@tailwind' &&
            ($node['params'] === 'utilities' || str_starts_with($node['params'], 'utilities'))
        ) {
            if ($utilitiesNode !== null) {
                return []; // Remove duplicate
            }

            $utilitiesNode = &$node;
            $features |= FEATURE_UTILITIES;
            return null;
        }

        // Handle @theme
        if ($node['name'] === '@theme') {
            $features |= FEATURE_AT_THEME;

            $themeOptions = parseThemeOptions($node['params']);

            // Process theme declarations
            foreach ($node['nodes'] ?? [] as $child) {
                if ($child['kind'] === 'declaration' && str_starts_with($child['property'], '--')) {
                    $theme->add($child['property'], $child['value'] ?? '', $themeOptions);
                }
            }

            // Replace first @theme with a style rule for :root
            if ($firstThemeRule === null) {
                $firstThemeRule = styleRule(':root, :host', []);
                return $firstThemeRule;
            }

            return []; // Remove subsequent @theme blocks
        }

        // Handle @source
        if ($node['name'] === '@source') {
            $path = trim($node['params'], "\"'");
            $sources[] = [
                'base' => $context['base'] ?? '',
                'pattern' => $path,
                'negated' => false,
            ];
            return [];
        }

        return null;
    });

    // Build the design system
    $designSystem = buildDesignSystem($theme);

    // Output theme variables
    if ($firstThemeRule !== null) {
        $nodes = [];
        foreach ($theme->entries() as [$key, $value]) {
            if ($value['options'] & Theme::OPTIONS_REFERENCE) {
                continue;
            }
            $nodes[] = decl(\TailwindPHP\Utils\escape($key), $value['value']);
        }
        $firstThemeRule['nodes'] = $nodes;
    }

    return [
        'designSystem' => $designSystem,
        'ast' => $ast,
        'sources' => $sources,
        'root' => $root,
        'utilitiesNode' => $utilitiesNode,
        'features' => $features,
        'inlineCandidates' => $inlineCandidates,
    ];
}

/**
 * Parse @theme options from params string.
 *
 * @param string $params
 * @return int Theme options flags
 */
function parseThemeOptions(string $params): int
{
    $options = Theme::OPTIONS_NONE;

    foreach (\TailwindPHP\Utils\segment($params, ' ') as $option) {
        switch ($option) {
            case 'reference':
                $options |= Theme::OPTIONS_REFERENCE;
                break;
            case 'inline':
                $options |= Theme::OPTIONS_INLINE;
                break;
            case 'default':
                $options |= Theme::OPTIONS_DEFAULT;
                break;
            case 'static':
                $options |= Theme::OPTIONS_STATIC;
                break;
        }
    }

    return $options;
}

/**
 * Optimize AST by applying polyfills and removing empty nodes.
 *
 * @param array $ast
 * @param DesignSystem $designSystem
 * @param int $polyfills
 * @return array
 */
function optimizeAst(array $ast, DesignSystem $designSystem, int $polyfills = POLYFILL_ALL): array
{
    // For now, just return the AST as-is
    // Full optimization will be implemented later
    return $ast;
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
