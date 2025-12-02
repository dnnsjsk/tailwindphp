<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\Ast\decl;
use function TailwindPHP\Ast\atRoot;
use function TailwindPHP\Utilities\property;
use function TailwindPHP\Utils\isPositiveInteger;
use function TailwindPHP\Utils\isValidSpacingMultiplier;

/**
 * Mask Utilities
 *
 * Port of mask utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - mask-linear-* (linear gradient masks)
 * - mask-radial-* (radial gradient masks)
 * - mask-conic-* (conic gradient masks)
 * - mask-x/y/t/r/b/l-* (edge masks)
 * - mask-circle, mask-ellipse (shape utilities)
 */

/**
 * Register mask utilities.
 */
function registerMaskUtilities(UtilityBuilder $builder): void
{
    // =========================================================================
    // Mask Gradient Properties
    // =========================================================================

    $maskPropertiesGradient = function () {
        return atRoot([
            property('--tw-mask-linear', 'linear-gradient(#fff, #fff)'),
            property('--tw-mask-radial', 'linear-gradient(#fff, #fff)'),
            property('--tw-mask-conic', 'linear-gradient(#fff, #fff)'),
        ]);
    };

    // =========================================================================
    // Linear Mask Properties
    // =========================================================================

    $maskPropertiesLinear = function () {
        return atRoot([
            property('--tw-mask-linear-position', '0deg'),
            property('--tw-mask-linear-from-position', '0%'),
            property('--tw-mask-linear-to-position', '100%'),
            property('--tw-mask-linear-from-color', 'black'),
            property('--tw-mask-linear-to-color', 'transparent'),
        ]);
    };

    // mask-linear-{angle} utility
    $builder->functionalUtility('mask-linear', [
        'themeKeys' => [],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "calc(1deg * {$value['value']})";
        },
        'handleNegativeBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "calc(1deg * -{$value['value']})";
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesLinear) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesLinear(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl('--tw-mask-linear', 'linear-gradient(var(--tw-mask-linear-stops, var(--tw-mask-linear-position)))'),
                decl('--tw-mask-linear-position', $value),
            ];
        },
    ]);

    // mask-linear-from-{position}% utility
    $builder->functionalUtility('mask-linear-from', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            $val = $value['value'] ?? '';
            if (str_ends_with($val, '%')) {
                $num = substr($val, 0, -1);
                if (isPositiveInteger($num)) {
                    return $val;
                }
            }
            return null;
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesLinear) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesLinear(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl(
                    '--tw-mask-linear-stops',
                    'var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position)'
                ),
                decl('--tw-mask-linear', 'linear-gradient(var(--tw-mask-linear-stops))'),
                decl('--tw-mask-linear-from-position', $value),
            ];
        },
    ]);

    // mask-linear-to-{position}% utility
    $builder->functionalUtility('mask-linear-to', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            $val = $value['value'] ?? '';
            if (str_ends_with($val, '%')) {
                $num = substr($val, 0, -1);
                if (isPositiveInteger($num)) {
                    return $val;
                }
            }
            return null;
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesLinear) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesLinear(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl(
                    '--tw-mask-linear-stops',
                    'var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position)'
                ),
                decl('--tw-mask-linear', 'linear-gradient(var(--tw-mask-linear-stops))'),
                decl('--tw-mask-linear-to-position', $value),
            ];
        },
    ]);

    // =========================================================================
    // Radial Mask Properties
    // =========================================================================

    $maskPropertiesRadial = function () {
        return atRoot([
            property('--tw-mask-radial-from-position', '0%'),
            property('--tw-mask-radial-to-position', '100%'),
            property('--tw-mask-radial-from-color', 'black'),
            property('--tw-mask-radial-to-color', 'transparent'),
            property('--tw-mask-radial-shape', 'ellipse'),
            property('--tw-mask-radial-size', 'farthest-corner'),
            property('--tw-mask-radial-position', 'center'),
        ]);
    };

    // mask-circle, mask-ellipse
    $builder->staticUtility('mask-circle', [['--tw-mask-radial-shape', 'circle']]);
    $builder->staticUtility('mask-ellipse', [['--tw-mask-radial-shape', 'ellipse']]);

    // mask-radial-* size utilities
    $builder->staticUtility('mask-radial-closest-side', [['--tw-mask-radial-size', 'closest-side']]);
    $builder->staticUtility('mask-radial-farthest-side', [['--tw-mask-radial-size', 'farthest-side']]);
    $builder->staticUtility('mask-radial-closest-corner', [['--tw-mask-radial-size', 'closest-corner']]);
    $builder->staticUtility('mask-radial-farthest-corner', [['--tw-mask-radial-size', 'farthest-corner']]);

    // mask-radial-at-* position utilities
    $builder->staticUtility('mask-radial-at-top', [['--tw-mask-radial-position', 'top']]);
    $builder->staticUtility('mask-radial-at-top-left', [['--tw-mask-radial-position', 'top left']]);
    $builder->staticUtility('mask-radial-at-top-right', [['--tw-mask-radial-position', 'top right']]);
    $builder->staticUtility('mask-radial-at-bottom', [['--tw-mask-radial-position', 'bottom']]);
    $builder->staticUtility('mask-radial-at-bottom-left', [['--tw-mask-radial-position', 'bottom left']]);
    $builder->staticUtility('mask-radial-at-bottom-right', [['--tw-mask-radial-position', 'bottom right']]);
    $builder->staticUtility('mask-radial-at-left', [['--tw-mask-radial-position', 'left']]);
    $builder->staticUtility('mask-radial-at-right', [['--tw-mask-radial-position', 'right']]);
    $builder->staticUtility('mask-radial-at-center', [['--tw-mask-radial-position', 'center']]);

    // mask-radial-at-[arbitrary] position
    $builder->functionalUtility('mask-radial-at', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('--tw-mask-radial-position', $value)];
        },
    ]);

    // mask-radial-[size] utility
    $builder->functionalUtility('mask-radial', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesRadial) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesRadial(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl('--tw-mask-radial', 'radial-gradient(var(--tw-mask-radial-stops, var(--tw-mask-radial-size)))'),
                decl('--tw-mask-radial-size', $value),
            ];
        },
    ]);

    // mask-radial-from-{position}% utility
    $builder->functionalUtility('mask-radial-from', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            $val = $value['value'] ?? '';
            if (str_ends_with($val, '%')) {
                $num = substr($val, 0, -1);
                if (isPositiveInteger($num)) {
                    return $val;
                }
            }
            return null;
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesRadial) {
            // Validate percentage values - must be positive
            if (str_ends_with($value, '%')) {
                $num = substr($value, 0, -1);
                if (!isPositiveInteger($num)) {
                    return null;
                }
            }
            return [
                $maskPropertiesGradient(),
                $maskPropertiesRadial(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl(
                    '--tw-mask-radial-stops',
                    'var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position)'
                ),
                decl('--tw-mask-radial', 'radial-gradient(var(--tw-mask-radial-stops))'),
                decl('--tw-mask-radial-from-position', $value),
            ];
        },
    ]);

    // mask-radial-to-{position}% utility
    $builder->functionalUtility('mask-radial-to', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            $val = $value['value'] ?? '';
            if (str_ends_with($val, '%')) {
                $num = substr($val, 0, -1);
                if (isPositiveInteger($num)) {
                    return $val;
                }
            }
            return null;
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesRadial) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesRadial(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl(
                    '--tw-mask-radial-stops',
                    'var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position)'
                ),
                decl('--tw-mask-radial', 'radial-gradient(var(--tw-mask-radial-stops))'),
                decl('--tw-mask-radial-to-position', $value),
            ];
        },
    ]);

    // =========================================================================
    // Conic Mask Properties
    // =========================================================================

    $maskPropertiesConic = function () {
        return atRoot([
            property('--tw-mask-conic-position', '0deg'),
            property('--tw-mask-conic-from-position', '0%'),
            property('--tw-mask-conic-to-position', '100%'),
            property('--tw-mask-conic-from-color', 'black'),
            property('--tw-mask-conic-to-color', 'transparent'),
        ]);
    };

    // mask-conic-{angle} utility
    $builder->functionalUtility('mask-conic', [
        'themeKeys' => [],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "calc(1deg * {$value['value']})";
        },
        'handleNegativeBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "calc(1deg * -{$value['value']})";
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesConic) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesConic(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl('--tw-mask-conic', 'conic-gradient(var(--tw-mask-conic-stops, var(--tw-mask-conic-position)))'),
                decl('--tw-mask-conic-position', $value),
            ];
        },
    ]);

    // mask-conic-from-{position}% utility
    $builder->functionalUtility('mask-conic-from', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            $val = $value['value'] ?? '';
            if (str_ends_with($val, '%')) {
                $num = substr($val, 0, -1);
                if (isPositiveInteger($num)) {
                    return $val;
                }
            }
            return null;
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesConic) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesConic(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl(
                    '--tw-mask-conic-stops',
                    'from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position)'
                ),
                decl('--tw-mask-conic', 'conic-gradient(var(--tw-mask-conic-stops))'),
                decl('--tw-mask-conic-from-position', $value),
            ];
        },
    ]);

    // mask-conic-to-{position}% utility
    $builder->functionalUtility('mask-conic-to', [
        'themeKeys' => [],
        'defaultValue' => null,
        'handleBareValue' => function ($value) {
            $val = $value['value'] ?? '';
            if (str_ends_with($val, '%')) {
                $num = substr($val, 0, -1);
                if (isPositiveInteger($num)) {
                    return $val;
                }
            }
            return null;
        },
        'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesConic) {
            return [
                $maskPropertiesGradient(),
                $maskPropertiesConic(),
                decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                decl('mask-composite', 'intersect'),
                decl(
                    '--tw-mask-conic-stops',
                    'from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position)'
                ),
                decl('--tw-mask-conic', 'conic-gradient(var(--tw-mask-conic-stops))'),
                decl('--tw-mask-conic-to-position', $value),
            ];
        },
    ]);

    // =========================================================================
    // Edge Mask Properties
    // =========================================================================

    $maskPropertiesEdge = function () {
        return atRoot([
            property('--tw-mask-left', 'linear-gradient(#fff, #fff)'),
            property('--tw-mask-right', 'linear-gradient(#fff, #fff)'),
            property('--tw-mask-bottom', 'linear-gradient(#fff, #fff)'),
            property('--tw-mask-top', 'linear-gradient(#fff, #fff)'),
        ]);
    };

    // Helper function to create edge mask utilities
    $createMaskEdgeUtility = function (string $name, string $stop, array $edges) use ($builder, $maskPropertiesGradient, $maskPropertiesEdge) {
        $builder->functionalUtility($name, [
            'themeKeys' => [],
            'defaultValue' => null,
            'handleBareValue' => function ($value) {
                $val = $value['value'] ?? '';
                if (str_ends_with($val, '%')) {
                    $num = substr($val, 0, -1);
                    if (isPositiveInteger($num)) {
                        return $val;
                    }
                }
                return null;
            },
            'handle' => function ($value) use ($maskPropertiesGradient, $maskPropertiesEdge, $stop, $edges) {
                $nodes = [
                    $maskPropertiesGradient(),
                    $maskPropertiesEdge(),
                    decl('mask-image', 'var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic)'),
                    decl('mask-composite', 'intersect'),
                    decl('--tw-mask-linear', 'var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top)'),
                ];

                foreach (['top', 'right', 'bottom', 'left'] as $edge) {
                    if (!($edges[$edge] ?? false)) {
                        continue;
                    }

                    $nodes[] = decl(
                        "--tw-mask-{$edge}",
                        "linear-gradient(to {$edge}, var(--tw-mask-{$edge}-from-color) var(--tw-mask-{$edge}-from-position), var(--tw-mask-{$edge}-to-color) var(--tw-mask-{$edge}-to-position))"
                    );

                    $nodes[] = atRoot([
                        property("--tw-mask-{$edge}-from-position", '0%'),
                        property("--tw-mask-{$edge}-to-position", '100%'),
                        property("--tw-mask-{$edge}-from-color", 'black'),
                        property("--tw-mask-{$edge}-to-color", 'transparent'),
                    ]);

                    $nodes[] = decl("--tw-mask-{$edge}-{$stop}-position", $value);
                }

                return $nodes;
            },
        ]);
    };

    // Register edge mask utilities
    $createMaskEdgeUtility('mask-x-from', 'from', ['top' => false, 'right' => true, 'bottom' => false, 'left' => true]);
    $createMaskEdgeUtility('mask-x-to', 'to', ['top' => false, 'right' => true, 'bottom' => false, 'left' => true]);
    $createMaskEdgeUtility('mask-y-from', 'from', ['top' => true, 'right' => false, 'bottom' => true, 'left' => false]);
    $createMaskEdgeUtility('mask-y-to', 'to', ['top' => true, 'right' => false, 'bottom' => true, 'left' => false]);
    $createMaskEdgeUtility('mask-t-from', 'from', ['top' => true, 'right' => false, 'bottom' => false, 'left' => false]);
    $createMaskEdgeUtility('mask-t-to', 'to', ['top' => true, 'right' => false, 'bottom' => false, 'left' => false]);
    $createMaskEdgeUtility('mask-r-from', 'from', ['top' => false, 'right' => true, 'bottom' => false, 'left' => false]);
    $createMaskEdgeUtility('mask-r-to', 'to', ['top' => false, 'right' => true, 'bottom' => false, 'left' => false]);
    $createMaskEdgeUtility('mask-b-from', 'from', ['top' => false, 'right' => false, 'bottom' => true, 'left' => false]);
    $createMaskEdgeUtility('mask-b-to', 'to', ['top' => false, 'right' => false, 'bottom' => true, 'left' => false]);
    $createMaskEdgeUtility('mask-l-from', 'from', ['top' => false, 'right' => false, 'bottom' => false, 'left' => true]);
    $createMaskEdgeUtility('mask-l-to', 'to', ['top' => false, 'right' => false, 'bottom' => false, 'left' => true]);
}
