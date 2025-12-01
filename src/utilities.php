<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use TailwindPHP\Theme;
use TailwindPHP\Utils\DefaultMap;
use function TailwindPHP\decl;
use function TailwindPHP\atRule;
use function TailwindPHP\cloneAstNode;
use function TailwindPHP\Utils\segment;
use function TailwindPHP\Utils\isValidOpacityValue;
use function TailwindPHP\Utils\isPositiveInteger;
use function TailwindPHP\Utils\isValidSpacingMultiplier;

/**
 * Utilities - Utility registry and core utility functions.
 *
 * Port of: packages/tailwindcss/src/utilities.ts
 */

const IS_VALID_STATIC_UTILITY_NAME = '/^-?[a-z][a-zA-Z0-9\/%._-]*$/';
const IS_VALID_FUNCTIONAL_UTILITY_NAME = '/^-?[a-z][a-zA-Z0-9\/%._-]*-\*$/';

const DEFAULT_SPACING_SUGGESTIONS = [
    '0', '0.5', '1', '1.5', '2', '2.5', '3', '3.5', '4', '5', '6', '7', '8', '9',
    '10', '11', '12', '14', '16', '20', '24', '28', '32', '36', '40', '44', '48',
    '52', '56', '60', '64', '72', '80', '96',
];

/**
 * Utility class to manage utility registrations.
 */
class Utilities
{
    /**
     * @var DefaultMap<string, array<array{kind: string, compileFn: callable, options?: array}>>
     */
    private DefaultMap $utilities;

    /**
     * @var array<string, callable>
     */
    private array $completions = [];

    public function __construct()
    {
        $this->utilities = new DefaultMap(fn() => []);
    }

    /**
     * Register a static utility.
     *
     * @param string $name
     * @param callable $compileFn
     * @return void
     */
    public function static(string $name, callable $compileFn): void
    {
        $utilities = $this->utilities->get($name);
        $utilities[] = ['kind' => 'static', 'compileFn' => $compileFn];
        $this->utilities->set($name, $utilities);
    }

    /**
     * Register a functional utility.
     *
     * @param string $name
     * @param callable $compileFn
     * @param array|null $options
     * @return void
     */
    public function functional(string $name, callable $compileFn, ?array $options = null): void
    {
        $utilities = $this->utilities->get($name);
        $utility = ['kind' => 'functional', 'compileFn' => $compileFn];
        if ($options !== null) {
            $utility['options'] = $options;
        }
        $utilities[] = $utility;
        $this->utilities->set($name, $utilities);
    }

    /**
     * Check if a utility exists with a given kind.
     *
     * @param string $name
     * @param string $kind 'static' or 'functional'
     * @return bool
     */
    public function has(string $name, string $kind): bool
    {
        if (!$this->utilities->has($name)) {
            return false;
        }

        $utils = $this->utilities->get($name);
        foreach ($utils as $util) {
            if ($util['kind'] === $kind) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all utilities for a name.
     *
     * @param string $name
     * @return array
     */
    public function get(string $name): array
    {
        if (!$this->utilities->has($name)) {
            return [];
        }
        return $this->utilities->get($name);
    }

    /**
     * Get completions for a utility.
     *
     * @param string $name
     * @return array
     */
    public function getCompletions(string $name): array
    {
        if ($this->has($name, 'static')) {
            if (isset($this->completions[$name])) {
                return ($this->completions[$name])();
            }
            return [['supportsNegative' => false, 'values' => [], 'modifiers' => []]];
        }

        if (isset($this->completions[$name])) {
            return ($this->completions[$name])();
        }

        return [];
    }

    /**
     * Register suggestion groups for a utility.
     *
     * @param string $name
     * @param callable $groups
     * @return void
     */
    public function suggest(string $name, callable $groups): void
    {
        if (isset($this->completions[$name])) {
            $existingGroups = $this->completions[$name];
            $this->completions[$name] = fn() => array_merge($existingGroups(), $groups());
        } else {
            $this->completions[$name] = $groups;
        }
    }

    /**
     * Get all utility keys of a specific kind.
     *
     * @param string $kind 'static' or 'functional'
     * @return array<string>
     */
    public function keys(string $kind): array
    {
        $keys = [];

        foreach ($this->utilities->entries() as [$key, $fns]) {
            foreach ($fns as $fn) {
                if ($fn['kind'] === $kind) {
                    $keys[] = $key;
                    break;
                }
            }
        }

        return $keys;
    }
}

/**
 * Create a @property at-rule for CSS custom properties.
 *
 * @param string $ident
 * @param string|null $initialValue
 * @param string|null $syntax
 * @return array
 */
function property(string $ident, ?string $initialValue = null, ?string $syntax = null): array
{
    $nodes = [
        decl('syntax', $syntax ? "\"{$syntax}\"" : '"*"'),
        decl('inherits', 'false'),
    ];

    if ($initialValue !== null) {
        $nodes[] = decl('initial-value', $initialValue);
    }

    return atRule('@property', $ident, $nodes);
}

/**
 * Apply opacity to a color using `color-mix`.
 *
 * @param string $value
 * @param string|null $alpha
 * @return string
 */
function withAlpha(string $value, ?string $alpha): string
{
    if ($alpha === null || $alpha === '') return $value;

    // Convert numeric values to percentages
    if (is_numeric($alpha)) {
        $alpha = (floatval($alpha) * 100) . '%';
    }

    // No need for color-mix if the alpha is 100%
    if ($alpha === '100%') {
        return $value;
    }

    return "color-mix(in oklab, {$value} {$alpha}, transparent)";
}

/**
 * Replace the alpha channel of a color.
 *
 * @param string $value
 * @param string $alpha
 * @return string
 */
function replaceAlpha(string $value, string $alpha): string
{
    // Convert numeric values to percentages
    if (is_numeric($alpha)) {
        $alpha = (floatval($alpha) * 100) . '%';
    }

    return "oklab(from {$value} l a b / {$alpha})";
}

/**
 * Resolve a color value + optional opacity modifier to a final color.
 *
 * @param string $value
 * @param array|null $modifier
 * @param Theme $theme
 * @return string|null
 */
function asColor(string $value, ?array $modifier, Theme $theme): ?string
{
    if ($modifier === null) return $value;

    if ($modifier['kind'] === 'arbitrary') {
        return withAlpha($value, $modifier['value']);
    }

    // Check if the modifier exists in the `opacity` theme configuration
    $alpha = $theme->resolve($modifier['value'], ['--opacity']);
    if ($alpha) {
        return withAlpha($value, $alpha);
    }

    if (!isValidOpacityValue($modifier['value'])) {
        return null;
    }

    // The modifier is a bare value like `50`, so convert that to `50%`.
    return withAlpha($value, $modifier['value'] . '%');
}

/**
 * Resolve a theme color for a candidate.
 *
 * @param array $candidate Functional candidate
 * @param Theme $theme
 * @param array $themeKeys
 * @return string|null
 */
function resolveThemeColor(array $candidate, Theme $theme, array $themeKeys): ?string
{
    if (!isset($candidate['value']) || $candidate['value']['kind'] !== 'named') {
        return null;
    }

    $value = null;

    switch ($candidate['value']['value']) {
        case 'inherit':
            $value = 'inherit';
            break;
        case 'transparent':
            $value = 'transparent';
            break;
        case 'current':
            $value = 'currentcolor';
            break;
        default:
            $value = $theme->resolve($candidate['value']['value'], $themeKeys);
            break;
    }

    return $value ? asColor($value, $candidate['modifier'] ?? null, $theme) : null;
}

/**
 * Helper class for registering utilities with a theme.
 */
class UtilityBuilder
{
    private Utilities $utilities;
    private Theme $theme;

    public function __construct(Utilities $utilities, Theme $theme)
    {
        $this->utilities = $utilities;
        $this->theme = $theme;
    }

    /**
     * Get the utilities instance.
     */
    public function getUtilities(): Utilities
    {
        return $this->utilities;
    }

    /**
     * Get the theme instance.
     */
    public function getTheme(): Theme
    {
        return $this->theme;
    }

    /**
     * Register a static utility class like `justify-center`.
     *
     * @param string $className
     * @param array $declarations Array of [property, value] tuples or callables
     */
    public function staticUtility(string $className, array $declarations): void
    {
        $this->utilities->static($className, function () use ($declarations) {
            return array_map(function ($node) {
                return is_callable($node) ? $node() : \TailwindPHP\decl($node[0], $node[1]);
            }, $declarations);
        });
    }

    /**
     * Register a functional utility class like `max-w-*`.
     *
     * @param string $classRoot
     * @param array $desc Utility description
     */
    public function functionalUtility(string $classRoot, array $desc): void
    {
        $theme = $this->theme;
        $utilities = $this->utilities;

        $handleFunctionalUtility = function (bool $negative) use ($theme, $desc) {
            return function (array $candidate) use ($theme, $desc, $negative) {
                $value = null;
                $dataType = null;

                if (!isset($candidate['value']) || $candidate['value'] === null) {
                    if (isset($candidate['modifier']) && $candidate['modifier'] !== null) {
                        return null;
                    }

                    // Use defaultValue or resolve from theme
                    if (array_key_exists('defaultValue', $desc)) {
                        $value = $desc['defaultValue'];
                    } else {
                        $value = $theme->resolve(null, $desc['themeKeys'] ?? []);
                    }
                } elseif ($candidate['value']['kind'] === 'arbitrary') {
                    if (isset($candidate['modifier']) && $candidate['modifier'] !== null) {
                        return null;
                    }
                    $value = $candidate['value']['value'];
                    $dataType = $candidate['value']['dataType'] ?? null;
                } else {
                    $lookupValue = $candidate['value']['fraction'] ?? $candidate['value']['value'];

                    // For spacing utilities (handleBareValueFirst), try bare value handler before theme resolution
                    // This ensures p-4 uses calc(var(--spacing) * 4) instead of var(--spacing-4)
                    if (($desc['handleBareValueFirst'] ?? false) && isset($desc['handleBareValue'])) {
                        $value = $desc['handleBareValue']($candidate['value']);
                        if ($value !== null && strpos($value, '/') === false && isset($candidate['modifier'])) {
                            return null;
                        }
                    }

                    // Theme resolution
                    if ($value === null) {
                        $value = $theme->resolve($lookupValue, $desc['themeKeys'] ?? []);
                    }

                    // Handle fractions like w-1/2
                    if ($value === null && ($desc['supportsFractions'] ?? false) && isset($candidate['value']['fraction'])) {
                        $parts = segment($candidate['value']['fraction'], '/');
                        if (count($parts) === 2 && isPositiveInteger($parts[0]) && isPositiveInteger($parts[1])) {
                            $value = "calc({$candidate['value']['fraction']} * 100%)";
                        }
                    }

                    // Handle bare values with negative handler
                    if ($value === null && $negative && isset($desc['handleNegativeBareValue'])) {
                        $value = $desc['handleNegativeBareValue']($candidate['value']);
                        if ($value !== null && strpos($value, '/') === false && isset($candidate['modifier'])) {
                            return null;
                        }
                        if ($value !== null) {
                            return $desc['handle']($value, null);
                        }
                    }

                    // Handle bare values (fallback for non-spacing utilities)
                    if ($value === null && isset($desc['handleBareValue']) && !($desc['handleBareValueFirst'] ?? false)) {
                        $value = $desc['handleBareValue']($candidate['value']);
                        if ($value !== null && strpos($value, '/') === false && isset($candidate['modifier'])) {
                            return null;
                        }
                    }

                    // Handle static values as fallback
                    if ($value === null && !$negative && isset($desc['staticValues']) && !isset($candidate['modifier'])) {
                        $fallback = $desc['staticValues'][$candidate['value']['value']] ?? null;
                        if ($fallback !== null) {
                            return array_map('TailwindPHP\\cloneAstNode', $fallback);
                        }
                    }
                }

                if ($value === null) {
                    return null;
                }

                // Negate the value if needed
                if ($negative) {
                    $value = "calc({$value} * -1)";
                }

                return $desc['handle']($value, $dataType);
            };
        };

        if ($desc['supportsNegative'] ?? false) {
            $utilities->functional("-{$classRoot}", $handleFunctionalUtility(true));
        }
        $utilities->functional($classRoot, $handleFunctionalUtility(false));

        // Add suggestions
        $this->suggest($classRoot, fn() => [
            [
                'supportsNegative' => $desc['supportsNegative'] ?? false,
                'valueThemeKeys' => $desc['themeKeys'] ?? [],
                'hasDefaultValue' => array_key_exists('defaultValue', $desc) && $desc['defaultValue'] !== null,
                'supportsFractions' => $desc['supportsFractions'] ?? false,
            ],
        ]);

        // Add static value suggestions
        if (isset($desc['staticValues']) && count($desc['staticValues']) > 0) {
            $values = array_keys($desc['staticValues']);
            $this->suggest($classRoot, fn() => [['values' => $values]]);
        }
    }

    /**
     * Register a color utility class.
     *
     * @param string $classRoot
     * @param array $desc Color utility description
     */
    public function colorUtility(string $classRoot, array $desc): void
    {
        $theme = $this->theme;

        $this->utilities->functional($classRoot, function (array $candidate) use ($theme, $desc) {
            if (!isset($candidate['value'])) {
                return null;
            }

            $value = null;

            if ($candidate['value']['kind'] === 'arbitrary') {
                $value = $candidate['value']['value'];
                $value = asColor($value, $candidate['modifier'] ?? null, $theme);
            } else {
                $value = resolveThemeColor($candidate, $theme, $desc['themeKeys']);
            }

            if ($value === null) {
                return null;
            }

            return $desc['handle']($value);
        });

        $this->suggest($classRoot, fn() => [
            [
                'values' => ['current', 'inherit', 'transparent'],
                'valueThemeKeys' => $desc['themeKeys'],
                'modifiers' => array_map(fn($i) => (string)($i * 5), range(0, 20)),
            ],
        ]);
    }

    /**
     * Register a spacing utility.
     *
     * @param string $name
     * @param array $themeKeys
     * @param callable $handle
     * @param array $options
     */
    public function spacingUtility(string $name, array $themeKeys, callable $handle, array $options = []): void
    {
        $supportsNegative = $options['supportsNegative'] ?? false;
        $supportsFractions = $options['supportsFractions'] ?? false;
        $staticValues = $options['staticValues'] ?? null;
        $theme = $this->theme;

        if ($supportsNegative) {
            $this->utilities->static("-{$name}-px", fn() => $handle('-1px'));
        }
        $this->utilities->static("{$name}-px", fn() => $handle('1px'));

        $this->functionalUtility($name, [
            'themeKeys' => $themeKeys,
            'supportsFractions' => $supportsFractions,
            'supportsNegative' => $supportsNegative,
            'defaultValue' => null,
            'handleBareValueFirst' => true, // TailwindCSS 4.0: prefer calc(var(--spacing) * N) over var(--spacing-N)
            'handleBareValue' => function ($value) use ($theme) {
                $multiplier = $theme->resolve(null, ['--spacing']);
                if ($multiplier === null) return null;
                if (!isValidSpacingMultiplier($value['value'])) return null;
                return "calc(var(--spacing) * {$value['value']})";
            },
            'staticValues' => $staticValues,
            'handle' => $handle,
        ]);
    }

    /**
     * Register suggestions for a utility.
     *
     * @param string $name
     * @param callable $groups
     */
    public function suggest(string $name, callable $groups): void
    {
        $this->utilities->suggest($name, $groups);
    }
}

/**
 * Create utilities with the given theme.
 *
 * @param Theme $theme
 * @return Utilities
 */
function createUtilities(Theme $theme): Utilities
{
    $utilities = new Utilities();
    $builder = new UtilityBuilder($utilities, $theme);

    // Register all utilities by loading individual utility files
    registerAccessibilityUtilities($builder);
    registerLayoutUtilities($builder);
    registerFlexboxGridUtilities($builder);
    registerSpacingUtilities($builder);
    registerSizingUtilities($builder);
    registerTypographyUtilities($builder);
    registerBackgroundUtilities($builder);
    registerBorderUtilities($builder);
    registerEffectsUtilities($builder);
    registerFiltersUtilities($builder);
    registerTablesUtilities($builder);
    registerTransitionsUtilities($builder);
    registerTransformsUtilities($builder);
    registerInteractivityUtilities($builder);
    registerSvgUtilities($builder);

    return $utilities;
}

// Include individual utility registration files
require_once __DIR__ . '/utilities/accessibility.php';
require_once __DIR__ . '/utilities/layout.php';
require_once __DIR__ . '/utilities/spacing.php';
require_once __DIR__ . '/utilities/sizing.php';

// Stub functions - will be implemented in individual files
function registerFlexboxGridUtilities(UtilityBuilder $builder): void {}
function registerTypographyUtilities(UtilityBuilder $builder): void {}
function registerBackgroundUtilities(UtilityBuilder $builder): void {}
function registerBorderUtilities(UtilityBuilder $builder): void {}
function registerEffectsUtilities(UtilityBuilder $builder): void {}
function registerFiltersUtilities(UtilityBuilder $builder): void {}
function registerTablesUtilities(UtilityBuilder $builder): void {}
function registerTransitionsUtilities(UtilityBuilder $builder): void {}
function registerTransformsUtilities(UtilityBuilder $builder): void {}
function registerInteractivityUtilities(UtilityBuilder $builder): void {}
function registerSvgUtilities(UtilityBuilder $builder): void {}
