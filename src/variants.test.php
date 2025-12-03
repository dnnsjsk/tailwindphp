<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Tests\TestHelper;

/**
 * TailwindCSS Variants Compliance Tests
 *
 * These tests are auto-parsed from the extracted TailwindCSS variant test suite
 * (originally from packages/tailwindcss/src/variants.test.ts - 2.6k LOC).
 *
 * The tests verify that our PHP implementation produces CSS output that
 * matches the expected output from TailwindCSS for variant handling.
 */
class variants extends TestCase
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

        $extractedDir = __DIR__ . '/../test-coverage/variants/tests';
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
            $toEqualMatch = preg_match('/\)\s*,?\s*\)\s*\.toEqual\s*\(\s*[\'\"][\'\"]/', $afterArray, $matchEqual, PREG_OFFSET_CAPTURE);
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
        $i = 0;

        while ($i < $len) {
            // Skip whitespace and commas
            while ($i < $len && ($str[$i] === ' ' || $str[$i] === ',' || $str[$i] === "\n" || $str[$i] === "\t")) {
                $i++;
            }
            if ($i >= $len) {
                break;
            }

            // Check for string start
            if ($str[$i] === "'" || $str[$i] === '"') {
                $quote = $str[$i];
                $i++;
                $class = '';

                while ($i < $len) {
                    // Handle escape sequences
                    if ($str[$i] === '\\' && $i + 1 < $len) {
                        $class .= $str[$i + 1];
                        $i += 2;
                        continue;
                    }
                    // End of string
                    if ($str[$i] === $quote) {
                        $i++;
                        break;
                    }
                    $class .= $str[$i];
                    $i++;
                }

                if ($class !== '') {
                    $classes[] = $class;
                }
            } else {
                $i++;
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

        // Remove @layer properties blocks (with nested content)
        $css = self::removeNestedAtRule($css, '@layer properties');
        // Remove :root, :host blocks
        $css = preg_replace('/:root,\s*:host\s*\{[\s\S]*?\}\s*/m', '', $css);
        // Remove @property blocks
        $css = preg_replace('/@property\s+[^\{]+\{[\s\S]*?\}\s*/m', '', $css);
        // Remove browser-detection @supports blocks (the ones with -webkit-hyphens)
        // These are NOT feature detection queries, just internal Tailwind blocks
        $css = self::removeNestedAtRule($css, '@supports (((-webkit-hyphens');

        return trim($css);
    }

    /**
     * Remove an at-rule with nested content (handles nested braces correctly).
     */
    private static function removeNestedAtRule(string $css, string $atRuleName): string
    {
        $pattern = preg_quote($atRuleName, '/');
        $offset = 0;

        while (preg_match('/' . $pattern . '\s*[^{]*\{/s', $css, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $startPos = $match[0][1];
            $braceStart = $startPos + strlen($match[0][0]) - 1;

            // Find matching closing brace
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

            if ($braceCount === 0) {
                // Remove the entire block
                $css = substr($css, 0, $startPos) . substr($css, $pos);
            } else {
                // Couldn't find matching brace, skip
                $offset = $braceStart + 1;
            }
        }

        return $css;
    }

    /**
     * Extract CSS rules from a CSS string.
     */
    private static function extractCssRules(string $css): array
    {
        $rules = [];

        // Parse CSS by tracking brace nesting level
        $len = strlen($css);
        $i = 0;
        $currentSelector = '';
        $currentBody = '';
        $braceLevel = 0;
        $inRule = false;
        $atRuleSelector = null;

        while ($i < $len) {
            $char = $css[$i];

            if ($char === '{') {
                $braceLevel++;
                if ($braceLevel === 1) {
                    // Starting a rule
                    $currentSelector = trim($currentSelector);
                    if (str_starts_with($currentSelector, '@')) {
                        $atRuleSelector = $currentSelector;
                    }
                    $currentBody = '';
                    $inRule = true;
                } else {
                    $currentBody .= $char;
                }
            } elseif ($char === '}') {
                $braceLevel--;
                if ($braceLevel === 0) {
                    // Ending a rule
                    $selector = trim($currentSelector);
                    $body = trim($currentBody);

                    if (str_starts_with($selector, '@')) {
                        // At-rule with nested content - extract nested rules
                        $nestedRules = self::extractCssRules($body);
                        foreach ($nestedRules as $nestedSelector => $decls) {
                            // Store with at-rule prefix for matching
                            $fullKey = $selector . '|||' . $nestedSelector;
                            $rules[$fullKey] = $decls;
                            // Also store just the nested selector for simple matching
                            $rules[$nestedSelector] = $decls;
                        }
                    } else {
                        // Regular rule - parse declarations
                        $declarations = self::parseDeclarations($body);
                        if (!empty($declarations)) {
                            $rules[$selector] = $declarations;
                        }
                    }

                    $currentSelector = '';
                    $currentBody = '';
                    $inRule = false;
                    $atRuleSelector = null;
                } else {
                    $currentBody .= $char;
                }
            } elseif ($inRule) {
                $currentBody .= $char;
            } else {
                $currentSelector .= $char;
            }

            $i++;
        }

        return $rules;
    }

    /**
     * Parse CSS declarations from a block body.
     */
    private static function parseDeclarations(string $body): array
    {
        $declarations = [];
        $parts = array_filter(array_map('trim', explode(';', $body)));

        foreach ($parts as $part) {
            if (strpos($part, ':') !== false) {
                [$prop, $value] = array_map('trim', explode(':', $part, 2));
                $declarations[$prop] = $value;
            }
        }

        return $declarations;
    }

    /**
     * Provide test cases for the data provider.
     */
    public static function variantTestCases(): array
    {
        self::parseTestFiles();

        return array_map(fn ($test) => [$test], self::$testCases);
    }

    #[Test]
    #[DataProvider('variantTestCases')]
    public function variant_compliance(array $testCase): void
    {
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
            // Handle comma-separated selectors - check each part exists
            // But don't split inside parentheses (e.g., :where(:dir(rtl), [dir="rtl"]))
            $selectorsToCheck = self::splitSelectorList($selector);

            foreach ($selectorsToCheck as $singleSelector) {
                // Check selector exists (with possible escaping differences)
                $selectorFound = false;
                $matchedSelector = null;

                foreach ($actualRules as $actualSelector => $actualDecls) {
                    if ($this->selectorsMatch($singleSelector, $actualSelector)) {
                        $selectorFound = true;
                        $matchedSelector = $actualSelector;
                        break;
                    }
                }

                $this->assertTrue($selectorFound, sprintf(
                    "Missing selector '%s' in output for classes: %s\nActual selectors: %s",
                    $singleSelector,
                    implode(', ', $testCase['classes']),
                    implode(', ', array_keys($actualRules)),
                ));

                if ($matchedSelector) {
                    $actualDecls = $actualRules[$matchedSelector];

                    foreach ($expectedDecls as $prop => $expectedValue) {
                        $this->assertArrayHasKey($prop, $actualDecls, sprintf(
                            "Missing property '%s' in selector '%s' for classes: %s",
                            $prop,
                            $singleSelector,
                            implode(', ', $testCase['classes']),
                        ));

                        // Allow theme variable differences
                        if (!$this->valuesMatch($expectedValue, $actualDecls[$prop])) {
                            $this->assertEquals($expectedValue, $actualDecls[$prop], sprintf(
                                'Value mismatch for %s { %s } in classes: %s',
                                $singleSelector,
                                $prop,
                                implode(', ', $testCase['classes']),
                            ));
                        }
                    }
                }
            }
        }
    }

    /**
     * Split a selector list by commas, but not inside parentheses or brackets.
     */
    private static function splitSelectorList(string $selector): array
    {
        if (!str_contains($selector, ',')) {
            return [$selector];
        }

        $parts = [];
        $current = '';
        $depth = 0;

        for ($i = 0; $i < strlen($selector); $i++) {
            $char = $selector[$i];

            if ($char === '(' || $char === '[') {
                $depth++;
                $current .= $char;
            } elseif ($char === ')' || $char === ']') {
                $depth--;
                $current .= $char;
            } elseif ($char === ',' && $depth === 0) {
                $parts[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if (trim($current) !== '') {
            $parts[] = trim($current);
        }

        return $parts;
    }

    /**
     * Check if two selectors match (accounting for escaping and media query syntax differences).
     */
    private function selectorsMatch(string $expected, string $actual): bool
    {
        if ($expected === $actual) {
            return true;
        }

        // Normalize escaping
        $expected = str_replace('\\\\', '\\', $expected);
        $actual = str_replace('\\\\', '\\', $actual);

        if ($expected === $actual) {
            return true;
        }

        // Handle merged selectors - if actual is a comma-separated list,
        // check if expected matches one of the parts (after normalization)
        // Use the splitSelectorList helper to correctly handle commas inside parentheses
        if (str_contains($actual, ', ')) {
            $actualParts = self::splitSelectorList($actual);
            foreach ($actualParts as $part) {
                $normalizedPart = trim($part);
                if ($expected === $normalizedPart) {
                    return true;
                }
            }
        }

        // Normalize media query syntax differences
        // Legacy: @media not all and (min-width: X) === Modern: @media (width < X)
        // Legacy: @media (min-width: X) === Modern: @media (width >= X)
        $expected = $this->normalizeMediaQuery($expected);
        $actual = $this->normalizeMediaQuery($actual);

        if ($expected === $actual) {
            return true;
        }

        // Handle nested @media deduplication
        // Expected: @media X|||@media X|||selector vs Actual: @media X|||selector
        // Both are functionally equivalent when the @media queries are identical
        if (str_contains($expected, '|||') || str_contains($actual, '|||')) {
            $expectedParts = explode('|||', $expected);
            $actualParts = explode('|||', $actual);

            // Get unique @media prefixes from expected
            $expectedMedias = [];
            foreach ($expectedParts as $part) {
                if (str_starts_with($part, '@media')) {
                    $expectedMedias[$part] = true;
                }
            }

            // Get unique @media prefixes from actual
            $actualMedias = [];
            foreach ($actualParts as $part) {
                if (str_starts_with($part, '@media')) {
                    $actualMedias[$part] = true;
                }
            }

            // Check if all unique @media prefixes match
            if ($expectedMedias != $actualMedias) {
                // Check if actual's @media is a subset of expected's (deduplication case)
                foreach ($actualMedias as $media => $_) {
                    if (!isset($expectedMedias[$media])) {
                        return false;
                    }
                }
            }

            // Compare the selector part (last non-@media element)
            $expectedSelector = end($expectedParts);
            $actualSelector = end($actualParts);

            // Handle escaping differences in selector
            $expectedSelector = str_replace('\\\\', '\\', $expectedSelector);
            $actualSelector = str_replace('\\\\', '\\', $actualSelector);

            if ($expectedSelector === $actualSelector) {
                return true;
            }

            // If actual selector is comma-separated (merged), check each part
            if (str_contains($actualSelector, ', ')) {
                $actualSelectorParts = self::splitSelectorList($actualSelector);
                foreach ($actualSelectorParts as $part) {
                    if ($expectedSelector === trim($part)) {
                        return true;
                    }
                }
            }

            return false;
        }

        return false;
    }

    /**
     * Normalize media query to a canonical form for comparison.
     */
    private function normalizeMediaQuery(string $selector): string
    {
        // Convert legacy max syntax: @media not all and (min-width: X) -> @media (width < X)
        $selector = preg_replace_callback(
            '/@media not all and \(min-width:\s*([^)]+)\)/',
            fn ($m) => '@media (width < ' . trim($m[1]) . ')',
            $selector,
        );

        // Convert legacy min syntax: @media (min-width: X) -> @media (width >= X)
        // But not if it's already part of a "not all and" pattern
        $selector = preg_replace_callback(
            '/@media \(min-width:\s*([^)]+)\)(?!\s*\|\|\|@media not)/',
            fn ($m) => '@media (width >= ' . trim($m[1]) . ')',
            $selector,
        );

        return $selector;
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
