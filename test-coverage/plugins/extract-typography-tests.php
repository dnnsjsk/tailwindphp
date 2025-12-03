#!/usr/bin/env php
<?php

/**
 * Extract Typography Plugin Tests
 *
 * Parses the Jest test file from @tailwindcss/typography and extracts
 * test cases into JSON format for PHP testing.
 *
 * Includes full theme.typography config extraction for 1:1 test parity.
 *
 * Usage: php extract-typography-tests.php
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

    // Extract config from test body (includes full theme.typography)
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

// Clear existing files
foreach (glob($outputDir . '/*.json') as $file) {
    unlink($file);
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
    if (!preg_match('/let\s+config\s*=\s*\{/', $testBody, $match, PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $start = $match[0][1] + strlen('let config = ');
    $configStr = extractBalancedBraces($testBody, $start);

    $result = [];

    // Extract content property
    if (preg_match('/content:\s*\[([^\]]+)\]/', $configStr, $m)) {
        $result['content'] = trim($m[1]);
    }

    // Extract full theme.typography config
    $typographyConfig = extractTypographyTheme($configStr);
    if (!empty($typographyConfig)) {
        $result['typography'] = $typographyConfig;
    }

    // Extract plugin options from plugins array
    if (preg_match('/plugins:\s*\[\s*typographyPlugin\s*\(\s*\{([^}]+)\}/', $configStr, $m)) {
        $result['pluginOptionsFromConfig'] = parseSimpleOptions($m[1]);
    }

    // Extract darkMode setting
    if (preg_match('/darkMode:\s*[\'"]([^\'"]+)[\'"]/', $configStr, $m)) {
        $result['darkMode'] = $m[1];
    }

    return $result;
}

function extractTypographyTheme(string $configStr): array
{
    // Find theme: { typography: { ... } }
    if (!preg_match('/theme:\s*\{/', $configStr, $match, PREG_OFFSET_CAPTURE)) {
        return [];
    }

    $themeStart = $match[0][1] + strlen('theme: ');
    $themeStr = extractBalancedBraces($configStr, $themeStart);

    // Now find typography within theme
    // Handle both theme.typography and theme.extend.typography
    $typography = [];

    // Check for theme.extend.typography
    if (preg_match('/extend:\s*\{/', $themeStr, $m, PREG_OFFSET_CAPTURE)) {
        $extendStart = $m[0][1] + strlen('extend: ');
        $extendStr = extractBalancedBraces($themeStr, $extendStart);

        if (preg_match('/typography:\s*\{/', $extendStr, $m2, PREG_OFFSET_CAPTURE)) {
            $typoStart = $m2[0][1] + strlen('typography: ');
            $typoStr = extractBalancedBraces($extendStr, $typoStart);
            $typography = parseTypographyObject($typoStr);
            $typography['_isExtend'] = true;
        }
    }

    // Check for direct theme.typography (takes precedence over extend)
    if (preg_match('/typography:\s*\{/', $themeStr, $m, PREG_OFFSET_CAPTURE)) {
        // Make sure it's not inside extend
        $typoPos = $m[0][1];
        $extendPos = strpos($themeStr, 'extend:');
        if ($extendPos === false || $typoPos < $extendPos) {
            $typoStart = $m[0][1] + strlen('typography: ');
            $typoStr = extractBalancedBraces($themeStr, $typoStart);
            $typography = parseTypographyObject($typoStr);
        }
    }

    return $typography;
}

function parseTypographyObject(string $typoStr): array
{
    $result = [];

    // Find all modifier blocks (DEFAULT, lg, xl, etc.)
    // Pattern: modifier: { css: [...] } or modifier: { css: {...} }
    $pattern = '/([A-Za-z]+):\s*\{/';
    preg_match_all($pattern, $typoStr, $modifierMatches, PREG_OFFSET_CAPTURE);

    foreach ($modifierMatches[1] as $i => $modifierMatch) {
        $modifier = $modifierMatch[0];
        $modifierStart = $modifierMatches[0][$i][1] + strlen($modifierMatches[0][$i][0]);

        // Find the closing brace for this modifier block
        $modifierBlockStr = extractBalancedBraces($typoStr, $modifierStart - 1);

        // Extract the css property
        if (preg_match('/css:\s*(\[|\{)/', $modifierBlockStr, $cssMatch, PREG_OFFSET_CAPTURE)) {
            $isArray = $cssMatch[1][0] === '[';
            $cssStart = $cssMatch[0][1] + strlen('css: ');

            if ($isArray) {
                $cssStr = extractBalancedBrackets($modifierBlockStr, $cssStart);
                $cssRules = parseCssArray($cssStr);
            } else {
                $cssStr = extractBalancedBraces($modifierBlockStr, $cssStart);
                $cssRules = [parseCssObject($cssStr)];
            }

            $result[$modifier] = ['css' => $cssRules];
        }
    }

    return $result;
}

function parseCssArray(string $arrayStr): array
{
    $result = [];

    // Find all objects in the array
    $braceLevel = 0;
    $currentObjectStart = -1;

    for ($i = 0; $i < strlen($arrayStr); $i++) {
        $char = $arrayStr[$i];

        if ($char === '{') {
            if ($braceLevel === 0) {
                $currentObjectStart = $i;
            }
            $braceLevel++;
        } elseif ($char === '}') {
            $braceLevel--;
            if ($braceLevel === 0 && $currentObjectStart !== -1) {
                $objectStr = substr($arrayStr, $currentObjectStart, $i - $currentObjectStart + 1);
                $result[] = parseCssObject($objectStr);
                $currentObjectStart = -1;
            }
        }
    }

    return $result;
}

function parseCssObject(string $objectStr): array
{
    $result = [];

    // Remove outer braces
    $objectStr = trim($objectStr);
    if (str_starts_with($objectStr, '{')) {
        $objectStr = substr($objectStr, 1);
    }
    if (str_ends_with($objectStr, '}')) {
        $objectStr = substr($objectStr, 0, -1);
    }

    // Parse property: value pairs
    // Handle nested objects for selectors like 'strong': { ... }
    $pos = 0;
    $len = strlen($objectStr);

    while ($pos < $len) {
        // Skip whitespace and commas
        while ($pos < $len && preg_match('/[\s,]/', $objectStr[$pos])) {
            $pos++;
        }

        if ($pos >= $len) {
            break;
        }

        // Find property name
        $propertyStart = $pos;
        $propertyEnd = $pos;

        // Handle quoted property names like '[class~="lead"]'
        if ($objectStr[$pos] === "'" || $objectStr[$pos] === '"') {
            $quote = $objectStr[$pos];
            $pos++;
            while ($pos < $len && $objectStr[$pos] !== $quote) {
                if ($objectStr[$pos] === '\\') {
                    $pos++;
                }
                $pos++;
            }
            $propertyEnd = $pos;
            $pos++; // Skip closing quote
            $property = substr($objectStr, $propertyStart + 1, $propertyEnd - $propertyStart - 1);
        } else {
            // Unquoted property
            while ($pos < $len && !preg_match('/[\s:,}]/', $objectStr[$pos])) {
                $pos++;
            }
            $propertyEnd = $pos;
            $property = substr($objectStr, $propertyStart, $propertyEnd - $propertyStart);
        }

        // Skip to colon
        while ($pos < $len && $objectStr[$pos] !== ':') {
            $pos++;
        }
        $pos++; // Skip colon

        // Skip whitespace
        while ($pos < $len && preg_match('/\s/', $objectStr[$pos])) {
            $pos++;
        }

        if ($pos >= $len) {
            break;
        }

        // Parse value
        if ($objectStr[$pos] === '{') {
            // Nested object (selector with CSS)
            $valueStr = extractBalancedBraces($objectStr, $pos);
            $value = parseCssObject($valueStr);
            $pos += strlen($valueStr);
        } elseif ($objectStr[$pos] === '[') {
            // Array value (for multiple values like textAlign)
            $valueStr = extractBalancedBrackets($objectStr, $pos);
            $value = parseArrayValue($valueStr);
            $pos += strlen($valueStr);
        } elseif ($objectStr[$pos] === "'" || $objectStr[$pos] === '"') {
            // String value
            $quote = $objectStr[$pos];
            $pos++;
            $valueStart = $pos;
            while ($pos < $len && $objectStr[$pos] !== $quote) {
                if ($objectStr[$pos] === '\\') {
                    $pos++;
                }
                $pos++;
            }
            $value = substr($objectStr, $valueStart, $pos - $valueStart);
            $pos++; // Skip closing quote
        } else {
            // Numeric or keyword value
            $valueStart = $pos;
            while ($pos < $len && !preg_match('/[,}]/', $objectStr[$pos])) {
                $pos++;
            }
            $value = trim(substr($objectStr, $valueStart, $pos - $valueStart));

            // Convert numeric strings to numbers
            if (is_numeric($value)) {
                $value = strpos($value, '.') !== false ? (float) $value : (int) $value;
            }
        }

        if (!empty($property)) {
            $result[$property] = $value;
        }
    }

    return $result;
}

function parseArrayValue(string $arrayStr): array
{
    $result = [];

    // Remove outer brackets
    $arrayStr = trim($arrayStr);
    if (str_starts_with($arrayStr, '[')) {
        $arrayStr = substr($arrayStr, 1);
    }
    if (str_ends_with($arrayStr, ']')) {
        $arrayStr = substr($arrayStr, 0, -1);
    }

    // Split by comma, handling quoted strings
    $pos = 0;
    $len = strlen($arrayStr);
    $valueStart = 0;

    while ($pos <= $len) {
        if ($pos === $len || $arrayStr[$pos] === ',') {
            $value = trim(substr($arrayStr, $valueStart, $pos - $valueStart));
            if (!empty($value)) {
                // Remove quotes
                if (preg_match('/^[\'"](.+)[\'"]$/', $value, $m)) {
                    $value = $m[1];
                }
                $result[] = $value;
            }
            $valueStart = $pos + 1;
        } elseif ($arrayStr[$pos] === "'" || $arrayStr[$pos] === '"') {
            // Skip quoted string
            $quote = $arrayStr[$pos];
            $pos++;
            while ($pos < $len && $arrayStr[$pos] !== $quote) {
                if ($arrayStr[$pos] === '\\') {
                    $pos++;
                }
                $pos++;
            }
        }
        $pos++;
    }

    return $result;
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
    // Look for typographyPlugin({ ... }) - but not in plugins array
    // This handles the top-level plugins array
    if (preg_match('/plugins:\s*\[\s*typographyPlugin\s*\(\s*\{([^}]+)\}\s*\)\s*\]/', $testBody, $match)) {
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
        } elseif ($char === "'" || $char === '"' || $char === '`') {
            // Handle strings
            $quote = $char;
            $result .= $char;
            $pos++;
            while ($pos < $len && $content[$pos] !== $quote) {
                if ($content[$pos] === '\\') {
                    $result .= $content[$pos];
                    $pos++;
                }
                if ($pos < $len) {
                    $result .= $content[$pos];
                    $pos++;
                }
            }
            if ($pos < $len) {
                $result .= $content[$pos];
            }
            $pos++;
            continue;
        }

        $result .= $char;
        $pos++;
    }

    return $result;
}

function extractBalancedBrackets(string $content, int $start): string
{
    $bracketCount = 0;
    $pos = $start;
    $len = strlen($content);
    $result = '';

    while ($pos < $len) {
        $char = $content[$pos];

        if ($char === '[') {
            $bracketCount++;
        } elseif ($char === ']') {
            $bracketCount--;
            if ($bracketCount === 0) {
                $result .= $char;
                break;
            }
        } elseif ($char === "'" || $char === '"') {
            // Handle strings
            $quote = $char;
            $result .= $char;
            $pos++;
            while ($pos < $len && $content[$pos] !== $quote) {
                if ($content[$pos] === '\\') {
                    $result .= $content[$pos];
                    $pos++;
                }
                if ($pos < $len) {
                    $result .= $content[$pos];
                    $pos++;
                }
            }
            if ($pos < $len) {
                $result .= $content[$pos];
            }
            $pos++;
            continue;
        }

        $result .= $char;
        $pos++;
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
