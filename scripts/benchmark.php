#!/usr/bin/env php
<?php

/**
 * Benchmark Comparison: PHP vs TypeScript
 *
 * Runs equivalent benchmarks in PHP and TypeScript (via vitest)
 * and displays a side-by-side comparison.
 *
 * Usage:
 *   php scripts/benchmark.php              # Run all benchmarks
 *   php scripts/benchmark.php css-parser   # Run specific benchmark
 *   php scripts/benchmark.php --php-only   # Run PHP benchmarks only
 *   php scripts/benchmark.php --ts-only    # Run TypeScript benchmarks only
 *   php scripts/benchmark.php --list       # List available benchmarks
 *   php scripts/benchmark.php --save       # Save results to benchmarks/results.json
 */

declare(strict_types=1);

$rootDir = dirname(__DIR__);
require_once $rootDir . '/vendor/autoload.php';

// Colors for terminal output
function color(string $text, string $color): string
{
    $colors = [
        'green' => "\033[32m",
        'red' => "\033[31m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'cyan' => "\033[36m",
        'magenta' => "\033[35m",
        'reset' => "\033[0m",
        'bold' => "\033[1m",
        'dim' => "\033[2m",
    ];

    return ($colors[$color] ?? '') . $text . $colors['reset'];
}

function formatOps(float $ops): string
{
    if ($ops >= 1000000) {
        return number_format($ops / 1000000, 2) . 'M';
    } elseif ($ops >= 1000) {
        return number_format($ops / 1000, 2) . 'K';
    }

    return number_format($ops, 0);
}

function formatTime(float $ms): string
{
    if ($ms < 0.001) {
        return number_format($ms * 1000000, 2) . 'ns';
    } elseif ($ms < 1) {
        return number_format($ms * 1000, 2) . 'μs';
    }

    return number_format($ms, 2) . 'ms';
}

/**
 * Run a PHP benchmark
 */
function runPhpBenchmark(callable $fn, int $iterations = 1000): array
{
    // Warm up
    for ($i = 0; $i < 10; $i++) {
        $fn();
    }

    // Collect samples
    $times = [];
    for ($i = 0; $i < $iterations; $i++) {
        $start = hrtime(true);
        $fn();
        $end = hrtime(true);
        $times[] = ($end - $start) / 1e6; // ms
    }

    sort($times);
    $mean = array_sum($times) / count($times);
    $opsPerSec = 1000 / $mean;

    return [
        'hz' => $opsPerSec,
        'mean' => $mean,
    ];
}

/**
 * Run TypeScript benchmarks via vitest
 */
function runTsBenchmarks(string $pattern, string $rootDir): array
{
    $jsonFile = sys_get_temp_dir() . '/tailwind-bench-' . uniqid() . '.json';
    $refDir = $rootDir . '/reference/tailwindcss';

    $cmd = sprintf(
        'cd %s && pnpm exec vitest bench %s --run --outputJson %s 2>/dev/null',
        escapeshellarg($refDir),
        escapeshellarg($pattern),
        escapeshellarg($jsonFile),
    );

    exec($cmd, $output, $returnCode);

    if (!file_exists($jsonFile)) {
        return [];
    }

    $json = json_decode(file_get_contents($jsonFile), true);
    unlink($jsonFile);

    if (!$json || !isset($json['files'])) {
        return [];
    }

    $results = [];
    foreach ($json['files'] as $file) {
        foreach ($file['groups'] ?? [] as $group) {
            foreach ($group['benchmarks'] ?? [] as $bench) {
                $results[$bench['name']] = [
                    'hz' => $bench['hz'],
                    'mean' => $bench['mean'],
                ];
            }
        }
    }

    return $results;
}

/**
 * Print a comparison row
 */
function printRow(string $name, ?array $php, ?array $ts): void
{
    $name = str_pad($name, 32);

    $phpStr = $php ? formatOps($php['hz']) . ' ops/s' : 'N/A';
    $tsStr = $ts ? formatOps($ts['hz']) . ' ops/s' : 'N/A';

    $phpStr = str_pad($phpStr, 14);
    $tsStr = str_pad($tsStr, 14);

    $ratio = '';
    if ($php && $ts) {
        $r = $ts['hz'] / $php['hz'];
        if ($r > 1) {
            $ratio = color(sprintf('TS %.0fx faster', $r), 'yellow');
        } else {
            $ratio = color(sprintf('PHP %.1fx faster', 1 / $r), 'green');
        }
    }

    echo "  {$name} " . color($phpStr, 'cyan') . ' ' . color($tsStr, 'magenta') . "  {$ratio}\n";
}

// =============================================================================
// BENCHMARK DEFINITIONS
// =============================================================================

use function TailwindPHP\Ast\toCss;
use function TailwindPHP\CssParser\parse;

use TailwindPHP\Tailwind;

$preflightCss = file_get_contents($rootDir . '/resources/preflight.css');
$preflightAst = parse($preflightCss);

// Realistic HTML with many Tailwind classes for generate benchmark
$sampleHtml = <<<'HTML'
<div class="min-h-screen bg-gray-100 dark:bg-gray-900">
    <nav class="bg-white dark:bg-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <span class="text-xl font-bold text-gray-800 dark:text-white">Logo</span>
                </div>
                <div class="hidden md:flex items-center space-x-4">
                    <a href="#" class="text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">Home</a>
                    <a href="#" class="text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">About</a>
                    <a href="#" class="text-gray-600 hover:text-gray-800 dark:text-gray-300 dark:hover:text-white px-3 py-2 rounded-md text-sm font-medium transition-colors duration-200">Contact</a>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors duration-200 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">Sign In</button>
                </div>
            </div>
        </div>
    </nav>
    <main class="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Card Title</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Lorem ipsum dolor sit amet, consectetur adipiscing elit.</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-500">2 hours ago</span>
                        <button class="text-blue-500 hover:text-blue-600 text-sm font-medium">Read more →</button>
                    </div>
                </div>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
                <div class="p-6">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Another Card</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>
                    <div class="mt-4 flex items-center justify-between">
                        <span class="text-xs text-gray-500 dark:text-gray-500">5 hours ago</span>
                        <button class="text-blue-500 hover:text-blue-600 text-sm font-medium">Read more →</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="mt-8 flex flex-col sm:flex-row gap-4">
            <input type="text" placeholder="Enter your email" class="flex-1 px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
            <button class="bg-gradient-to-r from-blue-500 to-purple-600 hover:from-blue-600 hover:to-purple-700 text-white px-6 py-3 rounded-lg font-medium transition-all duration-200 transform hover:scale-105">Subscribe</button>
        </div>
    </main>
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-gray-400 text-sm">© 2024 Company. All rights reserved.</p>
        </div>
    </footer>
</div>
HTML;

$benchmarks = [
    'generate' => [
        'description' => 'Full CSS Generation',
        'ts_file' => null, // No direct TS equivalent
        'tests' => [
            'Tailwind::generate (50 classes)' => [
                'php' => function () use ($sampleHtml) {
                    return runPhpBenchmark(function () use ($sampleHtml) {
                        Tailwind::generate($sampleHtml);
                    }, 50); // Fewer iterations due to heavier operation
                },
            ],
        ],
    ],
    'css-parser' => [
        'description' => 'CSS Parser',
        'ts_file' => 'packages/tailwindcss/src/css-parser.bench.ts',
        'tests' => [
            'css-parser on preflight.css' => [
                'php' => function () use ($preflightCss) {
                    return runPhpBenchmark(function () use ($preflightCss) {
                        parse($preflightCss);
                    });
                },
            ],
        ],
    ],
    'ast' => [
        'description' => 'AST Operations',
        'ts_file' => 'packages/tailwindcss/src/ast.bench.ts',
        'tests' => [
            'toCss' => [
                'php' => function () use ($preflightAst) {
                    return runPhpBenchmark(function () use ($preflightAst) {
                        toCss($preflightAst);
                    });
                },
            ],
        ],
    ],
];

// =============================================================================
// MAIN
// =============================================================================

$args = array_slice($argv, 1);
$phpOnly = in_array('--php-only', $args);
$tsOnly = in_array('--ts-only', $args);
$listOnly = in_array('--list', $args);
$saveResults = in_array('--save', $args);

// Filter out flags
$args = array_filter($args, fn ($a) => !str_starts_with($a, '--'));
$specificBench = $args[0] ?? null;

// Collect all results for saving
$allResults = [
    'timestamp' => date('c'),
    'php_version' => PHP_VERSION,
    'node_version' => trim(shell_exec('node --version 2>/dev/null') ?? 'N/A'),
    'benchmarks' => [],
];

if ($listOnly) {
    echo "\nAvailable benchmarks:\n";
    foreach ($benchmarks as $key => $bench) {
        echo "  {$key} - {$bench['description']}\n";
    }
    echo "\n";
    exit(0);
}

echo "\n";
echo color("  ┌─────────────────────────────────────────────────────────────────────┐\n", 'dim');
echo color('  │', 'dim') . color('              TailwindPHP - Benchmark Comparison                   ', 'bold') . color("│\n", 'dim');
echo color("  └─────────────────────────────────────────────────────────────────────┘\n", 'dim');
echo "\n";

// Show versions
if (!$tsOnly) {
    echo '  PHP:     ' . color(PHP_VERSION, 'cyan') . "\n";
}
if (!$phpOnly) {
    $nodeVersion = trim(shell_exec('node --version 2>/dev/null') ?? 'N/A');
    echo '  Node.js: ' . color($nodeVersion, 'magenta') . "\n";
}
echo "\n";

// Header
echo '  ' . str_pad('Benchmark', 32) . ' ' . color(str_pad('PHP', 14), 'cyan') . ' ' . color(str_pad('TypeScript', 14), 'magenta') . "  Comparison\n";
echo '  ' . str_repeat('─', 75) . "\n";

$toRun = $specificBench && isset($benchmarks[$specificBench])
    ? [$specificBench => $benchmarks[$specificBench]]
    : $benchmarks;

foreach ($toRun as $key => $bench) {
    echo "\n  " . color($bench['description'], 'bold') . "\n";

    // Run TS benchmarks for this file if available
    $tsResults = [];
    if (!$phpOnly && $bench['ts_file']) {
        $tsResults = runTsBenchmarks($bench['ts_file'], $rootDir);
    }

    foreach ($bench['tests'] as $testName => $test) {
        $phpResult = null;
        $tsResult = null;

        if (!$tsOnly && isset($test['php'])) {
            $phpResult = ($test['php'])();
        }

        if (!$phpOnly && isset($tsResults[$testName])) {
            $tsResult = $tsResults[$testName];
        }

        printRow($testName, $phpResult, $tsResult);

        // Collect for saving
        $allResults['benchmarks'][] = [
            'category' => $bench['description'],
            'name' => $testName,
            'php' => $phpResult,
            'typescript' => $tsResult,
            'ratio' => ($phpResult && $tsResult) ? $tsResult['hz'] / $phpResult['hz'] : null,
        ];
    }
}

echo "\n";

// Save results if requested
if ($saveResults) {
    $outputFile = $rootDir . '/benchmarks/results.json';
    file_put_contents($outputFile, json_encode($allResults, JSON_PRETTY_PRINT));
    echo '  Results saved to: ' . color($outputFile, 'green') . "\n\n";
}
