<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\Ast\decl;

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
