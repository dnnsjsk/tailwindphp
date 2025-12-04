<?php

declare(strict_types=1);

namespace TailwindPHP\Cli\Commands;

use TailwindPHP\Cli\Command;
use TailwindPHP\Tailwind;

/**
 * Watch content files and rebuild CSS on changes.
 *
 * Uses file polling to detect changes. Works on all platforms.
 */
class WatchCommand extends Command
{
    /** @var array<string, int> File modification times */
    private array $fileTimes = [];

    /** @var bool Running flag */
    private bool $running = true;

    public function getName(): string
    {
        return 'watch';
    }

    public function getDescription(): string
    {
        return 'Watch for changes and rebuild CSS';
    }

    public function getHelp(): string
    {
        return <<<'HELP'
<yellow>USAGE:</yellow>
  tailwindphp watch [options]

<yellow>OPTIONS:</yellow>
  <green>-c, --content</green>=PATH    Content files to watch (glob pattern or directory)
  <green>-i, --input</green>=FILE      Input CSS file with @import directives
  <green>-o, --output</green>=FILE     Output CSS file (required)
  <green>-m, --minify</green>          Minify output CSS
  <green>--poll</green>=MS             Polling interval in milliseconds (default: 500)
  <green>--config</green>=FILE         Config file (default: tailwind.config.php)

<yellow>EXAMPLES:</yellow>
  <gray># Watch PHP templates</gray>
  tailwindphp watch -c "./templates/**/*.php" -o "./dist/styles.css"

  <gray># Watch with faster polling</gray>
  tailwindphp watch -c "./templates" -o "./dist/styles.css" --poll=100

  <gray># Watch with minification</gray>
  tailwindphp watch -c "./src" -o "./dist/styles.css" --minify

<yellow>NOTES:</yellow>
  Press Ctrl+C to stop watching.

HELP;
    }

    public function execute(): int
    {
        // Load config
        $config = $this->loadConfig();

        // Get content path(s)
        $contentPath = $this->getStringOpt('content', 'c', '');
        if ($contentPath === '' && isset($config['content'])) {
            $contentPath = $config['content'];
        }

        if ($contentPath === '' || $contentPath === []) {
            $this->output->error('No content path specified. Use -c or --content option.');

            return 1;
        }

        // Get input CSS file
        $inputFile = $this->getStringOpt('input', 'i', '');
        if ($inputFile === '' && isset($config['css'])) {
            $inputFile = $config['css'];
        }

        // Get output file (required for watch)
        $outputFile = $this->getStringOpt('output', 'o', '');
        if ($outputFile === '' && isset($config['output'])) {
            $outputFile = $config['output'];
        }

        if ($outputFile === '') {
            $this->output->error('Output file is required for watch mode. Use -o or --output option.');

            return 1;
        }

        // Get options
        $minify = $this->getBoolOpt('minify', 'm', $config['minify'] ?? false);
        $pollInterval = (int) $this->getStringOpt('poll', '', '500');
        $pollInterval = max(50, min(5000, $pollInterval)); // Clamp between 50ms and 5s

        $this->output->title('TailwindPHP Watch Mode');
        $this->output->info('Watching for changes...');
        $this->output->writeln('  ' . $this->output->color('gray', 'Press Ctrl+C to stop'));
        $this->output->writeln();

        // Set up signal handling for graceful shutdown
        if (function_exists('pcntl_signal')) {
            pcntl_signal(SIGINT, function () {
                $this->running = false;
            });
            pcntl_signal(SIGTERM, function () {
                $this->running = false;
            });
        }

        // Initial build
        $files = $this->resolveContentFiles($contentPath);
        if (empty($files)) {
            $this->output->warning('No content files found.');

            return 1;
        }

        // Store initial file times
        $this->updateFileTimes($files);

        // Also watch the input CSS file if specified
        $watchFiles = $files;
        if ($inputFile !== '' && file_exists($inputFile)) {
            $watchFiles[] = $inputFile;
            $this->fileTimes[$inputFile] = filemtime($inputFile) ?: 0;
        }

        // Initial build
        $this->build($files, $inputFile, $outputFile, $minify);

        // Watch loop
        $spinFrame = 0;
        while ($this->running) {
            // Check for signal
            if (function_exists('pcntl_signal_dispatch')) {
                pcntl_signal_dispatch();
            }

            // Check for changes
            $changedFiles = $this->detectChanges($watchFiles);

            if (!empty($changedFiles)) {
                // Clear spinner line
                $this->output->clearLine();

                foreach ($changedFiles as $file) {
                    $this->output->verbose("Changed: {$file}");
                }

                // Rebuild files list in case new files were added
                $files = $this->resolveContentFiles($contentPath);
                $this->updateFileTimes($files);

                // Rebuild
                $this->build($files, $inputFile, $outputFile, $minify);
            } else {
                // Show spinner
                $this->output->spinner($spinFrame++, 'Watching...');
            }

            // Sleep
            usleep($pollInterval * 1000);
        }

        $this->output->clearLine();
        $this->output->writeln();
        $this->output->info('Watch stopped.');

        return 0;
    }

    /**
     * Build CSS from content files.
     *
     * @param array<string> $files
     */
    private function build(array $files, string $inputFile, string $outputFile, bool $minify): void
    {
        $startTime = microtime(true);

        // Read content
        $content = '';
        foreach ($files as $file) {
            $fileContent = file_get_contents($file);
            if ($fileContent !== false) {
                $content .= $fileContent . "\n";
            }
        }

        // Get CSS
        $css = '@import "tailwindcss";';
        if ($inputFile !== '' && file_exists($inputFile)) {
            $css = file_get_contents($inputFile) ?: $css;
        }

        try {
            $generatedCss = Tailwind::generate([
                'content' => $content,
                'css' => $css,
                'minify' => $minify,
            ]);

            // Ensure output directory exists
            $outputDir = dirname($outputFile);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            file_put_contents($outputFile, $generatedCss);

            $duration = round((microtime(true) - $startTime) * 1000);
            $size = $this->formatBytes(strlen($generatedCss));
            $time = date('H:i:s');

            $this->output->writeln(
                '  ' . $this->output->color('gray', "[{$time}]") .
                $this->output->color('green', ' ✓ ') .
                "Rebuilt in {$duration}ms ({$size})",
            );
        } catch (\Throwable $e) {
            $time = date('H:i:s');
            $this->output->writeln(
                '  ' . $this->output->color('gray', "[{$time}]") .
                $this->output->color('red', ' ✗ ') .
                'Build failed: ' . $e->getMessage(),
            );
        }
    }

    /**
     * Detect changed files.
     *
     * @param array<string> $files
     * @return array<string> Changed files
     */
    private function detectChanges(array $files): array
    {
        $changed = [];

        foreach ($files as $file) {
            if (!file_exists($file)) {
                continue;
            }

            $mtime = filemtime($file) ?: 0;
            $oldTime = $this->fileTimes[$file] ?? 0;

            if ($mtime > $oldTime) {
                $changed[] = $file;
                $this->fileTimes[$file] = $mtime;
            }
        }

        return $changed;
    }

    /**
     * Update stored file modification times.
     *
     * @param array<string> $files
     */
    private function updateFileTimes(array $files): void
    {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $this->fileTimes[$file] = filemtime($file) ?: 0;
            }
        }
    }

    /**
     * Load config file.
     *
     * @return array<string, mixed>
     */
    private function loadConfig(): array
    {
        $configFile = $this->getStringOpt('config', '', 'tailwind.config.php');
        $configPath = getcwd() . '/' . $configFile;

        if (!file_exists($configPath)) {
            if (!file_exists($configFile)) {
                return [];
            }
            $configPath = $configFile;
        }

        $config = require $configPath;

        return is_array($config) ? $config : [];
    }

    /**
     * Resolve content files.
     *
     * @param string|array<string> $contentPath
     * @return array<string>
     */
    private function resolveContentFiles(string|array $contentPath): array
    {
        $paths = is_array($contentPath) ? $contentPath : [$contentPath];
        $files = [];

        foreach ($paths as $path) {
            $path = trim($path);
            if ($path === '') {
                continue;
            }

            if (str_contains($path, '*')) {
                $files = array_merge($files, $this->globRecursive($path));
            } elseif (is_dir($path)) {
                $files = array_merge($files, $this->scanDirectory($path));
            } elseif (is_file($path)) {
                $files[] = $path;
            }
        }

        return array_unique($files);
    }

    /**
     * Glob with ** support.
     *
     * @return array<string>
     */
    private function globRecursive(string $pattern): array
    {
        if (!str_contains($pattern, '**')) {
            $files = glob($pattern);

            return $files !== false ? $files : [];
        }

        $files = [];
        $parts = explode('**', $pattern, 2);
        $baseDir = rtrim($parts[0], '/\\') ?: '.';
        $filePattern = ltrim($parts[1] ?? '', '/\\');

        if (!is_dir($baseDir)) {
            return [];
        }

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            if ($filePattern !== '' && $filePattern !== '*') {
                $regex = $this->globToRegex($filePattern);
                if (!preg_match($regex, basename($file->getPathname()))) {
                    continue;
                }
            }

            $files[] = $file->getPathname();
        }

        return $files;
    }

    /**
     * Convert glob to regex.
     */
    private function globToRegex(string $pattern): string
    {
        $regex = preg_quote($pattern, '/');
        $regex = str_replace('\*', '.*', $regex);
        $regex = str_replace('\?', '.', $regex);

        $regex = preg_replace_callback('/\\\{([^}]+)\\\}/', function ($matches) {
            $options = explode(',', str_replace('\,', ',', $matches[1]));

            return '(' . implode('|', array_map('preg_quote', $options)) . ')';
        }, $regex) ?? $regex;

        return '/^' . $regex . '$/i';
    }

    /**
     * Scan directory for content files.
     *
     * @return array<string>
     */
    private function scanDirectory(string $dir): array
    {
        $extensions = ['php', 'html', 'htm', 'twig', 'blade.php', 'vue', 'jsx', 'tsx', 'svelte'];
        $files = [];

        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($dir, \RecursiveDirectoryIterator::SKIP_DOTS),
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $ext = $file->getExtension();
            $filename = $file->getFilename();

            if (str_ends_with($filename, '.blade.php') || in_array($ext, $extensions, true)) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Format bytes.
     */
    private function formatBytes(int $bytes): string
    {
        if ($bytes < 1024) {
            return $bytes . 'B';
        }
        if ($bytes < 1024 * 1024) {
            return round($bytes / 1024, 1) . 'KB';
        }

        return round($bytes / (1024 * 1024), 2) . 'MB';
    }
}
