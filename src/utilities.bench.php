<?php

/**
 * Benchmark: Utility compilation
 *
 * Tests how fast we can compile various utility classes.
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../benchmarks/Benchmark.php';

use TailwindPHP\Benchmarks\Benchmark;
use TailwindPHP\Tests\TestHelper;

$bench = new Benchmark();

// Common utility classes
$simpleUtilities = [
    'flex', 'hidden', 'block', 'inline', 'grid',
    'p-4', 'm-2', 'px-6', 'py-3',
    'w-full', 'h-screen', 'min-w-0',
    'text-center', 'font-bold', 'text-lg',
    'bg-blue-500', 'text-white', 'border-gray-200',
    'rounded-lg', 'shadow-md',
];

// Complex utilities with arbitrary values
$complexUtilities = [
    'w-[calc(100%-2rem)]',
    'bg-[#ff0000]',
    'grid-cols-[repeat(auto-fit,minmax(200px,1fr))]',
    'translate-x-1/2',
    '-rotate-45',
    'shadow-[0_0_10px_rgba(0,0,0,0.5)]',
];

// Mixed realistic usage
$realisticSet = [
    'flex', 'items-center', 'justify-between', 'p-4', 'bg-white',
    'rounded-lg', 'shadow-md', 'hover:shadow-lg', 'transition-shadow',
    'text-gray-900', 'font-semibold', 'text-xl',
];

$bench->describe('Utility Compilation', function ($bench) use ($simpleUtilities, $complexUtilities, $realisticSet) {
    $bench->bench('Simple utilities (15 classes)', function () use ($simpleUtilities) {
        TestHelper::run($simpleUtilities);
    });

    $bench->bench('Complex utilities (6 classes)', function () use ($complexUtilities) {
        TestHelper::run($complexUtilities);
    });

    $bench->bench('Realistic component (12 classes)', function () use ($realisticSet) {
        TestHelper::run($realisticSet);
    });

    $bench->bench('Single utility (flex)', function () {
        TestHelper::run(['flex']);
    });

    $bench->bench('Single utility with value (p-4)', function () {
        TestHelper::run(['p-4']);
    });

    $bench->bench('Arbitrary value (w-[100px])', function () {
        TestHelper::run(['w-[100px]']);
    });
});

$bench->printResults();
