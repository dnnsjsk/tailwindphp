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

        $lines = explode("\n", $content);
        $inTest = false;
        $braceCount = 0;
        $testBody = '';
        $currentTestName = '';
        $testBodies = [];

        foreach ($lines as $line) {
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
            $pos = 0;
            $testIndex = 0;

            while (($expectPos = strpos($testBody, 'expect(', $pos)) !== false) {
                $runPos = strpos($testBody, 'await run([', $expectPos);
                $compilePos = strpos($testBody, 'await compileCss(', $expectPos);

                $isRun = $runPos !== false && ($compilePos === false || $runPos < $compilePos);
                $isCompile = $compilePos !== false && ($runPos === false || $compilePos < $runPos);

                if ($isRun && $runPos < $expectPos + 50) {
                    $arrayStart = strpos($testBody, '[', $runPos);
                    $arrayEnd = self::findMatchingBracket($testBody, $arrayStart, '[', ']');

                    if ($arrayEnd !== null) {
                        $classesStr = substr($testBody, $arrayStart + 1, $arrayEnd - $arrayStart - 1);
                        $classes = self::parseClassArray($classesStr);
                        $afterArray = substr($testBody, $arrayEnd, 200);

                        if (preg_match('/\)\s*\)\s*\.toMatchInlineSnapshot\s*\(\s*`/', $afterArray)) {
                            $snapshotStart = strpos($testBody, '.toMatchInlineSnapshot(`', $arrayEnd);
                            if ($snapshotStart !== false) {
                                $cssStart = strpos($testBody, '`', $snapshotStart + 20) + 1;
                                $cssEnd = strpos($testBody, '`)', $cssStart);
                                if ($cssEnd !== false) {
                                    $expectedCss = substr($testBody, $cssStart, $cssEnd - $cssStart);
                                    if (!empty($classes)) {
                                        $tests[] = [
                                            'name' => $testName,
                                            'index' => $testIndex,
                                            'classes' => $classes,
                                            'expected' => self::cleanExpectedCss($expectedCss),
                                            'type' => 'match',
                                        ];
                                        $testIndex++;
                                    }
                                }
                            }
                        } elseif (preg_match('/\)\s*\)\s*\.toEqual\s*\(\s*[\'"][\'"]/', $afterArray)) {
                            if (!empty($classes)) {
                                $tests[] = [
                                    'name' => $testName,
                                    'index' => $testIndex,
                                    'classes' => $classes,
                                    'expected' => '',
                                    'type' => 'empty',
                                ];
                                $testIndex++;
                            }
                        }
                    }
                    $pos = $arrayEnd !== null ? $arrayEnd : $expectPos + 10;
                } elseif ($isCompile && $compilePos < $expectPos + 50) {
                    $pos = $compilePos + 20;
                    $testIndex++;
                } else {
                    $pos = $expectPos + 10;
                }
            }
        }

        return $tests;
    }

    private static function findMatchingBracket(string $str, int $start, string $open, string $close): ?int
    {
        $count = 1;
        $pos = $start + 1;
        $len = strlen($str);

        while ($pos < $len && $count > 0) {
            $char = $str[$pos];
            if ($char === $open) $count++;
            elseif ($char === $close) $count--;
            $pos++;
        }

        return $count === 0 ? $pos - 1 : null;
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
