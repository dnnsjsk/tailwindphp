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

    // =========================================================================
    // EXHAUSTIVE OPACITY MODIFIERS (5, 10, 15, ... 95)
    // =========================================================================

    public function test_bg_opacity_5(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/5">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_10(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/10">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_15(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/15">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_20(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/20">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/25">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_30(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/30">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_35(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/35">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_40(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/40">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_45(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/45">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_55(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/55">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_60(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/60">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_65(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/65">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_70(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/70">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/75">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_80(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/80">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_85(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/85">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_90(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/90">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_opacity_95(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/95">');
        $this->assertStringContainsString('background-color:', $css);
    }

    // Text color with all opacity values
    public function test_text_opacity_5(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/5">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_text_opacity_10(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/10">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_text_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/25">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_text_opacity_50(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/50">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_text_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/75">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_text_opacity_95(): void
    {
        $css = Tailwind::generate('<div class="text-red-500/95">');
        $this->assertStringContainsString('color:', $css);
    }

    // Border color with all opacity values
    public function test_border_opacity_5(): void
    {
        $css = Tailwind::generate('<div class="border-green-500/5">');
        $this->assertStringContainsString('border-color:', $css);
    }

    public function test_border_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="border-green-500/25">');
        $this->assertStringContainsString('border-color:', $css);
    }

    public function test_border_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="border-green-500/75">');
        $this->assertStringContainsString('border-color:', $css);
    }

    public function test_border_opacity_95(): void
    {
        $css = Tailwind::generate('<div class="border-green-500/95">');
        $this->assertStringContainsString('border-color:', $css);
    }

    // =========================================================================
    // EXHAUSTIVE COLOR TESTS
    // =========================================================================

    // All color palette shades
    public function test_bg_red_50(): void
    {
        $css = Tailwind::generate('<div class="bg-red-50">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_100(): void
    {
        $css = Tailwind::generate('<div class="bg-red-100">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_200(): void
    {
        $css = Tailwind::generate('<div class="bg-red-200">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_300(): void
    {
        $css = Tailwind::generate('<div class="bg-red-300">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_400(): void
    {
        $css = Tailwind::generate('<div class="bg-red-400">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_600(): void
    {
        $css = Tailwind::generate('<div class="bg-red-600">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_700(): void
    {
        $css = Tailwind::generate('<div class="bg-red-700">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_800(): void
    {
        $css = Tailwind::generate('<div class="bg-red-800">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_900(): void
    {
        $css = Tailwind::generate('<div class="bg-red-900">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_red_950(): void
    {
        $css = Tailwind::generate('<div class="bg-red-950">');
        $this->assertStringContainsString('background-color:', $css);
    }

    // Other color palettes
    public function test_bg_orange_500(): void
    {
        $css = Tailwind::generate('<div class="bg-orange-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_amber_500(): void
    {
        $css = Tailwind::generate('<div class="bg-amber-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_yellow_500(): void
    {
        $css = Tailwind::generate('<div class="bg-yellow-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_lime_500(): void
    {
        $css = Tailwind::generate('<div class="bg-lime-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_green_500(): void
    {
        $css = Tailwind::generate('<div class="bg-green-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_emerald_500(): void
    {
        $css = Tailwind::generate('<div class="bg-emerald-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_teal_500(): void
    {
        $css = Tailwind::generate('<div class="bg-teal-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_cyan_500(): void
    {
        $css = Tailwind::generate('<div class="bg-cyan-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_sky_500(): void
    {
        $css = Tailwind::generate('<div class="bg-sky-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_indigo_500(): void
    {
        $css = Tailwind::generate('<div class="bg-indigo-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_violet_500(): void
    {
        $css = Tailwind::generate('<div class="bg-violet-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_purple_500(): void
    {
        $css = Tailwind::generate('<div class="bg-purple-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_fuchsia_500(): void
    {
        $css = Tailwind::generate('<div class="bg-fuchsia-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_pink_500(): void
    {
        $css = Tailwind::generate('<div class="bg-pink-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_rose_500(): void
    {
        $css = Tailwind::generate('<div class="bg-rose-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    // Neutral colors
    public function test_bg_slate_500(): void
    {
        $css = Tailwind::generate('<div class="bg-slate-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_gray_500(): void
    {
        $css = Tailwind::generate('<div class="bg-gray-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_zinc_500(): void
    {
        $css = Tailwind::generate('<div class="bg-zinc-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_neutral_500(): void
    {
        $css = Tailwind::generate('<div class="bg-neutral-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_bg_stone_500(): void
    {
        $css = Tailwind::generate('<div class="bg-stone-500">');
        $this->assertStringContainsString('background-color:', $css);
    }

    // =========================================================================
    // EXHAUSTIVE LINE HEIGHT WITH TEXT SIZE COMBINATIONS
    // =========================================================================

    public function test_text_xs_7(): void
    {
        $css = Tailwind::generate('<div class="text-xs/7">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_sm_7(): void
    {
        $css = Tailwind::generate('<div class="text-sm/7">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_base_7(): void
    {
        $css = Tailwind::generate('<div class="text-base/7">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_lg_8(): void
    {
        $css = Tailwind::generate('<div class="text-lg/8">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_xl_8(): void
    {
        $css = Tailwind::generate('<div class="text-xl/8">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_2xl_9(): void
    {
        $css = Tailwind::generate('<div class="text-2xl/9">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_3xl_10(): void
    {
        $css = Tailwind::generate('<div class="text-3xl/10">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_sm_snug(): void
    {
        $css = Tailwind::generate('<div class="text-sm/snug">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_base_relaxed(): void
    {
        $css = Tailwind::generate('<div class="text-base/relaxed">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    public function test_text_lg_normal(): void
    {
        $css = Tailwind::generate('<div class="text-lg/normal">');
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('line-height:', $css);
    }

    // =========================================================================
    // EXHAUSTIVE GRADIENT STOP OPACITY
    // =========================================================================

    public function test_from_opacity_10(): void
    {
        $css = Tailwind::generate('<div class="from-blue-500/10">');
        $this->assertStringContainsString('--tw-gradient-from:', $css);
    }

    public function test_from_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="from-blue-500/25">');
        $this->assertStringContainsString('--tw-gradient-from:', $css);
    }

    public function test_from_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="from-blue-500/75">');
        $this->assertStringContainsString('--tw-gradient-from:', $css);
    }

    public function test_via_opacity_10(): void
    {
        $css = Tailwind::generate('<div class="via-green-500/10">');
        $this->assertStringContainsString('--tw-gradient-via:', $css);
    }

    public function test_via_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="via-green-500/25">');
        $this->assertStringContainsString('--tw-gradient-via:', $css);
    }

    public function test_via_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="via-green-500/75">');
        $this->assertStringContainsString('--tw-gradient-via:', $css);
    }

    public function test_to_opacity_10(): void
    {
        $css = Tailwind::generate('<div class="to-red-500/10">');
        $this->assertStringContainsString('--tw-gradient-to:', $css);
    }

    public function test_to_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="to-red-500/25">');
        $this->assertStringContainsString('--tw-gradient-to:', $css);
    }

    public function test_to_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="to-red-500/75">');
        $this->assertStringContainsString('--tw-gradient-to:', $css);
    }

    // =========================================================================
    // EXHAUSTIVE RING/SHADOW OPACITY
    // =========================================================================

    public function test_ring_color_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="ring-blue-500/25">');
        $this->assertStringContainsString('--tw-ring-color:', $css);
    }

    public function test_ring_color_opacity_50(): void
    {
        $css = Tailwind::generate('<div class="ring-blue-500/50">');
        $this->assertStringContainsString('--tw-ring-color:', $css);
    }

    public function test_ring_color_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="ring-blue-500/75">');
        $this->assertStringContainsString('--tw-ring-color:', $css);
    }

    public function test_shadow_color_opacity_25(): void
    {
        $css = Tailwind::generate('<div class="shadow-blue-500/25">');
        $this->assertStringContainsString('--tw-shadow-color:', $css);
    }

    public function test_shadow_color_opacity_50(): void
    {
        $css = Tailwind::generate('<div class="shadow-blue-500/50">');
        $this->assertStringContainsString('--tw-shadow-color:', $css);
    }

    public function test_shadow_color_opacity_75(): void
    {
        $css = Tailwind::generate('<div class="shadow-blue-500/75">');
        $this->assertStringContainsString('--tw-shadow-color:', $css);
    }

    // =========================================================================
    // EXHAUSTIVE ARBITRARY VALUE TESTS
    // =========================================================================

    public function test_arbitrary_margin_px(): void
    {
        $css = Tailwind::generate('<div class="m-[15px]">');
        $this->assertStringContainsString('margin: 15px', $css);
    }

    public function test_arbitrary_margin_rem(): void
    {
        $css = Tailwind::generate('<div class="m-[1.5rem]">');
        $this->assertStringContainsString('margin: 1.5rem', $css);
    }

    public function test_arbitrary_padding_px(): void
    {
        $css = Tailwind::generate('<div class="p-[25px]">');
        $this->assertStringContainsString('padding: 25px', $css);
    }

    public function test_arbitrary_gap_px(): void
    {
        $css = Tailwind::generate('<div class="gap-[12px]">');
        $this->assertStringContainsString('gap: 12px', $css);
    }

    public function test_arbitrary_gap_rem(): void
    {
        $css = Tailwind::generate('<div class="gap-[1rem]">');
        $this->assertStringContainsString('gap: 1rem', $css);
    }

    public function test_arbitrary_top_px(): void
    {
        $css = Tailwind::generate('<div class="top-[100px]">');
        $this->assertStringContainsString('top: 100px', $css);
    }

    public function test_arbitrary_left_percent(): void
    {
        $css = Tailwind::generate('<div class="left-[25%]">');
        $this->assertStringContainsString('left: 25%', $css);
    }

    public function test_arbitrary_translate_percent(): void
    {
        $css = Tailwind::generate('<div class="translate-x-[50%]">');
        $this->assertStringContainsString('translate:', $css);
    }

    public function test_arbitrary_rotate_deg(): void
    {
        $css = Tailwind::generate('<div class="rotate-[17deg]">');
        $this->assertStringContainsString('rotate:', $css);
    }

    public function test_arbitrary_scale_number(): void
    {
        $css = Tailwind::generate('<div class="scale-[1.15]">');
        $this->assertStringContainsString('scale:', $css);
    }

    public function test_arbitrary_skew_deg(): void
    {
        $css = Tailwind::generate('<div class="skew-x-[5deg]">');
        $this->assertStringContainsString('transform:', $css);
    }

    public function test_arbitrary_blur_px(): void
    {
        $css = Tailwind::generate('<div class="blur-[10px]">');
        $this->assertStringContainsString('filter:', $css);
    }

    public function test_arbitrary_brightness_number(): void
    {
        $css = Tailwind::generate('<div class="brightness-[1.25]">');
        $this->assertStringContainsString('filter:', $css);
    }

    public function test_arbitrary_border_radius_px(): void
    {
        $css = Tailwind::generate('<div class="rounded-[12px]">');
        $this->assertStringContainsString('border-radius: 12px', $css);
    }

    public function test_arbitrary_border_width_px(): void
    {
        $css = Tailwind::generate('<div class="border-[3px]">');
        $this->assertStringContainsString('border-width: 3px', $css);
    }

    public function test_arbitrary_font_size_px(): void
    {
        $css = Tailwind::generate('<div class="text-[22px]">');
        $this->assertStringContainsString('font-size: 22px', $css);
    }

    public function test_arbitrary_font_size_rem(): void
    {
        $css = Tailwind::generate('<div class="text-[1.375rem]">');
        $this->assertStringContainsString('font-size: 1.375rem', $css);
    }

    public function test_arbitrary_line_height_number(): void
    {
        $css = Tailwind::generate('<div class="leading-[1.6]">');
        $this->assertStringContainsString('line-height: 1.6', $css);
    }

    public function test_arbitrary_line_height_px(): void
    {
        $css = Tailwind::generate('<div class="leading-[28px]">');
        $this->assertStringContainsString('line-height: 28px', $css);
    }

    public function test_arbitrary_z_index(): void
    {
        $css = Tailwind::generate('<div class="z-[100]">');
        $this->assertStringContainsString('z-index: 100', $css);
    }

    public function test_arbitrary_grid_cols(): void
    {
        $css = Tailwind::generate('<div class="grid-cols-[200px_1fr_200px]">');
        $this->assertStringContainsString('grid-template-columns: 200px 1fr 200px', $css);
    }

    public function test_arbitrary_grid_rows(): void
    {
        $css = Tailwind::generate('<div class="grid-rows-[auto_1fr_auto]">');
        $this->assertStringContainsString('grid-template-rows: auto 1fr auto', $css);
    }

    public function test_arbitrary_aspect_ratio(): void
    {
        $css = Tailwind::generate('<div class="aspect-[4/3]">');
        // Note: no spaces around / in the output
        $this->assertStringContainsString('aspect-ratio: 4/3', $css);
    }
}
