<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Effects Utilities Tests
 *
 * Port of effects tests from: packages/tailwindcss/src/utilities.test.ts
 *
 * Includes:
 * - opacity
 * - box-shadow (shadow-*)
 * - mix-blend-mode
 * - background-blend-mode
 */
class effects extends TestCase
{
    // =========================================================================
    // Opacity
    // =========================================================================

    #[Test]
    public function opacity_values(): void
    {
        $css = TestHelper::run(['opacity-0']);
        $this->assertStringContainsString('opacity: 0;', $css);

        $css = TestHelper::run(['opacity-50']);
        $this->assertStringContainsString('opacity: 0.5;', $css);

        $css = TestHelper::run(['opacity-100']);
        $this->assertStringContainsString('opacity: 1;', $css);
    }

    #[Test]
    public function opacity_intermediate_values(): void
    {
        $css = TestHelper::run(['opacity-5']);
        $this->assertStringContainsString('opacity: 0.05;', $css);

        $css = TestHelper::run(['opacity-25']);
        $this->assertStringContainsString('opacity: 0.25;', $css);

        $css = TestHelper::run(['opacity-75']);
        $this->assertStringContainsString('opacity: 0.75;', $css);

        $css = TestHelper::run(['opacity-95']);
        $this->assertStringContainsString('opacity: 0.95;', $css);
    }

    #[Test]
    public function opacity_arbitrary(): void
    {
        $css = TestHelper::run(['opacity-[0.33]']);
        $this->assertStringContainsString('opacity: 0.33;', $css);
    }

    // =========================================================================
    // Box Shadow
    // =========================================================================

    #[Test]
    public function shadow_default(): void
    {
        $css = TestHelper::run(['shadow']);
        $this->assertStringContainsString('box-shadow:', $css);
    }

    #[Test]
    public function shadow_none(): void
    {
        $css = TestHelper::run(['shadow-none']);
        $this->assertStringContainsString('box-shadow: none;', $css);
    }

    #[Test]
    public function shadow_sizes(): void
    {
        $css = TestHelper::run(['shadow-sm']);
        $this->assertStringContainsString('box-shadow:', $css);
        $this->assertStringContainsString('shadow-sm', $css);

        $css = TestHelper::run(['shadow-md']);
        $this->assertStringContainsString('box-shadow:', $css);

        $css = TestHelper::run(['shadow-lg']);
        $this->assertStringContainsString('box-shadow:', $css);

        $css = TestHelper::run(['shadow-xl']);
        $this->assertStringContainsString('box-shadow:', $css);

        $css = TestHelper::run(['shadow-2xl']);
        $this->assertStringContainsString('box-shadow:', $css);
    }

    #[Test]
    public function shadow_inner(): void
    {
        $css = TestHelper::run(['shadow-inner']);
        $this->assertStringContainsString('box-shadow:', $css);
        $this->assertStringContainsString('shadow-inner', $css);
    }

    #[Test]
    public function shadow_arbitrary(): void
    {
        // TailwindCSS 4.0: Underscores in arbitrary values are converted to spaces
        $css = TestHelper::run(['shadow-[0_0_10px_red]']);
        $this->assertStringContainsString('box-shadow: 0 0 10px red;', $css);
    }

    // =========================================================================
    // Inset Shadow
    // =========================================================================

    #[Test]
    public function inset_shadow_default(): void
    {
        $css = TestHelper::run(['inset-shadow']);
        $this->assertStringContainsString('box-shadow:', $css);
    }

    #[Test]
    public function inset_shadow_none(): void
    {
        $css = TestHelper::run(['inset-shadow-none']);
        $this->assertStringContainsString('box-shadow: none;', $css);
    }

    #[Test]
    public function inset_shadow_sizes(): void
    {
        $css = TestHelper::run(['inset-shadow-sm']);
        $this->assertStringContainsString('box-shadow:', $css);

        $css = TestHelper::run(['inset-shadow-xs']);
        $this->assertStringContainsString('box-shadow:', $css);
    }

    // =========================================================================
    // Drop Shadow
    // =========================================================================

    #[Test]
    public function drop_shadow_default(): void
    {
        $css = TestHelper::run(['drop-shadow']);
        $this->assertStringContainsString('filter:', $css);
    }

    #[Test]
    public function drop_shadow_none(): void
    {
        $css = TestHelper::run(['drop-shadow-none']);
        $this->assertStringContainsString('filter: drop-shadow(0 0 #0000);', $css);
    }

    #[Test]
    public function drop_shadow_sizes(): void
    {
        $css = TestHelper::run(['drop-shadow-sm']);
        $this->assertStringContainsString('filter:', $css);

        $css = TestHelper::run(['drop-shadow-md']);
        $this->assertStringContainsString('filter:', $css);

        $css = TestHelper::run(['drop-shadow-lg']);
        $this->assertStringContainsString('filter:', $css);

        $css = TestHelper::run(['drop-shadow-xl']);
        $this->assertStringContainsString('filter:', $css);

        $css = TestHelper::run(['drop-shadow-2xl']);
        $this->assertStringContainsString('filter:', $css);
    }

    // =========================================================================
    // Mix Blend Mode
    // =========================================================================

    #[Test]
    public function mix_blend_mode(): void
    {
        $css = TestHelper::run(['mix-blend-normal']);
        $this->assertStringContainsString('mix-blend-mode: normal;', $css);

        $css = TestHelper::run(['mix-blend-multiply']);
        $this->assertStringContainsString('mix-blend-mode: multiply;', $css);

        $css = TestHelper::run(['mix-blend-screen']);
        $this->assertStringContainsString('mix-blend-mode: screen;', $css);

        $css = TestHelper::run(['mix-blend-overlay']);
        $this->assertStringContainsString('mix-blend-mode: overlay;', $css);
    }

    #[Test]
    public function mix_blend_mode_extended(): void
    {
        $css = TestHelper::run(['mix-blend-darken']);
        $this->assertStringContainsString('mix-blend-mode: darken;', $css);

        $css = TestHelper::run(['mix-blend-lighten']);
        $this->assertStringContainsString('mix-blend-mode: lighten;', $css);

        $css = TestHelper::run(['mix-blend-color-dodge']);
        $this->assertStringContainsString('mix-blend-mode: color-dodge;', $css);

        $css = TestHelper::run(['mix-blend-color-burn']);
        $this->assertStringContainsString('mix-blend-mode: color-burn;', $css);
    }

    #[Test]
    public function mix_blend_mode_light(): void
    {
        $css = TestHelper::run(['mix-blend-hard-light']);
        $this->assertStringContainsString('mix-blend-mode: hard-light;', $css);

        $css = TestHelper::run(['mix-blend-soft-light']);
        $this->assertStringContainsString('mix-blend-mode: soft-light;', $css);
    }

    #[Test]
    public function mix_blend_mode_difference(): void
    {
        $css = TestHelper::run(['mix-blend-difference']);
        $this->assertStringContainsString('mix-blend-mode: difference;', $css);

        $css = TestHelper::run(['mix-blend-exclusion']);
        $this->assertStringContainsString('mix-blend-mode: exclusion;', $css);
    }

    #[Test]
    public function mix_blend_mode_color(): void
    {
        $css = TestHelper::run(['mix-blend-hue']);
        $this->assertStringContainsString('mix-blend-mode: hue;', $css);

        $css = TestHelper::run(['mix-blend-saturation']);
        $this->assertStringContainsString('mix-blend-mode: saturation;', $css);

        $css = TestHelper::run(['mix-blend-color']);
        $this->assertStringContainsString('mix-blend-mode: color;', $css);

        $css = TestHelper::run(['mix-blend-luminosity']);
        $this->assertStringContainsString('mix-blend-mode: luminosity;', $css);
    }

    #[Test]
    public function mix_blend_mode_plus(): void
    {
        $css = TestHelper::run(['mix-blend-plus-darker']);
        $this->assertStringContainsString('mix-blend-mode: plus-darker;', $css);

        $css = TestHelper::run(['mix-blend-plus-lighter']);
        $this->assertStringContainsString('mix-blend-mode: plus-lighter;', $css);
    }

    // =========================================================================
    // Background Blend Mode
    // =========================================================================

    #[Test]
    public function bg_blend_mode(): void
    {
        $css = TestHelper::run(['bg-blend-normal']);
        $this->assertStringContainsString('background-blend-mode: normal;', $css);

        $css = TestHelper::run(['bg-blend-multiply']);
        $this->assertStringContainsString('background-blend-mode: multiply;', $css);

        $css = TestHelper::run(['bg-blend-screen']);
        $this->assertStringContainsString('background-blend-mode: screen;', $css);

        $css = TestHelper::run(['bg-blend-overlay']);
        $this->assertStringContainsString('background-blend-mode: overlay;', $css);
    }

    #[Test]
    public function bg_blend_mode_extended(): void
    {
        $css = TestHelper::run(['bg-blend-darken']);
        $this->assertStringContainsString('background-blend-mode: darken;', $css);

        $css = TestHelper::run(['bg-blend-lighten']);
        $this->assertStringContainsString('background-blend-mode: lighten;', $css);

        $css = TestHelper::run(['bg-blend-color-dodge']);
        $this->assertStringContainsString('background-blend-mode: color-dodge;', $css);

        $css = TestHelper::run(['bg-blend-color-burn']);
        $this->assertStringContainsString('background-blend-mode: color-burn;', $css);
    }

    #[Test]
    public function bg_blend_mode_light(): void
    {
        $css = TestHelper::run(['bg-blend-hard-light']);
        $this->assertStringContainsString('background-blend-mode: hard-light;', $css);

        $css = TestHelper::run(['bg-blend-soft-light']);
        $this->assertStringContainsString('background-blend-mode: soft-light;', $css);
    }

    #[Test]
    public function bg_blend_mode_difference(): void
    {
        $css = TestHelper::run(['bg-blend-difference']);
        $this->assertStringContainsString('background-blend-mode: difference;', $css);

        $css = TestHelper::run(['bg-blend-exclusion']);
        $this->assertStringContainsString('background-blend-mode: exclusion;', $css);
    }

    #[Test]
    public function bg_blend_mode_color(): void
    {
        $css = TestHelper::run(['bg-blend-hue']);
        $this->assertStringContainsString('background-blend-mode: hue;', $css);

        $css = TestHelper::run(['bg-blend-saturation']);
        $this->assertStringContainsString('background-blend-mode: saturation;', $css);

        $css = TestHelper::run(['bg-blend-color']);
        $this->assertStringContainsString('background-blend-mode: color;', $css);

        $css = TestHelper::run(['bg-blend-luminosity']);
        $this->assertStringContainsString('background-blend-mode: luminosity;', $css);
    }
}
