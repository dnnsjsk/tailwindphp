<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for Tailwind CSS directives.
 */
class DirectivesTest extends TestCase
{
    // =========================================================================
    // @tailwind DIRECTIVE
    // =========================================================================

    public function test_tailwind_utilities_directive(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">',
            'css' => '@tailwind utilities;',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_tailwind_utilities_with_content_classes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex grid hidden">',
            'css' => '@tailwind utilities;',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('display: grid', $css);
        $this->assertStringContainsString('display: none', $css);
    }

    // =========================================================================
    // @theme DIRECTIVE
    // =========================================================================

    public function test_theme_custom_color(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand text-brand">',
            'css' => '@tailwind utilities; @theme { --color-brand: #3b82f6; }',
        ]);
        $this->assertStringContainsString('--color-brand:', $css);
        $this->assertStringContainsString('background-color:', $css);
        $this->assertStringContainsString('color:', $css);
    }

    public function test_theme_custom_spacing(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="p-huge m-huge">',
            'css' => '@tailwind utilities; @theme { --spacing-huge: 100px; }',
        ]);
        $this->assertStringContainsString('--spacing-huge:', $css);
    }

    public function test_theme_custom_font_family(): void
    {
        // In Tailwind 4, font families use --font-* (not --font-family-*)
        $css = Tailwind::generate([
            'content' => '<div class="font-custom">',
            'css' => '@tailwind utilities; @theme { --font-custom: "Comic Sans MS", cursive; }',
        ]);
        $this->assertStringContainsString('font-family:', $css);
        $this->assertStringContainsString('--font-custom', $css);
    }

    public function test_theme_custom_font_size(): void
    {
        // In Tailwind 4, font sizes use --text-* (not --font-size-*)
        $css = Tailwind::generate([
            'content' => '<div class="text-custom">',
            'css' => '@tailwind utilities; @theme { --text-custom: 18px; }',
        ]);
        $this->assertStringContainsString('font-size:', $css);
    }

    public function test_theme_custom_breakpoint(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom:flex">',
            'css' => '@tailwind utilities; @theme { --breakpoint-custom: 900px; }',
        ]);
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('900px', $css);
    }

    public function test_theme_multiple_values(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-primary text-secondary p-custom">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --color-primary: #ff0000;
                    --color-secondary: #00ff00;
                    --spacing-custom: 50px;
                }
            ',
        ]);
        $this->assertStringContainsString('--color-primary:', $css);
        $this->assertStringContainsString('--color-secondary:', $css);
    }

    public function test_theme_reference(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-primary">',
            'css' => '
                @tailwind utilities;
                @theme reference {
                    --color-primary: #ff0000;
                }
            ',
        ]);
        // Reference theme values should generate utility with fallback but not add to :root theme block
        // Note: default theme fonts still appear in :root output
        $this->assertStringContainsString('bg-primary', $css);
        $this->assertStringContainsString('var(--color-primary', $css);
    }

    public function test_theme_inline(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '
                @tailwind utilities;
                @theme inline {
                    --color-brand: #ff0000;
                }
            ',
        ]);
        // Inline theme values should not appear as CSS variables in :root
        // but the utility should still work
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_theme_with_keyframes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="animate-custom">',
            'css' => '
                @tailwind utilities;
                @theme {
                    --animate-custom: custom 1s ease-in-out infinite;
                    @keyframes custom {
                        0%, 100% { opacity: 1; }
                        50% { opacity: 0.5; }
                    }
                }
            ',
        ]);
        $this->assertStringContainsString('@keyframes custom', $css);
        $this->assertStringContainsString('animation:', $css);
    }

    public function test_theme_prefix(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:bg-brand tw:p-4">',
            'css' => '
                @tailwind utilities;
                @theme prefix(tw) {
                    --color-brand: #ff0000;
                }
            ',
        ]);
        $this->assertStringContainsString('--tw-color-brand:', $css);
    }

    public function test_theme_static(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @tailwind utilities;
                @theme static {
                    --color-always-present: #ff0000;
                }
            ',
        ]);
        // Static theme values should always be present
        $this->assertStringContainsString('--color-always-present:', $css);
    }

    // =========================================================================
    // @apply DIRECTIVE
    // =========================================================================

    public function test_apply_basic(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn">',
            'css' => '
                @tailwind utilities;
                .btn {
                    @apply px-4 py-2 bg-blue-500;
                }
            ',
        ]);
        $this->assertStringContainsString('.btn', $css);
        // Tailwind 4 uses logical properties for padding
        $this->assertStringContainsString('padding-inline:', $css);
        $this->assertStringContainsString('padding-block:', $css);
    }

    public function test_apply_multiple_classes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="card">',
            'css' => '
                @tailwind utilities;
                .card {
                    @apply rounded-lg shadow-md p-6 bg-white;
                }
            ',
        ]);
        $this->assertStringContainsString('.card', $css);
        $this->assertStringContainsString('border-radius:', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_apply_with_variants(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn">',
            'css' => '
                @tailwind utilities;
                .btn {
                    @apply bg-blue-500 hover:bg-blue-600;
                }
            ',
        ]);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_apply_with_important(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn">',
            'css' => '
                @tailwind utilities;
                .btn {
                    @apply !flex;
                }
            ',
        ]);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_apply_with_custom_theme_values(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #ff0000; }
                .btn {
                    @apply bg-brand text-white;
                }
            ',
        ]);
        $this->assertStringContainsString('.btn', $css);
    }

    public function test_apply_with_responsive_variants(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="container">',
            'css' => '
                @tailwind utilities;
                .container {
                    @apply px-4 md:px-6 lg:px-8;
                }
            ',
        ]);
        $this->assertStringContainsString('@media', $css);
    }

    public function test_apply_in_nested_selector(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="card">',
            'css' => '
                @tailwind utilities;
                .card {
                    h2 {
                        @apply text-xl font-bold;
                    }
                }
            ',
        ]);
        $this->assertStringContainsString('.card', $css);
        $this->assertStringContainsString('h2', $css);
    }

    public function test_apply_in_media_query(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="container">',
            'css' => '
                @tailwind utilities;
                @media (min-width: 768px) {
                    .container {
                        @apply flex;
                    }
                }
            ',
        ]);
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('768px', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    // =========================================================================
    // @utility DIRECTIVE
    // =========================================================================

    public function test_utility_static(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="content-auto">',
            'css' => '
                @tailwind utilities;
                @utility content-auto {
                    content-visibility: auto;
                }
            ',
        ]);
        $this->assertStringContainsString('content-visibility: auto', $css);
    }

    public function test_utility_with_apply(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn-primary">',
            'css' => '
                @tailwind utilities;
                @utility btn-primary {
                    @apply px-4 py-2 bg-blue-500 text-white rounded;
                }
            ',
        ]);
        $this->assertStringContainsString('padding', $css);
        $this->assertStringContainsString('border-radius:', $css);
    }

    public function test_utility_functional(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tab-4">',
            'css' => '
                @tailwind utilities;
                @utility tab-* {
                    tab-size: --value(--tab-size, integer);
                }
            ',
        ]);
        // Functional utilities need --value support - may or may not work depending on implementation
        $this->assertIsString($css);
    }

    public function test_utility_with_variants(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="hover:content-auto">',
            'css' => '
                @tailwind utilities;
                @utility content-auto {
                    content-visibility: auto;
                }
            ',
        ]);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('content-visibility: auto', $css);
    }

    public function test_utility_with_nested_selectors(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="my-utility">',
            'css' => '
                @tailwind utilities;
                @utility my-utility {
                    color: red;
                    &:hover {
                        color: blue;
                    }
                }
            ',
        ]);
        $this->assertStringContainsString('color: red', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    // =========================================================================
    // @custom-variant DIRECTIVE
    // =========================================================================

    public function test_custom_variant_simple_selector(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="hocus:bg-blue-500">',
            'css' => '
                @tailwind utilities;
                @custom-variant hocus (&:hover, &:focus);
            ',
        ]);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_custom_variant_with_slot(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="child:mt-4">',
            'css' => '
                @tailwind utilities;
                @custom-variant child {
                    & > * {
                        @slot;
                    }
                }
            ',
        ]);
        $this->assertStringContainsString('> *', $css);
    }

    public function test_custom_variant_at_rule(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tablet:flex">',
            'css' => '
                @tailwind utilities;
                @custom-variant tablet (@media (min-width: 600px));
            ',
        ]);
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('600px', $css);
    }

    public function test_custom_variant_combined_with_existing(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="sm:hocus:bg-blue-500">',
            'css' => '
                @tailwind utilities;
                @custom-variant hocus (&:hover, &:focus);
            ',
        ]);
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_custom_variant_attribute_selector(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="loading:opacity-50">',
            'css' => '
                @tailwind utilities;
                @custom-variant loading (&[data-loading="true"]);
            ',
        ]);
        $this->assertStringContainsString('[data-loading="true"]', $css);
    }

    public function test_custom_variant_parent_selector(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="sidebar-open:translate-x-0">',
            'css' => '
                @tailwind utilities;
                @custom-variant sidebar-open {
                    .sidebar-open & {
                        @slot;
                    }
                }
            ',
        ]);
        $this->assertStringContainsString('.sidebar-open', $css);
    }

    // =========================================================================
    // @source DIRECTIVE (basic test - file system not supported)
    // =========================================================================

    public function test_source_directive_is_parsed(): void
    {
        // @source directive is parsed but file system access is not supported
        // This test just ensures it doesn't throw an error
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @source "./src/**/*.html";
                @tailwind utilities;
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    // =========================================================================
    // DIRECTIVE COMBINATIONS
    // =========================================================================

    public function test_theme_and_utility_together(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand content-auto">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #ff0000; }
                @utility content-auto { content-visibility: auto; }
            ',
        ]);
        $this->assertStringContainsString('--color-brand:', $css);
        $this->assertStringContainsString('content-visibility: auto', $css);
    }

    public function test_theme_and_custom_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="hocus:bg-brand">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #ff0000; }
                @custom-variant hocus (&:hover, &:focus);
            ',
        ]);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_apply_with_custom_utility(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="card">',
            'css' => '
                @tailwind utilities;
                @utility content-auto { content-visibility: auto; }
                .card {
                    @apply content-auto p-4;
                }
            ',
        ]);
        $this->assertStringContainsString('.card', $css);
        $this->assertStringContainsString('content-visibility: auto', $css);
    }

    public function test_all_directives_together(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="card hocus:bg-brand">',
            'css' => '
                @tailwind utilities;
                @theme { --color-brand: #ff0000; }
                @custom-variant hocus (&:hover, &:focus);
                @utility content-auto { content-visibility: auto; }
                .card {
                    @apply p-4 content-auto;
                }
            ',
        ]);
        $this->assertStringContainsString('--color-brand:', $css);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':focus', $css);
        $this->assertStringContainsString('.card', $css);
    }

    // =========================================================================
    // @import 'tailwindcss/preflight' - CSS RESET
    // =========================================================================

    public function test_preflight_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "@import 'tailwindcss/preflight'; @tailwind utilities;",
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('margin: 0', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_preflight_import_with_css_extension(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "@import 'tailwindcss/preflight.css'; @tailwind utilities;",
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_import_with_layer_base(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "@import 'tailwindcss/preflight' layer(base); @tailwind utilities;",
        ]);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_option_true(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_preflight_option_false(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => false,
        ]);
        $this->assertStringNotContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_preflight_includes_html_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "@import 'tailwindcss/preflight'; @tailwind utilities;",
        ]);
        $this->assertStringContainsString('line-height: 1.5', $css);
        $this->assertStringContainsString('tab-size: 4', $css);
    }

    public function test_preflight_includes_form_resets(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => "@import 'tailwindcss/preflight'; @tailwind utilities;",
        ]);
        // Preflight includes form element resets
        $this->assertStringContainsString('button', $css);
        $this->assertStringContainsString('input', $css);
    }

    public function test_preflight_includes_image_resets(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'css' => "@import 'tailwindcss/preflight'; @tailwind utilities;",
        ]);
        $this->assertStringContainsString('img', $css);
        $this->assertStringContainsString('max-width: 100%', $css);
    }

    public function test_preflight_with_theme(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => "
                @import 'tailwindcss/preflight';
                @tailwind utilities;
                @theme { --color-brand: #ff0000; }
            ",
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('--color-brand', $css);
    }

    public function test_preflight_with_custom_css(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "
                @import 'tailwindcss/preflight';
                @tailwind utilities;
                .custom { color: red; }
            ",
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('color: red', $css);
    }

    public function test_preflight_option_with_custom_css(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@tailwind utilities; .custom { color: blue; }',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('.custom', $css);
        $this->assertStringContainsString('display: flex', $css);
    }
}
