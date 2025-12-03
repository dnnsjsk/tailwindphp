#!/usr/bin/env php
<?php

/**
 * Extract Typography Plugin Tests
 *
 * Parses the Jest test file from @tailwindcss/typography and extracts
 * test cases into JSON format for PHP testing.
 *
 * Usage: php extract-tests.php
 */

$baseDir = dirname(__DIR__, 2);
$referenceFile = $baseDir . '/reference/tailwindcss-typography/src/index.test.js';
$outputDir = __DIR__ . '/typography/tests';
$summaryFile = __DIR__ . '/typography/summary.json';

if (!file_exists($referenceFile)) {
    echo "Error: Reference file not found: {$referenceFile}\n";
    echo "Make sure the tailwindcss-typography submodule is initialized.\n";
    exit(1);
}

// Read the test file
$content = file_get_contents($referenceFile);

// Extract test cases
$tests = [];
$pattern = '/(?:test|it)\s*\(\s*[\'"`]([^\'"`]+)[\'"`]\s*,\s*(?:async\s*)?\(\s*\)\s*=>\s*\{/';

preg_match_all($pattern, $content, $matches, PREG_OFFSET_CAPTURE);

foreach ($matches[1] as $index => $match) {
    $testName = $match[0];
    $startOffset = $matches[0][$index][1];

    // Find the test body by counting braces
    $bodyStart = strpos($content, '{', $startOffset);
    $braceCount = 1;
    $pos = $bodyStart + 1;
    $len = strlen($content);

    while ($braceCount > 0 && $pos < $len) {
        $char = $content[$pos];
        if ($char === '{') {
            $braceCount++;
        } elseif ($char === '}') {
            $braceCount--;
        } elseif ($char === "'" || $char === '"' || $char === '`') {
            // Skip string contents
            $quote = $char;
            $pos++;
            while ($pos < $len && $content[$pos] !== $quote) {
                if ($content[$pos] === '\\') {
                    $pos++; // Skip escaped character
                }
                $pos++;
            }
        }
        $pos++;
    }

    $testBody = substr($content, $bodyStart, $pos - $bodyStart);

    // Extract config from test body
    $config = extractConfig($testBody);

    // Extract expected CSS from toMatchFormattedCss or toIncludeCss
    $expectedCss = extractExpectedCss($testBody);

    // Extract HTML content
    $htmlContent = extractHtmlContent($testBody);

    // Extract plugin options if any
    $pluginOptions = extractPluginOptions($testBody);

    $tests[] = [
        'name' => $testName,
        'html' => $htmlContent,
        'config' => $config,
        'pluginOptions' => $pluginOptions,
        'expectedCss' => $expectedCss,
    ];
}

// Create output directory if needed
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

// Write individual test files
$validTests = 0;
foreach ($tests as $index => $test) {
    if (empty($test['expectedCss'])) {
        continue;
    }

    $filename = sprintf('%02d-%s.json', $index + 1, slugify($test['name']));
    $filepath = $outputDir . '/' . $filename;

    file_put_contents($filepath, json_encode($test, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    $validTests++;
}

// Write summary
$summary = [
    'source' => 'reference/tailwindcss-typography/src/index.test.js',
    'extracted' => date('Y-m-d H:i:s'),
    'total_tests' => count($tests),
    'valid_tests' => $validTests,
    'skipped' => count($tests) - $validTests,
];

file_put_contents($summaryFile, json_encode($summary, JSON_PRETTY_PRINT));

echo "Extracted {$validTests} tests from typography plugin\n";
echo "Output: {$outputDir}\n";

// Helper functions

function extractConfig(string $testBody): array
{
    // Look for config object in the test
    if (preg_match('/let\s+config\s*=\s*\{/', $testBody, $match, PREG_OFFSET_CAPTURE)) {
        $start = $match[0][1] + strlen('let config = ');
        $config = extractBalancedBraces($testBody, $start);
        return parseJsObject($config);
    }
    return [];
}

function extractExpectedCss(string $testBody): array
{
    $expected = [];

    // Match toMatchFormattedCss
    if (preg_match_all('/toMatchFormattedCss\s*\(\s*css`([^`]+)`/s', $testBody, $matches)) {
        foreach ($matches[1] as $css) {
            $expected[] = [
                'type' => 'match',
                'css' => cleanCss($css),
            ];
        }
    }

    // Match toIncludeCss
    if (preg_match_all('/toIncludeCss\s*\(\s*css`([^`]+)`/s', $testBody, $matches)) {
        foreach ($matches[1] as $css) {
            $expected[] = [
                'type' => 'include',
                'css' => cleanCss($css),
            ];
        }
    }

    return $expected;
}

function extractHtmlContent(string $testBody): string
{
    if (preg_match('/raw:\s*html`([^`]+)`/', $testBody, $match)) {
        return trim($match[1]);
    }
    if (preg_match('/content:\s*\[\s*\{\s*raw:\s*html`([^`]+)`/', $testBody, $match)) {
        return trim($match[1]);
    }
    return '';
}

function extractPluginOptions(string $testBody): array
{
    // Look for typographyPlugin({ ... })
    if (preg_match('/typographyPlugin\s*\(\s*\{([^}]+)\}/', $testBody, $match)) {
        return parseSimpleOptions($match[1]);
    }
    return [];
}

function extractBalancedBraces(string $content, int $start): string
{
    $braceCount = 0;
    $pos = $start;
    $len = strlen($content);
    $result = '';

    while ($pos < $len) {
        $char = $content[$pos];

        if ($char === '{') {
            $braceCount++;
        } elseif ($char === '}') {
            $braceCount--;
            if ($braceCount === 0) {
                $result .= $char;
                break;
            }
        }

        $result .= $char;
        $pos++;
    }

    return $result;
}

function parseJsObject(string $js): array
{
    // Simplified JS object parser - just extract key structure
    // This is a basic implementation; complex nested objects may not parse perfectly
    $result = [];

    // Extract content property
    if (preg_match('/content:\s*\[([^\]]+)\]/', $js, $match)) {
        $result['content'] = trim($match[1]);
    }

    // Extract theme.typography if present
    if (preg_match('/typography:\s*\{/', $js)) {
        $result['hasTypographyTheme'] = true;
    }

    return $result;
}

function parseSimpleOptions(string $options): array
{
    $result = [];

    // Parse simple key: value pairs
    if (preg_match('/className:\s*[\'"]([^\'"]+)[\'"]/', $options, $match)) {
        $result['className'] = $match[1];
    }
    if (preg_match('/target:\s*[\'"]([^\'"]+)[\'"]/', $options, $match)) {
        $result['target'] = $match[1];
    }

    return $result;
}

function cleanCss(string $css): string
{
    // Remove template literal interpolations like ${defaults}
    $css = preg_replace('/\$\{[^}]+\}/', '', $css);

    // Normalize whitespace but preserve CSS structure
    $css = preg_replace('/\s+/', ' ', $css);
    $css = preg_replace('/\s*\{\s*/', ' { ', $css);
    $css = preg_replace('/\s*\}\s*/', ' } ', $css);
    $css = preg_replace('/\s*;\s*/', '; ', $css);

    // Only add space after colons for property values, not pseudo-selectors
    // Match colon followed by space and a value (not a pseudo-selector like :where, :not, ::before)
    $css = preg_replace('/:\s+(?!:)/', ': ', $css);

    // Remove any accidental double spaces
    $css = preg_replace('/  +/', ' ', $css);

    return trim($css);
}

function slugify(string $text): string
{
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9]+/', '-', $text);
    $text = trim($text, '-');
    return substr($text, 0, 50);
}
