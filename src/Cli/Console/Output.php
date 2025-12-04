<?php

/**
 * CLI Output Handler.
 *
 * Port of: @tailwindcss/cli terminal output
 *
 * This provides terminal output with ANSI color support similar to how
 * the original @tailwindcss/cli handles console output.
 *
 * Original: https://github.com/tailwindlabs/tailwindcss/tree/next/packages/%40tailwindcss-cli
 * License: MIT (https://github.com/tailwindlabs/tailwindcss/blob/next/LICENSE)
 *
 * @port-deviation:replacement The original uses picocolors for terminal colors.
 * PHP implementation provides equivalent ANSI escape sequences directly.
 *
 * @credits Tailwind Labs (https://tailwindcss.com)
 */

declare(strict_types=1);

namespace TailwindPHP\Cli\Console;

/**
 * Console output with color support.
 *
 * Provides methods for writing colored and styled output to the terminal.
 * Automatically detects if colors are supported.
 */
class Output
{
    private bool $supportsColor;

    private bool $quiet = false;

    private bool $verbose = false;

    /** @var resource */
    private $stdout;

    /** @var resource */
    private $stderr;

    // ANSI color codes
    private const COLORS = [
        'reset' => "\033[0m",
        'bold' => "\033[1m",
        'dim' => "\033[2m",
        'italic' => "\033[3m",
        'underline' => "\033[4m",

        // Foreground colors
        'black' => "\033[30m",
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'gray' => "\033[90m",

        // Bright foreground
        'bright_red' => "\033[91m",
        'bright_green' => "\033[92m",
        'bright_yellow' => "\033[93m",
        'bright_blue' => "\033[94m",
        'bright_magenta' => "\033[95m",
        'bright_cyan' => "\033[96m",
        'bright_white' => "\033[97m",

        // Background colors
        'bg_red' => "\033[41m",
        'bg_green' => "\033[42m",
        'bg_yellow' => "\033[43m",
        'bg_blue' => "\033[44m",
    ];

    public function __construct()
    {
        $this->stdout = \STDOUT;
        $this->stderr = \STDERR;
        $this->supportsColor = $this->detectColorSupport();
    }

    /**
     * Detect if the terminal supports colors.
     */
    private function detectColorSupport(): bool
    {
        // Check for NO_COLOR environment variable
        if (getenv('NO_COLOR') !== false) {
            return false;
        }

        // Check for FORCE_COLOR
        if (getenv('FORCE_COLOR') !== false) {
            return true;
        }

        // Check if stdout is a TTY
        if (function_exists('stream_isatty')) {
            return stream_isatty($this->stdout);
        }

        // Fallback for older PHP
        if (function_exists('posix_isatty')) {
            return posix_isatty($this->stdout);
        }

        // Check TERM environment variable
        $term = getenv('TERM');
        if ($term === false || $term === 'dumb') {
            return false;
        }

        return true;
    }

    /**
     * Set quiet mode (suppress normal output).
     */
    public function setQuiet(bool $quiet): void
    {
        $this->quiet = $quiet;
    }

    /**
     * Set verbose mode.
     */
    public function setVerbose(bool $verbose): void
    {
        $this->verbose = $verbose;
    }

    /**
     * Check if in verbose mode.
     */
    public function isVerbose(): bool
    {
        return $this->verbose;
    }

    /**
     * Write a line to stdout.
     */
    public function writeln(string $message = ''): void
    {
        $this->write($message . PHP_EOL);
    }

    /**
     * Write to stdout.
     */
    public function write(string $message): void
    {
        if ($this->quiet) {
            return;
        }

        fwrite($this->stdout, $this->format($message));
    }

    /**
     * Write to stderr.
     */
    public function writeError(string $message): void
    {
        fwrite($this->stderr, $this->format($message));
    }

    /**
     * Write a line to stderr.
     */
    public function writeErrorln(string $message = ''): void
    {
        $this->writeError($message . PHP_EOL);
    }

    /**
     * Write a success message.
     */
    public function success(string $message): void
    {
        $this->writeln($this->color('green', '  ✓ ') . $message);
    }

    /**
     * Write an error message.
     */
    public function error(string $message): void
    {
        $this->writeErrorln($this->color('red', '  ✗ ') . $message);
    }

    /**
     * Write a warning message.
     */
    public function warning(string $message): void
    {
        $this->writeln($this->color('yellow', '  ! ') . $message);
    }

    /**
     * Write an info message.
     */
    public function info(string $message): void
    {
        $this->writeln($this->color('cyan', '  → ') . $message);
    }

    /**
     * Write a verbose message (only shown in verbose mode).
     */
    public function verbose(string $message): void
    {
        if ($this->verbose) {
            $this->writeln($this->color('gray', '    ' . $message));
        }
    }

    /**
     * Write a title/header.
     */
    public function title(string $message): void
    {
        $this->writeln();
        $this->writeln($this->color('bold', $message));
        $this->writeln();
    }

    /**
     * Write a section header.
     */
    public function section(string $message): void
    {
        $this->writeln($this->color('yellow', $message));
    }

    /**
     * Write a newline.
     */
    public function newLine(int $count = 1): void
    {
        $this->write(str_repeat(PHP_EOL, $count));
    }

    /**
     * Apply color to text.
     */
    public function color(string $color, string $text): string
    {
        if (!$this->supportsColor || !isset(self::COLORS[$color])) {
            return $text;
        }

        return self::COLORS[$color] . $text . self::COLORS['reset'];
    }

    /**
     * Format text with inline color tags.
     *
     * Supports: <green>text</green>, <bold>text</bold>, etc.
     */
    public function format(string $message): string
    {
        if (!$this->supportsColor) {
            // Strip all tags if no color support
            return preg_replace('/<\/?[a-z_]+>/i', '', $message) ?? $message;
        }

        // Replace opening tags
        $message = preg_replace_callback(
            '/<([a-z_]+)>/i',
            function ($matches) {
                $color = strtolower($matches[1]);

                return self::COLORS[$color] ?? '';
            },
            $message,
        ) ?? $message;

        // Replace closing tags
        $message = preg_replace('/<\/[a-z_]+>/i', self::COLORS['reset'], $message) ?? $message;

        return $message;
    }

    /**
     * Display a progress indicator.
     */
    public function progress(int $current, int $total, string $message = ''): void
    {
        if ($this->quiet) {
            return;
        }

        $width = 30;
        $percent = $total > 0 ? ($current / $total) : 0;
        $filled = (int) ($width * $percent);
        $empty = $width - $filled;

        $bar = str_repeat('█', $filled) . str_repeat('░', $empty);
        $percentStr = str_pad((string) (int) ($percent * 100), 3, ' ', STR_PAD_LEFT);

        $output = "\r  {$bar} {$percentStr}%";
        if ($message !== '') {
            $output .= " {$message}";
        }

        fwrite($this->stdout, $this->format($output));

        if ($current >= $total) {
            fwrite($this->stdout, PHP_EOL);
        }
    }

    /**
     * Clear the current line.
     */
    public function clearLine(): void
    {
        if ($this->supportsColor) {
            fwrite($this->stdout, "\r\033[K");
        }
    }

    /**
     * Display a spinner frame.
     */
    public function spinner(int $frame, string $message = ''): void
    {
        if ($this->quiet) {
            return;
        }

        $frames = ['⠋', '⠙', '⠹', '⠸', '⠼', '⠴', '⠦', '⠧', '⠇', '⠏'];
        $spinner = $frames[$frame % count($frames)];

        $this->clearLine();
        fwrite($this->stdout, $this->format("  {$spinner} {$message}"));
    }

    /**
     * Create a formatted table.
     *
     * @param array<string> $headers
     * @param array<array<string>> $rows
     */
    public function table(array $headers, array $rows): void
    {
        // Calculate column widths
        $widths = [];
        foreach ($headers as $i => $header) {
            $widths[$i] = mb_strlen($header);
        }
        foreach ($rows as $row) {
            foreach ($row as $i => $cell) {
                $widths[$i] = max($widths[$i] ?? 0, mb_strlen((string) $cell));
            }
        }

        // Print headers
        $headerLine = '';
        $separator = '';
        foreach ($headers as $i => $header) {
            $headerLine .= str_pad($header, $widths[$i] + 2);
            $separator .= str_repeat('─', $widths[$i] + 2);
        }
        $this->writeln($this->color('bold', '  ' . $headerLine));
        $this->writeln($this->color('gray', '  ' . $separator));

        // Print rows
        foreach ($rows as $row) {
            $line = '  ';
            foreach ($row as $i => $cell) {
                $line .= str_pad((string) $cell, $widths[$i] + 2);
            }
            $this->writeln($line);
        }
    }
}
