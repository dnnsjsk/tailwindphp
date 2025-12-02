<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;

/**
 * Tests for canonicalize-candidates functionality
 *
 * Original: packages/tailwindcss/src/canonicalize-candidates.test.ts (1,132 lines)
 *
 * @port-deviation:omitted NOT APPLICABLE TO PHP PORT.
 *
 * The TypeScript tests verify the `canonicalizeCandidates` function which is
 * used by IDE tooling (Prettier plugin) for:
 * - Converting arbitrary properties to utility classes
 * - Migrating legacy class names to modern equivalents
 * - Sorting and normalizing class names
 *
 * This is IDE/tooling functionality, not CSS generation. The PHP port focuses
 * on compiling Tailwind classes to CSS, not on class name manipulation for
 * code formatting tools.
 */
class canonicalize_candidates extends TestCase
{
    public function test_not_applicable(): void
    {
        // This test file intentionally has no tests.
        // See class docblock for explanation.
        $this->assertTrue(true, 'Canonicalize candidates tests are IDE tooling - not applicable to PHP port');
    }
}
