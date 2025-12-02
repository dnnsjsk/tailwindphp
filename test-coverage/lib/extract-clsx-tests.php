#!/usr/bin/env php
<?php

/**
 * Extract clsx test files from reference to test-coverage/lib/clsx/tests/
 *
 * This copies the JavaScript test files so they can be parsed by the PHP test runner.
 *
 * Usage: php extract-clsx-tests.php
 */

$baseDir = dirname(__DIR__, 2);
$referenceDir = $baseDir . '/reference/clsx/test';
$outputDir = __DIR__ . '/clsx/tests';

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Test files to extract (excluding lite.js as we don't port the lite version)
$testFiles = [
    'index.js',
    'classnames.js',
];

$totalTests = 0;

foreach ($testFiles as $file) {
    $sourcePath = $referenceDir . '/' . $file;
    $destPath = $outputDir . '/' . $file;

    if (!file_exists($sourcePath)) {
        echo "Warning: Source file not found: $sourcePath\n";
        continue;
    }

    // Copy the file
    copy($sourcePath, $destPath);

    // Count tests in file
    $content = file_get_contents($sourcePath);
    preg_match_all('/^test\(/m', $content, $matches);
    $testCount = count($matches[0]);
    $totalTests += $testCount;

    echo "Extracted: $file ($testCount tests)\n";
}

echo "\nTotal clsx tests extracted: $totalTests\n";

// Write a summary file
$summary = [
    'library' => 'clsx',
    'reference' => 'https://github.com/lukeed/clsx',
    'extracted_at' => date('Y-m-d H:i:s'),
    'files' => $testFiles,
    'total_tests' => $totalTests,
];

file_put_contents($outputDir . '/summary.json', json_encode($summary, JSON_PRETTY_PRINT));
echo "Summary written to: $outputDir/summary.json\n";
