<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Filters Utilities Tests
 *
 * Port of filter tests from: packages/tailwindcss/src/utilities.test.ts
 */
class filters extends TestCase
{
    // =========================================================================
    // filter
    // =========================================================================

    #[Test]
    public function filter_default(): void
    {
        $css = TestHelper::run(['filter']);
        $this->assertStringContainsString('.filter {', $css);
        $this->assertStringContainsString('filter:', $css);
        $this->assertStringContainsString('var(--tw-blur,)', $css);
    }

    #[Test]
    public function filter_none(): void
    {
        $css = TestHelper::run(['filter-none']);
        $this->assertStringContainsString('.filter-none {', $css);
        $this->assertStringContainsString('filter: none;', $css);
    }

    #[Test]
    public function filter_arbitrary(): void
    {
        $css = TestHelper::run(['filter-[var(--value)]']);
        $this->assertStringContainsString('filter-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('filter: var(--value);', $css);
    }

    #[Test]
    public function filter_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-filter']));
        $this->assertEquals('', TestHelper::run(['-filter-none']));
        $this->assertEquals('', TestHelper::run(['filter/foo']));
        $this->assertEquals('', TestHelper::run(['filter-none/foo']));
    }

    // =========================================================================
    // backdrop-filter
    // =========================================================================

    #[Test]
    public function backdrop_filter_default(): void
    {
        $css = TestHelper::run(['backdrop-filter']);
        $this->assertStringContainsString('.backdrop-filter {', $css);
        $this->assertStringContainsString('-webkit-backdrop-filter:', $css);
        $this->assertStringContainsString('backdrop-filter:', $css);
    }

    #[Test]
    public function backdrop_filter_none(): void
    {
        $css = TestHelper::run(['backdrop-filter-none']);
        $this->assertStringContainsString('.backdrop-filter-none {', $css);
        $this->assertStringContainsString('-webkit-backdrop-filter: none;', $css);
        $this->assertStringContainsString('backdrop-filter: none;', $css);
    }

    #[Test]
    public function backdrop_filter_arbitrary(): void
    {
        $css = TestHelper::run(['backdrop-filter-[var(--value)]']);
        $this->assertStringContainsString('backdrop-filter-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('-webkit-backdrop-filter: var(--value);', $css);
        $this->assertStringContainsString('backdrop-filter: var(--value);', $css);
    }

    // =========================================================================
    // blur
    // =========================================================================

    #[Test]
    public function blur_values(): void
    {
        $css = TestHelper::run(['blur-[4px]']);
        $this->assertStringContainsString('blur-\\[4px\\]', $css);
        $this->assertStringContainsString('--tw-blur: blur(4px);', $css);
        $this->assertStringContainsString('filter:', $css);
    }

    #[Test]
    public function blur_none(): void
    {
        // When theme has --blur-none, it uses the theme value
        // When theme doesn't have it, it uses the static value (empty)
        $css = TestHelper::run(['blur-none']);
        $this->assertStringContainsString('.blur-none {', $css);
        // Our default theme has --blur-none, so it resolves to blur(var(--blur-none))
        $this->assertStringContainsString('--tw-blur: blur(var(--blur-none));', $css);
    }

    #[Test]
    public function blur_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-blur-xl']));
        $this->assertEquals('', TestHelper::run(['-blur-[4px]']));
        $this->assertEquals('', TestHelper::run(['blur-xl/foo']));
    }

    // =========================================================================
    // backdrop-blur
    // =========================================================================

    #[Test]
    public function backdrop_blur_values(): void
    {
        $css = TestHelper::run(['backdrop-blur-[4px]']);
        $this->assertStringContainsString('backdrop-blur-\\[4px\\]', $css);
        $this->assertStringContainsString('--tw-backdrop-blur: blur(4px);', $css);
        $this->assertStringContainsString('-webkit-backdrop-filter:', $css);
        $this->assertStringContainsString('backdrop-filter:', $css);
    }

    #[Test]
    public function backdrop_blur_none(): void
    {
        // When theme has --blur-none, it uses the theme value
        $css = TestHelper::run(['backdrop-blur-none']);
        $this->assertStringContainsString('.backdrop-blur-none {', $css);
        // Our default theme has --blur-none, so it resolves to blur(var(--blur-none))
        $this->assertStringContainsString('--tw-backdrop-blur: blur(var(--blur-none));', $css);
    }

    // =========================================================================
    // brightness
    // =========================================================================

    #[Test]
    public function brightness_values(): void
    {
        $css = TestHelper::run(['brightness-50']);
        $this->assertStringContainsString('.brightness-50 {', $css);
        $this->assertStringContainsString('--tw-brightness: brightness(50%);', $css);
        $this->assertStringContainsString('filter:', $css);
    }

    #[Test]
    public function brightness_arbitrary(): void
    {
        $css = TestHelper::run(['brightness-[1.23]']);
        $this->assertStringContainsString('brightness-\\[1\\.23\\]', $css);
        $this->assertStringContainsString('--tw-brightness: brightness(1.23);', $css);
    }

    #[Test]
    public function brightness_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-brightness-50']));
        $this->assertEquals('', TestHelper::run(['brightness-50/foo']));
    }

    // =========================================================================
    // backdrop-brightness
    // =========================================================================

    #[Test]
    public function backdrop_brightness_values(): void
    {
        $css = TestHelper::run(['backdrop-brightness-50']);
        $this->assertStringContainsString('.backdrop-brightness-50 {', $css);
        $this->assertStringContainsString('--tw-backdrop-brightness: brightness(50%);', $css);
    }

    // =========================================================================
    // contrast
    // =========================================================================

    #[Test]
    public function contrast_values(): void
    {
        $css = TestHelper::run(['contrast-50']);
        $this->assertStringContainsString('.contrast-50 {', $css);
        $this->assertStringContainsString('--tw-contrast: contrast(50%);', $css);
        $this->assertStringContainsString('filter:', $css);
    }

    #[Test]
    public function contrast_arbitrary(): void
    {
        $css = TestHelper::run(['contrast-[1.23]']);
        $this->assertStringContainsString('contrast-\\[1\\.23\\]', $css);
        $this->assertStringContainsString('--tw-contrast: contrast(1.23);', $css);
    }

    // =========================================================================
    // backdrop-contrast
    // =========================================================================

    #[Test]
    public function backdrop_contrast_values(): void
    {
        $css = TestHelper::run(['backdrop-contrast-50']);
        $this->assertStringContainsString('.backdrop-contrast-50 {', $css);
        $this->assertStringContainsString('--tw-backdrop-contrast: contrast(50%);', $css);
    }

    // =========================================================================
    // grayscale
    // =========================================================================

    #[Test]
    public function grayscale_default(): void
    {
        $css = TestHelper::run(['grayscale']);
        $this->assertStringContainsString('.grayscale {', $css);
        $this->assertStringContainsString('--tw-grayscale: grayscale(100%);', $css);
    }

    #[Test]
    public function grayscale_values(): void
    {
        $css = TestHelper::run(['grayscale-0']);
        $this->assertStringContainsString('.grayscale-0 {', $css);
        $this->assertStringContainsString('--tw-grayscale: grayscale(0%);', $css);
    }

    #[Test]
    public function grayscale_arbitrary(): void
    {
        $css = TestHelper::run(['grayscale-[var(--value)]']);
        $this->assertStringContainsString('grayscale-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('--tw-grayscale: grayscale(var(--value));', $css);
    }

    #[Test]
    public function grayscale_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-grayscale']));
        $this->assertEquals('', TestHelper::run(['-grayscale-0']));
        $this->assertEquals('', TestHelper::run(['grayscale/foo']));
    }

    // =========================================================================
    // backdrop-grayscale
    // =========================================================================

    #[Test]
    public function backdrop_grayscale_default(): void
    {
        $css = TestHelper::run(['backdrop-grayscale']);
        $this->assertStringContainsString('.backdrop-grayscale {', $css);
        $this->assertStringContainsString('--tw-backdrop-grayscale: grayscale(100%);', $css);
    }

    #[Test]
    public function backdrop_grayscale_values(): void
    {
        $css = TestHelper::run(['backdrop-grayscale-0']);
        $this->assertStringContainsString('.backdrop-grayscale-0 {', $css);
        $this->assertStringContainsString('--tw-backdrop-grayscale: grayscale(0%);', $css);
    }

    // =========================================================================
    // hue-rotate
    // =========================================================================

    #[Test]
    public function hue_rotate_values(): void
    {
        $css = TestHelper::run(['hue-rotate-15']);
        $this->assertStringContainsString('.hue-rotate-15 {', $css);
        $this->assertStringContainsString('--tw-hue-rotate: hue-rotate(15deg);', $css);
    }

    #[Test]
    public function hue_rotate_negative(): void
    {
        $css = TestHelper::run(['-hue-rotate-15']);
        $this->assertStringContainsString('.-hue-rotate-15 {', $css);
        $this->assertStringContainsString('--tw-hue-rotate: hue-rotate(calc(15deg * -1));', $css);
    }

    #[Test]
    public function hue_rotate_arbitrary(): void
    {
        $css = TestHelper::run(['hue-rotate-[45deg]']);
        $this->assertStringContainsString('hue-rotate-\\[45deg\\]', $css);
        $this->assertStringContainsString('--tw-hue-rotate: hue-rotate(45deg);', $css);
    }

    #[Test]
    public function hue_rotate_negative_arbitrary(): void
    {
        $css = TestHelper::run(['-hue-rotate-[45deg]']);
        $this->assertStringContainsString('-hue-rotate-\\[45deg\\]', $css);
        $this->assertStringContainsString('--tw-hue-rotate: hue-rotate(calc(45deg * -1));', $css);
    }

    // =========================================================================
    // backdrop-hue-rotate
    // =========================================================================

    #[Test]
    public function backdrop_hue_rotate_values(): void
    {
        $css = TestHelper::run(['backdrop-hue-rotate-15']);
        $this->assertStringContainsString('.backdrop-hue-rotate-15 {', $css);
        $this->assertStringContainsString('--tw-backdrop-hue-rotate: hue-rotate(15deg);', $css);
    }

    #[Test]
    public function backdrop_hue_rotate_negative(): void
    {
        $css = TestHelper::run(['-backdrop-hue-rotate-15']);
        $this->assertStringContainsString('.-backdrop-hue-rotate-15 {', $css);
        $this->assertStringContainsString('--tw-backdrop-hue-rotate: hue-rotate(calc(15deg * -1));', $css);
    }

    // =========================================================================
    // invert
    // =========================================================================

    #[Test]
    public function invert_default(): void
    {
        $css = TestHelper::run(['invert']);
        $this->assertStringContainsString('.invert {', $css);
        $this->assertStringContainsString('--tw-invert: invert(100%);', $css);
    }

    #[Test]
    public function invert_values(): void
    {
        $css = TestHelper::run(['invert-0']);
        $this->assertStringContainsString('.invert-0 {', $css);
        $this->assertStringContainsString('--tw-invert: invert(0%);', $css);
    }

    #[Test]
    public function invert_arbitrary(): void
    {
        $css = TestHelper::run(['invert-[var(--value)]']);
        $this->assertStringContainsString('invert-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('--tw-invert: invert(var(--value));', $css);
    }

    #[Test]
    public function invert_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-invert']));
        $this->assertEquals('', TestHelper::run(['-invert-0']));
        $this->assertEquals('', TestHelper::run(['invert/foo']));
    }

    // =========================================================================
    // backdrop-invert
    // =========================================================================

    #[Test]
    public function backdrop_invert_default(): void
    {
        $css = TestHelper::run(['backdrop-invert']);
        $this->assertStringContainsString('.backdrop-invert {', $css);
        $this->assertStringContainsString('--tw-backdrop-invert: invert(100%);', $css);
    }

    #[Test]
    public function backdrop_invert_values(): void
    {
        $css = TestHelper::run(['backdrop-invert-0']);
        $this->assertStringContainsString('.backdrop-invert-0 {', $css);
        $this->assertStringContainsString('--tw-backdrop-invert: invert(0%);', $css);
    }

    // =========================================================================
    // saturate
    // =========================================================================

    #[Test]
    public function saturate_values(): void
    {
        $css = TestHelper::run(['saturate-0']);
        $this->assertStringContainsString('.saturate-0 {', $css);
        $this->assertStringContainsString('--tw-saturate: saturate(0%);', $css);
    }

    #[Test]
    public function saturate_arbitrary(): void
    {
        $css = TestHelper::run(['saturate-[1.75]']);
        $this->assertStringContainsString('saturate-\\[1\\.75\\]', $css);
        $this->assertStringContainsString('--tw-saturate: saturate(1.75);', $css);
    }

    #[Test]
    public function saturate_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-saturate-0']));
        $this->assertEquals('', TestHelper::run(['saturate-0/foo']));
    }

    // =========================================================================
    // backdrop-saturate
    // =========================================================================

    #[Test]
    public function backdrop_saturate_values(): void
    {
        $css = TestHelper::run(['backdrop-saturate-0']);
        $this->assertStringContainsString('.backdrop-saturate-0 {', $css);
        $this->assertStringContainsString('--tw-backdrop-saturate: saturate(0%);', $css);
    }

    // =========================================================================
    // sepia
    // =========================================================================

    #[Test]
    public function sepia_default(): void
    {
        $css = TestHelper::run(['sepia']);
        $this->assertStringContainsString('.sepia {', $css);
        $this->assertStringContainsString('--tw-sepia: sepia(100%);', $css);
    }

    #[Test]
    public function sepia_values(): void
    {
        $css = TestHelper::run(['sepia-0']);
        $this->assertStringContainsString('.sepia-0 {', $css);
        $this->assertStringContainsString('--tw-sepia: sepia(0%);', $css);
    }

    #[Test]
    public function sepia_arbitrary(): void
    {
        $css = TestHelper::run(['sepia-[50%]']);
        $this->assertStringContainsString('sepia-\\[50\\%\\]', $css);
        $this->assertStringContainsString('--tw-sepia: sepia(50%);', $css);
    }

    #[Test]
    public function sepia_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-sepia']));
        $this->assertEquals('', TestHelper::run(['-sepia-0']));
        $this->assertEquals('', TestHelper::run(['sepia/foo']));
    }

    // =========================================================================
    // backdrop-sepia
    // =========================================================================

    #[Test]
    public function backdrop_sepia_default(): void
    {
        $css = TestHelper::run(['backdrop-sepia']);
        $this->assertStringContainsString('.backdrop-sepia {', $css);
        $this->assertStringContainsString('--tw-backdrop-sepia: sepia(100%);', $css);
    }

    #[Test]
    public function backdrop_sepia_values(): void
    {
        $css = TestHelper::run(['backdrop-sepia-0']);
        $this->assertStringContainsString('.backdrop-sepia-0 {', $css);
        $this->assertStringContainsString('--tw-backdrop-sepia: sepia(0%);', $css);
    }

    // =========================================================================
    // drop-shadow
    // =========================================================================

    #[Test]
    public function drop_shadow_none(): void
    {
        $css = TestHelper::run(['drop-shadow-none']);
        $this->assertStringContainsString('.drop-shadow-none {', $css);
        // The static utility value is a single space ' '
        $this->assertStringContainsString('--tw-drop-shadow:', $css);
        $this->assertStringContainsString('filter:', $css);
    }

    // =========================================================================
    // backdrop-opacity
    // =========================================================================

    #[Test]
    public function backdrop_opacity_values(): void
    {
        // When theme has --opacity-50, it uses the theme value
        $css = TestHelper::run(['backdrop-opacity-50']);
        $this->assertStringContainsString('.backdrop-opacity-50 {', $css);
        $this->assertStringContainsString('--tw-backdrop-opacity: opacity(var(--opacity-50));', $css);
    }

    #[Test]
    public function backdrop_opacity_arbitrary(): void
    {
        $css = TestHelper::run(['backdrop-opacity-[0.5]']);
        $this->assertStringContainsString('backdrop-opacity-\\[0\\.5\\]', $css);
        // Arbitrary values are passed through as-is
        $this->assertStringContainsString('--tw-backdrop-opacity: opacity(0.5);', $css);
    }
}
