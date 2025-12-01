<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Tables Utilities Tests
 *
 * Port of table tests from: packages/tailwindcss/src/utilities.test.ts
 */
class tables extends TestCase
{
    // =========================================================================
    // Table Layout
    // =========================================================================

    #[Test]
    public function table_layout(): void
    {
        $css = TestHelper::run(['table-auto']);
        $this->assertStringContainsString('.table-auto {', $css);
        $this->assertStringContainsString('table-layout: auto;', $css);

        $css = TestHelper::run(['table-fixed']);
        $this->assertStringContainsString('.table-fixed {', $css);
        $this->assertStringContainsString('table-layout: fixed;', $css);
    }

    #[Test]
    public function table_layout_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-table-auto']));
        $this->assertEquals('', TestHelper::run(['-table-fixed']));
        $this->assertEquals('', TestHelper::run(['table-auto/foo']));
        $this->assertEquals('', TestHelper::run(['table-fixed/foo']));
    }

    // =========================================================================
    // Caption Side
    // =========================================================================

    #[Test]
    public function caption_side(): void
    {
        $css = TestHelper::run(['caption-top']);
        $this->assertStringContainsString('.caption-top {', $css);
        $this->assertStringContainsString('caption-side: top;', $css);

        $css = TestHelper::run(['caption-bottom']);
        $this->assertStringContainsString('.caption-bottom {', $css);
        $this->assertStringContainsString('caption-side: bottom;', $css);
    }

    #[Test]
    public function caption_side_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-caption-top']));
        $this->assertEquals('', TestHelper::run(['-caption-bottom']));
        $this->assertEquals('', TestHelper::run(['caption-top/foo']));
        $this->assertEquals('', TestHelper::run(['caption-bottom/foo']));
    }

    // =========================================================================
    // Border Collapse
    // =========================================================================

    #[Test]
    public function border_collapse(): void
    {
        $css = TestHelper::run(['border-collapse']);
        $this->assertStringContainsString('.border-collapse {', $css);
        $this->assertStringContainsString('border-collapse: collapse;', $css);

        $css = TestHelper::run(['border-separate']);
        $this->assertStringContainsString('.border-separate {', $css);
        $this->assertStringContainsString('border-collapse: separate;', $css);
    }

    #[Test]
    public function border_collapse_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-border-collapse']));
        $this->assertEquals('', TestHelper::run(['-border-separate']));
        $this->assertEquals('', TestHelper::run(['border-collapse/foo']));
        $this->assertEquals('', TestHelper::run(['border-separate/foo']));
    }

    // =========================================================================
    // Border Spacing
    // =========================================================================

    #[Test]
    public function border_spacing(): void
    {
        $css = TestHelper::run(['border-spacing-1']);
        $this->assertStringContainsString('.border-spacing-1 {', $css);
        $this->assertStringContainsString('--tw-border-spacing-x: var(--spacing-1);', $css);
        $this->assertStringContainsString('--tw-border-spacing-y: var(--spacing-1);', $css);
        $this->assertStringContainsString('border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);', $css);
    }

    #[Test]
    public function border_spacing_arbitrary(): void
    {
        $css = TestHelper::run(['border-spacing-[123px]']);
        $this->assertStringContainsString('border-spacing-\\[123px\\]', $css);
        $this->assertStringContainsString('--tw-border-spacing-x: 123px;', $css);
        $this->assertStringContainsString('--tw-border-spacing-y: 123px;', $css);
    }

    #[Test]
    public function border_spacing_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['border-spacing']));
        $this->assertEquals('', TestHelper::run(['-border-spacing-1']));
        $this->assertEquals('', TestHelper::run(['border-spacing-1/foo']));
    }

    #[Test]
    public function border_spacing_x(): void
    {
        $css = TestHelper::run(['border-spacing-x-1']);
        $this->assertStringContainsString('.border-spacing-x-1 {', $css);
        $this->assertStringContainsString('--tw-border-spacing-x: var(--spacing-1);', $css);
        $this->assertStringContainsString('border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);', $css);
    }

    #[Test]
    public function border_spacing_x_arbitrary(): void
    {
        $css = TestHelper::run(['border-spacing-x-[123px]']);
        $this->assertStringContainsString('border-spacing-x-\\[123px\\]', $css);
        $this->assertStringContainsString('--tw-border-spacing-x: 123px;', $css);
    }

    #[Test]
    public function border_spacing_y(): void
    {
        $css = TestHelper::run(['border-spacing-y-1']);
        $this->assertStringContainsString('.border-spacing-y-1 {', $css);
        $this->assertStringContainsString('--tw-border-spacing-y: var(--spacing-1);', $css);
        $this->assertStringContainsString('border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);', $css);
    }

    #[Test]
    public function border_spacing_y_arbitrary(): void
    {
        $css = TestHelper::run(['border-spacing-y-[123px]']);
        $this->assertStringContainsString('border-spacing-y-\\[123px\\]', $css);
        $this->assertStringContainsString('--tw-border-spacing-y: 123px;', $css);
    }
}
