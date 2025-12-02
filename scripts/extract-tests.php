#!/usr/bin/env php
<?php

/**
 * Extract and split utilities.test.ts into smaller chunks for easier porting.
 *
 * This script parses the TypeScript test file and extracts individual tests
 * into separate .ts files grouped by category. Files over 1000 lines are split.
 */

$inputFile = dirname(__DIR__) . '/reference/tailwindcss/packages/tailwindcss/src/utilities.test.ts';
$outputDir = dirname(__DIR__) . '/src/utilities-test';

if (!file_exists($inputFile)) {
    echo "Error: utilities.test.ts not found at: $inputFile\n";
    exit(1);
}

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Clear existing files
foreach (glob("$outputDir/*.ts") as $file) {
    unlink($file);
}
foreach (glob("$outputDir/*.json") as $file) {
    unlink($file);
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
            $currentTest['lineCount'] = count($testContent);
            $tests[] = $currentTest;
            $inTest = false;
            $currentTest = null;
            $testContent = [];
        }
    }
}

echo "Found " . count($tests) . " tests\n\n";

// Category patterns for grouping tests
$categoryPatterns = [
    'accessibility' => ['sr-only', 'not-sr-only'],
    'pointer-events' => ['pointer-events'],
    'visibility' => ['visible', 'invisible', 'collapse'],
    'position' => ['position', 'static', 'fixed', 'absolute', 'relative', 'sticky'],
    'inset' => ['inset', 'top-', 'right-', 'bottom-', 'left-', 'start-', 'end-'],
    'isolation' => ['isolation', 'isolate'],
    'z-index' => ['z-index', 'z-'],
    'order' => ['order'],
    'columns' => ['columns'],
    'float' => ['float'],
    'clear' => ['clear'],
    'box-sizing' => ['box-border', 'box-content', 'box-sizing'],
    'display' => ['display', 'block', 'inline', 'flex-display', 'grid-display', 'hidden', 'contents'],
    'aspect-ratio' => ['aspect'],
    'container' => ['container'],
    'object' => ['object-fit', 'object-position', 'object-'],
    'overflow' => ['overflow'],
    'overscroll' => ['overscroll'],
    'scroll' => ['scroll-'],
    'truncate' => ['truncate'],
    'whitespace' => ['whitespace'],
    'text-wrap' => ['text-wrap'],
    'word-break' => ['word-break', 'break-words', 'break-all'],
    'hyphens' => ['hyphens'],
    'content' => ['content-none', 'content-empty'],
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
    'align' => ['align-', 'items-', 'self-', 'content-start', 'content-end', 'content-center', 'justify-'],
    'padding' => ['p-', 'px-', 'py-', 'pt-', 'pr-', 'pb-', 'pl-', 'ps-', 'pe-'],
    'margin' => ['m-', 'mx-', 'my-', 'mt-', 'mr-', 'mb-', 'ml-', 'ms-', 'me-'],
    'border' => ['border', 'rounded'],
    'outline' => ['outline'],
    'ring' => ['ring'],
    'shadow' => ['shadow', 'inset-shadow'],
    'opacity' => ['opacity'],
    'blend' => ['mix-blend', 'bg-blend'],
    'filter' => ['blur', 'brightness', 'contrast', 'grayscale', 'hue-rotate', 'invert', 'saturate', 'sepia', 'drop-shadow', 'filter-'],
    'backdrop' => ['backdrop-'],
    'table' => ['table-', 'border-collapse', 'border-spacing', 'caption-'],
    'transition' => ['transition', 'duration', 'ease-', 'delay'],
    'animation' => ['animate'],
    'transform' => ['transform', 'scale', 'rotate', 'translate', 'skew', 'origin'],
    'accent' => ['accent-'],
    'appearance' => ['appearance'],
    'cursor' => ['cursor'],
    'caret' => ['caret-'],
    'resize' => ['resize'],
    'scroll-behavior' => ['scroll-behavior'],
    'scroll-snap' => ['snap-'],
    'touch' => ['touch-'],
    'select' => ['select-'],
    'will-change' => ['will-change'],
    'fill' => ['fill-'],
    'stroke' => ['stroke-'],
    'text' => ['text-', 'font-', 'leading-', 'tracking-', 'line-clamp', 'list-'],
    'bg' => ['bg-'],
    'color' => ['color-'],
    'gradient' => ['from-', 'via-', 'to-'],
];

// Assign tests to categories
$categories = [];
foreach ($tests as $test) {
    $testName = strtolower($test['name']);
    $assigned = false;

    foreach ($categoryPatterns as $category => $patterns) {
        foreach ($patterns as $pattern) {
            $patternLower = strtolower($pattern);
            if (str_starts_with($testName, $patternLower) ||
                str_starts_with($testName, str_replace('-', '', $patternLower))) {
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

// Generate file header
$fileHeader = <<<'TS'
/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

TS;

// Write category files, splitting if over 1000 lines
$maxLinesPerFile = 1000;
$stats = [];

foreach ($categories as $category => $categoryTests) {
    $totalCategoryLines = array_sum(array_map(fn($t) => $t['lineCount'], $categoryTests));
    $stats[$category] = [
        'tests' => count($categoryTests),
        'lines' => $totalCategoryLines,
        'files' => 0,
    ];

    // If small enough, write as single file
    if ($totalCategoryLines <= $maxLinesPerFile) {
        $filename = "$outputDir/$category.ts";
        $content = $fileHeader . "\n";
        foreach ($categoryTests as $test) {
            $content .= $test['content'] . "\n\n";
        }
        file_put_contents($filename, $content);
        $stats[$category]['files'] = 1;
        $lineCount = count(explode("\n", $content));
        echo "Wrote $category.ts (" . count($categoryTests) . " tests, $lineCount lines)\n";
    } else {
        // Split into multiple files
        $fileIndex = 1;
        $currentContent = $fileHeader . "\n";
        $currentLines = count(explode("\n", $fileHeader)) + 1;
        $testsInFile = 0;

        foreach ($categoryTests as $test) {
            $testLines = $test['lineCount'];

            // If adding this test would exceed limit, write current file and start new one
            if ($currentLines + $testLines > $maxLinesPerFile && $testsInFile > 0) {
                $filename = "$outputDir/{$category}-" . str_pad($fileIndex, 2, '0', STR_PAD_LEFT) . ".ts";
                file_put_contents($filename, $currentContent);
                $lineCount = count(explode("\n", $currentContent));
                echo "Wrote {$category}-" . str_pad($fileIndex, 2, '0', STR_PAD_LEFT) . ".ts ($testsInFile tests, $lineCount lines)\n";
                $fileIndex++;
                $currentContent = $fileHeader . "\n";
                $currentLines = count(explode("\n", $fileHeader)) + 1;
                $testsInFile = 0;
            }

            $currentContent .= $test['content'] . "\n\n";
            $currentLines += $testLines + 2;
            $testsInFile++;
        }

        // Write remaining content
        if ($testsInFile > 0) {
            $filename = "$outputDir/{$category}-" . str_pad($fileIndex, 2, '0', STR_PAD_LEFT) . ".ts";
            file_put_contents($filename, $currentContent);
            $lineCount = count(explode("\n", $currentContent));
            echo "Wrote {$category}-" . str_pad($fileIndex, 2, '0', STR_PAD_LEFT) . ".ts ($testsInFile tests, $lineCount lines)\n";
        }

        $stats[$category]['files'] = $fileIndex;
    }
}

// Write summary JSON
$summary = [
    'sourceFile' => 'tailwindcss/packages/tailwindcss/src/utilities.test.ts',
    'sourceLines' => $totalLines,
    'totalTests' => count($tests),
    'categories' => [],
];

foreach ($stats as $category => $info) {
    $summary['categories'][$category] = [
        'tests' => $info['tests'],
        'lines' => $info['lines'],
        'files' => $info['files'],
        'testNames' => array_map(fn($t) => $t['name'], $categories[$category]),
    ];
}

file_put_contents("$outputDir/summary.json", json_encode($summary, JSON_PRETTY_PRINT));

// Print summary
echo "\n=== Summary ===\n";
echo "Source: $totalLines lines\n";
echo "Total tests: " . count($tests) . "\n";
echo "Categories: " . count($categories) . "\n\n";

ksort($stats);
$totalFiles = 0;
foreach ($stats as $category => $info) {
    $totalFiles += $info['files'];
    $filesStr = $info['files'] > 1 ? "({$info['files']} files)" : "";
    echo sprintf("  %-20s %3d tests, %5d lines %s\n", $category, $info['tests'], $info['lines'], $filesStr);
}

echo "\nTotal files: $totalFiles\n";
echo "\nDone! Check $outputDir for extracted tests.\n";
