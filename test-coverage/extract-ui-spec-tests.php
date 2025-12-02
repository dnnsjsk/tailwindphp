#!/usr/bin/env php
<?php

/**
 * Extract UI spec tests from ui.spec.ts
 *
 * These are Playwright browser tests that check computed CSS values.
 * We extract the test data to verify our PHP implementation generates
 * CSS that would produce the expected computed values.
 *
 * Test patterns:
 * 1. for() loops with [classes, expected] arrays generating multiple tests
 * 2. Standalone test() blocks with HTML and expected property values
 *
 * Usage: php extract-ui-spec-tests.php
 */

$baseDir = dirname(__DIR__);
$inputFile = $baseDir . '/reference/tailwindcss/packages/tailwindcss/tests/ui.spec.ts';
$outputDir = $baseDir . '/test-coverage/ui-spec/tests';

if (!file_exists($inputFile)) {
    echo "Error: ui.spec.ts not found at: $inputFile\n";
    exit(1);
}

// Create output directory
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$content = file_get_contents($inputFile);
$lines = explode("\n", $content);
$totalLines = count($lines);

echo "Processing $totalLines lines from ui.spec.ts\n";

$tests = [];

// =============================================================================
// Pattern 1: Extract for() loops with test data arrays
// Format: for (let [classes, expected] of [ ...array... ]) { test(`...`) }
// =============================================================================

// Find all for loops
$forPattern = '/for\s*\(\s*let\s+\[classes,\s*expected\]\s+of\s+\[/s';
preg_match_all($forPattern, $content, $forMatches, PREG_OFFSET_CAPTURE);

echo "Found " . count($forMatches[0]) . " for loops with test data\n";

foreach ($forMatches[0] as $matchIndex => $match) {
    $forStartPos = $match[1];

    // Find the end of the for loop's array (closing ] before the { of for body)
    $arrayStart = $forStartPos + strlen($match[0]) - 1; // Position of opening [
    $arrayContent = extractArrayAtPosition($content, $arrayStart);

    if ($arrayContent === null) {
        echo "  Warning: Could not extract array for for-loop at position $forStartPos\n";
        continue;
    }

    // Find the test() call after the for loop array
    $afterArray = substr($content, $arrayStart + strlen($arrayContent) + 2, 500);

    // Get test name template
    $testNameTemplate = 'unknown';
    if (preg_match('/test\s*\(\s*`([^`]+)`/', $afterArray, $nameMatch)) {
        $testNameTemplate = $nameMatch[1];
    }

    // Get property being tested
    $property = 'unknown';
    if (preg_match("/getPropertyValue\s*\([^,]+,\s*['\"]([^'\"]+)['\"]\s*\)/", $afterArray, $propMatch)) {
        $property = $propMatch[1];
    }

    // Parse the test data pairs from the array
    $pairs = parseForLoopArray($arrayContent);

    echo "  For loop " . ($matchIndex + 1) . ": template='$testNameTemplate', property='$property', " . count($pairs) . " pairs\n";

    foreach ($pairs as $pair) {
        $classes = $pair['classes'];
        $expected = $pair['expected'];

        // Generate test name from template
        $testName = str_replace('${classes}', $classes, $testNameTemplate);

        $tests[] = [
            'name' => $testName,
            'type' => 'for-loop',
            'classes' => preg_split('/\s+/', $classes),
            'property' => $property,
            'expected' => $expected,
        ];
    }
}

// =============================================================================
// Pattern 2: Extract standalone test() blocks
// =============================================================================

$testPattern = '/test\(\s*[\'"]([^\'"]+)[\'"]\s*,\s*async/';
preg_match_all($testPattern, $content, $testMatches, PREG_OFFSET_CAPTURE);

echo "\nFound " . count($testMatches[0]) . " standalone test() calls\n";

foreach ($testMatches[0] as $i => $match) {
    $testName = $testMatches[1][$i][0];
    $startPos = $match[1];

    // Skip tests that are inside for loops (their names contain ${})
    if (str_contains($testName, '${')) continue;

    // Skip test.skip
    if (str_contains(substr($content, max(0, $startPos - 5), 10), '.skip')) continue;

    // Extract test body
    $testBody = extractTestBody(substr($content, $startPos, 8000));
    if ($testBody === null) continue;

    // Find HTML content with classes
    $classes = extractClassesFromHtml($testBody);
    if (empty($classes)) continue;

    // Find expected property values
    $expectations = extractExpectations($testBody);
    if (empty($expectations)) continue;

    $tests[] = [
        'name' => $testName,
        'type' => 'standalone',
        'classes' => $classes,
        'expectations' => $expectations,
    ];
}

/**
 * Extract array content starting at a specific position
 */
function extractArrayAtPosition(string $content, int $start): ?string
{
    if ($content[$start] !== '[') {
        return null;
    }

    $depth = 0;
    $len = strlen($content);
    $inString = false;
    $stringChar = '';
    $inComment = false;
    $inLineComment = false;

    for ($i = $start; $i < $len; $i++) {
        $char = $content[$i];
        $nextChar = $i + 1 < $len ? $content[$i + 1] : '';

        // Handle comments
        if (!$inString && !$inComment && !$inLineComment) {
            if ($char === '/' && $nextChar === '/') {
                $inLineComment = true;
                continue;
            }
            if ($char === '/' && $nextChar === '*') {
                $inComment = true;
                $i++;
                continue;
            }
        }

        if ($inLineComment && $char === "\n") {
            $inLineComment = false;
            continue;
        }

        if ($inComment && $char === '*' && $nextChar === '/') {
            $inComment = false;
            $i++;
            continue;
        }

        if ($inComment || $inLineComment) continue;

        // Handle strings
        if (!$inString && ($char === '"' || $char === "'" || $char === '`')) {
            $inString = true;
            $stringChar = $char;
            continue;
        }

        if ($inString) {
            if ($char === '\\') {
                $i++; // Skip escaped char
                continue;
            }
            if ($char === $stringChar) {
                $inString = false;
            }
            continue;
        }

        // Handle brackets
        if ($char === '[') {
            $depth++;
        } elseif ($char === ']') {
            $depth--;
            if ($depth === 0) {
                return substr($content, $start + 1, $i - $start - 1);
            }
        }
    }

    return null;
}

/**
 * Parse for loop array into [classes, expected] pairs
 */
function parseForLoopArray(string $arrayContent): array
{
    $pairs = [];

    // Find each top-level [ ] pair in the array
    $pos = 0;
    $len = strlen($arrayContent);

    while ($pos < $len) {
        // Skip whitespace and comments
        while ($pos < $len && (ctype_space($arrayContent[$pos]) || $arrayContent[$pos] === ',')) {
            $pos++;
        }

        if ($pos >= $len) break;

        // We should be at a [
        if ($arrayContent[$pos] !== '[') {
            $pos++;
            continue;
        }

        // Extract this pair
        $pairContent = extractArrayAtPosition($arrayContent, $pos);
        if ($pairContent === null) {
            $pos++;
            continue;
        }

        // Parse the pair content: [classes, expected]
        $parsed = parsePairContent($pairContent);
        if ($parsed !== null) {
            $pairs[] = $parsed;
        }

        $pos += strlen($pairContent) + 2; // +2 for the [ ]
    }

    return $pairs;
}

/**
 * Parse a single pair [classes, expected]
 */
function parsePairContent(string $content): ?array
{
    // Find the first string (classes)
    $classesMatch = null;
    if (preg_match("/^\\s*['\"`]([^'\"`]+)['\"`]/s", $content, $m)) {
        $classesMatch = $m[1];
        $afterClasses = substr($content, strlen($m[0]));
    } else {
        return null;
    }

    // Find the comma and then the expected value
    $commaPos = strpos($afterClasses, ',');
    if ($commaPos === false) return null;

    $expectedPart = trim(substr($afterClasses, $commaPos + 1));

    // Expected can be a string or an array
    $expected = null;

    // Check if it's an array (multiple valid values)
    if (str_starts_with($expectedPart, '[')) {
        $arrayContent = extractArrayAtPosition($expectedPart, 0);
        if ($arrayContent !== null) {
            // Parse array of strings
            preg_match_all("/['\"`]([^'\"`]+)['\"`]/", $arrayContent, $matches);
            $expected = $matches[1];
        }
    } else {
        // It's a string
        if (preg_match("/^['\"`]([^'\"`]+)['\"`]/s", $expectedPart, $m)) {
            $expected = $m[1];
        } elseif (preg_match("/^`([^`]+)`/s", $expectedPart, $m)) {
            $expected = $m[1];
        }
    }

    if ($expected === null) return null;

    return [
        'classes' => $classesMatch,
        'expected' => $expected,
    ];
}

/**
 * Extract test body from test() call
 */
function extractTestBody(string $str): ?string
{
    $start = strpos($str, '=>');
    if ($start === false) return null;

    $braceStart = strpos($str, '{', $start);
    if ($braceStart === false) return null;

    $depth = 0;
    $len = strlen($str);
    $inString = false;
    $stringChar = '';

    for ($i = $braceStart; $i < $len; $i++) {
        $char = $str[$i];

        if (!$inString && ($char === '"' || $char === "'" || $char === '`')) {
            $inString = true;
            $stringChar = $char;
            continue;
        }

        if ($inString) {
            if ($char === '\\') {
                $i++;
                continue;
            }
            if ($char === $stringChar) {
                $inString = false;
            }
            continue;
        }

        if ($char === '{') {
            $depth++;
        } elseif ($char === '}') {
            $depth--;
            if ($depth === 0) {
                return substr($str, $braceStart, $i - $braceStart + 1);
            }
        }
    }

    return null;
}

/**
 * Extract classes from HTML in test body
 */
function extractClassesFromHtml(string $body): array
{
    $classes = [];

    // Match class="..." (including template literals with ${classes})
    if (preg_match_all('/class=(?:"([^"]+)"|`([^`]+)`)/', $body, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $classStr = $match[1] ?: $match[2];
            // Skip if it's just a variable
            if ($classStr === '${classes}') continue;

            // Handle interpolation - extract static parts
            $classStr = preg_replace('/\$\{[^}]+\}/', ' ', $classStr);

            $classList = preg_split('/\s+/', $classStr);
            foreach ($classList as $class) {
                $class = trim($class);
                if ($class !== '' && !str_starts_with($class, '$')) {
                    $classes[] = $class;
                }
            }
        }
    }

    return array_unique(array_values($classes));
}

/**
 * Extract expectations from test body
 */
function extractExpectations(string $body): array
{
    $expectations = [];

    // Pattern: expect(await getPropertyValue(..., 'property')).toEqual('value')
    if (preg_match_all("/expect\s*\(\s*await\s+getPropertyValue\s*\([^,]+,\s*['\"]([^'\"]+)['\"]\s*\)\s*\)\.toEqual\s*\(\s*['\"]([^'\"]+)['\"]\s*\)/s", $body, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $expectations[] = [
                'property' => $match[1],
                'value' => $match[2],
            ];
        }
    }

    // Also handle the inline toEqual pattern
    if (preg_match_all("/getPropertyValue\s*\([^,]+,\s*['\"]([^'\"]+)['\"]\s*\)\s*\)\.toEqual\s*\(\s*['\"`]([^'\"`]+)['\"`]\s*\)/s", $body, $matches, PREG_SET_ORDER)) {
        foreach ($matches as $match) {
            $exists = false;
            foreach ($expectations as $exp) {
                if ($exp['property'] === $match[1] && $exp['value'] === $match[2]) {
                    $exists = true;
                    break;
                }
            }
            if (!$exists) {
                $expectations[] = [
                    'property' => $match[1],
                    'value' => $match[2],
                ];
            }
        }
    }

    return $expectations;
}

echo "\nExtracted " . count($tests) . " total test cases\n\n";

// Categorize tests
$categories = [];
foreach ($tests as $test) {
    $category = 'general';
    $name = strtolower($test['name']);

    if (str_contains($name, 'gradient') || str_contains($name, 'linear') || str_contains($name, 'radial') || str_contains($name, 'conic')) {
        $category = 'gradients';
    } elseif (str_contains($name, 'mask')) {
        $category = 'masks';
    } elseif (str_contains($name, 'shadow')) {
        $category = 'shadows';
    } elseif (str_contains($name, 'filter') || str_contains($name, 'drop')) {
        $category = 'filters';
    } elseif (str_contains($name, 'border') || str_contains($name, 'outline') || str_contains($name, 'divider')) {
        $category = 'borders';
    } elseif (str_contains($name, 'transition') || str_contains($name, 'duration') || str_contains($name, 'ease')) {
        $category = 'transitions';
    } elseif (str_contains($name, 'font') || str_contains($name, 'text') || str_contains($name, 'leading') || str_contains($name, 'tracking')) {
        $category = 'typography';
    } elseif (str_contains($name, 'touch') || str_contains($name, 'scale') || str_contains($name, 'content')) {
        $category = 'misc';
    }

    if (!isset($categories[$category])) {
        $categories[$category] = [];
    }
    $categories[$category][] = $test;
}

// Write category files
foreach ($categories as $category => $cases) {
    $filename = "$outputDir/$category.json";
    file_put_contents($filename, json_encode($cases, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    echo "Wrote $category.json (" . count($cases) . " cases)\n";
}

// Write summary
$summaryDir = dirname($outputDir);
$summary = [
    'sourceFile' => 'tailwindcss/packages/tailwindcss/tests/ui.spec.ts',
    'sourceLines' => $totalLines,
    'totalTests' => count($tests),
    'forLoopTests' => count(array_filter($tests, fn($t) => $t['type'] === 'for-loop')),
    'standaloneTests' => count(array_filter($tests, fn($t) => $t['type'] === 'standalone')),
    'categories' => array_map(fn($c) => count($c), $categories),
];

file_put_contents("$summaryDir/summary.json", json_encode($summary, JSON_PRETTY_PRINT));

echo "\nTotal: " . count($tests) . " test cases extracted\n";
echo "  For-loop tests: " . $summary['forLoopTests'] . "\n";
echo "  Standalone tests: " . $summary['standaloneTests'] . "\n";
echo "\nDone! Check $summaryDir for extracted tests.\n";
