<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use TailwindPHP\Theme;
use TailwindPHP\DefaultTheme;
use TailwindPHP\CandidateParser;
use TailwindPHP\CssFormatter;
use TailwindPHP\Utilities\Utilities;
use function TailwindPHP\createUtilities;
use function TailwindPHP\Utils\escape;

/**
 * Test helper for running utility tests.
 *
 * Port of: packages/tailwindcss/src/test-utils/run.ts
 *
 * This helper uses the real Utilities registry and Theme with
 * CandidateParser for parsing and CssFormatter for output.
 */
class TestHelper
{
    private static ?Theme $theme = null;
    private static ?Utilities $utilities = null;
    private static ?CandidateParser $parser = null;

    /**
     * Get or create the shared Theme instance.
     */
    private static function getTheme(): Theme
    {
        if (self::$theme === null) {
            self::$theme = DefaultTheme::create();
        }
        return self::$theme;
    }

    /**
     * Get or create the shared Utilities instance.
     */
    private static function getUtilities(): Utilities
    {
        if (self::$utilities === null) {
            self::$utilities = createUtilities(self::getTheme());
        }
        return self::$utilities;
    }

    /**
     * Get or create the shared CandidateParser instance.
     */
    private static function getParser(): CandidateParser
    {
        if (self::$parser === null) {
            self::$parser = new CandidateParser(self::getUtilities());
        }
        return self::$parser;
    }

    /**
     * Reset the test helper (useful between tests if needed).
     */
    public static function reset(): void
    {
        self::$theme = null;
        self::$utilities = null;
        self::$parser = null;
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
        $utilities = self::getUtilities();
        $parser = self::getParser();
        $css = [];

        foreach ($candidates as $candidate) {
            $result = self::compileCandidate($candidate, $utilities, $parser);
            if ($result !== null) {
                $css[] = $result;
            }
        }

        // Sort CSS rules alphabetically by selector for consistent output
        usort($css, fn($a, $b) => strcmp($a['selector'], $b['selector']));

        return CssFormatter::format($css);
    }

    /**
     * Compile a single candidate to CSS.
     */
    private static function compileCandidate(string $candidate, Utilities $utilities, CandidateParser $parser): ?array
    {
        $parsed = $parser->parse($candidate);

        if ($parsed === null) {
            return null;
        }

        $important = $parsed['important'];

        if ($parsed['kind'] === 'static') {
            $utils = $utilities->get($parsed['root']);
            foreach ($utils as $util) {
                if ($util['kind'] === 'static') {
                    $nodes = $util['compileFn']();
                    if ($nodes !== null) {
                        return [
                            'selector' => '.' . escape($candidate),
                            'nodes' => $nodes,
                            'important' => $important,
                        ];
                    }
                }
            }
        } elseif ($parsed['kind'] === 'functional') {
            $utils = $utilities->get($parsed['root']);
            foreach ($utils as $util) {
                if ($util['kind'] === 'functional') {
                    $nodes = $util['compileFn']($parsed);
                    if ($nodes !== null && $nodes !== false) {
                        return [
                            'selector' => '.' . escape($candidate),
                            'nodes' => $nodes,
                            'important' => $important,
                        ];
                    }
                }
            }
        }

        return null;
    }
}
