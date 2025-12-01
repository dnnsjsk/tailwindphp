<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Spacing Utilities Tests
 *
 * Port of spacing tests from: packages/tailwindcss/src/utilities.test.ts
 *
 * Includes:
 * - margin (m, mx, my, mt, mr, mb, ml, ms, me)
 * - padding (p, px, py, pt, pr, pb, pl, ps, pe)
 * - space-x, space-y
 */
class spacing extends TestCase
{
    // =========================================================================
    // Margin utilities
    // =========================================================================

    #[Test]
    public function margin_with_spacing_multiplier(): void
    {
        $css = TestHelper::run(['m-4', 'm-1', 'm-99']);

        // When --spacing-1 exists in theme, use var(--spacing-1)
        $this->assertStringContainsString('.m-1 {', $css);
        $this->assertStringContainsString('margin: var(--spacing-1);', $css);

        // When --spacing-4 exists in theme, use var(--spacing-4)
        $this->assertStringContainsString('.m-4 {', $css);
        $this->assertStringContainsString('margin: var(--spacing-4);', $css);

        // When --spacing-99 doesn't exist, fall back to calc()
        $this->assertStringContainsString('.m-99 {', $css);
        $this->assertStringContainsString('margin: calc(var(--spacing) * 99);', $css);
    }

    #[Test]
    public function margin_with_arbitrary_value(): void
    {
        $css = TestHelper::run(['m-[4px]']);

        $this->assertStringContainsString('.m-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin: 4px;', $css);
    }

    #[Test]
    public function margin_auto(): void
    {
        $css = TestHelper::run(['m-auto']);

        $this->assertStringContainsString('.m-auto {', $css);
        $this->assertStringContainsString('margin: auto;', $css);
    }

    #[Test]
    public function margin_negative(): void
    {
        $css = TestHelper::run(['-m-4']);

        $this->assertStringContainsString('.-m-4 {', $css);
        // When --spacing-4 exists, negative is calc(var(--spacing-4) * -1)
        $this->assertStringContainsString('margin: calc(var(--spacing-4) * -1);', $css);
    }

    #[Test]
    public function margin_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['m']));
        $this->assertEquals('', TestHelper::run(['m-4/foo']));
        $this->assertEquals('', TestHelper::run(['m-[4px]/foo']));
    }

    #[Test]
    public function mx_margin_inline(): void
    {
        $css = TestHelper::run(['mx-4', 'mx-[4px]', 'mx-auto']);

        $this->assertStringContainsString('.mx-4 {', $css);
        $this->assertStringContainsString('margin-inline: var(--spacing-4);', $css);

        $this->assertStringContainsString('.mx-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-inline: 4px;', $css);

        $this->assertStringContainsString('.mx-auto {', $css);
        $this->assertStringContainsString('margin-inline: auto;', $css);
    }

    #[Test]
    public function my_margin_block(): void
    {
        $css = TestHelper::run(['my-4', 'my-[4px]', 'my-auto']);

        $this->assertStringContainsString('.my-4 {', $css);
        $this->assertStringContainsString('margin-block: var(--spacing-4);', $css);

        $this->assertStringContainsString('.my-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-block: 4px;', $css);

        $this->assertStringContainsString('.my-auto {', $css);
        $this->assertStringContainsString('margin-block: auto;', $css);
    }

    #[Test]
    public function mt_margin_top(): void
    {
        $css = TestHelper::run(['mt-4', 'mt-[4px]', 'mt-auto']);

        $this->assertStringContainsString('.mt-4 {', $css);
        $this->assertStringContainsString('margin-top: var(--spacing-4);', $css);

        $this->assertStringContainsString('.mt-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-top: 4px;', $css);

        $this->assertStringContainsString('.mt-auto {', $css);
        $this->assertStringContainsString('margin-top: auto;', $css);
    }

    #[Test]
    public function mr_margin_right(): void
    {
        $css = TestHelper::run(['mr-4', 'mr-[4px]', 'mr-auto']);

        $this->assertStringContainsString('.mr-4 {', $css);
        $this->assertStringContainsString('margin-right: var(--spacing-4);', $css);

        $this->assertStringContainsString('.mr-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-right: 4px;', $css);

        $this->assertStringContainsString('.mr-auto {', $css);
        $this->assertStringContainsString('margin-right: auto;', $css);
    }

    #[Test]
    public function mb_margin_bottom(): void
    {
        $css = TestHelper::run(['mb-4', 'mb-[4px]', 'mb-auto']);

        $this->assertStringContainsString('.mb-4 {', $css);
        $this->assertStringContainsString('margin-bottom: var(--spacing-4);', $css);

        $this->assertStringContainsString('.mb-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-bottom: 4px;', $css);

        $this->assertStringContainsString('.mb-auto {', $css);
        $this->assertStringContainsString('margin-bottom: auto;', $css);
    }

    #[Test]
    public function ml_margin_left(): void
    {
        $css = TestHelper::run(['ml-4', 'ml-[4px]', 'ml-auto']);

        $this->assertStringContainsString('.ml-4 {', $css);
        $this->assertStringContainsString('margin-left: var(--spacing-4);', $css);

        $this->assertStringContainsString('.ml-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-left: 4px;', $css);

        $this->assertStringContainsString('.ml-auto {', $css);
        $this->assertStringContainsString('margin-left: auto;', $css);
    }

    #[Test]
    public function ms_margin_inline_start(): void
    {
        $css = TestHelper::run(['ms-4', 'ms-[4px]', 'ms-auto']);

        $this->assertStringContainsString('.ms-4 {', $css);
        $this->assertStringContainsString('margin-inline-start: var(--spacing-4);', $css);

        $this->assertStringContainsString('.ms-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-inline-start: 4px;', $css);

        $this->assertStringContainsString('.ms-auto {', $css);
        $this->assertStringContainsString('margin-inline-start: auto;', $css);
    }

    #[Test]
    public function me_margin_inline_end(): void
    {
        $css = TestHelper::run(['me-4', 'me-[4px]', 'me-auto']);

        $this->assertStringContainsString('.me-4 {', $css);
        $this->assertStringContainsString('margin-inline-end: var(--spacing-4);', $css);

        $this->assertStringContainsString('.me-\\[4px\\] {', $css);
        $this->assertStringContainsString('margin-inline-end: 4px;', $css);

        $this->assertStringContainsString('.me-auto {', $css);
        $this->assertStringContainsString('margin-inline-end: auto;', $css);
    }

    // =========================================================================
    // Padding utilities
    // =========================================================================

    #[Test]
    public function padding_with_spacing_multiplier(): void
    {
        $css = TestHelper::run(['p-4', 'p-1', 'p-99']);

        $this->assertStringContainsString('.p-1 {', $css);
        $this->assertStringContainsString('padding: var(--spacing-1);', $css);

        $this->assertStringContainsString('.p-4 {', $css);
        $this->assertStringContainsString('padding: var(--spacing-4);', $css);

        $this->assertStringContainsString('.p-99 {', $css);
        $this->assertStringContainsString('padding: calc(var(--spacing) * 99);', $css);
    }

    #[Test]
    public function padding_with_arbitrary_value(): void
    {
        $css = TestHelper::run(['p-[4px]']);

        $this->assertStringContainsString('.p-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding: 4px;', $css);
    }

    #[Test]
    public function padding_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['p']));
        $this->assertEquals('', TestHelper::run(['-p-4']));
        $this->assertEquals('', TestHelper::run(['-p-[4px]']));
        $this->assertEquals('', TestHelper::run(['p-4/foo']));
        $this->assertEquals('', TestHelper::run(['p-[4px]/foo']));
    }

    #[Test]
    public function px_padding_inline(): void
    {
        $css = TestHelper::run(['px-4', 'px-[4px]']);

        $this->assertStringContainsString('.px-4 {', $css);
        $this->assertStringContainsString('padding-inline: var(--spacing-4);', $css);

        $this->assertStringContainsString('.px-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-inline: 4px;', $css);
    }

    #[Test]
    public function py_padding_block(): void
    {
        $css = TestHelper::run(['py-4', 'py-[4px]']);

        $this->assertStringContainsString('.py-4 {', $css);
        $this->assertStringContainsString('padding-block: var(--spacing-4);', $css);

        $this->assertStringContainsString('.py-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-block: 4px;', $css);
    }

    #[Test]
    public function pt_padding_top(): void
    {
        $css = TestHelper::run(['pt-4', 'pt-[4px]']);

        $this->assertStringContainsString('.pt-4 {', $css);
        $this->assertStringContainsString('padding-top: var(--spacing-4);', $css);

        $this->assertStringContainsString('.pt-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-top: 4px;', $css);
    }

    #[Test]
    public function pr_padding_right(): void
    {
        $css = TestHelper::run(['pr-4', 'pr-[4px]']);

        $this->assertStringContainsString('.pr-4 {', $css);
        $this->assertStringContainsString('padding-right: var(--spacing-4);', $css);

        $this->assertStringContainsString('.pr-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-right: 4px;', $css);
    }

    #[Test]
    public function pb_padding_bottom(): void
    {
        $css = TestHelper::run(['pb-4', 'pb-[4px]']);

        $this->assertStringContainsString('.pb-4 {', $css);
        $this->assertStringContainsString('padding-bottom: var(--spacing-4);', $css);

        $this->assertStringContainsString('.pb-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-bottom: 4px;', $css);
    }

    #[Test]
    public function pl_padding_left(): void
    {
        $css = TestHelper::run(['pl-4', 'pl-[4px]']);

        $this->assertStringContainsString('.pl-4 {', $css);
        $this->assertStringContainsString('padding-left: var(--spacing-4);', $css);

        $this->assertStringContainsString('.pl-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-left: 4px;', $css);
    }

    #[Test]
    public function ps_padding_inline_start(): void
    {
        $css = TestHelper::run(['ps-4', 'ps-[4px]']);

        $this->assertStringContainsString('.ps-4 {', $css);
        $this->assertStringContainsString('padding-inline-start: var(--spacing-4);', $css);

        $this->assertStringContainsString('.ps-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-inline-start: 4px;', $css);
    }

    #[Test]
    public function pe_padding_inline_end(): void
    {
        $css = TestHelper::run(['pe-4', 'pe-[4px]']);

        $this->assertStringContainsString('.pe-4 {', $css);
        $this->assertStringContainsString('padding-inline-end: var(--spacing-4);', $css);

        $this->assertStringContainsString('.pe-\\[4px\\] {', $css);
        $this->assertStringContainsString('padding-inline-end: 4px;', $css);
    }

    // =========================================================================
    // Space Between utilities
    // =========================================================================

    #[Test]
    public function space_x_reverse(): void
    {
        $css = TestHelper::run(['space-x-reverse']);

        $this->assertStringContainsString('.space-x-reverse {', $css);
        $this->assertStringContainsString('--tw-space-x-reverse: 1;', $css);
    }

    #[Test]
    public function space_y_reverse(): void
    {
        $css = TestHelper::run(['space-y-reverse']);

        $this->assertStringContainsString('.space-y-reverse {', $css);
        $this->assertStringContainsString('--tw-space-y-reverse: 1;', $css);
    }

    #[Test]
    public function space_reverse_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-space-x-reverse']));
        $this->assertEquals('', TestHelper::run(['-space-y-reverse']));
        $this->assertEquals('', TestHelper::run(['space-x-reverse/foo']));
        $this->assertEquals('', TestHelper::run(['space-y-reverse/foo']));
    }

    // Note: space-x-* and space-y-* with selectors require variant/selector support
    // which is not yet fully implemented in the test helper.
    // The following tests are simplified versions:

    #[Test]
    public function space_x_with_value(): void
    {
        $css = TestHelper::run(['space-x-4']);

        $this->assertStringContainsString('space-x-4', $css);
        // The actual output should use the :where(& > :not(:last-child)) selector
        // but our simplified test helper doesn't support this yet
        $this->assertStringContainsString('--tw-space-x-reverse', $css);
        $this->assertStringContainsString('margin-inline-end', $css);
        $this->assertStringContainsString('margin-inline-start', $css);
    }

    #[Test]
    public function space_y_with_value(): void
    {
        $css = TestHelper::run(['space-y-4']);

        $this->assertStringContainsString('space-y-4', $css);
        $this->assertStringContainsString('--tw-space-y-reverse', $css);
        $this->assertStringContainsString('margin-block-end', $css);
        $this->assertStringContainsString('margin-block-start', $css);
    }

    #[Test]
    public function space_with_arbitrary_value(): void
    {
        $css = TestHelper::run(['space-x-[4px]', 'space-y-[4px]']);

        $this->assertStringContainsString('space-x-\\[4px\\]', $css);
        $this->assertStringContainsString('space-y-\\[4px\\]', $css);
    }

    #[Test]
    public function space_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['space-x']));
        $this->assertEquals('', TestHelper::run(['space-y']));
        $this->assertEquals('', TestHelper::run(['space-x-4/foo']));
        $this->assertEquals('', TestHelper::run(['space-y-4/foo']));
    }
}
