<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\TestHelper;
use TailwindPHP\Theme;

use function TailwindPHP\Utilities\registerBorderUtilities;

/**
 * Border Utilities Tests
 *
 * Port of border-related tests from: packages/tailwindcss/src/utilities.test.ts
 *
 * Includes:
 * - border-radius (rounded-*)
 * - border-width (border-*)
 * - border-style (border-solid, border-dashed, etc.)
 * - border-collapse
 * - outline
 * - divide
 */
class BordersTest extends TestCase
{
    private TestHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestHelper();
        $this->helper->registerUtilities(function ($builder) {
            registerBorderUtilities($builder);
        });
    }

    // =========================================================================
    // Border Radius
    // =========================================================================

    #[Test]
    public function rounded_default(): void
    {
        $css = $this->helper->run(['rounded']);
        $this->assertStringContainsString('border-radius: var(--radius);', $css);
    }

    #[Test]
    public function rounded_none(): void
    {
        $css = $this->helper->run(['rounded-none']);
        $this->assertStringContainsString('border-radius: 0', $css);
    }

    #[Test]
    public function rounded_full(): void
    {
        $css = $this->helper->run(['rounded-full']);
        $this->assertStringContainsString('border-radius: 3.40282e38px', $css);
    }

    #[Test]
    public function rounded_with_theme_value(): void
    {
        // Create helper with custom theme
        $theme = new Theme();
        $theme->add('--radius-lg', '0.5rem');
        $helper = new TestHelper($theme);
        $helper->registerUtilities(function ($builder) {
            registerBorderUtilities($builder);
        });

        $css = $helper->run(['rounded-lg']);
        // TailwindCSS 4.0 outputs CSS variables, not resolved values
        $this->assertStringContainsString('border-radius: var(--radius-lg)', $css);
    }

    #[Test]
    public function rounded_arbitrary(): void
    {
        $css = $this->helper->run(['rounded-[10px]']);
        $this->assertStringContainsString('border-radius: 10px', $css);
    }

    #[Test]
    public function rounded_top_corners(): void
    {
        $css = $this->helper->run(['rounded-t']);
        $this->assertStringContainsString('border-top-left-radius: var(--radius)', $css);
        $this->assertStringContainsString('border-top-right-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_right_corners(): void
    {
        $css = $this->helper->run(['rounded-r']);
        $this->assertStringContainsString('border-top-right-radius: var(--radius)', $css);
        $this->assertStringContainsString('border-bottom-right-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_bottom_corners(): void
    {
        $css = $this->helper->run(['rounded-b']);
        $this->assertStringContainsString('border-bottom-right-radius: var(--radius)', $css);
        $this->assertStringContainsString('border-bottom-left-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_left_corners(): void
    {
        $css = $this->helper->run(['rounded-l']);
        $this->assertStringContainsString('border-top-left-radius: var(--radius)', $css);
        $this->assertStringContainsString('border-bottom-left-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_start_corners(): void
    {
        $css = $this->helper->run(['rounded-s']);
        $this->assertStringContainsString('border-start-start-radius: var(--radius)', $css);
        $this->assertStringContainsString('border-end-start-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_end_corners(): void
    {
        $css = $this->helper->run(['rounded-e']);
        $this->assertStringContainsString('border-start-end-radius: var(--radius)', $css);
        $this->assertStringContainsString('border-end-end-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_individual_physical_corners(): void
    {
        $css = $this->helper->run(['rounded-tl']);
        $this->assertStringContainsString('border-top-left-radius: var(--radius)', $css);

        $css = $this->helper->run(['rounded-tr']);
        $this->assertStringContainsString('border-top-right-radius: var(--radius)', $css);

        $css = $this->helper->run(['rounded-br']);
        $this->assertStringContainsString('border-bottom-right-radius: var(--radius)', $css);

        $css = $this->helper->run(['rounded-bl']);
        $this->assertStringContainsString('border-bottom-left-radius: var(--radius)', $css);
    }

    #[Test]
    public function rounded_individual_logical_corners(): void
    {
        $css = $this->helper->run(['rounded-ss']);
        $this->assertStringContainsString('border-start-start-radius: var(--radius)', $css);

        $css = $this->helper->run(['rounded-se']);
        $this->assertStringContainsString('border-start-end-radius: var(--radius)', $css);

        $css = $this->helper->run(['rounded-ee']);
        $this->assertStringContainsString('border-end-end-radius: var(--radius)', $css);

        $css = $this->helper->run(['rounded-es']);
        $this->assertStringContainsString('border-end-start-radius: var(--radius)', $css);
    }

    // =========================================================================
    // Border Width
    // =========================================================================

    #[Test]
    public function border_width_default(): void
    {
        $css = $this->helper->run(['border']);
        $this->assertStringContainsString('border-width: 1px', $css);
    }

    #[Test]
    public function border_width_values(): void
    {
        $css = $this->helper->run(['border-0']);
        $this->assertStringContainsString('border-width: 0px', $css);

        $css = $this->helper->run(['border-2']);
        $this->assertStringContainsString('border-width: 2px', $css);

        $css = $this->helper->run(['border-4']);
        $this->assertStringContainsString('border-width: 4px', $css);

        $css = $this->helper->run(['border-8']);
        $this->assertStringContainsString('border-width: 8px', $css);
    }

    #[Test]
    public function border_width_arbitrary(): void
    {
        $css = $this->helper->run(['border-[3px]']);
        $this->assertStringContainsString('border-width: 3px', $css);
    }

    #[Test]
    public function border_width_x_axis(): void
    {
        $css = $this->helper->run(['border-x']);
        $this->assertStringContainsString('border-left-width: 1px', $css);
        $this->assertStringContainsString('border-right-width: 1px', $css);
    }

    #[Test]
    public function border_width_y_axis(): void
    {
        $css = $this->helper->run(['border-y']);
        $this->assertStringContainsString('border-top-width: 1px', $css);
        $this->assertStringContainsString('border-bottom-width: 1px', $css);
    }

    #[Test]
    public function border_width_individual_sides(): void
    {
        $css = $this->helper->run(['border-t']);
        $this->assertStringContainsString('border-top-width: 1px', $css);

        $css = $this->helper->run(['border-r']);
        $this->assertStringContainsString('border-right-width: 1px', $css);

        $css = $this->helper->run(['border-b']);
        $this->assertStringContainsString('border-bottom-width: 1px', $css);

        $css = $this->helper->run(['border-l']);
        $this->assertStringContainsString('border-left-width: 1px', $css);
    }

    #[Test]
    public function border_width_logical_sides(): void
    {
        $css = $this->helper->run(['border-s']);
        $this->assertStringContainsString('border-inline-start-width: 1px', $css);

        $css = $this->helper->run(['border-e']);
        $this->assertStringContainsString('border-inline-end-width: 1px', $css);
    }

    #[Test]
    public function border_width_sides_with_values(): void
    {
        $css = $this->helper->run(['border-x-4']);
        $this->assertStringContainsString('border-left-width: 4px', $css);
        $this->assertStringContainsString('border-right-width: 4px', $css);

        $css = $this->helper->run(['border-t-2']);
        $this->assertStringContainsString('border-top-width: 2px', $css);
    }

    // =========================================================================
    // Border Style
    // =========================================================================

    #[Test]
    public function border_style_solid(): void
    {
        $css = $this->helper->run(['border-solid']);
        $this->assertStringContainsString('border-style: solid', $css);
    }

    #[Test]
    public function border_style_dashed(): void
    {
        $css = $this->helper->run(['border-dashed']);
        $this->assertStringContainsString('border-style: dashed', $css);
    }

    #[Test]
    public function border_style_dotted(): void
    {
        $css = $this->helper->run(['border-dotted']);
        $this->assertStringContainsString('border-style: dotted', $css);
    }

    #[Test]
    public function border_style_double(): void
    {
        $css = $this->helper->run(['border-double']);
        $this->assertStringContainsString('border-style: double', $css);
    }

    #[Test]
    public function border_style_hidden(): void
    {
        $css = $this->helper->run(['border-hidden']);
        $this->assertStringContainsString('border-style: hidden', $css);
    }

    #[Test]
    public function border_style_none(): void
    {
        $css = $this->helper->run(['border-none']);
        $this->assertStringContainsString('border-style: none', $css);
    }

    // =========================================================================
    // Border Collapse
    // =========================================================================

    #[Test]
    public function border_collapse(): void
    {
        $css = $this->helper->run(['border-collapse']);
        $this->assertStringContainsString('border-collapse: collapse', $css);
    }

    #[Test]
    public function border_separate(): void
    {
        $css = $this->helper->run(['border-separate']);
        $this->assertStringContainsString('border-collapse: separate', $css);
    }

    // =========================================================================
    // Outline Style
    // =========================================================================

    #[Test]
    public function outline_none(): void
    {
        $css = $this->helper->run(['outline-none']);
        $this->assertStringContainsString('outline: 2px solid transparent', $css);
        $this->assertStringContainsString('outline-offset: 2px', $css);
    }

    #[Test]
    public function outline_style_solid(): void
    {
        $css = $this->helper->run(['outline']);
        $this->assertStringContainsString('outline-style: solid', $css);
    }

    #[Test]
    public function outline_style_dashed(): void
    {
        $css = $this->helper->run(['outline-dashed']);
        $this->assertStringContainsString('outline-style: dashed', $css);
    }

    #[Test]
    public function outline_style_dotted(): void
    {
        $css = $this->helper->run(['outline-dotted']);
        $this->assertStringContainsString('outline-style: dotted', $css);
    }

    #[Test]
    public function outline_style_double(): void
    {
        $css = $this->helper->run(['outline-double']);
        $this->assertStringContainsString('outline-style: double', $css);
    }

    // =========================================================================
    // Outline Width
    // =========================================================================

    #[Test]
    public function outline_width_values(): void
    {
        $css = $this->helper->run(['outline-0']);
        $this->assertStringContainsString('outline-width: 0px', $css);

        $css = $this->helper->run(['outline-1']);
        $this->assertStringContainsString('outline-width: 1px', $css);

        $css = $this->helper->run(['outline-2']);
        $this->assertStringContainsString('outline-width: 2px', $css);

        $css = $this->helper->run(['outline-4']);
        $this->assertStringContainsString('outline-width: 4px', $css);

        $css = $this->helper->run(['outline-8']);
        $this->assertStringContainsString('outline-width: 8px', $css);
    }

    #[Test]
    public function outline_width_arbitrary(): void
    {
        $css = $this->helper->run(['outline-[3px]']);
        $this->assertStringContainsString('outline-width: 3px', $css);
    }

    // =========================================================================
    // Outline Offset
    // =========================================================================

    #[Test]
    public function outline_offset_values(): void
    {
        $css = $this->helper->run(['outline-offset-0']);
        $this->assertStringContainsString('outline-offset: 0px', $css);

        $css = $this->helper->run(['outline-offset-1']);
        $this->assertStringContainsString('outline-offset: 1px', $css);

        $css = $this->helper->run(['outline-offset-2']);
        $this->assertStringContainsString('outline-offset: 2px', $css);

        $css = $this->helper->run(['outline-offset-4']);
        $this->assertStringContainsString('outline-offset: 4px', $css);

        $css = $this->helper->run(['outline-offset-8']);
        $this->assertStringContainsString('outline-offset: 8px', $css);
    }

    #[Test]
    public function outline_offset_arbitrary(): void
    {
        $css = $this->helper->run(['outline-offset-[5px]']);
        $this->assertStringContainsString('outline-offset: 5px', $css);
    }

    // =========================================================================
    // Divide Width
    // =========================================================================

    #[Test]
    public function divide_x(): void
    {
        $css = $this->helper->run(['divide-x']);
        $this->assertStringContainsString('--tw-divide-x-reverse: 0', $css);
        $this->assertStringContainsString('border-inline-end-width: calc(1px * var(--tw-divide-x-reverse))', $css);
        $this->assertStringContainsString('border-inline-start-width: calc(1px * calc(1 - var(--tw-divide-x-reverse)))', $css);
    }

    #[Test]
    public function divide_y(): void
    {
        $css = $this->helper->run(['divide-y']);
        $this->assertStringContainsString('--tw-divide-y-reverse: 0', $css);
        $this->assertStringContainsString('border-block-end-width: calc(1px * var(--tw-divide-y-reverse))', $css);
        $this->assertStringContainsString('border-block-start-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)))', $css);
    }

    #[Test]
    public function divide_x_reverse(): void
    {
        $css = $this->helper->run(['divide-x-reverse']);
        $this->assertStringContainsString('--tw-divide-x-reverse: 1', $css);
    }

    #[Test]
    public function divide_y_reverse(): void
    {
        $css = $this->helper->run(['divide-y-reverse']);
        $this->assertStringContainsString('--tw-divide-y-reverse: 1', $css);
    }

    // =========================================================================
    // Divide Style
    // =========================================================================

    #[Test]
    public function divide_style_solid(): void
    {
        $css = $this->helper->run(['divide-solid']);
        $this->assertStringContainsString('border-style: solid', $css);
    }

    #[Test]
    public function divide_style_dashed(): void
    {
        $css = $this->helper->run(['divide-dashed']);
        $this->assertStringContainsString('border-style: dashed', $css);
    }

    #[Test]
    public function divide_style_dotted(): void
    {
        $css = $this->helper->run(['divide-dotted']);
        $this->assertStringContainsString('border-style: dotted', $css);
    }

    #[Test]
    public function divide_style_double(): void
    {
        $css = $this->helper->run(['divide-double']);
        $this->assertStringContainsString('border-style: double', $css);
    }

    #[Test]
    public function divide_style_none(): void
    {
        $css = $this->helper->run(['divide-none']);
        $this->assertStringContainsString('border-style: none', $css);
    }
}
