<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Sizing Utilities Tests
 *
 * Port of sizing tests from: packages/tailwindcss/src/utilities.test.ts
 *
 * Includes:
 * - width (w, min-w, max-w)
 * - height (h, min-h, max-h)
 * - size
 */
class sizing extends TestCase
{
    // =========================================================================
    // Width utilities
    // =========================================================================

    #[Test]
    public function width_static_values(): void
    {
        $css = TestHelper::run([
            'w-full', 'w-auto', 'w-screen', 'w-svw', 'w-lvw', 'w-dvw',
            'w-min', 'w-max', 'w-fit'
        ]);

        $this->assertStringContainsString('.w-auto {', $css);
        $this->assertStringContainsString('width: auto;', $css);

        $this->assertStringContainsString('.w-dvw {', $css);
        $this->assertStringContainsString('width: 100dvw;', $css);

        $this->assertStringContainsString('.w-fit {', $css);
        $this->assertStringContainsString('width: fit-content;', $css);

        $this->assertStringContainsString('.w-full {', $css);
        $this->assertStringContainsString('width: 100%;', $css);

        $this->assertStringContainsString('.w-lvw {', $css);
        $this->assertStringContainsString('width: 100lvw;', $css);

        $this->assertStringContainsString('.w-max {', $css);
        $this->assertStringContainsString('width: max-content;', $css);

        $this->assertStringContainsString('.w-min {', $css);
        $this->assertStringContainsString('width: min-content;', $css);

        $this->assertStringContainsString('.w-screen {', $css);
        $this->assertStringContainsString('width: 100vw;', $css);

        $this->assertStringContainsString('.w-svw {', $css);
        $this->assertStringContainsString('width: 100svw;', $css);
    }

    #[Test]
    public function width_with_spacing(): void
    {
        $css = TestHelper::run(['w-4']);

        $this->assertStringContainsString('.w-4 {', $css);
        $this->assertStringContainsString('width: var(--spacing-4);', $css);
    }

    #[Test]
    public function width_with_fractions(): void
    {
        $css = TestHelper::run(['w-1/2', 'w-1/3', 'w-2/3']);

        $this->assertStringContainsString('.w-1\\/2 {', $css);
        $this->assertStringContainsString('width: calc(1/2 * 100%);', $css);

        $this->assertStringContainsString('.w-1\\/3 {', $css);
        $this->assertStringContainsString('width: calc(1/3 * 100%);', $css);

        $this->assertStringContainsString('.w-2\\/3 {', $css);
        $this->assertStringContainsString('width: calc(2/3 * 100%);', $css);
    }

    #[Test]
    public function width_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['w-[4px]', 'w-[50%]']);

        $this->assertStringContainsString('.w-\\[4px\\] {', $css);
        $this->assertStringContainsString('width: 4px;', $css);

        $this->assertStringContainsString('.w-\\[50\\%\\] {', $css);
        $this->assertStringContainsString('width: 50%;', $css);
    }

    #[Test]
    public function width_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['w']));
        $this->assertEquals('', TestHelper::run(['-w-4']));
        $this->assertEquals('', TestHelper::run(['-w-1/2']));
        $this->assertEquals('', TestHelper::run(['-w-[4px]']));
        $this->assertEquals('', TestHelper::run(['w-full/foo']));
        $this->assertEquals('', TestHelper::run(['w-4/foo']));
    }

    // =========================================================================
    // Min-width utilities
    // =========================================================================

    #[Test]
    public function min_width_static_values(): void
    {
        $css = TestHelper::run(['min-w-full', 'min-w-auto', 'min-w-min', 'min-w-max', 'min-w-fit']);

        $this->assertStringContainsString('.min-w-auto {', $css);
        $this->assertStringContainsString('min-width: auto;', $css);

        $this->assertStringContainsString('.min-w-fit {', $css);
        $this->assertStringContainsString('min-width: fit-content;', $css);

        $this->assertStringContainsString('.min-w-full {', $css);
        $this->assertStringContainsString('min-width: 100%;', $css);

        $this->assertStringContainsString('.min-w-max {', $css);
        $this->assertStringContainsString('min-width: max-content;', $css);

        $this->assertStringContainsString('.min-w-min {', $css);
        $this->assertStringContainsString('min-width: min-content;', $css);
    }

    #[Test]
    public function min_width_with_spacing(): void
    {
        $css = TestHelper::run(['min-w-4']);

        $this->assertStringContainsString('.min-w-4 {', $css);
        $this->assertStringContainsString('min-width: var(--spacing-4);', $css);
    }

    #[Test]
    public function min_width_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['min-w-[4px]']);

        $this->assertStringContainsString('.min-w-\\[4px\\] {', $css);
        $this->assertStringContainsString('min-width: 4px;', $css);
    }

    #[Test]
    public function min_width_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['min-w']));
        $this->assertEquals('', TestHelper::run(['-min-w-4']));
        $this->assertEquals('', TestHelper::run(['-min-w-[4px]']));
        $this->assertEquals('', TestHelper::run(['min-w-4/foo']));
    }

    // =========================================================================
    // Max-width utilities
    // =========================================================================

    #[Test]
    public function max_width_static_values(): void
    {
        $css = TestHelper::run(['max-w-none', 'max-w-full', 'max-w-max', 'max-w-fit']);

        $this->assertStringContainsString('.max-w-fit {', $css);
        $this->assertStringContainsString('max-width: fit-content;', $css);

        $this->assertStringContainsString('.max-w-full {', $css);
        $this->assertStringContainsString('max-width: 100%;', $css);

        $this->assertStringContainsString('.max-w-max {', $css);
        $this->assertStringContainsString('max-width: max-content;', $css);

        $this->assertStringContainsString('.max-w-none {', $css);
        $this->assertStringContainsString('max-width: none;', $css);
    }

    #[Test]
    public function max_width_with_spacing(): void
    {
        $css = TestHelper::run(['max-w-4']);

        $this->assertStringContainsString('.max-w-4 {', $css);
        $this->assertStringContainsString('max-width: var(--spacing-4);', $css);
    }

    #[Test]
    public function max_width_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['max-w-[4px]']);

        $this->assertStringContainsString('.max-w-\\[4px\\] {', $css);
        $this->assertStringContainsString('max-width: 4px;', $css);
    }

    #[Test]
    public function max_width_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['max-w']));
        $this->assertEquals('', TestHelper::run(['max-w-auto'])); // auto is not valid for max-width
        $this->assertEquals('', TestHelper::run(['-max-w-4']));
        $this->assertEquals('', TestHelper::run(['-max-w-[4px]']));
        $this->assertEquals('', TestHelper::run(['max-w-4/foo']));
    }

    // =========================================================================
    // Height utilities
    // =========================================================================

    #[Test]
    public function height_static_values(): void
    {
        $css = TestHelper::run([
            'h-full', 'h-auto', 'h-screen', 'h-svh', 'h-lvh', 'h-dvh',
            'h-min', 'h-max', 'h-fit', 'h-lh'
        ]);

        $this->assertStringContainsString('.h-auto {', $css);
        $this->assertStringContainsString('height: auto;', $css);

        $this->assertStringContainsString('.h-dvh {', $css);
        $this->assertStringContainsString('height: 100dvh;', $css);

        $this->assertStringContainsString('.h-fit {', $css);
        $this->assertStringContainsString('height: fit-content;', $css);

        $this->assertStringContainsString('.h-full {', $css);
        $this->assertStringContainsString('height: 100%;', $css);

        $this->assertStringContainsString('.h-lh {', $css);
        $this->assertStringContainsString('height: 1lh;', $css);

        $this->assertStringContainsString('.h-lvh {', $css);
        $this->assertStringContainsString('height: 100lvh;', $css);

        $this->assertStringContainsString('.h-max {', $css);
        $this->assertStringContainsString('height: max-content;', $css);

        $this->assertStringContainsString('.h-min {', $css);
        $this->assertStringContainsString('height: min-content;', $css);

        $this->assertStringContainsString('.h-screen {', $css);
        $this->assertStringContainsString('height: 100vh;', $css);

        $this->assertStringContainsString('.h-svh {', $css);
        $this->assertStringContainsString('height: 100svh;', $css);
    }

    #[Test]
    public function height_with_spacing(): void
    {
        $css = TestHelper::run(['h-4']);

        $this->assertStringContainsString('.h-4 {', $css);
        $this->assertStringContainsString('height: var(--spacing-4);', $css);
    }

    #[Test]
    public function height_with_fractions(): void
    {
        $css = TestHelper::run(['h-1/2']);

        $this->assertStringContainsString('.h-1\\/2 {', $css);
        $this->assertStringContainsString('height: calc(1/2 * 100%);', $css);
    }

    #[Test]
    public function height_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['h-[4px]']);

        $this->assertStringContainsString('.h-\\[4px\\] {', $css);
        $this->assertStringContainsString('height: 4px;', $css);
    }

    #[Test]
    public function height_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['h']));
        $this->assertEquals('', TestHelper::run(['-h-4']));
        $this->assertEquals('', TestHelper::run(['-h-1/2']));
        $this->assertEquals('', TestHelper::run(['-h-[4px]']));
        $this->assertEquals('', TestHelper::run(['h-full/foo']));
        $this->assertEquals('', TestHelper::run(['h-4/foo']));
    }

    // =========================================================================
    // Min-height utilities
    // =========================================================================

    #[Test]
    public function min_height_static_values(): void
    {
        $css = TestHelper::run([
            'min-h-full', 'min-h-auto', 'min-h-screen', 'min-h-svh', 'min-h-lvh',
            'min-h-dvh', 'min-h-min', 'min-h-max', 'min-h-fit', 'min-h-lh'
        ]);

        $this->assertStringContainsString('.min-h-auto {', $css);
        $this->assertStringContainsString('min-height: auto;', $css);

        $this->assertStringContainsString('.min-h-dvh {', $css);
        $this->assertStringContainsString('min-height: 100dvh;', $css);

        $this->assertStringContainsString('.min-h-fit {', $css);
        $this->assertStringContainsString('min-height: fit-content;', $css);

        $this->assertStringContainsString('.min-h-full {', $css);
        $this->assertStringContainsString('min-height: 100%;', $css);

        $this->assertStringContainsString('.min-h-lh {', $css);
        $this->assertStringContainsString('min-height: 1lh;', $css);

        $this->assertStringContainsString('.min-h-lvh {', $css);
        $this->assertStringContainsString('min-height: 100lvh;', $css);

        $this->assertStringContainsString('.min-h-max {', $css);
        $this->assertStringContainsString('min-height: max-content;', $css);

        $this->assertStringContainsString('.min-h-min {', $css);
        $this->assertStringContainsString('min-height: min-content;', $css);

        $this->assertStringContainsString('.min-h-screen {', $css);
        $this->assertStringContainsString('min-height: 100vh;', $css);

        $this->assertStringContainsString('.min-h-svh {', $css);
        $this->assertStringContainsString('min-height: 100svh;', $css);
    }

    #[Test]
    public function min_height_with_spacing(): void
    {
        $css = TestHelper::run(['min-h-4']);

        $this->assertStringContainsString('.min-h-4 {', $css);
        $this->assertStringContainsString('min-height: var(--spacing-4);', $css);
    }

    #[Test]
    public function min_height_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['min-h-[4px]']);

        $this->assertStringContainsString('.min-h-\\[4px\\] {', $css);
        $this->assertStringContainsString('min-height: 4px;', $css);
    }

    #[Test]
    public function min_height_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['min-h']));
        $this->assertEquals('', TestHelper::run(['-min-h-4']));
        $this->assertEquals('', TestHelper::run(['-min-h-[4px]']));
        $this->assertEquals('', TestHelper::run(['min-h-4/foo']));
    }

    // =========================================================================
    // Max-height utilities
    // =========================================================================

    #[Test]
    public function max_height_static_values(): void
    {
        $css = TestHelper::run([
            'max-h-none', 'max-h-full', 'max-h-screen', 'max-h-svh', 'max-h-lvh',
            'max-h-dvh', 'max-h-min', 'max-h-max', 'max-h-fit', 'max-h-lh'
        ]);

        $this->assertStringContainsString('.max-h-dvh {', $css);
        $this->assertStringContainsString('max-height: 100dvh;', $css);

        $this->assertStringContainsString('.max-h-fit {', $css);
        $this->assertStringContainsString('max-height: fit-content;', $css);

        $this->assertStringContainsString('.max-h-full {', $css);
        $this->assertStringContainsString('max-height: 100%;', $css);

        $this->assertStringContainsString('.max-h-lh {', $css);
        $this->assertStringContainsString('max-height: 1lh;', $css);

        $this->assertStringContainsString('.max-h-lvh {', $css);
        $this->assertStringContainsString('max-height: 100lvh;', $css);

        $this->assertStringContainsString('.max-h-max {', $css);
        $this->assertStringContainsString('max-height: max-content;', $css);

        $this->assertStringContainsString('.max-h-min {', $css);
        $this->assertStringContainsString('max-height: min-content;', $css);

        $this->assertStringContainsString('.max-h-none {', $css);
        $this->assertStringContainsString('max-height: none;', $css);

        $this->assertStringContainsString('.max-h-screen {', $css);
        $this->assertStringContainsString('max-height: 100vh;', $css);

        $this->assertStringContainsString('.max-h-svh {', $css);
        $this->assertStringContainsString('max-height: 100svh;', $css);
    }

    #[Test]
    public function max_height_with_spacing(): void
    {
        $css = TestHelper::run(['max-h-4']);

        $this->assertStringContainsString('.max-h-4 {', $css);
        $this->assertStringContainsString('max-height: var(--spacing-4);', $css);
    }

    #[Test]
    public function max_height_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['max-h-[4px]']);

        $this->assertStringContainsString('.max-h-\\[4px\\] {', $css);
        $this->assertStringContainsString('max-height: 4px;', $css);
    }

    #[Test]
    public function max_height_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['max-h']));
        $this->assertEquals('', TestHelper::run(['max-h-auto'])); // auto is not valid for max-height
        $this->assertEquals('', TestHelper::run(['-max-h-4']));
        $this->assertEquals('', TestHelper::run(['-max-h-[4px]']));
        $this->assertEquals('', TestHelper::run(['max-h-4/foo']));
    }

    // =========================================================================
    // Size utilities
    // =========================================================================

    #[Test]
    public function size_static_values(): void
    {
        $css = TestHelper::run(['size-full', 'size-auto', 'size-min', 'size-max', 'size-fit']);

        $this->assertStringContainsString('.size-auto {', $css);
        $this->assertStringContainsString('width: auto;', $css);
        $this->assertStringContainsString('height: auto;', $css);

        $this->assertStringContainsString('.size-fit {', $css);
        $this->assertStringContainsString('width: fit-content;', $css);
        $this->assertStringContainsString('height: fit-content;', $css);

        $this->assertStringContainsString('.size-full {', $css);
        $this->assertStringContainsString('width: 100%;', $css);
        $this->assertStringContainsString('height: 100%;', $css);
    }

    #[Test]
    public function size_with_spacing(): void
    {
        $css = TestHelper::run(['size-4']);

        $this->assertStringContainsString('.size-4 {', $css);
        $this->assertStringContainsString('width: var(--spacing-4);', $css);
        $this->assertStringContainsString('height: var(--spacing-4);', $css);
    }

    #[Test]
    public function size_with_fractions(): void
    {
        $css = TestHelper::run(['size-1/2']);

        $this->assertStringContainsString('.size-1\\/2 {', $css);
        $this->assertStringContainsString('width: calc(1/2 * 100%);', $css);
        $this->assertStringContainsString('height: calc(1/2 * 100%);', $css);
    }

    #[Test]
    public function size_with_arbitrary_values(): void
    {
        $css = TestHelper::run(['size-[4px]']);

        $this->assertStringContainsString('.size-\\[4px\\] {', $css);
        $this->assertStringContainsString('width: 4px;', $css);
        $this->assertStringContainsString('height: 4px;', $css);
    }

    #[Test]
    public function size_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['size']));
        $this->assertEquals('', TestHelper::run(['-size-4']));
        $this->assertEquals('', TestHelper::run(['size-full/foo']));
        $this->assertEquals('', TestHelper::run(['size-4/foo']));
    }
}
