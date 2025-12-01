<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use TailwindPHP\Theme;
use function TailwindPHP\decl;
use function TailwindPHP\Utils\isPositiveInteger;

/**
 * Transforms Utilities
 *
 * Port of transform utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - origin (transform-origin)
 * - perspective-origin
 * - perspective
 * - translate, translate-x, translate-y, translate-z, translate-3d
 * - scale, scale-x, scale-y, scale-z, scale-3d
 * - rotate, rotate-x, rotate-y, rotate-z
 * - skew, skew-x, skew-y
 * - transform
 */

/**
 * Register transforms utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerTransformsUtilities(UtilityBuilder $builder): void
{
    $theme = $builder->getTheme();

    // =========================================================================
    // Transform Origin
    // =========================================================================

    $builder->functionalUtility('origin', [
        'themeKeys' => ['--transform-origin'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('transform-origin', $value)];
        },
        'staticValues' => [
            'center' => [decl('transform-origin', 'center')],
            'top' => [decl('transform-origin', 'top')],
            'top-right' => [decl('transform-origin', '100% 0')],
            'right' => [decl('transform-origin', '100%')],
            'bottom-right' => [decl('transform-origin', '100% 100%')],
            'bottom' => [decl('transform-origin', 'bottom')],
            'bottom-left' => [decl('transform-origin', '0 100%')],
            'left' => [decl('transform-origin', '0')],
            'top-left' => [decl('transform-origin', '0 0')],
        ],
    ]);

    // =========================================================================
    // Perspective Origin
    // =========================================================================

    $builder->functionalUtility('perspective-origin', [
        'themeKeys' => ['--perspective-origin'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('perspective-origin', $value)];
        },
        'staticValues' => [
            'center' => [decl('perspective-origin', 'center')],
            'top' => [decl('perspective-origin', 'top')],
            'top-right' => [decl('perspective-origin', '100% 0')],
            'right' => [decl('perspective-origin', '100%')],
            'bottom-right' => [decl('perspective-origin', '100% 100%')],
            'bottom' => [decl('perspective-origin', 'bottom')],
            'bottom-left' => [decl('perspective-origin', '0 100%')],
            'left' => [decl('perspective-origin', '0')],
            'top-left' => [decl('perspective-origin', '0 0')],
        ],
    ]);

    // =========================================================================
    // Perspective
    // =========================================================================

    $builder->functionalUtility('perspective', [
        'themeKeys' => ['--perspective'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('perspective', $value)];
        },
        'staticValues' => [
            'none' => [decl('perspective', 'none')],
        ],
    ]);

    // =========================================================================
    // Translate
    // =========================================================================

    // translate-none
    $builder->staticUtility('translate-none', [['translate', 'none']]);

    // translate-full / -translate-full
    $builder->staticUtility('translate-full', [
        ['--tw-translate-x', '100%'],
        ['--tw-translate-y', '100%'],
        ['translate', 'var(--tw-translate-x) var(--tw-translate-y)'],
    ]);
    $builder->staticUtility('-translate-full', [
        ['--tw-translate-x', '-100%'],
        ['--tw-translate-y', '-100%'],
        ['translate', 'var(--tw-translate-x) var(--tw-translate-y)'],
    ]);

    // translate-* (spacing-based)
    $builder->spacingUtility('translate', ['--translate', '--spacing'], function ($value) {
        return [
            decl('--tw-translate-x', $value),
            decl('--tw-translate-y', $value),
            decl('translate', 'var(--tw-translate-x) var(--tw-translate-y)'),
        ];
    }, ['supportsNegative' => true, 'supportsFractions' => true]);

    // translate-x-full / -translate-x-full / translate-y-full / -translate-y-full
    foreach (['x', 'y'] as $axis) {
        $builder->staticUtility("translate-{$axis}-full", [
            ["--tw-translate-{$axis}", '100%'],
            ['translate', 'var(--tw-translate-x) var(--tw-translate-y)'],
        ]);
        $builder->staticUtility("-translate-{$axis}-full", [
            ["--tw-translate-{$axis}", '-100%'],
            ['translate', 'var(--tw-translate-x) var(--tw-translate-y)'],
        ]);

        // translate-x-* / translate-y-* (spacing-based)
        $builder->spacingUtility("translate-{$axis}", ['--translate', '--spacing'], function ($value) use ($axis) {
            return [
                decl("--tw-translate-{$axis}", $value),
                decl('translate', 'var(--tw-translate-x) var(--tw-translate-y)'),
            ];
        }, ['supportsNegative' => true, 'supportsFractions' => true]);
    }

    // translate-z-* (spacing-based, no fractions)
    $builder->spacingUtility('translate-z', ['--translate', '--spacing'], function ($value) {
        return [
            decl('--tw-translate-z', $value),
            decl('translate', 'var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z)'),
        ];
    }, ['supportsNegative' => true]);

    // translate-3d
    $builder->staticUtility('translate-3d', [
        ['translate', 'var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z)'],
    ]);

    // =========================================================================
    // Scale
    // =========================================================================

    // scale-none
    $builder->staticUtility('scale-none', [['scale', 'none']]);

    // scale-* (custom handler for bare integer -> percentage)
    $builder->functionalUtility('scale', [
        'themeKeys' => ['--scale'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}%";
        },
        'handle' => function ($value) {
            return [
                decl('--tw-scale-x', $value),
                decl('--tw-scale-y', $value),
                decl('--tw-scale-z', $value),
                decl('scale', 'var(--tw-scale-x) var(--tw-scale-y)'),
            ];
        },
    ]);

    // scale-x-*, scale-y-*, scale-z-*
    foreach (['x', 'y', 'z'] as $axis) {
        $scaleValue = $axis === 'z'
            ? 'var(--tw-scale-x) var(--tw-scale-y) var(--tw-scale-z)'
            : 'var(--tw-scale-x) var(--tw-scale-y)';

        $builder->functionalUtility("scale-{$axis}", [
            'themeKeys' => ['--scale'],
            'defaultValue' => null,
            'supportsNegative' => true,
            'handleBareValue' => function ($value) {
                if (!isPositiveInteger($value['value'])) {
                    return null;
                }
                return "{$value['value']}%";
            },
            'handle' => function ($value) use ($axis, $scaleValue) {
                return [
                    decl("--tw-scale-{$axis}", $value),
                    decl('scale', $scaleValue),
                ];
            },
        ]);
    }

    // scale-3d
    $builder->staticUtility('scale-3d', [
        ['scale', 'var(--tw-scale-x) var(--tw-scale-y) var(--tw-scale-z)'],
    ]);

    // =========================================================================
    // Rotate
    // =========================================================================

    // rotate-none
    $builder->staticUtility('rotate-none', [['rotate', 'none']]);

    // rotate-* (custom handler for bare integer -> deg)
    $builder->functionalUtility('rotate', [
        'themeKeys' => ['--rotate'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}deg";
        },
        'handle' => function ($value) {
            return [decl('rotate', $value)];
        },
    ]);

    // Transform value for rotate-x, rotate-y, rotate-z and skew
    $transformValue = 'var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, )';

    // rotate-x-*, rotate-y-*, rotate-z-*
    foreach (['x', 'y', 'z'] as $axis) {
        $builder->functionalUtility("rotate-{$axis}", [
            'themeKeys' => ['--rotate'],
            'defaultValue' => null,
            'supportsNegative' => true,
            'handleBareValue' => function ($value) {
                if (!isPositiveInteger($value['value'])) {
                    return null;
                }
                return "{$value['value']}deg";
            },
            'handle' => function ($value) use ($axis, $transformValue) {
                $rotateFunc = 'rotate' . strtoupper($axis);
                return [
                    decl("--tw-rotate-{$axis}", "{$rotateFunc}({$value})"),
                    decl('transform', $transformValue),
                ];
            },
        ]);
    }

    // =========================================================================
    // Skew
    // =========================================================================

    // skew-* (both x and y)
    $builder->functionalUtility('skew', [
        'themeKeys' => ['--skew'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}deg";
        },
        'handle' => function ($value) use ($transformValue) {
            return [
                decl('--tw-skew-x', "skewX({$value})"),
                decl('--tw-skew-y', "skewY({$value})"),
                decl('transform', $transformValue),
            ];
        },
    ]);

    // skew-x-*, skew-y-*
    foreach (['x', 'y'] as $axis) {
        $builder->functionalUtility("skew-{$axis}", [
            'themeKeys' => ['--skew'],
            'defaultValue' => null,
            'supportsNegative' => true,
            'handleBareValue' => function ($value) {
                if (!isPositiveInteger($value['value'])) {
                    return null;
                }
                return "{$value['value']}deg";
            },
            'handle' => function ($value) use ($axis, $transformValue) {
                $skewFunc = 'skew' . strtoupper($axis);
                return [
                    decl("--tw-skew-{$axis}", "{$skewFunc}({$value})"),
                    decl('transform', $transformValue),
                ];
            },
        ]);
    }

    // =========================================================================
    // Transform (general)
    // =========================================================================

    $builder->staticUtility('transform', [
        ['transform', $transformValue],
    ]);

    $builder->staticUtility('transform-cpu', [
        ['transform', $transformValue],
    ]);

    $builder->staticUtility('transform-gpu', [
        ['transform', "translateZ(0) {$transformValue}"],
    ]);

    $builder->staticUtility('transform-none', [
        ['transform', 'none'],
    ]);

    // transform-[arbitrary]
    $builder->functionalUtility('transform', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('transform', $value)];
        },
    ]);
}
