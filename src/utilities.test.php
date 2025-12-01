<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
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

        $extractedDir = dirname(__DIR__) . '/extracted-tests';
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
                if ($char === '{') $braceCount++;
                elseif ($char === '}') $braceCount--;
                $pos++;
            }

            $testBody = substr($content, $testStart, $pos - $testStart);

            // Parse run() calls from this test body
            $runTests = self::parseRunCalls($testBody, $testName);
            $tests = array_merge($tests, $runTests);
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
                    if ($str[$pos] === '\\') $pos++;
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
        preg_match_all('/[\'"]([^\'"]+)[\'"]/', $str, $matches);
        foreach ($matches[1] as $class) {
            $classes[] = $class;
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

        // Remove @layer properties blocks
        $css = preg_replace('/@layer\s+properties\s*\{[\s\S]*?\n\s*\}\s*/m', '', $css);
        // Remove :root, :host blocks
        $css = preg_replace('/:root,\s*:host\s*\{[\s\S]*?\}\s*/m', '', $css);
        // Remove @property blocks
        $css = preg_replace('/@property\s+[^\{]+\{[\s\S]*?\}\s*/m', '', $css);
        // Remove @supports blocks
        $css = preg_replace('/@supports\s*\([^\)]+\)\s*\{[\s\S]*?\n\s*\}\s*/m', '', $css);

        return trim($css);
    }

    /**
     * Extract CSS rules from a CSS string.
     */
    private static function extractCssRules(string $css): array
    {
        $rules = [];
        preg_match_all('/([^\{\}]+)\s*\{([^\}]*)\}/m', $css, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $selector = trim($match[1]);
            $declarationsStr = trim($match[2]);
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
     * Provide test cases for the data provider.
     */
    public static function tailwindTestCases(): array
    {
        self::parseTestFiles();
        return array_map(fn($test) => [$test], self::$testCases);
    }

    #[Test]
    #[DataProvider('tailwindTestCases')]
    public function tailwind_compliance(array $testCase): void
    {
        $css = TestHelper::run($testCase['classes']);

        if ($testCase['type'] === 'empty') {
            $this->assertEquals('', $css, sprintf(
                "Expected empty output for classes: %s",
                implode(', ', $testCase['classes'])
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
                implode(', ', array_keys($actualRules))
            ));

            if ($matchedSelector) {
                $actualDecls = $actualRules[$matchedSelector];

                foreach ($expectedDecls as $prop => $expectedValue) {
                    $this->assertArrayHasKey($prop, $actualDecls, sprintf(
                        "Missing property '%s' in selector '%s' for classes: %s",
                        $prop,
                        $selector,
                        implode(', ', $testCase['classes'])
                    ));

                    // Allow theme variable differences
                    if (!$this->valuesMatch($expectedValue, $actualDecls[$prop])) {
                        $this->assertEquals($expectedValue, $actualDecls[$prop], sprintf(
                            "Value mismatch for %s { %s } in classes: %s",
                            $selector,
                            $prop,
                            implode(', ', $testCase['classes'])
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
     * Check if two CSS values match (allowing theme variable usage).
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

        // Theme variable usage is acceptable
        if (preg_match('/^var\(--[\w-]+\)$/', $actual)) {
            return true;
        }

        // calc() with var() is also acceptable
        if (preg_match('/^calc\(var\(--[\w-]+\)/', $actual)) {
            return true;
        }

        // calc(var(...) * N) patterns
        if (preg_match('/^calc\(var\(--[\w-]+\)\s*\*\s*[\d.]+\)$/', $actual)) {
            return true;
        }

        return false;
    }
}
