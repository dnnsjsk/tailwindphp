<?php

declare(strict_types=1);

namespace TailwindPHP\Plugin;

use TailwindPHP\Theme;
use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Variants\Variants;

/**
 * Plugin API - Interface for TailwindCSS plugins.
 *
 * This mirrors the TailwindCSS v4 plugin API exactly, allowing plugins
 * to register utilities, variants, and components.
 *
 * @see https://tailwindcss.com/docs/plugins
 */
class PluginAPI
{
    private Theme $theme;
    private Utilities $utilities;
    private Variants $variants;
    private array $config;
    private array $baseStyles = [];
    private array $componentStyles = [];

    public function __construct(
        Theme $theme,
        Utilities $utilities,
        Variants $variants,
        array $config = [],
    ) {
        $this->theme = $theme;
        $this->utilities = $utilities;
        $this->variants = $variants;
        $this->config = $config;
    }

    /**
     * Add base styles (applied to @layer base).
     *
     * @param array $css CSS-in-JS style array
     */
    public function addBase(array $css): void
    {
        $this->baseStyles[] = $css;
    }

    /**
     * Get all registered base styles.
     *
     * @return array
     */
    public function getBaseStyles(): array
    {
        return $this->baseStyles;
    }

    /**
     * Add static utility classes.
     *
     * @param array $utilities Map of class name to CSS-in-JS
     * @param array $options Optional settings (variants, respectPrefix, respectImportant)
     */
    public function addUtilities(array $utilities, array $options = []): void
    {
        foreach ($utilities as $className => $css) {
            $this->registerUtility($className, $css, $options);
        }
    }

    /**
     * Add functional utilities that accept values.
     *
     * @param array $utilities Map of utility name to callback function
     * @param array $options Settings including values, type, supportsNegativeValues, modifiers
     */
    public function matchUtilities(array $utilities, array $options = []): void
    {
        $values = $options['values'] ?? [];
        $type = $options['type'] ?? null;
        $supportsNegativeValues = $options['supportsNegativeValues'] ?? false;
        $modifiers = $options['modifiers'] ?? null;

        foreach ($utilities as $name => $callback) {
            // Register each value combination
            foreach ($values as $key => $value) {
                $className = $key === 'DEFAULT' ? $name : "{$name}-{$key}";
                $css = $callback($value, ['modifier' => null]);

                if ($css !== null) {
                    $this->registerUtility(".{$className}", $css, $options);
                }

                // Handle negative values
                if ($supportsNegativeValues && is_numeric($value)) {
                    $negativeValue = $this->negate($value);
                    $negativeClassName = "-{$className}";
                    $negativeCss = $callback($negativeValue, ['modifier' => null]);

                    if ($negativeCss !== null) {
                        $this->registerUtility(".{$negativeClassName}", $negativeCss, $options);
                    }
                }
            }

            // Store the callback for arbitrary value support
            $this->utilities->addFunctional($name, $callback, $options);
        }
    }

    /**
     * Add static component classes (alias for addUtilities with component layer).
     *
     * @param array $components Map of class name to CSS-in-JS
     * @param array $options Optional settings
     */
    public function addComponents(array $components, array $options = []): void
    {
        foreach ($components as $className => $css) {
            $this->componentStyles[$className] = $css;
            $this->registerUtility($className, $css, array_merge($options, ['layer' => 'components']));
        }
    }

    /**
     * Get all registered component styles.
     *
     * @return array
     */
    public function getComponentStyles(): array
    {
        return $this->componentStyles;
    }

    /**
     * Add functional components that accept values.
     *
     * @param array $components Map of component name to callback function
     * @param array $options Settings including values, type, modifiers
     */
    public function matchComponents(array $components, array $options = []): void
    {
        // Same as matchUtilities but for component layer
        $this->matchUtilities($components, array_merge($options, ['layer' => 'components']));
    }

    /**
     * Add a static variant.
     *
     * @param string $name Variant name
     * @param string|array $variant Selector string, array of selectors, or CSS-in-JS
     */
    public function addVariant(string $name, string|array $variant): void
    {
        $this->variants->addPluginVariant($name, $variant);
    }

    /**
     * Add a functional variant that accepts values.
     *
     * @param string $name Variant name
     * @param callable $callback Function that returns selector(s)
     * @param array $options Settings including values, sort
     */
    public function matchVariant(string $name, callable $callback, array $options = []): void
    {
        $values = $options['values'] ?? [];

        foreach ($values as $key => $value) {
            $variantName = $key === 'DEFAULT' ? $name : "{$name}-{$key}";
            $selector = $callback($value, ['modifier' => null]);

            $this->variants->addPluginVariant($variantName, $selector);
        }

        // Store callback for arbitrary value support
        $this->variants->addFunctionalVariant($name, $callback, $options);
    }

    /**
     * Get a value from the theme.
     *
     * @param string $path Dot-notation path (e.g., 'colors.red.500')
     * @param mixed $defaultValue Default value if not found
     * @return mixed
     */
    public function theme(string $path, mixed $defaultValue = null): mixed
    {
        // Handle opacity modifier syntax: 'colors.red.500 / 50%'
        $modifier = null;
        if (str_contains($path, '/')) {
            $parts = explode('/', $path, 2);
            $path = trim($parts[0]);
            $modifier = trim($parts[1]);
        }

        $value = $this->resolveThemePath($path, $defaultValue);

        // Apply opacity modifier if present
        if ($modifier !== null && is_string($value)) {
            return $this->applyOpacityModifier($value, $modifier);
        }

        return $value;
    }

    /**
     * Get a value from the config.
     *
     * @param string|null $path Dot-notation path
     * @param mixed $defaultValue Default value if not found
     * @return mixed
     */
    public function config(?string $path = null, mixed $defaultValue = null): mixed
    {
        if ($path === null) {
            return $this->config;
        }

        return $this->resolvePath($this->config, $path, $defaultValue);
    }

    /**
     * Get the configured prefix.
     *
     * @param string $className Class name to prefix
     * @return string
     */
    public function prefix(string $className): string
    {
        $prefix = $this->theme->getPrefix();

        if ($prefix === null) {
            return $className;
        }

        return "{$prefix}:{$className}";
    }

    /**
     * Register a utility with the utilities registry.
     */
    private function registerUtility(string $className, array $css, array $options): void
    {
        // Normalize class name (remove leading dot if present)
        $name = ltrim($className, '.');

        // Convert CSS-in-JS to declarations
        $declarations = $this->cssToDeclarations($css);

        // Register with utilities
        $this->utilities->addPluginUtility($name, $declarations, $options);
    }

    /**
     * Convert CSS-in-JS object to flat declarations array.
     *
     * @param array $css CSS-in-JS object
     * @return array Flat declarations
     */
    private function cssToDeclarations(array $css): array
    {
        $declarations = [];

        foreach ($css as $property => $value) {
            if (is_array($value)) {
                // Nested selector or multiple values
                if ($this->isNestedSelector($property)) {
                    // Handle nested selectors like '&:hover'
                    $declarations[$property] = $this->cssToDeclarations($value);
                } else {
                    // Multiple values for same property
                    foreach ($value as $v) {
                        $declarations[] = [$this->toKebabCase($property), $v];
                    }
                }
            } else {
                // Simple declaration
                $declarations[$this->toKebabCase($property)] = $value;
            }
        }

        return $declarations;
    }

    /**
     * Check if a property is a nested selector.
     */
    private function isNestedSelector(string $property): bool
    {
        return str_starts_with($property, '&') ||
               str_starts_with($property, '@') ||
               str_starts_with($property, '.') ||
               str_contains($property, ' ') ||
               str_contains($property, ':') ||
               str_contains($property, '>');
    }

    /**
     * Convert camelCase to kebab-case.
     */
    private function toKebabCase(string $str): string
    {
        return strtolower(preg_replace('/([A-Z])/', '-$1', $str));
    }

    /**
     * Resolve a dot-notation path in the theme.
     */
    private function resolveThemePath(string $path, mixed $default): mixed
    {
        // Convert dot notation to theme key
        // e.g., 'colors.red.500' -> '--color-red-500'
        $parts = explode('.', $path);
        $namespace = array_shift($parts);

        // Map common theme namespaces
        $namespaceMap = [
            'colors' => '--color',
            'spacing' => '--spacing',
            'fontSize' => '--font-size',
            'fontFamily' => '--font-family',
            'fontWeight' => '--font-weight',
            'lineHeight' => '--line-height',
            'letterSpacing' => '--letter-spacing',
            'borderRadius' => '--radius',
            'borderWidth' => '--border-width',
            'boxShadow' => '--shadow',
            'opacity' => '--opacity',
            'zIndex' => '--z-index',
            'width' => '--width',
            'height' => '--height',
            'maxWidth' => '--max-width',
            'screens' => '--breakpoint',
        ];

        $themeNamespace = $namespaceMap[$namespace] ?? "--{$this->toKebabCase($namespace)}";

        if (empty($parts)) {
            // Return all values in namespace
            return $this->theme->namespace($themeNamespace);
        }

        // Build theme key
        $themeKey = $themeNamespace . '-' . implode('-', $parts);

        $value = $this->theme->get([$themeKey]);

        return $value ?? $default;
    }

    /**
     * Resolve a dot-notation path in an array.
     */
    private function resolvePath(array $data, string $path, mixed $default): mixed
    {
        $parts = explode('.', $path);
        $current = $data;

        foreach ($parts as $part) {
            if (!is_array($current) || !array_key_exists($part, $current)) {
                return $default;
            }
            $current = $current[$part];
        }

        return $current;
    }

    /**
     * Negate a numeric value.
     */
    private function negate(string $value): string
    {
        if (str_starts_with($value, '-')) {
            return substr($value, 1);
        }

        return "-{$value}";
    }

    /**
     * Apply opacity modifier to a color value.
     */
    private function applyOpacityModifier(string $value, string $opacity): string
    {
        // Convert percentage to decimal if needed
        if (str_ends_with($opacity, '%')) {
            $opacity = rtrim($opacity, '%');
        }

        return "color-mix(in oklab, {$value} {$opacity}%, transparent)";
    }
}
