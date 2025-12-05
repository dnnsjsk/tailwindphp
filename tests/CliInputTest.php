<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Cli\Console\Input;

/**
 * Tests for CLI Input Handler.
 *
 * Tests command-line argument parsing including:
 * - Long options (--name, --name=value)
 * - Short options (-v, -o value)
 * - Boolean flags (--verbose, --no-color)
 * - Commands and positional arguments
 * - Special options (help, version, verbose, quiet)
 */
class CliInputTest extends TestCase
{
    // ============================================
    // Long Option Tests
    // ============================================

    #[Test]
    public function parses_long_boolean_option(): void
    {
        $input = new Input(['script.php', '--verbose']);

        $this->assertTrue($input->hasOption('verbose'));
        $this->assertTrue($input->getOption('verbose'));
    }

    #[Test]
    public function parses_long_option_with_equals_value(): void
    {
        $input = new Input(['script.php', '--output=dist/app.css']);

        $this->assertTrue($input->hasOption('output'));
        $this->assertSame('dist/app.css', $input->getOption('output'));
    }

    #[Test]
    public function parses_no_prefix_as_false(): void
    {
        $input = new Input(['script.php', '--no-color']);

        $this->assertTrue($input->hasOption('color'));
        $this->assertFalse($input->getOption('color'));
    }

    #[Test]
    public function parses_multiple_long_options(): void
    {
        $input = new Input(['script.php', '--verbose', '--watch', '--minify']);

        $this->assertTrue($input->getOption('verbose'));
        $this->assertTrue($input->getOption('watch'));
        $this->assertTrue($input->getOption('minify'));
    }

    // ============================================
    // Short Option Tests
    // ============================================

    #[Test]
    public function parses_short_boolean_option(): void
    {
        $input = new Input(['script.php', '-v']);

        $this->assertTrue($input->hasOption('v'));
        $this->assertTrue($input->getOption('v'));
    }

    #[Test]
    public function parses_short_option_with_value(): void
    {
        $input = new Input(['script.php', '-o', 'output.css']);

        $this->assertTrue($input->hasOption('o'));
        $this->assertSame('output.css', $input->getOption('o'));
    }

    #[Test]
    public function parses_multiple_short_flags(): void
    {
        $input = new Input(['script.php', '-vvv']);

        $this->assertTrue($input->hasOption('v'));
    }

    #[Test]
    public function parses_multiple_separate_short_options(): void
    {
        $input = new Input(['script.php', '-v', '-m']);

        $this->assertTrue($input->hasOption('v'));
        $this->assertTrue($input->hasOption('m'));
    }

    // ============================================
    // Command and Argument Tests
    // ============================================

    #[Test]
    public function parses_command(): void
    {
        $input = new Input(['script.php', 'build']);

        $this->assertSame('build', $input->getCommand());
    }

    #[Test]
    public function parses_command_with_options(): void
    {
        $input = new Input(['script.php', '--verbose', 'build', '--minify']);

        $this->assertSame('build', $input->getCommand());
        $this->assertTrue($input->getOption('verbose'));
        $this->assertTrue($input->getOption('minify'));
    }

    #[Test]
    public function parses_positional_arguments(): void
    {
        $input = new Input(['script.php', 'build', 'input.css', 'output.css']);

        $this->assertSame('build', $input->getCommand());
        $this->assertSame(['input.css', 'output.css'], $input->getArguments());
    }

    #[Test]
    public function gets_specific_argument_by_index(): void
    {
        $input = new Input(['script.php', 'build', 'first', 'second', 'third']);

        $this->assertSame('first', $input->getArgument(0));
        $this->assertSame('second', $input->getArgument(1));
        $this->assertSame('third', $input->getArgument(2));
    }

    #[Test]
    public function returns_default_for_missing_argument(): void
    {
        $input = new Input(['script.php', 'build']);

        $this->assertNull($input->getArgument(0));
        $this->assertSame('default', $input->getArgument(0, 'default'));
    }

    // ============================================
    // Option Value Access Tests
    // ============================================

    #[Test]
    public function gets_string_option_value(): void
    {
        $input = new Input(['script.php', '--output=styles.css']);

        $this->assertSame('styles.css', $input->getStringOption('output'));
    }

    #[Test]
    public function returns_default_for_missing_string_option(): void
    {
        $input = new Input(['script.php']);

        $this->assertSame('', $input->getStringOption('output'));
        $this->assertSame('default.css', $input->getStringOption('output', 'default.css'));
    }

    #[Test]
    public function returns_default_when_bool_option_used_for_string(): void
    {
        $input = new Input(['script.php', '--output']);

        // When --output is a flag (no value), getStringOption returns default
        $this->assertSame('fallback', $input->getStringOption('output', 'fallback'));
    }

    #[Test]
    public function gets_bool_option_value(): void
    {
        $input = new Input(['script.php', '--minify']);

        $this->assertTrue($input->getBoolOption('minify'));
    }

    #[Test]
    public function returns_default_for_missing_bool_option(): void
    {
        $input = new Input(['script.php']);

        $this->assertFalse($input->getBoolOption('minify'));
        $this->assertTrue($input->getBoolOption('minify', true));
    }

    #[Test]
    public function interprets_no_prefix_as_false_bool(): void
    {
        $input = new Input(['script.php', '--no-minify']);

        $this->assertFalse($input->getBoolOption('minify'));
    }

    #[Test]
    public function gets_all_options(): void
    {
        $input = new Input(['script.php', '--verbose', '--output=file.css', '-m']);

        $options = $input->getOptions();

        $this->assertArrayHasKey('verbose', $options);
        $this->assertArrayHasKey('output', $options);
        $this->assertArrayHasKey('m', $options);
    }

    // ============================================
    // Special Option Tests
    // ============================================

    #[Test]
    public function detects_help_long_option(): void
    {
        $input = new Input(['script.php', '--help']);

        $this->assertTrue($input->wantsHelp());
    }

    #[Test]
    public function detects_help_short_option(): void
    {
        $input = new Input(['script.php', '-h']);

        $this->assertTrue($input->wantsHelp());
    }

    #[Test]
    public function detects_version_long_option(): void
    {
        $input = new Input(['script.php', '--version']);

        $this->assertTrue($input->wantsVersion());
    }

    #[Test]
    public function detects_version_short_option(): void
    {
        $input = new Input(['script.php', '-V']);

        $this->assertTrue($input->wantsVersion());
    }

    #[Test]
    public function detects_verbose_long_option(): void
    {
        $input = new Input(['script.php', '--verbose']);

        $this->assertTrue($input->isVerbose());
    }

    #[Test]
    public function detects_verbose_short_option(): void
    {
        $input = new Input(['script.php', '-v']);

        $this->assertTrue($input->isVerbose());
    }

    #[Test]
    public function detects_quiet_long_option(): void
    {
        $input = new Input(['script.php', '--quiet']);

        $this->assertTrue($input->isQuiet());
    }

    #[Test]
    public function detects_quiet_short_option(): void
    {
        $input = new Input(['script.php', '-q']);

        $this->assertTrue($input->isQuiet());
    }

    // ============================================
    // Raw Args Tests
    // ============================================

    #[Test]
    public function returns_raw_args(): void
    {
        $argv = ['script.php', '--verbose', 'build'];
        $input = new Input($argv);

        $this->assertSame($argv, $input->getRawArgs());
    }

    // ============================================
    // Edge Cases
    // ============================================

    #[Test]
    public function handles_empty_args(): void
    {
        $input = new Input(['script.php']);

        $this->assertSame('', $input->getCommand());
        $this->assertSame([], $input->getArguments());
        $this->assertSame([], $input->getOptions());
    }

    #[Test]
    public function handles_dash_alone_as_argument(): void
    {
        // A single dash is sometimes used for stdin
        $input = new Input(['script.php', 'build', '-']);

        $this->assertSame('build', $input->getCommand());
        $this->assertSame(['-'], $input->getArguments());
    }

    #[Test]
    public function parses_complex_command_line(): void
    {
        // Note: Short options like -v consume the next arg as value if it doesn't start with -
        // So we use long --verbose to avoid this, or put command first
        $input = new Input([
            'script.php',
            'build',
            '--input=src/styles.css',
            '-o', 'dist/output.css',
            '--minify',
            '--watch',
            '--verbose',
            'extra-arg',
        ]);

        $this->assertSame('build', $input->getCommand());
        $this->assertSame('src/styles.css', $input->getOption('input'));
        $this->assertSame('dist/output.css', $input->getOption('o'));
        $this->assertTrue($input->getOption('minify'));
        $this->assertTrue($input->getOption('watch'));
        $this->assertTrue($input->getOption('verbose'));
        $this->assertSame(['extra-arg'], $input->getArguments());
    }

    #[Test]
    public function option_with_equals_preserves_value_with_equals(): void
    {
        $input = new Input(['script.php', '--config=path/to/file=name.json']);

        $this->assertSame('path/to/file=name.json', $input->getOption('config'));
    }

    #[Test]
    public function bool_option_interprets_string_true_values(): void
    {
        // When a bool option has a string value, getBoolOption interprets it
        $input1 = new Input(['script.php', '--debug=true']);
        $input2 = new Input(['script.php', '--debug=1']);
        $input3 = new Input(['script.php', '--debug=yes']);
        $input4 = new Input(['script.php', '--debug=on']);

        $this->assertTrue($input1->getBoolOption('debug'));
        $this->assertTrue($input2->getBoolOption('debug'));
        $this->assertTrue($input3->getBoolOption('debug'));
        $this->assertTrue($input4->getBoolOption('debug'));
    }

    #[Test]
    public function bool_option_interprets_string_false_values(): void
    {
        $input = new Input(['script.php', '--debug=false']);

        // 'false' is not in the truthy list, so it's false
        $this->assertFalse($input->getBoolOption('debug'));
    }
}
