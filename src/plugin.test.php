<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for plugin.php
 *
 * Tests the plugin system: PluginInterface, PluginAPI, PluginManager.
 */
class plugin extends TestCase
{
    private function compileCss(string $css, array $classes): string
    {
        $compiled = compile($css, ['loadDefaultTheme' => false]);

        return $compiled['build']($classes);
    }

    #[Test]
    public function it_loads_typography_plugin(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/typography";
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['prose']);

        $this->assertStringContainsString('.prose', $result);
        $this->assertStringContainsString('--tw-prose-body', $result);
    }

    #[Test]
    public function it_loads_forms_plugin(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/forms";
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['form-input']);

        $this->assertStringContainsString('.form-input', $result);
    }

    #[Test]
    public function it_throws_on_unknown_plugin(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('not registered');

        $css = <<<'CSS'
        @plugin "@tailwindcss/unknown";
        @import "tailwindcss/utilities.css";
        CSS;

        $this->compileCss($css, []);
    }

    #[Test]
    public function it_rejects_nested_plugin_directive(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('cannot be nested');

        $css = <<<'CSS'
        @media screen {
            @plugin "@tailwindcss/typography";
        }
        @import "tailwindcss/utilities.css";
        CSS;

        $this->compileCss($css, []);
    }

    #[Test]
    public function typography_plugin_supports_custom_class_name(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/typography" {
            className: "markdown";
        }
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['markdown']);

        $this->assertStringContainsString('.markdown', $result);
    }

    #[Test]
    public function typography_plugin_generates_prose_modifiers(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/typography";
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['prose', 'prose-lg', 'prose-invert']);

        $this->assertStringContainsString('.prose', $result);
        $this->assertStringContainsString('.prose-lg', $result);
        $this->assertStringContainsString('.prose-invert', $result);
    }

    #[Test]
    public function forms_plugin_generates_form_classes(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/forms";
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['form-input', 'form-checkbox', 'form-select']);

        $this->assertStringContainsString('.form-input', $result);
        $this->assertStringContainsString('.form-checkbox', $result);
        $this->assertStringContainsString('.form-select', $result);
    }

    #[Test]
    public function forms_plugin_supports_strategy_option(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/forms" {
            strategy: "class";
        }
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['form-input']);

        $this->assertStringContainsString('.form-input', $result);
    }

    #[Test]
    public function multiple_plugins_can_be_loaded(): void
    {
        $css = <<<'CSS'
        @plugin "@tailwindcss/typography";
        @plugin "@tailwindcss/forms";
        @import "tailwindcss/utilities.css";
        CSS;

        $result = $this->compileCss($css, ['prose', 'form-input']);

        $this->assertStringContainsString('.prose', $result);
        $this->assertStringContainsString('.form-input', $result);
    }
}
