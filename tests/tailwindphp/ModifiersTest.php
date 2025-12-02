<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for Tailwind modifiers (opacity, line-height, etc.).
 */
class ModifiersTest extends TestCase
{
    // =========================================================================
    // OPACITY MODIFIERS (/50, /[0.5], etc.)
    // =========================================================================

    public function test_bg_color_with_opacity_number(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/50">');
        $this->assertStringContainsString('background-color:', $css);
        // Opacity should be applied (50% = 0.5)
    }

    public function test_bg_color_with_opacity_0(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/0">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_color_with_opacity_100(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/100">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_color_with_opacity_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/[0.33]">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_color_with_opacity_arbitrary_percent(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/[33%]">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_text_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/75">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_border_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="border-blue-500/50">');
        $this->assertStringContainsString('border-color:', $css);
    }

    public function test_ring_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="ring-purple-500/50">');
        $this->assertStringContainsString('--tw-ring-color:', $css);
    }

    public function test_divide_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="divide-yellow-500/50">');
        $this->assertStringContainsString('border-color:', $css);
    }

    public function test_outline_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="outline-pink-500/50">');
        $this->assertStringContainsString('outline-color:', $css);
    }

    public function test_fill_with_opacity(): void
    {
        $css = Tailwind::generate('<svg class="fill-blue-500/50">');
        $this->assertStringContainsString('fill:', $css);
    }

    public function test_stroke_with_opacity(): void
    {
        $css = Tailwind::generate('<svg class="stroke-blue-500/50">');
        $this->assertStringContainsString('stroke:', $css);
    }

    public function test_shadow_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="shadow-blue-500/50">');
        $this->assertStringContainsString('--tw-shadow-color:', $css);
    }

    public function test_accent_color_with_opacity(): void
    {
        $css = Tailwind::generate('<input class="accent-blue-500/50">');
        $this->assertStringContainsString('accent-color:', $css);
    }

    public function test_caret_color_with_opacity(): void
    {
        $css = Tailwind::generate('<input class="caret-blue-500/50">');
        $this->assertStringContainsString('caret-color:', $css);
    }

    public function test_decoration_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="decoration-blue-500/50">');
        $this->assertStringContainsString('text-decoration-color:', $css);
    }

    // Gradient stops with opacity
    public function test_from_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="from-blue-500/50">');
        $this->assertStringContainsString('--tw-gradient-from:', $css);
    }

    public function test_via_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="via-green-500/50">');
        $this->assertStringContainsString('--tw-gradient-via:', $css);
    }

    public function test_to_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="to-red-500/50">');
        $this->assertStringContainsString('--tw-gradient-to:', $css);
    }

    // =========================================================================
    // THEME OPACITY MODIFIERS
    // =========================================================================

    public function test_opacity_from_theme(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-blue-500/half">',
            'css' => '@tailwind utilities; @theme { --opacity-half: 50%; }',
        ]);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_opacity_from_theme_custom_value(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="text-red-500/muted">',
            'css' => '@tailwind utilities; @theme { --opacity-muted: 60%; }',
        ]);
        $this->assertStringContainsString('color:', $css);
    }

    // =========================================================================
    // LINE HEIGHT MODIFIERS (text-lg/7, text-base/loose)
    // =========================================================================

    public function test_text_with_line_height_number(): void
    {
        $css = Tailwind::generate('<div class="text-lg/7">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_with_line_height_named(): void
    {
        $css = Tailwind::generate('<div class="text-base/loose">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_with_line_height_tight(): void
    {
        $css = Tailwind::generate('<div class="text-xl/tight">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_with_line_height_none(): void
    {
        $css = Tailwind::generate('<div class="text-2xl/none">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height: 1', $css);
    }

    public function test_text_with_line_height_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="text-lg/[1.75]">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_with_line_height_arbitrary_rem(): void
    {
        $css = Tailwind::generate('<div class="text-lg/[2rem]">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height: 2rem', $css);
    }

    // =========================================================================
    // ARBITRARY VALUES
    // =========================================================================

    public function test_arbitrary_color(): void
    {
        $css = Tailwind::generate('<div class="bg-[#1da1f2]">');
        $this->assertStringContainsString('background-color:', $css);
        $this->assertStringContainsString('#1da1f2', $css);
    }

    public function test_arbitrary_color_rgb(): void
    {
        $css = Tailwind::generate('<div class="bg-[rgb(255,0,0)]">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_arbitrary_color_hsl(): void
    {
        $css = Tailwind::generate('<div class="bg-[hsl(200,100%,50%)]">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_arbitrary_color_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="bg-[#1da1f2]/50">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_arbitrary_length(): void
    {
        $css = Tailwind::generate('<div class="w-[300px]">');
        $this->assertStringContainsString('width: 300px', $css);
    }

    public function test_arbitrary_length_em(): void
    {
        $css = Tailwind::generate('<div class="w-[20em]">');
        $this->assertStringContainsString('width: 20em', $css);
    }

    public function test_arbitrary_length_rem(): void
    {
        $css = Tailwind::generate('<div class="w-[10rem]">');
        $this->assertStringContainsString('width: 10rem', $css);
    }

    public function test_arbitrary_length_percent(): void
    {
        $css = Tailwind::generate('<div class="w-[50%]">');
        $this->assertStringContainsString('width: 50%', $css);
    }

    public function test_arbitrary_length_vh(): void
    {
        $css = Tailwind::generate('<div class="h-[50vh]">');
        $this->assertStringContainsString('height: 50vh', $css);
    }

    public function test_arbitrary_length_vw(): void
    {
        $css = Tailwind::generate('<div class="w-[50vw]">');
        $this->assertStringContainsString('width: 50vw', $css);
    }

    public function test_arbitrary_calc(): void
    {
        $css = Tailwind::generate('<div class="w-[calc(100%-2rem)]">');
        $this->assertStringContainsString('width: calc(100% - 2rem)', $css);
    }

    public function test_arbitrary_min(): void
    {
        $css = Tailwind::generate('<div class="w-[min(100%,500px)]">');
        $this->assertStringContainsString('width:', $css);
        $this->assertStringContainsString('min(', $css);
    }

    public function test_arbitrary_max(): void
    {
        $css = Tailwind::generate('<div class="w-[max(300px,50%)]">');
        $this->assertStringContainsString('width:', $css);
        $this->assertStringContainsString('max(', $css);
    }

    public function test_arbitrary_clamp(): void
    {
        $css = Tailwind::generate('<div class="w-[clamp(200px,50%,500px)]">');
        $this->assertStringContainsString('width:', $css);
        $this->assertStringContainsString('clamp(', $css);
    }

    public function test_arbitrary_var(): void
    {
        $css = Tailwind::generate('<div class="w-[var(--custom-width)]">');
        $this->assertStringContainsString('width: var(--custom-width)', $css);
    }

    // =========================================================================
    // ARBITRARY PROPERTIES
    // =========================================================================

    public function test_arbitrary_property_simple(): void
    {
        $css = Tailwind::generate('<div class="[clip-path:circle(50%)]">');
        $this->assertStringContainsString('clip-path: circle(50%)', $css);
    }

    public function test_arbitrary_property_with_underscores(): void
    {
        $css = Tailwind::generate('<div class="[margin:10px_20px]">');
        $this->assertStringContainsString('margin: 10px 20px', $css);
    }

    public function test_arbitrary_property_complex(): void
    {
        $css = Tailwind::generate('<div class="[mask-image:linear-gradient(black,transparent)]">');
        $this->assertStringContainsString('mask-image:', $css);
    }

    public function test_arbitrary_property_with_vendor_prefix(): void
    {
        $css = Tailwind::generate('<div class="[-webkit-line-clamp:3]">');
        $this->assertStringContainsString('-webkit-line-clamp: 3', $css);
    }

    public function test_arbitrary_property_css_variable(): void
    {
        $css = Tailwind::generate('<div class="[--custom-color:red]">');
        $this->assertStringContainsString('--custom-color: red', $css);
    }

    public function test_arbitrary_property_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="[color:red]/50">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_arbitrary_property_with_variant(): void
    {
        $css = Tailwind::generate('<div class="hover:[clip-path:circle(50%)]">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('clip-path:', $css);
    }

    // =========================================================================
    // NEGATIVE VALUES
    // =========================================================================

    public function test_negative_margin(): void
    {
        $css = Tailwind::generate('<div class="-mt-4">');
        $this->assertStringContainsString('margin-top:', $css);
    }

    public function test_negative_translate(): void
    {
        $css = Tailwind::generate('<div class="-translate-x-4">');
        $this->assertStringContainsString('translate:', $css);
    }

    public function test_negative_rotate(): void
    {
        $css = Tailwind::generate('<div class="-rotate-45">');
        $this->assertStringContainsString('rotate:', $css);
    }

    public function test_negative_skew(): void
    {
        $css = Tailwind::generate('<div class="-skew-x-6">');
        $this->assertStringContainsString('transform:', $css);
    }

    public function test_negative_z_index(): void
    {
        $css = Tailwind::generate('<div class="-z-10">');
        $this->assertStringContainsString('z-index:', $css);
    }

    public function test_negative_inset(): void
    {
        $css = Tailwind::generate('<div class="-top-4">');
        $this->assertStringContainsString('top:', $css);
    }

    public function test_negative_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="-mt-[20px]">');
        $this->assertStringContainsString('margin-top:', $css);
    }

    public function test_negative_space(): void
    {
        $css = Tailwind::generate('<div class="-space-x-4">');
        $this->assertStringContainsString('margin-inline-start:', $css);
    }

    // =========================================================================
    // FRACTION VALUES
    // =========================================================================

    // Tailwind 4 uses calc() for fractions instead of computed percentages
    public function test_width_fraction(): void
    {
        $css = Tailwind::generate('<div class="w-1/2">');
        $this->assertStringContainsString('width: calc(1 / 2 * 100%)', $css);
    }

    public function test_width_fraction_third(): void
    {
        $css = Tailwind::generate('<div class="w-1/3">');
        $this->assertStringContainsString('width: calc(1 / 3 * 100%)', $css);
    }

    public function test_width_fraction_two_thirds(): void
    {
        $css = Tailwind::generate('<div class="w-2/3">');
        $this->assertStringContainsString('width: calc(2 / 3 * 100%)', $css);
    }

    public function test_width_fraction_quarter(): void
    {
        $css = Tailwind::generate('<div class="w-1/4">');
        $this->assertStringContainsString('width: calc(1 / 4 * 100%)', $css);
    }

    public function test_width_fraction_twelfths(): void
    {
        $css = Tailwind::generate('<div class="w-5/12">');
        $this->assertStringContainsString('width: calc(5 / 12 * 100%)', $css);
    }

    public function test_translate_fraction(): void
    {
        $css = Tailwind::generate('<div class="translate-x-1/2">');
        $this->assertStringContainsString('translate:', $css);
        $this->assertStringContainsString('calc(1 / 2 * 100%)', $css);
    }

    public function test_negative_translate_fraction(): void
    {
        $css = Tailwind::generate('<div class="-translate-x-1/2">');
        $this->assertStringContainsString('translate:', $css);
    }

    // =========================================================================
    // IMPORTANT MODIFIER
    // =========================================================================

    public function test_important_basic(): void
    {
        $css = Tailwind::generate('<div class="!flex">');
        $this->assertStringContainsString('display: flex !important', $css);
    }

    public function test_important_with_variant(): void
    {
        $css = Tailwind::generate('<div class="hover:!flex">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_important_with_responsive(): void
    {
        $css = Tailwind::generate('<div class="md:!hidden">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_important_with_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="!w-[300px]">');
        $this->assertStringContainsString('width: 300px !important', $css);
    }

    public function test_important_with_color_opacity(): void
    {
        $css = Tailwind::generate('<div class="!bg-blue-500/50">');
        $this->assertStringContainsString('!important', $css);
    }

    // =========================================================================
    // COMPLEX COMBINATIONS
    // =========================================================================

    public function test_variant_with_arbitrary_and_opacity(): void
    {
        $css = Tailwind::generate('<div class="hover:bg-[#1da1f2]/50">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_important_variant_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="hover:!w-[300px]">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('width: 300px !important', $css);
    }

    public function test_responsive_important_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="md:!text-[18px]">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('font-size: 18px !important', $css);
    }

    public function test_negative_with_variant(): void
    {
        $css = Tailwind::generate('<div class="hover:-translate-x-4">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('translate:', $css);
    }

    public function test_dark_important_opacity(): void
    {
        $css = Tailwind::generate('<div class="dark:!bg-gray-900/90">');
        $this->assertStringContainsString('prefers-color-scheme: dark', $css);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_group_hover_with_opacity(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-hover:text-blue-500/80">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('.group', $css);
    }
}
