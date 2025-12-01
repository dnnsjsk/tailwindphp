<?php

/**
 * Run all benchmarks and output results.
 *
 * Usage:
 *   php benchmarks/run-all.php
 *   php benchmarks/run-all.php --json    # Export to JSON
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/Benchmark.php';

use TailwindPHP\Benchmarks\Benchmark;

echo "TailwindPHP Benchmarks\n";
echo "======================\n";
echo "PHP " . PHP_VERSION . "\n";
echo date('Y-m-d H:i:s') . "\n\n";

$allResults = [];

// Run each benchmark file (located in src/ like the original TS implementation)
$benchmarkFiles = [
    'src/utils/segment.bench.php' => 'Segment Utility',
    'src/css-parser.bench.php' => 'CSS Parser',
    'src/ast.bench.php' => 'AST Operations',
    'src/utilities.bench.php' => 'Utility Compilation',
];

$baseDir = dirname(__DIR__);

foreach ($benchmarkFiles as $file => $name) {
    echo "## {$name}\n";
    echo str_repeat('-', strlen($name) + 3) . "\n";

    $filepath = $baseDir . '/' . $file;
    if (file_exists($filepath)) {
        include $filepath;
        echo "\n";
    } else {
        echo "File not found: {$file}\n\n";
    }
}

// Export to JSON if requested
if (in_array('--json', $argv ?? [])) {
    $outputPath = __DIR__ . '/results/php-' . date('Y-m-d-His') . '.json';
    if (!is_dir(__DIR__ . '/results')) {
        mkdir(__DIR__ . '/results', 0755, true);
    }

    $data = [
        'timestamp' => date('c'),
        'php_version' => PHP_VERSION,
        'platform' => PHP_OS,
    ];

    file_put_contents($outputPath, json_encode($data, JSON_PRETTY_PRINT));
    echo "Results exported to: {$outputPath}\n";
}
