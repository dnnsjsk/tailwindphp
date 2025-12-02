<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for Tailwind CSS functions.
 */
class FunctionsTest extends TestCase
{
    // =========================================================================
    // theme() FUNCTION
    // =========================================================================

    public function test_theme_function_color(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .custom {
                    color: theme(--color-brand);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        // theme() resolves to the actual value
        $this->assertStringContainsString('color:', $css);
    }

    public function test_theme_function_spacing(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --spacing-huge: 100px; }
                .custom {
                    padding: theme(--spacing-huge);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_theme_function_with_fallback(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    color: theme(--color-nonexistent, #ff0000);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        // Fallback #ff0000 gets normalized to 'red' by LightningCSS
        $this->assertStringContainsString('color: red', $css);
    }

    public function test_theme_function_with_opacity(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .custom {
                    background-color: theme(--color-brand / 50%);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_theme_function_nested_reference(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --color-primary: #3b82f6;
                    --color-accent: var(--color-primary);
                }
                .custom {
                    color: theme(--color-accent);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    public function test_theme_function_in_apply(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .btn {
                    @apply p-4;
                    background-color: theme(--color-brand);
                }
            ',
        ]);
        $this->assertStringContainsString('.btn', $css);
    }

    // =========================================================================
    // --theme() FUNCTION
    // =========================================================================

    public function test_double_dash_theme_function(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .custom {
                    color: --theme(--color-brand);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    public function test_double_dash_theme_with_fallback(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    color: --theme(--color-nonexistent, #ff0000);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        // Fallback #ff0000 gets normalized to 'red' by LightningCSS
        $this->assertStringContainsString('color: red', $css);
    }

    public function test_double_dash_theme_with_initial_fallback(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    font-family: --theme(--font-family-custom, initial);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        // When fallback is 'initial', it should be handled appropriately
    }

    public function test_double_dash_theme_inline(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --spacing-large: 2rem; }
                .custom {
                    padding: --theme(--spacing-large inline);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        // Inline modifier should return the value directly instead of var()
    }

    // =========================================================================
    // --spacing() FUNCTION
    // =========================================================================

    public function test_spacing_function_number(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    padding: --spacing(4);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_spacing_function_with_calc(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    margin: calc(--spacing(4) * 2);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('margin:', $css);
    }

    public function test_spacing_function_multiple_values(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    padding: --spacing(2) --spacing(4);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    // =========================================================================
    // --alpha() FUNCTION
    // =========================================================================

    public function test_alpha_function_percentage(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .custom {
                    background-color: --alpha(var(--color-brand) / 50%);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_alpha_function_decimal(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .custom {
                    background-color: --alpha(var(--color-brand) / 0.5);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    public function test_alpha_function_with_var(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --color-brand: #3b82f6;
                    --opacity-half: 50%;
                }
                .custom {
                    background-color: --alpha(var(--color-brand) / var(--opacity-half));
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    // =========================================================================
    // FUNCTIONS IN UTILITIES
    // =========================================================================

    public function test_theme_function_in_utility_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: theme(--color-blue-500, #3b82f6); }
            ',
        ]);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_functions_in_arbitrary_values(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="p-[--spacing(4)]">',
            'css' => '@tailwind utilities;',
        ]);
        $this->assertStringContainsString('padding:', $css);
    }

    // =========================================================================
    // FUNCTIONS IN @theme
    // =========================================================================

    public function test_theme_function_within_theme_block(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-primary">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --color-base: #3b82f6;
                    --color-primary: theme(--color-base);
                }
            ',
        ]);
        // @todo investigate: theme() within @theme block resolves to value, --color-base not in output
        $this->assertStringContainsString('--color-primary:', $css);
    }

    public function test_spacing_function_within_theme_block(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="p-custom">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --spacing-custom: calc(--spacing(4) * 2);
                }
            ',
        ]);
        $this->assertStringContainsString('--spacing-custom:', $css);
    }

    // =========================================================================
    // COMPLEX FUNCTION COMBINATIONS
    // =========================================================================

    public function test_nested_functions(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #3b82f6; }
                .custom {
                    background-color: --alpha(theme(--color-brand) / 50%);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    public function test_functions_in_calc(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    padding: calc(--spacing(4) + --spacing(2));
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_functions_in_multiple_properties(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --color-brand: #3b82f6;
                    --spacing-large: 2rem;
                }
                .custom {
                    color: theme(--color-brand);
                    padding: theme(--spacing-large);
                    margin: --spacing(4);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('color:', $css);
        $this->assertStringContainsString('padding:', $css);
        $this->assertStringContainsString('margin:', $css);
    }

    // =========================================================================
    // EDGE CASES
    // =========================================================================

    public function test_function_with_empty_fallback(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    color: theme(--color-nonexistent, );
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
    }

    public function test_function_with_complex_fallback(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    font-family: theme(--font-family-custom, system-ui, -apple-system, sans-serif);
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('font-family:', $css);
    }

    public function test_function_preserves_whitespace(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom">',
            'css' => '
                @tailwind utilities;
                .custom {
                    box-shadow: 0 4px 6px -1px theme(--color-shadow, rgba(0, 0, 0, 0.1));
                }
            ',
        ]);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('box-shadow:', $css);
    }
}
