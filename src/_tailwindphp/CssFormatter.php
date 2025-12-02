<?php

declare(strict_types=1);

namespace TailwindPHP\CssFormatter;

/**
 * CSS Formatter - Format AST nodes into CSS strings.
 *
 * @port-deviation:helper This is NOT part of the TailwindCSS port.
 * It's a simplified CSS formatter that handles the test output format.
 *
 * The full ast.php has toCss() which is more complete. This formatter
 * specifically handles the nested rules expansion needed for test comparison.
 */
class CssFormatter
{
    /**
     * Format CSS rules into a string.
     *
     * @param array $rules Array of rule objects with selector, nodes, and important
     * @return string Formatted CSS
     */
    public static function format(array $rules): string
    {
        if (empty($rules)) {
            return '';
        }

        $output = [];

        foreach ($rules as $rule) {
            $selector = $rule['selector'];
            $formatted = self::formatRule($selector, $rule['nodes'], $rule['important'] ?? false);
            if ($formatted !== '') {
                $output[] = $formatted;
            }
        }

        return implode("\n\n", $output);
    }

    /**
     * Format a rule and its nodes, handling nested rules.
     *
     * @param string $parentSelector The parent selector
     * @param array $nodes Array of AST nodes (declarations and nested rules)
     * @param bool $important Whether to add !important
     * @return string Formatted CSS
     */
    private static function formatRule(string $parentSelector, array $nodes, bool $important): string
    {
        $declarations = [];
        $nestedRules = [];

        foreach ($nodes as $node) {
            if ($node['kind'] === 'declaration') {
                $value = $node['value'];
                if ($important || ($node['important'] ?? false)) {
                    $value .= ' !important';
                }
                $declarations[] = "  {$node['property']}: {$value};";
            } elseif ($node['kind'] === 'rule') {
                // Handle nested rule - replace & with parent selector
                $nestedSelector = str_replace('&', $parentSelector, $node['selector']);
                $nestedFormatted = self::formatRule($nestedSelector, $node['nodes'], $important);
                if ($nestedFormatted !== '') {
                    $nestedRules[] = $nestedFormatted;
                }
            }
        }

        $output = [];

        // Add declarations for this rule
        if (!empty($declarations)) {
            $output[] = "{$parentSelector} {\n" . implode("\n", $declarations) . "\n}";
        }

        // Add nested rules
        foreach ($nestedRules as $nested) {
            $output[] = $nested;
        }

        return implode("\n\n", $output);
    }
}
