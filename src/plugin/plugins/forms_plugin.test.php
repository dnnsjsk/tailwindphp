<?php

declare(strict_types=1);

namespace TailwindPHP\Plugin\Plugins;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\compile;

/**
 * Tests for forms-plugin.php
 *
 * Tests for the @tailwindcss/forms port. Since the reference plugin doesn't have
 * official tests, these tests verify expected CSS output.
 *
 * Note: The forms plugin adds:
 * - Base styles via addBase() for native selectors like [type='text'], select, etc.
 * - Component styles via addComponents() for class-based selectors like .form-input
 *
 * Pseudo-class selectors like .form-checkbox:checked are registered as separate
 * utilities and can be accessed by using the full selector name.
 */
class forms_plugin extends TestCase
{
    private function compileForms(array $classes, array $pluginOptions = []): string
    {
        $optionsStr = '';
        if (!empty($pluginOptions)) {
            $lines = [];
            foreach ($pluginOptions as $key => $value) {
                $lines[] = "    {$key}: \"{$value}\";";
            }
            $optionsStr = "{\n" . implode("\n", $lines) . "\n}";
        }

        $css = <<<CSS
        @plugin "@tailwindcss/forms" {$optionsStr};
        @import "tailwindcss/utilities.css";
        CSS;

        $compiled = compile($css, ['loadDefaultTheme' => false]);

        return $compiled['build']($classes);
    }

    // ============================================
    // Class Strategy Tests (via addComponents)
    // ============================================

    #[Test]
    public function form_input_class_has_appearance_none(): void
    {
        $css = $this->compileForms(['form-input']);

        $this->assertStringContainsString('.form-input', $css);
        $this->assertStringContainsString('appearance: none', $css);
    }

    #[Test]
    public function form_input_class_has_border_styling(): void
    {
        $css = $this->compileForms(['form-input']);

        $this->assertStringContainsString('border-color:', $css);
        $this->assertStringContainsString('border-width: 1px', $css);
    }

    #[Test]
    public function form_input_class_has_padding(): void
    {
        $css = $this->compileForms(['form-input']);

        $this->assertStringContainsString('padding-top:', $css);
        $this->assertStringContainsString('padding-bottom:', $css);
        $this->assertStringContainsString('padding-left:', $css);
        $this->assertStringContainsString('padding-right:', $css);
    }

    #[Test]
    public function form_input_class_has_focus_styling(): void
    {
        $css = $this->compileForms(['form-input']);

        // Focus state should have ring styling (nested under .form-input)
        $this->assertStringContainsString('--tw-ring-color:', $css);
    }

    #[Test]
    public function form_textarea_class_is_generated(): void
    {
        $css = $this->compileForms(['form-textarea']);

        $this->assertStringContainsString('.form-textarea', $css);
        $this->assertStringContainsString('appearance: none', $css);
    }

    #[Test]
    public function form_select_class_has_chevron_background(): void
    {
        $css = $this->compileForms(['form-select']);

        $this->assertStringContainsString('.form-select', $css);
        $this->assertStringContainsString('background-image:', $css);
        $this->assertStringContainsString('background-position:', $css);
        $this->assertStringContainsString('background-repeat: no-repeat', $css);
        $this->assertStringContainsString('background-size:', $css);
    }

    #[Test]
    public function form_multiselect_class_is_generated(): void
    {
        $css = $this->compileForms(['form-multiselect']);

        $this->assertStringContainsString('.form-multiselect', $css);
    }

    #[Test]
    public function form_checkbox_class_has_base_styling(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('.form-checkbox', $css);
        $this->assertStringContainsString('appearance: none', $css);
        $this->assertStringContainsString('vertical-align: middle', $css);
        $this->assertStringContainsString('user-select: none', $css);
    }

    #[Test]
    public function form_radio_class_has_circular_border(): void
    {
        $css = $this->compileForms(['form-radio']);

        $this->assertStringContainsString('.form-radio', $css);
        $this->assertStringContainsString('border-radius: 100%', $css);
    }

    #[Test]
    public function form_checkbox_class_has_square_border(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        // Checkbox uses none border radius (square)
        $this->assertStringContainsString('border-radius: 0px', $css);
    }

    // ============================================
    // Strategy Option Tests
    // ============================================

    #[Test]
    public function class_strategy_outputs_form_classes(): void
    {
        $css = $this->compileForms(['form-input', 'form-checkbox', 'form-select'], ['strategy' => 'class']);

        $this->assertStringContainsString('.form-input', $css);
        $this->assertStringContainsString('.form-checkbox', $css);
        $this->assertStringContainsString('.form-select', $css);
    }

    #[Test]
    public function default_strategy_also_outputs_form_classes(): void
    {
        // Default strategy includes both base AND class styles
        $css = $this->compileForms(['form-input', 'form-textarea']);

        $this->assertStringContainsString('.form-input', $css);
        $this->assertStringContainsString('.form-textarea', $css);
    }

    // ============================================
    // Color Tests
    // ============================================

    #[Test]
    public function uses_gray_500_for_borders(): void
    {
        $css = $this->compileForms(['form-input']);

        // Should use gray-500 (#6b7280) for borders
        $this->assertStringContainsString('#6b7280', $css);
    }

    #[Test]
    public function uses_blue_600_for_focus(): void
    {
        $css = $this->compileForms(['form-input']);

        // Should use blue-600 (#2563eb) for focus colors
        $this->assertStringContainsString('#2563eb', $css);
    }

    // ============================================
    // Multiple Classes Tests
    // ============================================

    #[Test]
    public function multiple_form_classes_can_be_generated(): void
    {
        $css = $this->compileForms(['form-input', 'form-checkbox', 'form-radio', 'form-select', 'form-textarea']);

        $this->assertStringContainsString('.form-input', $css);
        $this->assertStringContainsString('.form-checkbox', $css);
        $this->assertStringContainsString('.form-radio', $css);
        $this->assertStringContainsString('.form-select', $css);
        $this->assertStringContainsString('.form-textarea', $css);
    }

    // ============================================
    // Focus Ring Tests
    // ============================================

    #[Test]
    public function focus_states_have_ring_offset(): void
    {
        $css = $this->compileForms(['form-input']);

        // Focus should set up ring offset
        $this->assertStringContainsString('--tw-ring-offset-width:', $css);
        $this->assertStringContainsString('--tw-ring-offset-color:', $css);
    }

    #[Test]
    public function focus_states_have_outline_transparent(): void
    {
        $css = $this->compileForms(['form-input']);

        $this->assertStringContainsString('outline: 2px solid transparent', $css);
    }

    // ============================================
    // SVG Data URI Tests
    // ============================================

    #[Test]
    public function svg_icons_are_data_uris(): void
    {
        $css = $this->compileForms(['form-select']);

        // Select has chevron SVG as data URI
        $this->assertStringContainsString('data:image/svg+xml', $css);
    }

    // ============================================
    // Print Color Adjust Tests
    // ============================================

    #[Test]
    public function form_checkbox_has_print_color_adjust(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('print-color-adjust: exact', $css);
    }

    #[Test]
    public function form_select_has_print_color_adjust(): void
    {
        $css = $this->compileForms(['form-select']);

        $this->assertStringContainsString('print-color-adjust: exact', $css);
    }

    // ============================================
    // Sizing Tests
    // ============================================

    #[Test]
    public function form_checkbox_has_size(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('height: 1rem', $css);
        $this->assertStringContainsString('width: 1rem', $css);
    }

    #[Test]
    public function form_radio_has_size(): void
    {
        $css = $this->compileForms(['form-radio']);

        $this->assertStringContainsString('height: 1rem', $css);
        $this->assertStringContainsString('width: 1rem', $css);
    }

    // ============================================
    // Display Tests
    // ============================================

    #[Test]
    public function form_checkbox_has_inline_block_display(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('display: inline-block', $css);
    }

    // ============================================
    // Background Tests
    // ============================================

    #[Test]
    public function form_input_has_white_background(): void
    {
        $css = $this->compileForms(['form-input']);

        $this->assertStringContainsString('background-color: #fff', $css);
    }

    #[Test]
    public function form_checkbox_has_white_background(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('background-color: #fff', $css);
    }

    // ============================================
    // Shadow Variable Tests
    // ============================================

    #[Test]
    public function form_input_has_shadow_variable(): void
    {
        $css = $this->compileForms(['form-input']);

        $this->assertStringContainsString('--tw-shadow: 0 0 #0000', $css);
    }

    // ============================================
    // Border Origin Tests
    // ============================================

    #[Test]
    public function form_checkbox_has_border_box_origin(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('background-origin: border-box', $css);
    }

    // ============================================
    // Flex Shrink Tests
    // ============================================

    #[Test]
    public function form_checkbox_has_flex_shrink_zero(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        $this->assertStringContainsString('flex-shrink: 0', $css);
    }

    // ============================================
    // Color Property Tests
    // ============================================

    #[Test]
    public function form_checkbox_has_blue_color(): void
    {
        $css = $this->compileForms(['form-checkbox']);

        // The color property is set to blue-600 for the currentColor trick
        $this->assertStringContainsString('color: #2563eb', $css);
    }
}
