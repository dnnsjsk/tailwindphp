<?php

declare(strict_types=1);

namespace TailwindPHP\Cli\Commands;

use TailwindPHP\Cli\Command;
use TailwindPHP\Tailwind;

/**
 * Build CSS from content files.
 *
 * Scans content files for Tailwind classes and generates optimized CSS.
 */
class BuildCommand extends Command
{
    public function getName(): string
    {
        return 'build';
    }

    public function getDescription(): string
    {
        return 'Build CSS from content files';
    }

    public function getHelp(): string
    {
        return <<<'HELP'
<yellow>USAGE:</yellow>
  tailwindphp build [options]

<yellow>OPTIONS:</yellow>
  <green>-c, --content</green>=PATH    Content files to scan (glob pattern or directory)
  <green>-i, --input</green>=FILE      Input CSS file with @import directives
  <green>-o, --output</green>=FILE     Output CSS file (default: stdout)
  <green>-m, --minify</green>          Minify output CSS
  <green>--cache</green>[=DIR]         Enable caching (optionally specify directory)
  <green>--no-cache</green>            Disable caching
  <green>--config</green>=FILE         Config file (default: tailwind.config.php)

<yellow>EXAMPLES:</yellow>
  <gray># Build from PHP templates</gray>
  tailwindphp build -c "./templates/**/*.php" -o "./dist/styles.css"

  <gray># Build with custom CSS input</gray>
  tailwindphp build -c "./src" -i "./css/app.css" -o "./dist/app.css"

  <gray># Build minified with caching</gray>
  tailwindphp build -c "./templates" -o "./dist/styles.css" --minify --cache

  <gray># Output to stdout</gray>
  tailwindphp build -c "./templates" --minify

HELP;
    }

    public function execute(): int
    {
        $startTime = microtime(true);

        // Load config file if exists
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

        // Get output file
        $outputFile = $this->getStringOpt('output', 'o', '');
        if ($outputFile === '' && isset($config['output'])) {
            $outputFile = $config['output'];
        }

        // Get options
        $minify = $this->getBoolOpt('minify', 'm', $config['minify'] ?? false);
        $cache = $this->resolveCache($config);

        $this->output->info('Building CSS...');

        // Scan content files
        $files = $this->resolveContentFiles($contentPath);
        if (empty($files)) {
            $this->output->warning('No content files found matching pattern.');

            return 1;
        }

        $this->output->verbose('Found ' . count($files) . ' content files');

        // Read and combine content
        $content = '';
        foreach ($files as $file) {
            $this->output->verbose("Reading: {$file}");
            $content .= file_get_contents($file) . "\n";
        }

        // Build CSS configuration
        $css = '@import "tailwindcss";';
        if ($inputFile !== '') {
            if (!file_exists($inputFile)) {
                $this->output->error("Input CSS file not found: {$inputFile}");

                return 1;
            }
            $css = file_get_contents($inputFile);
            $this->output->verbose("Using CSS from: {$inputFile}");
        }

        // Generate CSS
        try {
            $generatedCss = Tailwind::generate([
                'content' => $content,
                'css' => $css,
                'minify' => $minify,
                'cache' => $cache,
            ]);
        } catch (\Throwable $e) {
            $this->output->error('CSS generation failed: ' . $e->getMessage());

            return 1;
        }

        // Output results
        if ($outputFile !== '') {
            // Ensure output directory exists
            $outputDir = dirname($outputFile);
            if (!is_dir($outputDir)) {
                mkdir($outputDir, 0755, true);
            }

            file_put_contents($outputFile, $generatedCss);

            $duration = round((microtime(true) - $startTime) * 1000);
            $size = $this->formatBytes(strlen($generatedCss));

            $this->output->success("Built {$outputFile} ({$size}) in {$duration}ms");
        } else {
            // Output to stdout
            echo $generatedCss;
        }

        return 0;
    }

    /**
     * Load config from tailwind.config.php.
     *
     * @return array<string, mixed>
     */
    private function loadConfig(): array
    {
        $configFile = $this->getStringOpt('config', '', 'tailwind.config.php');

        // Try current directory first
        $configPath = getcwd() . '/' . $configFile;
        if (!file_exists($configPath)) {
            // Try without path
            if (!file_exists($configFile)) {
                return [];
            }
            $configPath = $configFile;
        }

        $this->output->verbose("Loading config: {$configPath}");

        $config = require $configPath;

        return is_array($config) ? $config : [];
    }

    /**
     * Resolve content files from path/pattern.
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

            // Check if it's a glob pattern
            if (str_contains($path, '*')) {
                $matched = $this->globRecursive($path);
                $files = array_merge($files, $matched);
            } elseif (is_dir($path)) {
                // Directory - scan recursively for common file types
                $matched = $this->scanDirectory($path);
                $files = array_merge($files, $matched);
            } elseif (is_file($path)) {
                $files[] = $path;
            }
        }

        return array_unique($files);
    }

    /**
     * Recursively match glob pattern.
     *
     * @return array<string>
     */
    private function globRecursive(string $pattern): array
    {
        // Handle ** pattern for recursive matching
        if (str_contains($pattern, '**')) {
            return $this->globDoublestar($pattern);
        }

        $files = glob($pattern);

        return $files !== false ? $files : [];
    }

    /**
     * Handle ** glob pattern.
     *
     * @return array<string>
     */
    private function globDoublestar(string $pattern): array
    {
        $files = [];

        // Split pattern at **
        $parts = explode('**', $pattern, 2);
        $baseDir = rtrim($parts[0], '/\\');
        $filePattern = ltrim($parts[1] ?? '', '/\\');

        if ($baseDir === '') {
            $baseDir = '.';
        }

        if (!is_dir($baseDir)) {
            return [];
        }

        // Recursively scan directory
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($baseDir, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST,
        );

        foreach ($iterator as $file) {
            if (!$file->isFile()) {
                continue;
            }

            $filePath = $file->getPathname();

            // Match against file pattern
            if ($filePattern !== '' && $filePattern !== '*') {
                // Convert glob pattern to regex
                $regex = $this->globToRegex($filePattern);
                if (!preg_match($regex, basename($filePath))) {
                    continue;
                }
            }

            $files[] = $filePath;
        }

        return $files;
    }

    /**
     * Convert glob pattern to regex.
     */
    private function globToRegex(string $pattern): string
    {
        $regex = preg_quote($pattern, '/');
        $regex = str_replace('\*', '.*', $regex);
        $regex = str_replace('\?', '.', $regex);

        // Handle brace expansion like *.{php,html,blade.php}
        $regex = preg_replace_callback('/\\\{([^}]+)\\\}/', function ($matches) {
            $options = explode(',', str_replace('\,', ',', $matches[1]));

            return '(' . implode('|', array_map('preg_quote', $options)) . ')';
        }, $regex);

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

            // Check for .blade.php
            if (str_ends_with($filename, '.blade.php')) {
                $files[] = $file->getPathname();

                continue;
            }

            if (in_array($ext, $extensions, true)) {
                $files[] = $file->getPathname();
            }
        }

        return $files;
    }

    /**
     * Resolve cache option.
     *
     * @param array<string, mixed> $config
     * @return bool|string|null
     */
    private function resolveCache(array $config): bool|string|null
    {
        // --no-cache disables
        if ($this->input->hasOption('no-cache')) {
            return null;
        }

        // --cache or --cache=path
        if ($this->input->hasOption('cache')) {
            $cacheValue = $this->input->getOption('cache');
            if (is_string($cacheValue) && $cacheValue !== '') {
                return $cacheValue;
            }

            return true;
        }

        // From config
        return $config['cache'] ?? null;
    }

    /**
     * Format bytes to human readable.
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
