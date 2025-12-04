<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use TailwindPHP\Minifier\CssMinifier as Minifier;

class CssMinifier extends TestCase
{
    public function test_removes_comments(): void
    {
        $css = '/* comment */ .foo { color: red; } /* another */';
        $result = Minifier::minify($css);

        $this->assertStringNotContainsString('/*', $result);
        $this->assertStringNotContainsString('*/', $result);
        $this->assertStringContainsString('.foo', $result);
    }

    public function test_removes_multiline_comments(): void
    {
        $css = "/*\n * Multi-line\n * comment\n */ .foo { color: red; }";
        $result = Minifier::minify($css);

        $this->assertStringNotContainsString('Multi-line', $result);
        $this->assertStringContainsString('.foo', $result);
    }

    public function test_removes_whitespace(): void
    {
        $css = ".foo {\n    color: red;\n    padding: 10px;\n}";
        $result = Minifier::minify($css);

        $this->assertEquals('.foo{color:red;padding:10px}', $result);
    }

    public function test_removes_space_around_combinators(): void
    {
        $css = '.foo > .bar { color: red; }';
        $result = Minifier::minify($css);

        $this->assertEquals('.foo>.bar{color:red}', $result);
    }

    public function test_shortens_hex_colors(): void
    {
        $css = '.foo { color: #ffffff; background: #aabbcc; }';
        $result = Minifier::minify($css);

        $this->assertStringContainsString('#fff', $result);
        $this->assertStringContainsString('#abc', $result);
        $this->assertStringNotContainsString('#ffffff', $result);
        $this->assertStringNotContainsString('#aabbcc', $result);
    }

    public function test_preserves_non_shortenable_hex_colors(): void
    {
        $css = '.foo { color: #123456; }';
        $result = Minifier::minify($css);

        $this->assertStringContainsString('#123456', $result);
    }

    public function test_removes_zero_units(): void
    {
        $css = '.foo { margin: 0px; padding: 0rem; border: 0em; }';
        $result = Minifier::minify($css);

        $this->assertStringContainsString('margin:0', $result);
        $this->assertStringContainsString('padding:0', $result);
        $this->assertStringContainsString('border:0', $result);
        $this->assertStringNotContainsString('0px', $result);
        $this->assertStringNotContainsString('0rem', $result);
    }

    public function test_preserves_time_units_on_zero(): void
    {
        // Time units need to be preserved for animations
        $css = '.foo { transition-duration: 0s; animation-delay: 0ms; }';
        $result = Minifier::minify($css);

        $this->assertStringContainsString('0s', $result);
        $this->assertStringContainsString('0ms', $result);
    }

    public function test_shortens_font_weight(): void
    {
        $css = '.foo { font-weight: normal; } .bar { font-weight: bold; }';
        $result = Minifier::minify($css);

        $this->assertStringContainsString('font-weight:400', $result);
        $this->assertStringContainsString('font-weight:700', $result);
        $this->assertStringNotContainsString('normal', $result);
        $this->assertStringNotContainsString('bold', $result);
    }

    public function test_removes_empty_rules(): void
    {
        $css = '.foo {} .bar { color: red; } .baz {}';
        $result = Minifier::minify($css);

        $this->assertStringNotContainsString('.foo', $result);
        $this->assertStringNotContainsString('.baz', $result);
        $this->assertStringContainsString('.bar', $result);
    }

    public function test_removes_trailing_semicolons(): void
    {
        $css = '.foo { color: red; }';
        $result = Minifier::minify($css);

        $this->assertStringNotContainsString(';}', $result);
        $this->assertEquals('.foo{color:red}', $result);
    }

    public function test_handles_media_queries(): void
    {
        $css = "@media (min-width: 768px) {\n    .foo {\n        color: red;\n    }\n}";
        $result = Minifier::minify($css);

        // Space after @media is preserved (valid CSS)
        $this->assertEquals('@media (min-width:768px){.foo{color:red}}', $result);
    }

    public function test_integration_with_generate(): void
    {
        $css = \TailwindPHP\Tailwind::generate([
            'content' => '<div class="p-4 m-2">Hello</div>',
            'css' => '@import "tailwindcss/utilities";',
            'minify' => true,
        ]);

        // Should not contain newlines or unnecessary whitespace
        $this->assertStringNotContainsString("\n", $css);

        // Should still contain the utilities
        $this->assertStringContainsString('padding:', $css);
        $this->assertStringContainsString('margin:', $css);
    }

    public function test_minify_false_preserves_formatting(): void
    {
        $css = \TailwindPHP\Tailwind::generate([
            'content' => '<div class="p-4">Hello</div>',
            'css' => '@import "tailwindcss/utilities";',
            'minify' => false,
        ]);

        // Should contain formatting (newlines)
        $this->assertStringContainsString("\n", $css);
    }

    // =========================================================================
    // Public API Tests (Tailwind::minify)
    // =========================================================================

    public function test_public_api_minify(): void
    {
        $css = ".foo {\n    color: red;\n    padding: 0px;\n}";
        $result = \TailwindPHP\Tailwind::minify($css);

        $this->assertEquals('.foo{color:red;padding:0}', $result);
    }

    public function test_public_api_minify_with_generate_output(): void
    {
        // Generate unminified CSS
        $css = \TailwindPHP\Tailwind::generate([
            'content' => '<div class="p-4 text-red-500">Hello</div>',
            'css' => '@import "tailwindcss/utilities";',
        ]);

        // Then minify separately
        $minified = \TailwindPHP\Tailwind::minify($css);

        // Should be smaller
        $this->assertLessThan(strlen($css), strlen($minified));

        // Should still work (contain key styles)
        $this->assertStringContainsString('padding:', $minified);
        $this->assertStringContainsString('color:', $minified);
    }

    public function test_public_api_minify_hex_shortening(): void
    {
        $css = '.foo { color: #ffffff; background: #000000; }';
        $result = \TailwindPHP\Tailwind::minify($css);

        $this->assertStringContainsString('#fff', $result);
        $this->assertStringContainsString('#000', $result);
    }
}
