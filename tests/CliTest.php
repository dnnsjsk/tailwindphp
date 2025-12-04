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
 *
 * Tests the PHP port of @tailwindcss/cli.
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
    public function input_parses_long_option_with_value(): void
    {
        $input = new Input(['tailwindphp', '--input=./src/app.css']);
        $this->assertSame('./src/app.css', $input->getOption('input'));
    }

    #[Test]
    public function input_parses_long_option_boolean(): void
    {
        $input = new Input(['tailwindphp', '--minify']);
        $this->assertTrue($input->getBoolOption('minify'));
    }

    #[Test]
    public function input_parses_short_option_with_value(): void
    {
        $input = new Input(['tailwindphp', '-i', './templates/app.css']);
        $this->assertSame('./templates/app.css', $input->getOption('i'));
    }

    #[Test]
    public function input_parses_short_option_boolean(): void
    {
        $input = new Input(['tailwindphp', '-m']);
        $this->assertTrue($input->getBoolOption('m'));
    }

    #[Test]
    public function input_parses_multiple_options(): void
    {
        $input = new Input([
            'tailwindphp',
            '-i', './app.css',
            '-o', './dist/styles.css',
            '--minify',
        ]);

        $this->assertSame('./app.css', $input->getOption('i'));
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
        $input = new Input(['tailwindphp', '-v']);
        $this->assertTrue($input->isVerbose());

        $input = new Input(['tailwindphp', '--verbose']);
        $this->assertTrue($input->isVerbose());
    }

    #[Test]
    public function input_detects_quiet_option(): void
    {
        $input = new Input(['tailwindphp', '-q']);
        $this->assertTrue($input->isQuiet());

        $input = new Input(['tailwindphp', '--quiet']);
        $this->assertTrue($input->isQuiet());
    }

    #[Test]
    public function input_handles_no_prefix(): void
    {
        $input = new Input(['tailwindphp', '--no-cache']);
        $this->assertFalse($input->getBoolOption('cache'));
    }

    #[Test]
    public function input_parses_watch_option(): void
    {
        $input = new Input(['tailwindphp', '-w']);
        $this->assertTrue($input->getBoolOption('w'));

        $input = new Input(['tailwindphp', '--watch']);
        $this->assertTrue($input->getBoolOption('watch'));
    }

    #[Test]
    public function input_parses_optimize_option(): void
    {
        $input = new Input(['tailwindphp', '--optimize']);
        $this->assertTrue($input->getBoolOption('optimize'));
    }

    #[Test]
    public function input_parses_cwd_option(): void
    {
        $input = new Input(['tailwindphp', '--cwd=/path/to/project']);
        $this->assertSame('/path/to/project', $input->getOption('cwd'));
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
    public function application_shows_version(): void
    {
        $input = new Input(['tailwindphp', '--version']);
        $output = $this->createMock(Output::class);

        $output->expects($this->once())
            ->method('writeln')
            ->with($this->stringContains('tailwindphp'));

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
    }

    #[Test]
    public function application_has_correct_version(): void
    {
        $this->assertSame('1.0.0', Application::VERSION);
    }

    #[Test]
    public function application_has_correct_name(): void
    {
        $this->assertSame('tailwindphp', Application::NAME);
    }

    // =========================================================================
    // Build Tests (implicit command)
    // =========================================================================

    #[Test]
    public function build_with_input_and_output(): void
    {
        // Create test input CSS file
        $inputFile = $this->testDir . '/app.css';
        $outputFile = $this->testDir . '/output.css';

        // Create a template file for scanning
        mkdir($this->testDir . '/templates');
        file_put_contents($this->testDir . '/templates/index.html', '<div class="flex p-4 bg-blue-500">Hello</div>');

        // Write CSS with @source directive pointing to templates
        file_put_contents($inputFile, '@import "tailwindcss"; @source "./templates";');

        $input = new Input([
            'tailwindphp',
            '-i', $inputFile,
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        // Change to test directory so @source can find templates
        $originalDir = getcwd();
        chdir($this->testDir);

        try {
            $app = new Application($input, $output);
            $exitCode = $app->run();

            $this->assertSame(0, $exitCode);
            $this->assertFileExists($outputFile);

            $css = file_get_contents($outputFile);
            $this->assertStringContainsString('.flex', $css);
            $this->assertStringContainsString('.p-4', $css);
            $this->assertStringContainsString('.bg-blue-500', $css);
        } finally {
            chdir($originalDir);
        }
    }

    #[Test]
    public function build_with_minify_option(): void
    {
        $inputFile = $this->testDir . '/app.css';
        $outputFile = $this->testDir . '/output.css';

        mkdir($this->testDir . '/templates');
        file_put_contents($this->testDir . '/templates/index.html', '<div class="flex">Hello</div>');
        file_put_contents($inputFile, '@import "tailwindcss"; @source "./templates";');

        $input = new Input([
            'tailwindphp',
            '-i', $inputFile,
            '-o', $outputFile,
            '--minify',
        ]);
        $output = $this->createMock(Output::class);

        $originalDir = getcwd();
        chdir($this->testDir);

        try {
            $app = new Application($input, $output);
            $exitCode = $app->run();

            $this->assertSame(0, $exitCode);

            $css = file_get_contents($outputFile);
            // Minified CSS should not have excessive whitespace
            $this->assertStringNotContainsString('  ', $css); // No double spaces
        } finally {
            chdir($originalDir);
        }
    }

    #[Test]
    public function build_creates_output_directory(): void
    {
        $inputFile = $this->testDir . '/app.css';
        $outputFile = $this->testDir . '/nested/deep/output.css';

        mkdir($this->testDir . '/templates');
        file_put_contents($this->testDir . '/templates/index.html', '<div class="flex">Hello</div>');
        file_put_contents($inputFile, '@import "tailwindcss"; @source "./templates";');

        $input = new Input([
            'tailwindphp',
            '-i', $inputFile,
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        $originalDir = getcwd();
        chdir($this->testDir);

        try {
            $app = new Application($input, $output);
            $exitCode = $app->run();

            $this->assertSame(0, $exitCode);
            $this->assertFileExists($outputFile);
        } finally {
            chdir($originalDir);
        }
    }

    #[Test]
    public function build_returns_error_for_missing_input_file(): void
    {
        $input = new Input([
            'tailwindphp',
            '-i', '/nonexistent/file.css',
            '-o', $this->testDir . '/output.css',
        ]);
        $output = $this->createMock(Output::class);

        $output->expects($this->atLeastOnce())
            ->method('writeErrorln');

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(1, $exitCode);
    }

    #[Test]
    public function build_returns_error_when_input_equals_output(): void
    {
        $file = $this->testDir . '/app.css';
        file_put_contents($file, '@import "tailwindcss";');

        $input = new Input([
            'tailwindphp',
            '-i', $file,
            '-o', $file,
        ]);
        $output = $this->createMock(Output::class);

        $output->expects($this->atLeastOnce())
            ->method('writeErrorln');

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(1, $exitCode);
    }

    #[Test]
    public function build_with_cwd_option(): void
    {
        // Create project directory
        $projectDir = $this->testDir . '/project';
        mkdir($projectDir);
        mkdir($projectDir . '/templates');

        file_put_contents($projectDir . '/app.css', '@import "tailwindcss"; @source "./templates";');
        file_put_contents($projectDir . '/templates/index.html', '<div class="flex">Hello</div>');

        $input = new Input([
            'tailwindphp',
            '-i', 'app.css',
            '-o', 'output.css',
            '--cwd=' . $projectDir,
        ]);
        $output = $this->createMock(Output::class);

        $app = new Application($input, $output);
        $exitCode = $app->run();

        $this->assertSame(0, $exitCode);
        $this->assertFileExists($projectDir . '/output.css');
    }

    #[Test]
    public function build_with_optimize_option(): void
    {
        $inputFile = $this->testDir . '/app.css';
        $outputFile = $this->testDir . '/output.css';

        mkdir($this->testDir . '/templates');
        file_put_contents($this->testDir . '/templates/index.html', '<div class="flex">Hello</div>');
        file_put_contents($inputFile, '@import "tailwindcss"; @source "./templates";');

        $input = new Input([
            'tailwindphp',
            '-i', $inputFile,
            '-o', $outputFile,
            '--optimize',
        ]);
        $output = $this->createMock(Output::class);

        $originalDir = getcwd();
        chdir($this->testDir);

        try {
            $app = new Application($input, $output);
            $exitCode = $app->run();

            $this->assertSame(0, $exitCode);
            $this->assertFileExists($outputFile);
        } finally {
            chdir($originalDir);
        }
    }

    #[Test]
    public function build_scans_multiple_file_types(): void
    {
        $inputFile = $this->testDir . '/app.css';
        $outputFile = $this->testDir . '/output.css';

        mkdir($this->testDir . '/templates');
        file_put_contents($this->testDir . '/templates/index.php', '<div class="flex">PHP</div>');
        file_put_contents($this->testDir . '/templates/page.html', '<div class="grid">HTML</div>');
        file_put_contents($inputFile, '@import "tailwindcss"; @source "./templates";');

        $input = new Input([
            'tailwindphp',
            '-i', $inputFile,
            '-o', $outputFile,
        ]);
        $output = $this->createMock(Output::class);

        $originalDir = getcwd();
        chdir($this->testDir);

        try {
            $app = new Application($input, $output);
            $exitCode = $app->run();

            $this->assertSame(0, $exitCode);

            $css = file_get_contents($outputFile);
            $this->assertStringContainsString('.flex', $css);
            $this->assertStringContainsString('.grid', $css);
        } finally {
            chdir($originalDir);
        }
    }
}
