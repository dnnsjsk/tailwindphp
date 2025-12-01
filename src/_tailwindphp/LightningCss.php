<?php

declare(strict_types=1);

namespace TailwindPHP;

/**
 * CSS Optimizer - PHP implementation of lightningcss transformations.
 *
 * This is NOT part of the TailwindCSS port. It's a PHP implementation of
 * the CSS optimizations that lightningcss performs in the original Tailwind.
 *
 * lightningcss is a fast CSS parser, transformer, and minifier written in Rust.
 * TailwindCSS uses it to post-process generated CSS. Since we can't use the
 * Rust library directly in PHP, we implement the relevant transformations here.
 *
 * @see https://lightningcss.dev/
 */
class LightningCss
{
    /**
     * Optimize a complete CSS string.
     *
     * @param string $css The CSS to optimize
     * @return string Optimized CSS
     */
    public static function optimize(string $css): string
    {
        // For now, we optimize at the value level during generation.
        // Full CSS string optimization can be added here later if needed.
        return $css;
    }

    /**
     * Optimize a CSS property value.
     *
     * @param string $value The CSS value to optimize
     * @param string $property The CSS property name (for context-aware optimization)
     * @return string Optimized value
     */
    public static function optimizeValue(string $value, string $property = ''): string
    {
        $value = self::simplifyCalcExpressions($value);
        $value = self::normalizeLeadingZeros($value);
        $value = self::normalizeGridValues($value, $property);
        $value = self::normalizeTransformFunctions($value, $property);

        return $value;
    }

    /**
     * Simplify calc() expressions where possible.
     *
     * lightningcss simplifies calc expressions like:
     * - calc(45deg * -1) -> -45deg
     * - calc(90deg * -1) -> -90deg
     *
     * Only applies to angle units (deg, rad, grad, turn).
     * Length units (px, rem, em) stay as calc() for properties like outline-offset.
     * Expressions with var() must stay as calc().
     *
     * @param string $value The CSS value
     * @return string Simplified value
     */
    public static function simplifyCalcExpressions(string $value): string
    {
        // Match: calc(NUMBER UNIT * -1) for angle units only
        if (preg_match('/^calc\(([+-]?\d*\.?\d+)(deg|rad|grad|turn)\s*\*\s*-1\)$/', $value, $m)) {
            $num = $m[1];
            $unit = $m[2];

            // If number is already negative, make it positive
            if (str_starts_with($num, '-')) {
                return substr($num, 1) . $unit;
            }
            return '-' . $num . $unit;
        }

        return $value;
    }

    /**
     * Normalize leading zeros in decimal numbers.
     *
     * lightningcss removes leading zeros: 0.5 -> .5, 0.25 -> .25
     *
     * @param string $value The CSS value
     * @return string Normalized value
     */
    public static function normalizeLeadingZeros(string $value): string
    {
        // Match 0.X at word boundaries and replace with .X
        return preg_replace('/\b0+(\.\d+)/', '$1', $value);
    }

    /**
     * Normalize grid-related values.
     *
     * lightningcss normalizations for grid:
     * - Add spaces around / in span values: "span 1/span 2" -> "span 1 / span 2"
     * - Convert bare integers to px for grid-template-*: 123 -> 123px
     *
     * @param string $value The CSS value
     * @param string $property The CSS property name
     * @return string Normalized value
     */
    public static function normalizeGridValues(string $value, string $property = ''): string
    {
        // Add spaces around / in grid span values
        // Match patterns like "span 123/span 123" -> "span 123 / span 123"
        if (str_contains($value, 'span') && str_contains($value, '/')) {
            $value = preg_replace('/(\S)\/(\S)/', '$1 / $2', $value);
        }

        // Convert bare integers to px for grid-template-columns/rows
        // lightningcss does this normalization: 123 -> 123px
        // Only for grid-template-* properties, NOT for grid-column/grid-row (which use line numbers)
        if (preg_match('/^\d+$/', $value) &&
            ($property === 'grid-template-columns' || $property === 'grid-template-rows')) {
            $value = $value . 'px';
        }

        return $value;
    }

    /**
     * Normalize transform function spacing.
     *
     * lightningcss removes spaces between consecutive transform functions:
     * "scaleZ(2) rotateY(45deg)" -> "scaleZ(2)rotateY(45deg)"
     *
     * Only applies to values without var() calls, as those need spaces preserved.
     *
     * @param string $value The CSS value
     * @param string $property The CSS property name
     * @return string Normalized value
     */
    public static function normalizeTransformFunctions(string $value, string $property = ''): string
    {
        // Only apply to transform property
        if ($property !== 'transform') {
            return $value;
        }

        // Don't apply to values with CSS variables - they need spaces preserved
        // e.g., "var(--tw-rotate-x, ) var(--tw-rotate-y, )"
        if (str_contains($value, 'var(')) {
            return $value;
        }

        // Remove spaces between consecutive transform functions
        // Match ") " followed by a function name and "("
        return preg_replace('/\)\s+([a-zA-Z]+\()/', ')$1', $value);
    }

    /**
     * Minify a CSS string (optional, for production builds).
     *
     * @param string $css The CSS to minify
     * @return string Minified CSS
     */
    public static function minify(string $css): string
    {
        // Remove comments
        $css = preg_replace('/\/\*[\s\S]*?\*\//', '', $css);

        // Remove unnecessary whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*([{};:,])\s*/', '$1', $css);

        // Remove trailing semicolons before closing braces
        $css = str_replace(';}', '}', $css);

        return trim($css);
    }
}
