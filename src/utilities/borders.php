<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\decl;
use function TailwindPHP\styleRule;
use function TailwindPHP\Utils\isPositiveInteger;

/**
 * Border Utilities
 *
 * Port of border utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - border-radius (rounded-*)
 * - border-width (border-*)
 * - border-style (border-solid, border-dashed, etc.)
 * - border-collapse
 * - outline
 */

// Very large number for "full" radius when --radius-full is not defined
const RADIUS_FULL_DEFAULT = '3.40282e38px';

/**
 * Register border utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerBorderUtilities(UtilityBuilder $builder): void
{
    // =========================================================================
    // Border Radius
    // =========================================================================

    // Helper function to create radius utility
    $createRadiusUtility = function (string $name, array $properties, bool $hasDefault = false) use ($builder) {
        $builder->functionalUtility($name, [
            'themeKeys' => ['--radius'],
            'defaultValue' => $hasDefault ? 'var(--radius)' : null,
            'handle' => function ($value) use ($properties) {
                $decls = [];
                foreach ($properties as $prop) {
                    $decls[] = decl($prop, $value);
                }
                return $decls;
            },
            'staticValues' => [
                'none' => array_map(fn($p) => decl($p, '0'), $properties),
                'full' => array_map(fn($p) => decl($p, RADIUS_FULL_DEFAULT), $properties),
            ],
        ]);
    };

    // rounded (all corners)
    $createRadiusUtility('rounded', ['border-radius'], true);

    // rounded-s (start corners: top-left and bottom-left in LTR)
    $createRadiusUtility('rounded-s', ['border-start-start-radius', 'border-end-start-radius'], true);

    // rounded-e (end corners)
    $createRadiusUtility('rounded-e', ['border-start-end-radius', 'border-end-end-radius'], true);

    // rounded-t (top corners)
    $createRadiusUtility('rounded-t', ['border-top-left-radius', 'border-top-right-radius'], true);

    // rounded-r (right corners)
    $createRadiusUtility('rounded-r', ['border-top-right-radius', 'border-bottom-right-radius'], true);

    // rounded-b (bottom corners)
    $createRadiusUtility('rounded-b', ['border-bottom-right-radius', 'border-bottom-left-radius'], true);

    // rounded-l (left corners)
    $createRadiusUtility('rounded-l', ['border-top-left-radius', 'border-bottom-left-radius'], true);

    // Individual corners (logical)
    $createRadiusUtility('rounded-ss', ['border-start-start-radius'], true);
    $createRadiusUtility('rounded-se', ['border-start-end-radius'], true);
    $createRadiusUtility('rounded-ee', ['border-end-end-radius'], true);
    $createRadiusUtility('rounded-es', ['border-end-start-radius'], true);

    // Individual corners (physical)
    $createRadiusUtility('rounded-tl', ['border-top-left-radius'], true);
    $createRadiusUtility('rounded-tr', ['border-top-right-radius'], true);
    $createRadiusUtility('rounded-br', ['border-bottom-right-radius'], true);
    $createRadiusUtility('rounded-bl', ['border-bottom-left-radius'], true);

    // =========================================================================
    // Border Width
    // =========================================================================

    // Helper for border width utilities
    $createBorderWidthUtility = function (string $name, array $properties) use ($builder) {
        $builder->functionalUtility($name, [
            'themeKeys' => ['--border-width'],
            'defaultValue' => '1px',
            'handle' => function ($value) use ($properties) {
                $decls = [];
                foreach ($properties as $prop) {
                    $decls[] = decl($prop, $value);
                }
                return $decls;
            },
            'staticValues' => [
                '0' => array_map(fn($p) => decl($p, '0px'), $properties),
                '2' => array_map(fn($p) => decl($p, '2px'), $properties),
                '4' => array_map(fn($p) => decl($p, '4px'), $properties),
                '8' => array_map(fn($p) => decl($p, '8px'), $properties),
            ],
        ]);
    };

    $createBorderWidthUtility('border', ['border-width']);
    $createBorderWidthUtility('border-x', ['border-left-width', 'border-right-width']);
    $createBorderWidthUtility('border-y', ['border-top-width', 'border-bottom-width']);
    $createBorderWidthUtility('border-s', ['border-inline-start-width']);
    $createBorderWidthUtility('border-e', ['border-inline-end-width']);
    $createBorderWidthUtility('border-t', ['border-top-width']);
    $createBorderWidthUtility('border-r', ['border-right-width']);
    $createBorderWidthUtility('border-b', ['border-bottom-width']);
    $createBorderWidthUtility('border-l', ['border-left-width']);

    // =========================================================================
    // Border Style
    // =========================================================================

    $builder->staticUtility('border-solid', [['--tw-border-style', 'solid'], ['border-style', 'solid']]);
    $builder->staticUtility('border-dashed', [['--tw-border-style', 'dashed'], ['border-style', 'dashed']]);
    $builder->staticUtility('border-dotted', [['--tw-border-style', 'dotted'], ['border-style', 'dotted']]);
    $builder->staticUtility('border-double', [['--tw-border-style', 'double'], ['border-style', 'double']]);
    $builder->staticUtility('border-hidden', [['--tw-border-style', 'hidden'], ['border-style', 'hidden']]);
    $builder->staticUtility('border-none', [['--tw-border-style', 'none'], ['border-style', 'none']]);

    // =========================================================================
    // Border Collapse
    // =========================================================================

    $builder->staticUtility('border-collapse', [['border-collapse', 'collapse']]);
    $builder->staticUtility('border-separate', [['border-collapse', 'separate']]);

    // =========================================================================
    // Outline Style
    // =========================================================================

    $builder->staticUtility('outline-none', [
        ['outline', '2px solid transparent'],
        ['outline-offset', '2px'],
    ]);
    $builder->staticUtility('outline', [['outline-style', 'solid']]);
    $builder->staticUtility('outline-dashed', [['outline-style', 'dashed']]);
    $builder->staticUtility('outline-dotted', [['outline-style', 'dotted']]);
    $builder->staticUtility('outline-double', [['outline-style', 'double']]);

    // Outline Width
    $builder->functionalUtility('outline', [
        'themeKeys' => ['--outline-width'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('outline-width', $value)];
        },
        'staticValues' => [
            '0' => [decl('outline-width', '0px')],
            '1' => [decl('outline-width', '1px')],
            '2' => [decl('outline-width', '2px')],
            '4' => [decl('outline-width', '4px')],
            '8' => [decl('outline-width', '8px')],
        ],
    ]);

    // Outline Offset
    // Note: For bare values like -outline-offset-4, Tailwind outputs calc(4px * -1)
    // not -4px. This is because the handleBareValue returns "4px" which then gets
    // wrapped in calc() by the negation logic.
    $builder->functionalUtility('outline-offset', [
        'themeKeys' => ['--outline-offset'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}px";
        },
        'handle' => function ($value) {
            return [decl('outline-offset', $value)];
        },
        'staticValues' => [
            '0' => [decl('outline-offset', '0px')],
            '1' => [decl('outline-offset', '1px')],
            '2' => [decl('outline-offset', '2px')],
            '4' => [decl('outline-offset', '4px')],
            '8' => [decl('outline-offset', '8px')],
        ],
    ]);

    // =========================================================================
    // Divide Width (space between children)
    // =========================================================================

    // divide-x
    $builder->functionalUtility('divide-x', [
        'themeKeys' => ['--divide-width'],
        'defaultValue' => '1px',
        'handle' => function ($value) {
            return [
                decl('--tw-divide-x-reverse', '0'),
                decl('border-inline-end-width', "calc({$value} * var(--tw-divide-x-reverse))"),
                decl('border-inline-start-width', "calc({$value} * calc(1 - var(--tw-divide-x-reverse)))"),
            ];
        },
    ]);

    // divide-y
    $builder->functionalUtility('divide-y', [
        'themeKeys' => ['--divide-width'],
        'defaultValue' => '1px',
        'handle' => function ($value) {
            return [
                decl('--tw-divide-y-reverse', '0'),
                decl('border-block-end-width', "calc({$value} * var(--tw-divide-y-reverse))"),
                decl('border-block-start-width', "calc({$value} * calc(1 - var(--tw-divide-y-reverse)))"),
            ];
        },
    ]);

    // divide-x-reverse, divide-y-reverse
    // These use :where(& > :not(:last-child)) selector
    $builder->staticUtility('divide-x-reverse', [
        fn() => styleRule(':where(& > :not(:last-child))', [decl('--tw-divide-x-reverse', '1')]),
    ]);
    $builder->staticUtility('divide-y-reverse', [
        fn() => styleRule(':where(& > :not(:last-child))', [decl('--tw-divide-y-reverse', '1')]),
    ]);

    // Divide Style - also uses :where(& > :not(:last-child)) selector
    $builder->staticUtility('divide-solid', [
        fn() => styleRule(':where(& > :not(:last-child))', [
            decl('--tw-border-style', 'solid'),
            decl('border-style', 'solid'),
        ]),
    ]);
    $builder->staticUtility('divide-dashed', [
        fn() => styleRule(':where(& > :not(:last-child))', [
            decl('--tw-border-style', 'dashed'),
            decl('border-style', 'dashed'),
        ]),
    ]);
    $builder->staticUtility('divide-dotted', [
        fn() => styleRule(':where(& > :not(:last-child))', [
            decl('--tw-border-style', 'dotted'),
            decl('border-style', 'dotted'),
        ]),
    ]);
    $builder->staticUtility('divide-double', [
        fn() => styleRule(':where(& > :not(:last-child))', [
            decl('--tw-border-style', 'double'),
            decl('border-style', 'double'),
        ]),
    ]);
    $builder->staticUtility('divide-none', [
        fn() => styleRule(':where(& > :not(:last-child))', [
            decl('--tw-border-style', 'none'),
            decl('border-style', 'none'),
        ]),
    ]);
}
