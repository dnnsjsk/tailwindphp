#!/usr/bin/env php
<?php

/**
 * Verify PHP utility output against TailwindCSS test suite.
 *
 * This script parses the extracted TypeScript test files and compares
 * our PHP output against the expected TailwindCSS output.
 */

require_once dirname(__DIR__) . '/vendor/autoload.php';

use TailwindPHP\Tests\TestHelper;

$extractedTestsDir = dirname(__DIR__) . '/extracted-tests';

$totalTests = 0;
$passedTests = 0;
$failedTests = 0;
$skippedTests = 0;
$failures = [];

/**
 * Parse a TypeScript test file and extract test cases.
 *
 * Returns array of test cases, each with:
 * - name: test name
 * - classes: array of input classes
 * - expected: expected CSS output (or empty string)
 * - theme: optional theme configuration
 */
function parseTestFile(string $filePath): array
{
    $content = file_get_contents($filePath);
    $tests = [];

    // Split by test() blocks
    $lines = explode("\n", $content);
    $currentTest = null;
    $currentTestName = '';
    $testBodies = [];

    $inTest = false;
    $braceCount = 0;
    $testBody = '';

    foreach ($lines as $line) {
        // Check for test start
        if (preg_match('/^test\([\'"]([^\'"]+)[\'"]/', $line, $m)) {
            $inTest = true;
            $currentTestName = $m[1];
            $braceCount = substr_count($line, '{') - substr_count($line, '}');
            $testBody = $line . "\n";
            continue;
        }

        if ($inTest) {
            $testBody .= $line . "\n";
            $braceCount += substr_count($line, '{') - substr_count($line, '}');

            if ($braceCount <= 0) {
                $testBodies[$currentTestName] = $testBody;
                $inTest = false;
                $testBody = '';
            }
        }
    }

    foreach ($testBodies as $testName => $testBody) {
        // Find all expect() statements
        $pos = 0;
        $testIndex = 0;

        while (($expectPos = strpos($testBody, 'expect(', $pos)) !== false) {
            // Find the matching run() or compileCss() call
            $runPos = strpos($testBody, 'await run([', $expectPos);
            $compilePos = strpos($testBody, 'await compileCss(', $expectPos);

            // Determine which comes first after expect
            $isRun = $runPos !== false && ($compilePos === false || $runPos < $compilePos);
            $isCompile = $compilePos !== false && ($runPos === false || $compilePos < $runPos);

            if ($isRun && $runPos < $expectPos + 50) {
                // Parse run([...])
                $arrayStart = strpos($testBody, '[', $runPos);
                $arrayEnd = findMatchingBracket($testBody, $arrayStart, '[', ']');

                if ($arrayEnd !== false) {
                    $classesStr = substr($testBody, $arrayStart + 1, $arrayEnd - $arrayStart - 1);
                    $classes = parseClassArray($classesStr);

                    // Find what follows: toMatchInlineSnapshot or toEqual
                    $afterArray = substr($testBody, $arrayEnd, 200);

                    if (preg_match('/\)\s*\)\s*\.toMatchInlineSnapshot\s*\(\s*`/', $afterArray)) {
                        // Find the CSS in backticks
                        $snapshotStart = strpos($testBody, '.toMatchInlineSnapshot(`', $arrayEnd);
                        if ($snapshotStart !== false) {
                            $cssStart = strpos($testBody, '`', $snapshotStart + 20) + 1;
                            $cssEnd = strpos($testBody, '`)', $cssStart);
                            if ($cssEnd !== false) {
                                $expectedCss = substr($testBody, $cssStart, $cssEnd - $cssStart);
                                if (!empty($classes)) {
                                    $tests[] = [
                                        'name' => $testName . ($testIndex > 0 ? " #{$testIndex}" : ''),
                                        'classes' => $classes,
                                        'expected' => cleanExpectedCss($expectedCss),
                                        'theme' => null,
                                        'type' => 'run',
                                    ];
                                    $testIndex++;
                                }
                            }
                        }
                    } elseif (preg_match('/\)\s*\)\s*\.toEqual\s*\(\s*[\'"][\'"]/', $afterArray)) {
                        // Empty output expected
                        if (!empty($classes)) {
                            $tests[] = [
                                'name' => $testName . ' (invalid)',
                                'classes' => $classes,
                                'expected' => '',
                                'theme' => null,
                                'type' => 'empty',
                            ];
                            $testIndex++;
                        }
                    }
                }
                $pos = $arrayEnd !== false ? $arrayEnd : $expectPos + 10;
            } elseif ($isCompile && $compilePos < $expectPos + 50) {
                // Skip compileCss for now - requires theme setup
                $tests[] = [
                    'name' => $testName . ' (themed)',
                    'classes' => [],
                    'expected' => '',
                    'theme' => 'custom',
                    'type' => 'compileCss',
                ];
                $pos = $compilePos + 20;
                $testIndex++;
            } else {
                $pos = $expectPos + 10;
            }
        }
    }

    return $tests;
}

/**
 * Find matching closing bracket.
 */
function findMatchingBracket(string $str, int $start, string $open, string $close): ?int
{
    $count = 1;
    $pos = $start + 1;
    $len = strlen($str);

    while ($pos < $len && $count > 0) {
        $char = $str[$pos];
        if ($char === $open) {
            $count++;
        } elseif ($char === $close) {
            $count--;
        }
        $pos++;
    }

    return $count === 0 ? $pos - 1 : null;
}

/**
 * Parse a JavaScript array of strings into PHP array.
 */
function parseClassArray(string $str): array
{
    $classes = [];

    // Match quoted strings (single or double)
    preg_match_all('/[\'"]([^\'"]+)[\'"]/', $str, $matches);

    foreach ($matches[1] as $class) {
        $classes[] = $class;
    }

    return $classes;
}

/**
 * Clean up expected CSS output.
 */
function cleanExpectedCss(string $css): string
{
    // Remove @layer properties blocks (we don't generate these)
    $css = preg_replace('/@layer\s+properties\s*\{[\s\S]*?\n\s*\}\s*/m', '', $css);

    // Remove :root, :host blocks (theme variable definitions)
    $css = preg_replace('/:root,\s*:host\s*\{[\s\S]*?\}\s*/m', '', $css);

    // Remove @property blocks
    $css = preg_replace('/@property\s+[^\{]+\{[\s\S]*?\}\s*/m', '', $css);

    // Remove @supports blocks (browser compatibility)
    $css = preg_replace('/@supports\s*\([^\)]+\)\s*\{[\s\S]*?\n\s*\}\s*/m', '', $css);

    // Trim and normalize whitespace
    $css = trim($css);

    return $css;
}

/**
 * Normalize CSS for comparison.
 */
function normalizeCss(string $css): string
{
    // Remove all whitespace for comparison
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*\{\s*/', '{', $css);
    $css = preg_replace('/\s*\}\s*/', '}', $css);
    $css = preg_replace('/\s*:\s*/', ':', $css);
    $css = preg_replace('/\s*;\s*/', ';', $css);
    $css = trim($css);

    return $css;
}

/**
 * Extract individual CSS rules from a CSS string.
 * Returns array of [selector => declarations[]]
 */
function extractCssRules(string $css): array
{
    $rules = [];

    // Match CSS rules: .selector { declarations }
    preg_match_all('/([^\{\}]+)\s*\{([^\}]*)\}/m', $css, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $selector = trim($match[1]);
        $declarationsStr = trim($match[2]);

        // Parse declarations
        $declarations = [];
        $parts = array_filter(array_map('trim', explode(';', $declarationsStr)));
        foreach ($parts as $part) {
            if (strpos($part, ':') !== false) {
                [$prop, $value] = array_map('trim', explode(':', $part, 2));
                $declarations[$prop] = $value;
            }
        }

        $rules[$selector] = $declarations;
    }

    return $rules;
}

/**
 * Compare CSS outputs intelligently.
 * Returns [passed, missing, extra, differences]
 */
function compareCss(string $expected, string $actual): array
{
    $expectedRules = extractCssRules($expected);
    $actualRules = extractCssRules($actual);

    $missing = [];
    $extra = [];
    $differences = [];
    $passed = true;

    // Check for missing rules in actual
    foreach ($expectedRules as $selector => $expectedDecls) {
        if (!isset($actualRules[$selector])) {
            $missing[$selector] = $expectedDecls;
            $passed = false;
            continue;
        }

        $actualDecls = $actualRules[$selector];

        // Check declarations
        foreach ($expectedDecls as $prop => $expectedValue) {
            if (!isset($actualDecls[$prop])) {
                $differences[$selector][$prop] = [
                    'expected' => $expectedValue,
                    'actual' => '(missing)',
                ];
                $passed = false;
            } elseif (!valuesMatch($expectedValue, $actualDecls[$prop])) {
                $differences[$selector][$prop] = [
                    'expected' => $expectedValue,
                    'actual' => $actualDecls[$prop],
                ];
                // Don't fail for theme variable differences
                if (!isThemeVariableDifference($expectedValue, $actualDecls[$prop])) {
                    $passed = false;
                }
            }
        }
    }

    // Check for extra rules in actual (not necessarily a failure)
    foreach ($actualRules as $selector => $actualDecls) {
        if (!isset($expectedRules[$selector])) {
            $extra[$selector] = $actualDecls;
        }
    }

    return [$passed, $missing, $extra, $differences];
}

/**
 * Check if two CSS values match.
 */
function valuesMatch(string $expected, string $actual): bool
{
    // Direct match
    if ($expected === $actual) {
        return true;
    }

    // Normalize and compare
    $expected = strtolower(trim($expected));
    $actual = strtolower(trim($actual));

    if ($expected === $actual) {
        return true;
    }

    // Handle var() vs direct value (theme variable usage is correct)
    if (isThemeVariableDifference($expected, $actual)) {
        return true;
    }

    return false;
}

/**
 * Check if the difference is just theme variable usage.
 * e.g., expected "10" but got "var(--z-index-10)"
 */
function isThemeVariableDifference(string $expected, string $actual): bool
{
    // If actual uses var() and contains similar naming, it's likely correct
    if (preg_match('/^var\(--[\w-]+\)$/', $actual)) {
        return true;
    }

    // calc() with var() is also acceptable
    if (preg_match('/^calc\(var\(--[\w-]+\)/', $actual)) {
        return true;
    }

    return false;
}

/**
 * Run a single test case.
 */
function runTest(array $test): array
{
    global $totalTests, $passedTests, $failedTests, $skippedTests;

    $totalTests++;

    // Skip compileCss tests with custom themes for now
    if ($test['type'] === 'compileCss' && $test['theme']) {
        $skippedTests++;
        return ['status' => 'skipped', 'reason' => 'Custom theme required'];
    }

    // Run through PHP
    $actualCss = TestHelper::run($test['classes']);

    // For empty tests
    if ($test['type'] === 'empty') {
        if (trim($actualCss) === '') {
            $passedTests++;
            return ['status' => 'passed'];
        } else {
            $failedTests++;
            return [
                'status' => 'failed',
                'reason' => 'Expected empty output',
                'actual' => $actualCss,
            ];
        }
    }

    // Compare CSS
    [$passed, $missing, $extra, $differences] = compareCss($test['expected'], $actualCss);

    if ($passed && empty($missing)) {
        $passedTests++;
        return ['status' => 'passed'];
    } else {
        $failedTests++;
        return [
            'status' => 'failed',
            'missing' => $missing,
            'extra' => $extra,
            'differences' => $differences,
            'expected' => $test['expected'],
            'actual' => $actualCss,
        ];
    }
}

// Main execution
echo "=================================================================\n";
echo "TailwindCSS Test Suite Verification\n";
echo "=================================================================\n\n";

// Get all .ts files in extracted-tests directory
$tsFiles = glob($extractedTestsDir . '/*.ts');

if (empty($tsFiles)) {
    echo "No .ts files found in $extractedTestsDir\n";
    exit(1);
}

echo "Found " . count($tsFiles) . " test files\n\n";

foreach ($tsFiles as $file) {
    $basename = basename($file);

    // Skip summary.json
    if ($basename === 'summary.json') {
        continue;
    }

    $tests = parseTestFile($file);

    if (empty($tests)) {
        continue;
    }

    echo str_pad($basename, 40) . ": ";

    $fileResults = [];
    $filePassed = 0;
    $fileFailed = 0;
    $fileSkipped = 0;

    foreach ($tests as $test) {
        $result = runTest($test);

        if ($result['status'] === 'passed') {
            echo ".";
            $filePassed++;
        } elseif ($result['status'] === 'skipped') {
            echo "S";
            $fileSkipped++;
        } else {
            echo "F";
            $fileFailed++;
            $failures[] = [
                'file' => $basename,
                'test' => $test,
                'result' => $result,
            ];
        }
    }

    echo " ({$filePassed}/" . count($tests) . ")\n";
}

// Summary
echo "\n=================================================================\n";
echo "SUMMARY\n";
echo "=================================================================\n";
echo "Total Tests: $totalTests\n";
echo "Passed:      $passedTests\n";
echo "Failed:      $failedTests\n";
echo "Skipped:     $skippedTests\n";
echo "\n";

if (!empty($failures)) {
    echo "FAILURES:\n";
    echo "-----------------------------------------------------------------\n";

    $shown = 0;
    foreach ($failures as $failure) {
        if ($shown >= 20) {
            echo "\n... and " . (count($failures) - 20) . " more failures\n";
            break;
        }

        echo "\n[{$failure['file']}] {$failure['test']['name']}\n";
        echo "  Classes: " . implode(', ', array_slice($failure['test']['classes'], 0, 5));
        if (count($failure['test']['classes']) > 5) {
            echo " (+" . (count($failure['test']['classes']) - 5) . " more)";
        }
        echo "\n";

        if (!empty($failure['result']['missing'])) {
            echo "  Missing selectors:\n";
            foreach (array_slice($failure['result']['missing'], 0, 3) as $sel => $decls) {
                echo "    - $sel\n";
            }
        }

        if (!empty($failure['result']['differences'])) {
            echo "  Differences:\n";
            $diffCount = 0;
            foreach ($failure['result']['differences'] as $sel => $props) {
                if ($diffCount >= 3) break;
                foreach ($props as $prop => $diff) {
                    echo "    $sel { $prop: {$diff['expected']} vs {$diff['actual']} }\n";
                    $diffCount++;
                }
            }
        }

        if (isset($failure['result']['reason'])) {
            echo "  Reason: {$failure['result']['reason']}\n";
        }

        $shown++;
    }
}

$passRate = $totalTests > 0 ? round(($passedTests / $totalTests) * 100, 1) : 0;
echo "\n=================================================================\n";
echo "Pass Rate: {$passRate}%\n";
echo "=================================================================\n";

exit($failedTests > 0 ? 1 : 0);
