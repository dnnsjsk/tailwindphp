<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;


/**
 * Typography Utilities Tests
 *
 * Port of typography tests from: packages/tailwindcss/src/utilities.test.ts
 */
class typography extends TestCase
{
    // =========================================================================
    // Font Style
    // =========================================================================

    #[Test]
    public function font_style(): void
    {
        $css = TestHelper::run(['italic', 'not-italic']);

        $this->assertStringContainsString('.italic {', $css);
        $this->assertStringContainsString('font-style: italic;', $css);

        $this->assertStringContainsString('.not-italic {', $css);
        $this->assertStringContainsString('font-style: normal;', $css);
    }

    #[Test]
    public function font_style_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-italic']));
        $this->assertEquals('', TestHelper::run(['-not-italic']));
        $this->assertEquals('', TestHelper::run(['italic/foo']));
        $this->assertEquals('', TestHelper::run(['not-italic/foo']));
    }

    // =========================================================================
    // Font Weight
    // =========================================================================

    #[Test]
    public function font_weight_static_values(): void
    {
        $css = TestHelper::run([
            'font-thin', 'font-extralight', 'font-light', 'font-normal',
            'font-medium', 'font-semibold', 'font-bold', 'font-extrabold', 'font-black'
        ]);

        // TailwindCSS 4.0 uses CSS variables for theme values
        $this->assertStringContainsString('.font-thin {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-thin);', $css);

        $this->assertStringContainsString('.font-extralight {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-extralight);', $css);

        $this->assertStringContainsString('.font-light {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-light);', $css);

        $this->assertStringContainsString('.font-normal {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-normal);', $css);

        $this->assertStringContainsString('.font-medium {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-medium);', $css);

        $this->assertStringContainsString('.font-semibold {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-semibold);', $css);

        $this->assertStringContainsString('.font-bold {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-bold);', $css);

        $this->assertStringContainsString('.font-extrabold {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-extrabold);', $css);

        $this->assertStringContainsString('.font-black {', $css);
        $this->assertStringContainsString('font-weight: var(--font-weight-black);', $css);
    }

    #[Test]
    public function font_weight_with_arbitrary_value(): void
    {
        $css = TestHelper::run(['font-[100]', 'font-[550]']);

        $this->assertStringContainsString('.font-\\[100\\] {', $css);
        $this->assertStringContainsString('font-weight: 100;', $css);

        $this->assertStringContainsString('.font-\\[550\\] {', $css);
        $this->assertStringContainsString('font-weight: 550;', $css);
    }

    #[Test]
    public function font_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['font']));
        $this->assertEquals('', TestHelper::run(['-font-bold']));
        $this->assertEquals('', TestHelper::run(['font-bold/foo']));
    }

    // =========================================================================
    // Text Decoration Line
    // =========================================================================

    #[Test]
    public function text_decoration_line(): void
    {
        $css = TestHelper::run(['underline', 'overline', 'line-through', 'no-underline']);

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
        $this->assertEquals('', TestHelper::run(['-underline']));
        $this->assertEquals('', TestHelper::run(['-overline']));
        $this->assertEquals('', TestHelper::run(['-line-through']));
        $this->assertEquals('', TestHelper::run(['-no-underline']));
        $this->assertEquals('', TestHelper::run(['underline/foo']));
        $this->assertEquals('', TestHelper::run(['overline/foo']));
        $this->assertEquals('', TestHelper::run(['line-through/foo']));
        $this->assertEquals('', TestHelper::run(['no-underline/foo']));
    }

    // =========================================================================
    // Text Decoration Style
    // =========================================================================

    #[Test]
    public function text_decoration_style(): void
    {
        $css = TestHelper::run([
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
        $css = TestHelper::run(['uppercase', 'lowercase', 'capitalize', 'normal-case']);

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
        $css = TestHelper::run([
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
        $css = TestHelper::run(['text-wrap', 'text-nowrap', 'text-balance', 'text-pretty']);

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
        $css = TestHelper::run([
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
        $css = TestHelper::run(['break-normal', 'break-words', 'break-all', 'break-keep']);

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
        $css = TestHelper::run(['hyphens-none', 'hyphens-manual', 'hyphens-auto']);

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
        $css = TestHelper::run(['list-none', 'list-disc', 'list-decimal']);

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
        $css = TestHelper::run(['list-[var(--value)]']);

        $this->assertStringContainsString('.list-\\[var\\(--value\\)\\] {', $css);
        $this->assertStringContainsString('list-style-type: var(--value);', $css);
    }

    #[Test]
    public function list_style_type_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-list-none']));
        $this->assertEquals('', TestHelper::run(['-list-disc']));
        $this->assertEquals('', TestHelper::run(['-list-decimal']));
        $this->assertEquals('', TestHelper::run(['-list-[var(--value)]']));
        $this->assertEquals('', TestHelper::run(['list-none/foo']));
        $this->assertEquals('', TestHelper::run(['list-disc/foo']));
        $this->assertEquals('', TestHelper::run(['list-decimal/foo']));
        $this->assertEquals('', TestHelper::run(['list-[var(--value)]/foo']));
    }

    // =========================================================================
    // List Style Position
    // =========================================================================

    #[Test]
    public function list_style_position(): void
    {
        $css = TestHelper::run(['list-inside', 'list-outside']);

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
        $css = TestHelper::run([
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
        $css = TestHelper::run([
            'leading-none', 'leading-tight', 'leading-snug',
            'leading-normal', 'leading-relaxed', 'leading-loose'
        ]);

        // TailwindCSS 4.0 uses CSS variables for theme values
        $this->assertStringContainsString('.leading-loose {', $css);
        $this->assertStringContainsString('line-height: var(--line-height-loose);', $css);

        $this->assertStringContainsString('.leading-none {', $css);
        $this->assertStringContainsString('line-height: var(--line-height-none);', $css);

        $this->assertStringContainsString('.leading-normal {', $css);
        $this->assertStringContainsString('line-height: var(--line-height-normal);', $css);

        $this->assertStringContainsString('.leading-relaxed {', $css);
        $this->assertStringContainsString('line-height: var(--line-height-relaxed);', $css);

        $this->assertStringContainsString('.leading-snug {', $css);
        $this->assertStringContainsString('line-height: var(--line-height-snug);', $css);

        $this->assertStringContainsString('.leading-tight {', $css);
        $this->assertStringContainsString('line-height: var(--line-height-tight);', $css);
    }

    #[Test]
    public function leading_with_arbitrary_value(): void
    {
        $css = TestHelper::run(['leading-[var(--value)]']);

        $this->assertStringContainsString('.leading-\\[var\\(--value\\)\\] {', $css);
        $this->assertStringContainsString('--tw-leading: var(--value);', $css);
        $this->assertStringContainsString('line-height: var(--value);', $css);
    }

    #[Test]
    public function leading_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['leading']));
        $this->assertEquals('', TestHelper::run(['-leading-tight']));
        $this->assertEquals('', TestHelper::run(['-leading-[var(--value)]']));
        $this->assertEquals('', TestHelper::run(['leading-tight/foo']));
        $this->assertEquals('', TestHelper::run(['leading-[var(--value)]/foo']));
    }

    // =========================================================================
    // Tracking (letter-spacing)
    // =========================================================================

    #[Test]
    public function tracking_static_values(): void
    {
        $css = TestHelper::run([
            'tracking-tighter', 'tracking-tight', 'tracking-normal',
            'tracking-wide', 'tracking-wider', 'tracking-widest'
        ]);

        // TailwindCSS 4.0 uses CSS variables for theme values
        $this->assertStringContainsString('.tracking-normal {', $css);
        $this->assertStringContainsString('letter-spacing: var(--letter-spacing-normal);', $css);

        $this->assertStringContainsString('.tracking-tight {', $css);
        $this->assertStringContainsString('letter-spacing: var(--letter-spacing-tight);', $css);

        $this->assertStringContainsString('.tracking-tighter {', $css);
        $this->assertStringContainsString('letter-spacing: var(--letter-spacing-tighter);', $css);

        $this->assertStringContainsString('.tracking-wide {', $css);
        $this->assertStringContainsString('letter-spacing: var(--letter-spacing-wide);', $css);

        $this->assertStringContainsString('.tracking-wider {', $css);
        $this->assertStringContainsString('letter-spacing: var(--letter-spacing-wider);', $css);

        $this->assertStringContainsString('.tracking-widest {', $css);
        $this->assertStringContainsString('letter-spacing: var(--letter-spacing-widest);', $css);
    }

    #[Test]
    public function tracking_with_arbitrary_value(): void
    {
        $css = TestHelper::run(['tracking-[var(--value)]']);

        $this->assertStringContainsString('.tracking-\\[var\\(--value\\)\\] {', $css);
        $this->assertStringContainsString('--tw-tracking: var(--value);', $css);
        $this->assertStringContainsString('letter-spacing: var(--value);', $css);
    }

    #[Test]
    public function tracking_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['tracking']));
        $this->assertEquals('', TestHelper::run(['tracking-normal/foo']));
        $this->assertEquals('', TestHelper::run(['tracking-wide/foo']));
        $this->assertEquals('', TestHelper::run(['tracking-[var(--value)]/foo']));
    }

    // =========================================================================
    // Text Indent
    // =========================================================================

    #[Test]
    public function text_indent(): void
    {
        $css = TestHelper::run(['indent-4', 'indent-[10px]', '-indent-4']);

        // When --spacing-4 is defined in theme, use var(--spacing-4)
        $this->assertStringContainsString('.indent-4 {', $css);
        $this->assertStringContainsString('text-indent: var(--spacing-4);', $css);

        $this->assertStringContainsString('.indent-\\[10px\\] {', $css);
        $this->assertStringContainsString('text-indent: 10px;', $css);

        $this->assertStringContainsString('.-indent-4 {', $css);
        $this->assertStringContainsString('text-indent: calc(var(--spacing-4) * -1);', $css);
    }

    // =========================================================================
    // Truncate
    // =========================================================================

    #[Test]
    public function truncate(): void
    {
        $css = TestHelper::run(['truncate']);

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
        $css = TestHelper::run(['text-ellipsis', 'text-clip']);

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
        $css = TestHelper::run([
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
        $css = TestHelper::run(['antialiased', 'subpixel-antialiased']);

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
        $this->assertEquals('', TestHelper::run(['-antialiased']));
        $this->assertEquals('', TestHelper::run(['-subpixel-antialiased']));
        $this->assertEquals('', TestHelper::run(['antialiased/foo']));
        $this->assertEquals('', TestHelper::run(['subpixel-antialiased/foo']));
    }
}
