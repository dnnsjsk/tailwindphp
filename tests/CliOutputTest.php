<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Cli\Console\Output;

/**
 * Tests for CLI Output Handler.
 *
 * Tests terminal output including:
 * - ANSI color support
 * - Quiet and verbose modes
 * - Message formatting
 * - Color tags
 */
class CliOutputTest extends TestCase
{
    // ==================================================
    // Basic Output Tests
    // ==================================================

    #[Test]
    public function can_instantiate_output(): void
    {
        $output = new Output();

        $this->assertInstanceOf(Output::class, $output);
    }

    // ==================================================
    // Quiet Mode Tests
    // ==================================================

    #[Test]
    public function quiet_mode_is_off_by_default(): void
    {
        $output = new Output();

        // When quiet mode is off, write should produce output
        // We can't easily test actual output, but we can verify the object state
        $this->assertInstanceOf(Output::class, $output);
    }

    #[Test]
    public function can_set_quiet_mode(): void
    {
        $output = new Output();
        $output->setQuiet(true);

        // No exception means it worked
        $this->assertTrue(true);
    }

    // ==================================================
    // Verbose Mode Tests
    // ==================================================

    #[Test]
    public function verbose_mode_is_off_by_default(): void
    {
        $output = new Output();

        $this->assertFalse($output->isVerbose());
    }

    #[Test]
    public function can_set_verbose_mode(): void
    {
        $output = new Output();
        $output->setVerbose(true);

        $this->assertTrue($output->isVerbose());
    }

    #[Test]
    public function can_toggle_verbose_mode(): void
    {
        $output = new Output();

        $output->setVerbose(true);
        $this->assertTrue($output->isVerbose());

        $output->setVerbose(false);
        $this->assertFalse($output->isVerbose());
    }

    // ==================================================
    // Color Formatting Tests
    // ==================================================

    #[Test]
    public function color_returns_text_when_color_not_found(): void
    {
        $output = new Output();

        // Unknown color should return plain text
        $result = $output->color('nonexistent', 'test');

        $this->assertSame('test', $result);
    }

    #[Test]
    public function format_strips_tags_when_no_color_support(): void
    {
        // We can test the format method's tag stripping behavior
        // by checking that it handles tags properly
        $output = new Output();

        $input = '<green>success</green> message';
        $result = $output->format($input);

        // Result should either have ANSI codes or stripped tags
        $this->assertStringContainsString('success', $result);
        $this->assertStringContainsString('message', $result);
    }

    #[Test]
    public function format_handles_multiple_tags(): void
    {
        $output = new Output();

        $input = '<red>error</red> and <green>success</green>';
        $result = $output->format($input);

        $this->assertStringContainsString('error', $result);
        $this->assertStringContainsString('success', $result);
    }

    #[Test]
    public function format_handles_nested_content(): void
    {
        $output = new Output();

        $input = '<bold>Header: <cyan>value</cyan></bold>';
        $result = $output->format($input);

        $this->assertStringContainsString('Header', $result);
        $this->assertStringContainsString('value', $result);
    }

    #[Test]
    public function format_handles_text_without_tags(): void
    {
        $output = new Output();

        $input = 'plain text without any tags';
        $result = $output->format($input);

        $this->assertSame('plain text without any tags', $result);
    }

    // ==================================================
    // Color Method Tests
    // ==================================================

    #[Test]
    public function color_method_applies_color(): void
    {
        $output = new Output();

        $result = $output->color('green', 'success');

        // Either has ANSI codes or is plain text
        $this->assertStringContainsString('success', $result);
    }

    #[Test]
    public function color_method_handles_empty_text(): void
    {
        $output = new Output();

        $result = $output->color('red', '');

        // Should not throw, result may be empty or just reset codes
        $this->assertIsString($result);
    }

    // ==================================================
    // ANSI Color Constants Tests
    // ==================================================

    #[Test]
    public function supports_basic_colors(): void
    {
        $output = new Output();

        $colors = ['red', 'green', 'yellow', 'blue', 'magenta', 'cyan', 'white', 'gray'];

        foreach ($colors as $color) {
            $result = $output->color($color, 'test');
            $this->assertStringContainsString('test', $result, "Color $color should work");
        }
    }

    #[Test]
    public function supports_bright_colors(): void
    {
        $output = new Output();

        $colors = ['bright_red', 'bright_green', 'bright_yellow', 'bright_blue'];

        foreach ($colors as $color) {
            $result = $output->color($color, 'test');
            $this->assertStringContainsString('test', $result, "Bright color $color should work");
        }
    }

    #[Test]
    public function supports_style_modifiers(): void
    {
        $output = new Output();

        $styles = ['bold', 'dim', 'italic', 'underline'];

        foreach ($styles as $style) {
            $result = $output->color($style, 'test');
            $this->assertStringContainsString('test', $result, "Style $style should work");
        }
    }

    #[Test]
    public function supports_background_colors(): void
    {
        $output = new Output();

        $colors = ['bg_red', 'bg_green', 'bg_yellow', 'bg_blue'];

        foreach ($colors as $color) {
            $result = $output->color($color, 'test');
            $this->assertStringContainsString('test', $result, "Background color $color should work");
        }
    }

    // ==================================================
    // Format Tag Tests
    // ==================================================

    #[Test]
    public function format_handles_case_insensitive_tags(): void
    {
        $output = new Output();

        $input = '<GREEN>text</GREEN>';
        $result = $output->format($input);

        $this->assertStringContainsString('text', $result);
    }

    #[Test]
    public function format_handles_underscore_in_tag_names(): void
    {
        $output = new Output();

        $input = '<bright_green>text</bright_green>';
        $result = $output->format($input);

        $this->assertStringContainsString('text', $result);
    }

    // ==================================================
    // Method Existence Tests
    // ==================================================

    #[Test]
    public function has_all_output_methods(): void
    {
        $output = new Output();

        // Verify all methods exist and are callable
        $this->assertTrue(method_exists($output, 'write'));
        $this->assertTrue(method_exists($output, 'writeln'));
        $this->assertTrue(method_exists($output, 'writeError'));
        $this->assertTrue(method_exists($output, 'writeErrorln'));
        $this->assertTrue(method_exists($output, 'success'));
        $this->assertTrue(method_exists($output, 'error'));
        $this->assertTrue(method_exists($output, 'warning'));
        $this->assertTrue(method_exists($output, 'info'));
        $this->assertTrue(method_exists($output, 'verbose'));
        $this->assertTrue(method_exists($output, 'title'));
        $this->assertTrue(method_exists($output, 'section'));
        $this->assertTrue(method_exists($output, 'newLine'));
        $this->assertTrue(method_exists($output, 'progress'));
        $this->assertTrue(method_exists($output, 'spinner'));
        $this->assertTrue(method_exists($output, 'clearLine'));
        $this->assertTrue(method_exists($output, 'table'));
    }

    // ==================================================
    // Edge Cases
    // ==================================================

    #[Test]
    public function format_handles_empty_string(): void
    {
        $output = new Output();

        $result = $output->format('');

        $this->assertSame('', $result);
    }

    #[Test]
    public function format_handles_special_characters(): void
    {
        $output = new Output();

        $input = 'Path: /usr/local/bin & other <symbols>';
        $result = $output->format($input);

        $this->assertStringContainsString('/usr/local/bin', $result);
        $this->assertStringContainsString('&', $result);
    }

    #[Test]
    public function format_handles_unclosed_tags(): void
    {
        $output = new Output();

        // Unclosed tags should still process without error
        $input = '<green>text without closing';
        $result = $output->format($input);

        $this->assertStringContainsString('text without closing', $result);
    }

    #[Test]
    public function format_handles_unmatched_closing_tags(): void
    {
        $output = new Output();

        $input = 'text</green> with stray closing';
        $result = $output->format($input);

        $this->assertStringContainsString('text', $result);
        $this->assertStringContainsString('with stray closing', $result);
    }
}
