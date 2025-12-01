<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\decl;

/**
 * Sizing Utilities
 *
 * Port of sizing utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - width (w, min-w, max-w)
 * - height (h, min-h, max-h)
 * - size (size)
 */

/**
 * Register sizing utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerSizingUtilities(UtilityBuilder $builder): void
{
    // Width utilities
    $builder->functionalUtility('w', [
        'themeKeys' => ['--width', '--spacing'],
        'supportsFractions' => true,
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [decl('width', $value)];
        },
        'staticValues' => [
            'full' => [decl('width', '100%')],
            'auto' => [decl('width', 'auto')],
            'screen' => [decl('width', '100vw')],
            'svw' => [decl('width', '100svw')],
            'lvw' => [decl('width', '100lvw')],
            'dvw' => [decl('width', '100dvw')],
            'min' => [decl('width', 'min-content')],
            'max' => [decl('width', 'max-content')],
            'fit' => [decl('width', 'fit-content')],
        ],
    ]);

    // Min-width utilities
    $builder->functionalUtility('min-w', [
        'themeKeys' => ['--min-width', '--container', '--spacing'],
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [decl('min-width', $value)];
        },
        'staticValues' => [
            'full' => [decl('min-width', '100%')],
            'auto' => [decl('min-width', 'auto')],
            'min' => [decl('min-width', 'min-content')],
            'max' => [decl('min-width', 'max-content')],
            'fit' => [decl('min-width', 'fit-content')],
        ],
    ]);

    // Max-width utilities
    $builder->functionalUtility('max-w', [
        'themeKeys' => ['--max-width', '--container', '--spacing'],
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [decl('max-width', $value)];
        },
        'staticValues' => [
            'none' => [decl('max-width', 'none')],
            'full' => [decl('max-width', '100%')],
            'max' => [decl('max-width', 'max-content')],
            'fit' => [decl('max-width', 'fit-content')],
        ],
    ]);

    // Height utilities
    $builder->functionalUtility('h', [
        'themeKeys' => ['--height', '--spacing'],
        'supportsFractions' => true,
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [decl('height', $value)];
        },
        'staticValues' => [
            'full' => [decl('height', '100%')],
            'auto' => [decl('height', 'auto')],
            'screen' => [decl('height', '100vh')],
            'svh' => [decl('height', '100svh')],
            'lvh' => [decl('height', '100lvh')],
            'dvh' => [decl('height', '100dvh')],
            'min' => [decl('height', 'min-content')],
            'max' => [decl('height', 'max-content')],
            'fit' => [decl('height', 'fit-content')],
            'lh' => [decl('height', '1lh')],
        ],
    ]);

    // Min-height utilities
    $builder->functionalUtility('min-h', [
        'themeKeys' => ['--min-height', '--spacing'],
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [decl('min-height', $value)];
        },
        'staticValues' => [
            'full' => [decl('min-height', '100%')],
            'auto' => [decl('min-height', 'auto')],
            'screen' => [decl('min-height', '100vh')],
            'svh' => [decl('min-height', '100svh')],
            'lvh' => [decl('min-height', '100lvh')],
            'dvh' => [decl('min-height', '100dvh')],
            'min' => [decl('min-height', 'min-content')],
            'max' => [decl('min-height', 'max-content')],
            'fit' => [decl('min-height', 'fit-content')],
            'lh' => [decl('min-height', '1lh')],
        ],
    ]);

    // Max-height utilities
    $builder->functionalUtility('max-h', [
        'themeKeys' => ['--max-height', '--spacing'],
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [decl('max-height', $value)];
        },
        'staticValues' => [
            'none' => [decl('max-height', 'none')],
            'full' => [decl('max-height', '100%')],
            'screen' => [decl('max-height', '100vh')],
            'svh' => [decl('max-height', '100svh')],
            'lvh' => [decl('max-height', '100lvh')],
            'dvh' => [decl('max-height', '100dvh')],
            'min' => [decl('max-height', 'min-content')],
            'max' => [decl('max-height', 'max-content')],
            'fit' => [decl('max-height', 'fit-content')],
            'lh' => [decl('max-height', '1lh')],
        ],
    ]);

    // Size utility (sets both width and height)
    $builder->functionalUtility('size', [
        'themeKeys' => ['--size', '--spacing'],
        'supportsFractions' => true,
        'defaultValue' => null, // No default - requires a value
        'handle' => function ($value) {
            return [
                decl('width', $value),
                decl('height', $value),
            ];
        },
        'staticValues' => [
            'full' => [decl('width', '100%'), decl('height', '100%')],
            'auto' => [decl('width', 'auto'), decl('height', 'auto')],
            'min' => [decl('width', 'min-content'), decl('height', 'min-content')],
            'max' => [decl('width', 'max-content'), decl('height', 'max-content')],
            'fit' => [decl('width', 'fit-content'), decl('height', 'fit-content')],
        ],
    ]);
}
