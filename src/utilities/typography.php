<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use function TailwindPHP\Ast\decl;
use function TailwindPHP\Utils\isPositiveInteger;
use function TailwindPHP\Utils\isValidSpacingMultiplier;
use function TailwindPHP\Utils\inferDataType;
use function TailwindPHP\Utilities\asColor;
use function TailwindPHP\Utilities\resolveThemeColor;

/**
 * Typography Utilities
 *
 * Port of typography utilities from: packages/tailwindcss/src/utilities.ts
 *
 * Includes:
 * - font-family (font-sans, font-serif, font-mono)
 * - font-style (italic, not-italic)
 * - font-weight (font-thin, font-bold, etc.)
 * - font-size (text-sm, text-lg, etc.)
 * - line-height (leading-*)
 * - letter-spacing (tracking-*)
 * - text-decoration (underline, line-through, etc.)
 * - text-transform (uppercase, lowercase, capitalize)
 * - text-align (text-left, text-center, etc.)
 * - text-wrap (text-wrap, text-nowrap, etc.)
 * - whitespace (whitespace-normal, whitespace-nowrap, etc.)
 * - word-break (break-normal, break-words, etc.)
 * - hyphens (hyphens-none, hyphens-manual, hyphens-auto)
 * - list-style (list-none, list-disc, list-decimal)
 * - vertical-align (align-baseline, align-top, etc.)
 */

/**
 * Register typography utilities.
 *
 * @param UtilityBuilder $builder
 * @return void
 */
function registerTypographyUtilities(UtilityBuilder $builder): void
{
    // =========================================================================
    // Text (color and font-size)
    // =========================================================================
    $theme = $builder->getTheme();
    $utilities = $builder->getUtilities();

    $utilities->functional('text', function (array $candidate) use ($theme) {
        if (!isset($candidate['value'])) {
            return null;
        }

        // Handle arbitrary values
        if ($candidate['value']['kind'] === 'arbitrary') {
            $value = $candidate['value']['value'];
            $type = $candidate['value']['dataType'] ??
                inferDataType($value, ['color', 'length', 'percentage', 'absolute-size', 'relative-size']);

            switch ($type) {
                case 'size':
                case 'length':
                case 'percentage':
                case 'absolute-size':
                case 'relative-size':
                    if (isset($candidate['modifier'])) {
                        $modifier = null;
                        if ($candidate['modifier']['kind'] === 'arbitrary') {
                            $modifier = $candidate['modifier']['value'];
                        } else {
                            $modifier = $theme->resolve($candidate['modifier']['value'], ['--leading']);
                            if (!$modifier && isValidSpacingMultiplier($candidate['modifier']['value'])) {
                                $multiplier = $theme->resolve(null, ['--spacing']);
                                if ($multiplier) {
                                    $modifier = "calc({$multiplier} * {$candidate['modifier']['value']})";
                                }
                            }
                            // Shorthand for leading-none
                            if (!$modifier && $candidate['modifier']['value'] === 'none') {
                                $modifier = '1';
                            }
                        }

                        if ($modifier) {
                            return [decl('font-size', $value), decl('line-height', $modifier)];
                        }
                        return null;
                    }
                    return [decl('font-size', $value)];

                default:
                    $value = asColor($value, $candidate['modifier'] ?? null, $theme);
                    if ($value === null) return null;
                    return [decl('color', $value)];
            }
        }

        // Try color first (--text-color, --color)
        $value = resolveThemeColor($candidate, $theme, ['--text-color', '--color']);
        if ($value !== null) {
            return [decl('color', $value)];
        }

        // Try font-size (--text namespace)
        $result = $theme->resolveWith($candidate['value']['value'], ['--text'], ['--line-height', '--letter-spacing', '--font-weight']);
        if ($result !== null) {
            [$fontSize, $options] = $result;

            if (isset($candidate['modifier'])) {
                $modifier = null;
                if ($candidate['modifier']['kind'] === 'arbitrary') {
                    $modifier = $candidate['modifier']['value'];
                } else {
                    $modifier = $theme->resolve($candidate['modifier']['value'], ['--leading']);
                    if (!$modifier && isValidSpacingMultiplier($candidate['modifier']['value'])) {
                        $multiplier = $theme->resolve(null, ['--spacing']);
                        if ($multiplier) {
                            $modifier = "calc({$multiplier} * {$candidate['modifier']['value']})";
                        }
                    }
                    // Shorthand for leading-none
                    if (!$modifier && $candidate['modifier']['value'] === 'none') {
                        $modifier = '1';
                    }
                }

                if (!$modifier) {
                    return null;
                }

                $declarations = [decl('font-size', $fontSize)];
                if ($modifier) {
                    $declarations[] = decl('line-height', $modifier);
                }
                return $declarations;
            }

            if (is_string($options)) {
                return [decl('font-size', $fontSize), decl('line-height', $options)];
            }

            $declarations = [decl('font-size', $fontSize)];
            if (isset($options['--line-height'])) {
                $declarations[] = decl('line-height', "var(--tw-leading, {$options['--line-height']})");
            }
            if (isset($options['--letter-spacing'])) {
                $declarations[] = decl('letter-spacing', "var(--tw-tracking, {$options['--letter-spacing']})");
            }
            if (isset($options['--font-weight'])) {
                $declarations[] = decl('font-weight', "var(--tw-font-weight, {$options['--font-weight']})");
            }
            return $declarations;
        }

        return null;
    });

    $builder->suggest('text', fn() => [
        [
            'values' => ['current', 'inherit', 'transparent'],
            'valueThemeKeys' => ['--text-color', '--color'],
            'modifiers' => array_map(fn($i) => (string)($i * 5), range(0, 20)),
        ],
        [
            'values' => [],
            'valueThemeKeys' => ['--text'],
            'modifiers' => [],
            'modifierThemeKeys' => ['--leading'],
        ],
    ]);

    // Font Style
    $builder->staticUtility('italic', [['font-style', 'italic']]);
    $builder->staticUtility('not-italic', [['font-style', 'normal']]);

    // Font Stretch
    $fontStretchKeywords = [
        'ultra-condensed', 'extra-condensed', 'condensed', 'semi-condensed',
        'normal', 'semi-expanded', 'expanded', 'extra-expanded', 'ultra-expanded',
    ];

    $builder->functionalUtility('font-stretch', [
        'themeKeys' => [],
        'handleBareValue' => function ($value) use ($fontStretchKeywords) {
            // Handle percentage values (50%, 100%, 200%)
            if (preg_match('/^(\d+)%$/', $value['value'], $m)) {
                $percent = (int)$m[1];
                // Valid range is 50% to 200%
                if ($percent >= 50 && $percent <= 200 && $percent % 1 === 0) {
                    return $value['value'];
                }
                return null;
            }
            return null;
        },
        'handle' => function ($value) {
            return [decl('font-stretch', $value)];
        },
        'staticValues' => array_combine(
            $fontStretchKeywords,
            array_map(fn($kw) => [decl('font-stretch', $kw)], $fontStretchKeywords)
        ),
    ]);

    // Font Weight
    $builder->functionalUtility('font', [
        'themeKeys' => ['--font', '--font-weight'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [
                decl('--tw-font-weight', $value),
                decl('font-weight', $value),
            ];
        },
        'staticValues' => [
            'thin' => [decl('--tw-font-weight', '100'), decl('font-weight', '100')],
            'extralight' => [decl('--tw-font-weight', '200'), decl('font-weight', '200')],
            'light' => [decl('--tw-font-weight', '300'), decl('font-weight', '300')],
            'normal' => [decl('--tw-font-weight', '400'), decl('font-weight', '400')],
            'medium' => [decl('--tw-font-weight', '500'), decl('font-weight', '500')],
            'semibold' => [decl('--tw-font-weight', '600'), decl('font-weight', '600')],
            'bold' => [decl('--tw-font-weight', '700'), decl('font-weight', '700')],
            'extrabold' => [decl('--tw-font-weight', '800'), decl('font-weight', '800')],
            'black' => [decl('--tw-font-weight', '900'), decl('font-weight', '900')],
        ],
    ]);

    // Text Decoration Line
    $builder->staticUtility('underline', [['text-decoration-line', 'underline']]);
    $builder->staticUtility('overline', [['text-decoration-line', 'overline']]);
    $builder->staticUtility('line-through', [['text-decoration-line', 'line-through']]);
    $builder->staticUtility('no-underline', [['text-decoration-line', 'none']]);

    // Text Decoration Style
    $builder->staticUtility('decoration-solid', [['text-decoration-style', 'solid']]);
    $builder->staticUtility('decoration-double', [['text-decoration-style', 'double']]);
    $builder->staticUtility('decoration-dotted', [['text-decoration-style', 'dotted']]);
    $builder->staticUtility('decoration-dashed', [['text-decoration-style', 'dashed']]);
    $builder->staticUtility('decoration-wavy', [['text-decoration-style', 'wavy']]);

    // Text Underline Offset
    $builder->functionalUtility('underline-offset', [
        'themeKeys' => ['--text-underline-offset'],
        'defaultValue' => null,
        'supportsNegative' => true,
        'handleBareValue' => function ($value) {
            if (!isPositiveInteger($value['value'])) {
                return null;
            }
            return "{$value['value']}px";
        },
        'handle' => function ($value) {
            return [decl('text-underline-offset', $value)];
        },
        'staticValues' => [
            'auto' => [decl('text-underline-offset', 'auto')],
        ],
    ]);

    // Text Transform
    $builder->staticUtility('uppercase', [['text-transform', 'uppercase']]);
    $builder->staticUtility('lowercase', [['text-transform', 'lowercase']]);
    $builder->staticUtility('capitalize', [['text-transform', 'capitalize']]);
    $builder->staticUtility('normal-case', [['text-transform', 'none']]);

    // Text Align
    $builder->staticUtility('text-left', [['text-align', 'left']]);
    $builder->staticUtility('text-center', [['text-align', 'center']]);
    $builder->staticUtility('text-right', [['text-align', 'right']]);
    $builder->staticUtility('text-justify', [['text-align', 'justify']]);
    $builder->staticUtility('text-start', [['text-align', 'start']]);
    $builder->staticUtility('text-end', [['text-align', 'end']]);

    // Text Wrap
    $builder->staticUtility('text-wrap', [['text-wrap', 'wrap']]);
    $builder->staticUtility('text-nowrap', [['text-wrap', 'nowrap']]);
    $builder->staticUtility('text-balance', [['text-wrap', 'balance']]);
    $builder->staticUtility('text-pretty', [['text-wrap', 'pretty']]);

    // Whitespace
    $builder->staticUtility('whitespace-normal', [['white-space', 'normal']]);
    $builder->staticUtility('whitespace-nowrap', [['white-space', 'nowrap']]);
    $builder->staticUtility('whitespace-pre', [['white-space', 'pre']]);
    $builder->staticUtility('whitespace-pre-line', [['white-space', 'pre-line']]);
    $builder->staticUtility('whitespace-pre-wrap', [['white-space', 'pre-wrap']]);
    $builder->staticUtility('whitespace-break-spaces', [['white-space', 'break-spaces']]);

    // Word Break
    $builder->staticUtility('break-normal', [['overflow-wrap', 'normal'], ['word-break', 'normal']]);
    $builder->staticUtility('break-words', [['overflow-wrap', 'break-word']]);
    $builder->staticUtility('break-all', [['word-break', 'break-all']]);
    $builder->staticUtility('break-keep', [['word-break', 'keep-all']]);

    // Hyphens
    $builder->staticUtility('hyphens-none', [
        ['-webkit-hyphens', 'none'],
        ['hyphens', 'none'],
    ]);
    $builder->staticUtility('hyphens-manual', [
        ['-webkit-hyphens', 'manual'],
        ['hyphens', 'manual'],
    ]);
    $builder->staticUtility('hyphens-auto', [
        ['-webkit-hyphens', 'auto'],
        ['hyphens', 'auto'],
    ]);

    // List Style Type
    $builder->functionalUtility('list', [
        'themeKeys' => ['--list-style-type'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [decl('list-style-type', $value)];
        },
        'staticValues' => [
            'none' => [decl('list-style-type', 'none')],
            'disc' => [decl('list-style-type', 'disc')],
            'decimal' => [decl('list-style-type', 'decimal')],
        ],
    ]);

    // List Style Position
    $builder->staticUtility('list-inside', [['list-style-position', 'inside']]);
    $builder->staticUtility('list-outside', [['list-style-position', 'outside']]);

    // List Style Image
    $builder->functionalUtility('list-image', [
        'themeKeys' => ['--list-style-image'],
        'handle' => function ($value) {
            return [decl('list-style-image', $value)];
        },
        'staticValues' => [
            'none' => [decl('list-style-image', 'none')],
        ],
    ]);

    // Vertical Align
    $builder->functionalUtility('align', [
        'themeKeys' => ['--vertical-align'],
        'handle' => function ($value) {
            return [decl('vertical-align', $value)];
        },
        'staticValues' => [
            'baseline' => [decl('vertical-align', 'baseline')],
            'top' => [decl('vertical-align', 'top')],
            'middle' => [decl('vertical-align', 'middle')],
            'bottom' => [decl('vertical-align', 'bottom')],
            'text-top' => [decl('vertical-align', 'text-top')],
            'text-bottom' => [decl('vertical-align', 'text-bottom')],
            'sub' => [decl('vertical-align', 'sub')],
            'super' => [decl('vertical-align', 'super')],
        ],
    ]);

    // Line Height (leading)
    $builder->functionalUtility('leading', [
        'themeKeys' => ['--leading', '--line-height'],
        'defaultValue' => null,
        'handle' => function ($value) {
            return [
                decl('--tw-leading', $value),
                decl('line-height', $value),
            ];
        },
        'staticValues' => [
            'none' => [decl('--tw-leading', '1'), decl('line-height', '1')],
            'tight' => [decl('--tw-leading', '1.25'), decl('line-height', '1.25')],
            'snug' => [decl('--tw-leading', '1.375'), decl('line-height', '1.375')],
            'normal' => [decl('--tw-leading', '1.5'), decl('line-height', '1.5')],
            'relaxed' => [decl('--tw-leading', '1.625'), decl('line-height', '1.625')],
            'loose' => [decl('--tw-leading', '2'), decl('line-height', '2')],
        ],
    ]);

    // Letter Spacing (tracking)
    $builder->functionalUtility('tracking', [
        'themeKeys' => ['--tracking', '--letter-spacing'],
        'supportsNegative' => true,
        'defaultValue' => null,
        'handle' => function ($value) {
            return [
                decl('--tw-tracking', $value),
                decl('letter-spacing', $value),
            ];
        },
        'staticValues' => [
            'tighter' => [decl('--tw-tracking', '-0.05em'), decl('letter-spacing', '-0.05em')],
            'tight' => [decl('--tw-tracking', '-0.025em'), decl('letter-spacing', '-0.025em')],
            'normal' => [decl('--tw-tracking', '0em'), decl('letter-spacing', '0em')],
            'wide' => [decl('--tw-tracking', '0.025em'), decl('letter-spacing', '0.025em')],
            'wider' => [decl('--tw-tracking', '0.05em'), decl('letter-spacing', '0.05em')],
            'widest' => [decl('--tw-tracking', '0.1em'), decl('letter-spacing', '0.1em')],
        ],
    ]);

    // Text Indent
    $builder->spacingUtility('indent', ['--text-indent', '--spacing'], function ($value) {
        return [decl('text-indent', $value)];
    }, [
        'supportsNegative' => true,
    ]);

    // Truncate - declaration order matches lightningcss output
    $builder->staticUtility('truncate', [
        ['text-overflow', 'ellipsis'],
        ['white-space', 'nowrap'],
        ['overflow', 'hidden'],
    ]);

    // Text Overflow
    $builder->staticUtility('text-ellipsis', [['text-overflow', 'ellipsis']]);
    $builder->staticUtility('text-clip', [['text-overflow', 'clip']]);

    // Font Variant Numeric
    // Uses CSS variables to compose multiple numeric features
    $numericVar = 'var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, )';

    $builder->staticUtility('normal-nums', [
        ['--tw-ordinal', 'initial'],
        ['--tw-slashed-zero', 'initial'],
        ['--tw-numeric-figure', 'initial'],
        ['--tw-numeric-spacing', 'initial'],
        ['--tw-numeric-fraction', 'initial'],
        ['font-variant-numeric', 'normal'],
    ]);
    $builder->staticUtility('ordinal', [
        ['--tw-ordinal', 'ordinal'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('slashed-zero', [
        ['--tw-slashed-zero', 'slashed-zero'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('lining-nums', [
        ['--tw-numeric-figure', 'lining-nums'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('oldstyle-nums', [
        ['--tw-numeric-figure', 'oldstyle-nums'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('proportional-nums', [
        ['--tw-numeric-spacing', 'proportional-nums'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('tabular-nums', [
        ['--tw-numeric-spacing', 'tabular-nums'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('diagonal-fractions', [
        ['--tw-numeric-fraction', 'diagonal-fractions'],
        ['font-variant-numeric', $numericVar],
    ]);
    $builder->staticUtility('stacked-fractions', [
        ['--tw-numeric-fraction', 'stacked-fractions'],
        ['font-variant-numeric', $numericVar],
    ]);

    // Font Smoothing
    $builder->staticUtility('antialiased', [
        ['-webkit-font-smoothing', 'antialiased'],
        ['-moz-osx-font-smoothing', 'grayscale'],
    ]);
    $builder->staticUtility('subpixel-antialiased', [
        ['-webkit-font-smoothing', 'auto'],
        ['-moz-osx-font-smoothing', 'auto'],
    ]);
}
