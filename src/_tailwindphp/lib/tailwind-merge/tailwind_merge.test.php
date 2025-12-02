<?php

declare(strict_types=1);

/**
 * Port of: https://github.com/dcastil/tailwind-merge/tree/main/tests
 *
 * These tests are auto-parsed from the extracted tailwind-merge test suite.
 * Total applicable tests: 67 (tests requiring custom config are skipped)
 */

namespace TailwindPHP\Lib\TailwindMerge;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

require_once __DIR__ . '/index.php';

class tailwind_merge extends TestCase
{
    private static array $testCases = [];
    private static bool $parsed = false;

    /**
     * Test files that are applicable (don't require custom config).
     */
    private static array $applicableFiles = [
        'arbitrary-properties.test.ts',
        'arbitrary-values.test.ts',
        'arbitrary-variants.test.ts',
        'conflicts-across-class-groups.test.ts',
        'important-modifier.test.ts',
        'modifiers.test.ts',
        'negative-values.test.ts',
        'non-conflicting-classes.test.ts',
        'pseudo-variants.test.ts',
        'public-api.test.ts',
        'tw-join.test.ts',
        'tw-merge.test.ts',
        'validators.test.ts',
        'wonky-inputs.test.ts',
    ];

    /**
     * Parse all extracted .ts test files and build test cases.
     */
    private static function parseTestFiles(): void
    {
        if (self::$parsed) {
            return;
        }

        $extractedDir = __DIR__ . '/../../../../test-coverage/lib/tailwind-merge/tests';

        foreach (self::$applicableFiles as $file) {
            $filePath = $extractedDir . '/' . $file;
            if (!file_exists($filePath)) {
                continue;
            }

            $tests = self::parseTestFile($filePath);
            foreach ($tests as $test) {
                $key = basename($file, '.test.ts') . '::' . $test['name'];
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
        $filename = basename($filePath);

        // Find all test() blocks
        preg_match_all('/test\([\'"]([^\'"]+)[\'"],\s*\(\)\s*=>\s*\{([\s\S]*?)\n\}\);?/m', $content, $matches, PREG_SET_ORDER);

        foreach ($matches as $match) {
            $testName = $match[1];
            $testBody = $match[2];

            // Parse expect().toBe() calls - handle both single-line and multi-line formats
            // String: expect(twMerge('foo')).toBe('bar') or .toBe(\n    'bar',\n)
            // Boolean: expect(isAny()).toBe(true)
            preg_match_all('/expect\(([^)]+(?:\([^)]*\))?[^)]*)\)\.toBe\(\s*([\'"][^\'"]*[\'"]|true|false)\s*,?\s*\)/', $testBody, $assertions, PREG_SET_ORDER);

            $assertionsList = [];
            foreach ($assertions as $assertion) {
                $call = trim($assertion[1]);
                $expected = trim($assertion[2]);

                // Parse expected value
                if ($expected === 'true') {
                    $expected = true;
                } elseif ($expected === 'false') {
                    $expected = false;
                } elseif (preg_match('/^[\'"](.*)[\'"]\s*$/', $expected, $m)) {
                    $expected = $m[1];
                }

                // Parse the function call
                $parsed = self::parseFunctionCall($call, $filename);
                if ($parsed !== null) {
                    $assertionsList[] = [
                        'function' => $parsed['function'],
                        'args' => $parsed['args'],
                        'expected' => $expected,
                    ];
                }
            }

            if (!empty($assertionsList)) {
                $tests[] = [
                    'name' => $testName,
                    'file' => $filename,
                    'assertions' => $assertionsList,
                ];
            }
        }

        return $tests;
    }

    /**
     * Parse a function call and return function name and arguments.
     */
    private static function parseFunctionCall(string $call, string $filename): ?array
    {
        // Handle twMerge(...)
        if (preg_match('/^twMerge\((.*)\)$/s', trim($call), $m)) {
            return [
                'function' => 'twMerge',
                'args' => self::parseArguments($m[1]),
            ];
        }

        // Handle twJoin(...)
        if (preg_match('/^twJoin\((.*)\)$/s', trim($call), $m)) {
            return [
                'function' => 'twJoin',
                'args' => self::parseArguments($m[1]),
            ];
        }

        // Handle validators - e.g., isArbitraryLength('[3.7%]')
        if (preg_match('/^(is[A-Za-z]+)\((.*)\)$/s', trim($call), $m)) {
            return [
                'function' => $m[1],
                'args' => self::parseArguments($m[2]),
            ];
        }

        return null;
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
            // Process escape sequences
            $str = $m[1];
            $str = str_replace('\\n', "\n", $str);
            $str = str_replace('\\r', "\r", $str);
            $str = str_replace('\\t', "\t", $str);
            $str = str_replace('\\\\', "\\", $str);
            return $str;
        }

        // Boolean/null
        if ($value === 'true') return true;
        if ($value === 'false') return false;
        if ($value === 'null') return null;
        if ($value === 'undefined') return null;

        // Numbers
        if (is_numeric($value)) return $value + 0;

        // Empty object/array
        if ($value === '{}') return [];
        if ($value === '[]') return [];

        // Array literal
        if (preg_match('/^\[(.*)]$/s', $value, $m)) {
            return self::parseArray($m[1]);
        }

        // Handle short-circuit expressions like "true && 'foo'"
        if (preg_match('/^(true|false)\s*&&\s*[\'"]([^\'"]*)[\'"]$/', $value, $m)) {
            return $m[1] === 'true' ? $m[2] : false;
        }

        // Handle "0 && 'foo'" or "1 && 'bar'"
        if (preg_match('/^(\d+)\s*&&\s*[\'"]([^\'"]*)[\'"]$/', $value, $m)) {
            return ((int)$m[1]) ? $m[2] : false;
        }

        return $value;
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
        foreach ($test['assertions'] as $assertion) {
            $func = $assertion['function'];
            $args = $assertion['args'];
            $expected = $assertion['expected'];

            // Call the appropriate function
            switch ($func) {
                case 'twMerge':
                    $result = twMerge(...$args);
                    break;

                case 'twJoin':
                    $result = twJoin(...$args);
                    break;

                default:
                    // Validator functions
                    if (function_exists(__NAMESPACE__ . '\\' . $func)) {
                        $fn = __NAMESPACE__ . '\\' . $func;
                        $result = $fn(...$args);
                    } else {
                        $this->markTestSkipped("Unknown function: $func");
                        return;
                    }
                    break;
            }

            $this->assertSame(
                $expected,
                $result,
                sprintf(
                    "Test '%s': %s(%s) should return %s, got %s",
                    $test['name'],
                    $func,
                    json_encode($args),
                    json_encode($expected),
                    json_encode($result)
                )
            );
        }
    }
}
