<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\decl;

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
        'handle' => function ($value) {
            return [decl('opacity', $value)];
        },
        'staticValues' => [
            '0' => [decl('opacity', '0')],
            '5' => [decl('opacity', '0.05')],
            '10' => [decl('opacity', '0.1')],
            '15' => [decl('opacity', '0.15')],
            '20' => [decl('opacity', '0.2')],
            '25' => [decl('opacity', '0.25')],
            '30' => [decl('opacity', '0.3')],
            '35' => [decl('opacity', '0.35')],
            '40' => [decl('opacity', '0.4')],
            '45' => [decl('opacity', '0.45')],
            '50' => [decl('opacity', '0.5')],
            '55' => [decl('opacity', '0.55')],
            '60' => [decl('opacity', '0.6')],
            '65' => [decl('opacity', '0.65')],
            '70' => [decl('opacity', '0.7')],
            '75' => [decl('opacity', '0.75')],
            '80' => [decl('opacity', '0.8')],
            '85' => [decl('opacity', '0.85')],
            '90' => [decl('opacity', '0.9')],
            '95' => [decl('opacity', '0.95')],
            '100' => [decl('opacity', '1')],
        ],
    ]);

    // =========================================================================
    // Box Shadow
    // =========================================================================

    // Shadow color variable (used by shadow utilities)
    $builder->functionalUtility('shadow', [
        'themeKeys' => ['--shadow'],
        'defaultValue' => 'var(--shadow, 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1))',
        'handle' => function ($value) {
            return [decl('box-shadow', $value)];
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

    // Inset shadow
    $builder->functionalUtility('inset-shadow', [
        'themeKeys' => ['--inset-shadow'],
        'defaultValue' => 'var(--inset-shadow, inset 0 2px 4px 0 rgb(0 0 0 / 0.05))',
        'handle' => function ($value) {
            return [decl('box-shadow', $value)];
        },
        'staticValues' => [
            'none' => [decl('box-shadow', 'none')],
            'sm' => [decl('box-shadow', 'var(--inset-shadow-sm, inset 0 1px 1px 0 rgb(0 0 0 / 0.05))')],
            'xs' => [decl('box-shadow', 'var(--inset-shadow-xs, inset 0 1px 0 0 rgb(0 0 0 / 0.05))')],
        ],
    ]);

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
}
