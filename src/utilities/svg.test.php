<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * SVG Utilities Tests
 *
 * Port of SVG tests from: packages/tailwindcss/src/utilities.test.ts
 */
class svg extends TestCase
{
    // =========================================================================
    // Fill
    // =========================================================================

    #[Test]
    public function fill_none(): void
    {
        $css = TestHelper::run(['fill-none']);
        $this->assertStringContainsString('.fill-none {', $css);
        $this->assertStringContainsString('fill: none;', $css);
    }

    #[Test]
    public function fill_color_values(): void
    {
        $css = TestHelper::run(['fill-inherit']);
        $this->assertStringContainsString('.fill-inherit {', $css);
        $this->assertStringContainsString('fill: inherit;', $css);

        $css = TestHelper::run(['fill-current']);
        $this->assertStringContainsString('.fill-current', $css);
        $this->assertStringContainsString('fill: currentcolor;', $css);

        $css = TestHelper::run(['fill-transparent']);
        $this->assertStringContainsString('.fill-transparent {', $css);
        $this->assertStringContainsString('fill:', $css);
    }

    #[Test]
    public function fill_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-fill-red-500']));
        $this->assertEquals('', TestHelper::run(['-fill-current']));
        $this->assertEquals('', TestHelper::run(['-fill-inherit']));
    }

    // =========================================================================
    // Stroke
    // =========================================================================

    #[Test]
    public function stroke_none(): void
    {
        $css = TestHelper::run(['stroke-none']);
        $this->assertStringContainsString('.stroke-none {', $css);
        $this->assertStringContainsString('stroke: none;', $css);
    }

    #[Test]
    public function stroke_color_values(): void
    {
        $css = TestHelper::run(['stroke-inherit']);
        $this->assertStringContainsString('.stroke-inherit {', $css);
        $this->assertStringContainsString('stroke: inherit;', $css);

        $css = TestHelper::run(['stroke-current']);
        $this->assertStringContainsString('.stroke-current', $css);
        $this->assertStringContainsString('stroke: currentcolor;', $css);

        $css = TestHelper::run(['stroke-transparent']);
        $this->assertStringContainsString('.stroke-transparent {', $css);
        $this->assertStringContainsString('stroke:', $css);
    }

    #[Test]
    public function stroke_width_values(): void
    {
        $css = TestHelper::run(['stroke-0']);
        $this->assertStringContainsString('.stroke-0 {', $css);
        $this->assertStringContainsString('stroke-width: 0;', $css);

        $css = TestHelper::run(['stroke-1']);
        $this->assertStringContainsString('.stroke-1 {', $css);
        $this->assertStringContainsString('stroke-width: 1px;', $css);

        $css = TestHelper::run(['stroke-2']);
        $this->assertStringContainsString('.stroke-2 {', $css);
        $this->assertStringContainsString('stroke-width: 2px;', $css);
    }

    #[Test]
    public function stroke_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-stroke-red-500']));
        $this->assertEquals('', TestHelper::run(['-stroke-current']));
        $this->assertEquals('', TestHelper::run(['-stroke-0']));
    }
}
