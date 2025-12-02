<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;

/**
 * Tests for plugin.php
 *
 * Port of: packages/tailwindcss/src/plugin.test.ts
 *
 * N/A: Tests @plugin directive which loads JS modules. This is the JavaScript
 * plugin API, not applicable for CSS-only PHP port.
 */
class plugin extends TestCase
{
    public function test_not_applicable(): void
    {
        // N/A: JavaScript plugin API, not needed for CSS-only PHP port
        $this->assertTrue(true);
    }
}
