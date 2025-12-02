<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use TailwindPHP\Tests\TestHelper;

/**
 * Tests for index.php
 *
 * Port of: packages/tailwindcss/src/index.test.ts
 *
 * These tests are loaded from JSON files extracted from the TypeScript test suite.
 */
class index extends TestCase
{
    private static array $testCases = [];
    private static bool $loaded = false;

    /**
     * Load all test cases from JSON files.
     */
    private static function loadTestCases(): void
    {
        if (self::$loaded) {
            return;
        }

        $testDir = __DIR__ . '/../test-coverage/index/tests';
        $jsonFiles = glob($testDir . '/*.json');

        foreach ($jsonFiles as $file) {
            $category = basename($file, '.json');
            $tests = json_decode(file_get_contents($file), true);

            if (!is_array($tests)) {
                continue;
            }

            foreach ($tests as $i => $test) {
                $key = $category . '::' . ($test['name'] ?? "test_$i");
                self::$testCases[$key] = array_merge($test, ['category' => $category]);
            }
        }

        self::$loaded = true;
    }

    /**
     * Data provider for index tests.
     */
    public static function indexTestProvider(): array
    {
        self::loadTestCases();

        $data = [];
        foreach (self::$testCases as $key => $test) {
            $data[$key] = [$test];
        }

        return $data;
    }

    /**
     * Run a single index test case.
     */
    #[DataProvider('indexTestProvider')]
    public function test_index(array $test): void
    {
        $name = $test['name'] ?? 'unknown';
        $type = $test['type'] ?? 'run';
        $classes = $test['classes'] ?? [];
        $css = $test['css'] ?? null;
        $expected = $test['expected'] ?? '';

        // Parse the expected string
        // The expected value is stored as a quoted string literal from TypeScript snapshots
        // e.g., '".selector { ... }"' - we need to strip the outer quotes
        if (is_string($expected) && strlen($expected) >= 2) {
            $firstChar = $expected[0];
            $lastChar = $expected[strlen($expected) - 1];
            if (($firstChar === '"' && $lastChar === '"') || ($firstChar === "'" && $lastChar === "'")) {
                $expected = substr($expected, 1, -1);
            }
        }

        if ($type === 'run') {
            // Simple run test - uses TestHelper::run()
            $actual = TestHelper::run($classes);
        } elseif ($type === 'compileCss') {
            // Compile with custom CSS
            if ($css === null) {
                $css = '@tailwind utilities;';
            }
            $compiled = compile($css);
            $actual = $compiled['build']($classes);
        } else {
            $this->markTestSkipped("Unknown test type: $type");
            return;
        }

        // Normalize both for comparison
        $normalizedExpected = self::normalizeCss($expected);
        $normalizedActual = self::normalizeCss($actual);

        $this->assertEquals(
            $normalizedExpected,
            $normalizedActual,
            "Test '$name' failed.\n\nExpected:\n$expected\n\nActual:\n$actual"
        );
    }

    /**
     * Normalize CSS for comparison.
     */
    private static function normalizeCss(string $css): string
    {
        // Normalize whitespace
        $css = preg_replace('/\s+/', ' ', $css);
        $css = preg_replace('/\s*{\s*/', ' { ', $css);
        $css = preg_replace('/\s*}\s*/', ' } ', $css);
        $css = preg_replace('/\s*;\s*/', '; ', $css);
        // Only normalize colons in property-value pairs (after a property name, not in selectors)
        $css = preg_replace('/(\w)\s*:\s*(\S)/', '$1: $2', $css);
        $css = preg_replace('/\s*,\s*/', ', ', $css);

        // Remove extra spaces
        $css = preg_replace('/\s+/', ' ', $css);

        // Normalize leading zeros
        $css = preg_replace('/\b0+(\.\d+)/', '$1', $css);

        // Normalize CSS escape sequences - double backslash to single
        $css = str_replace('\\\\', '\\', $css);

        // Remove wrapping quotes if present (from JSON encoding)
        $css = trim($css, '"\'');

        return trim($css);
    }
}
