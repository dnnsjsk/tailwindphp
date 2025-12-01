<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use TailwindPHP\Theme;
use function TailwindPHP\Ast\decl;
use function TailwindPHP\Utils\isPositiveInteger;

/**
 * SVG Utilities
 *
 * Port of SVG utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - fill
 * - stroke (color and width)
 */

/**
 * Register SVG utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerSvgUtilities(UtilityBuilder $builder): void
{
    $theme = $builder->getTheme();

    // =========================================================================
    // Fill
    // =========================================================================

    $builder->staticUtility('fill-none', [['fill', 'none']]);

    $builder->colorUtility('fill', [
        'themeKeys' => ['--fill', '--color'],
        'handle' => function ($value) {
            return [decl('fill', $value)];
        },
    ]);

    // =========================================================================
    // Stroke
    // =========================================================================

    $builder->staticUtility('stroke-none', [['stroke', 'none']]);

    // stroke color utility (functional to handle both color and width)
    $builder->colorUtility('stroke', [
        'themeKeys' => ['--stroke', '--color'],
        'handle' => function ($value) {
            return [decl('stroke', $value)];
        },
    ]);

    // stroke-width values (0, 1, 2)
    foreach (['0', '1', '2'] as $value) {
        $width = $value === '0' ? '0' : "{$value}px";
        $builder->staticUtility("stroke-{$value}", [['stroke-width', $width]]);
    }
}
