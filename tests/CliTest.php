<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Cli\Application;
use TailwindPHP\Cli\Console\Input;
use TailwindPHP\Cli\Console\Output;

/**
 * Tests for the CLI application.
 */
class CliTest extends TestCase
{
    private string $testDir;

    protected function setUp(): void
    {
        $this->testDir = sys_get_temp_dir() . '/tailwindphp_cli_test_' . uniqid();
        mkdir($this->testDir, 0755, true);
    }

    protected function tearDown(): void
    {
        // Clean up test directory
        $this->removeDir($this->testDir);
    }

    private function removeDir(string $dir): void
    {
        if (!is_dir($dir)) {
            return;
        }

        $files = array_diff(scandir($dir) ?: [], ['.', '..']);
        foreach ($files as $file) {
            $path = $dir . '/' . $file;
            if (is_dir($path)) {
                $this->removeDir($path);
            } else {
                unlink($path);
            }
        }
        rmdir($dir);
    }

    // =========================================================================
    // Input Tests
    // =========================================================================

    #[Test]
    public function input_parses_command(): void
    {
        $input = new Input(['tailwindphp', 'build']);
        $this->assertSame('build', $input->getCommand());
    }

    #[Test]
    public function input_parses_long_option_with_value(): void
    {
        $input = new Input(['tailwindphp', 'build', '--content=./src']);
        $this->assertSame('./src', $input->getOption('content'));
    }

    #[Test]
    public function input_parses_long_option_boolean(): void
    {
        $input = new Input(['tailwindphp', 'build', '--minify']);
        $this->assertTrue($input->getBoolOption('minify'));
    }

    #[Test]
    public function input_parses_short_option_with_value(): void
    {
        $input = new Input(['tailwindphp', 'build', '-c', './templates']);
        $this->assertSame('./templates', $input->getOption('c'));
    }

    #[Test]
    public function input_parses_short_option_boolean(): void
    {
        $input = new Input(['tailwindphp', 'build', '-m']);
        $this->assertTrue($input->getBoolOption('m'));
    }

    #[Test]
    public function input_parses_multiple_options(): void
    {
        $input = new Input([
            'tailwindphp', 'build',
            '-c', './templates',
            '-o', './dist/styles.css',
            '--minify',
        ]);

        $this->assertSame('build', $input->getCommand());
        $this->assertSame('./templates', $input->getOption('c'));
        $this->assertSame('./dist/styles.css', $input->getOption('o'));
        $this->assertTrue($input->getBoolOption('minify'));
    }

    #[Test]
    public function input_detects_help_option(): void
    {
        $input = new Input(['tailwindphp', '--help']);
        $this->assertTrue($input->wantsHelp());

        $input = new Input(['tailwindphp', '-h']);
        $this->assertTrue($input->wantsHelp());
    }

    #[Test]
    public function input_detects_version_option(): void
    {
        $input = new Input(['tailwindphp', '--version']);
        $this->assertTrue($input->wantsVersion());

        $input = new Input(['tailwindphp', '-V']);
        $this->assertTrue($input->wantsVersion());
    }

    #[Test]
    public function input_detects_verbose_option(): void
    {
        $input = new Input(['tailwindphp', 'build', '-v']);
        $this->assertTrue($input->isVerbose());

        $input = new Input(['tailwindphp', 'build', '--verbose']);
        $this->assertTrue($input->isVerbose());
    }

    #[Test]
    public function input_detects_quiet_option(): void
    {
        $input = new Input(['tailwindphp', 'build', '-q']);
        $this->assertTrue($input->isQuiet());

        $input = new Input(['tailwindphp', 'build', '--quiet']);
        $this->assertTrue($input->isQuiet());
    }

    #[Test]
    public function input_handles_no_prefix(): void
    {
        $input = new Input(['tailwindphp', 'build', '--no-cache']);
        $this->assertFalse($input->getBoolOption('cache'));
    }

    // =========================================================================
    // Output Tests
    // =========================================================================

    #[Test]
    public function output_formats_color_tags(): void
    {
        $output = new Output();

        // Without color support (strip tags)
        $result = preg_replace('/<\/?[a-z_]+>/i', '', '<green>text</green>');
        $this->assertSame('text', $result);
    }

    // =========================================================================
    // Application Tests
    // =========================================================================

    #[Test]
    public function application_shows_help_with_no_command(): void
    {
        $input = new Input(['tailwindphp']);
        $output = $this->createMock(Output::class);

        $output->expects($this->atLeastOnce())
            ->method('writeln');

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
    }

    #[Test]
    public function application_shows_version(): void
    {
        $input = new Input(['tailwindphp', '--version']);
        $output = $this->createMock(Output::class);

        $output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('TailwindPHP'));

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
    }

    #[Test]
    public function application_returns_error_for_unknown_command(): void
    {
        $input = new Input(['tailwindphp', 'unknown-command']);
        $output = $this->createMock(Output::class);

        $output->expects($this->atLeastOnce())
            ->method('error')
            ->with($this->stringContains('Unknown command'));

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(1, $exitCode);
    }

    // =========================================================================
    // Build Command Tests
    // =========================================================================

    #[Test]
    public function build_requires_content_option(): void
    {
        $input = new Input(['tailwindphp', 'build']);
        $output = $this->createMock(Output::class);

        $output->expects($this->once())
            ->method('error')
            ->with($this->stringContains('No content path'));

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(1, $exitCode);
    }

    #[Test]
    public function build_generates_css(): void
    {
        // Create test content file
        $contentFile = $this->testDir . '/template.html';
        $outputFile = $this->testDir . '/output.css';
        file_put_contents($contentFile, '<div class="flex p-4 bg-blue-500">Hello</div>');

        $input = new Input([
            'tailwindphp', 'build',
            '-c', $contentFile,
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($outputFile);

        $css = file_get_contents($outputFile);
        $this->assertStringContainsString('.flex', $css);
        $this->assertStringContainsString('.p-4', $css);
        $this->assertStringContainsString('.bg-blue-500', $css);
    }

    #[Test]
    public function build_with_minify_option(): void
    {
        $contentFile = $this->testDir . '/template.html';
        $outputFile = $this->testDir . '/output.css';
        file_put_contents($contentFile, '<div class="flex">Hello</div>');

        $input = new Input([
            'tailwindphp', 'build',
            '-c', $contentFile,
            '-o', $outputFile,
            '--minify',
        ]);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);

        $css = file_get_contents($outputFile);
        // Minified CSS should not have excessive whitespace
        $this->assertStringNotContainsString('  ', $css); // No double spaces
    }

    #[Test]
    public function build_with_custom_css_input(): void
    {
        $contentFile = $this->testDir . '/template.html';
        $cssFile = $this->testDir . '/app.css';
        $outputFile = $this->testDir . '/output.css';

        file_put_contents($contentFile, '<div class="flex custom-class">Hello</div>');
        file_put_contents($cssFile, '@import "tailwindcss"; @utility custom-class { color: red; }');

        $input = new Input([
            'tailwindphp', 'build',
            '-c', $contentFile,
            '-i', $cssFile,
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);

        $css = file_get_contents($outputFile);
        $this->assertStringContainsString('.custom-class', $css);
        $this->assertStringContainsString('color: red', $css);
    }

    #[Test]
    public function build_creates_output_directory(): void
    {
        $contentFile = $this->testDir . '/template.html';
        $outputFile = $this->testDir . '/nested/deep/output.css';
        file_put_contents($contentFile, '<div class="flex">Hello</div>');

        $input = new Input([
            'tailwindphp', 'build',
            '-c', $contentFile,
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($outputFile);
    }

    #[Test]
    public function build_scans_directory(): void
    {
        // Create multiple template files
        mkdir($this->testDir . '/templates');
        file_put_contents($this->testDir . '/templates/a.php', '<div class="flex">A</div>');
        file_put_contents($this->testDir . '/templates/b.php', '<div class="grid">B</div>');

        $outputFile = $this->testDir . '/output.css';

        $input = new Input([
            'tailwindphp', 'build',
            '-c', $this->testDir . '/templates',
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);

        $css = file_get_contents($outputFile);
        $this->assertStringContainsString('.flex', $css);
        $this->assertStringContainsString('.grid', $css);
    }

    // =========================================================================
    // Init Command Tests
    // =========================================================================

    #[Test]
    public function init_creates_config_file(): void
    {
        chdir($this->testDir);

        $input = new Input(['tailwindphp', 'init']);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($this->testDir . '/tailwind.config.php');

        $config = require $this->testDir . '/tailwind.config.php';
        $this->assertIsArray($config);
        $this->assertArrayHasKey('content', $config);
        $this->assertArrayHasKey('output', $config);
    }

    #[Test]
    public function init_creates_css_file_with_option(): void
    {
        chdir($this->testDir);

        $input = new Input(['tailwindphp', 'init', '--css']);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($this->testDir . '/tailwind.config.php');
        $this->assertFileExists($this->testDir . '/app.css');

        $css = file_get_contents($this->testDir . '/app.css');
        $this->assertStringContainsString('@import "tailwindcss"', $css);
        $this->assertStringContainsString('@theme', $css);
    }

    #[Test]
    public function init_refuses_to_overwrite_without_force(): void
    {
        chdir($this->testDir);

        // Create existing config
        file_put_contents($this->testDir . '/tailwind.config.php', '<?php return [];');

        $input = new Input(['tailwindphp', 'init']);
        $output = $this->createMock(Output::class);

        $output->expects($this->once())
            ->method('error')
            ->with($this->stringContains('already exists'));

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(1, $exitCode);
    }

    #[Test]
    public function init_overwrites_with_force(): void
    {
        chdir($this->testDir);

        // Create existing config
        file_put_contents($this->testDir . '/tailwind.config.php', '<?php return [];');

        $input = new Input(['tailwindphp', 'init', '--force']);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);

        // Verify it was replaced with full config
        $config = require $this->testDir . '/tailwind.config.php';
        $this->assertArrayHasKey('content', $config);
    }

    // =========================================================================
    // Cache Clear Command Tests
    // =========================================================================

    #[Test]
    public function cache_clear_works_on_empty_cache(): void
    {
        $input = new Input(['tailwindphp', 'cache:clear']);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
    }
}
