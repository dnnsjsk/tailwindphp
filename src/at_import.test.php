<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;

/**
 * Tests for at-import functionality
 *
 * Original: packages/tailwindcss/src/at-import.test.ts (636 lines)
 *
 * @port-deviation:omitted NOT APPLICABLE TO PHP PORT.
 *
 * The TypeScript tests verify async file resolution via `loadStylesheet` and
 * `loadModule` callbacks - functionality used by build tools (Vite, PostCSS).
 *
 * The PHP port handles @import differently:
 * - CSS content is provided directly to compile()
 * - File resolution is handled by the calling application
 * - @import directives are processed inline during compilation
 *
 * The core @import parsing and @media/@supports/@layer wrapping is tested
 * indirectly through index.test.php integration tests.
 */
class at_import extends TestCase
{
    public function test_not_applicable(): void
    {
        // This test file intentionally has no tests.
        // See class docblock for explanation.
        $this->assertTrue(true, '@import async file resolution tests are not applicable to PHP port');
    }
}
