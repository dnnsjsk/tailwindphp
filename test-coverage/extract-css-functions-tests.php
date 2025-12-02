#!/usr/bin/env php
<?php

/**
 * Extract CSS function tests from css-functions.test.ts
 *
 * This extracts tests that verify CSS function transformations:
 *   - --alpha(), --spacing(), --theme()
 *
 * Pattern: compileCss(css`...`) with toMatchInlineSnapshot or error handling
 *
 * Usage: php extract-css-functions-tests.php
 */

$baseDir = dirname(__DIR__);
$inputFile = $baseDir . '/reference/tailwindcss/packages/tailwindcss/src/css-functions.test.ts';
$outputDir = $baseDir . '/test-coverage/css-functions/tests';

if (!file_exists($inputFile)) {
    echo "Error: css-functions.test.ts not found at: $inputFile\n";
    exit(1);
}

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$content = file_get_contents($inputFile);
$lines = explode("\n", $content);
$totalLines = count($lines);

echo "Processing $totalLines lines from css-functions.test.ts\n";

// Parse test() blocks
$tests = [];
$currentTest = null;
$braceDepth = 0;
$inTest = false;
$testContent = [];

for ($i = 0; $i < $totalLines; $i++) {
    $line = $lines[$i];

    // Check for test start
    if (preg_match("/^\\s*test\(['\"](.+?)['\"]/", $line, $matches)) {
        $currentTest = [
            'name' => $matches[1],
            'startLine' => $i + 1,
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

        if ($braceDepth <= 0 && preg_match('/^\s*\}\)/', $line)) {
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

// Extract test cases
$testCases = [];

foreach ($tests as $test) {
    $body = $test['content'];

    // Find CSS input and expected output
    // Pattern: compileCss(css`...`) or compileCss(css`...`, [...classes])

    $cssStart = strpos($body, 'css`');
    if ($cssStart === false) continue;

    $cssStart += 4;
    $remaining = substr($body, $cssStart);
    $cssEnd = findClosingBacktick($remaining);

    if ($cssEnd === null) continue;

    $inputCss = trim(substr($remaining, 0, $cssEnd));
    $afterCss = substr($body, $cssStart + $cssEnd);

    // Check for classes array - the array can contain brackets like [500] so we need
    // to find the matching closing bracket properly
    $classes = [];
    if (preg_match('/,\s*\[/s', $afterCss, $arrayStartMatch, PREG_OFFSET_CAPTURE)) {
        $arrayStart = $arrayStartMatch[0][1] + strlen($arrayStartMatch[0][0]);
        $arrayContent = extractBracketContent(substr($afterCss, $arrayStart - 1));
        if ($arrayContent !== null) {
            $classes = parseClassArray($arrayContent);
        }
    }

    // Check if it's an error test
    if (str_contains($body, 'rejects.toThrowErrorMatchingInlineSnapshot')) {
        // Extract error message
        if (preg_match('/toThrowErrorMatchingInlineSnapshot\s*\(\s*`([^`]+)`/s', $afterCss, $errorMatch)) {
            $testCases[] = [
                'name' => $test['name'],
                'type' => 'error',
                'inputCss' => $inputCss,
                'classes' => $classes,
                'expectedError' => trim($errorMatch[1]),
            ];
        }
        continue;
    }

    // Check for toMatchInlineSnapshot
    if (preg_match('/\.toMatchInlineSnapshot\s*\(\s*`/s', $afterCss, $snapshotMatch, PREG_OFFSET_CAPTURE)) {
        $backtickStart = $snapshotMatch[0][1] + strlen($snapshotMatch[0][0]);
        $snapshotRemaining = substr($afterCss, $backtickStart);
        $backtickEnd = findClosingBacktick($snapshotRemaining);

        if ($backtickEnd !== null) {
            $expectedCss = trim(substr($snapshotRemaining, 0, $backtickEnd));
            $testCases[] = [
                'name' => $test['name'],
                'type' => 'output',
                'inputCss' => $inputCss,
                'classes' => $classes,
                'expected' => $expectedCss,
            ];
        }
    }
}

function parseClassArray(string $str): array
{
    $classes = [];
    preg_match_all('/[\'"]([^\'"]+)[\'"]/', $str, $matches);
    foreach ($matches[1] as $class) {
        $classes[] = $class;
    }
    return $classes;
}

function extractBracketContent(string $str): ?string
{
    // $str should start with '['
    if ($str[0] !== '[') {
        return null;
    }

    $depth = 0;
    $len = strlen($str);

    for ($i = 0; $i < $len; $i++) {
        $char = $str[$i];

        // Handle string literals to avoid counting brackets inside strings
        if ($char === "'" || $char === '"') {
            $quote = $char;
            $i++;
            while ($i < $len && $str[$i] !== $quote) {
                if ($str[$i] === '\\') {
                    $i++; // Skip escaped char
                }
                $i++;
            }
            continue;
        }

        if ($char === '[') {
            $depth++;
        } elseif ($char === ']') {
            $depth--;
            if ($depth === 0) {
                // Return content between brackets (excluding the brackets themselves)
                return substr($str, 1, $i - 1);
            }
        }
    }

    return null;
}

function findClosingBacktick(string $str): ?int
{
    $pos = 0;
    $len = strlen($str);

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

// Count by type
$outputTests = array_filter($testCases, fn($t) => $t['type'] === 'output');
$errorTests = array_filter($testCases, fn($t) => $t['type'] === 'error');

echo "Output tests: " . count($outputTests) . "\n";
echo "Error tests: " . count($errorTests) . "\n\n";

// Categorize by function
$categories = [];
foreach ($testCases as $case) {
    $testName = $case['name'];

    // Determine category from test name
    $category = 'other';
    if (str_starts_with($testName, '--alpha')) $category = 'alpha';
    elseif (str_starts_with($testName, '--spacing')) $category = 'spacing';
    elseif (str_starts_with($testName, '--theme')) $category = 'theme';

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

// Write summary
$summaryDir = dirname($outputDir);
$outputData = [
    'sourceFile' => 'tailwindcss/packages/tailwindcss/src/css-functions.test.ts',
    'sourceLines' => $totalLines,
    'totalTests' => count($tests),
    'totalCases' => count($testCases),
    'outputTests' => count($outputTests),
    'errorTests' => count($errorTests),
    'categories' => array_map(fn($c) => count($c), $categories),
];

file_put_contents("$summaryDir/summary.json", json_encode($outputData, JSON_PRETTY_PRINT));

// Write README
$readme = "# CSS Functions Test Coverage\n\n";
$readme .= "Tests extracted from `tailwindcss/packages/tailwindcss/src/css-functions.test.ts`\n\n";
$readme .= "## Coverage Stats\n\n";
$readme .= "| Metric | Value |\n";
$readme .= "|--------|-------|\n";
$readme .= "| Source File Lines | $totalLines |\n";
$readme .= "| Original Tests | {$outputData['totalTests']} |\n";
$readme .= "| Extracted Cases | {$outputData['totalCases']} |\n";
$readme .= "| Output tests | {$outputData['outputTests']} |\n";
$readme .= "| Error tests | {$outputData['errorTests']} |\n\n";
$readme .= "## Test Categories\n\n";
$readme .= "| Category | Cases |\n";
$readme .= "|----------|-------|\n";

ksort($categories);
foreach ($categories as $category => $cases) {
    $count = count($cases);
    $readme .= "| $category | $count |\n";
}

$readme .= "\n## CSS Functions Tested\n\n";
$readme .= "- **--alpha(color / opacity)**: Apply opacity to colors\n";
$readme .= "- **--spacing(multiplier)**: Calculate spacing based on theme\n";
$readme .= "- **--theme(variable)**: Reference theme variables\n";

file_put_contents("$summaryDir/README.md", $readme);

echo "\nDone! Check $summaryDir for extracted tests.\n";
