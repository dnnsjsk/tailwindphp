<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;
use TailwindPHP\TailwindCompiler;
use TailwindPHP\tw;

/**
 * Tests for the TailwindPHP public API.
 *
 * Covers:
 * - Tailwind::generate() / tw::generate()
 * - Tailwind::extractCandidates() / tw::extractCandidates()
 * - Tailwind::compile() / tw::compile() - returns TailwindCompiler
 * - Tailwind::properties() / tw::properties()
 * - Tailwind::computedProperties() / tw::computedProperties()
 * - Tailwind::value() / tw::value()
 * - Tailwind::computedValue() / tw::computedValue()
 * - TailwindCompiler instance methods
 */
class ApiTest extends TestCase
{
    // ==================================================
    // Tailwind::generate()
    // ==================================================

    public function test_generate_with_string_input(): void
    {
        $css = Tailwind::generate('<div class="flex">');
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_generate_with_array_input(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_generate_with_custom_css(): void
    {
        $css = Tailwind::generate('<div class="flex">', '@import "tailwindcss/utilities.css";');
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_generate_with_array_and_custom_css(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '@import "tailwindcss/utilities.css"; @theme { --color-brand: #ff0000; }',
        ]);
        $this->assertStringContainsString('--color-brand', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_generate_with_empty_content(): void
    {
        $css = Tailwind::generate('');
        // Should not throw, may return empty or minimal CSS
        $this->assertIsString($css);
    }

    public function test_generate_with_no_tailwind_classes(): void
    {
        $css = Tailwind::generate('<div class="my-custom-class">');
        // Should not throw, custom classes are ignored
        $this->assertIsString($css);
    }

    public function test_generate_multiple_classes(): void
    {
        $css = Tailwind::generate('<div class="flex items-center justify-between p-4 m-2">');
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('align-items: center', $css);
        $this->assertStringContainsString('justify-content: space-between', $css);
        $this->assertStringContainsString('padding:', $css);
        $this->assertStringContainsString('margin:', $css);
    }

    // ==================================================
    // tw:: alias
    // ==================================================

    public function test_tw_alias_works(): void
    {
        $css = tw::generate('<div class="flex">');
        $this->assertStringContainsString('display: flex', $css);
    }

    // ==================================================
    // Tailwind::extractCandidates()
    // ==================================================

    public function test_extract_from_class_attribute(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="flex p-4">');
        $this->assertContains('flex', $candidates);
        $this->assertContains('p-4', $candidates);
    }

    public function test_extract_from_className_jsx(): void
    {
        $candidates = Tailwind::extractCandidates('<div className="flex p-4">');
        $this->assertContains('flex', $candidates);
        $this->assertContains('p-4', $candidates);
    }

    public function test_extract_with_single_quotes(): void
    {
        $candidates = Tailwind::extractCandidates("<div class='flex p-4'>");
        $this->assertContains('flex', $candidates);
        $this->assertContains('p-4', $candidates);
    }

    public function test_extract_deduplicates(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="flex flex p-4 p-4">');
        $this->assertCount(2, $candidates);
    }

    public function test_extract_from_multiple_elements(): void
    {
        $html = '<div class="flex"><span class="text-red-500"><p class="m-4">';
        $candidates = Tailwind::extractCandidates($html);
        $this->assertContains('flex', $candidates);
        $this->assertContains('text-red-500', $candidates);
        $this->assertContains('m-4', $candidates);
    }

    public function test_extract_handles_variants(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="hover:bg-blue-500 md:flex dark:text-white">');
        $this->assertContains('hover:bg-blue-500', $candidates);
        $this->assertContains('md:flex', $candidates);
        $this->assertContains('dark:text-white', $candidates);
    }

    public function test_extract_handles_arbitrary_values(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="w-[300px] bg-[#ff0000]">');
        $this->assertContains('w-[300px]', $candidates);
        $this->assertContains('bg-[#ff0000]', $candidates);
    }

    public function test_extract_handles_negative_values(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="-mt-4 -translate-x-1/2">');
        $this->assertContains('-mt-4', $candidates);
        $this->assertContains('-translate-x-1/2', $candidates);
    }

    public function test_extract_handles_important(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="!flex !p-4">');
        $this->assertContains('!flex', $candidates);
        $this->assertContains('!p-4', $candidates);
    }

    public function test_extract_handles_fractions(): void
    {
        $candidates = Tailwind::extractCandidates('<div class="w-1/2 h-2/3">');
        $this->assertContains('w-1/2', $candidates);
        $this->assertContains('h-2/3', $candidates);
    }

    // ==================================================
    // Tailwind::compile() - returns TailwindCompiler instance
    // ==================================================

    public function test_compile_returns_tailwind_compiler(): void
    {
        $compiler = Tailwind::compile('@import "tailwindcss";');
        $this->assertInstanceOf(TailwindCompiler::class, $compiler);
    }

    public function test_compile_css_generates_css(): void
    {
        $compiler = Tailwind::compile('@import "tailwindcss";');
        $css = $compiler->css(['flex', 'p-4']);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_compile_generate_from_html(): void
    {
        $compiler = Tailwind::compile('@import "tailwindcss";');
        $css = $compiler->generate('<div class="flex p-4">');
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_compile_incremental_builds(): void
    {
        $compiler = Tailwind::compile('@import "tailwindcss";');

        // First build
        $css1 = $compiler->css(['flex']);
        $this->assertStringContainsString('.flex {', $css1);
        $this->assertStringNotContainsString('.p-4 {', $css1);

        // Second build with additional classes - incremental
        $css2 = $compiler->css(['flex', 'p-4']);
        $this->assertStringContainsString('.flex {', $css2);
        $this->assertStringContainsString('.p-4 {', $css2);
    }

    public function test_compile_with_theme(): void
    {
        $compiler = Tailwind::compile('@import "tailwindcss"; @theme { --color-brand: #3b82f6; }');
        $css = $compiler->css(['bg-brand']);
        $this->assertStringContainsString('--color-brand', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    // ==================================================
    // Tailwind::properties() - static method
    // ==================================================

    public function test_properties_with_string(): void
    {
        $props = Tailwind::properties('p-4');
        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('calc(var(--spacing) * 4)', $props['padding']);
    }

    public function test_properties_with_string_and_css(): void
    {
        $props = Tailwind::properties('bg-brand', '@import "tailwindcss"; @theme { --color-brand: #ff0000; }');
        $this->assertArrayHasKey('background-color', $props);
    }

    public function test_properties_with_array_content(): void
    {
        $props = Tailwind::properties([
            'content' => 'p-4',
        ]);
        $this->assertArrayHasKey('padding', $props);
    }

    public function test_properties_with_array_content_and_css(): void
    {
        $props = Tailwind::properties([
            'content' => 'bg-brand',
            'css' => '@import "tailwindcss"; @theme { --color-brand: #ff0000; }',
        ]);
        $this->assertArrayHasKey('background-color', $props);
    }

    public function test_properties_multiple_utilities(): void
    {
        $props = Tailwind::properties(['p-4', 'm-2']);
        $this->assertArrayHasKey('padding', $props);
        $this->assertArrayHasKey('margin', $props);
    }

    public function test_properties_flex(): void
    {
        $props = Tailwind::properties('flex');
        $this->assertArrayHasKey('display', $props);
        $this->assertSame('flex', $props['display']);
    }

    public function test_properties_text_color(): void
    {
        $props = Tailwind::properties('text-red-500');
        $this->assertArrayHasKey('color', $props);
    }

    public function test_properties_invalid_utility(): void
    {
        $props = Tailwind::properties('not-a-utility');
        $this->assertEmpty($props);
    }

    // ==================================================
    // Tailwind::computedProperties() - static method
    // ==================================================

    public function test_computed_properties_with_string(): void
    {
        $props = Tailwind::computedProperties('p-4');
        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('1rem', $props['padding']);
    }

    public function test_computed_properties_with_string_and_css(): void
    {
        $props = Tailwind::computedProperties('p-4', '@import "tailwindcss";');
        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('1rem', $props['padding']);
    }

    public function test_computed_properties_with_array_content(): void
    {
        $props = Tailwind::computedProperties([
            'content' => 'p-4',
        ]);
        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('1rem', $props['padding']);
    }

    public function test_computed_properties_multiple(): void
    {
        $props = Tailwind::computedProperties(['p-4', 'm-2']);
        $this->assertSame('1rem', $props['padding']);
        $this->assertSame('0.5rem', $props['margin']);
    }

    public function test_computed_properties_flex(): void
    {
        $props = Tailwind::computedProperties('flex');
        $this->assertSame('flex', $props['display']);
    }

    // ==================================================
    // Tailwind::value() - static method
    // ==================================================

    public function test_value_with_string(): void
    {
        $value = Tailwind::value('p-4');
        $this->assertSame('calc(var(--spacing) * 4)', $value);
    }

    public function test_value_with_string_and_css(): void
    {
        $value = Tailwind::value('p-4', '@import "tailwindcss";');
        $this->assertSame('calc(var(--spacing) * 4)', $value);
    }

    public function test_value_with_array_content(): void
    {
        $value = Tailwind::value([
            'content' => 'p-4',
        ]);
        $this->assertSame('calc(var(--spacing) * 4)', $value);
    }

    public function test_value_flex(): void
    {
        $value = Tailwind::value('flex');
        $this->assertSame('flex', $value);
    }

    public function test_value_invalid_utility(): void
    {
        $value = Tailwind::value('not-a-utility');
        $this->assertNull($value);
    }

    public function test_value_opacity(): void
    {
        $value = Tailwind::value('opacity-50');
        $this->assertSame('.5', $value);
    }

    // ==================================================
    // Tailwind::computedValue() - static method
    // ==================================================

    public function test_computed_value_with_string(): void
    {
        $value = Tailwind::computedValue('p-4');
        $this->assertSame('1rem', $value);
    }

    public function test_computed_value_with_string_and_css(): void
    {
        $value = Tailwind::computedValue('p-4', '@import "tailwindcss";');
        $this->assertSame('1rem', $value);
    }

    public function test_computed_value_with_array_content(): void
    {
        $value = Tailwind::computedValue([
            'content' => 'p-4',
        ]);
        $this->assertSame('1rem', $value);
    }

    public function test_computed_value_flex(): void
    {
        $value = Tailwind::computedValue('flex');
        $this->assertSame('flex', $value);
    }

    public function test_computed_value_invalid_utility(): void
    {
        $value = Tailwind::computedValue('not-a-utility');
        $this->assertNull($value);
    }

    public function test_computed_value_m2(): void
    {
        $value = Tailwind::computedValue('m-2');
        $this->assertSame('0.5rem', $value);
    }

    public function test_computed_value_various_spacing(): void
    {
        $this->assertSame('0.25rem', Tailwind::computedValue('p-1'));
        $this->assertSame('0.5rem', Tailwind::computedValue('p-2'));
        $this->assertSame('0.75rem', Tailwind::computedValue('p-3'));
        $this->assertSame('1rem', Tailwind::computedValue('p-4'));
        $this->assertSame('2rem', Tailwind::computedValue('p-8'));
    }

    // ==================================================
    // TailwindCompiler instance methods
    // ==================================================

    public function test_compiler_properties(): void
    {
        $compiler = tw::compile();
        $props = $compiler->properties('p-4');
        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('calc(var(--spacing) * 4)', $props['padding']);
    }

    public function test_compiler_computed_properties(): void
    {
        $compiler = tw::compile();
        $props = $compiler->computedProperties('p-4');
        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('1rem', $props['padding']);
    }

    public function test_compiler_value(): void
    {
        $compiler = tw::compile();
        $value = $compiler->value('p-4');
        $this->assertSame('calc(var(--spacing) * 4)', $value);
    }

    public function test_compiler_computed_value(): void
    {
        $compiler = tw::compile();
        $value = $compiler->computedValue('p-4');
        $this->assertSame('1rem', $value);
    }

    public function test_compiler_reuse(): void
    {
        $compiler = tw::compile();

        // Multiple calls on same compiler instance
        $this->assertSame('1rem', $compiler->computedValue('p-4'));
        $this->assertSame('0.5rem', $compiler->computedValue('m-2'));
        $this->assertSame('flex', $compiler->computedValue('flex'));

        $props = $compiler->computedProperties(['p-4', 'm-2']);
        $this->assertSame('1rem', $props['padding']);
        $this->assertSame('0.5rem', $props['margin']);
    }

    public function test_compiler_with_custom_theme(): void
    {
        $compiler = tw::compile('@import "tailwindcss"; @theme { --spacing: 0.5rem; }');

        // With doubled spacing (0.5rem base), p-4 should be 2rem
        $value = $compiler->computedValue('p-4');
        $this->assertSame('2rem', $value);
    }

    public function test_compiler_extract_candidates(): void
    {
        $compiler = tw::compile();
        $candidates = $compiler->extractCandidates('<div class="flex p-4">');
        $this->assertContains('flex', $candidates);
        $this->assertContains('p-4', $candidates);
    }

    public function test_compiler_minify(): void
    {
        $compiler = tw::compile();
        $css = ".test { color: red; }\n\n.foo { display: flex; }";
        $minified = $compiler->minify($css);
        $this->assertStringNotContainsString("\n\n", $minified);
    }

    public function test_compiler_properties_multiple(): void
    {
        $compiler = tw::compile();
        $props = $compiler->properties(['p-4', 'm-2']);
        $this->assertArrayHasKey('padding', $props);
        $this->assertArrayHasKey('margin', $props);
    }

    public function test_compiler_value_invalid(): void
    {
        $compiler = tw::compile();
        $value = $compiler->value('not-a-utility');
        $this->assertNull($value);
    }

    public function test_compiler_computed_value_invalid(): void
    {
        $compiler = tw::compile();
        $value = $compiler->computedValue('not-a-utility');
        $this->assertNull($value);
    }

    public function test_compiler_generate(): void
    {
        $compiler = tw::compile();
        $css = $compiler->generate('<div class="flex p-4">');
        $this->assertStringContainsString('.flex {', $css);
        $this->assertStringContainsString('.p-4 {', $css);
    }

    public function test_compiler_css(): void
    {
        $compiler = tw::compile();
        $css = $compiler->css(['flex', 'p-4']);
        $this->assertStringContainsString('.flex {', $css);
        $this->assertStringContainsString('.p-4 {', $css);
    }

    // ==================================================
    // Edge cases and special values
    // ==================================================

    public function test_arbitrary_value(): void
    {
        $props = Tailwind::properties('w-[300px]');
        $this->assertArrayHasKey('width', $props);
        $this->assertSame('300px', $props['width']);
    }

    public function test_arbitrary_computed(): void
    {
        $value = Tailwind::computedValue('w-[300px]');
        $this->assertSame('300px', $value);
    }

    public function test_negative_value(): void
    {
        $value = Tailwind::value('-m-4');
        $this->assertSame('calc(var(--spacing) * -4)', $value);
    }

    public function test_negative_computed(): void
    {
        // Negative values may stay as calc expressions
        $value = Tailwind::computedValue('-m-4');
        // Either -1rem or the calc expression is acceptable
        $this->assertNotNull($value);
    }

    public function test_fraction_value(): void
    {
        $value = Tailwind::value('w-1/2');
        $this->assertSame('calc(1 / 2 * 100%)', $value);
    }

    public function test_fraction_computed(): void
    {
        $value = Tailwind::computedValue('w-1/2');
        // Fractions stay as calc expressions
        $this->assertSame('calc(1 / 2 * 100%)', $value);
    }

    public function test_opacity_value(): void
    {
        $value = Tailwind::value('opacity-50');
        $this->assertSame('.5', $value);
    }

    public function test_opacity_computed(): void
    {
        $value = Tailwind::computedValue('opacity-50');
        $this->assertSame('.5', $value);
    }

    public function test_color_utility(): void
    {
        $props = Tailwind::properties('bg-blue-500');
        $this->assertArrayHasKey('background-color', $props);
    }

    public function test_multiple_properties_utility(): void
    {
        // inset generates top, right, bottom, left
        $props = Tailwind::properties('inset-0');
        $this->assertArrayHasKey('inset', $props);
    }

    public function test_border_utility(): void
    {
        $props = Tailwind::properties('border');
        $this->assertArrayHasKey('border-style', $props);
    }

    public function test_rounded_utility(): void
    {
        $props = Tailwind::properties('rounded');
        $this->assertArrayHasKey('border-radius', $props);
    }

    // ==================================================
    // tw:: static alias for all methods
    // ==================================================

    public function test_tw_properties(): void
    {
        $props = tw::properties('p-4');
        $this->assertArrayHasKey('padding', $props);
    }

    public function test_tw_computed_properties(): void
    {
        $props = tw::computedProperties('p-4');
        $this->assertSame('1rem', $props['padding']);
    }

    public function test_tw_value(): void
    {
        $value = tw::value('p-4');
        $this->assertSame('calc(var(--spacing) * 4)', $value);
    }

    public function test_tw_computed_value(): void
    {
        $value = tw::computedValue('p-4');
        $this->assertSame('1rem', $value);
    }

    public function test_tw_compile(): void
    {
        $compiler = tw::compile();
        $this->assertInstanceOf(TailwindCompiler::class, $compiler);
    }

    public function test_tw_extract_candidates(): void
    {
        $candidates = tw::extractCandidates('<div class="flex">');
        $this->assertContains('flex', $candidates);
    }

    public function test_tw_minify(): void
    {
        $minified = tw::minify('.test { color: red; }');
        $this->assertIsString($minified);
    }

    // ==================================================
    // Edge cases: Multi-property utilities
    // ==================================================

    public function test_properties_shadow_excludes_at_property_declarations(): void
    {
        $props = tw::properties('shadow');

        // Should NOT contain @property internal declarations
        $this->assertArrayNotHasKey('syntax', $props);
        $this->assertArrayNotHasKey('inherits', $props);
        $this->assertArrayNotHasKey('initial-value', $props);

        // Should contain actual CSS properties
        $this->assertArrayHasKey('--tw-shadow', $props);
        $this->assertArrayHasKey('box-shadow', $props);
    }

    public function test_properties_shadow_lg_excludes_at_property_declarations(): void
    {
        $props = tw::properties('shadow-lg');

        $this->assertArrayNotHasKey('syntax', $props);
        $this->assertArrayNotHasKey('inherits', $props);
        $this->assertArrayNotHasKey('initial-value', $props);
        $this->assertArrayHasKey('--tw-shadow', $props);
        $this->assertArrayHasKey('box-shadow', $props);
    }

    public function test_properties_ring_excludes_at_property_declarations(): void
    {
        $props = tw::properties('ring');

        $this->assertArrayNotHasKey('syntax', $props);
        $this->assertArrayNotHasKey('inherits', $props);
        $this->assertArrayNotHasKey('initial-value', $props);
        $this->assertArrayHasKey('--tw-ring-shadow', $props);
        $this->assertArrayHasKey('box-shadow', $props);
    }

    public function test_value_shadow_returns_box_shadow_not_variable(): void
    {
        $value = tw::value('shadow');

        // Should return box-shadow value, not --tw-shadow variable value
        $this->assertStringContainsString('var(--tw-shadow)', $value);
        $this->assertStringNotContainsString('"*"', $value);
    }

    public function test_value_shadow_lg_returns_box_shadow(): void
    {
        $value = tw::value('shadow-lg');

        $this->assertStringContainsString('var(--tw-shadow)', $value);
    }

    public function test_value_ring_returns_box_shadow(): void
    {
        $value = tw::value('ring');

        $this->assertStringContainsString('var(--tw-ring-shadow)', $value);
    }

    // ==================================================
    // Edge cases: Color utilities
    // ==================================================

    public function test_properties_bg_color(): void
    {
        $props = tw::properties('bg-blue-500');

        $this->assertArrayHasKey('background-color', $props);
        $this->assertSame('var(--color-blue-500)', $props['background-color']);
    }

    public function test_computed_properties_bg_color(): void
    {
        $props = tw::computedProperties('bg-blue-500');

        $this->assertArrayHasKey('background-color', $props);
        // oklch color format
        $this->assertStringContainsString('oklch', $props['background-color']);
    }

    public function test_value_bg_color(): void
    {
        $value = tw::value('bg-blue-500');

        $this->assertSame('var(--color-blue-500)', $value);
    }

    public function test_computed_value_bg_color(): void
    {
        $value = tw::computedValue('bg-blue-500');

        $this->assertStringContainsString('oklch', $value);
    }

    public function test_properties_text_color_returns_css_variable(): void
    {
        $props = tw::properties('text-red-500');

        $this->assertArrayHasKey('color', $props);
        $this->assertSame('var(--color-red-500)', $props['color']);
    }

    public function test_computed_properties_text_color_resolves_to_oklch(): void
    {
        $props = tw::computedProperties('text-red-500');

        $this->assertArrayHasKey('color', $props);
        $this->assertStringContainsString('oklch', $props['color']);
    }

    public function test_properties_border_color_returns_css_variable(): void
    {
        $props = tw::properties('border-green-500');

        $this->assertArrayHasKey('border-color', $props);
        $this->assertSame('var(--color-green-500)', $props['border-color']);
    }

    // ==================================================
    // Edge cases: Typography utilities with multiple properties
    // ==================================================

    public function test_properties_text_lg(): void
    {
        $props = tw::properties('text-lg');

        $this->assertArrayHasKey('font-size', $props);
        $this->assertArrayHasKey('line-height', $props);
    }

    public function test_value_text_lg_returns_font_size(): void
    {
        $value = tw::value('text-lg');

        // Should return font-size, not line-height
        $this->assertSame('var(--text-lg)', $value);
    }

    public function test_computed_properties_text_lg(): void
    {
        $props = tw::computedProperties('text-lg');

        $this->assertArrayHasKey('font-size', $props);
        // font-size should be resolved
        $this->assertStringContainsString('rem', $props['font-size']);
    }

    // ==================================================
    // Edge cases: Border utilities
    // ==================================================

    public function test_properties_border(): void
    {
        $props = tw::properties('border');

        $this->assertArrayNotHasKey('syntax', $props);
        $this->assertArrayNotHasKey('inherits', $props);
        $this->assertArrayNotHasKey('initial-value', $props);
        $this->assertArrayHasKey('border-width', $props);
    }

    public function test_properties_rounded(): void
    {
        $props = tw::properties('rounded');

        $this->assertArrayHasKey('border-radius', $props);
    }

    public function test_properties_rounded_lg(): void
    {
        $props = tw::properties('rounded-lg');

        $this->assertArrayHasKey('border-radius', $props);
        $this->assertSame('var(--radius-lg)', $props['border-radius']);
    }

    // ==================================================
    // Edge cases: Display and layout utilities
    // ==================================================

    public function test_properties_flex_returns_single_property(): void
    {
        $props = tw::properties('flex');

        $this->assertSame(['display' => 'flex'], $props);
    }

    public function test_properties_grid_returns_single_property(): void
    {
        $props = tw::properties('grid');

        $this->assertSame(['display' => 'grid'], $props);
    }

    public function test_properties_hidden_returns_single_property(): void
    {
        $props = tw::properties('hidden');

        $this->assertSame(['display' => 'none'], $props);
    }

    public function test_properties_block_returns_single_property(): void
    {
        $props = tw::properties('block');

        $this->assertSame(['display' => 'block'], $props);
    }

    // ==================================================
    // Edge cases: Gap and spacing
    // ==================================================

    public function test_properties_gap(): void
    {
        $props = tw::properties('gap-4');

        $this->assertArrayHasKey('gap', $props);
        $this->assertSame('calc(var(--spacing) * 4)', $props['gap']);
    }

    public function test_computed_properties_gap(): void
    {
        $props = tw::computedProperties('gap-4');

        $this->assertArrayHasKey('gap', $props);
        $this->assertSame('1rem', $props['gap']);
    }

    // ==================================================
    // Edge cases: Transform utilities (multiple CSS variable properties)
    // ==================================================

    public function test_properties_translate_x(): void
    {
        $props = tw::properties('translate-x-4');

        // Should have --tw-translate-x and translate
        $this->assertArrayHasKey('--tw-translate-x', $props);
        $this->assertArrayHasKey('translate', $props);
    }

    public function test_value_translate_x_returns_translate_not_variable(): void
    {
        $value = tw::value('translate-x-4');

        // Should return translate value, not the CSS variable value
        $this->assertStringContainsString('var(--tw-translate-x)', $value);
    }

    // ==================================================
    // Edge cases: Width and height
    // ==================================================

    public function test_properties_w_full(): void
    {
        $props = tw::properties('w-full');

        $this->assertSame(['width' => '100%'], $props);
    }

    public function test_properties_h_screen(): void
    {
        $props = tw::properties('h-screen');

        $this->assertSame(['height' => '100vh'], $props);
    }

    public function test_properties_w_4(): void
    {
        $props = tw::properties('w-4');

        $this->assertArrayHasKey('width', $props);
        $this->assertSame('calc(var(--spacing) * 4)', $props['width']);
    }

    public function test_computed_properties_w_4(): void
    {
        $props = tw::computedProperties('w-4');

        $this->assertSame('1rem', $props['width']);
    }

    // ==================================================
    // Edge cases: Opacity
    // ==================================================

    public function test_properties_opacity(): void
    {
        $props = tw::properties('opacity-50');

        $this->assertSame(['opacity' => '.5'], $props);
    }

    public function test_value_opacity_75(): void
    {
        $value = tw::value('opacity-75');

        $this->assertSame('.75', $value);
    }

    // ==================================================
    // Edge cases: Z-index
    // ==================================================

    public function test_properties_z_index(): void
    {
        $props = tw::properties('z-10');

        $this->assertSame(['z-index' => '10'], $props);
    }

    public function test_properties_z_50(): void
    {
        $props = tw::properties('z-50');

        $this->assertSame(['z-index' => '50'], $props);
    }

    // ==================================================
    // Edge cases: Inset utilities
    // ==================================================

    public function test_properties_inset_0(): void
    {
        $props = tw::properties('inset-0');

        $this->assertArrayHasKey('inset', $props);
        $this->assertSame('calc(var(--spacing) * 0)', $props['inset']);
    }

    public function test_computed_properties_inset_0(): void
    {
        $props = tw::computedProperties('inset-0');

        $this->assertSame('0rem', $props['inset']);
    }

    // ==================================================
    // Edge cases: Arbitrary values
    // ==================================================

    public function test_properties_arbitrary_padding(): void
    {
        $props = tw::properties('p-[20px]');

        $this->assertArrayHasKey('padding', $props);
        $this->assertSame('20px', $props['padding']);
    }

    public function test_properties_arbitrary_color(): void
    {
        $props = tw::properties('bg-[#ff0000]');

        $this->assertArrayHasKey('background-color', $props);
        $this->assertSame('#ff0000', $props['background-color']);
    }

    // ==================================================
    // Theme value accessors
    // ==================================================

    public function test_colors_static(): void
    {
        $colors = tw::colors();

        $this->assertIsArray($colors);
        $this->assertArrayHasKey('red-500', $colors);
        $this->assertArrayHasKey('blue-500', $colors);
        $this->assertArrayHasKey('white', $colors);
        $this->assertArrayHasKey('black', $colors);
    }

    public function test_colors_compiler_instance(): void
    {
        $compiler = tw::compile();
        $colors = $compiler->colors();

        $this->assertIsArray($colors);
        $this->assertArrayHasKey('red-500', $colors);
        $this->assertArrayHasKey('blue-500', $colors);
    }

    public function test_colors_with_custom_theme(): void
    {
        $colors = tw::colors('@import "tailwindcss"; @theme { --color-brand: #3b82f6; }');

        $this->assertArrayHasKey('brand', $colors);
        $this->assertSame('#3b82f6', $colors['brand']);
    }

    public function test_breakpoints_static(): void
    {
        $breakpoints = tw::breakpoints();

        $this->assertIsArray($breakpoints);
        $this->assertArrayHasKey('sm', $breakpoints);
        $this->assertArrayHasKey('md', $breakpoints);
        $this->assertArrayHasKey('lg', $breakpoints);
        $this->assertArrayHasKey('xl', $breakpoints);
        $this->assertArrayHasKey('2xl', $breakpoints);
    }

    public function test_breakpoints_compiler_instance(): void
    {
        $compiler = tw::compile();
        $breakpoints = $compiler->breakpoints();

        $this->assertIsArray($breakpoints);
        $this->assertArrayHasKey('sm', $breakpoints);
        $this->assertSame('40rem', $breakpoints['sm']);
    }

    public function test_breakpoints_with_custom_theme(): void
    {
        $breakpoints = tw::breakpoints('@import "tailwindcss"; @theme { --breakpoint-xs: 20rem; }');

        $this->assertArrayHasKey('xs', $breakpoints);
        $this->assertSame('20rem', $breakpoints['xs']);
    }

    public function test_spacing_static(): void
    {
        $spacing = tw::spacing();

        // TailwindCSS 4 uses a single --spacing base value, not --spacing-* namespace
        // So default theme returns empty array
        $this->assertIsArray($spacing);
    }

    public function test_spacing_compiler_instance(): void
    {
        $compiler = tw::compile();
        $spacing = $compiler->spacing();

        $this->assertIsArray($spacing);
    }

    public function test_spacing_with_custom_theme(): void
    {
        $spacing = tw::spacing('@import "tailwindcss"; @theme { --spacing-huge: 10rem; }');

        $this->assertArrayHasKey('huge', $spacing);
        $this->assertSame('10rem', $spacing['huge']);
    }

    // ==================================================
    // LightningCSS optimization in computed values
    // ==================================================

    public function test_computed_value_color_with_opacity(): void
    {
        // color-mix should be evaluated to oklch with alpha
        $value = tw::computedValue('bg-red-500/50');
        $this->assertSame('oklch(63.7% .237 25.331 / .5)', $value);
    }

    public function test_computed_value_color_without_opacity(): void
    {
        $value = tw::computedValue('bg-blue-500');
        $this->assertSame('oklch(62.3% .214 259.815)', $value);
    }

    public function test_computed_value_duration_normalized(): void
    {
        // 500ms should become .5s
        $value = tw::computedValue('duration-500');
        $this->assertSame('.5s', $value);
    }

    public function test_computed_value_leading_zero_removed(): void
    {
        // 0.5 should become .5
        $value = tw::computedValue('opacity-50');
        $this->assertSame('.5', $value);
    }

    public function test_computed_value_text_size(): void
    {
        // 0.875rem should become .875rem
        $value = tw::computedValue('text-sm');
        $this->assertSame('.875rem', $value);
    }

    public function test_computed_properties_color_with_opacity(): void
    {
        $props = tw::computedProperties('bg-red-500/50');
        $this->assertSame('oklch(63.7% .237 25.331 / .5)', $props['background-color']);
    }

    public function test_computed_properties_multiple_optimized(): void
    {
        $props = tw::computedProperties(['text-sm', 'opacity-75']);
        $this->assertSame('.875rem', $props['font-size']);
        $this->assertSame('.75', $props['opacity']);
    }

    public function test_properties_raw_not_optimized(): void
    {
        // Raw properties should NOT be optimized (they contain CSS variables)
        $props = tw::properties('p-4');
        $this->assertSame('calc(var(--spacing) * 4)', $props['padding']);
    }

    public function test_value_raw_not_optimized(): void
    {
        // Raw value should NOT be optimized
        $value = tw::value('p-4');
        $this->assertSame('calc(var(--spacing) * 4)', $value);
    }

    public function test_computed_value_spacing_resolved(): void
    {
        // calc(var(--spacing) * 4) should resolve to 1rem
        $value = tw::computedValue('p-4');
        $this->assertSame('1rem', $value);
    }

    public function test_compiler_computed_value_optimized(): void
    {
        $compiler = tw::compile();
        $value = $compiler->computedValue('bg-green-500/25');
        $this->assertSame('oklch(72.3% .219 149.579 / .25)', $value);
    }
}
