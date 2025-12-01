<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;

/**
 * Tests for intellisense.php
 *
 * Port of: packages/tailwindcss/src/intellisense.test.ts
 *
 * N/A: Intellisense is VS Code extension functionality for autocomplete,
 * hover previews, etc. Not applicable for PHP port which focuses on
 * CSS generation.
 */
class intellisense extends TestCase
{
    public function test_not_applicable(): void
    {
        // N/A: VS Code extension functionality, not needed for PHP CSS generation
        $this->assertTrue(true);
    }
}
