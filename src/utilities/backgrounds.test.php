<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Background Utilities Tests
 *
 * Port of background tests from: packages/tailwindcss/src/utilities.test.ts
 */
class backgrounds extends TestCase
{
    // =========================================================================
    // Background Color
    // =========================================================================

    #[Test]
    public function bg_color_named(): void
    {
        // Use a color that exists in our theme (--color-red-500)
        $css = TestHelper::run(['bg-black']);
        $this->assertStringContainsString('.bg-black {', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    #[Test]
    public function bg_color_special(): void
    {
        $css = TestHelper::run(['bg-inherit']);
        $this->assertStringContainsString('.bg-inherit {', $css);
        $this->assertStringContainsString('background-color: inherit;', $css);

        $css = TestHelper::run(['bg-current']);
        $this->assertStringContainsString('.bg-current', $css);
        $this->assertStringContainsString('background-color: currentcolor;', $css);

        $css = TestHelper::run(['bg-transparent']);
        $this->assertStringContainsString('.bg-transparent {', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    #[Test]
    public function bg_color_arbitrary(): void
    {
        $css = TestHelper::run(['bg-[#0088cc]']);
        $this->assertStringContainsString('background-color:', $css);
        // The actual color value is kept as-is
        $this->assertStringContainsString('#0088cc', $css);
    }

    #[Test]
    public function bg_color_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-red-500']));
        $this->assertEquals('', TestHelper::run(['-bg-inherit']));
        $this->assertEquals('', TestHelper::run(['-bg-transparent']));
    }

    // =========================================================================
    // Background Image
    // =========================================================================

    #[Test]
    public function bg_none(): void
    {
        $css = TestHelper::run(['bg-none']);
        $this->assertStringContainsString('.bg-none {', $css);
        $this->assertStringContainsString('background-image: none;', $css);
    }

    #[Test]
    public function bg_none_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-none']));
    }

    // =========================================================================
    // Background Size
    // =========================================================================

    #[Test]
    public function bg_size_auto(): void
    {
        $css = TestHelper::run(['bg-auto']);
        $this->assertStringContainsString('.bg-auto {', $css);
        $this->assertStringContainsString('background-size: auto;', $css);
    }

    #[Test]
    public function bg_size_cover(): void
    {
        $css = TestHelper::run(['bg-cover']);
        $this->assertStringContainsString('.bg-cover {', $css);
        $this->assertStringContainsString('background-size: cover;', $css);
    }

    #[Test]
    public function bg_size_contain(): void
    {
        $css = TestHelper::run(['bg-contain']);
        $this->assertStringContainsString('.bg-contain {', $css);
        $this->assertStringContainsString('background-size: contain;', $css);
    }

    #[Test]
    public function bg_size_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-auto']));
        $this->assertEquals('', TestHelper::run(['-bg-cover']));
        $this->assertEquals('', TestHelper::run(['-bg-contain']));
    }

    // =========================================================================
    // Background Attachment
    // =========================================================================

    #[Test]
    public function bg_attachment_fixed(): void
    {
        $css = TestHelper::run(['bg-fixed']);
        $this->assertStringContainsString('.bg-fixed {', $css);
        $this->assertStringContainsString('background-attachment: fixed;', $css);
    }

    #[Test]
    public function bg_attachment_local(): void
    {
        $css = TestHelper::run(['bg-local']);
        $this->assertStringContainsString('.bg-local {', $css);
        $this->assertStringContainsString('background-attachment: local;', $css);
    }

    #[Test]
    public function bg_attachment_scroll(): void
    {
        $css = TestHelper::run(['bg-scroll']);
        $this->assertStringContainsString('.bg-scroll {', $css);
        $this->assertStringContainsString('background-attachment: scroll;', $css);
    }

    #[Test]
    public function bg_attachment_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-fixed']));
        $this->assertEquals('', TestHelper::run(['-bg-local']));
        $this->assertEquals('', TestHelper::run(['-bg-scroll']));
    }

    // =========================================================================
    // Background Position
    // =========================================================================

    #[Test]
    public function bg_position_basic(): void
    {
        $css = TestHelper::run(['bg-top']);
        $this->assertStringContainsString('.bg-top {', $css);
        $this->assertStringContainsString('background-position: top;', $css);

        $css = TestHelper::run(['bg-bottom']);
        $this->assertStringContainsString('.bg-bottom {', $css);
        $this->assertStringContainsString('background-position: bottom;', $css);

        $css = TestHelper::run(['bg-center']);
        $this->assertStringContainsString('.bg-center {', $css);
        $this->assertStringContainsString('background-position: center;', $css);

        $css = TestHelper::run(['bg-left']);
        $this->assertStringContainsString('.bg-left {', $css);
        $this->assertStringContainsString('background-position: 0;', $css);

        $css = TestHelper::run(['bg-right']);
        $this->assertStringContainsString('.bg-right {', $css);
        $this->assertStringContainsString('background-position: 100%;', $css);
    }

    #[Test]
    public function bg_position_corners(): void
    {
        $css = TestHelper::run(['bg-top-left']);
        $this->assertStringContainsString('.bg-top-left {', $css);
        $this->assertStringContainsString('background-position: 0 0;', $css);

        $css = TestHelper::run(['bg-top-right']);
        $this->assertStringContainsString('.bg-top-right {', $css);
        $this->assertStringContainsString('background-position: 100% 0;', $css);

        $css = TestHelper::run(['bg-bottom-left']);
        $this->assertStringContainsString('.bg-bottom-left {', $css);
        $this->assertStringContainsString('background-position: 0 100%;', $css);

        $css = TestHelper::run(['bg-bottom-right']);
        $this->assertStringContainsString('.bg-bottom-right {', $css);
        $this->assertStringContainsString('background-position: 100% 100%;', $css);
    }

    #[Test]
    public function bg_position_legacy_corners(): void
    {
        $css = TestHelper::run(['bg-left-top']);
        $this->assertStringContainsString('.bg-left-top {', $css);
        $this->assertStringContainsString('background-position: 0 0;', $css);

        $css = TestHelper::run(['bg-left-bottom']);
        $this->assertStringContainsString('.bg-left-bottom {', $css);
        $this->assertStringContainsString('background-position: 0 100%;', $css);

        $css = TestHelper::run(['bg-right-top']);
        $this->assertStringContainsString('.bg-right-top {', $css);
        $this->assertStringContainsString('background-position: 100% 0;', $css);

        $css = TestHelper::run(['bg-right-bottom']);
        $this->assertStringContainsString('.bg-right-bottom {', $css);
        $this->assertStringContainsString('background-position: 100% 100%;', $css);
    }

    #[Test]
    public function bg_position_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-center']));
        $this->assertEquals('', TestHelper::run(['-bg-top']));
        $this->assertEquals('', TestHelper::run(['-bg-bottom']));
    }

    // =========================================================================
    // Background Repeat
    // =========================================================================

    #[Test]
    public function bg_repeat(): void
    {
        $css = TestHelper::run(['bg-repeat']);
        $this->assertStringContainsString('.bg-repeat {', $css);
        $this->assertStringContainsString('background-repeat: repeat;', $css);
    }

    #[Test]
    public function bg_no_repeat(): void
    {
        $css = TestHelper::run(['bg-no-repeat']);
        $this->assertStringContainsString('.bg-no-repeat {', $css);
        $this->assertStringContainsString('background-repeat: no-repeat;', $css);
    }

    #[Test]
    public function bg_repeat_x(): void
    {
        $css = TestHelper::run(['bg-repeat-x']);
        $this->assertStringContainsString('.bg-repeat-x {', $css);
        $this->assertStringContainsString('background-repeat: repeat-x;', $css);
    }

    #[Test]
    public function bg_repeat_y(): void
    {
        $css = TestHelper::run(['bg-repeat-y']);
        $this->assertStringContainsString('.bg-repeat-y {', $css);
        $this->assertStringContainsString('background-repeat: repeat-y;', $css);
    }

    #[Test]
    public function bg_repeat_round(): void
    {
        $css = TestHelper::run(['bg-repeat-round']);
        $this->assertStringContainsString('.bg-repeat-round {', $css);
        $this->assertStringContainsString('background-repeat: round;', $css);
    }

    #[Test]
    public function bg_repeat_space(): void
    {
        $css = TestHelper::run(['bg-repeat-space']);
        $this->assertStringContainsString('.bg-repeat-space {', $css);
        $this->assertStringContainsString('background-repeat: space;', $css);
    }

    #[Test]
    public function bg_repeat_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-repeat']));
        $this->assertEquals('', TestHelper::run(['-bg-no-repeat']));
    }

    // =========================================================================
    // Background Origin
    // =========================================================================

    #[Test]
    public function bg_origin_border(): void
    {
        $css = TestHelper::run(['bg-origin-border']);
        $this->assertStringContainsString('.bg-origin-border {', $css);
        $this->assertStringContainsString('background-origin: border-box;', $css);
    }

    #[Test]
    public function bg_origin_padding(): void
    {
        $css = TestHelper::run(['bg-origin-padding']);
        $this->assertStringContainsString('.bg-origin-padding {', $css);
        $this->assertStringContainsString('background-origin: padding-box;', $css);
    }

    #[Test]
    public function bg_origin_content(): void
    {
        $css = TestHelper::run(['bg-origin-content']);
        $this->assertStringContainsString('.bg-origin-content {', $css);
        $this->assertStringContainsString('background-origin: content-box;', $css);
    }

    #[Test]
    public function bg_origin_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-origin-border']));
        $this->assertEquals('', TestHelper::run(['-bg-origin-padding']));
        $this->assertEquals('', TestHelper::run(['-bg-origin-content']));
    }

    // =========================================================================
    // Background Clip
    // =========================================================================

    #[Test]
    public function bg_clip_border(): void
    {
        $css = TestHelper::run(['bg-clip-border']);
        $this->assertStringContainsString('.bg-clip-border {', $css);
        $this->assertStringContainsString('background-clip: border-box;', $css);
    }

    #[Test]
    public function bg_clip_padding(): void
    {
        $css = TestHelper::run(['bg-clip-padding']);
        $this->assertStringContainsString('.bg-clip-padding {', $css);
        $this->assertStringContainsString('background-clip: padding-box;', $css);
    }

    #[Test]
    public function bg_clip_content(): void
    {
        $css = TestHelper::run(['bg-clip-content']);
        $this->assertStringContainsString('.bg-clip-content {', $css);
        $this->assertStringContainsString('background-clip: content-box;', $css);
    }

    #[Test]
    public function bg_clip_text(): void
    {
        $css = TestHelper::run(['bg-clip-text']);
        $this->assertStringContainsString('.bg-clip-text {', $css);
        $this->assertStringContainsString('-webkit-background-clip: text;', $css);
        $this->assertStringContainsString('background-clip: text;', $css);
    }

    #[Test]
    public function bg_clip_invalid_returns_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-bg-clip-border']));
        $this->assertEquals('', TestHelper::run(['-bg-clip-padding']));
        $this->assertEquals('', TestHelper::run(['-bg-clip-content']));
        $this->assertEquals('', TestHelper::run(['-bg-clip-text']));
    }

    // =========================================================================
    // Linear Gradient
    // =========================================================================

    #[Test]
    public function bg_linear_directions(): void
    {
        $css = TestHelper::run(['bg-linear-to-t']);
        $this->assertStringContainsString('.bg-linear-to-t {', $css);
        $this->assertStringContainsString('--tw-gradient-position:', $css);
        $this->assertStringContainsString('to top', $css);
        $this->assertStringContainsString('background-image: linear-gradient(var(--tw-gradient-stops));', $css);

        $css = TestHelper::run(['bg-linear-to-r']);
        $this->assertStringContainsString('.bg-linear-to-r {', $css);
        $this->assertStringContainsString('to right', $css);

        $css = TestHelper::run(['bg-linear-to-b']);
        $this->assertStringContainsString('.bg-linear-to-b {', $css);
        $this->assertStringContainsString('to bottom', $css);

        $css = TestHelper::run(['bg-linear-to-l']);
        $this->assertStringContainsString('.bg-linear-to-l {', $css);
        $this->assertStringContainsString('to left', $css);
    }

    #[Test]
    public function bg_linear_corner_directions(): void
    {
        $css = TestHelper::run(['bg-linear-to-tr']);
        $this->assertStringContainsString('.bg-linear-to-tr {', $css);
        $this->assertStringContainsString('to top right', $css);

        $css = TestHelper::run(['bg-linear-to-tl']);
        $this->assertStringContainsString('.bg-linear-to-tl {', $css);
        $this->assertStringContainsString('to top left', $css);

        $css = TestHelper::run(['bg-linear-to-br']);
        $this->assertStringContainsString('.bg-linear-to-br {', $css);
        $this->assertStringContainsString('to bottom right', $css);

        $css = TestHelper::run(['bg-linear-to-bl']);
        $this->assertStringContainsString('.bg-linear-to-bl {', $css);
        $this->assertStringContainsString('to bottom left', $css);
    }

    // =========================================================================
    // Conic Gradient
    // =========================================================================

    #[Test]
    public function bg_conic_with_interpolation(): void
    {
        $css = TestHelper::run(['bg-conic/oklch']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch;', $css);
        $this->assertStringContainsString('background-image: conic-gradient(var(--tw-gradient-stops));', $css);

        $css = TestHelper::run(['bg-conic/oklab']);
        $this->assertStringContainsString('--tw-gradient-position: in oklab;', $css);

        $css = TestHelper::run(['bg-conic/hsl']);
        $this->assertStringContainsString('--tw-gradient-position: in hsl;', $css);

        $css = TestHelper::run(['bg-conic/srgb']);
        $this->assertStringContainsString('--tw-gradient-position: in srgb;', $css);
    }

    #[Test]
    public function bg_conic_with_hue_interpolation(): void
    {
        $css = TestHelper::run(['bg-conic/longer']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch longer hue;', $css);

        $css = TestHelper::run(['bg-conic/shorter']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch shorter hue;', $css);

        $css = TestHelper::run(['bg-conic/increasing']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch increasing hue;', $css);

        $css = TestHelper::run(['bg-conic/decreasing']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch decreasing hue;', $css);
    }

    // =========================================================================
    // Radial Gradient
    // =========================================================================

    #[Test]
    public function bg_radial_with_interpolation(): void
    {
        $css = TestHelper::run(['bg-radial/oklch']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch;', $css);
        $this->assertStringContainsString('background-image: radial-gradient(var(--tw-gradient-stops));', $css);

        $css = TestHelper::run(['bg-radial/oklab']);
        $this->assertStringContainsString('--tw-gradient-position: in oklab;', $css);

        $css = TestHelper::run(['bg-radial/hsl']);
        $this->assertStringContainsString('--tw-gradient-position: in hsl;', $css);

        $css = TestHelper::run(['bg-radial/srgb']);
        $this->assertStringContainsString('--tw-gradient-position: in srgb;', $css);
    }

    #[Test]
    public function bg_radial_with_hue_interpolation(): void
    {
        $css = TestHelper::run(['bg-radial/longer']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch longer hue;', $css);

        $css = TestHelper::run(['bg-radial/shorter']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch shorter hue;', $css);

        $css = TestHelper::run(['bg-radial/increasing']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch increasing hue;', $css);

        $css = TestHelper::run(['bg-radial/decreasing']);
        $this->assertStringContainsString('--tw-gradient-position: in oklch decreasing hue;', $css);
    }
}
