<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Tests for tw-animate-css integration.
 *
 * tw-animate-css is a TailwindCSS v4.0 compatible replacement for tailwindcss-animate,
 * used by shadcn/ui for component animations.
 *
 * @see https://github.com/Wombosvideo/tw-animate-css
 */
class TwAnimateCssTest extends TestCase
{
    #[Test]
    public function it_imports_tw_animate_css(): void
    {
        $css = Tailwind::generate(
            '<div class="animate-in animate-out">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        // Should contain @property declarations
        $this->assertStringContainsString('@property --tw-enter-opacity', $css);
        $this->assertStringContainsString('@property --tw-exit-opacity', $css);

        // Should contain keyframes
        $this->assertStringContainsString('@keyframes enter', $css);
        $this->assertStringContainsString('@keyframes exit', $css);

        // Should contain animate-in/out utilities
        $this->assertStringContainsString('.animate-in', $css);
        $this->assertStringContainsString('.animate-out', $css);
    }

    #[Test]
    public function it_generates_fade_in_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="fade-in fade-in-50">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.fade-in', $css);
        $this->assertStringContainsString('--tw-enter-opacity', $css);
    }

    #[Test]
    public function it_generates_fade_out_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="fade-out fade-out-50">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.fade-out', $css);
        $this->assertStringContainsString('--tw-exit-opacity', $css);
    }

    #[Test]
    public function it_generates_zoom_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="zoom-in zoom-in-95 zoom-out zoom-out-95">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.zoom-in', $css);
        $this->assertStringContainsString('.zoom-out', $css);
        $this->assertStringContainsString('--tw-enter-scale', $css);
        $this->assertStringContainsString('--tw-exit-scale', $css);
    }

    #[Test]
    public function it_generates_spin_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="spin-in spin-in-90 spin-out spin-out-180">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.spin-in', $css);
        $this->assertStringContainsString('.spin-out', $css);
        $this->assertStringContainsString('--tw-enter-rotate', $css);
        $this->assertStringContainsString('--tw-exit-rotate', $css);
    }

    #[Test]
    public function it_generates_slide_in_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="slide-in-from-top slide-in-from-bottom slide-in-from-left slide-in-from-right">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.slide-in-from-top', $css);
        $this->assertStringContainsString('.slide-in-from-bottom', $css);
        $this->assertStringContainsString('.slide-in-from-left', $css);
        $this->assertStringContainsString('.slide-in-from-right', $css);
        $this->assertStringContainsString('--tw-enter-translate', $css);
    }

    #[Test]
    public function it_generates_slide_out_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="slide-out-to-top slide-out-to-bottom slide-out-to-left slide-out-to-right">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.slide-out-to-top', $css);
        $this->assertStringContainsString('.slide-out-to-bottom', $css);
        $this->assertStringContainsString('.slide-out-to-left', $css);
        $this->assertStringContainsString('.slide-out-to-right', $css);
        $this->assertStringContainsString('--tw-exit-translate', $css);
    }

    #[Test]
    public function it_generates_slide_utilities_with_values(): void
    {
        $css = Tailwind::generate(
            '<div class="slide-in-from-top-4 slide-in-from-bottom-8 slide-out-to-left-full">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.slide-in-from-top-4', $css);
        $this->assertStringContainsString('.slide-in-from-bottom-8', $css);
        $this->assertStringContainsString('.slide-out-to-left-full', $css);
    }

    #[Test]
    public function it_generates_animation_duration_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="animation-duration-300 animation-duration-500">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.animation-duration-300', $css);
        $this->assertStringContainsString('.animation-duration-500', $css);
        $this->assertStringContainsString('animation-duration', $css);
    }

    #[Test]
    public function it_generates_delay_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="delay-150 delay-300">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('animation-delay', $css);
    }

    #[Test]
    public function it_generates_repeat_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="repeat-1 repeat-infinite">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.repeat-1', $css);
        $this->assertStringContainsString('.repeat-infinite', $css);
        $this->assertStringContainsString('animation-iteration-count', $css);
    }

    #[Test]
    public function it_generates_direction_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="direction-normal direction-reverse direction-alternate">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.direction-normal', $css);
        $this->assertStringContainsString('.direction-reverse', $css);
        $this->assertStringContainsString('.direction-alternate', $css);
        $this->assertStringContainsString('animation-direction', $css);
    }

    #[Test]
    public function it_generates_fill_mode_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="fill-mode-none fill-mode-forwards fill-mode-backwards fill-mode-both">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.fill-mode-none', $css);
        $this->assertStringContainsString('.fill-mode-forwards', $css);
        $this->assertStringContainsString('.fill-mode-backwards', $css);
        $this->assertStringContainsString('.fill-mode-both', $css);
        $this->assertStringContainsString('animation-fill-mode', $css);
    }

    #[Test]
    public function it_generates_play_state_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="running paused">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.running', $css);
        $this->assertStringContainsString('.paused', $css);
        $this->assertStringContainsString('animation-play-state', $css);
    }

    #[Test]
    public function it_generates_blur_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="blur-in blur-out blur-in-4 blur-out-8">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.blur-in', $css);
        $this->assertStringContainsString('.blur-out', $css);
        $this->assertStringContainsString('--tw-enter-blur', $css);
        $this->assertStringContainsString('--tw-exit-blur', $css);
    }

    #[Test]
    public function it_generates_accordion_animations(): void
    {
        $css = Tailwind::generate(
            '<div class="animate-accordion-down animate-accordion-up">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('@keyframes accordion-down', $css);
        $this->assertStringContainsString('@keyframes accordion-up', $css);
        $this->assertStringContainsString('.animate-accordion-down', $css);
        $this->assertStringContainsString('.animate-accordion-up', $css);
    }

    #[Test]
    public function it_generates_collapsible_animations(): void
    {
        $css = Tailwind::generate(
            '<div class="animate-collapsible-down animate-collapsible-up">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('@keyframes collapsible-down', $css);
        $this->assertStringContainsString('@keyframes collapsible-up', $css);
    }

    #[Test]
    public function it_generates_caret_blink_animation(): void
    {
        $css = Tailwind::generate(
            '<div class="animate-caret-blink">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('@keyframes caret-blink', $css);
        $this->assertStringContainsString('.animate-caret-blink', $css);
    }

    #[Test]
    public function it_generates_animate_in_and_out(): void
    {
        $css = Tailwind::generate(
            '<div class="animate-in animate-out">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.animate-in', $css);
        $this->assertStringContainsString('.animate-out', $css);
    }

    #[Test]
    public function it_works_with_shadcn_dialog_pattern(): void
    {
        // Common shadcn/ui dialog animation pattern
        $html = '<div class="animate-in fade-in-0 zoom-in-95 slide-in-from-left-1/2 slide-in-from-top-[48%] duration-200">Dialog</div>';

        $css = Tailwind::generate(
            $html,
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.animate-in', $css);
        $this->assertStringContainsString('.fade-in-0', $css);
        $this->assertStringContainsString('.zoom-in-95', $css);
    }

    #[Test]
    public function it_works_with_shadcn_dropdown_pattern(): void
    {
        // Common shadcn/ui dropdown animation pattern
        $html = '<div class="animate-in fade-in-0 zoom-in-95" data-state="open">Dropdown</div>';
        $html .= '<div class="animate-out fade-out-0 zoom-out-95" data-state="closed">Dropdown</div>';

        $css = Tailwind::generate(
            $html,
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        $this->assertStringContainsString('.animate-in', $css);
        $this->assertStringContainsString('.animate-out', $css);
        $this->assertStringContainsString('.fade-in-0', $css);
        $this->assertStringContainsString('.fade-out-0', $css);
        $this->assertStringContainsString('.zoom-in-95', $css);
        $this->assertStringContainsString('.zoom-out-95', $css);
    }

    #[Test]
    public function it_generates_directional_slide_utilities(): void
    {
        $css = Tailwind::generate(
            '<div class="slide-in-from-start slide-in-from-end slide-out-to-start slide-out-to-end">Hello</div>',
            '@import "tw-animate-css"; @import "tailwindcss/utilities.css";',
        );

        // These are RTL/LTR aware utilities
        $this->assertStringContainsString('.slide-in-from-start', $css);
        $this->assertStringContainsString('.slide-in-from-end', $css);
        $this->assertStringContainsString('.slide-out-to-start', $css);
        $this->assertStringContainsString('.slide-out-to-end', $css);
    }

    #[Test]
    public function it_works_combined_with_tailwindcss_import(): void
    {
        $css = Tailwind::generate(
            '<div class="flex p-4 animate-in fade-in-50 bg-blue-500">Hello</div>',
            '@import "tailwindcss"; @import "tw-animate-css";',
        );

        // Core Tailwind utilities
        $this->assertStringContainsString('.flex', $css);
        $this->assertStringContainsString('.p-4', $css);
        $this->assertStringContainsString('.bg-blue-500', $css);

        // tw-animate-css utilities
        $this->assertStringContainsString('.animate-in', $css);
        $this->assertStringContainsString('.fade-in-50', $css);
    }
}
