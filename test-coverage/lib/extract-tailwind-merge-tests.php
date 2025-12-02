#!/usr/bin/env php
<?php

/**
 * Extract tailwind-merge test files from reference to test-coverage/lib/tailwind-merge/tests/
 *
 * This copies the TypeScript test files so they can be parsed by the PHP test runner.
 *
 * Usage: php extract-tailwind-merge-tests.php
 */

$baseDir = dirname(__DIR__, 2);
$referenceDir = $baseDir . '/reference/tailwind-merge/tests';
$outputDir = __DIR__ . '/tailwind-merge/tests';

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Get all test files
$testFiles = glob($referenceDir . '/*.test.ts');

$totalTests = 0;
$filesSummary = [];

// Test files that require custom configuration or exports we don't have
// These are tracked but marked as not applicable
$notApplicable = [
    'create-tailwind-merge.test.ts',  // requires custom config
    'extend-tailwind-merge.test.ts',  // requires extendTailwindMerge
    'merge-configs.test.ts',          // requires mergeConfigs
    'theme.test.ts',                  // requires fromTheme
    'prefixes.test.ts',               // requires custom config with prefix
    'tailwind-css-versions.test.ts',  // requires version-specific configs
    'experimental-parse-class-name.test.ts', // requires experimentalParseClassName
    'lazy-initialization.test.ts',    // requires createTailwindMerge internals
    'docs-examples.test.ts',          // requires extendTailwindMerge
    'default-config.test.ts',         // requires getDefaultConfig
    'class-map.test.ts',              // requires createClassMap
    'class-group-conflicts.test.ts',  // requires custom config
    'colors.test.ts',                 // requires custom config
    'content-utilities.test.ts',      // requires custom config
    'per-side-border-colors.test.ts', // requires custom config
    'standalone-classes.test.ts',     // requires custom config
    'non-tailwind-classes.test.ts',   // requires custom config
    'type-generics.test.ts',          // TypeScript-only tests
];

foreach ($testFiles as $sourcePath) {
    $filename = basename($sourcePath);
    $destPath = $outputDir . '/' . $filename;

    // Copy the file
    copy($sourcePath, $destPath);

    // Count tests in file
    $content = file_get_contents($sourcePath);
    preg_match_all('/^test\(/m', $content, $matches);
    $testCount = count($matches[0]);

    $isApplicable = !in_array($filename, $notApplicable);

    $filesSummary[] = [
        'file' => $filename,
        'tests' => $testCount,
        'applicable' => $isApplicable,
    ];

    if ($isApplicable) {
        $totalTests += $testCount;
        echo "Extracted: $filename ($testCount tests)\n";
    } else {
        echo "Extracted: $filename ($testCount tests) [N/A - requires additional exports]\n";
    }
}

$totalInFiles = array_sum(array_column($filesSummary, 'tests'));

echo "\n";
echo "Total test files: " . count($testFiles) . "\n";
echo "Total tests in files: $totalInFiles\n";
echo "Applicable tests: $totalTests\n";
echo "N/A tests: " . ($totalInFiles - $totalTests) . "\n";

// Write a summary file
$summary = [
    'library' => 'tailwind-merge',
    'reference' => 'https://github.com/dcastil/tailwind-merge',
    'extracted_at' => date('Y-m-d H:i:s'),
    'total_files' => count($testFiles),
    'total_tests' => $totalInFiles,
    'applicable_tests' => $totalTests,
    'not_applicable_tests' => $totalInFiles - $totalTests,
    'files' => $filesSummary,
];

file_put_contents($outputDir . '/summary.json', json_encode($summary, JSON_PRETTY_PRINT));
echo "\nSummary written to: $outputDir/summary.json\n";
