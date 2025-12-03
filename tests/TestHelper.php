<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use function TailwindPHP\compile;

/**
 * Test helper for running utility tests.
 *
 * Port of: packages/tailwindcss/src/test-utils/run.ts
 *
 * This helper uses the compile() function to match the real Tailwind
 * compilation flow including variants support.
 */
class TestHelper
{
    /**
     * Reset the test helper (for compatibility - no-op now).
     */
    public static function reset(): void
    {
        // No-op - we compile fresh each time now
    }

    /**
     * Run utilities and generate CSS for the given candidates.
     *
     * This is the main test function that mirrors test-utils/run.ts
     *
     * @param array<string> $candidates Array of class names
     * @return string Generated CSS
     */
    public static function run(array $candidates): string
    {
        // Compile fresh each time to avoid candidate accumulation
        // Spec tests provide their own @theme in CSS, so don't load default theme
        $compiled = compile('@import "tailwindcss/utilities.css";', ['loadDefaultTheme' => false]);
        $css = $compiled['build']($candidates);

        return trim($css);
    }
}
