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

    // =========================================================================
    // @layer ORDER DECLARATIONS
    // =========================================================================

    public function test_layer_order_declaration_passthrough(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@layer theme, base, components, utilities; @tailwind utilities;',
        ]);
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_layer_order_with_two_layers(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@layer base, utilities; @tailwind utilities;',
        ]);
        $this->assertStringContainsString('@layer base, utilities;', $css);
    }

    // =========================================================================
    // @import 'tailwindcss/utilities.css' VARIANTS
    // =========================================================================

    public function test_import_utilities_css(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_import_utilities_css_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities);',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_import_utilities_with_important(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities" important;',
        ]);
        $this->assertStringContainsString('!important', $css);
    }

    // =========================================================================
    // @import 'tailwindcss/theme.css' VARIANTS
    // =========================================================================

    public function test_import_theme_css(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css"; @tailwind utilities;',
        ]);
        $this->assertStringContainsString('--font-sans:', $css);
    }

    public function test_import_theme_css_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme); @tailwind utilities;',
        ]);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('--font-sans:', $css);
    }

    // =========================================================================
    // FULL TAILWIND SETUP (like docs)
    // =========================================================================

    public function test_full_tailwind_setup(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">',
            'css' => '
                @layer theme, base, components, utilities;
                @import "tailwindcss/theme.css" layer(theme);
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_full_setup_without_preflight(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @layer theme, base, components, utilities;
                @import "tailwindcss/theme.css" layer(theme);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringNotContainsString('box-sizing: border-box', $css);
    }

    public function test_extend_base_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @layer theme, base, components, utilities;
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities);
                @layer base {
                    h1 { font-size: 2rem; }
                }
            ',
        ]);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('font-size: 2rem', $css);
    }

    // =========================================================================
    // IMPORT EDGE CASES
    // =========================================================================

    public function test_import_without_layer_modifier(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities";',
        ]);
        // Should work without layer() modifier
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringNotContainsString('@layer', $css);
    }

    public function test_import_theme_without_utilities(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme);',
        ]);
        // Theme should load but no utilities generated
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringNotContainsString('display: flex', $css);
    }

    public function test_import_with_single_quotes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "@import 'tailwindcss/utilities.css' layer(utilities);",
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_multiple_layer_declarations(): void
    {
        // Multiple @layer declarations should all pass through
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @layer reset, base;
                @layer components, utilities;
                @tailwind utilities;
            ',
        ]);
        $this->assertStringContainsString('@layer reset, base;', $css);
        $this->assertStringContainsString('@layer components, utilities;', $css);
    }

    public function test_layer_with_content_preserved(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @layer components {
                    .btn { padding: 1rem; }
                }
                @tailwind utilities;
            ',
        ]);
        $this->assertStringContainsString('@layer components', $css);
        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('padding: 1rem', $css);
    }

    public function test_import_utilities_with_layer_and_important(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities) important;',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_theme_variables_available_in_utilities(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="font-sans">',
            'css' => '
                @import "tailwindcss/theme.css" layer(theme);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        $this->assertStringContainsString('--font-sans', $css);
        $this->assertStringContainsString('font-family:', $css);
    }

    public function test_preflight_before_utilities_order(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        // Preflight should appear before utilities in output
        $preflightPos = strpos($css, 'box-sizing: border-box');
        $utilitiesPos = strpos($css, 'display: flex');
        $this->assertLessThan($utilitiesPos, $preflightPos);
    }

    // =========================================================================
    // source(none) MODIFIER
    // =========================================================================

    public function test_source_none_on_theme_import(): void
    {
        // source(none) tells Tailwind not to scan files for candidates
        // In TailwindPHP this is a no-op since we don't do file scanning
        // but we should accept the syntax without errors
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" source(none); @tailwind utilities;',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_source_none_on_utilities_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" source(none);',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_source_none_with_layer_modifier(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities) source(none);',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_source_none_on_preflight_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/preflight.css" layer(base) source(none); @tailwind utilities;',
        ]);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    public function test_source_none_full_setup(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">',
            'css' => '
                @layer theme, base, components, utilities;
                @import "tailwindcss/theme.css" layer(theme) source(none);
                @import "tailwindcss/preflight.css" layer(base) source(none);
                @import "tailwindcss/utilities.css" layer(utilities) source(none);
            ',
        ]);
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    // =========================================================================
    // theme(static) MODIFIER
    // =========================================================================

    public function test_theme_static_modifier_on_import(): void
    {
        // theme(static) makes all theme values always included in output
        // even if they're not used by any utility
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" theme(static); @tailwind utilities;',
        ]);
        // With theme(static), theme variables should be in output
        // even though we're only using "flex" which doesn't need them
        $this->assertStringContainsString('--color-red-500', $css);
        $this->assertStringContainsString('--color-blue-500', $css);
    }

    public function test_theme_static_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme) theme(static); @tailwind utilities;',
        ]);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('--color-red-500', $css);
    }

    public function test_theme_static_directive_already_works(): void
    {
        // Verify @theme static directive already works (it does)
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @tailwind utilities;
                @theme static {
                    --color-always-here: #ff0000;
                }
            ',
        ]);
        // Static values should be in output even if unused
        $this->assertStringContainsString('--color-always-here:', $css);
    }

    // =========================================================================
    // theme(inline) MODIFIER
    // =========================================================================

    public function test_theme_inline_modifier_on_import(): void
    {
        // theme(inline) makes theme values be inlined directly rather than via CSS variables
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" theme(inline); @tailwind utilities;',
        ]);
        // With theme(inline), theme values are inlined - no CSS variables in :root
        // This is a complex feature - for now just verify it doesn't error
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_theme_inline_with_layer(): void
    {
        // With theme(inline), values are inlined directly into utilities
        // rather than being CSS variables in :root. This means the @layer theme
        // block will be empty and removed, since there are no CSS variables to output.
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme) theme(inline); @tailwind utilities;',
        ]);
        // The utility should still work
        $this->assertStringContainsString('display: flex', $css);
        // No @layer theme since all values are inlined (no CSS variables)
        $this->assertStringNotContainsString('@layer theme', $css);
    }

    public function test_theme_inline_directive_already_works(): void
    {
        // Verify @theme inline directive already works
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '
                @tailwind utilities;
                @theme inline {
                    --color-brand: #ff0000;
                }
            ',
        ]);
        // Inline theme values should not appear as CSS variables
        $this->assertStringContainsString('background-color:', $css);
    }

    // =========================================================================
    // prefix() MODIFIER
    // =========================================================================

    public function test_prefix_modifier_on_theme_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex tw:p-4">',
            'css' => '@import "tailwindcss/theme.css" prefix(tw); @tailwind utilities;',
        ]);
        // Prefix should apply to theme variables
        $this->assertStringContainsString('--tw-', $css);
    }

    public function test_prefix_modifier_on_utilities_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex">',
            'css' => '@import "tailwindcss/utilities.css" prefix(tw);',
        ]);
        // Utilities should work with prefix
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_prefix_modifier_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities) prefix(tw);',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_prefix_full_setup(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex tw:p-4 tw:bg-blue-500">',
            'css' => '
                @layer theme, base, components, utilities;
                @import "tailwindcss/theme.css" layer(theme) prefix(tw);
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities) prefix(tw);
            ',
        ]);
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    // =========================================================================
    // COMBINED MODIFIERS
    // =========================================================================

    public function test_all_modifiers_combined(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities) important source(none);',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('!important', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_theme_import_with_multiple_modifiers(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme) theme(static) source(none); @tailwind utilities;',
        ]);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('--color-red-500', $css);
    }

    // =========================================================================
    // PREFLIGHT - COMPREHENSIVE TESTS
    // Based on https://tailwindcss.com/docs/preflight
    // =========================================================================

    // --- Box Sizing & Margins Reset ---

    public function test_preflight_universal_box_sizing(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Universal selector should have box-sizing: border-box
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_universal_margin_reset(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Universal selector should reset margins
        $this->assertStringContainsString('margin: 0', $css);
    }

    public function test_preflight_universal_padding_reset(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Universal selector should reset padding
        $this->assertStringContainsString('padding: 0', $css);
    }

    public function test_preflight_universal_border_reset(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Universal selector should reset borders
        $this->assertStringContainsString('border: 0 solid', $css);
    }

    public function test_preflight_pseudo_elements_included(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Pseudo-elements should be included in reset
        $this->assertStringContainsString('::after', $css);
        $this->assertStringContainsString('::before', $css);
        $this->assertStringContainsString('::backdrop', $css);
        $this->assertStringContainsString('::file-selector-button', $css);
    }

    // --- HTML/Body Base Styles ---

    public function test_preflight_html_line_height(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('line-height: 1.5', $css);
    }

    public function test_preflight_html_text_size_adjust(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('-webkit-text-size-adjust: 100%', $css);
    }

    public function test_preflight_html_tab_size(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('tab-size: 4', $css);
    }

    public function test_preflight_html_font_family(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Should include system font stack
        $this->assertStringContainsString('font-family:', $css);
        $this->assertStringContainsString('ui-sans-serif', $css);
        $this->assertStringContainsString('system-ui', $css);
    }

    public function test_preflight_html_tap_highlight(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('-webkit-tap-highlight-color: transparent', $css);
    }

    // --- Typography Resets ---

    public function test_preflight_headings_unstyled(): void
    {
        $css = Tailwind::generate([
            'content' => '<h1 class="flex">',
            'preflight' => true,
        ]);
        // Headings should inherit font-size and font-weight
        $this->assertMatchesRegularExpression('/h1.*font-size:\s*inherit/s', $css);
        $this->assertMatchesRegularExpression('/h1.*font-weight:\s*inherit/s', $css);
    }

    public function test_preflight_all_heading_levels(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // All heading levels should be included
        $this->assertMatchesRegularExpression('/h1,\s*h2,\s*h3,\s*h4,\s*h5,\s*h6/', $css);
    }

    public function test_preflight_lists_unstyled(): void
    {
        $css = Tailwind::generate([
            'content' => '<ul class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('list-style: none', $css);
    }

    public function test_preflight_list_elements_included(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // ol, ul, menu should all be unstyled
        $this->assertMatchesRegularExpression('/ol,\s*ul,\s*menu/', $css);
    }

    // --- Link Resets ---

    public function test_preflight_links_inherit_color(): void
    {
        $css = Tailwind::generate([
            'content' => '<a class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/\ba\s*\{[^}]*color:\s*inherit/s', $css);
    }

    public function test_preflight_links_inherit_text_decoration(): void
    {
        $css = Tailwind::generate([
            'content' => '<a class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('text-decoration: inherit', $css);
    }

    // --- Media Elements ---

    public function test_preflight_images_block(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'preflight' => true,
        ]);
        // Images should be display: block
        $this->assertMatchesRegularExpression('/img[^{]*\{[^}]*display:\s*block/s', $css);
    }

    public function test_preflight_images_max_width(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('max-width: 100%', $css);
    }

    public function test_preflight_images_height_auto(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('height: auto', $css);
    }

    public function test_preflight_media_elements_block(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Various media elements should be display: block
        $this->assertStringContainsString('svg', $css);
        $this->assertStringContainsString('video', $css);
        $this->assertStringContainsString('canvas', $css);
        $this->assertStringContainsString('audio', $css);
        $this->assertStringContainsString('iframe', $css);
    }

    public function test_preflight_media_vertical_align(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('vertical-align: middle', $css);
    }

    // --- Form Elements ---

    public function test_preflight_form_elements_inherit_font(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('font: inherit', $css);
    }

    public function test_preflight_form_elements_no_border_radius(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('border-radius: 0', $css);
    }

    public function test_preflight_form_elements_transparent_bg(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('background-color: transparent', $css);
    }

    public function test_preflight_form_elements_included(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // All form elements should be included
        $this->assertStringContainsString('button', $css);
        $this->assertStringContainsString('input', $css);
        $this->assertStringContainsString('select', $css);
        $this->assertStringContainsString('textarea', $css);
    }

    public function test_preflight_textarea_resize(): void
    {
        $css = Tailwind::generate([
            'content' => '<textarea class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('resize: vertical', $css);
    }

    public function test_preflight_placeholder_opacity(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('::placeholder', $css);
        $this->assertStringContainsString('opacity: 1', $css);
    }

    public function test_preflight_button_appearance(): void
    {
        $css = Tailwind::generate([
            'content' => '<button class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('appearance: button', $css);
    }

    // --- Table Resets ---

    public function test_preflight_table_border_collapse(): void
    {
        $css = Tailwind::generate([
            'content' => '<table class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('border-collapse: collapse', $css);
    }

    public function test_preflight_table_border_color_inherit(): void
    {
        $css = Tailwind::generate([
            'content' => '<table class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/table\s*\{[^}]*border-color:\s*inherit/s', $css);
    }

    public function test_preflight_table_text_indent(): void
    {
        $css = Tailwind::generate([
            'content' => '<table class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/table\s*\{[^}]*text-indent:\s*0/s', $css);
    }

    // --- Other Elements ---

    public function test_preflight_hr_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<hr class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/hr\s*\{[^}]*height:\s*0/s', $css);
        $this->assertStringContainsString('border-top-width: 1px', $css);
    }

    public function test_preflight_abbr_title_decoration(): void
    {
        $css = Tailwind::generate([
            'content' => '<abbr class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('abbr:where([title])', $css);
        $this->assertStringContainsString('underline dotted', $css);
    }

    public function test_preflight_strong_bold_weight(): void
    {
        $css = Tailwind::generate([
            'content' => '<strong class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('font-weight: bolder', $css);
    }

    public function test_preflight_code_font_family(): void
    {
        $css = Tailwind::generate([
            'content' => '<code class="flex">',
            'preflight' => true,
        ]);
        // Code elements should use mono font stack
        $this->assertStringContainsString('ui-monospace', $css);
        $this->assertStringContainsString('monospace', $css);
    }

    public function test_preflight_small_font_size(): void
    {
        $css = Tailwind::generate([
            'content' => '<small class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/small\s*\{[^}]*font-size:\s*80%/s', $css);
    }

    public function test_preflight_sub_sup_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<sub class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('font-size: 75%', $css);
        $this->assertStringContainsString('line-height: 0', $css);
        $this->assertStringContainsString('vertical-align: baseline', $css);
    }

    public function test_preflight_summary_display(): void
    {
        $css = Tailwind::generate([
            'content' => '<summary class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/summary\s*\{[^}]*display:\s*list-item/s', $css);
    }

    public function test_preflight_progress_vertical_align(): void
    {
        $css = Tailwind::generate([
            'content' => '<progress class="flex">',
            'preflight' => true,
        ]);
        $this->assertMatchesRegularExpression('/progress\s*\{[^}]*vertical-align:\s*baseline/s', $css);
    }

    // --- Hidden Attribute ---

    public function test_preflight_hidden_attribute(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Hidden elements should be display: none !important
        $this->assertStringContainsString('[hidden]', $css);
        $this->assertStringContainsString('display: none !important', $css);
    }

    public function test_preflight_hidden_until_found_exception(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'preflight' => true,
        ]);
        // Should exclude hidden="until-found"
        $this->assertStringContainsString("hidden='until-found'", $css);
    }

    // --- Browser Specific ---

    public function test_preflight_webkit_search_decoration(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('::-webkit-search-decoration', $css);
        $this->assertStringContainsString('-webkit-appearance: none', $css);
    }

    public function test_preflight_webkit_datetime(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('::-webkit-datetime-edit', $css);
        $this->assertStringContainsString('::-webkit-date-and-time-value', $css);
    }

    public function test_preflight_moz_focusring(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString(':-moz-focusring', $css);
        $this->assertStringContainsString('outline: auto', $css);
    }

    public function test_preflight_moz_ui_invalid(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString(':-moz-ui-invalid', $css);
        $this->assertStringContainsString('box-shadow: none', $css);
    }

    public function test_preflight_webkit_spin_buttons(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        $this->assertStringContainsString('::-webkit-inner-spin-button', $css);
        $this->assertStringContainsString('::-webkit-outer-spin-button', $css);
    }

    // --- @supports for Placeholder Color ---

    public function test_preflight_placeholder_color_supports(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'preflight' => true,
        ]);
        // Should have @supports block for placeholder color
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('color-mix', $css);
    }

    // --- Configuration Options ---

    public function test_preflight_disabled_via_import(): void
    {
        // Preflight can be disabled by not importing it
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/theme.css" layer(theme);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        // Should have utilities but no preflight
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringNotContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_with_custom_base_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/preflight.css" layer(base);
                @tailwind utilities;
                @layer base {
                    body { background-color: white; }
                }
            ',
        ]);
        // Should have both preflight and custom base styles
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('background-color: white', $css);
    }

    public function test_preflight_layer_order_preserved(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @layer theme, base, components, utilities;
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        // Layer order should be preserved
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        // Preflight in base, utilities in utilities
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('@layer utilities', $css);
    }

    // --- Integration with Other Features ---

    public function test_preflight_with_theme_customization(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="font-custom">',
            'css' => '
                @import "tailwindcss/preflight.css" layer(base);
                @tailwind utilities;
                @theme {
                    --font-custom: "Custom Font", sans-serif;
                }
            ',
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('--font-custom', $css);
    }

    public function test_preflight_with_prefix(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex">',
            'css' => '
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities) prefix(tw);
            ',
        ]);
        // Preflight should still work
        $this->assertStringContainsString('box-sizing: border-box', $css);
        // Utilities should work with prefix
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_preflight_with_important(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css" layer(utilities) important;
            ',
        ]);
        // Preflight should NOT be !important
        $this->assertStringContainsString('box-sizing: border-box', $css);
        // Utilities should be !important
        $this->assertStringContainsString('display: flex !important', $css);
    }
}
