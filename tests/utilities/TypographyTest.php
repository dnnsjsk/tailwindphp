<?php

declare(strict_types=1);

namespace TailwindPHP\Tests\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

use function TailwindPHP\Utilities\registerTypographyUtilities;

/**
 * Typography Utilities Tests
 *
 * Port of typography tests from: packages/tailwindcss/src/utilities.test.ts
 */
class TypographyTest extends TestCase
{
    private TestHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestHelper();
        $this->helper->registerUtilities(function ($builder) {
            registerTypographyUtilities($builder);
        });
    }

    // =========================================================================
    // Font Style
    // =========================================================================

    #[Test]
    public function font_style(): void
    {
        $css = $this->helper->run(['italic', 'not-italic']);

        $this->assertStringContainsString('.italic {', $css);
        $this->assertStringContainsString('font-style: italic;', $css);

        $this->assertStringContainsString('.not-italic {', $css);
        $this->assertStringContainsString('font-style: normal;', $css);
    }

    #[Test]
    public function font_style_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-italic']));
        $this->assertEquals('', $this->helper->run(['-not-italic']));
        $this->assertEquals('', $this->helper->run(['italic/foo']));
        $this->assertEquals('', $this->helper->run(['not-italic/foo']));
    }

    // =========================================================================
    // Font Weight
    // =========================================================================

    #[Test]
    public function font_weight_static_values(): void
    {
        $css = $this->helper->run([
            'font-thin', 'font-extralight', 'font-light', 'font-normal',
            'font-medium', 'font-semibold', 'font-bold', 'font-extrabold', 'font-black'
        ]);

        $this->assertStringContainsString('.font-thin {', $css);
        $this->assertStringContainsString('font-weight: 100;', $css);

        $this->assertStringContainsString('.font-extralight {', $css);
        $this->assertStringContainsString('font-weight: 200;', $css);

        $this->assertStringContainsString('.font-light {', $css);
        $this->assertStringContainsString('font-weight: 300;', $css);

        $this->assertStringContainsString('.font-normal {', $css);
        $this->assertStringContainsString('font-weight: 400;', $css);

        $this->assertStringContainsString('.font-medium {', $css);
        $this->assertStringContainsString('font-weight: 500;', $css);

        $this->assertStringContainsString('.font-semibold {', $css);
        $this->assertStringContainsString('font-weight: 600;', $css);

        $this->assertStringContainsString('.font-bold {', $css);
        $this->assertStringContainsString('font-weight: 700;', $css);

        $this->assertStringContainsString('.font-extrabold {', $css);
        $this->assertStringContainsString('font-weight: 800;', $css);

        $this->assertStringContainsString('.font-black {', $css);
        $this->assertStringContainsString('font-weight: 900;', $css);
    }

    #[Test]
    public function font_weight_with_arbitrary_value(): void
    {
        $css = $this->helper->run(['font-[100]', 'font-[550]']);

        $this->assertStringContainsString('.font-\\[100\\] {', $css);
        $this->assertStringContainsString('font-weight: 100;', $css);

        $this->assertStringContainsString('.font-\\[550\\] {', $css);
        $this->assertStringContainsString('font-weight: 550;', $css);
    }

    #[Test]
    public function font_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['font']));
        $this->assertEquals('', $this->helper->run(['-font-bold']));
        $this->assertEquals('', $this->helper->run(['font-bold/foo']));
    }

    // =========================================================================
    // Text Decoration Line
    // =========================================================================

    #[Test]
    public function text_decoration_line(): void
    {
        $css = $this->helper->run(['underline', 'overline', 'line-through', 'no-underline']);

        $this->assertStringContainsString('.line-through {', $css);
        $this->assertStringContainsString('text-decoration-line: line-through;', $css);

        $this->assertStringContainsString('.no-underline {', $css);
        $this->assertStringContainsString('text-decoration-line: none;', $css);

        $this->assertStringContainsString('.overline {', $css);
        $this->assertStringContainsString('text-decoration-line: overline;', $css);

        $this->assertStringContainsString('.underline {', $css);
        $this->assertStringContainsString('text-decoration-line: underline;', $css);
    }

    #[Test]
    public function text_decoration_line_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-underline']));
        $this->assertEquals('', $this->helper->run(['-overline']));
        $this->assertEquals('', $this->helper->run(['-line-through']));
        $this->assertEquals('', $this->helper->run(['-no-underline']));
        $this->assertEquals('', $this->helper->run(['underline/foo']));
        $this->assertEquals('', $this->helper->run(['overline/foo']));
        $this->assertEquals('', $this->helper->run(['line-through/foo']));
        $this->assertEquals('', $this->helper->run(['no-underline/foo']));
    }

    // =========================================================================
    // Text Decoration Style
    // =========================================================================

    #[Test]
    public function text_decoration_style(): void
    {
        $css = $this->helper->run([
            'decoration-solid', 'decoration-double', 'decoration-dotted',
            'decoration-dashed', 'decoration-wavy'
        ]);

        $this->assertStringContainsString('.decoration-dashed {', $css);
        $this->assertStringContainsString('text-decoration-style: dashed;', $css);

        $this->assertStringContainsString('.decoration-dotted {', $css);
        $this->assertStringContainsString('text-decoration-style: dotted;', $css);

        $this->assertStringContainsString('.decoration-double {', $css);
        $this->assertStringContainsString('text-decoration-style: double;', $css);

        $this->assertStringContainsString('.decoration-solid {', $css);
        $this->assertStringContainsString('text-decoration-style: solid;', $css);

        $this->assertStringContainsString('.decoration-wavy {', $css);
        $this->assertStringContainsString('text-decoration-style: wavy;', $css);
    }

    // =========================================================================
    // Text Transform
    // =========================================================================

    #[Test]
    public function text_transform(): void
    {
        $css = $this->helper->run(['uppercase', 'lowercase', 'capitalize', 'normal-case']);

        $this->assertStringContainsString('.capitalize {', $css);
        $this->assertStringContainsString('text-transform: capitalize;', $css);

        $this->assertStringContainsString('.lowercase {', $css);
        $this->assertStringContainsString('text-transform: lowercase;', $css);

        $this->assertStringContainsString('.normal-case {', $css);
        $this->assertStringContainsString('text-transform: none;', $css);

        $this->assertStringContainsString('.uppercase {', $css);
        $this->assertStringContainsString('text-transform: uppercase;', $css);
    }

    // =========================================================================
    // Text Align
    // =========================================================================

    #[Test]
    public function text_align(): void
    {
        $css = $this->helper->run([
            'text-left', 'text-center', 'text-right', 'text-justify',
            'text-start', 'text-end'
        ]);

        $this->assertStringContainsString('.text-center {', $css);
        $this->assertStringContainsString('text-align: center;', $css);

        $this->assertStringContainsString('.text-end {', $css);
        $this->assertStringContainsString('text-align: end;', $css);

        $this->assertStringContainsString('.text-justify {', $css);
        $this->assertStringContainsString('text-align: justify;', $css);

        $this->assertStringContainsString('.text-left {', $css);
        $this->assertStringContainsString('text-align: left;', $css);

        $this->assertStringContainsString('.text-right {', $css);
        $this->assertStringContainsString('text-align: right;', $css);

        $this->assertStringContainsString('.text-start {', $css);
        $this->assertStringContainsString('text-align: start;', $css);
    }

    // =========================================================================
    // Text Wrap
    // =========================================================================

    #[Test]
    public function text_wrap(): void
    {
        $css = $this->helper->run(['text-wrap', 'text-nowrap', 'text-balance', 'text-pretty']);

        $this->assertStringContainsString('.text-balance {', $css);
        $this->assertStringContainsString('text-wrap: balance;', $css);

        $this->assertStringContainsString('.text-nowrap {', $css);
        $this->assertStringContainsString('text-wrap: nowrap;', $css);

        $this->assertStringContainsString('.text-pretty {', $css);
        $this->assertStringContainsString('text-wrap: pretty;', $css);

        $this->assertStringContainsString('.text-wrap {', $css);
        $this->assertStringContainsString('text-wrap: wrap;', $css);
    }

    // =========================================================================
    // Whitespace
    // =========================================================================

    #[Test]
    public function whitespace(): void
    {
        $css = $this->helper->run([
            'whitespace-normal', 'whitespace-nowrap', 'whitespace-pre',
            'whitespace-pre-line', 'whitespace-pre-wrap', 'whitespace-break-spaces'
        ]);

        $this->assertStringContainsString('.whitespace-break-spaces {', $css);
        $this->assertStringContainsString('white-space: break-spaces;', $css);

        $this->assertStringContainsString('.whitespace-normal {', $css);
        $this->assertStringContainsString('white-space: normal;', $css);

        $this->assertStringContainsString('.whitespace-nowrap {', $css);
        $this->assertStringContainsString('white-space: nowrap;', $css);

        $this->assertStringContainsString('.whitespace-pre {', $css);
        $this->assertStringContainsString('white-space: pre;', $css);

        $this->assertStringContainsString('.whitespace-pre-line {', $css);
        $this->assertStringContainsString('white-space: pre-line;', $css);

        $this->assertStringContainsString('.whitespace-pre-wrap {', $css);
        $this->assertStringContainsString('white-space: pre-wrap;', $css);
    }

    // =========================================================================
    // Word Break
    // =========================================================================

    #[Test]
    public function word_break(): void
    {
        $css = $this->helper->run(['break-normal', 'break-words', 'break-all', 'break-keep']);

        $this->assertStringContainsString('.break-all {', $css);
        $this->assertStringContainsString('word-break: break-all;', $css);

        $this->assertStringContainsString('.break-keep {', $css);
        $this->assertStringContainsString('word-break: keep-all;', $css);

        $this->assertStringContainsString('.break-normal {', $css);
        $this->assertStringContainsString('overflow-wrap: normal;', $css);
        $this->assertStringContainsString('word-break: normal;', $css);

        $this->assertStringContainsString('.break-words {', $css);
        $this->assertStringContainsString('overflow-wrap: break-word;', $css);
    }

    // =========================================================================
    // Hyphens
    // =========================================================================

    #[Test]
    public function hyphens(): void
    {
        $css = $this->helper->run(['hyphens-none', 'hyphens-manual', 'hyphens-auto']);

        $this->assertStringContainsString('.hyphens-auto {', $css);
        $this->assertStringContainsString('hyphens: auto;', $css);

        $this->assertStringContainsString('.hyphens-manual {', $css);
        $this->assertStringContainsString('hyphens: manual;', $css);

        $this->assertStringContainsString('.hyphens-none {', $css);
        $this->assertStringContainsString('hyphens: none;', $css);
    }

    // =========================================================================
    // List Style Type
    // =========================================================================

    #[Test]
    public function list_style_type(): void
    {
        $css = $this->helper->run(['list-none', 'list-disc', 'list-decimal']);

        $this->assertStringContainsString('.list-decimal {', $css);
        $this->assertStringContainsString('list-style-type: decimal;', $css);

        $this->assertStringContainsString('.list-disc {', $css);
        $this->assertStringContainsString('list-style-type: disc;', $css);

        $this->assertStringContainsString('.list-none {', $css);
        $this->assertStringContainsString('list-style-type: none;', $css);
    }

    #[Test]
    public function list_style_type_with_arbitrary_value(): void
    {
        $css = $this->helper->run(['list-[var(--value)]']);

        $this->assertStringContainsString('.list-\\[var\\(--value\\)\\] {', $css);
        $this->assertStringContainsString('list-style-type: var(--value);', $css);
    }

    #[Test]
    public function list_style_type_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-list-none']));
        $this->assertEquals('', $this->helper->run(['-list-disc']));
        $this->assertEquals('', $this->helper->run(['-list-decimal']));
        $this->assertEquals('', $this->helper->run(['-list-[var(--value)]']));
        $this->assertEquals('', $this->helper->run(['list-none/foo']));
        $this->assertEquals('', $this->helper->run(['list-disc/foo']));
        $this->assertEquals('', $this->helper->run(['list-decimal/foo']));
        $this->assertEquals('', $this->helper->run(['list-[var(--value)]/foo']));
    }

    // =========================================================================
    // List Style Position
    // =========================================================================

    #[Test]
    public function list_style_position(): void
    {
        $css = $this->helper->run(['list-inside', 'list-outside']);

        $this->assertStringContainsString('.list-inside {', $css);
        $this->assertStringContainsString('list-style-position: inside;', $css);

        $this->assertStringContainsString('.list-outside {', $css);
        $this->assertStringContainsString('list-style-position: outside;', $css);
    }

    // =========================================================================
    // Vertical Align
    // =========================================================================

    #[Test]
    public function vertical_align(): void
    {
        $css = $this->helper->run([
            'align-baseline', 'align-top', 'align-middle', 'align-bottom',
            'align-text-top', 'align-text-bottom', 'align-sub', 'align-super'
        ]);

        $this->assertStringContainsString('.align-baseline {', $css);
        $this->assertStringContainsString('vertical-align: baseline;', $css);

        $this->assertStringContainsString('.align-bottom {', $css);
        $this->assertStringContainsString('vertical-align: bottom;', $css);

        $this->assertStringContainsString('.align-middle {', $css);
        $this->assertStringContainsString('vertical-align: middle;', $css);

        $this->assertStringContainsString('.align-sub {', $css);
        $this->assertStringContainsString('vertical-align: sub;', $css);

        $this->assertStringContainsString('.align-super {', $css);
        $this->assertStringContainsString('vertical-align: super;', $css);

        $this->assertStringContainsString('.align-text-bottom {', $css);
        $this->assertStringContainsString('vertical-align: text-bottom;', $css);

        $this->assertStringContainsString('.align-text-top {', $css);
        $this->assertStringContainsString('vertical-align: text-top;', $css);

        $this->assertStringContainsString('.align-top {', $css);
        $this->assertStringContainsString('vertical-align: top;', $css);
    }

    // =========================================================================
    // Leading (line-height)
    // =========================================================================

    #[Test]
    public function leading_static_values(): void
    {
        $css = $this->helper->run([
            'leading-none', 'leading-tight', 'leading-snug',
            'leading-normal', 'leading-relaxed', 'leading-loose'
        ]);

        $this->assertStringContainsString('.leading-loose {', $css);
        $this->assertStringContainsString('line-height: 2;', $css);

        $this->assertStringContainsString('.leading-none {', $css);
        $this->assertStringContainsString('line-height: 1;', $css);

        $this->assertStringContainsString('.leading-normal {', $css);
        $this->assertStringContainsString('line-height: 1.5;', $css);

        $this->assertStringContainsString('.leading-relaxed {', $css);
        $this->assertStringContainsString('line-height: 1.625;', $css);

        $this->assertStringContainsString('.leading-snug {', $css);
        $this->assertStringContainsString('line-height: 1.375;', $css);

        $this->assertStringContainsString('.leading-tight {', $css);
        $this->assertStringContainsString('line-height: 1.25;', $css);
    }

    #[Test]
    public function leading_with_arbitrary_value(): void
    {
        $css = $this->helper->run(['leading-[var(--value)]']);

        $this->assertStringContainsString('.leading-\\[var\\(--value\\)\\] {', $css);
        $this->assertStringContainsString('--tw-leading: var(--value);', $css);
        $this->assertStringContainsString('line-height: var(--value);', $css);
    }

    #[Test]
    public function leading_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['leading']));
        $this->assertEquals('', $this->helper->run(['-leading-tight']));
        $this->assertEquals('', $this->helper->run(['-leading-[var(--value)]']));
        $this->assertEquals('', $this->helper->run(['leading-tight/foo']));
        $this->assertEquals('', $this->helper->run(['leading-[var(--value)]/foo']));
    }

    // =========================================================================
    // Tracking (letter-spacing)
    // =========================================================================

    #[Test]
    public function tracking_static_values(): void
    {
        $css = $this->helper->run([
            'tracking-tighter', 'tracking-tight', 'tracking-normal',
            'tracking-wide', 'tracking-wider', 'tracking-widest'
        ]);

        $this->assertStringContainsString('.tracking-normal {', $css);
        $this->assertStringContainsString('letter-spacing: 0em;', $css);

        $this->assertStringContainsString('.tracking-tight {', $css);
        $this->assertStringContainsString('letter-spacing: -0.025em;', $css);

        $this->assertStringContainsString('.tracking-tighter {', $css);
        $this->assertStringContainsString('letter-spacing: -0.05em;', $css);

        $this->assertStringContainsString('.tracking-wide {', $css);
        $this->assertStringContainsString('letter-spacing: 0.025em;', $css);

        $this->assertStringContainsString('.tracking-wider {', $css);
        $this->assertStringContainsString('letter-spacing: 0.05em;', $css);

        $this->assertStringContainsString('.tracking-widest {', $css);
        $this->assertStringContainsString('letter-spacing: 0.1em;', $css);
    }

    #[Test]
    public function tracking_with_arbitrary_value(): void
    {
        $css = $this->helper->run(['tracking-[var(--value)]']);

        $this->assertStringContainsString('.tracking-\\[var\\(--value\\)\\] {', $css);
        $this->assertStringContainsString('--tw-tracking: var(--value);', $css);
        $this->assertStringContainsString('letter-spacing: var(--value);', $css);
    }

    #[Test]
    public function tracking_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['tracking']));
        $this->assertEquals('', $this->helper->run(['tracking-normal/foo']));
        $this->assertEquals('', $this->helper->run(['tracking-wide/foo']));
        $this->assertEquals('', $this->helper->run(['tracking-[var(--value)]/foo']));
    }

    // =========================================================================
    // Text Indent
    // =========================================================================

    #[Test]
    public function text_indent(): void
    {
        $css = $this->helper->run(['indent-4', 'indent-[10px]', '-indent-4']);

        $this->assertStringContainsString('.indent-4 {', $css);
        $this->assertStringContainsString('text-indent: calc(var(--spacing) * 4);', $css);

        $this->assertStringContainsString('.indent-\\[10px\\] {', $css);
        $this->assertStringContainsString('text-indent: 10px;', $css);

        $this->assertStringContainsString('.-indent-4 {', $css);
        $this->assertStringContainsString('text-indent: calc(calc(var(--spacing) * 4) * -1);', $css);
    }

    // =========================================================================
    // Truncate
    // =========================================================================

    #[Test]
    public function truncate(): void
    {
        $css = $this->helper->run(['truncate']);

        $this->assertStringContainsString('.truncate {', $css);
        $this->assertStringContainsString('overflow: hidden;', $css);
        $this->assertStringContainsString('text-overflow: ellipsis;', $css);
        $this->assertStringContainsString('white-space: nowrap;', $css);
    }

    // =========================================================================
    // Text Overflow
    // =========================================================================

    #[Test]
    public function text_overflow(): void
    {
        $css = $this->helper->run(['text-ellipsis', 'text-clip']);

        $this->assertStringContainsString('.text-clip {', $css);
        $this->assertStringContainsString('text-overflow: clip;', $css);

        $this->assertStringContainsString('.text-ellipsis {', $css);
        $this->assertStringContainsString('text-overflow: ellipsis;', $css);
    }

    // =========================================================================
    // Font Variant Numeric
    // =========================================================================

    #[Test]
    public function font_variant_numeric(): void
    {
        $css = $this->helper->run([
            'normal-nums', 'ordinal', 'slashed-zero', 'lining-nums', 'oldstyle-nums',
            'proportional-nums', 'tabular-nums', 'diagonal-fractions', 'stacked-fractions'
        ]);

        $this->assertStringContainsString('.diagonal-fractions {', $css);
        $this->assertStringContainsString('font-variant-numeric: diagonal-fractions;', $css);

        $this->assertStringContainsString('.lining-nums {', $css);
        $this->assertStringContainsString('font-variant-numeric: lining-nums;', $css);

        $this->assertStringContainsString('.normal-nums {', $css);
        $this->assertStringContainsString('font-variant-numeric: normal;', $css);

        $this->assertStringContainsString('.oldstyle-nums {', $css);
        $this->assertStringContainsString('font-variant-numeric: oldstyle-nums;', $css);

        $this->assertStringContainsString('.ordinal {', $css);
        $this->assertStringContainsString('font-variant-numeric: ordinal;', $css);

        $this->assertStringContainsString('.proportional-nums {', $css);
        $this->assertStringContainsString('font-variant-numeric: proportional-nums;', $css);

        $this->assertStringContainsString('.slashed-zero {', $css);
        $this->assertStringContainsString('font-variant-numeric: slashed-zero;', $css);

        $this->assertStringContainsString('.stacked-fractions {', $css);
        $this->assertStringContainsString('font-variant-numeric: stacked-fractions;', $css);

        $this->assertStringContainsString('.tabular-nums {', $css);
        $this->assertStringContainsString('font-variant-numeric: tabular-nums;', $css);
    }

    // =========================================================================
    // Font Smoothing
    // =========================================================================

    #[Test]
    public function font_smoothing(): void
    {
        $css = $this->helper->run(['antialiased', 'subpixel-antialiased']);

        $this->assertStringContainsString('.antialiased {', $css);
        $this->assertStringContainsString('-webkit-font-smoothing: antialiased;', $css);
        $this->assertStringContainsString('-moz-osx-font-smoothing: grayscale;', $css);

        $this->assertStringContainsString('.subpixel-antialiased {', $css);
        $this->assertStringContainsString('-webkit-font-smoothing: auto;', $css);
        $this->assertStringContainsString('-moz-osx-font-smoothing: auto;', $css);
    }

    #[Test]
    public function font_smoothing_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-antialiased']));
        $this->assertEquals('', $this->helper->run(['-subpixel-antialiased']));
        $this->assertEquals('', $this->helper->run(['antialiased/foo']));
        $this->assertEquals('', $this->helper->run(['subpixel-antialiased/foo']));
    }
}
