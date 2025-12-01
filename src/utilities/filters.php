<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use TailwindPHP\Theme;
use function TailwindPHP\decl;
use function TailwindPHP\Utils\isPositiveInteger;
use function TailwindPHP\Utils\isValidOpacityValue;

/**
 * Filters Utilities
 *
 * Port of filter utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - filter, backdrop-filter
 * - blur, backdrop-blur
 * - brightness, backdrop-brightness
 * - contrast, backdrop-contrast
 * - grayscale, backdrop-grayscale
 * - hue-rotate, backdrop-hue-rotate
 * - invert, backdrop-invert
 * - saturate, backdrop-saturate
 * - sepia, backdrop-sepia
 * - drop-shadow
 * - backdrop-opacity
 */

/**
 * Register filters utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerFiltersUtilities(UtilityBuilder $builder): void
{
    $theme = $builder->getTheme();

    // CSS filter value
    $cssFilterValue = implode(' ', [
        'var(--tw-blur,)',
        'var(--tw-brightness,)',
        'var(--tw-contrast,)',
        'var(--tw-grayscale,)',
        'var(--tw-hue-rotate,)',
        'var(--tw-invert,)',
        'var(--tw-saturate,)',
        'var(--tw-sepia,)',
        'var(--tw-drop-shadow,)',
    ]);

    // CSS backdrop filter value
    $cssBackdropFilterValue = implode(' ', [
        'var(--tw-backdrop-blur,)',
        'var(--tw-backdrop-brightness,)',
        'var(--tw-backdrop-contrast,)',
        'var(--tw-backdrop-grayscale,)',
        'var(--tw-backdrop-hue-rotate,)',
        'var(--tw-backdrop-invert,)',
        'var(--tw-backdrop-opacity,)',
        'var(--tw-backdrop-saturate,)',
        'var(--tw-backdrop-sepia,)',
    ]);

    // =========================================================================
    // filter
    // =========================================================================

    // filter (default)
    $builder->staticUtility('filter', [
        ['filter', $cssFilterValue],
    ]);

    // filter-none
    $builder->staticUtility('filter-none', [
        ['filter', 'none'],
    ]);

    // filter-[arbitrary]
    $builder->functionalUtility('filter', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('filter', $value)];
        },
    ]);

    // =========================================================================
    // backdrop-filter
    // =========================================================================

    // backdrop-filter (default)
    $builder->staticUtility('backdrop-filter', [
        ['-webkit-backdrop-filter', $cssBackdropFilterValue],
        ['backdrop-filter', $cssBackdropFilterValue],
    ]);

    // backdrop-filter-none
    $builder->staticUtility('backdrop-filter-none', [
        ['-webkit-backdrop-filter', 'none'],
        ['backdrop-filter', 'none'],
    ]);

    // backdrop-filter-[arbitrary]
    $builder->functionalUtility('backdrop-filter', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [
                decl('-webkit-backdrop-filter', $value),
                decl('backdrop-filter', $value),
            ];
        },
    ]);

    // =========================================================================
    // blur
    // =========================================================================

    $builder->functionalUtility('blur', [
        'themeKeys' => ['--blur'],
        'defaultValue' => null,
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-blur', "blur({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
        'staticValues' => [
            'none' => [
                decl('--tw-blur', ' '),
                decl('filter', $cssFilterValue),
            ],
        ],
    ]);

    // =========================================================================
    // backdrop-blur
    // =========================================================================

    $builder->functionalUtility('backdrop-blur', [
        'themeKeys' => ['--backdrop-blur', '--blur'],
        'defaultValue' => null,
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-blur', "blur({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
        'staticValues' => [
            'none' => [
                decl('--tw-backdrop-blur', ' '),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ],
        ],
    ]);

    // =========================================================================
    // brightness
    // =========================================================================

    $builder->functionalUtility('brightness', [
        'themeKeys' => ['--brightness'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-brightness', "brightness({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-brightness
    // =========================================================================

    $builder->functionalUtility('backdrop-brightness', [
        'themeKeys' => ['--backdrop-brightness', '--brightness'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-brightness', "brightness({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // contrast
    // =========================================================================

    $builder->functionalUtility('contrast', [
        'themeKeys' => ['--contrast'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-contrast', "contrast({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-contrast
    // =========================================================================

    $builder->functionalUtility('backdrop-contrast', [
        'themeKeys' => ['--backdrop-contrast', '--contrast'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-contrast', "contrast({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // grayscale
    // =========================================================================

    $builder->functionalUtility('grayscale', [
        'themeKeys' => ['--grayscale'],
        'defaultValue' => '100%',
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-grayscale', "grayscale({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-grayscale
    // =========================================================================

    $builder->functionalUtility('backdrop-grayscale', [
        'themeKeys' => ['--backdrop-grayscale', '--grayscale'],
        'defaultValue' => '100%',
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-grayscale', "grayscale({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // hue-rotate
    // =========================================================================

    $builder->functionalUtility('hue-rotate', [
        'themeKeys' => ['--hue-rotate'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}deg";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-hue-rotate', "hue-rotate({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-hue-rotate
    // =========================================================================

    $builder->functionalUtility('backdrop-hue-rotate', [
        'themeKeys' => ['--backdrop-hue-rotate', '--hue-rotate'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}deg";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-hue-rotate', "hue-rotate({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // invert
    // =========================================================================

    $builder->functionalUtility('invert', [
        'themeKeys' => ['--invert'],
        'defaultValue' => '100%',
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-invert', "invert({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-invert
    // =========================================================================

    $builder->functionalUtility('backdrop-invert', [
        'themeKeys' => ['--backdrop-invert', '--invert'],
        'defaultValue' => '100%',
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-invert', "invert({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // saturate
    // =========================================================================

    $builder->functionalUtility('saturate', [
        'themeKeys' => ['--saturate'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-saturate', "saturate({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-saturate
    // =========================================================================

    $builder->functionalUtility('backdrop-saturate', [
        'themeKeys' => ['--backdrop-saturate', '--saturate'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-saturate', "saturate({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // sepia
    // =========================================================================

    $builder->functionalUtility('sepia', [
        'themeKeys' => ['--sepia'],
        'defaultValue' => '100%',
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssFilterValue) {
            return [
                decl('--tw-sepia', "sepia({$value})"),
                decl('filter', $cssFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // backdrop-sepia
    // =========================================================================

    $builder->functionalUtility('backdrop-sepia', [
        'themeKeys' => ['--backdrop-sepia', '--sepia'],
        'defaultValue' => '100%',
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-sepia', "sepia({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);

    // =========================================================================
    // drop-shadow
    // =========================================================================

    // drop-shadow-none
    $builder->staticUtility('drop-shadow-none', [
        ['--tw-drop-shadow', ' '],
        ['filter', $cssFilterValue],
    ]);

    // NOTE: drop-shadow with colors and modifiers is very complex
    // and requires special handling for color resolution, alpha replacement, etc.
    // For now, implementing basic drop-shadow with theme values

    // =========================================================================
    // backdrop-opacity
    // =========================================================================

    $builder->functionalUtility('backdrop-opacity', [
        'themeKeys' => ['--backdrop-opacity', '--opacity'],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            if (!isValidOpacityValue($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) use ($cssBackdropFilterValue) {
            return [
                decl('--tw-backdrop-opacity', "opacity({$value})"),
                decl('-webkit-backdrop-filter', $cssBackdropFilterValue),
                decl('backdrop-filter', $cssBackdropFilterValue),
            ];
        },
    ]);
}
