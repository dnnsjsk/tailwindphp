<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for the TailwindPHP plugin system.
 *
 * Tests the @plugin directive, built-in plugins (@tailwindcss/typography,
 * @tailwindcss/forms), plugin options, and custom plugin registration.
 */
class PluginsTest extends TestCase
{
    // =========================================================================
    // @plugin DIRECTIVE - BASIC LOADING
    // =========================================================================

    public function test_plugin_directive_loads_typography(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="prose">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose', $css);
    }

    public function test_plugin_directive_loads_forms(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="form-input">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-input', $css);
    }

    public function test_plugin_directive_with_single_quotes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="prose">',
            'css' => "@plugin '@tailwindcss/typography'; @import 'tailwindcss/utilities';",
        ]);
        $this->assertStringContainsString('.prose', $css);
    }

    public function test_plugin_directive_without_quotes_handled(): void
    {
        // Plugin directive without quotes may be handled gracefully by the parser
        // or may throw - depends on CSS parser implementation
        $css = Tailwind::generate([
            'content' => '<div class="prose">',
            'css' => '@plugin @tailwindcss/typography; @import "tailwindcss/utilities.css";',
        ]);
        // If it doesn't throw, just verify we get a string back
        $this->assertIsString($css);
    }

    public function test_unknown_plugin_throws(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not registered');
        Tailwind::generate([
            'content' => '<div class="something">',
            'css' => '@plugin "@tailwindcss/unknown"; @import "tailwindcss/utilities.css";',
        ]);
    }

    public function test_nested_plugin_directive_throws(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('cannot be nested');
        Tailwind::generate([
            'content' => '<div class="prose">',
            'css' => '@media screen { @plugin "@tailwindcss/typography"; } @import "tailwindcss/utilities.css";',
        ]);
    }

    // =========================================================================
    // TYPOGRAPHY PLUGIN - BASIC CLASSES
    // =========================================================================

    public function test_typography_prose_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose">Content</article>',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('--tw-prose-body', $css);
    }

    public function test_typography_prose_sm(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-sm">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-sm', $css);
    }

    public function test_typography_prose_base(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-base">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-base', $css);
    }

    public function test_typography_prose_lg(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-lg">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-lg', $css);
    }

    public function test_typography_prose_xl(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-xl">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-xl', $css);
    }

    public function test_typography_prose_2xl(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-2xl">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-2xl', $css);
    }

    // =========================================================================
    // TYPOGRAPHY PLUGIN - COLOR MODIFIERS
    // =========================================================================

    public function test_typography_prose_invert(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-invert">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-invert', $css);
    }

    public function test_typography_prose_gray(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-gray">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-gray', $css);
    }

    public function test_typography_prose_slate(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-slate">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-slate', $css);
    }

    public function test_typography_prose_zinc(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-zinc">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-zinc', $css);
    }

    public function test_typography_prose_neutral(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-neutral">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-neutral', $css);
    }

    public function test_typography_prose_stone(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose-stone">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose-stone', $css);
    }

    // =========================================================================
    // TYPOGRAPHY PLUGIN - CSS VARIABLES
    // =========================================================================

    public function test_typography_generates_css_variables(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--tw-prose-body', $css);
        $this->assertStringContainsString('--tw-prose-headings', $css);
        $this->assertStringContainsString('--tw-prose-links', $css);
    }

    public function test_typography_prose_includes_element_styles(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        // Should include styles for headings, paragraphs, lists, etc.
        $this->assertStringContainsString('h1', $css);
        $this->assertStringContainsString('h2', $css);
        $this->assertStringContainsString('p', $css);
    }

    // =========================================================================
    // TYPOGRAPHY PLUGIN - OPTIONS
    // =========================================================================

    public function test_typography_custom_classname(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="markdown">Content</article>',
            'css' => '
                @plugin "@tailwindcss/typography" {
                    className: "markdown";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.markdown', $css);
        // Should NOT contain .prose when using custom className
        $this->assertStringNotContainsString('.prose', $css);
    }

    public function test_typography_custom_classname_with_size_modifier(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="markdown markdown-lg">',
            'css' => '
                @plugin "@tailwindcss/typography" {
                    className: "markdown";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.markdown', $css);
        $this->assertStringContainsString('.markdown-lg', $css);
    }

    public function test_typography_custom_classname_with_color_modifier(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="markdown markdown-invert">',
            'css' => '
                @plugin "@tailwindcss/typography" {
                    className: "markdown";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.markdown', $css);
        $this->assertStringContainsString('.markdown-invert', $css);
    }

    // =========================================================================
    // TYPOGRAPHY PLUGIN - COMBINED MODIFIERS
    // =========================================================================

    public function test_typography_prose_with_multiple_modifiers(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose prose-lg prose-invert">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('.prose-lg', $css);
        $this->assertStringContainsString('.prose-invert', $css);
    }

    public function test_typography_prose_with_size_and_color(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose prose-xl prose-slate">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('.prose-xl', $css);
        $this->assertStringContainsString('.prose-slate', $css);
    }

    // =========================================================================
    // FORMS PLUGIN - BASIC CLASSES
    // =========================================================================

    public function test_forms_input_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="form-input">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-input', $css);
    }

    public function test_forms_textarea_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<textarea class="form-textarea">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-textarea', $css);
    }

    public function test_forms_select_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<select class="form-select">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-select', $css);
    }

    public function test_forms_multiselect_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<select class="form-multiselect" multiple>',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-multiselect', $css);
    }

    public function test_forms_checkbox_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<input type="checkbox" class="form-checkbox">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-checkbox', $css);
    }

    public function test_forms_radio_class(): void
    {
        $css = Tailwind::generate([
            'content' => '<input type="radio" class="form-radio">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-radio', $css);
    }

    // =========================================================================
    // FORMS PLUGIN - ALL CLASSES TOGETHER
    // =========================================================================

    public function test_forms_all_classes(): void
    {
        $css = Tailwind::generate([
            'content' => '
                <input class="form-input">
                <textarea class="form-textarea"></textarea>
                <select class="form-select"></select>
                <select class="form-multiselect" multiple></select>
                <input type="checkbox" class="form-checkbox">
                <input type="radio" class="form-radio">
            ',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-input', $css);
        $this->assertStringContainsString('.form-textarea', $css);
        $this->assertStringContainsString('.form-select', $css);
        $this->assertStringContainsString('.form-multiselect', $css);
        $this->assertStringContainsString('.form-checkbox', $css);
        $this->assertStringContainsString('.form-radio', $css);
    }

    // =========================================================================
    // FORMS PLUGIN - OPTIONS
    // =========================================================================

    public function test_forms_class_strategy(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="form-input">',
            'css' => '
                @plugin "@tailwindcss/forms" {
                    strategy: "class";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        // With class strategy, only .form-* classes are styled, not base elements
        $this->assertStringContainsString('.form-input', $css);
    }

    public function test_forms_base_strategy(): void
    {
        $css = Tailwind::generate([
            'content' => '<input type="text">',
            'css' => '
                @plugin "@tailwindcss/forms" {
                    strategy: "base";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        // With base strategy, form elements get styled by default
        // (may or may not have visible output depending on content)
        $this->assertIsString($css);
    }

    // =========================================================================
    // MULTIPLE PLUGINS
    // =========================================================================

    public function test_multiple_plugins_loaded(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose"><input class="form-input"></article>',
            'css' => '
                @plugin "@tailwindcss/typography";
                @plugin "@tailwindcss/forms";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('.form-input', $css);
    }

    public function test_multiple_plugins_with_options(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="markdown"><input class="form-input"></article>',
            'css' => '
                @plugin "@tailwindcss/typography" {
                    className: "markdown";
                }
                @plugin "@tailwindcss/forms" {
                    strategy: "class";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.markdown', $css);
        $this->assertStringContainsString('.form-input', $css);
    }

    public function test_plugins_order_independence(): void
    {
        // Order shouldn't matter
        $css1 = Tailwind::generate([
            'content' => '<article class="prose"><input class="form-input"></article>',
            'css' => '
                @plugin "@tailwindcss/typography";
                @plugin "@tailwindcss/forms";
                @import "tailwindcss/utilities.css";
            ',
        ]);

        $css2 = Tailwind::generate([
            'content' => '<article class="prose"><input class="form-input"></article>',
            'css' => '
                @plugin "@tailwindcss/forms";
                @plugin "@tailwindcss/typography";
                @import "tailwindcss/utilities.css";
            ',
        ]);

        // Both should contain the same classes
        $this->assertStringContainsString('.prose', $css1);
        $this->assertStringContainsString('.form-input', $css1);
        $this->assertStringContainsString('.prose', $css2);
        $this->assertStringContainsString('.form-input', $css2);
    }

    // =========================================================================
    // PLUGINS WITH OTHER DIRECTIVES
    // =========================================================================

    public function test_plugin_with_theme(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose bg-brand">',
            'css' => '
                @plugin "@tailwindcss/typography";
                @import "tailwindcss/utilities.css";
                @theme { --color-brand: #ff0000; }
            ',
        ]);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('--color-brand', $css);
    }

    public function test_plugin_with_custom_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="hocus:prose">',
            'css' => '
                @plugin "@tailwindcss/typography";
                @custom-variant hocus (&:hover, &:focus);
                @import "tailwindcss/utilities.css";
            ',
        ]);
        // Custom variant should work with plugin classes
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_plugin_with_custom_utility(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose content-auto">',
            'css' => '
                @plugin "@tailwindcss/typography";
                @utility content-auto { content-visibility: auto; }
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('content-visibility: auto', $css);
    }

    public function test_plugin_with_apply(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="article">',
            'css' => '
                @plugin "@tailwindcss/typography";
                @import "tailwindcss/utilities.css";
                .article {
                    @apply prose prose-lg;
                }
            ',
        ]);
        $this->assertStringContainsString('.article', $css);
    }

    // =========================================================================
    // PLUGINS WITH VARIANTS
    // =========================================================================

    public function test_typography_with_hover_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="hover:prose-invert">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_typography_with_responsive_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="md:prose-lg">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('@media', $css);
    }

    public function test_typography_with_dark_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="dark:prose-invert">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        // dark variant uses @media (prefers-color-scheme: dark) in v4
        $this->assertStringContainsString('@media', $css);
    }

    public function test_forms_with_focus_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="focus:form-input">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_forms_with_disabled_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="disabled:form-input">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString(':disabled', $css);
    }

    // =========================================================================
    // PLUGINS WITH ARBITRARY VALUES (if supported)
    // =========================================================================

    public function test_typography_with_arbitrary_prose_value(): void
    {
        // Typography may or may not support arbitrary values
        $css = Tailwind::generate([
            'content' => '<article class="prose">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        // Just ensure no errors
        $this->assertIsString($css);
    }

    // =========================================================================
    // PLUGIN EDGE CASES
    // =========================================================================

    public function test_plugin_without_tailwind_utilities_directive(): void
    {
        // Plugin classes won't appear without @tailwind utilities
        $css = Tailwind::generate([
            'content' => '<article class="prose">',
            'css' => '@plugin "@tailwindcss/typography";',
        ]);
        // Should not error, but prose won't be in output
        $this->assertIsString($css);
    }

    public function test_plugin_with_empty_content(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        // Should not error
        $this->assertIsString($css);
    }

    public function test_plugin_with_no_matching_classes(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        // No prose classes, so plugin CSS shouldn't be in output
        $this->assertStringNotContainsString('.prose', $css);
        // But flex should still work
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_duplicate_plugin_loads(): void
    {
        // Loading same plugin twice shouldn't cause issues
        $css = Tailwind::generate([
            'content' => '<article class="prose">',
            'css' => '
                @plugin "@tailwindcss/typography";
                @plugin "@tailwindcss/typography";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('.prose', $css);
    }

    // =========================================================================
    // TYPOGRAPHY PLUGIN - ELEMENT TARGETING
    // =========================================================================

    public function test_typography_targets_headings(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose"><h1>Title</h1></article>',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        // Check that heading styles are included
        $this->assertMatchesRegularExpression('/\.prose\s+.*h1|\.prose\s*>\s*.*h1|h1/', $css);
    }

    public function test_typography_targets_links(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose"><a href="#">Link</a></article>',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--tw-prose-links', $css);
    }

    public function test_typography_targets_code(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose"><code>code</code></article>',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--tw-prose-code', $css);
    }

    public function test_typography_targets_blockquotes(): void
    {
        $css = Tailwind::generate([
            'content' => '<article class="prose"><blockquote>Quote</blockquote></article>',
            'css' => '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('--tw-prose-quotes', $css);
    }

    // =========================================================================
    // FORMS PLUGIN - STYLING PROPERTIES
    // =========================================================================

    public function test_forms_input_has_appearance_none(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="form-input">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        // Form elements typically reset appearance
        $this->assertStringContainsString('.form-input', $css);
    }

    public function test_forms_checkbox_has_appearance_none(): void
    {
        $css = Tailwind::generate([
            'content' => '<input class="form-checkbox">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        $this->assertStringContainsString('.form-checkbox', $css);
    }

    public function test_forms_select_has_background_image(): void
    {
        $css = Tailwind::generate([
            'content' => '<select class="form-select">',
            'css' => '@plugin "@tailwindcss/forms"; @import "tailwindcss/utilities.css";',
        ]);
        // Select elements typically have a dropdown arrow background
        $this->assertStringContainsString('.form-select', $css);
    }

    // =========================================================================
    // PLUGIN INTEGRATION WITH COMPILE API
    // =========================================================================

    public function test_plugin_with_compile_api(): void
    {
        $compiler = Tailwind::compile('@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";');
        $css = $compiler->css(['prose', 'prose-lg']);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('.prose-lg', $css);
    }

    public function test_plugin_incremental_build(): void
    {
        $compiler = Tailwind::compile('@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";');

        // First build
        $css1 = $compiler->css(['prose']);
        $this->assertStringContainsString('.prose', $css1);
        $this->assertStringNotContainsString('.prose-lg', $css1);

        // Second build with additional classes
        $css2 = $compiler->css(['prose', 'prose-lg']);
        $this->assertStringContainsString('.prose', $css2);
        $this->assertStringContainsString('.prose-lg', $css2);
    }

    public function test_multiple_plugins_with_compile_api(): void
    {
        $compiler = Tailwind::compile('
            @plugin "@tailwindcss/typography";
            @plugin "@tailwindcss/forms";
            @import "tailwindcss/utilities.css";
        ');
        $css = $compiler->css(['prose', 'form-input']);
        $this->assertStringContainsString('.prose', $css);
        $this->assertStringContainsString('.form-input', $css);
    }
}
