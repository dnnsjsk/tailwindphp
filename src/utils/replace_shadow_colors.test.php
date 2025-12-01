<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use function TailwindPHP\Utils\replaceShadowColors;
use function TailwindPHP\DesignSystem\replaceAlpha;

/**
 * Tests for replace-shadow-colors.php
 *
 * Port of: packages/tailwindcss/src/utils/replace-shadow-colors.test.ts
 */
class replace_shadow_colors extends TestCase
{
    /**
     * Helper replacer without alpha modification.
     */
    private function simpleReplacer(string $color): string
    {
        return "var(--tw-shadow-color, {$color})";
    }

    /**
     * Helper replacer with alpha modification (50%).
     */
    private function alphaReplacer(string $color): string
    {
        return 'var(--tw-shadow-color, ' . replaceAlpha($color, '50%') . ')';
    }

    // ==========================================================================
    // Without replacer (simple)
    // ==========================================================================

    #[Test]
    public function should_handle_var_shadow(): void
    {
        $parsed = replaceShadowColors('var(--my-shadow)', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('var(--my-shadow)', $parsed);
    }

    #[Test]
    public function should_handle_var_shadow_with_offset(): void
    {
        $parsed = replaceShadowColors('1px var(--my-shadow)', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('1px var(--my-shadow)', $parsed);
    }

    #[Test]
    public function should_handle_var_color_with_offsets(): void
    {
        $parsed = replaceShadowColors('1px 1px var(--my-color)', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('1px 1px var(--tw-shadow-color, var(--my-color))', $parsed);
    }

    #[Test]
    public function should_handle_var_color_with_zero_offsets(): void
    {
        $parsed = replaceShadowColors('0 0 0 var(--my-color)', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('0 0 0 var(--tw-shadow-color, var(--my-color))', $parsed);
    }

    #[Test]
    public function should_handle_two_values_with_currentcolor(): void
    {
        $parsed = replaceShadowColors('1px 2px', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('1px 2px var(--tw-shadow-color, currentcolor)', $parsed);
    }

    #[Test]
    public function should_handle_three_values_with_currentcolor(): void
    {
        $parsed = replaceShadowColors('1px 2px 3px', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('1px 2px 3px var(--tw-shadow-color, currentcolor)', $parsed);
    }

    #[Test]
    public function should_handle_four_values_with_currentcolor(): void
    {
        $parsed = replaceShadowColors('1px 2px 3px 4px', fn($c) => $this->simpleReplacer($c));
        $this->assertEquals('1px 2px 3px 4px var(--tw-shadow-color, currentcolor)', $parsed);
    }

    #[Test]
    public function should_handle_multiple_shadows(): void
    {
        $input = implode(', ', ['var(--my-shadow)', '1px 1px var(--my-color)', '0 0 1px var(--my-color)']);
        $parsed = replaceShadowColors($input, fn($c) => $this->simpleReplacer($c));
        $this->assertEquals(
            'var(--my-shadow), 1px 1px var(--tw-shadow-color, var(--my-color)), 0 0 1px var(--tw-shadow-color, var(--my-color))',
            $parsed
        );
    }

    // ==========================================================================
    // With replacer (alpha modification)
    // ==========================================================================

    #[Test]
    public function should_handle_var_color_with_intensity(): void
    {
        $parsed = replaceShadowColors('1px 1px var(--my-color)', fn($c) => $this->alphaReplacer($c));
        // PHP uses color-mix for better browser support
        $this->assertEquals(
            '1px 1px var(--tw-shadow-color, color-mix(in oklab, var(--my-color) 50%, transparent))',
            $parsed
        );
    }

    #[Test]
    public function should_handle_box_shadow_with_intensity(): void
    {
        $parsed = replaceShadowColors('1px 1px var(--my-color)', fn($c) => $this->alphaReplacer($c));
        // PHP uses color-mix for better browser support
        $this->assertEquals(
            '1px 1px var(--tw-shadow-color, color-mix(in oklab, var(--my-color) 50%, transparent))',
            $parsed
        );
    }

    #[Test]
    public function should_handle_four_values_with_intensity_and_no_color_value(): void
    {
        $parsed = replaceShadowColors('1px 2px 3px 4px', fn($c) => $this->alphaReplacer($c));
        // PHP uses color-mix for better browser support
        $this->assertEquals(
            '1px 2px 3px 4px var(--tw-shadow-color, color-mix(in oklab, currentcolor 50%, transparent))',
            $parsed
        );
    }

    #[Test]
    public function should_handle_multiple_shadows_with_intensity(): void
    {
        $input = implode(', ', ['var(--my-shadow)', '1px 1px var(--my-color)', '0 0 1px var(--my-color)']);
        $parsed = replaceShadowColors($input, fn($c) => $this->alphaReplacer($c));
        // PHP uses color-mix for better browser support
        $this->assertEquals(
            'var(--my-shadow), 1px 1px var(--tw-shadow-color, color-mix(in oklab, var(--my-color) 50%, transparent)), 0 0 1px var(--tw-shadow-color, color-mix(in oklab, var(--my-color) 50%, transparent))',
            $parsed
        );
    }
}
