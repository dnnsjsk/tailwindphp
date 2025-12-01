<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use TailwindPHP\Theme;
use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Utilities\UtilityBuilder;
use TailwindPHP\Variants\Variants;
use function TailwindPHP\toCss;
use function TailwindPHP\Utils\escape;

/**
 * Test helper class for running utility tests.
 *
 * Mirrors the test-utils/run.ts from the original Tailwind.
 */
class TestHelper
{
    private Theme $theme;
    private Utilities $utilities;
    private Variants $variants;

    public function __construct(?Theme $theme = null)
    {
        $this->theme = $theme ?? $this->createDefaultTheme();
        $this->utilities = new Utilities();
        $this->variants = new Variants();
    }

    /**
     * Create a default theme with common spacing values.
     */
    private function createDefaultTheme(): Theme
    {
        $theme = new Theme();

        // Add default spacing values
        $theme->add('--spacing', '0.25rem');
        $theme->add('--spacing-0', '0px');
        $theme->add('--spacing-1', '0.25rem');
        $theme->add('--spacing-2', '0.5rem');
        $theme->add('--spacing-3', '0.75rem');
        $theme->add('--spacing-4', '1rem');
        $theme->add('--spacing-5', '1.25rem');
        $theme->add('--spacing-6', '1.5rem');
        $theme->add('--spacing-8', '2rem');
        $theme->add('--spacing-10', '2.5rem');
        $theme->add('--spacing-12', '3rem');
        $theme->add('--spacing-16', '4rem');
        $theme->add('--spacing-20', '5rem');
        $theme->add('--spacing-24', '6rem');

        return $theme;
    }

    /**
     * Get the theme.
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * Get the utilities.
     */
    public function getUtilities(): Utilities
    {
        return $this->utilities;
    }

    /**
     * Get the utility builder.
     */
    public function getBuilder(): UtilityBuilder
    {
        return new UtilityBuilder($this->utilities, $this->theme);
    }

    /**
     * Register utilities using a callback.
     *
     * @param callable $registerFn Function that receives a UtilityBuilder
     */
    public function registerUtilities(callable $registerFn): self
    {
        $registerFn($this->getBuilder());
        return $this;
    }

    /**
     * Run utilities and generate CSS for the given candidates.
     *
     * @param array<string> $candidates Array of class names
     * @return string Generated CSS
     */
    public function run(array $candidates): string
    {
        $css = [];

        foreach ($candidates as $candidate) {
            $result = $this->compileCandidate($candidate);
            if ($result !== null) {
                $css[] = $result;
            }
        }

        // Sort CSS rules alphabetically by selector for consistent output
        usort($css, fn($a, $b) => strcmp($a['selector'], $b['selector']));

        return $this->formatCss($css);
    }

    /**
     * Compile a single candidate to CSS.
     *
     * @param string $candidate
     * @return array|null
     */
    private function compileCandidate(string $candidate): ?array
    {
        // Check for important modifier
        $important = false;
        $base = $candidate;

        if (str_ends_with($base, '!')) {
            $important = true;
            $base = substr($base, 0, -1);
        } elseif (str_starts_with($base, '!')) {
            $important = true;
            $base = substr($base, 1);
        }

        // Check for negative prefix
        $negative = false;
        if (str_starts_with($base, '-') && $this->utilities->has(substr($base, 1), 'static')) {
            // Invalid - static utilities can't be negative
            return null;
        }

        // Check for static utility
        if ($this->utilities->has($base, 'static')) {
            $utils = $this->utilities->get($base);
            foreach ($utils as $util) {
                if ($util['kind'] === 'static') {
                    $nodes = $util['compileFn']();
                    if ($nodes !== null) {
                        return [
                            'selector' => '.' . escape($candidate),
                            'nodes' => $nodes,
                            'important' => $important,
                        ];
                    }
                }
            }
        }

        // Check for functional utility
        // This is simplified - full implementation would parse the candidate properly
        $parts = $this->parseFunctionalCandidate($base);
        if ($parts !== null) {
            [$root, $value] = $parts;

            if ($this->utilities->has($root, 'functional')) {
                $utils = $this->utilities->get($root);
                foreach ($utils as $util) {
                    if ($util['kind'] === 'functional') {
                        $candidateObj = [
                            'kind' => 'functional',
                            'root' => $root,
                            'value' => $value,
                            'modifier' => null,
                            'important' => $important,
                            'raw' => $candidate,
                        ];

                        $nodes = $util['compileFn']($candidateObj);
                        if ($nodes !== null && $nodes !== false) {
                            return [
                                'selector' => '.' . escape($candidate),
                                'nodes' => $nodes,
                                'important' => $important,
                            ];
                        }
                    }
                }
            }
        }

        return null;
    }

    /**
     * Parse a functional candidate into root and value.
     *
     * @param string $candidate
     * @return array|null [root, value] or null
     */
    private function parseFunctionalCandidate(string $candidate): ?array
    {
        // Try to find the root by testing progressively shorter prefixes
        $idx = strrpos($candidate, '-');

        while ($idx !== false && $idx > 0) {
            $maybeRoot = substr($candidate, 0, $idx);

            if ($this->utilities->has($maybeRoot, 'functional')) {
                $value = substr($candidate, $idx + 1);
                if ($value === '') {
                    return null;
                }

                // Determine value kind
                $valueObj = null;
                if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                    $valueObj = [
                        'kind' => 'arbitrary',
                        'value' => substr($value, 1, -1),
                        'dataType' => null,
                    ];
                } else {
                    $valueObj = [
                        'kind' => 'named',
                        'value' => $value,
                        'fraction' => null,
                    ];
                }

                return [$maybeRoot, $valueObj];
            }

            $idx = strrpos(substr($candidate, 0, $idx), '-');
        }

        // Check for exact match (utility with default value)
        if ($this->utilities->has($candidate, 'functional')) {
            return [$candidate, null];
        }

        return null;
    }

    /**
     * Format CSS rules into a string.
     *
     * @param array $rules
     * @return string
     */
    private function formatCss(array $rules): string
    {
        if (empty($rules)) {
            return '';
        }

        $output = [];

        foreach ($rules as $rule) {
            $selector = $rule['selector'];
            $declarations = [];

            foreach ($rule['nodes'] as $node) {
                if ($node['kind'] === 'declaration') {
                    $value = $node['value'];
                    if ($rule['important'] || ($node['important'] ?? false)) {
                        $value .= ' !important';
                    }
                    $declarations[] = "  {$node['property']}: {$value};";
                }
            }

            if (!empty($declarations)) {
                $output[] = "{$selector} {\n" . implode("\n", $declarations) . "\n}";
            }
        }

        return implode("\n\n", $output);
    }
}
