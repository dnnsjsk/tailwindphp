<?php

declare(strict_types=1);

/**
 * Port of: https://github.com/lukeed/clsx/blob/main/test/
 *
 * These tests are auto-parsed from the extracted clsx test suite.
 * Test files: index.js (12 tests), classnames.js (15 tests)
 * Total: 27 tests
 */

namespace TailwindPHP\Lib\Clsx;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__ . '/clsx.php';

class clsx extends TestCase
{
    private static array $testCases = [];
    private static bool $parsed = false;

    /**
     * Parse all extracted .js test files and build test cases.
     */
    private static function parseTestFiles(): void
    {
        if (self::$parsed) {
            return;
        }

        $extractedDir = __DIR__ . '/../../../../test-coverage/lib/clsx/tests';
        $jsFiles = glob($extractedDir . '/*.js');

        foreach ($jsFiles as $file) {
            $tests = self::parseTestFile($file);
            foreach ($tests as $test) {
                $key = basename($file, '.js') . '::' . $test['name'];
                self::$testCases[$key] = $test;
            }
        }

        self::$parsed = true;
    }

    /**
     * Parse a single JavaScript test file.
     */
    private static function parseTestFile(string $filePath): array
    {
        $content = file_get_contents($filePath);
        $tests = [];

        // Find all test() blocks using proper brace matching
        $offset = 0;
        while (preg_match('/test\([\'"]([^\'"]+)[\'"],\s*\(\)\s*=>\s*\{/', $content, $match, PREG_OFFSET_CAPTURE, $offset)) {
            $testName = $match[1][0];
            $bodyStart = $match[0][1] + strlen($match[0][0]);

            // Find matching closing brace
            $depth = 1;
            $i = $bodyStart;
            $inString = false;
            $stringChar = '';

            while ($i < strlen($content) && $depth > 0) {
                $char = $content[$i];

                if ($inString) {
                    if ($char === $stringChar && $content[$i - 1] !== '\\') {
                        $inString = false;
                    }
                } elseif ($char === '"' || $char === "'" || $char === '`') {
                    $inString = true;
                    $stringChar = $char;
                } elseif ($char === '{') {
                    $depth++;
                } elseif ($char === '}') {
                    $depth--;
                }
                $i++;
            }

            $testBody = substr($content, $bodyStart, $i - $bodyStart - 1);
            $offset = $i;

            $assertionsList = [];

            // Pattern 1: Direct assert.is(fn(...), 'expected') or assert.is(clsx(...), 'expected')
            preg_match_all('/assert\.is\(\s*(?:fn|clsx)\(([^)]*(?:\([^)]*\)[^)]*)*)\)\s*,\s*[\'"]([^\'"]*)[\'"]/', $testBody, $assertions, PREG_SET_ORDER);

            foreach ($assertions as $assertion) {
                $argsStr = trim($assertion[1]);
                $expected = $assertion[2];

                $args = self::parseArguments($argsStr);
                $assertionsList[] = [
                    'args' => $args,
                    'expected' => $expected,
                ];
            }

            // Pattern 2: Variable assignment then assert.is(out, 'expected')
            // e.g., const out = clsx(...); assert.is(out, 'expected');
            if (preg_match('/const\s+out\s*=\s*(?:fn|clsx)\(([^;]+)\);?\s*assert\.is\(out,\s*[\'"]([^\'"]*)[\'"]/', $testBody, $varMatch)) {
                $argsStr = trim($varMatch[1]);
                // Remove trailing ) if present
                $argsStr = rtrim($argsStr, ')');
                $expected = $varMatch[2];

                $args = self::parseArguments($argsStr);
                $assertionsList[] = [
                    'args' => $args,
                    'expected' => $expected,
                ];
            }

            // Parse assert.type() calls for exports test
            preg_match_all('/assert\.type\(([^,]+),\s*[\'"]([^\'"]+)[\'"]/', $testBody, $typeAssertions, PREG_SET_ORDER);

            if (!empty($assertionsList) || !empty($typeAssertions)) {
                $tests[] = [
                    'name' => $testName,
                    'assertions' => $assertionsList,
                    'typeAssertions' => count($typeAssertions),
                ];
            }
        }

        return $tests;
    }

    /**
     * Parse JavaScript arguments into PHP values.
     */
    private static function parseArguments(string $argsStr): array
    {
        $args = [];
        $argsStr = trim($argsStr);

        if ($argsStr === '') {
            return $args;
        }

        // Remove JavaScript comments (// ... and /* ... */)
        $argsStr = preg_replace('/\/\/[^\n]*/', '', $argsStr);
        $argsStr = preg_replace('/\/\*.*?\*\//s', '', $argsStr);

        // Simple tokenizer for JS args
        $tokens = self::tokenize($argsStr);

        foreach ($tokens as $token) {
            $args[] = self::parseValue($token);
        }

        return $args;
    }

    /**
     * Tokenize a comma-separated argument string.
     */
    private static function tokenize(string $str): array
    {
        $tokens = [];
        $current = '';
        $depth = 0;
        $inString = false;
        $stringChar = '';

        for ($i = 0; $i < strlen($str); $i++) {
            $char = $str[$i];

            if ($inString) {
                $current .= $char;
                if ($char === $stringChar && ($i === 0 || $str[$i - 1] !== '\\')) {
                    $inString = false;
                }
            } elseif ($char === '"' || $char === "'") {
                $current .= $char;
                $inString = true;
                $stringChar = $char;
            } elseif ($char === '{' || $char === '[' || $char === '(') {
                $current .= $char;
                $depth++;
            } elseif ($char === '}' || $char === ']' || $char === ')') {
                $current .= $char;
                $depth--;
            } elseif ($char === ',' && $depth === 0) {
                $tokens[] = trim($current);
                $current = '';
            } else {
                $current .= $char;
            }
        }

        if (trim($current) !== '') {
            $tokens[] = trim($current);
        }

        return $tokens;
    }

    /**
     * Parse a JavaScript value into PHP.
     */
    private static function parseValue(string $value): mixed
    {
        $value = trim($value);

        // String literals
        if (preg_match('/^[\'"](.*)[\'"]\s*$/', $value, $m)) {
            return $m[1];
        }

        // Boolean/null
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if ($value === 'null') return null;
        if ($value === 'undefined') return null;

        // Numbers - but PHP outputs INF for Infinity, need special handling
        if ($value === 'Infinity') return 'INF_PLACEHOLDER';
        if ($value === 'NaN') return NAN;
        if (is_numeric($value)) return $value + 0;

        // Empty object
        if ($value === '{}') return [];

        // Object literal
        if (preg_match('/^\{(.+)\}$/s', $value, $m)) {
            return self::parseObject($m[1]);
        }

        // Array literal
        if (preg_match('/^\[(.*)]\s*$/s', $value, $m)) {
            return self::parseArray($m[1]);
        }

        // Ternary expressions like "true && 'foo'" or "false && 'bar'"
        if (preg_match('/^(true|false)\s*&&\s*[\'"]([^\'"]*)[\'"]$/', $value, $m)) {
            return $m[1] === 'true' ? $m[2] : false;
        }

        // Ternary with number: "0 && 'foo'" or "1 && 'bar'"
        if (preg_match('/^(\d+)\s*&&\s*[\'"]([^\'"]*)[\'"]$/', $value, $m)) {
            return ((int)$m[1]) ? $m[2] : false;
        }

        // Function reference - return a closure (ignored in PHP clsx)
        if (preg_match('/^(?:fn|foo|\(\)\s*=>\s*\{\}|Object\.\w+\.\w+)$/', $value)) {
            return function () {};
        }

        return $value;
    }

    /**
     * Parse a JavaScript object literal into PHP associative array.
     */
    private static function parseObject(string $content): array
    {
        $result = [];
        $tokens = self::tokenize($content);

        foreach ($tokens as $token) {
            // Handle shorthand property names and regular key:value pairs
            if (preg_match('/^\s*[\'"]?([^\'":\s]+)[\'"]?\s*:\s*(.+)$/s', $token, $m)) {
                $key = trim($m[1]);
                $val = self::parseValue($m[2]);
                $result[$key] = $val;
            } elseif (preg_match('/^\s*([a-zA-Z_]\w*)\s*$/', $token, $m)) {
                // Shorthand property: { foo } means { foo: foo }
                $result[$m[1]] = $m[1];
            }
        }

        return $result;
    }

    /**
     * Parse a JavaScript array literal into PHP array.
     */
    private static function parseArray(string $content): array
    {
        if (trim($content) === '') {
            return [];
        }

        $tokens = self::tokenize($content);
        $result = [];

        foreach ($tokens as $token) {
            $result[] = self::parseValue($token);
        }

        return $result;
    }

    /**
     * Provide test cases for the data provider.
     */
    public static function casesProvider(): array
    {
        self::parseTestFiles();

        $provided = [];
        foreach (self::$testCases as $key => $test) {
            $provided[$key] = [$test];
        }

        return $provided;
    }

    /**
     * Run a parsed test case.
     */
    #[DataProvider('casesProvider')]
    #[Test]
    public function test_case(array $test): void
    {
        // Handle exports test separately
        if ($test['name'] === 'exports' && $test['typeAssertions'] > 0) {
            $this->assertIsCallable('TailwindPHP\Lib\Clsx\clsx');
            $this->assertIsString(clsx());
            return;
        }

        foreach ($test['assertions'] as $assertion) {
            $args = $assertion['args'];
            $expected = $assertion['expected'];

            // Replace INF_PLACEHOLDER with actual INF value
            array_walk_recursive($args, function (&$val) {
                if ($val === 'INF_PLACEHOLDER') {
                    $val = INF;
                }
            });

            $result = clsx(...$args);

            // Handle Infinity output difference: PHP outputs 'INF', JS outputs 'Infinity'
            if ($expected === 'Infinity') {
                $expected = 'INF';
            }
            // Handle cases where expected contains 'Infinity'
            $expected = str_replace('Infinity', 'INF', $expected);

            $this->assertSame(
                $expected,
                $result,
                sprintf(
                    "Test '%s': clsx(%s) should return '%s', got '%s'",
                    $test['name'],
                    json_encode($args),
                    $expected,
                    $result
                )
            );
        }
    }
}
