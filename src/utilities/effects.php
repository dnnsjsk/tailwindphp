<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\Ast\decl;
use function TailwindPHP\Ast\atRoot;
use function TailwindPHP\Utilities\property;
use function TailwindPHP\Utils\replaceShadowColors;
use function TailwindPHP\Utils\isPositiveInteger;
use function TailwindPHP\Utils\inferDataType;

/**
 * Replace shadow colors with a CSS variable reference.
 *
 * @param string $shadow The shadow value
 * @param string $varName The CSS variable name (without var())
 * @return string The transformed shadow value
 */
function replaceShadowColor(string $shadow, string $varName): string
{
    return replaceShadowColors($shadow, function (string $color) use ($varName) {
        // Convert color to hex if it's a named color
        $hexColor = colorToHex($color);
        return "var({$varName}, {$hexColor})";
    });
}

/**
 * Convert rgb() colors to hex for shadow fallbacks.
 *
 * @param string $color Color value (rgb(), hex, or named)
 * @return string Hex color or original value
 */
function colorToHex(string $color): string
{
    // Handle rgb(0 0 0 / .05) and similar formats
    if (str_starts_with($color, 'rgb(')) {
        // Pattern: rgb(r g b / alpha) or rgb(r g b / .alpha)
        if (preg_match('/rgb\(\s*(\d+)\s+(\d+)\s+(\d+)\s*\/\s*([\d.]+%?)\s*\)/', $color, $m)) {
            $r = (int)$m[1];
            $g = (int)$m[2];
            $b = (int)$m[3];
            $alphaStr = $m[4];

            // Parse alpha value
            if (str_ends_with($alphaStr, '%')) {
                $alpha = (float)rtrim($alphaStr, '%') / 100;
            } elseif (str_starts_with($alphaStr, '.')) {
                $alpha = (float)("0" . $alphaStr);
            } elseif (str_starts_with($alphaStr, '0.')) {
                $alpha = (float)$alphaStr;
            } else {
                $alpha = (float)$alphaStr;
                if ($alpha > 1) {
                    $alpha = $alpha / 100; // Assume it's a percentage without %
                }
            }

            // Convert to hex
            $rHex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
            $gHex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
            $bHex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
            $alphaHex = str_pad(dechex((int)round($alpha * 255)), 2, '0', STR_PAD_LEFT);

            return "#{$rHex}{$gHex}{$bHex}{$alphaHex}";
        }
    }
    return $color;
}

/**
 * Effects Utilities
 *
 * Port of effects utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - opacity
 * - box-shadow (shadow-*)
 * - mix-blend-mode
 * - background-blend-mode
 */

/**
 * Register effects utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerEffectsUtilities(UtilityBuilder $builder): void
{
    // =========================================================================
    // Opacity
    // =========================================================================

    $builder->functionalUtility('opacity', [
        'themeKeys' => ['--opacity'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            // Handle both integers and decimals: opacity-15 (=0.15), opacity-2.5 (=0.025)
            // Valid decimal increments: .5, .25, .75 only
            if (preg_match('/^(\d+)(\.(?:5|25|75))?$/', $value['value'], $m)) {
                $numericVal = (float)$value['value'];

                // Reject invalid values (> 100%)
                if ($numericVal > 100) {
                    return null;
                }

                // Divide by 100 to get decimal (15 -> 0.15, 2.5 -> 0.025)
                $val = $numericVal / 100;

                // Format the value properly (e.g., 0.15 -> .15, 0.025 -> .025)
                $formatted = rtrim(number_format($val, 4, '.', ''), '0');
                $formatted = rtrim($formatted, '.');
                // Remove leading zero
                if (str_starts_with($formatted, '0.')) {
                    $formatted = substr($formatted, 1);
                }
                return $formatted ?: '0';
            }
            return null;
        },
        'handle' => function ($value) {
            return [decl('opacity', $value)];
        },
    ]);

    // =========================================================================
    // Box Shadow
    // =========================================================================

    // Shadow/Ring stacking system - combined box-shadow value
    $cssBoxShadowValue = 'var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow)';
    $nullShadow = '0 0 #0000';

    // Box shadow property rules for stacking
    $boxShadowProperties = function () use ($nullShadow) {
        return atRoot([
            property('--tw-shadow', $nullShadow),
            property('--tw-shadow-color'),
            property('--tw-shadow-alpha', '100%', '<percentage>'),
            property('--tw-inset-shadow', $nullShadow),
            property('--tw-inset-shadow-color'),
            property('--tw-inset-shadow-alpha', '100%', '<percentage>'),
            property('--tw-ring-color'),
            property('--tw-ring-shadow', $nullShadow),
            property('--tw-inset-ring-color'),
            property('--tw-inset-ring-shadow', $nullShadow),
            // Legacy
            property('--tw-ring-inset'),
            property('--tw-ring-offset-width', '0px', '<length>'),
            property('--tw-ring-offset-color', '#fff'),
            property('--tw-ring-offset-shadow', $nullShadow),
        ]);
    };

    $theme = $builder->getTheme();

    // Shadow color variable (used by shadow utilities)
    $builder->functionalUtility('shadow', [
        'themeKeys' => ['--shadow'],
        'defaultValue' => 'var(--shadow, 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1))',
        'handle' => function ($value) use ($boxShadowProperties, $cssBoxShadowValue) {
            return [
                $boxShadowProperties(),
                decl('--tw-shadow', $value),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        },
        'staticValues' => [
            'none' => [decl('box-shadow', 'none')],
            'sm' => [decl('box-shadow', 'var(--shadow-sm, 0 1px 2px 0 rgb(0 0 0 / 0.05))')],
            'md' => [decl('box-shadow', 'var(--shadow-md, 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1))')],
            'lg' => [decl('box-shadow', 'var(--shadow-lg, 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1))')],
            'xl' => [decl('box-shadow', 'var(--shadow-xl, 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1))')],
            '2xl' => [decl('box-shadow', 'var(--shadow-2xl, 0 25px 50px -12px rgb(0 0 0 / 0.25))')],
            'inner' => [decl('box-shadow', 'var(--shadow-inner, inset 0 2px 4px 0 rgb(0 0 0 / 0.05))')],
        ],
    ]);

    // Inset shadow - functional utility with stacking
    $builder->getUtilities()->functional('inset-shadow', function ($candidate) use ($theme, $boxShadowProperties, $cssBoxShadowValue) {
        if (!isset($candidate['value'])) {
            // Default value
            $value = $theme->get(['--inset-shadow']);
            if ($value === null) {
                return null;
            }
            // Replace color in shadow with var(--tw-inset-shadow-color, original-color)
            $transformedValue = replaceShadowColor($value, '--tw-inset-shadow-color');
            return [
                $boxShadowProperties(),
                decl('--tw-inset-shadow', $transformedValue),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        $candidateValue = $candidate['value'];

        // Handle arbitrary values
        if ($candidateValue['kind'] === 'arbitrary') {
            $value = $candidateValue['value'];
            return [
                $boxShadowProperties(),
                decl('--tw-inset-shadow', $value),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        // Named values (sm, xs, etc.)
        $value = $theme->resolveValue($candidateValue['value'] ?? null, ['--inset-shadow']);
        if ($value !== null) {
            // Replace color in shadow with var(--tw-inset-shadow-color, original-color)
            $transformedValue = replaceShadowColor($value, '--tw-inset-shadow-color');
            return [
                $boxShadowProperties(),
                decl('--tw-inset-shadow', $transformedValue),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        // Static values
        if (($candidateValue['value'] ?? null) === 'none') {
            return [decl('box-shadow', 'none')];
        }

        return null;
    });

    // =========================================================================
    // Ring utilities
    // =========================================================================

    // ring-inset static utility
    $builder->staticUtility('ring-inset', [
        fn() => $boxShadowProperties(),
        ['--tw-ring-inset', 'inset'],
    ]);

    // Ring shadow value generator
    $defaultRingColor = $theme->get(['--default-ring-color']) ?? 'currentcolor';
    $ringShadowValue = function (string $value) use ($defaultRingColor) {
        return "var(--tw-ring-inset,) 0 0 0 calc({$value} + var(--tw-ring-offset-width)) var(--tw-ring-color, {$defaultRingColor})";
    };

    // ring utility - width and color
    $builder->getUtilities()->functional('ring', function ($candidate) use ($theme, $boxShadowProperties, $cssBoxShadowValue, $ringShadowValue) {
        $candidateValue = $candidate['value'] ?? null;
        $modifier = $candidate['modifier'] ?? null;

        // No value = default ring width
        if ($candidateValue === null) {
            if ($modifier !== null) {
                return null;
            }
            $value = $theme->get(['--default-ring-width']) ?? '1px';
            return [
                $boxShadowProperties(),
                decl('--tw-ring-shadow', $ringShadowValue($value)),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        // Handle arbitrary values
        if ($candidateValue['kind'] === 'arbitrary') {
            $value = $candidateValue['value'];
            $type = $candidateValue['dataType'] ?? inferDataType($value, ['color', 'length']);

            if ($type === 'length') {
                if ($modifier !== null) {
                    return null;
                }
                return [
                    $boxShadowProperties(),
                    decl('--tw-ring-shadow', $ringShadowValue($value)),
                    decl('box-shadow', $cssBoxShadowValue),
                ];
            }

            // Color arbitrary value
            $value = asColor($value, $modifier, $theme);
            if ($value === null) {
                return null;
            }
            return [decl('--tw-ring-color', $value)];
        }

        $namedValue = $candidateValue['value'] ?? null;

        // Ring color
        $colorValue = resolveThemeColor($candidate, $theme, ['--ring-color', '--color']);
        if ($colorValue !== null) {
            return [decl('--tw-ring-color', $colorValue)];
        }

        // Ring width
        if ($modifier !== null) {
            return null;
        }
        $widthValue = $theme->resolve($namedValue, ['--ring-width']);
        if ($widthValue === null && isPositiveInteger($namedValue)) {
            $widthValue = "{$namedValue}px";
        }
        if ($widthValue !== null) {
            return [
                $boxShadowProperties(),
                decl('--tw-ring-shadow', $ringShadowValue($widthValue)),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        return null;
    });

    // inset-ring utility
    $insetRingShadowValue = function (string $value) {
        return "inset 0 0 0 {$value} var(--tw-inset-ring-color, currentcolor)";
    };

    $builder->getUtilities()->functional('inset-ring', function ($candidate) use ($theme, $boxShadowProperties, $cssBoxShadowValue, $insetRingShadowValue) {
        $candidateValue = $candidate['value'] ?? null;
        $modifier = $candidate['modifier'] ?? null;

        // No value = default 1px
        if ($candidateValue === null) {
            if ($modifier !== null) {
                return null;
            }
            return [
                $boxShadowProperties(),
                decl('--tw-inset-ring-shadow', $insetRingShadowValue('1px')),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        // Handle arbitrary values
        if ($candidateValue['kind'] === 'arbitrary') {
            $value = $candidateValue['value'];
            $type = $candidateValue['dataType'] ?? inferDataType($value, ['color', 'length']);

            if ($type === 'length') {
                if ($modifier !== null) {
                    return null;
                }
                return [
                    $boxShadowProperties(),
                    decl('--tw-inset-ring-shadow', $insetRingShadowValue($value)),
                    decl('box-shadow', $cssBoxShadowValue),
                ];
            }

            // Color arbitrary value
            $value = asColor($value, $modifier, $theme);
            if ($value === null) {
                return null;
            }
            return [decl('--tw-inset-ring-color', $value)];
        }

        $namedValue = $candidateValue['value'] ?? null;

        // Ring color
        $colorValue = resolveThemeColor($candidate, $theme, ['--ring-color', '--color']);
        if ($colorValue !== null) {
            return [decl('--tw-inset-ring-color', $colorValue)];
        }

        // Ring width
        if ($modifier !== null) {
            return null;
        }
        $widthValue = $theme->resolve($namedValue, ['--ring-width']);
        if ($widthValue === null && isPositiveInteger($namedValue)) {
            $widthValue = "{$namedValue}px";
        }
        if ($widthValue !== null) {
            return [
                $boxShadowProperties(),
                decl('--tw-inset-ring-shadow', $insetRingShadowValue($widthValue)),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        return null;
    });

    // ring-offset utility
    $ringOffsetShadowValue = 'var(--tw-ring-inset,) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color)';

    $builder->getUtilities()->functional('ring-offset', function ($candidate) use ($theme, $boxShadowProperties, $cssBoxShadowValue, $ringOffsetShadowValue) {
        $candidateValue = $candidate['value'] ?? null;
        $modifier = $candidate['modifier'] ?? null;

        // No value = no match
        if ($candidateValue === null) {
            return null;
        }

        // Handle arbitrary values
        if ($candidateValue['kind'] === 'arbitrary') {
            $value = $candidateValue['value'];
            $type = $candidateValue['dataType'] ?? inferDataType($value, ['color', 'length']);

            if ($type === 'length') {
                if ($modifier !== null) {
                    return null;
                }
                return [
                    $boxShadowProperties(),
                    decl('--tw-ring-offset-width', $value),
                    decl('--tw-ring-offset-shadow', $ringOffsetShadowValue),
                    decl('box-shadow', $cssBoxShadowValue),
                ];
            }

            // Color arbitrary value
            $value = asColor($value, $modifier, $theme);
            if ($value === null) {
                return null;
            }
            return [decl('--tw-ring-offset-color', $value)];
        }

        $namedValue = $candidateValue['value'] ?? null;

        // ring-offset-inset
        if ($namedValue === 'inset') {
            return [decl('--tw-ring-inset', 'inset')];
        }

        // Offset color
        $colorValue = resolveThemeColor($candidate, $theme, ['--ring-offset-color', '--color']);
        if ($colorValue !== null) {
            return [decl('--tw-ring-offset-color', $colorValue)];
        }

        // Offset width
        if ($modifier !== null) {
            return null;
        }
        $widthValue = $theme->resolve($namedValue, ['--ring-offset-width']);
        if ($widthValue === null && isPositiveInteger($namedValue)) {
            $widthValue = "{$namedValue}px";
        }
        if ($widthValue !== null) {
            return [
                $boxShadowProperties(),
                decl('--tw-ring-offset-width', $widthValue),
                decl('--tw-ring-offset-shadow', $ringOffsetShadowValue),
                decl('box-shadow', $cssBoxShadowValue),
            ];
        }

        return null;
    });

    // Drop shadow (filter-based)
    $builder->functionalUtility('drop-shadow', [
        'themeKeys' => ['--drop-shadow'],
        'defaultValue' => 'var(--drop-shadow, drop-shadow(0 1px 2px rgb(0 0 0 / 0.1)) drop-shadow(0 1px 1px rgb(0 0 0 / 0.06)))',
        'handle' => function ($value) {
            return [decl('filter', $value)];
        },
        'staticValues' => [
            'none' => [decl('filter', 'drop-shadow(0 0 #0000)')],
            'sm' => [decl('filter', 'var(--drop-shadow-sm, drop-shadow(0 1px 1px rgb(0 0 0 / 0.05)))')],
            'md' => [decl('filter', 'var(--drop-shadow-md, drop-shadow(0 4px 3px rgb(0 0 0 / 0.07)) drop-shadow(0 2px 2px rgb(0 0 0 / 0.06)))')],
            'lg' => [decl('filter', 'var(--drop-shadow-lg, drop-shadow(0 10px 8px rgb(0 0 0 / 0.04)) drop-shadow(0 4px 3px rgb(0 0 0 / 0.1)))')],
            'xl' => [decl('filter', 'var(--drop-shadow-xl, drop-shadow(0 20px 13px rgb(0 0 0 / 0.03)) drop-shadow(0 8px 5px rgb(0 0 0 / 0.08)))')],
            '2xl' => [decl('filter', 'var(--drop-shadow-2xl, drop-shadow(0 25px 25px rgb(0 0 0 / 0.15)))')],
        ],
    ]);

    // =========================================================================
    // Mix Blend Mode
    // =========================================================================

    $blendModes = [
        'normal', 'multiply', 'screen', 'overlay', 'darken', 'lighten',
        'color-dodge', 'color-burn', 'hard-light', 'soft-light', 'difference',
        'exclusion', 'hue', 'saturation', 'color', 'luminosity', 'plus-darker', 'plus-lighter',
    ];

    foreach ($blendModes as $mode) {
        $builder->staticUtility("mix-blend-$mode", [['mix-blend-mode', $mode]]);
    }

    // =========================================================================
    // Background Blend Mode
    // =========================================================================

    foreach ($blendModes as $mode) {
        $builder->staticUtility("bg-blend-$mode", [['background-blend-mode', $mode]]);
    }

    // =========================================================================
    // Mask Clip
    // =========================================================================

    $maskClipValues = [
        'border' => 'border-box',
        'padding' => 'padding-box',
        'content' => 'content-box',
        'fill' => 'fill-box',
        'stroke' => 'stroke-box',
        'view' => 'view-box',
    ];

    foreach ($maskClipValues as $name => $value) {
        $builder->staticUtility("mask-clip-$name", [
            ['-webkit-mask-clip', $value],
            ['mask-clip', $value],
        ]);
    }

    $builder->staticUtility('mask-no-clip', [
        ['-webkit-mask-clip', 'no-clip'],
        ['mask-clip', 'no-clip'],
    ]);

    // =========================================================================
    // Mask Origin
    // =========================================================================

    $maskOriginValues = [
        'border' => 'border-box',
        'padding' => 'padding-box',
        'content' => 'content-box',
        'fill' => 'fill-box',
        'stroke' => 'stroke-box',
        'view' => 'view-box',
    ];

    foreach ($maskOriginValues as $name => $value) {
        $builder->staticUtility("mask-origin-$name", [
            ['-webkit-mask-origin', $value],
            ['mask-origin', $value],
        ]);
    }
}
