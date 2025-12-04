<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for Tailwind CSS v4 directives and imports.
 */
class DirectivesTest extends TestCase
{
    // =========================================================================
    // @import "tailwindcss" - FULL IMPORT
    // =========================================================================

    public function test_full_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_full_import_includes_preflight(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_full_import_includes_theme(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="font-sans">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('--font-sans', $css);
    }

    public function test_default_generate_uses_full_import(): void
    {
        // Default generate() should use @import "tailwindcss"
        $css = Tailwind::generate('<div class="flex p-4">');
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    // =========================================================================
    // @import "tailwindcss/utilities.css"
    // =========================================================================

    public function test_utilities_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">',
            'css' => '@import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_utilities_import_with_css_extension(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_utilities_import_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities);',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_utilities_import_with_important(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" important;',
        ]);
        $this->assertStringContainsString('!important', $css);
    }

    public function test_utilities_import_with_layer_and_important(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/utilities.css" layer(utilities) important;',
        ]);
        $this->assertStringContainsString('@layer utilities', $css);
        $this->assertStringContainsString('!important', $css);
    }

    // =========================================================================
    // @import "tailwindcss/theme"
    // =========================================================================

    public function test_theme_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--font-sans:', $css);
    }

    public function test_theme_import_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('--font-sans:', $css);
    }

    public function test_theme_import_without_utilities(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme);',
        ]);
        // Theme should load but no utilities generated
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringNotContainsString('display: flex', $css);
    }

    // =========================================================================
    // @import "tailwindcss/preflight"
    // =========================================================================

    public function test_preflight_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/preflight"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('margin: 0', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_preflight_import_with_css_extension(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/preflight.css"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_import_with_layer_base(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/preflight" layer(base); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('@layer base', $css);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    // =========================================================================
    // GRANULAR IMPORTS (without preflight)
    // =========================================================================

    public function test_granular_imports_without_preflight(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringNotContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    // =========================================================================
    // @theme DIRECTIVE
    // =========================================================================

    public function test_theme_custom_color(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand text-brand">',
            'css' => '@import "tailwindcss/utilities.css"; @theme { --color-brand: #3b82f6; }',
        ]);
        $this->assertStringContainsString('--color-brand:', $css);
        $this->assertStringContainsString('background-color:', $css);
        $this->assertStringContainsString('color:', $css);
    }

    public function test_theme_custom_spacing(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="p-huge m-huge">',
            'css' => '@import "tailwindcss/utilities.css"; @theme { --spacing-huge: 100px; }',
        ]);
        $this->assertStringContainsString('--spacing-huge:', $css);
    }

    public function test_theme_custom_font_family(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="font-custom">',
            'css' => '@import "tailwindcss/utilities.css"; @theme { --font-custom: "Comic Sans MS", cursive; }',
        ]);
        $this->assertStringContainsString('font-family:', $css);
        $this->assertStringContainsString('--font-custom', $css);
    }

    public function test_theme_custom_font_size(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="text-custom">',
            'css' => '@import "tailwindcss/utilities.css"; @theme { --text-custom: 18px; }',
        ]);
        $this->assertStringContainsString('font-size:', $css);
    }

    public function test_theme_custom_breakpoint(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom:flex">',
            'css' => '@import "tailwindcss/utilities.css"; @theme { --breakpoint-custom: 900px; }',
        ]);
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('900px', $css);
    }

    public function test_theme_multiple_values(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-primary text-secondary p-custom">',
            'css' => '
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
                @theme reference {
                    --color-primary: #ff0000;
                }
            ',
        ]);
        $this->assertStringContainsString('bg-primary', $css);
        $this->assertStringContainsString('var(--color-primary', $css);
    }

    public function test_theme_inline(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '
                @import "tailwindcss/utilities.css";
                @theme inline {
                    --color-brand: #ff0000;
                }
            ',
        ]);
        $this->assertStringContainsString('background-color:', $css);
    }

    public function test_theme_with_keyframes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="animate-custom">',
            'css' => '
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
                @theme static {
                    --color-always-present: #ff0000;
                }
            ',
        ]);
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
                @import "tailwindcss/utilities.css";
                .btn {
                    @apply px-4 py-2 bg-blue-500;
                }
            ',
        ]);
        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('padding-inline:', $css);
        $this->assertStringContainsString('padding-block:', $css);
    }

    public function test_apply_multiple_classes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="card">',
            'css' => '
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
                @utility tab-* {
                    tab-size: --value(--tab-size, integer);
                }
            ',
        ]);
        $this->assertIsString($css);
    }

    public function test_utility_with_variants(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="hover:content-auto">',
            'css' => '
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @source "./src/**/*.html";
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
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
    // @layer ORDER DECLARATIONS
    // =========================================================================

    public function test_layer_order_declaration_passthrough(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@layer theme, base, components, utilities; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_layer_order_with_two_layers(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@layer base, utilities; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('@layer base, utilities;', $css);
    }

    public function test_multiple_layer_declarations(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @layer reset, base;
                @layer components, utilities;
                @import "tailwindcss/utilities.css";
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
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('@layer components', $css);
        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('padding: 1rem', $css);
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
            'css' => '@import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringNotContainsString('@layer', $css);
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
        $preflightPos = strpos($css, 'box-sizing: border-box');
        $utilitiesPos = strpos($css, 'display: flex');
        $this->assertLessThan($utilitiesPos, $preflightPos);
    }

    // =========================================================================
    // source(none) MODIFIER
    // =========================================================================

    public function test_source_none_on_theme_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" source(none); @import "tailwindcss/utilities.css";',
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
            'css' => '@import "tailwindcss/preflight.css" layer(base) source(none); @import "tailwindcss/utilities.css";',
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
    // theme(static) MODIFIER & CSS VARIABLE TREE-SHAKING
    // =========================================================================

    public function test_tree_shaking_only_includes_used_color_variables(): void
    {
        // Without theme(static), only used variables should be included
        $css = Tailwind::generate([
            'content' => '<div class="text-red-500">',
            'css' => '@import "tailwindcss/theme.css"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--color-red-500', $css);
        $this->assertStringNotContainsString('--color-blue-500', $css);
        $this->assertStringNotContainsString('--color-green-500', $css);
    }

    public function test_tree_shaking_only_includes_used_spacing_variable(): void
    {
        // Spacing variable should only be included when spacing utilities are used
        $css = Tailwind::generate([
            'content' => '<div class="text-red-500">',
            'css' => '@import "tailwindcss/theme.css"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringNotContainsString('--spacing', $css);

        $css = Tailwind::generate([
            'content' => '<div class="p-4">',
            'css' => '@import "tailwindcss/theme.css"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--spacing', $css);
    }

    public function test_theme_static_includes_all_variables(): void
    {
        // With theme(static), ALL variables should be included even if unused
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" theme(static); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--color-red-500', $css);
        $this->assertStringContainsString('--color-blue-500', $css);
        $this->assertStringContainsString('--color-green-500', $css);
        $this->assertStringContainsString('--spacing', $css);
    }

    public function test_theme_static_modifier_on_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" theme(static); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--color-red-500', $css);
        $this->assertStringContainsString('--color-blue-500', $css);
    }

    public function test_theme_static_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme) theme(static); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('@layer theme', $css);
        $this->assertStringContainsString('--color-red-500', $css);
    }

    public function test_theme_static_directive_already_works(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/utilities.css";
                @theme static {
                    --color-always-here: #ff0000;
                }
            ',
        ]);
        $this->assertStringContainsString('--color-always-here:', $css);
    }

    // =========================================================================
    // theme(inline) MODIFIER
    // =========================================================================

    public function test_theme_inline_modifier_on_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" theme(inline); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_theme_inline_with_layer(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss/theme.css" layer(theme) theme(inline); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringNotContainsString('@layer theme', $css);
    }

    public function test_theme_inline_directive_already_works(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">',
            'css' => '
                @import "tailwindcss/utilities.css";
                @theme inline {
                    --color-brand: #ff0000;
                }
            ',
        ]);
        $this->assertStringContainsString('background-color:', $css);
    }

    // =========================================================================
    // prefix() MODIFIER
    // =========================================================================

    public function test_prefix_modifier_on_theme_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex tw:p-4">',
            'css' => '@import "tailwindcss/theme.css" prefix(tw); @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--tw-', $css);
    }

    public function test_prefix_modifier_on_utilities_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="tw:flex">',
            'css' => '@import "tailwindcss/utilities.css" prefix(tw);',
        ]);
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
            'css' => '@import "tailwindcss/theme.css" layer(theme) theme(static) source(none); @import "tailwindcss/utilities.css";',
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
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_universal_margin_reset(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('margin: 0', $css);
    }

    public function test_preflight_universal_padding_reset(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('padding: 0', $css);
    }

    public function test_preflight_universal_border_reset(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('border: 0 solid', $css);
    }

    public function test_preflight_pseudo_elements_included(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
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
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('line-height: 1.5', $css);
    }

    public function test_preflight_html_text_size_adjust(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('-webkit-text-size-adjust: 100%', $css);
    }

    public function test_preflight_html_tab_size(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('tab-size: 4', $css);
    }

    public function test_preflight_html_font_family(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('font-family:', $css);
        $this->assertStringContainsString('ui-sans-serif', $css);
        $this->assertStringContainsString('system-ui', $css);
    }

    public function test_preflight_html_tap_highlight(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('-webkit-tap-highlight-color: transparent', $css);
    }

    // --- Typography Resets ---

    public function test_preflight_headings_unstyled(): void
    {
        $css = Tailwind::generate([
            'content' => '<h1 class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/h1.*font-size:\s*inherit/s', $css);
        $this->assertMatchesRegularExpression('/h1.*font-weight:\s*inherit/s', $css);
    }

    public function test_preflight_all_heading_levels(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/h1,\s*h2,\s*h3,\s*h4,\s*h5,\s*h6/', $css);
    }

    public function test_preflight_lists_unstyled(): void
    {
        $css = Tailwind::generate([
            'content' => '<ul class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('list-style: none', $css);
    }

    public function test_preflight_list_elements_included(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/ol,\s*ul,\s*menu/', $css);
    }

    // --- Link Resets ---

    public function test_preflight_links_inherit_color(): void
    {
        $css = Tailwind::generate([
            'content' => '<a class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/\ba\s*\{[^}]*color:\s*inherit/s', $css);
    }

    public function test_preflight_links_inherit_text_decoration(): void
    {
        $css = Tailwind::generate([
            'content' => '<a class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('text-decoration: inherit', $css);
    }

    // --- Media Elements ---

    public function test_preflight_images_block(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/img[^{]*\{[^}]*display:\s*block/s', $css);
    }

    public function test_preflight_images_max_width(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('max-width: 100%', $css);
    }

    public function test_preflight_images_height_auto(): void
    {
        $css = Tailwind::generate([
            'content' => '<img class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('height: auto', $css);
    }

    public function test_preflight_media_elements_block(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
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
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('vertical-align: middle', $css);
    }

    // --- Form Elements ---

    public function test_preflight_form_elements_inherit_font(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('font: inherit', $css);
    }

    public function test_preflight_form_elements_no_border_radius(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('border-radius: 0', $css);
    }

    public function test_preflight_form_elements_transparent_bg(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('background-color: transparent', $css);
    }

    public function test_preflight_form_elements_included(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('button', $css);
        $this->assertStringContainsString('input', $css);
        $this->assertStringContainsString('select', $css);
        $this->assertStringContainsString('textarea', $css);
    }

    public function test_preflight_textarea_resize(): void
    {
        $css = Tailwind::generate([
            'content' => '<textarea class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('resize: vertical', $css);
    }

    public function test_preflight_placeholder_opacity(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('::placeholder', $css);
        $this->assertStringContainsString('opacity: 1', $css);
    }

    public function test_preflight_button_appearance(): void
    {
        $css = Tailwind::generate([
            'content' => '<button class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('appearance: button', $css);
    }

    // --- Table Resets ---

    public function test_preflight_table_border_collapse(): void
    {
        $css = Tailwind::generate([
            'content' => '<table class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('border-collapse: collapse', $css);
    }

    public function test_preflight_table_border_color_inherit(): void
    {
        $css = Tailwind::generate([
            'content' => '<table class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/table\s*\{[^}]*border-color:\s*inherit/s', $css);
    }

    public function test_preflight_table_text_indent(): void
    {
        $css = Tailwind::generate([
            'content' => '<table class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/table\s*\{[^}]*text-indent:\s*0/s', $css);
    }

    // --- Other Elements ---

    public function test_preflight_hr_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<hr class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/hr\s*\{[^}]*height:\s*0/s', $css);
        $this->assertStringContainsString('border-top-width: 1px', $css);
    }

    public function test_preflight_abbr_title_decoration(): void
    {
        $css = Tailwind::generate([
            'content' => '<abbr class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('abbr:where([title])', $css);
        $this->assertStringContainsString('underline dotted', $css);
    }

    public function test_preflight_strong_bold_weight(): void
    {
        $css = Tailwind::generate([
            'content' => '<strong class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('font-weight: bolder', $css);
    }

    public function test_preflight_code_font_family(): void
    {
        $css = Tailwind::generate([
            'content' => '<code class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('ui-monospace', $css);
        $this->assertStringContainsString('monospace', $css);
    }

    public function test_preflight_small_font_size(): void
    {
        $css = Tailwind::generate([
            'content' => '<small class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/small\s*\{[^}]*font-size:\s*80%/s', $css);
    }

    public function test_preflight_sub_sup_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<sub class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('font-size: 75%', $css);
        $this->assertStringContainsString('line-height: 0', $css);
        $this->assertStringContainsString('vertical-align: baseline', $css);
    }

    public function test_preflight_summary_display(): void
    {
        $css = Tailwind::generate([
            'content' => '<summary class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/summary\s*\{[^}]*display:\s*list-item/s', $css);
    }

    public function test_preflight_progress_vertical_align(): void
    {
        $css = Tailwind::generate([
            'content' => '<progress class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertMatchesRegularExpression('/progress\s*\{[^}]*vertical-align:\s*baseline/s', $css);
    }

    // --- Hidden Attribute ---

    public function test_preflight_hidden_attribute(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('[hidden]', $css);
        $this->assertStringContainsString('display: none !important', $css);
    }

    public function test_preflight_hidden_until_found_exception(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString("hidden='until-found'", $css);
    }

    // --- Browser Specific ---

    public function test_preflight_webkit_search_decoration(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('::-webkit-search-decoration', $css);
        $this->assertStringContainsString('-webkit-appearance: none', $css);
    }

    public function test_preflight_webkit_datetime(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('::-webkit-datetime-edit', $css);
        $this->assertStringContainsString('::-webkit-date-and-time-value', $css);
    }

    public function test_preflight_moz_focusring(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString(':-moz-focusring', $css);
        $this->assertStringContainsString('outline: auto', $css);
    }

    public function test_preflight_moz_ui_invalid(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString(':-moz-ui-invalid', $css);
        $this->assertStringContainsString('box-shadow: none', $css);
    }

    public function test_preflight_webkit_spin_buttons(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('::-webkit-inner-spin-button', $css);
        $this->assertStringContainsString('::-webkit-outer-spin-button', $css);
    }

    // --- @supports for Placeholder Color ---

    public function test_preflight_placeholder_color_supports(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="flex">',
            'css' => '@import "tailwindcss";',
        ]);
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('color-mix', $css);
    }

    // --- Configuration Options ---

    public function test_preflight_disabled_via_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/theme.css" layer(theme);
                @import "tailwindcss/utilities.css" layer(utilities);
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringNotContainsString('box-sizing: border-box', $css);
    }

    public function test_preflight_with_custom_base_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @import "tailwindcss/preflight.css" layer(base);
                @import "tailwindcss/utilities.css";
                @layer base {
                    body { background-color: white; }
                }
            ',
        ]);
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
        $this->assertStringContainsString('@layer theme, base, components, utilities;', $css);
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
                @import "tailwindcss/utilities.css";
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
        $this->assertStringContainsString('box-sizing: border-box', $css);
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
        $this->assertStringContainsString('box-sizing: border-box', $css);
        $this->assertStringContainsString('display: flex !important', $css);
    }
}
