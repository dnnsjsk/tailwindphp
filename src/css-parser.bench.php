<?php

/**
 * Benchmark: CSS parser
 *
 * Equivalent to: packages/tailwindcss/src/css-parser.bench.ts
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../benchmarks/Benchmark.php';

use TailwindPHP\Benchmarks\Benchmark;
use function TailwindPHP\parse;

$bench = new Benchmark();

// Load preflight.css from reference
$preflightPath = __DIR__ . '/../reference/tailwindcss/packages/tailwindcss/preflight.css';
$cssFile = file_get_contents($preflightPath);

$bench->bench('css-parser on preflight.css', function () use ($cssFile) {
    parse($cssFile);
});

$bench->printResults();
