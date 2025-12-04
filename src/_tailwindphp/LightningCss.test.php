<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\LightningCss\LightningCss as LightningCssOptimizer;

/**
 * Tests for LightningCss optimizations.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 * The original TailwindCSS uses the Rust lightningcss library; we test our PHP equivalent.
 */
class LightningCss extends TestCase
{
    // =========================================================================
    // normalizeWhitespace tests
    // =========================================================================

    #[Test]
    public function normalize_whitespace_collapses_multiple_spaces(): void
    {
        $this->assertSame('foo bar', LightningCssOptimizer::normalizeWhitespace('foo    bar'));
    }

    #[Test]
    public function normalize_whitespace_collapses_newlines(): void
    {
        $this->assertSame('foo bar', LightningCssOptimizer::normalizeWhitespace("foo\n  bar"));
    }

    #[Test]
    public function normalize_whitespace_removes_space_after_opening_paren(): void
    {
        $this->assertSame('calc(1 + 2)', LightningCssOptimizer::normalizeWhitespace('calc( 1 + 2)'));
    }

    #[Test]
    public function normalize_whitespace_removes_space_before_closing_paren(): void
    {
        $this->assertSame('calc(1 + 2)', LightningCssOptimizer::normalizeWhitespace('calc(1 + 2 )'));
    }

    #[Test]
    public function normalize_whitespace_preserves_empty_var_fallback(): void
    {
        $this->assertSame('var(--foo, )', LightningCssOptimizer::normalizeWhitespace('var(--foo, )'));
    }

    // =========================================================================
    // normalizeTimeValues tests
    // =========================================================================

    #[Test]
    public function normalize_time_converts_500ms_to_half_second(): void
    {
        $this->assertSame('.5s', LightningCssOptimizer::normalizeTimeValues('500ms'));
    }

    #[Test]
    public function normalize_time_converts_1000ms_to_1s(): void
    {
        $this->assertSame('1s', LightningCssOptimizer::normalizeTimeValues('1000ms'));
    }

    #[Test]
    public function normalize_time_converts_1500ms_to_1_5s(): void
    {
        $this->assertSame('1.5s', LightningCssOptimizer::normalizeTimeValues('1500ms'));
    }

    #[Test]
    public function normalize_time_converts_0ms_to_0s(): void
    {
        $this->assertSame('0s', LightningCssOptimizer::normalizeTimeValues('0ms'));
    }

    #[Test]
    public function normalize_time_converts_150ms(): void
    {
        $this->assertSame('.15s', LightningCssOptimizer::normalizeTimeValues('150ms'));
    }

    // =========================================================================
    // normalizeOpacityPercentages tests
    // =========================================================================

    #[Test]
    public function normalize_opacity_converts_0_percent(): void
    {
        $this->assertSame('0', LightningCssOptimizer::normalizeOpacityPercentages('0%', 'opacity'));
    }

    #[Test]
    public function normalize_opacity_converts_100_percent(): void
    {
        $this->assertSame('1', LightningCssOptimizer::normalizeOpacityPercentages('100%', 'opacity'));
    }

    #[Test]
    public function normalize_opacity_converts_50_percent(): void
    {
        $this->assertSame('.5', LightningCssOptimizer::normalizeOpacityPercentages('50%', 'opacity'));
    }

    #[Test]
    public function normalize_opacity_only_affects_opacity_property(): void
    {
        $this->assertSame('50%', LightningCssOptimizer::normalizeOpacityPercentages('50%', 'width'));
    }

    // =========================================================================
    // normalizeColors tests
    // =========================================================================

    #[Test]
    public function normalize_colors_converts_hex_red_to_named(): void
    {
        $this->assertSame('red', LightningCssOptimizer::normalizeColors('#f00'));
    }

    #[Test]
    public function normalize_colors_converts_long_hex_red_to_named(): void
    {
        $this->assertSame('red', LightningCssOptimizer::normalizeColors('#ff0000'));
    }

    #[Test]
    public function normalize_colors_converts_blue_to_hex(): void
    {
        $this->assertSame('#00f', LightningCssOptimizer::normalizeColors('blue'));
    }

    #[Test]
    public function normalize_colors_skips_var_references(): void
    {
        $this->assertSame('var(--blue)', LightningCssOptimizer::normalizeColors('var(--blue)'));
    }

    #[Test]
    public function normalize_colors_skips_custom_properties(): void
    {
        $this->assertSame('yellow', LightningCssOptimizer::normalizeColors('yellow', true));
    }

    // =========================================================================
    // simplifyCalcExpressions tests
    // =========================================================================

    #[Test]
    public function simplify_calc_negates_angle(): void
    {
        $this->assertSame('-45deg', LightningCssOptimizer::simplifyCalcExpressions('calc(45deg * -1)'));
    }

    #[Test]
    public function simplify_calc_negates_negative_angle(): void
    {
        $this->assertSame('90deg', LightningCssOptimizer::simplifyCalcExpressions('calc(-90deg * -1)'));
    }

    #[Test]
    public function simplify_calc_multiplies_rem(): void
    {
        $this->assertSame('1rem', LightningCssOptimizer::simplifyCalcExpressions('calc(.25rem * 4)'));
    }

    #[Test]
    public function simplify_calc_preserves_complex_expressions(): void
    {
        $expr = 'calc(100% - 1rem)';
        $this->assertSame($expr, LightningCssOptimizer::simplifyCalcExpressions($expr));
    }

    // =========================================================================
    // normalizeLeadingZeros tests
    // =========================================================================

    #[Test]
    public function normalize_leading_zeros_removes_zero(): void
    {
        $this->assertSame('.5', LightningCssOptimizer::normalizeLeadingZeros('0.5'));
    }

    #[Test]
    public function normalize_leading_zeros_in_value(): void
    {
        $this->assertSame('.5rem', LightningCssOptimizer::normalizeLeadingZeros('0.5rem'));
    }

    #[Test]
    public function normalize_leading_zeros_multiple(): void
    {
        $this->assertSame('.5 .25 .125', LightningCssOptimizer::normalizeLeadingZeros('0.5 0.25 0.125'));
    }

    // =========================================================================
    // normalizeGridValues tests
    // =========================================================================

    #[Test]
    public function normalize_grid_adds_spaces_around_slash(): void
    {
        $this->assertSame('span 1 / span 2', LightningCssOptimizer::normalizeGridValues('span 1/span 2'));
    }

    #[Test]
    public function normalize_grid_adds_px_to_bare_integer(): void
    {
        $this->assertSame('123px', LightningCssOptimizer::normalizeGridValues('123', 'grid-template-columns'));
    }

    #[Test]
    public function normalize_grid_preserves_non_grid_property(): void
    {
        $this->assertSame('123', LightningCssOptimizer::normalizeGridValues('123', 'grid-column'));
    }

    // =========================================================================
    // normalizeTransformFunctions tests
    // =========================================================================

    #[Test]
    public function normalize_transform_removes_spaces_between_functions(): void
    {
        $this->assertSame(
            'scaleZ(2)rotateY(45deg)',
            LightningCssOptimizer::normalizeTransformFunctions('scaleZ(2) rotateY(45deg)', 'transform'),
        );
    }

    #[Test]
    public function normalize_transform_preserves_var_spaces(): void
    {
        $value = 'var(--tw-rotate-x, ) var(--tw-rotate-y, )';
        $this->assertSame($value, LightningCssOptimizer::normalizeTransformFunctions($value, 'transform'));
    }

    #[Test]
    public function normalize_transform_only_affects_transform_property(): void
    {
        $value = 'scaleZ(2) rotateY(45deg)';
        $this->assertSame($value, LightningCssOptimizer::normalizeTransformFunctions($value, 'other'));
    }

    // =========================================================================
    // normalizeAnimationValue tests
    // =========================================================================

    #[Test]
    public function normalize_animation_moves_name_to_end(): void
    {
        $this->assertSame(
            '1s infinite bounce',
            LightningCssOptimizer::normalizeAnimationValue('bounce 1s infinite', 'animation'),
        );
    }

    #[Test]
    public function normalize_animation_handles_multiple(): void
    {
        $this->assertSame(
            '1s fade, 2s slide',
            LightningCssOptimizer::normalizeAnimationValue('fade 1s, slide 2s', 'animation'),
        );
    }

    #[Test]
    public function normalize_animation_skips_var(): void
    {
        $value = 'var(--animation) 1s';
        $this->assertSame($value, LightningCssOptimizer::normalizeAnimationValue($value, 'animation'));
    }

    // =========================================================================
    // normalizeUrlQuoting tests
    // =========================================================================

    #[Test]
    public function normalize_url_adds_quotes(): void
    {
        $this->assertSame('url("./file.jpg")', LightningCssOptimizer::normalizeUrlQuoting('url(./file.jpg)'));
    }

    #[Test]
    public function normalize_url_preserves_data_uri(): void
    {
        $value = 'url(data:image/png;base64,abc)';
        $this->assertSame($value, LightningCssOptimizer::normalizeUrlQuoting($value));
    }

    #[Test]
    public function normalize_url_preserves_var(): void
    {
        $value = 'url(var(--bg-image))';
        $this->assertSame($value, LightningCssOptimizer::normalizeUrlQuoting($value));
    }

    // =========================================================================
    // transformNesting tests
    // =========================================================================

    #[Test]
    public function transform_nesting_flattens_child_selector(): void
    {
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.parent',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                    [
                        'kind' => 'rule',
                        'selector' => '.child',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        $this->assertCount(2, $result);
        $this->assertSame('.parent', $result[0]['selector']);
        $this->assertSame('.parent .child', $result[1]['selector']);
    }

    #[Test]
    public function transform_nesting_resolves_ampersand(): void
    {
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.parent',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '&:hover',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'green'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        $this->assertSame('.parent:hover', $result[0]['selector']);
    }

    #[Test]
    public function transform_nesting_handles_media_query(): void
    {
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.card',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'padding', 'value' => '1rem'],
                    [
                        'kind' => 'at-rule',
                        'name' => '@media',
                        'params' => '(min-width: 768px)',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'padding', 'value' => '2rem'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        // First rule is the base .card
        $this->assertSame('.card', $result[0]['selector']);
        // Second should be the @media rule
        $this->assertSame('@media', $result[1]['name']);
        // Inside @media should have .card selector
        $this->assertSame('.card', $result[1]['nodes'][0]['selector']);
    }

    #[Test]
    public function transform_nesting_deep_nesting(): void
    {
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.a',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '.b',
                        'nodes' => [
                            [
                                'kind' => 'rule',
                                'selector' => '.c',
                                'nodes' => [
                                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        $this->assertSame('.a .b .c', $result[0]['selector']);
    }

    #[Test]
    public function transform_nesting_prefixes_all_selectors_in_list(): void
    {
        // When nesting a selector list like "h1, h2, h3" inside ".parent",
        // each selector should be prefixed: ".parent h1, .parent h2, .parent h3"
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.parent',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => 'h1, h2, h3',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        $this->assertSame('.parent h1, .parent h2, .parent h3', $result[0]['selector']);
    }

    #[Test]
    public function transform_nesting_does_not_split_commas_inside_pseudo_classes(): void
    {
        // Commas inside :where(), :not(), :is() should NOT be split
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.prose',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => ':where(a, b):not(:where(c, d))',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        // The entire selector should be prefixed as one unit, not split on inner commas
        $this->assertSame('.prose :where(a, b):not(:where(c, d))', $result[0]['selector']);
    }

    #[Test]
    public function transform_nesting_handles_complex_selector_list_with_pseudo_classes(): void
    {
        // Mix of top-level commas and commas inside pseudo-classes
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.scope',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => 'h1, :where(a, b), h2',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'margin', 'value' => '0'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        // Should split on top-level commas only
        $this->assertSame('.scope h1, .scope :where(a, b), .scope h2', $result[0]['selector']);
    }

    #[Test]
    public function transform_nesting_scoped_preflight_import(): void
    {
        // This simulates scoping preflight via nested @import
        // .editor-styles-wrapper { @import "preflight" } should scope all preflight selectors
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.editor-styles-wrapper',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '*, ::after, ::before',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'box-sizing', 'value' => 'border-box'],
                        ],
                    ],
                    [
                        'kind' => 'rule',
                        'selector' => 'h1, h2, h3, h4, h5, h6',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'font-size', 'value' => 'inherit'],
                        ],
                    ],
                ],
            ],
        ];

        $result = LightningCssOptimizer::transformNesting($ast);

        $this->assertCount(2, $result);
        $this->assertSame('.editor-styles-wrapper *, .editor-styles-wrapper ::after, .editor-styles-wrapper ::before', $result[0]['selector']);
        $this->assertSame('.editor-styles-wrapper h1, .editor-styles-wrapper h2, .editor-styles-wrapper h3, .editor-styles-wrapper h4, .editor-styles-wrapper h5, .editor-styles-wrapper h6', $result[1]['selector']);
    }

    // =========================================================================
    // addVendorPrefixes tests
    // =========================================================================

    #[Test]
    public function add_vendor_prefixes_for_user_select(): void
    {
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'user-select', 'value' => 'none'],
                ],
            ],
        ];

        $result = LightningCssOptimizer::addVendorPrefixes($ast);

        $declarations = $result[0]['nodes'];
        $properties = array_column($declarations, 'property');

        $this->assertContains('-webkit-user-select', $properties);
        $this->assertContains('-moz-user-select', $properties);
        $this->assertContains('user-select', $properties);
    }

    #[Test]
    public function add_vendor_prefixes_for_backdrop_filter(): void
    {
        $ast = [
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'backdrop-filter', 'value' => 'blur(10px)'],
                ],
            ],
        ];

        $result = LightningCssOptimizer::addVendorPrefixes($ast);

        $declarations = $result[0]['nodes'];
        $properties = array_column($declarations, 'property');

        $this->assertContains('-webkit-backdrop-filter', $properties);
        $this->assertContains('backdrop-filter', $properties);
    }

    // =========================================================================
    // evaluateColorMix tests
    // =========================================================================

    #[Test]
    public function evaluate_color_mix_red_50_percent(): void
    {
        $result = LightningCssOptimizer::evaluateColorMix('color-mix(in oklab, red 50%, transparent)');
        $this->assertStringStartsWith('oklab(', $result);
        $this->assertStringContainsString('/ .5', $result);
    }

    #[Test]
    public function evaluate_color_mix_preserves_unknown(): void
    {
        $value = 'color-mix(in oklab, unknown 50%, transparent)';
        $this->assertSame($value, LightningCssOptimizer::evaluateColorMix($value));
    }

    // =========================================================================
    // colorToOklabWithOpacity tests
    // =========================================================================

    #[Test]
    public function color_to_oklab_red(): void
    {
        $result = LightningCssOptimizer::colorToOklabWithOpacity('red', 1.0);
        $this->assertStringStartsWith('oklab(', $result);
        $this->assertStringContainsString('%', $result);
    }

    #[Test]
    public function color_to_oklab_hex(): void
    {
        $result = LightningCssOptimizer::colorToOklabWithOpacity('#ff0000', 0.5, true);
        $this->assertStringStartsWith('oklab(', $result);
        $this->assertStringContainsString('/ .5', $result);
    }

    // =========================================================================
    // colorWithAlpha tests
    // =========================================================================

    #[Test]
    public function color_with_alpha_named(): void
    {
        $result = LightningCssOptimizer::colorWithAlpha('red', 0.5);
        $this->assertSame('#ff000080', $result);
    }

    #[Test]
    public function color_with_alpha_hex(): void
    {
        $result = LightningCssOptimizer::colorWithAlpha('#00ff00', 0.5);
        $this->assertSame('#00ff0080', $result);
    }

    #[Test]
    public function color_with_alpha_short_hex(): void
    {
        $result = LightningCssOptimizer::colorWithAlpha('#0f0', 1.0);
        $this->assertSame('#00ff00ff', $result);
    }

    // =========================================================================
    // transformMediaQueryRange tests
    // =========================================================================

    #[Test]
    public function transform_media_query_gte(): void
    {
        $this->assertSame(
            '(min-width: 48rem)',
            LightningCssOptimizer::transformMediaQueryRange('(width >= 48rem)'),
        );
    }

    #[Test]
    public function transform_media_query_lte(): void
    {
        $this->assertSame(
            '(max-width: 48rem)',
            LightningCssOptimizer::transformMediaQueryRange('(width <= 48rem)'),
        );
    }

    #[Test]
    public function transform_media_query_gt(): void
    {
        $this->assertSame(
            '(min-width: 48rem)',
            LightningCssOptimizer::transformMediaQueryRange('(width > 48rem)'),
        );
    }

    #[Test]
    public function transform_media_query_lt(): void
    {
        $this->assertSame(
            '(not (min-width: 48rem))',
            LightningCssOptimizer::transformMediaQueryRange('(width < 48rem)'),
        );
    }

    // =========================================================================
    // transformContainerQueryRange tests
    // =========================================================================

    #[Test]
    public function transform_container_query_gte(): void
    {
        $this->assertSame(
            '(min-width: 48rem)',
            LightningCssOptimizer::transformContainerQueryRange('(width >= 48rem)'),
        );
    }

    #[Test]
    public function transform_container_query_gt(): void
    {
        $this->assertSame(
            'not (max-width: 48rem)',
            LightningCssOptimizer::transformContainerQueryRange('(width > 48rem)'),
        );
    }

    // =========================================================================
    // mergeRulesWithSameDeclarations tests
    // =========================================================================

    #[Test]
    public function merge_rules_with_same_declarations(): void
    {
        $nodes = [
            [
                'kind' => 'rule',
                'selector' => '.a',
                'nodes' => [['kind' => 'declaration', 'property' => 'color', 'value' => 'red']],
            ],
            [
                'kind' => 'rule',
                'selector' => '.b',
                'nodes' => [['kind' => 'declaration', 'property' => 'color', 'value' => 'red']],
            ],
        ];

        $result = LightningCssOptimizer::mergeRulesWithSameDeclarations($nodes);

        $this->assertCount(1, $result);
        $this->assertSame('.a, .b', $result[0]['selector']);
    }

    #[Test]
    public function merge_rules_keeps_different_declarations_separate(): void
    {
        $nodes = [
            [
                'kind' => 'rule',
                'selector' => '.a',
                'nodes' => [['kind' => 'declaration', 'property' => 'color', 'value' => 'red']],
            ],
            [
                'kind' => 'rule',
                'selector' => '.b',
                'nodes' => [['kind' => 'declaration', 'property' => 'color', 'value' => 'blue']],
            ],
        ];

        $result = LightningCssOptimizer::mergeRulesWithSameDeclarations($nodes);

        $this->assertCount(2, $result);
    }

    // =========================================================================
    // optimizeValue integration tests
    // =========================================================================

    #[Test]
    public function optimize_value_combines_all_optimizations(): void
    {
        // Test that multiple optimizations are applied
        $result = LightningCssOptimizer::optimizeValue('0.5rem', '');
        $this->assertSame('.5rem', $result);
    }

    #[Test]
    public function optimize_value_handles_complex_value(): void
    {
        $result = LightningCssOptimizer::optimizeValue('500ms ease-in-out', 'transition');
        $this->assertSame('.5s ease-in-out', $result);
    }

    // =========================================================================
    // minify tests
    // =========================================================================

    #[Test]
    public function minify_removes_comments(): void
    {
        $css = '.foo { /* comment */ color: red; }';
        $this->assertStringNotContainsString('comment', LightningCssOptimizer::minify($css));
    }

    #[Test]
    public function minify_removes_whitespace(): void
    {
        $css = ".foo {\n  color: red;\n}";
        $result = LightningCssOptimizer::minify($css);
        $this->assertStringNotContainsString("\n", $result);
    }

    #[Test]
    public function minify_removes_trailing_semicolons(): void
    {
        $css = '.foo { color: red; }';
        $result = LightningCssOptimizer::minify($css);
        $this->assertStringNotContainsString(';}', $result);
    }
}
