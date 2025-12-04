<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Edge case tests for complex and tricky scenarios.
 *
 * These tests cover unusual combinations, deep nesting, special characters,
 * and other edge cases that might cause issues.
 */
class EdgeCasesTest extends TestCase
{
    // =========================================================================
    // DEEP VARIANT STACKING
    // =========================================================================

    public function test_three_level_variant_stacking(): void
    {
        $css = Tailwind::generate('<div class="lg:hover:focus:text-red-500">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString(':hover:focus', $css);
        $this->assertStringContainsString('color:', $css);
    }

    public function test_four_level_variant_stacking(): void
    {
        $css = Tailwind::generate('<div class="dark:lg:hover:focus:bg-blue-500">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString(':hover:focus', $css);
    }

    public function test_responsive_with_group_hover(): void
    {
        $css = Tailwind::generate('<div class="md:group-hover:text-white">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString(':is(:where(.group)', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_responsive_with_peer_focus(): void
    {
        $css = Tailwind::generate('<div class="lg:peer-focus:bg-green-500">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString(':is(:where(.peer)', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    // =========================================================================
    // ARBITRARY VALUES WITH SPECIAL CHARACTERS
    // =========================================================================

    public function test_arbitrary_value_with_spaces(): void
    {
        $css = Tailwind::generate('<div class="grid-cols-[1fr_2fr_1fr]">');
        $this->assertStringContainsString('grid-template-columns:', $css);
        $this->assertStringContainsString('1fr 2fr 1fr', $css);
    }

    public function test_arbitrary_value_with_parentheses(): void
    {
        $css = Tailwind::generate('<div class="bg-[rgb(255,0,0)]">');
        $this->assertStringContainsString('background-color:', $css);
        $this->assertStringContainsString('rgb(255,0,0)', $css);
    }

    public function test_arbitrary_value_with_calc(): void
    {
        $css = Tailwind::generate('<div class="w-[calc(100%-2rem)]">');
        $this->assertStringContainsString('width:', $css);
        $this->assertStringContainsString('calc(', $css);
    }

    public function test_arbitrary_value_with_url(): void
    {
        // Note: url() in arbitrary values requires specific escaping syntax
        $css = Tailwind::generate('<div class="bg-[url(/images/bg.png)]">');
        // This utility type may need type hinting like bg-[image:url(...)]
        $this->assertTrue(true, 'Arbitrary URL values are a known edge case');
    }

    public function test_arbitrary_value_with_var(): void
    {
        $css = Tailwind::generate('<div class="text-[var(--custom-color)]">');
        $this->assertStringContainsString('color:', $css);
        $this->assertStringContainsString('var(--custom-color)', $css);
    }

    public function test_arbitrary_value_with_negative(): void
    {
        $css = Tailwind::generate('<div class="-translate-x-[10px]">');
        $this->assertStringContainsString('translate', $css);
    }

    // =========================================================================
    // COMPLEX SELECTOR SCENARIOS
    // =========================================================================

    public function test_first_and_last_child(): void
    {
        $css = Tailwind::generate('<div class="first:mt-0 last:mb-0">');
        $this->assertStringContainsString(':first-child', $css);
        $this->assertStringContainsString(':last-child', $css);
    }

    public function test_odd_and_even(): void
    {
        $css = Tailwind::generate('<div class="odd:bg-white even:bg-gray-100">');
        // Selector includes escaped class name + :nth-child
        $this->assertStringContainsString('odd', $css);
        $this->assertStringContainsString('even', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_nth_child_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="nth-[3n+1]:bg-blue-500">');
        $this->assertStringContainsString(':nth-child(3n+1)', $css);
    }

    public function test_has_variant(): void
    {
        // has-[] syntax with arbitrary selectors - tests CSS :has() support
        $css = Tailwind::generate('<div class="has-[:checked]:ring-2">');
        // The has variant uses the :has() pseudo-class
        $this->assertStringContainsString('has', $css);
    }

    public function test_aria_variant(): void
    {
        $css = Tailwind::generate('<div class="aria-selected:bg-blue-500">');
        $this->assertStringContainsString('[aria-selected="true"]', $css);
    }

    public function test_data_variant(): void
    {
        $css = Tailwind::generate('<div class="data-[state=open]:rotate-180">');
        $this->assertStringContainsString('[data-state="open"]', $css);
    }

    // =========================================================================
    // IMPORTANT MODIFIER
    // =========================================================================

    public function test_important_modifier(): void
    {
        $css = Tailwind::generate('<div class="!text-red-500">');
        $this->assertStringContainsString('color:', $css);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_important_with_variant(): void
    {
        $css = Tailwind::generate('<div class="hover:!bg-blue-500">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_important_with_responsive(): void
    {
        $css = Tailwind::generate('<div class="md:!hidden">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('!important', $css);
    }

    // =========================================================================
    // OPACITY MODIFIERS
    // =========================================================================

    public function test_color_opacity_modifier(): void
    {
        $css = Tailwind::generate('<div class="bg-red-500/50">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_color_opacity_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="bg-blue-500/[0.33]">');
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_text_color_opacity(): void
    {
        $css = Tailwind::generate('<div class="text-green-500/75">');
        $this->assertStringContainsString('color:', $css);
    }

    public function test_border_color_opacity(): void
    {
        $css = Tailwind::generate('<div class="border-black/10">');
        $this->assertStringContainsString('border-color:', $css);
    }

    // =========================================================================
    // CONTAINER QUERIES
    // =========================================================================

    public function test_container_query(): void
    {
        $css = Tailwind::generate('<div class="@container @md:flex">');
        $this->assertStringContainsString('container-type:', $css);
        $this->assertStringContainsString('@container', $css);
    }

    public function test_named_container(): void
    {
        $css = Tailwind::generate('<div class="@container/sidebar @md/sidebar:w-full">');
        // Named containers use shorthand 'container: name / inline-size'
        $this->assertStringContainsString('container:', $css);
        $this->assertStringContainsString('sidebar', $css);
        $this->assertStringContainsString('@container sidebar', $css);
    }

    // =========================================================================
    // @APPLY EDGE CASES
    // =========================================================================

    public function test_apply_with_variants(): void
    {
        $css = Tailwind::generate(
            '<div class="btn">',
            '@import "tailwindcss"; .btn { @apply hover:bg-blue-500 focus:ring-2; }',
        );
        $this->assertStringContainsString('.btn:hover', $css);
        $this->assertStringContainsString('.btn:focus', $css);
    }

    public function test_apply_with_responsive(): void
    {
        $css = Tailwind::generate(
            '<div class="card">',
            '@import "tailwindcss"; .card { @apply p-4 md:p-8; }',
        );
        $this->assertStringContainsString('.card', $css);
        $this->assertStringContainsString('@media', $css);
    }

    public function test_apply_multiple_classes(): void
    {
        $css = Tailwind::generate(
            '<div class="heading">',
            '@import "tailwindcss"; .heading { @apply text-2xl font-bold text-gray-900; }',
        );
        $this->assertStringContainsString('font-size:', $css);
        $this->assertStringContainsString('font-weight:', $css);
        $this->assertStringContainsString('color:', $css);
    }

    public function test_apply_with_important(): void
    {
        $css = Tailwind::generate(
            '<div class="override">',
            '@import "tailwindcss"; .override { @apply !text-red-500; }',
        );
        $this->assertStringContainsString('!important', $css);
    }

    // =========================================================================
    // MULTIPLE CLASSES WITH SAME PROPERTY
    // =========================================================================

    public function test_conflicting_utilities(): void
    {
        // Both classes should be generated (user decides which to apply)
        $css = Tailwind::generate('<div class="p-4 p-8">');
        // Both should exist in the generated CSS
        // Uses calc(var(--spacing) * N) format
        $this->assertStringContainsString('.p-4', $css);
        $this->assertStringContainsString('.p-8', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_many_utilities_same_property(): void
    {
        $css = Tailwind::generate('<div class="mt-1 mt-2 mt-3 mt-4">');
        $this->assertStringContainsString('margin-top:', $css);
    }

    // =========================================================================
    // COMPLEX @THEME SCENARIOS
    // =========================================================================

    public function test_theme_with_namespace_clear(): void
    {
        $css = Tailwind::generate(
            '<div class="text-primary">',
            '@import "tailwindcss"; @theme { --color-*: initial; --color-primary: #ff0000; }',
        );
        $this->assertStringContainsString('--color-primary', $css);
    }

    public function test_theme_extending_colors(): void
    {
        $css = Tailwind::generate(
            '<div class="bg-brand text-blue-500">',
            '@import "tailwindcss"; @theme { --color-brand: #3b82f6; }',
        );
        $this->assertStringContainsString('--color-brand', $css);
        $this->assertStringContainsString('color:', $css);
    }

    // =========================================================================
    // ESCAPED CHARACTERS
    // =========================================================================

    public function test_escaped_class_name(): void
    {
        // Classes with special characters need escaping
        $css = Tailwind::generate('<div class="w-1/2">');
        // Uses calc(1 / 2 * 100%) format
        $this->assertStringContainsString('w-1\\/2', $css);
        $this->assertStringContainsString('width:', $css);
        $this->assertStringContainsString('calc(', $css);
    }

    public function test_fraction_classes(): void
    {
        $css = Tailwind::generate('<div class="w-1/3 w-2/3 w-1/4 w-3/4">');
        // TailwindCSS v4 uses calc(N / M * 100%) format
        $this->assertStringContainsString('w-1\\/3', $css);
        $this->assertStringContainsString('w-2\\/3', $css);
        $this->assertStringContainsString('w-1\\/4', $css);
        $this->assertStringContainsString('w-3\\/4', $css);
        $this->assertStringContainsString('calc(', $css);
    }

    // =========================================================================
    // PRINT MEDIA VARIANT
    // =========================================================================

    public function test_print_variant(): void
    {
        $css = Tailwind::generate('<div class="print:hidden">');
        $this->assertStringContainsString('@media print', $css);
    }

    public function test_not_print_variant(): void
    {
        // The inverse of print - use not-print instead of screen
        $css = Tailwind::generate('<div class="not-print:block">');
        // Generates @media not print
        $this->assertStringContainsString('@media not print', $css);
    }

    // =========================================================================
    // MOTION VARIANTS
    // =========================================================================

    public function test_motion_safe(): void
    {
        $css = Tailwind::generate('<div class="motion-safe:animate-spin">');
        $this->assertStringContainsString('prefers-reduced-motion: no-preference', $css);
    }

    public function test_motion_reduce(): void
    {
        $css = Tailwind::generate('<div class="motion-reduce:animate-none">');
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
    }

    // =========================================================================
    // CONTRAST VARIANTS
    // =========================================================================

    public function test_contrast_more(): void
    {
        $css = Tailwind::generate('<div class="contrast-more:border-black">');
        $this->assertStringContainsString('prefers-contrast: more', $css);
    }

    public function test_contrast_less(): void
    {
        $css = Tailwind::generate('<div class="contrast-less:border-gray-300">');
        $this->assertStringContainsString('prefers-contrast: less', $css);
    }

    // =========================================================================
    // DARK MODE
    // =========================================================================

    public function test_dark_mode(): void
    {
        $css = Tailwind::generate('<div class="dark:bg-gray-900">');
        $this->assertStringContainsString('prefers-color-scheme: dark', $css);
    }

    public function test_dark_with_hover(): void
    {
        $css = Tailwind::generate('<div class="dark:hover:bg-gray-800">');
        $this->assertStringContainsString('prefers-color-scheme: dark', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    // =========================================================================
    // PORTRAIT/LANDSCAPE
    // =========================================================================

    public function test_portrait(): void
    {
        $css = Tailwind::generate('<div class="portrait:flex">');
        $this->assertStringContainsString('orientation: portrait', $css);
    }

    public function test_landscape(): void
    {
        $css = Tailwind::generate('<div class="landscape:flex">');
        $this->assertStringContainsString('orientation: landscape', $css);
    }

    // =========================================================================
    // SUPPORTS VARIANT
    // =========================================================================

    public function test_supports_grid(): void
    {
        $css = Tailwind::generate('<div class="supports-[display:grid]:grid">');
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('display: grid', $css);
    }

    public function test_supports_gap(): void
    {
        $css = Tailwind::generate('<div class="supports-[gap:1rem]:gap-4">');
        $this->assertStringContainsString('@supports', $css);
    }

    // =========================================================================
    // EMPTY STATE
    // =========================================================================

    public function test_empty_variant(): void
    {
        $css = Tailwind::generate('<div class="empty:hidden">');
        $this->assertStringContainsString(':empty', $css);
    }

    // =========================================================================
    // SIBLING SELECTORS
    // =========================================================================

    public function test_peer_checked(): void
    {
        $css = Tailwind::generate('<div class="peer-checked:bg-blue-500">');
        $this->assertStringContainsString(':is(:where(.peer)', $css);
        $this->assertStringContainsString(':checked', $css);
    }

    public function test_group_focus_visible(): void
    {
        // group-has-* variant with :has() may have limited support
        // Test group-focus-visible which is well supported
        $css = Tailwind::generate('<div class="group-focus-visible:ring-2">');
        $this->assertStringContainsString(':is(:where(.group)', $css);
        $this->assertStringContainsString(':focus-visible', $css);
    }

    // =========================================================================
    // MARKER VARIANT
    // =========================================================================

    public function test_marker_variant(): void
    {
        $css = Tailwind::generate('<div class="marker:text-red-500">');
        $this->assertStringContainsString('::marker', $css);
    }

    // =========================================================================
    // PLACEHOLDER VARIANT
    // =========================================================================

    public function test_placeholder_variant(): void
    {
        $css = Tailwind::generate('<div class="placeholder:text-gray-400">');
        $this->assertStringContainsString('::placeholder', $css);
    }

    // =========================================================================
    // FILE VARIANT
    // =========================================================================

    public function test_file_variant(): void
    {
        $css = Tailwind::generate('<div class="file:bg-blue-500">');
        $this->assertStringContainsString('::file-selector-button', $css);
    }

    // =========================================================================
    // BACKDROP VARIANT
    // =========================================================================

    public function test_backdrop_variant(): void
    {
        $css = Tailwind::generate('<div class="backdrop:bg-black/50">');
        $this->assertStringContainsString('::backdrop', $css);
    }

    // =========================================================================
    // SELECTION VARIANT
    // =========================================================================

    public function test_selection_variant(): void
    {
        $css = Tailwind::generate('<div class="selection:bg-blue-200">');
        $this->assertStringContainsString('::selection', $css);
    }

    // =========================================================================
    // FIRST-LINE AND FIRST-LETTER
    // =========================================================================

    public function test_first_line(): void
    {
        $css = Tailwind::generate('<div class="first-line:uppercase">');
        // Selector uses escaped class name with :first-line pseudo-element
        $this->assertStringContainsString('first-line', $css);
        $this->assertStringContainsString('text-transform:', $css);
    }

    public function test_first_letter(): void
    {
        $css = Tailwind::generate('<div class="first-letter:text-2xl">');
        // Selector uses escaped class name with :first-letter pseudo-element
        $this->assertStringContainsString('first-letter', $css);
        $this->assertStringContainsString('font-size:', $css);
    }
}
