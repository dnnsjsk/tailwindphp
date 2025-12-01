<?php

/**
 * Benchmark: segment utility
 *
 * Equivalent to: packages/tailwindcss/src/utils/segment.bench.ts
 */

declare(strict_types=1);

require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../benchmarks/Benchmark.php';

use TailwindPHP\Benchmarks\Benchmark;
use function TailwindPHP\Utils\segment;

$bench = new Benchmark();

$values = [
    ['hover:focus:underline', ':'],
    ['var(--a, 0 0 1px rgb(0, 0, 0)), 0 0 1px rgb(0, 0, 0)', ','],
    ['var(--some-value,env(safe-area-inset-top,var(--some-other-value,env(safe-area-inset))))', ','],
];

$bench->bench('segment', function () use ($values) {
    foreach ($values as [$value, $sep]) {
        segment($value, $sep);
    }
});

$bench->printResults();
