#!/usr/bin/env php
<?php

/**
 * Extract and split utilities.test.ts into smaller chunks for easier porting.
 *
 * This script parses the TypeScript test file and extracts individual tests
 * into separate JSON files that can be easily read and ported to PHP.
 */

$inputFile = dirname(__DIR__) . '/tailwindcss/packages/tailwindcss/src/utilities.test.ts';
$outputDir = dirname(__DIR__) . '/extracted-tests';

if (!file_exists($inputFile)) {
    echo "Error: utilities.test.ts not found at: $inputFile\n";
    exit(1);
}

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$content = file_get_contents($inputFile);
$lines = explode("\n", $content);
$totalLines = count($lines);

echo "Processing $totalLines lines from utilities.test.ts\n";

// Parse tests - look for test('name', async () => { ... })
$tests = [];
$currentTest = null;
$braceDepth = 0;
$inTest = false;
$testContent = [];

for ($i = 0; $i < $totalLines; $i++) {
    $line = $lines[$i];

    // Check for test start
    if (preg_match("/^test\(['\"](.+?)['\"]/", $line, $matches)) {
        $currentTest = [
            'name' => $matches[1],
            'startLine' => $i + 1,
            'content' => [],
        ];
        $inTest = true;
        $braceDepth = 0;
        $testContent = [$line];

        // Count braces on this line
        $braceDepth += substr_count($line, '{') - substr_count($line, '}');
        continue;
    }

    if ($inTest) {
        $testContent[] = $line;
        $braceDepth += substr_count($line, '{') - substr_count($line, '}');

        // Check if test ended
        if ($braceDepth <= 0 && preg_match('/^\}\)/', trim($line))) {
            $currentTest['endLine'] = $i + 1;
            $currentTest['content'] = implode("\n", $testContent);
            $tests[] = $currentTest;
            $inTest = false;
            $currentTest = null;
            $testContent = [];
        }
    }
}

echo "Found " . count($tests) . " tests\n\n";

// Group tests by utility category
$categories = [];
$categoryPatterns = [
    'accessibility' => ['sr-only', 'not-sr-only'],
    'pointer-events' => ['pointer-events'],
    'visibility' => ['visible', 'invisible', 'collapse'],
    'position' => ['position', 'static', 'fixed', 'absolute', 'relative', 'sticky'],
    'inset' => ['inset', 'top', 'right', 'bottom', 'left', 'start', 'end'],
    'isolation' => ['isolation', 'isolate'],
    'z-index' => ['z-index', 'z-'],
    'order' => ['order'],
    'columns' => ['columns'],
    'float' => ['float'],
    'clear' => ['clear'],
    'box-sizing' => ['box-border', 'box-content', 'box-sizing'],
    'display' => ['display', 'block', 'inline', 'flex', 'grid', 'hidden', 'contents'],
    'aspect-ratio' => ['aspect'],
    'container' => ['container'],
    'object' => ['object-fit', 'object-position', 'object-'],
    'overflow' => ['overflow'],
    'overscroll' => ['overscroll'],
    'scroll' => ['scroll-'],
    'truncate' => ['truncate'],
    'whitespace' => ['whitespace'],
    'text-wrap' => ['text-wrap'],
    'word-break' => ['word-break', 'break-'],
    'hyphens' => ['hyphens'],
    'content' => ['content-'],
    'forced-color' => ['forced-color'],
    'break' => ['break-before', 'break-inside', 'break-after'],
    'box-decoration' => ['box-decoration'],
    'sizing' => ['size-', 'w-', 'h-', 'min-w-', 'max-w-', 'min-h-', 'max-h-'],
    'flex' => ['flex', 'basis', 'grow', 'shrink'],
    'grid' => ['grid', 'col-', 'row-', 'auto-cols', 'auto-rows'],
    'gap' => ['gap'],
    'space' => ['space-x', 'space-y'],
    'divide' => ['divide'],
    'place' => ['place-content', 'place-items', 'place-self'],
    'align' => ['align-', 'items-', 'self-', 'content-', 'justify-'],
    'padding' => ['p-', 'px-', 'py-', 'pt-', 'pr-', 'pb-', 'pl-', 'ps-', 'pe-'],
    'margin' => ['m-', 'mx-', 'my-', 'mt-', 'mr-', 'mb-', 'ml-', 'ms-', 'me-'],
    'border' => ['border', 'rounded'],
    'outline' => ['outline'],
    'ring' => ['ring'],
    'shadow' => ['shadow'],
    'opacity' => ['opacity'],
    'blend' => ['mix-blend', 'bg-blend'],
    'filter' => ['blur', 'brightness', 'contrast', 'grayscale', 'hue-rotate', 'invert', 'saturate', 'sepia', 'drop-shadow', 'filter'],
    'backdrop' => ['backdrop-'],
    'table' => ['table-', 'border-collapse', 'border-spacing', 'caption-'],
    'transition' => ['transition', 'duration', 'ease', 'delay'],
    'animation' => ['animate'],
    'transform' => ['transform', 'scale', 'rotate', 'translate', 'skew', 'origin'],
    'accent' => ['accent-'],
    'appearance' => ['appearance'],
    'cursor' => ['cursor'],
    'caret' => ['caret-'],
    'pointer-events' => ['pointer-events'],
    'resize' => ['resize'],
    'scroll-behavior' => ['scroll-behavior'],
    'scroll-snap' => ['snap-'],
    'touch' => ['touch-'],
    'select' => ['select-'],
    'will-change' => ['will-change'],
    'fill' => ['fill-'],
    'stroke' => ['stroke-'],
    'text' => ['text-', 'font-', 'leading-', 'tracking-', 'line-clamp', 'list-'],
    'color' => ['color-', 'bg-', 'text-color'],
    'gradient' => ['from-', 'via-', 'to-', 'bg-gradient'],
];

// Assign tests to categories
foreach ($tests as $test) {
    $testName = strtolower($test['name']);
    $assigned = false;

    foreach ($categoryPatterns as $category => $patterns) {
        foreach ($patterns as $pattern) {
            if (stripos($testName, $pattern) !== false || stripos($testName, str_replace('-', '', $pattern)) !== false) {
                if (!isset($categories[$category])) {
                    $categories[$category] = [];
                }
                $categories[$category][] = $test;
                $assigned = true;
                break 2;
            }
        }
    }

    if (!$assigned) {
        if (!isset($categories['other'])) {
            $categories['other'] = [];
        }
        $categories['other'][] = $test;
    }
}

// Write summary
$summary = [
    'totalTests' => count($tests),
    'totalLines' => $totalLines,
    'categories' => [],
];

foreach ($categories as $category => $categoryTests) {
    $summary['categories'][$category] = [
        'count' => count($categoryTests),
        'tests' => array_map(fn($t) => $t['name'], $categoryTests),
    ];
}

file_put_contents("$outputDir/summary.json", json_encode($summary, JSON_PRETTY_PRINT));
echo "Wrote summary.json\n";

// Write individual category files
foreach ($categories as $category => $categoryTests) {
    $filename = "$outputDir/$category.json";
    $data = [
        'category' => $category,
        'testCount' => count($categoryTests),
        'tests' => $categoryTests,
    ];
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Wrote $category.json (" . count($categoryTests) . " tests)\n";
}

// Also write raw test chunks (every ~50 tests)
$chunkSize = 50;
$chunks = array_chunk($tests, $chunkSize);
foreach ($chunks as $index => $chunk) {
    $chunkNum = str_pad($index + 1, 2, '0', STR_PAD_LEFT);
    $filename = "$outputDir/chunk-$chunkNum.json";
    $data = [
        'chunk' => $index + 1,
        'totalChunks' => count($chunks),
        'startTest' => $chunk[0]['name'] ?? 'unknown',
        'endTest' => end($chunk)['name'] ?? 'unknown',
        'testCount' => count($chunk),
        'tests' => $chunk,
    ];
    file_put_contents($filename, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
}
echo "\nWrote " . count($chunks) . " chunk files\n";

// Print summary
echo "\n=== Summary ===\n";
echo "Total tests: " . count($tests) . "\n";
echo "Categories: " . count($categories) . "\n\n";

ksort($categories);
foreach ($categories as $category => $categoryTests) {
    echo sprintf("  %-20s %3d tests\n", $category, count($categoryTests));
}

echo "\nDone! Check $outputDir for extracted tests.\n";
