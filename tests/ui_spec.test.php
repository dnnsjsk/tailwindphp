<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use function TailwindPHP\compile;

/**
 * Tests extracted from ui.spec.ts (Playwright browser tests)
 *
 * Port of: packages/tailwindcss/tests/ui.spec.ts
 *
 * These tests verify that classes compile correctly and produce
 * the expected CSS properties. Note: Original tests check browser
 * computed values - we verify CSS compilation instead.
 *
 * @port-deviation:browser Original uses Playwright to check computed CSS values.
 * PHP tests verify that classes compile and produce correct CSS properties.
 */
class ui_spec extends TestCase
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

        $testDir = __DIR__ . '/../test-coverage/ui-spec/tests';
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
     * Data provider for UI spec tests.
     */
    public static function uiSpecTestProvider(): array
    {
        self::loadTestCases();

        $data = [];
        foreach (self::$testCases as $key => $test) {
            $data[$key] = [$test];
        }

        return $data;
    }

    /**
     * CSS with custom colors used in ui.spec.ts render() function
     */
    private const BASE_CSS = <<<'CSS'
@tailwind utilities;

@theme {
    --color-red: rgb(255, 0, 0);
    --color-green: rgb(0, 255, 0);
    --color-blue: rgb(0, 0, 255);
    --color-black: black;
    --color-white: white;
    --color-transparent: transparent;
}
CSS;

    /**
     * Run a single UI spec test case.
     */
    #[DataProvider('uiSpecTestProvider')]
    public function test_ui_spec(array $test): void
    {
        $name = $test['name'] ?? 'unknown';
        $type = $test['type'] ?? 'unknown';
        $classes = $test['classes'] ?? [];

        // Skip empty class lists
        if (empty($classes)) {
            $this->markTestSkipped("Test '$name' has no classes to test");
        }

        // Compile the CSS
        try {
            $compiled = compile(self::BASE_CSS);
            $css = $compiled['build']($classes);
        } catch (\Exception $e) {
            $this->fail("Test '$name' failed to compile: " . $e->getMessage());
        }

        // Verify CSS was generated (not empty)
        $this->assertNotEmpty(
            trim($css),
            "Test '$name' produced empty CSS for classes: " . implode(' ', $classes)
        );

        // For for-loop tests, verify the expected property is present
        if ($type === 'for-loop' && isset($test['property'])) {
            $property = $test['property'];
            $this->assertPropertyInCss(
                $css,
                $property,
                "Test '$name': Expected property '$property' not found in CSS"
            );
        }

        // For standalone tests, verify each expected property
        if ($type === 'standalone' && isset($test['expectations'])) {
            foreach ($test['expectations'] as $exp) {
                $property = $exp['property'];
                $this->assertPropertyInCss(
                    $css,
                    $property,
                    "Test '$name': Expected property '$property' not found in CSS"
                );
            }
        }
    }

    /**
     * Assert that a CSS property exists in the output.
     */
    private function assertPropertyInCss(string $css, string $property, string $message): void
    {
        // Handle shorthand properties that may be expanded
        $propertiesToCheck = [$property];

        // Add expanded versions for shorthand properties
        $expansions = [
            'border' => ['border', 'border-width', 'border-style', 'border-color'],
            'border-right' => ['border-right', 'border-right-width', 'border-right-style', 'border-right-color'],
            'border-bottom' => ['border-bottom', 'border-bottom-width', 'border-bottom-style', 'border-bottom-color'],
            'border-left' => ['border-left', 'border-left-width', 'border-left-style', 'border-left-color'],
            'border-top' => ['border-top', 'border-top-width', 'border-top-style', 'border-top-color'],
            'outline' => ['outline', 'outline-width', 'outline-style', 'outline-color', 'outline-offset'],
            'background-image' => ['background-image', 'background', '--tw-gradient'],
            'mask-image' => ['mask-image', '-webkit-mask-image', 'mask'],
        ];

        if (isset($expansions[$property])) {
            $propertiesToCheck = array_merge($propertiesToCheck, $expansions[$property]);
        }

        $found = false;
        foreach ($propertiesToCheck as $prop) {
            // Check for the property in CSS (property: or --tw-* variable)
            if (
                str_contains($css, "$prop:") ||
                str_contains($css, "$prop ") ||
                ($property === 'background-image' && str_contains($css, 'linear-gradient')) ||
                ($property === 'background-image' && str_contains($css, 'radial-gradient')) ||
                ($property === 'background-image' && str_contains($css, 'conic-gradient')) ||
                ($property === 'mask-image' && str_contains($css, 'mask'))
            ) {
                $found = true;
                break;
            }
        }

        $this->assertTrue($found, "$message\n\nGenerated CSS:\n$css");
    }
}
