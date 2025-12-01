<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;

/**
 * Tests for sort.php
 *
 * Port of: packages/tailwindcss/src/sort.test.ts
 *
 * N/A: getClassOrder() is used by Prettier plugin for sorting class names
 * in source code. Not needed for PHP port which focuses on CSS generation.
 * The core CSS output sorting happens in compileCandidates().
 */
class sort extends TestCase
{
    public function test_not_applicable(): void
    {
        // N/A: Prettier plugin functionality, not needed for PHP CSS generation
        $this->assertTrue(true);
    }
}
