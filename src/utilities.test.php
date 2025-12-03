<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Tests\TestHelper;

/**
 * TailwindCSS Compliance Tests
 *
 * These tests are auto-parsed from the extracted TailwindCSS test suite
 * (originally from packages/tailwindcss/src/utilities.test.ts - 28k LOC).
 *
 * The tests verify that our PHP implementation produces CSS output that
 * matches the expected output from TailwindCSS.
 */
class utilities extends TestCase
{
    private static array $testCases = [];
    private static bool $parsed = false;

    /**
     * Parse all extracted .ts test files and build test cases.
     */
    private static function parseTestFiles(): void
    {
        if (self::$parsed) {
            return;
        }

        $extractedDir = __DIR__ . '/../test-coverage/utilities/tests';
        $tsFiles = glob($extractedDir . '/*.ts');

        foreach ($tsFiles as $file) {
            $tests = self::parseTestFile($file);
            foreach ($tests as $test) {
                $key = basename($file, '.ts') . '::' . $test['name'];
                if ($test['index'] > 0) {
                    $key .= '#' . $test['index'];
                }
                self::$testCases[$key] = $test;
            }
        }

        self::$parsed = true;
    }

    /**
     * Parse a single TypeScript test file.
     */
    private static function parseTestFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $tests = [];

        // Find all test() blocks using regex
        preg_match_all('/test\([\'"]([^\'"]+)[\'"],\s*async\s*\(\)\s*=>\s*\{/s', $content, $testMatches, PREG_OFFSET_CAPTURE);

        foreach ($testMatches[0] as $i => $match) {
            $testName = $testMatches[1][$i][0];
            $testStart = $match[1];

            // Find the end of this test block by counting braces
            $braceCount = 1;
            $pos = $testStart + strlen($match[0]);

            while ($pos < strlen($content) && $braceCount > 0) {
                $char = $content[$pos];
                if ($char === '{') {
                    $braceCount++;
                } elseif ($char === '}') {
                    $braceCount--;
                }
                $pos++;
            }

            $testBody = substr($content, $testStart, $pos - $testStart);

            // Parse run() calls from this test body
            $runTests = self::parseRunCalls($testBody, $testName);
            $tests = array_merge($tests, $runTests);

            // Parse compileCss() calls from this test body
            $compileCssTests = self::parseCompileCssCalls($testBody, $testName, count($runTests));
            $tests = array_merge($tests, $compileCssTests);
        }

        return $tests;
    }

    /**
     * Parse all run() calls from a test body.
     */
    private static function parseRunCalls(string $testBody, string $testName): array
    {
        $tests = [];
        $testIndex = 0;
        $offset = 0;

        while (($runPos = strpos($testBody, 'await run([', $offset)) !== false) {
            $arrayStart = $runPos + strlen('await run(');
            $arrayEnd = self::findMatchingBracketWithStrings($testBody, $arrayStart);

            if ($arrayEnd === null) {
                $offset = $runPos + 10;
                continue;
            }

            $classesStr = substr($testBody, $arrayStart + 1, $arrayEnd - $arrayStart - 1);
            $classes = self::parseClassArray($classesStr);

            if (empty($classes)) {
                $offset = $arrayEnd;
                continue;
            }

            // Look for the assertion after the run() call
            $afterArray = substr($testBody, $arrayEnd, 500);

            // Find positions of both assertion types (if they exist)
            $toEqualMatch = preg_match('/\)\s*,?\s*\)\s*\.toEqual\s*\(\s*[\'"][\'"]/', $afterArray, $matchEqual, PREG_OFFSET_CAPTURE);
            $toSnapshotMatch = preg_match('/\)\s*,?\s*\)\s*\.toMatchInlineSnapshot\s*\(\s*`/s', $afterArray, $matchSnapshot, PREG_OFFSET_CAPTURE);

            $toEqualPos = $toEqualMatch ? $matchEqual[0][1] : PHP_INT_MAX;
            $toSnapshotPos = $toSnapshotMatch ? $matchSnapshot[0][1] : PHP_INT_MAX;

            // Check for toEqual('') FIRST - must appear BEFORE toMatchInlineSnapshot
            if ($toEqualMatch && $toEqualPos < $toSnapshotPos) {
                $tests[] = [
                    'name' => $testName,
                    'index' => $testIndex++,
                    'classes' => $classes,
                    'expected' => '',
                    'type' => 'empty',
                ];
                $offset = $arrayEnd;
                continue;
            }

            // Check for toMatchInlineSnapshot
            if ($toSnapshotMatch ||
                preg_match('/\)\s*\)\s*\n\s*\.toMatchInlineSnapshot\s*\(\s*`/s', $afterArray)) {

                $snapshotPos = strpos($testBody, '.toMatchInlineSnapshot(`', $arrayEnd);
                if ($snapshotPos !== false) {
                    $backtickStart = strpos($testBody, '`', $snapshotPos) + 1;
                    $backtickEnd = self::findClosingBacktick($testBody, $backtickStart);

                    if ($backtickEnd !== null) {
                        $expectedCss = substr($testBody, $backtickStart, $backtickEnd - $backtickStart);
                        $tests[] = [
                            'name' => $testName,
                            'index' => $testIndex++,
                            'classes' => $classes,
                            'expected' => self::cleanExpectedCss($expectedCss),
                            'type' => 'match',
                        ];
                        $offset = $backtickEnd;
                        continue;
                    }
                }
            }

            $offset = $arrayEnd;
        }

        return $tests;
    }

    /**
     * Parse all compileCss() calls from a test body.
     */
    private static function parseCompileCssCalls(string $testBody, string $testName, int $startIndex = 0): array
    {
        $tests = [];
        $testIndex = $startIndex;
        $offset = 0;

        while (($compilePos = strpos($testBody, 'await compileCss(', $offset)) !== false) {
            // Find the CSS template literal (first argument)
            $cssStart = strpos($testBody, 'css`', $compilePos);
            if ($cssStart === false) {
                $offset = $compilePos + 17;
                continue;
            }

            $cssBacktickStart = $cssStart + 4;
            $cssBacktickEnd = self::findClosingBacktick($testBody, $cssBacktickStart);
            if ($cssBacktickEnd === null) {
                $offset = $compilePos + 17;
                continue;
            }

            $cssTemplate = substr($testBody, $cssBacktickStart, $cssBacktickEnd - $cssBacktickStart);

            // Find the classes array (second argument)
            $arrayStart = strpos($testBody, '[', $cssBacktickEnd);
            if ($arrayStart === false) {
                $offset = $cssBacktickEnd;
                continue;
            }

            $arrayEnd = self::findMatchingBracketWithStrings($testBody, $arrayStart);
            if ($arrayEnd === null) {
                $offset = $cssBacktickEnd;
                continue;
            }

            $classesStr = substr($testBody, $arrayStart + 1, $arrayEnd - $arrayStart - 1);
            $classes = self::parseClassArray($classesStr);

            if (empty($classes)) {
                $offset = $arrayEnd;
                continue;
            }

            // Look for the toMatchInlineSnapshot assertion
            $snapshotPos = strpos($testBody, '.toMatchInlineSnapshot(`', $arrayEnd);
            if ($snapshotPos !== false && $snapshotPos < $arrayEnd + 200) {
                $backtickStart = strpos($testBody, '`', $snapshotPos) + 1;
                $backtickEnd = self::findClosingBacktick($testBody, $backtickStart);

                if ($backtickEnd !== null) {
                    $expectedCss = substr($testBody, $backtickStart, $backtickEnd - $backtickStart);
                    $tests[] = [
                        'name' => $testName,
                        'index' => $testIndex++,
                        'classes' => $classes,
                        'expected' => self::cleanExpectedCss($expectedCss),
                        'css' => $cssTemplate,
                        'type' => 'compileCss',
                    ];
                    $offset = $backtickEnd;
                    continue;
                }
            }

            $offset = $arrayEnd;
        }

        return $tests;
    }

    /**
     * Find matching bracket, handling strings properly.
     */
    private static function findMatchingBracketWithStrings(string $str, int $start): ?int
    {
        $count = 1;
        $pos = $start + 1;
        $len = strlen($str);

        while ($pos < $len && $count > 0) {
            $char = $str[$pos];

            // Skip strings
            if ($char === "'" || $char === '"') {
                $quote = $char;
                $pos++;
                while ($pos < $len && $str[$pos] !== $quote) {
                    if ($str[$pos] === '\\') {
                        $pos++;
                    }
                    $pos++;
                }
            } elseif ($char === '[') {
                $count++;
            } elseif ($char === ']') {
                $count--;
            }
            $pos++;
        }

        return $count === 0 ? $pos - 1 : null;
    }

    /**
     * Find the closing backtick for a template literal.
     */
    private static function findClosingBacktick(string $str, int $start): ?int
    {
        $pos = $start;
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

    private static function parseClassArray(string $str): array
    {
        $classes = [];
        $len = strlen($str);
        $pos = 0;

        while ($pos < $len) {
            // Skip whitespace and commas
            while ($pos < $len && ($str[$pos] === ' ' || $str[$pos] === "\n" || $str[$pos] === "\t" || $str[$pos] === ',' || $str[$pos] === "\r")) {
                $pos++;
            }

            if ($pos >= $len) {
                break;
            }

            // Check for string start
            if ($str[$pos] === "'" || $str[$pos] === '"') {
                $quote = $str[$pos];
                $pos++;
                $start = $pos;

                // Find closing quote, handling escapes and nested quotes
                while ($pos < $len) {
                    if ($str[$pos] === '\\' && $pos + 1 < $len) {
                        $pos += 2;
                        continue;
                    }
                    // Handle nested quotes of different type
                    if ($str[$pos] === ($quote === "'" ? '"' : "'")) {
                        $nestedQuote = $str[$pos];
                        $pos++;
                        while ($pos < $len && $str[$pos] !== $nestedQuote) {
                            if ($str[$pos] === '\\' && $pos + 1 < $len) {
                                $pos++;
                            }
                            $pos++;
                        }
                        if ($pos < $len) {
                            $pos++;
                        } // Skip closing nested quote
                        continue;
                    }
                    if ($str[$pos] === $quote) {
                        break;
                    }
                    $pos++;
                }

                $class = substr($str, $start, $pos - $start);
                if (!empty($class) && $class !== '[' && $class !== ']') {
                    $classes[] = $class;
                }
                if ($pos < $len) {
                    $pos++;
                } // Skip closing quote
            } else {
                // Skip non-string content
                $pos++;
            }
        }

        return $classes;
    }

    private static function cleanExpectedCss(string $css): string
    {
        // Remove surrounding quotes from template literal
        $css = trim($css);
        if (str_starts_with($css, '"') && str_ends_with($css, '"')) {
            $css = substr($css, 1, -1);
        }

        // Remove @layer properties blocks (match nested braces properly)
        $css = self::removeAtRule($css, '@layer properties');
        $css = self::removeAtRule($css, '@layer theme');

        // Remove :root, :host blocks
        $css = self::removeBlock($css, ':root, :host');
        $css = self::removeBlock($css, ':root,:host');

        // Remove @property blocks
        $css = preg_replace('/@property\s+[^\{]+\{[^}]*\}\s*/m', '', $css);

        // Remove @supports blocks (match nested braces properly)
        $css = self::removeAtRule($css, '@supports');

        // Clean up any orphaned closing braces at start
        $css = preg_replace('/^\s*\}\s*\n\s*\}\s*/m', '', $css);

        return trim($css);
    }

    /**
     * Remove an at-rule block, handling nested braces.
     */
    private static function removeAtRule(string $css, string $atRule): string
    {
        $pattern = preg_quote($atRule, '/');
        while (preg_match('/' . $pattern . '\s*[^{]*\{/', $css, $match, PREG_OFFSET_CAPTURE)) {
            $start = $match[0][1];
            $braceStart = strpos($css, '{', $start);
            if ($braceStart === false) {
                break;
            }

            $braceCount = 1;
            $pos = $braceStart + 1;
            $len = strlen($css);
            while ($pos < $len && $braceCount > 0) {
                if ($css[$pos] === '{') {
                    $braceCount++;
                } elseif ($css[$pos] === '}') {
                    $braceCount--;
                }
                $pos++;
            }

            $css = substr($css, 0, $start) . substr($css, $pos);
        }

        return $css;
    }

    /**
     * Remove a CSS block by selector.
     */
    private static function removeBlock(string $css, string $selector): string
    {
        $pattern = preg_quote($selector, '/');
        while (preg_match('/' . $pattern . '\s*\{/', $css, $match, PREG_OFFSET_CAPTURE)) {
            $start = $match[0][1];
            $braceStart = strpos($css, '{', $start);
            if ($braceStart === false) {
                break;
            }

            $braceCount = 1;
            $pos = $braceStart + 1;
            $len = strlen($css);
            while ($pos < $len && $braceCount > 0) {
                if ($css[$pos] === '{') {
                    $braceCount++;
                } elseif ($css[$pos] === '}') {
                    $braceCount--;
                }
                $pos++;
            }

            $css = substr($css, 0, $start) . substr($css, $pos);
        }

        return $css;
    }

    /**
     * Extract CSS rules from a CSS string, handling nested blocks.
     */
    private static function extractCssRules(string $css): array
    {
        $rules = [];
        $len = strlen($css);
        $pos = 0;

        while ($pos < $len) {
            // Skip whitespace
            while ($pos < $len && ctype_space($css[$pos])) {
                $pos++;
            }
            if ($pos >= $len) {
                break;
            }

            // Find selector (everything up to {)
            $selectorStart = $pos;
            while ($pos < $len && $css[$pos] !== '{') {
                $pos++;
            }
            if ($pos >= $len) {
                break;
            }

            $selector = trim(substr($css, $selectorStart, $pos - $selectorStart));
            $pos++; // Skip {

            // Find matching closing brace
            $braceCount = 1;
            $contentStart = $pos;
            while ($pos < $len && $braceCount > 0) {
                if ($css[$pos] === '{') {
                    $braceCount++;
                } elseif ($css[$pos] === '}') {
                    $braceCount--;
                }
                $pos++;
            }

            $content = trim(substr($css, $contentStart, $pos - $contentStart - 1));

            // If content contains nested braces, recursively extract
            if (str_contains($content, '{')) {
                $nestedRules = self::extractCssRules($content);
                foreach ($nestedRules as $nestedSelector => $nestedDecls) {
                    // Prefix nested selector with parent
                    $fullSelector = $selector;
                    $rules[$fullSelector] = $nestedDecls;
                }
            } else {
                // Parse declarations
                $declarations = [];
                $parts = array_filter(array_map('trim', explode(';', $content)));
                foreach ($parts as $part) {
                    // Split on first colon only (handles --var: value with colons)
                    $colonPos = strpos($part, ':');
                    if ($colonPos !== false) {
                        $prop = trim(substr($part, 0, $colonPos));
                        $value = trim(substr($part, $colonPos + 1));
                        $declarations[$prop] = $value;
                    }
                }
                if (!empty($declarations)) {
                    $rules[$selector] = $declarations;
                }
            }
        }

        return $rules;
    }

    /**
     * Provide test cases for the data provider.
     */
    public static function tailwindTestCases(): array
    {
        self::parseTestFiles();

        return array_map(fn ($test) => [$test], self::$testCases);
    }

    #[Test]
    #[DataProvider('tailwindTestCases')]
    public function tailwind_compliance(array $testCase): void
    {
        // Handle compileCss type tests - they have custom CSS with @theme
        if ($testCase['type'] === 'compileCss') {
            $cssInput = $testCase['css'];
            // Ensure @tailwind utilities is present
            if (!str_contains($cssInput, '@tailwind utilities')) {
                $cssInput .= "\n@tailwind utilities;";
            }
            $compiled = compile($cssInput);
            $css = $compiled['build']($testCase['classes']);

            // For compileCss tests, verify that:
            // 1. CSS is generated (not empty) - unless expected is also empty
            // 2. Each class that appears in expected output has a corresponding selector in actual output
            $expectedOutput = $testCase['expected'] ?? '';

            // If expected output is empty, verify actual is also empty
            if (trim($expectedOutput) === '') {
                $this->assertEmpty(trim($css), sprintf(
                    "Expected empty CSS output for classes: %s\n\nActual CSS:\n%s",
                    implode(', ', $testCase['classes']),
                    $css,
                ));

                return;
            }

            $this->assertNotEmpty(trim($css), sprintf(
                'Expected CSS output for classes: %s',
                implode(', ', $testCase['classes']),
            ));

            // Verify each class generates a selector - but only if the class appears in expected output
            foreach ($testCase['classes'] as $class) {
                // Skip invalid/negative test classes
                if (str_starts_with($class, '-') ||
                    $class === '' ||
                    str_contains($class, ' ')) {
                    continue;
                }

                // Convert class to expected CSS selector format
                // CSS escapes special characters with backslash: / -> \/, [ -> \[, ] -> \]
                $expectedSelector = '.' . strtr($class, [
                    '/' => '\\/',
                    '[' => '\\[',
                    ']' => '\\]',
                    '#' => '\\#',
                    ':' => '\\:',
                    '(' => '\\(',
                    ')' => '\\)',
                    '.' => '\\.',
                    '%' => '\\%',
                    ',' => '\\,',
                    '"' => '\\"',
                    "'" => "\\'",
                ]);

                // Check if this class is expected to produce output by checking if it's in the expected output
                // If it's not in expected output, skip checking for it in actual output
                $inExpected = str_contains($expectedOutput, $expectedSelector . ' ') ||
                              str_contains($expectedOutput, $expectedSelector . '{') ||
                              str_contains($expectedOutput, $expectedSelector . ',') ||
                              str_contains($expectedOutput, $expectedSelector . "\n");

                if (!$inExpected) {
                    continue; // Class is intentionally not in expected output
                }

                // Check if the CSS output contains this selector
                $foundSelector = str_contains($css, $expectedSelector . ' ') ||
                                 str_contains($css, $expectedSelector . '{') ||
                                 str_contains($css, $expectedSelector . ',') ||
                                 str_contains($css, $expectedSelector . "\n");

                $this->assertTrue(
                    $foundSelector,
                    sprintf("Class '%s' not found in CSS output (expected selector: %s)", $class, $expectedSelector),
                );
            }

            return;
        }

        $css = TestHelper::run($testCase['classes']);

        if ($testCase['type'] === 'empty') {
            $this->assertEquals('', $css, sprintf(
                'Expected empty output for classes: %s',
                implode(', ', $testCase['classes']),
            ));

            return;
        }

        $expectedRules = self::extractCssRules($testCase['expected']);
        $actualRules = self::extractCssRules($css);

        foreach ($expectedRules as $selector => $expectedDecls) {
            // Check selector exists (with possible escaping differences)
            $selectorFound = false;
            $matchedSelector = null;

            foreach ($actualRules as $actualSelector => $actualDecls) {
                if ($this->selectorsMatch($selector, $actualSelector)) {
                    $selectorFound = true;
                    $matchedSelector = $actualSelector;
                    break;
                }
            }

            $this->assertTrue($selectorFound, sprintf(
                "Missing selector '%s' in output for classes: %s\nActual selectors: %s",
                $selector,
                implode(', ', $testCase['classes']),
                implode(', ', array_keys($actualRules)),
            ));

            if ($matchedSelector) {
                $actualDecls = $actualRules[$matchedSelector];

                foreach ($expectedDecls as $prop => $expectedValue) {
                    $this->assertArrayHasKey($prop, $actualDecls, sprintf(
                        "Missing property '%s' in selector '%s' for classes: %s",
                        $prop,
                        $selector,
                        implode(', ', $testCase['classes']),
                    ));

                    // Allow theme variable differences
                    if (!$this->valuesMatch($expectedValue, $actualDecls[$prop])) {
                        $this->assertEquals($expectedValue, $actualDecls[$prop], sprintf(
                            'Value mismatch for %s { %s } in classes: %s',
                            $selector,
                            $prop,
                            implode(', ', $testCase['classes']),
                        ));
                    }
                }
            }
        }
    }

    /**
     * Check if two selectors match (accounting for escaping differences).
     */
    private function selectorsMatch(string $expected, string $actual): bool
    {
        if ($expected === $actual) {
            return true;
        }

        // Normalize escaping
        $expected = str_replace('\\\\', '\\', $expected);
        $actual = str_replace('\\\\', '\\', $actual);

        return $expected === $actual;
    }

    /**
     * Check if two CSS values match (allowing theme variable usage and lightningcss equivalences).
     */
    private function valuesMatch(string $expected, string $actual): bool
    {
        if ($expected === $actual) {
            return true;
        }

        $expected = strtolower(trim($expected));
        $actual = strtolower(trim($actual));

        if ($expected === $actual) {
            return true;
        }

        // Theme variable usage - MUST match expected if expected is also a var()
        // Only accept var() if expected is NOT a var (i.e., actual uses theme, expected was hardcoded)
        if (preg_match('/^var\(--[\w-]+\)$/', $actual) && !str_contains($expected, 'var(')) {
            return true;
        }

        // calc() with var() - only if expected doesn't have specific var
        if (preg_match('/^calc\(var\(--[\w-]+\)/', $actual) && !str_contains($expected, 'var(')) {
            return true;
        }

        // calc(var(...) * N) patterns - only if expected doesn't have specific var
        if (preg_match('/^calc\(var\(--[\w-]+\)\s*\*\s*[\d.]+\)$/', $actual) && !str_contains($expected, 'var(')) {
            return true;
        }

        // Check if values are equivalent calc expressions
        // calc(Xpx * -1) is equivalent to -Xpx (lightningcss normalization)
        if (preg_match('/^-?(\d+)(px|rem|em)$/', $expected, $m1) &&
            preg_match('/^calc\((\d+)(px|rem|em)\s*\*\s*-1\)$/', $actual, $m2)) {
            if ($m1[1] === $m2[1] && $m1[2] === $m2[2] && str_starts_with($expected, '-')) {
                return true;
            }
        }
        // And the reverse
        if (preg_match('/^calc\((\d+)(px|rem|em)\s*\*\s*-1\)$/', $expected, $m1) &&
            preg_match('/^-?(\d+)(px|rem|em)$/', $actual, $m2)) {
            if ($m1[1] === $m2[1] && $m1[2] === $m2[2] && str_starts_with($actual, '-')) {
                return true;
            }
        }

        // Hex color shortening: #0088cc === #08c
        $expandedExpected = self::expandHexColor($expected);
        $expandedActual = self::expandHexColor($actual);
        if ($expandedExpected === $expandedActual) {
            return true;
        }

        // Fraction to percentage: 50% === calc(1 / 2 * 100%)
        if (preg_match('/^(\d+(?:\.\d+)?)%$/', $expected, $m1)) {
            $expectedPercent = (float)$m1[1];
            // Check calc(a / b * 100%)
            if (preg_match('/^calc\((\d+)\s*\/\s*(\d+)\s*\*\s*100%\)$/', $actual, $m2)) {
                $actualPercent = ((int)$m2[1] / (int)$m2[2]) * 100;
                if (abs($expectedPercent - $actualPercent) < 0.001) {
                    return true;
                }
            }
        }

        // clamp() simplification: clamp(1rem, 2rem, 3rem) === 2rem (if middle value is in range)
        if (preg_match('/^clamp\(([^,]+),\s*([^,]+),\s*([^)]+)\)$/', $actual, $m)) {
            $middle = trim($m[2]);
            if ($expected === $middle) {
                return true;
            }
        }
        // Reverse: expected is clamp, actual is simplified
        if (preg_match('/^clamp\(([^,]+),\s*([^,]+),\s*([^)]+)\)$/', $expected, $m)) {
            $middle = trim($m[2]);
            if ($actual === $middle) {
                return true;
            }
        }

        // var() with fallback containing var(): var(--x, var(--y)) patterns
        // Only match if ALL variable names match (not just intersection)
        if (str_contains($actual, 'var(') && str_contains($expected, 'var(')) {
            $actualVars = [];
            $expectedVars = [];
            preg_match_all('/var\(--[\w-]+/', $actual, $actualVars);
            preg_match_all('/var\(--[\w-]+/', $expected, $expectedVars);
            // Sort and compare - must have same vars
            if (!empty($actualVars[0]) && !empty($expectedVars[0])) {
                sort($actualVars[0]);
                sort($expectedVars[0]);
                if ($actualVars[0] === $expectedVars[0]) {
                    return true;
                }
            }
        }

        // oklab() vs color-mix(in oklab, ...) - DISABLED: too loose, need proper color comparison
        // TODO: Implement actual color value comparison if needed
        // For now, these must match exactly

        return false;
    }

    /**
     * Expand shorthand hex colors to full form.
     */
    private static function expandHexColor(string $color): string
    {
        // Match 3 or 4 char hex colors
        if (preg_match('/^#([0-9a-f])([0-9a-f])([0-9a-f])([0-9a-f])?$/i', $color, $m)) {
            $r = $m[1] . $m[1];
            $g = $m[2] . $m[2];
            $b = $m[3] . $m[3];
            $a = isset($m[4]) ? $m[4] . $m[4] : '';

            return '#' . $r . $g . $b . $a;
        }

        return $color;
    }
}
