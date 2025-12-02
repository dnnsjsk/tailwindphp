<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Tests for the TailwindPHP public API.
 */
class ApiTest extends TestCase
{
    // =========================================================================
    // Tailwind::generate()
    // =========================================================================

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
        $css = Tailwind::generate('<div class="flex">', '@tailwind utilities;');
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_generate_with_array_and_custom_css(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '@tailwind utilities; @theme { --color-brand: #ff0000; }',
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

    // =========================================================================
    // Tailwind::extractCandidates()
    // =========================================================================

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

    // =========================================================================
    // Tailwind::compile()
    // =========================================================================

    public function test_compile_returns_build_function(): void
    {
        $result = Tailwind::compile('@tailwind utilities;');
        $this->assertIsArray($result);
        $this->assertArrayHasKey('build', $result);
        $this->assertIsCallable($result['build']);
    }

    public function test_compile_build_generates_css(): void
    {
        $result = Tailwind::compile('@tailwind utilities;');
        $css = $result['build'](['flex', 'p-4']);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_compile_incremental_builds(): void
    {
        $result = Tailwind::compile('@tailwind utilities;');

        // First build
        $css1 = $result['build'](['flex']);
        $this->assertStringContainsString('display: flex', $css1);
        $this->assertStringNotContainsString('padding:', $css1);

        // Second build with additional classes
        $css2 = $result['build'](['flex', 'p-4']);
        $this->assertStringContainsString('display: flex', $css2);
        $this->assertStringContainsString('padding:', $css2);
    }

    public function test_compile_with_theme(): void
    {
        $result = Tailwind::compile('@tailwind utilities; @theme { --color-brand: #3b82f6; }');
        $css = $result['build'](['bg-brand']);
        $this->assertStringContainsString('--color-brand', $css);
        $this->assertStringContainsString('background-color:', $css);
    }
}
