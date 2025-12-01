<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use TailwindPHP\Theme;
use TailwindPHP\DesignSystem;
use function TailwindPHP\compile;
use function TailwindPHP\toCss;
use function TailwindPHP\parse;
use function TailwindPHP\buildDesignSystem;
use function TailwindPHP\createUtilities;

/**
 * Test utilities mirroring the original test-utils/run.ts.
 *
 * These functions provide the same API as the TypeScript tests use.
 */

/**
 * Compile CSS with the given candidates.
 *
 * This mirrors the compileCss function from test-utils/run.ts:
 * ```ts
 * export async function compileCss(css: string, candidates: string[] = []) {
 *   let { build } = await compile(css)
 *   return optimize(build(candidates)).code.trim()
 * }
 * ```
 *
 * @param string $css CSS input with @theme and @tailwind directives
 * @param array<string> $candidates List of utility class names to generate
 * @return string Generated CSS
 */
function compileCss(string $css, array $candidates = []): string
{
    $compiled = compile($css);
    $output = $compiled['build']($candidates);
    return trim($output);
}

/**
 * Run utilities with default theme.
 *
 * This mirrors the run function from test-utils/run.ts:
 * ```ts
 * export async function run(candidates: string[]) {
 *   let { build } = await compile('@tailwind utilities;')
 *   return optimize(build(candidates)).code.trim()
 * }
 * ```
 *
 * @param array<string> $candidates List of utility class names to generate
 * @return string Generated CSS
 */
function run(array $candidates): string
{
    return compileCss('@tailwind utilities;', $candidates);
}

/**
 * Optimize CSS output (placeholder for now).
 *
 * @param string $css
 * @return string
 */
function optimizeCss(string $css): string
{
    // For now, just return as-is
    // Full optimization will be implemented later
    return $css;
}
