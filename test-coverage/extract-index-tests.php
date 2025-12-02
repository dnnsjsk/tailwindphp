#!/usr/bin/env php
<?php

/**
 * Extract compilation tests from index.test.ts
 *
 * This extracts tests that verify the full CSS compilation.
 * Patterns:
 *   - compileCss(css`...`, [...classes]) - Full compilation with theme
 *   - run([...classes]) - Simple class compilation
 *
 * Usage: php extract-index-tests.php
 */

$baseDir = dirname(__DIR__);
$inputFile = $baseDir . '/reference/tailwindcss/packages/tailwindcss/src/index.test.ts';
$outputDir = $baseDir . '/test-coverage/index/tests';

if (!file_exists($inputFile)) {
    echo "Error: index.test.ts not found at: $inputFile\n";
    exit(1);
}

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$content = file_get_contents($inputFile);
$lines = explode("\n", $content);
$totalLines = count($lines);

echo "Processing $totalLines lines from index.test.ts\n";

// Parse test() and it() blocks
$tests = [];
$currentTest = null;
$braceDepth = 0;
$inTest = false;
$testContent = [];

for ($i = 0; $i < $totalLines; $i++) {
    $line = $lines[$i];

    // Check for test start
    if (preg_match("/^\\s*(test|it)\(['\"](.+?)['\"]/", $line, $matches)) {
        $currentTest = [
            'name' => $matches[2],
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

    // Pattern 1: await run([...classes])
    preg_match_all('/await run\(\[([^\]]+)\]\)/', $body, $runMatches, PREG_OFFSET_CAPTURE);

    foreach ($runMatches[0] as $idx => $match) {
        $classesStr = $runMatches[1][$idx][0];
        $classes = parseClassArray($classesStr);
        $matchPos = $match[1];

        // Find toMatchInlineSnapshot after this
        $afterMatch = substr($body, $matchPos);
        if (preg_match('/\.toMatchInlineSnapshot\s*\(\s*`/s', $afterMatch, $snapshotMatch, PREG_OFFSET_CAPTURE)) {
            $backtickStart = $snapshotMatch[0][1] + strlen($snapshotMatch[0][0]);
            $remaining = substr($afterMatch, $backtickStart);
            $backtickEnd = findClosingBacktick($remaining);

            if ($backtickEnd !== null) {
                $expectedCss = substr($remaining, 0, $backtickEnd);
                $testCases[] = [
                    'name' => $test['name'],
                    'type' => 'run',
                    'classes' => $classes,
                    'css' => null,
                    'expected' => trim($expectedCss),
                ];
            }
        }
    }

    // Pattern 2: await compileCss(css`...`, [...classes])
    // This is more complex - need to extract the CSS template and classes
    if (preg_match('/await compileCss\(\s*css`/s', $body)) {
        // Find the CSS template
        $cssStart = strpos($body, 'css`');
        if ($cssStart !== false) {
            $cssStart += 4; // Skip 'css`'
            $cssContent = substr($body, $cssStart);
            $cssEnd = findClosingBacktick($cssContent);

            if ($cssEnd !== null) {
                $cssTemplate = substr($cssContent, 0, $cssEnd);

                // Find the classes array after the CSS template
                $afterCss = substr($body, $cssStart + $cssEnd);
                if (preg_match('/,\s*\[([^\]]+)\]/s', $afterCss, $classMatch)) {
                    $classes = parseClassArray($classMatch[1]);

                    // Find toMatchInlineSnapshot
                    if (preg_match('/\.toMatchInlineSnapshot\s*\(\s*`/s', $afterCss, $snapshotMatch, PREG_OFFSET_CAPTURE)) {
                        $backtickStart = $snapshotMatch[0][1] + strlen($snapshotMatch[0][0]);
                        $remaining = substr($afterCss, $backtickStart);
                        $backtickEnd = findClosingBacktick($remaining);

                        if ($backtickEnd !== null) {
                            $expectedCss = substr($remaining, 0, $backtickEnd);
                            $testCases[] = [
                                'name' => $test['name'],
                                'type' => 'compileCss',
                                'classes' => $classes,
                                'css' => trim($cssTemplate),
                                'expected' => trim($expectedCss),
                            ];
                        }
                    }
                }
            }
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
$runTests = array_filter($testCases, fn($t) => $t['type'] === 'run');
$compileCssTests = array_filter($testCases, fn($t) => $t['type'] === 'compileCss');

echo "run() tests: " . count($runTests) . "\n";
echo "compileCss() tests: " . count($compileCssTests) . "\n\n";

// Categorize tests
$categories = [];
foreach ($testCases as $case) {
    $testName = strtolower($case['name']);

    $category = 'other';
    if (str_contains($testName, '@tailwind')) $category = 'tailwind-directive';
    elseif (str_contains($testName, '@theme')) $category = 'theme';
    elseif (str_contains($testName, '@apply')) $category = 'apply';
    elseif (str_contains($testName, '@import')) $category = 'import';
    elseif (str_contains($testName, '@layer')) $category = 'layers';
    elseif (str_contains($testName, 'arbitrary')) $category = 'arbitrary';
    elseif (str_contains($testName, 'prefix')) $category = 'prefix';
    elseif (str_contains($testName, 'important')) $category = 'important';
    elseif (str_contains($testName, 'vendor')) $category = 'vendor-prefixes';
    elseif (str_contains($testName, 'variable') || str_contains($testName, '--')) $category = 'css-variables';

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
    'sourceFile' => 'tailwindcss/packages/tailwindcss/src/index.test.ts',
    'sourceLines' => $totalLines,
    'totalTests' => count($tests),
    'totalCases' => count($testCases),
    'runTests' => count($runTests),
    'compileCssTests' => count($compileCssTests),
    'categories' => array_map(fn($c) => count($c), $categories),
];

file_put_contents("$summaryDir/summary.json", json_encode($outputData, JSON_PRETTY_PRINT));

// Write README
$readme = "# Index Test Coverage\n\n";
$readme .= "Tests extracted from `tailwindcss/packages/tailwindcss/src/index.test.ts`\n\n";
$readme .= "## Coverage Stats\n\n";
$readme .= "| Metric | Value |\n";
$readme .= "|--------|-------|\n";
$readme .= "| Source File Lines | $totalLines |\n";
$readme .= "| Original Tests | {$outputData['totalTests']} |\n";
$readme .= "| Extracted Cases | {$outputData['totalCases']} |\n";
$readme .= "| run() tests | {$outputData['runTests']} |\n";
$readme .= "| compileCss() tests | {$outputData['compileCssTests']} |\n\n";
$readme .= "## Test Categories\n\n";
$readme .= "| Category | Cases |\n";
$readme .= "|----------|-------|\n";

ksort($categories);
foreach ($categories as $category => $cases) {
    $count = count($cases);
    $readme .= "| $category | $count |\n";
}

$readme .= "\n## Test Patterns\n\n";
$readme .= "- **run()**: Simple class compilation, similar to utilities tests\n";
$readme .= "- **compileCss()**: Full compilation with @theme blocks and configuration\n";

file_put_contents("$summaryDir/README.md", $readme);

echo "\nDone! Check $summaryDir for extracted tests.\n";
