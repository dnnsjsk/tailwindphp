<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Transforms Utilities Tests
 *
 * Port of transform tests from: packages/tailwindcss/src/utilities.test.ts
 */
class transforms extends TestCase
{
    // =========================================================================
    // Transform Origin
    // =========================================================================

    #[Test]
    public function origin_static_values(): void
    {
        $css = TestHelper::run(['origin-center']);
        $this->assertStringContainsString('.origin-center {', $css);
        $this->assertStringContainsString('transform-origin: center;', $css);

        $css = TestHelper::run(['origin-top']);
        $this->assertStringContainsString('.origin-top {', $css);
        $this->assertStringContainsString('transform-origin: top;', $css);

        $css = TestHelper::run(['origin-top-right']);
        $this->assertStringContainsString('.origin-top-right {', $css);
        $this->assertStringContainsString('transform-origin: 100% 0;', $css);

        $css = TestHelper::run(['origin-right']);
        $this->assertStringContainsString('.origin-right {', $css);
        $this->assertStringContainsString('transform-origin: 100%;', $css);

        $css = TestHelper::run(['origin-bottom-right']);
        $this->assertStringContainsString('.origin-bottom-right {', $css);
        $this->assertStringContainsString('transform-origin: 100% 100%;', $css);

        $css = TestHelper::run(['origin-bottom']);
        $this->assertStringContainsString('.origin-bottom {', $css);
        $this->assertStringContainsString('transform-origin: bottom;', $css);

        $css = TestHelper::run(['origin-bottom-left']);
        $this->assertStringContainsString('.origin-bottom-left {', $css);
        $this->assertStringContainsString('transform-origin: 0 100%;', $css);

        $css = TestHelper::run(['origin-left']);
        $this->assertStringContainsString('.origin-left {', $css);
        $this->assertStringContainsString('transform-origin: 0;', $css);

        $css = TestHelper::run(['origin-top-left']);
        $this->assertStringContainsString('.origin-top-left {', $css);
        $this->assertStringContainsString('transform-origin: 0 0;', $css);
    }

    #[Test]
    public function origin_arbitrary(): void
    {
        $css = TestHelper::run(['origin-[50px_100px]']);
        $this->assertStringContainsString('origin-\\[50px_100px\\]', $css);
        $this->assertStringContainsString('transform-origin: 50px 100px;', $css);

        $css = TestHelper::run(['origin-[var(--value)]']);
        $this->assertStringContainsString('origin-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('transform-origin: var(--value);', $css);
    }

    #[Test]
    public function origin_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-origin-center']));
        $this->assertEquals('', TestHelper::run(['origin-center/foo']));
    }

    // =========================================================================
    // Translate
    // =========================================================================

    #[Test]
    public function translate_full(): void
    {
        $css = TestHelper::run(['translate-full']);
        $this->assertStringContainsString('.translate-full {', $css);
        $this->assertStringContainsString('--tw-translate-x: 100%;', $css);
        $this->assertStringContainsString('--tw-translate-y: 100%;', $css);
        $this->assertStringContainsString('translate: var(--tw-translate-x) var(--tw-translate-y);', $css);
    }

    #[Test]
    public function translate_full_negative(): void
    {
        // Negative static utilities have a leading dash in their class name
        $css = TestHelper::run(['-translate-full']);
        $this->assertStringContainsString('-translate-full', $css);
        $this->assertStringContainsString('--tw-translate-x: -100%;', $css);
        $this->assertStringContainsString('--tw-translate-y: -100%;', $css);
    }

    #[Test]
    public function translate_fractions(): void
    {
        $css = TestHelper::run(['translate-1/2']);
        $this->assertStringContainsString('translate-1\\/2', $css);
        // Fraction format: calc(1/2 * 100%)
        $this->assertStringContainsString('--tw-translate-x: calc(1/2 * 100%);', $css);
        $this->assertStringContainsString('--tw-translate-y: calc(1/2 * 100%);', $css);
    }

    #[Test]
    public function translate_arbitrary(): void
    {
        $css = TestHelper::run(['translate-[123px]']);
        $this->assertStringContainsString('translate-\\[123px\\]', $css);
        $this->assertStringContainsString('--tw-translate-x: 123px;', $css);
        $this->assertStringContainsString('--tw-translate-y: 123px;', $css);
    }

    #[Test]
    public function translate_negative_arbitrary(): void
    {
        $css = TestHelper::run(['-translate-[var(--value)]']);
        $this->assertStringContainsString('-translate-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('calc(var(--value) * -1)', $css);
    }

    #[Test]
    public function translate_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['translate']));
        $this->assertEquals('', TestHelper::run(['translate-1/2/foo']));
    }

    #[Test]
    public function translate_x(): void
    {
        $css = TestHelper::run(['translate-x-full']);
        $this->assertStringContainsString('.translate-x-full {', $css);
        $this->assertStringContainsString('--tw-translate-x: 100%;', $css);

        $css = TestHelper::run(['translate-x-px']);
        $this->assertStringContainsString('.translate-x-px {', $css);
        $this->assertStringContainsString('--tw-translate-x: 1px;', $css);
    }

    #[Test]
    public function translate_x_negative(): void
    {
        $css = TestHelper::run(['-translate-x-full']);
        $this->assertStringContainsString('-translate-x-full', $css);
        $this->assertStringContainsString('--tw-translate-x: -100%;', $css);
    }

    #[Test]
    public function translate_y(): void
    {
        $css = TestHelper::run(['translate-y-full']);
        $this->assertStringContainsString('.translate-y-full {', $css);
        $this->assertStringContainsString('--tw-translate-y: 100%;', $css);
    }

    #[Test]
    public function translate_y_negative(): void
    {
        $css = TestHelper::run(['-translate-y-full']);
        $this->assertStringContainsString('-translate-y-full', $css);
        $this->assertStringContainsString('--tw-translate-y: -100%;', $css);
    }

    #[Test]
    public function translate_3d(): void
    {
        $css = TestHelper::run(['translate-3d']);
        $this->assertStringContainsString('.translate-3d {', $css);
        $this->assertStringContainsString('translate: var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z);', $css);
    }

    // =========================================================================
    // Rotate
    // =========================================================================

    #[Test]
    public function rotate_values(): void
    {
        $css = TestHelper::run(['rotate-45']);
        $this->assertStringContainsString('.rotate-45 {', $css);
        $this->assertStringContainsString('rotate: 45deg;', $css);

        $css = TestHelper::run(['-rotate-45']);
        $this->assertStringContainsString('.-rotate-45 {', $css);
        $this->assertStringContainsString('rotate: calc(45deg * -1);', $css);
    }

    #[Test]
    public function rotate_arbitrary(): void
    {
        $css = TestHelper::run(['rotate-[123deg]']);
        $this->assertStringContainsString('rotate-\\[123deg\\]', $css);
        $this->assertStringContainsString('rotate: 123deg;', $css);

        $css = TestHelper::run(['-rotate-[123deg]']);
        $this->assertStringContainsString('-rotate-\\[123deg\\]', $css);
        $this->assertStringContainsString('rotate: calc(123deg * -1);', $css);
    }

    #[Test]
    public function rotate_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['rotate']));
        $this->assertEquals('', TestHelper::run(['rotate-45/foo']));
    }

    #[Test]
    public function rotate_x(): void
    {
        $css = TestHelper::run(['rotate-x-45']);
        $this->assertStringContainsString('.rotate-x-45 {', $css);
        $this->assertStringContainsString('--tw-rotate-x: rotateX(45deg);', $css);
        $this->assertStringContainsString('transform:', $css);

        $css = TestHelper::run(['-rotate-x-45']);
        $this->assertStringContainsString('.-rotate-x-45 {', $css);
        $this->assertStringContainsString('--tw-rotate-x: rotateX(calc(45deg * -1));', $css);
    }

    #[Test]
    public function rotate_y(): void
    {
        $css = TestHelper::run(['rotate-y-45']);
        $this->assertStringContainsString('.rotate-y-45 {', $css);
        $this->assertStringContainsString('--tw-rotate-y: rotateY(45deg);', $css);
    }

    // =========================================================================
    // Scale
    // =========================================================================

    #[Test]
    public function scale_values(): void
    {
        $css = TestHelper::run(['scale-50']);
        $this->assertStringContainsString('.scale-50 {', $css);
        $this->assertStringContainsString('--tw-scale-x: 50%;', $css);
        $this->assertStringContainsString('--tw-scale-y: 50%;', $css);
        $this->assertStringContainsString('--tw-scale-z: 50%;', $css);
        $this->assertStringContainsString('scale: var(--tw-scale-x) var(--tw-scale-y);', $css);

        $css = TestHelper::run(['-scale-50']);
        $this->assertStringContainsString('.-scale-50 {', $css);
        $this->assertStringContainsString('--tw-scale-x: calc(50% * -1);', $css);
    }

    #[Test]
    public function scale_arbitrary(): void
    {
        // Single arbitrary value still uses CSS variables
        $css = TestHelper::run(['scale-[2]']);
        $this->assertStringContainsString('scale-\\[2\\]', $css);
        $this->assertStringContainsString('--tw-scale-x: 2;', $css);
        $this->assertStringContainsString('--tw-scale-y: 2;', $css);
    }

    #[Test]
    public function scale_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['scale']));
        $this->assertEquals('', TestHelper::run(['scale-50/foo']));
    }

    #[Test]
    public function scale_x(): void
    {
        $css = TestHelper::run(['scale-x-50']);
        $this->assertStringContainsString('.scale-x-50 {', $css);
        $this->assertStringContainsString('--tw-scale-x: 50%;', $css);
        $this->assertStringContainsString('scale: var(--tw-scale-x) var(--tw-scale-y);', $css);

        $css = TestHelper::run(['-scale-x-50']);
        $this->assertStringContainsString('.-scale-x-50 {', $css);
        $this->assertStringContainsString('--tw-scale-x: calc(50% * -1);', $css);
    }

    #[Test]
    public function scale_y(): void
    {
        $css = TestHelper::run(['scale-y-50']);
        $this->assertStringContainsString('.scale-y-50 {', $css);
        $this->assertStringContainsString('--tw-scale-y: 50%;', $css);

        $css = TestHelper::run(['-scale-y-50']);
        $this->assertStringContainsString('.-scale-y-50 {', $css);
    }

    #[Test]
    public function scale_3d(): void
    {
        $css = TestHelper::run(['scale-3d']);
        $this->assertStringContainsString('.scale-3d {', $css);
        $this->assertStringContainsString('scale: var(--tw-scale-x) var(--tw-scale-y) var(--tw-scale-z);', $css);
    }

    // =========================================================================
    // Skew
    // =========================================================================

    #[Test]
    public function skew_values(): void
    {
        $css = TestHelper::run(['skew-6']);
        $this->assertStringContainsString('.skew-6 {', $css);
        $this->assertStringContainsString('--tw-skew-x: skewX(6deg);', $css);
        $this->assertStringContainsString('--tw-skew-y: skewY(6deg);', $css);
        $this->assertStringContainsString('transform:', $css);
    }

    #[Test]
    public function skew_x(): void
    {
        $css = TestHelper::run(['skew-x-6']);
        $this->assertStringContainsString('.skew-x-6 {', $css);
        $this->assertStringContainsString('--tw-skew-x: skewX(6deg);', $css);

        $css = TestHelper::run(['-skew-x-6']);
        $this->assertStringContainsString('.-skew-x-6 {', $css);
        $this->assertStringContainsString('--tw-skew-x: skewX(calc(6deg * -1));', $css);
    }

    #[Test]
    public function skew_y(): void
    {
        $css = TestHelper::run(['skew-y-6']);
        $this->assertStringContainsString('.skew-y-6 {', $css);
        $this->assertStringContainsString('--tw-skew-y: skewY(6deg);', $css);
    }

    // =========================================================================
    // Transform
    // =========================================================================

    #[Test]
    public function transform_none(): void
    {
        $css = TestHelper::run(['transform-none']);
        $this->assertStringContainsString('.transform-none {', $css);
        $this->assertStringContainsString('transform: none;', $css);
    }

    #[Test]
    public function transform_gpu(): void
    {
        $css = TestHelper::run(['transform-gpu']);
        $this->assertStringContainsString('.transform-gpu {', $css);
        $this->assertStringContainsString('transform:', $css);
        $this->assertStringContainsString('translateZ(0)', $css);
    }
}
