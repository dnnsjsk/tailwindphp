<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Interactivity Utilities Tests
 *
 * Port of interactivity tests from: packages/tailwindcss/src/utilities.test.ts
 */
class interactivity extends TestCase
{
    // =========================================================================
    // Cursor
    // =========================================================================

    #[Test]
    public function cursor_static_values(): void
    {
        $css = TestHelper::run(['cursor-auto']);
        $this->assertStringContainsString('.cursor-auto {', $css);
        $this->assertStringContainsString('cursor: auto;', $css);

        $css = TestHelper::run(['cursor-pointer']);
        $this->assertStringContainsString('.cursor-pointer {', $css);
        $this->assertStringContainsString('cursor: pointer;', $css);

        $css = TestHelper::run(['cursor-wait']);
        $this->assertStringContainsString('.cursor-wait {', $css);
        $this->assertStringContainsString('cursor: wait;', $css);

        $css = TestHelper::run(['cursor-not-allowed']);
        $this->assertStringContainsString('.cursor-not-allowed {', $css);
        $this->assertStringContainsString('cursor: not-allowed;', $css);
    }

    #[Test]
    public function cursor_arbitrary(): void
    {
        $css = TestHelper::run(['cursor-[var(--value)]']);
        $this->assertStringContainsString('cursor-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('cursor: var(--value);', $css);
    }

    #[Test]
    public function cursor_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-cursor-auto']));
        $this->assertEquals('', TestHelper::run(['cursor-auto/foo']));
    }

    // =========================================================================
    // Touch Action
    // =========================================================================

    #[Test]
    public function touch_action_static_values(): void
    {
        $css = TestHelper::run(['touch-auto']);
        $this->assertStringContainsString('.touch-auto {', $css);
        $this->assertStringContainsString('touch-action: auto;', $css);

        $css = TestHelper::run(['touch-none']);
        $this->assertStringContainsString('.touch-none {', $css);
        $this->assertStringContainsString('touch-action: none;', $css);

        $css = TestHelper::run(['touch-manipulation']);
        $this->assertStringContainsString('.touch-manipulation {', $css);
        $this->assertStringContainsString('touch-action: manipulation;', $css);
    }

    #[Test]
    public function touch_pan_values(): void
    {
        $css = TestHelper::run(['touch-pan-x']);
        $this->assertStringContainsString('.touch-pan-x {', $css);
        $this->assertStringContainsString('--tw-pan-x: pan-x;', $css);
        $this->assertStringContainsString('touch-action:', $css);

        $css = TestHelper::run(['touch-pan-y']);
        $this->assertStringContainsString('.touch-pan-y {', $css);
        $this->assertStringContainsString('--tw-pan-y: pan-y;', $css);

        $css = TestHelper::run(['touch-pinch-zoom']);
        $this->assertStringContainsString('.touch-pinch-zoom {', $css);
        $this->assertStringContainsString('--tw-pinch-zoom: pinch-zoom;', $css);
    }

    // =========================================================================
    // User Select
    // =========================================================================

    #[Test]
    public function user_select_values(): void
    {
        $css = TestHelper::run(['select-none']);
        $this->assertStringContainsString('.select-none {', $css);
        $this->assertStringContainsString('-webkit-user-select: none;', $css);
        $this->assertStringContainsString('user-select: none;', $css);

        $css = TestHelper::run(['select-text']);
        $this->assertStringContainsString('.select-text {', $css);
        $this->assertStringContainsString('user-select: text;', $css);

        $css = TestHelper::run(['select-all']);
        $this->assertStringContainsString('.select-all {', $css);
        $this->assertStringContainsString('user-select: all;', $css);

        $css = TestHelper::run(['select-auto']);
        $this->assertStringContainsString('.select-auto {', $css);
        $this->assertStringContainsString('user-select: auto;', $css);
    }

    // =========================================================================
    // Resize
    // =========================================================================

    #[Test]
    public function resize_values(): void
    {
        $css = TestHelper::run(['resize-none']);
        $this->assertStringContainsString('.resize-none {', $css);
        $this->assertStringContainsString('resize: none;', $css);

        $css = TestHelper::run(['resize-x']);
        $this->assertStringContainsString('.resize-x {', $css);
        $this->assertStringContainsString('resize: horizontal;', $css);

        $css = TestHelper::run(['resize-y']);
        $this->assertStringContainsString('.resize-y {', $css);
        $this->assertStringContainsString('resize: vertical;', $css);

        $css = TestHelper::run(['resize']);
        $this->assertStringContainsString('.resize {', $css);
        $this->assertStringContainsString('resize: both;', $css);
    }

    // =========================================================================
    // Scroll Snap
    // =========================================================================

    #[Test]
    public function scroll_snap_type_values(): void
    {
        $css = TestHelper::run(['snap-none']);
        $this->assertStringContainsString('.snap-none {', $css);
        $this->assertStringContainsString('scroll-snap-type: none;', $css);

        $css = TestHelper::run(['snap-x']);
        $this->assertStringContainsString('.snap-x {', $css);
        $this->assertStringContainsString('scroll-snap-type: x', $css);

        $css = TestHelper::run(['snap-y']);
        $this->assertStringContainsString('.snap-y {', $css);
        $this->assertStringContainsString('scroll-snap-type: y', $css);
    }

    #[Test]
    public function scroll_snap_align_values(): void
    {
        $css = TestHelper::run(['snap-start']);
        $this->assertStringContainsString('.snap-start {', $css);
        $this->assertStringContainsString('scroll-snap-align: start;', $css);

        $css = TestHelper::run(['snap-end']);
        $this->assertStringContainsString('.snap-end {', $css);
        $this->assertStringContainsString('scroll-snap-align: end;', $css);

        $css = TestHelper::run(['snap-center']);
        $this->assertStringContainsString('.snap-center {', $css);
        $this->assertStringContainsString('scroll-snap-align: center;', $css);
    }

    #[Test]
    public function scroll_snap_stop_values(): void
    {
        $css = TestHelper::run(['snap-normal']);
        $this->assertStringContainsString('.snap-normal {', $css);
        $this->assertStringContainsString('scroll-snap-stop: normal;', $css);

        $css = TestHelper::run(['snap-always']);
        $this->assertStringContainsString('.snap-always {', $css);
        $this->assertStringContainsString('scroll-snap-stop: always;', $css);
    }

    // =========================================================================
    // Scroll Margin
    // =========================================================================

    #[Test]
    public function scroll_margin_values(): void
    {
        $css = TestHelper::run(['scroll-m-4']);
        $this->assertStringContainsString('.scroll-m-4 {', $css);
        $this->assertStringContainsString('scroll-margin:', $css);

        $css = TestHelper::run(['scroll-mt-4']);
        $this->assertStringContainsString('.scroll-mt-4 {', $css);
        $this->assertStringContainsString('scroll-margin-top:', $css);
    }

    // =========================================================================
    // Scroll Padding
    // =========================================================================

    #[Test]
    public function scroll_padding_values(): void
    {
        $css = TestHelper::run(['scroll-p-4']);
        $this->assertStringContainsString('.scroll-p-4 {', $css);
        $this->assertStringContainsString('scroll-padding:', $css);

        $css = TestHelper::run(['scroll-pt-4']);
        $this->assertStringContainsString('.scroll-pt-4 {', $css);
        $this->assertStringContainsString('scroll-padding-top:', $css);
    }

    // =========================================================================
    // Scroll Behavior
    // =========================================================================

    #[Test]
    public function scroll_behavior_values(): void
    {
        $css = TestHelper::run(['scroll-auto']);
        $this->assertStringContainsString('.scroll-auto {', $css);
        $this->assertStringContainsString('scroll-behavior: auto;', $css);

        $css = TestHelper::run(['scroll-smooth']);
        $this->assertStringContainsString('.scroll-smooth {', $css);
        $this->assertStringContainsString('scroll-behavior: smooth;', $css);
    }

    // =========================================================================
    // Overscroll Behavior
    // =========================================================================

    #[Test]
    public function overscroll_values(): void
    {
        $css = TestHelper::run(['overscroll-auto']);
        $this->assertStringContainsString('.overscroll-auto {', $css);
        $this->assertStringContainsString('overscroll-behavior: auto;', $css);

        $css = TestHelper::run(['overscroll-contain']);
        $this->assertStringContainsString('.overscroll-contain {', $css);
        $this->assertStringContainsString('overscroll-behavior: contain;', $css);

        $css = TestHelper::run(['overscroll-none']);
        $this->assertStringContainsString('.overscroll-none {', $css);
        $this->assertStringContainsString('overscroll-behavior: none;', $css);
    }

    // =========================================================================
    // Pointer Events
    // =========================================================================

    #[Test]
    public function pointer_events_values(): void
    {
        $css = TestHelper::run(['pointer-events-none']);
        $this->assertStringContainsString('.pointer-events-none {', $css);
        $this->assertStringContainsString('pointer-events: none;', $css);

        $css = TestHelper::run(['pointer-events-auto']);
        $this->assertStringContainsString('.pointer-events-auto {', $css);
        $this->assertStringContainsString('pointer-events: auto;', $css);
    }

    #[Test]
    public function pointer_events_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-pointer-events-none']));
        $this->assertEquals('', TestHelper::run(['pointer-events-none/foo']));
    }

    // =========================================================================
    // Appearance
    // =========================================================================

    #[Test]
    public function appearance_values(): void
    {
        $css = TestHelper::run(['appearance-none']);
        $this->assertStringContainsString('.appearance-none {', $css);
        $this->assertStringContainsString('appearance: none;', $css);

        $css = TestHelper::run(['appearance-auto']);
        $this->assertStringContainsString('.appearance-auto {', $css);
        $this->assertStringContainsString('appearance: auto;', $css);
    }

    // =========================================================================
    // Accent Color
    // =========================================================================

    #[Test]
    public function accent_color_values(): void
    {
        $css = TestHelper::run(['accent-inherit']);
        $this->assertStringContainsString('.accent-inherit {', $css);
        $this->assertStringContainsString('accent-color: inherit;', $css);

        $css = TestHelper::run(['accent-current']);
        $this->assertStringContainsString('.accent-current', $css);
        $this->assertStringContainsString('accent-color: currentcolor;', $css);

        $css = TestHelper::run(['accent-transparent']);
        $this->assertStringContainsString('.accent-transparent {', $css);
        $this->assertStringContainsString('accent-color:', $css);
    }

    // =========================================================================
    // Caret Color
    // =========================================================================

    #[Test]
    public function caret_color_values(): void
    {
        $css = TestHelper::run(['caret-inherit']);
        $this->assertStringContainsString('.caret-inherit {', $css);
        $this->assertStringContainsString('caret-color: inherit;', $css);

        $css = TestHelper::run(['caret-current']);
        $this->assertStringContainsString('.caret-current', $css);
        $this->assertStringContainsString('caret-color: currentcolor;', $css);

        $css = TestHelper::run(['caret-transparent']);
        $this->assertStringContainsString('.caret-transparent {', $css);
        $this->assertStringContainsString('caret-color:', $css);
    }

    // =========================================================================
    // Will Change
    // =========================================================================

    #[Test]
    public function will_change_values(): void
    {
        $css = TestHelper::run(['will-change-auto']);
        $this->assertStringContainsString('.will-change-auto {', $css);
        $this->assertStringContainsString('will-change: auto;', $css);

        $css = TestHelper::run(['will-change-contents']);
        $this->assertStringContainsString('.will-change-contents {', $css);
        $this->assertStringContainsString('will-change: contents;', $css);

        $css = TestHelper::run(['will-change-transform']);
        $this->assertStringContainsString('.will-change-transform {', $css);
        $this->assertStringContainsString('will-change: transform;', $css);

        $css = TestHelper::run(['will-change-scroll']);
        $this->assertStringContainsString('.will-change-scroll {', $css);
        $this->assertStringContainsString('will-change: scroll-position;', $css);
    }

    #[Test]
    public function will_change_arbitrary(): void
    {
        $css = TestHelper::run(['will-change-[var(--value)]']);
        $this->assertStringContainsString('will-change-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('will-change: var(--value);', $css);
    }

    #[Test]
    public function will_change_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-will-change-auto']));
        $this->assertEquals('', TestHelper::run(['will-change-auto/foo']));
    }
}
