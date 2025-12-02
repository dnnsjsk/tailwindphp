#!/usr/bin/env php
<?php

/**
 * Extract candidate parsing tests from candidate.test.ts
 *
 * This extracts tests that verify the candidate parser output.
 * Pattern: expect(run('candidate', {...})).toMatchInlineSnapshot(`[...]`)
 *
 * Usage: php extract-candidate-tests.php
 */

$baseDir = dirname(__DIR__);
$inputFile = $baseDir . '/reference/tailwindcss/packages/tailwindcss/src/candidate.test.ts';
$outputDir = $baseDir . '/test-coverage/candidate/tests';

if (!file_exists($inputFile)) {
    echo "Error: candidate.test.ts not found at: $inputFile\n";
    exit(1);
}

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$content = file_get_contents($inputFile);
$lines = explode("\n", $content);
$totalLines = count($lines);

echo "Processing $totalLines lines from candidate.test.ts\n";

// Parse it() blocks
$tests = [];
$currentTest = null;
$braceDepth = 0;
$inTest = false;
$testContent = [];

for ($i = 0; $i < $totalLines; $i++) {
    $line = $lines[$i];

    // Check for test start - it('name', () => { or it('name', async () => {
    if (preg_match("/^it\(['\"](.+?)['\"]/", $line, $matches)) {
        $currentTest = [
            'name' => $matches[1],
            'startLine' => $i + 1,
            'content' => [],
        ];
        $inTest = true;
        $braceDepth = 0;
        $testContent = [$line];

        $braceDepth += substr_count($line, '{') - substr_count($line, '}');
        continue;
    }

    if ($inTest) {
        $testContent[] = $line;
        $braceDepth += substr_count($line, '{') - substr_count($line, '}');

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

// Extract test cases from each test
$testCases = [];

foreach ($tests as $test) {
    $body = $test['content'];

    // Find run('candidate') calls with toMatchInlineSnapshot
    // Pattern: expect(run('candidate-string', ...)).toMatchInlineSnapshot(`[...]`)
    preg_match_all("/expect\(run\(['\"]([^'\"]+)['\"]/", $body, $runMatches, PREG_OFFSET_CAPTURE);

    foreach ($runMatches[0] as $idx => $match) {
        $candidate = $runMatches[1][$idx][0];
        $matchPos = $match[1];

        // Find the toMatchInlineSnapshot after this
        $afterMatch = substr($body, $matchPos);

        if (preg_match('/\.toMatchInlineSnapshot\s*\(\s*`/s', $afterMatch, $snapshotMatch, PREG_OFFSET_CAPTURE)) {
            $backtickStart = $matchPos + $snapshotMatch[0][1] + strlen($snapshotMatch[0][0]);
            $remaining = substr($body, $backtickStart);

            // Find closing backtick (handling nested backticks)
            $backtickEnd = findClosingBacktick($remaining);

            if ($backtickEnd !== null) {
                $expectedOutput = substr($remaining, 0, $backtickEnd);

                // Clean up the output - it's a JS object/array
                $expectedOutput = trim($expectedOutput);

                $testCases[] = [
                    'name' => $test['name'],
                    'candidate' => $candidate,
                    'expected' => $expectedOutput,
                    'type' => 'snapshot',
                ];
            }
        }

        // Also check for toEqual([]) pattern (empty result)
        if (preg_match('/\.toEqual\s*\(\s*\[\s*\]\s*\)/', $afterMatch)) {
            $testCases[] = [
                'name' => $test['name'],
                'candidate' => $candidate,
                'expected' => '[]',
                'type' => 'empty',
            ];
        }
    }
}

function findClosingBacktick(string $str): ?int
{
    $pos = 0;
    $len = strlen($str);
    $depth = 0;

    while ($pos < $len) {
        $char = $str[$pos];

        if ($char === '\\' && $pos + 1 < $len) {
            $pos += 2;
            continue;
        }

        if ($char === '`') {
            return $pos;
        }

        $pos++;
    }

    return null;
}

echo "Extracted " . count($testCases) . " test cases\n\n";

// Group by type
$snapshotTests = array_filter($testCases, fn($t) => $t['type'] === 'snapshot');
$emptyTests = array_filter($testCases, fn($t) => $t['type'] === 'empty');

echo "Snapshot tests: " . count($snapshotTests) . "\n";
echo "Empty tests: " . count($emptyTests) . "\n\n";

// Write test cases to JSON for use by PHP tests
$outputData = [
    'sourceFile' => 'tailwindcss/packages/tailwindcss/src/candidate.test.ts',
    'sourceLines' => $totalLines,
    'totalTests' => count($tests),
    'totalCases' => count($testCases),
    'cases' => $testCases,
];

// Write summary to parent directory
$summaryDir = dirname($outputDir);
file_put_contents("$summaryDir/summary.json", json_encode($outputData, JSON_PRETTY_PRINT));

// Write individual test files grouped by test name prefix
$categories = [];
foreach ($testCases as $case) {
    $testName = strtolower($case['name']);

    // Categorize by key words
    $category = 'other';
    if (str_contains($testName, 'variant')) $category = 'variants';
    elseif (str_contains($testName, 'modifier')) $category = 'modifiers';
    elseif (str_contains($testName, 'arbitrary')) $category = 'arbitrary';
    elseif (str_contains($testName, 'important')) $category = 'important';
    elseif (str_contains($testName, 'negative')) $category = 'negative';
    elseif (str_contains($testName, 'prefix')) $category = 'prefix';
    elseif (str_contains($testName, 'unknown') || str_contains($testName, 'skip')) $category = 'invalid';
    elseif (str_contains($testName, 'simple')) $category = 'basic';

    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }
    $categories[$category][] = $case;
}

// Write category files
foreach ($categories as $category => $cases) {
    $filename = "$outputDir/$category.json";
    file_put_contents($filename, json_encode($cases, JSON_PRETTY_PRINT));
    echo "Wrote $category.json (" . count($cases) . " cases)\n";
}

// Write README
$readme = <<<MD
# Candidate Parser Test Coverage

Tests extracted from `tailwindcss/packages/tailwindcss/src/candidate.test.ts`

## Coverage Stats

| Metric | Value |
|--------|-------|
| Source File Lines | $totalLines |
| Original Tests | {$outputData['totalTests']} |
| Extracted Cases | {$outputData['totalCases']} |

## Test Categories

| Category | Cases |
|----------|-------|

MD;

ksort($categories);
foreach ($categories as $category => $cases) {
    $count = count($cases);
    $readme .= "| $category | $count |\n";
}

$readme .= <<<MD

## Test Pattern

These tests verify the candidate parser output. Each test:
1. Parses a candidate string (e.g., `hover:flex`, `md:text-red-500`)
2. Expects a specific parsed structure with kind, root, variants, etc.

The PHP tests should verify that `parseCandidate()` returns equivalent structures.
MD;

file_put_contents("$summaryDir/README.md", $readme);

echo "\nDone! Check $summaryDir for extracted tests.\n";
