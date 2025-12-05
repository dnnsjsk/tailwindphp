<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Tests for the @source directive.
 *
 * The @source directive tells Tailwind where to scan for class names:
 * - Basic file/directory patterns
 * - Negated patterns with 'not' prefix
 * - Inline candidates with inline()
 * - Ignored candidates with not inline()
 */
class SourceDirectiveTest extends TestCase
{
    // ==================================================
    // Basic @source (file patterns)
    // ==================================================

    #[Test]
    public function parses_basic_source_directive(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @source "./templates";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    #[Test]
    public function parses_source_with_glob_pattern(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="p-4">',
            'css' => '
                @source "./src/**/*.html";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('padding:', $css);
    }

    #[Test]
    public function parses_multiple_source_directives(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex m-2">',
            'css' => '
                @source "./templates";
                @source "./components/**/*.php";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('margin:', $css);
    }

    // ==================================================
    // @source not (negated patterns)
    // ==================================================

    #[Test]
    public function parses_negated_source_directive(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @source "./src";
                @source not "./src/legacy";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    #[Test]
    public function negated_source_returns_in_sources_array(): void
    {
        $result = \TailwindPHP\compile('
            @source "./src";
            @source not "./ignored";
            @import "tailwindcss/utilities.css";
        ');

        $sources = $result['sources'];
        $this->assertCount(2, $sources);
        $this->assertFalse($sources[0]['negated']);
        $this->assertTrue($sources[1]['negated']);
        $this->assertSame('./src', $sources[0]['pattern']);
        $this->assertSame('./ignored', $sources[1]['pattern']);
    }

    // ==================================================
    // @source inline() (inline candidates)
    // ==================================================

    #[Test]
    public function inline_source_adds_candidates_directly(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '
                @source inline("flex p-4 m-2");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
        $this->assertStringContainsString('margin:', $css);
    }

    #[Test]
    public function inline_source_with_single_class(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '
                @source inline("hidden");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: none', $css);
    }

    #[Test]
    public function inline_source_with_brace_expansion(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '
                @source inline("p-{1,2,4}");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('padding: calc(var(--spacing) * 1)', $css);
        $this->assertStringContainsString('padding: calc(var(--spacing) * 2)', $css);
        $this->assertStringContainsString('padding: calc(var(--spacing) * 4)', $css);
    }

    #[Test]
    public function inline_source_with_complex_brace_expansion(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '
                @source inline("text-{red,blue}-500");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('color: var(--color-red-500)', $css);
        $this->assertStringContainsString('color: var(--color-blue-500)', $css);
    }

    #[Test]
    public function inline_source_with_variants(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '
                @source inline("hover:bg-blue-500 focus:ring-2");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('background-color:', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    // ==================================================
    // @source not inline() (ignored candidates)
    // ==================================================

    #[Test]
    public function not_inline_source_excludes_candidates(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex hidden block">',
            'css' => '
                @source not inline("hidden");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('display: block', $css);
        // hidden should be ignored
        $this->assertStringNotContainsString('display: none', $css);
    }

    #[Test]
    public function not_inline_source_with_brace_expansion(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="p-1 p-2 p-4 p-8">',
            'css' => '
                @source not inline("p-{1,2}");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        // p-1 and p-2 should be ignored
        $this->assertStringNotContainsString('padding: calc(var(--spacing) * 1)', $css);
        $this->assertStringNotContainsString('padding: calc(var(--spacing) * 2)', $css);
        // p-4 and p-8 should be included
        $this->assertStringContainsString('padding: calc(var(--spacing) * 4)', $css);
        $this->assertStringContainsString('padding: calc(var(--spacing) * 8)', $css);
    }

    #[Test]
    public function not_inline_source_excludes_multiple_candidates(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex hidden block invisible">',
            'css' => '
                @source not inline("hidden invisible");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('display: block', $css);
        $this->assertStringNotContainsString('display: none', $css);
        $this->assertStringNotContainsString('visibility: hidden', $css);
    }

    // ==================================================
    // Validation errors
    // ==================================================

    #[Test]
    public function throws_when_source_has_body(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('`@source` cannot have a body.');

        Tailwind::generate([
            'content' => '',
            'css' => '
                @source "./src" {
                    .some-style { color: red; }
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
    }

    #[Test]
    public function throws_when_source_is_nested(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('`@source` cannot be nested.');

        Tailwind::generate([
            'content' => '',
            'css' => '
                @media screen {
                    @source "./src";
                }
                @import "tailwindcss/utilities.css";
            ',
        ]);
    }

    #[Test]
    public function throws_when_source_path_is_unquoted(): void
    {
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('`@source` paths must be quoted.');

        Tailwind::generate([
            'content' => '',
            'css' => '
                @source ./src;
                @import "tailwindcss/utilities.css";
            ',
        ]);
    }

    #[Test]
    public function throws_when_source_path_has_mismatched_quotes(): void
    {
        // Note: Mismatched quotes are caught by CSS parser first
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Unterminated string');

        Tailwind::generate([
            'content' => '',
            'css' => '
                @source "./src;
                @import "tailwindcss/utilities.css";
            ',
        ]);
    }

    // ==================================================
    // Quote styles
    // ==================================================

    #[Test]
    public function accepts_double_quoted_paths(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @source "./templates";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    #[Test]
    public function accepts_single_quoted_paths(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => "
                @source './templates';
                @import 'tailwindcss/utilities.css';
            ",
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    // ==================================================
    // CLI integration (sources returned for scanning)
    // ==================================================

    #[Test]
    public function compile_returns_sources_array(): void
    {
        $result = \TailwindPHP\compile('
            @source "./src/**/*.html";
            @source "./templates";
            @import "tailwindcss/utilities.css";
        ');

        $this->assertArrayHasKey('sources', $result);
        $sources = $result['sources'];
        $this->assertCount(2, $sources);
        $this->assertSame('./src/**/*.html', $sources[0]['pattern']);
        $this->assertSame('./templates', $sources[1]['pattern']);
    }

    #[Test]
    public function sources_include_base_path(): void
    {
        $result = \TailwindPHP\compile('
            @source "./src";
            @import "tailwindcss/utilities.css";
        ', ['base' => '/var/www/project']);

        $sources = $result['sources'];
        $this->assertSame('/var/www/project', $sources[0]['base']);
    }

    // ==================================================
    // Edge cases
    // ==================================================

    #[Test]
    public function inline_with_empty_content_still_works(): void
    {
        $css = Tailwind::generate([
            'content' => '',
            'css' => '
                @source inline("flex");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
    }

    #[Test]
    public function inline_candidates_combined_with_content_candidates(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="block">',
            'css' => '
                @source inline("flex");
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('display: block', $css);
    }

    #[Test]
    public function source_directive_is_removed_from_output(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">',
            'css' => '
                @source "./templates";
                @import "tailwindcss/utilities.css";
            ',
        ]);
        $this->assertStringNotContainsString('@source', $css);
    }
}
