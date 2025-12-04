<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\Utils\compareBreakpoints;

/**
 * Tests for compare-breakpoints.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class compare_breakpoints extends TestCase
{
    #[Test]
    public function equal_values_return_zero(): void
    {
        $this->assertSame(0, compareBreakpoints('100px', '100px', 'asc'));
        $this->assertSame(0, compareBreakpoints('100px', '100px', 'desc'));
    }

    #[Test]
    public function ascending_order_smaller_first(): void
    {
        $this->assertLessThan(0, compareBreakpoints('100px', '200px', 'asc'));
        $this->assertGreaterThan(0, compareBreakpoints('200px', '100px', 'asc'));
    }

    #[Test]
    public function descending_order_larger_first(): void
    {
        $this->assertGreaterThan(0, compareBreakpoints('100px', '200px', 'desc'));
        $this->assertLessThan(0, compareBreakpoints('200px', '100px', 'desc'));
    }

    #[Test]
    public function different_units_compared_by_unit_name(): void
    {
        // px vs rem - different buckets, sorted alphabetically by unit
        $result = compareBreakpoints('100px', '10rem', 'asc');
        $this->assertNotEquals(0, $result);
    }

    #[Test]
    public function css_function_bucketed_by_name(): void
    {
        // calc( values are bucketed by function name
        $result = compareBreakpoints('calc(100% - 1rem)', 'calc(50% + 2rem)', 'asc');
        // Both have same bucket, so compares by value
        $this->assertIsInt($result);
    }

    #[Test]
    public function different_css_functions_compared(): void
    {
        // Different function names
        $result = compareBreakpoints('calc(100px)', 'min(100px, 200px)', 'asc');
        $this->assertNotEquals(0, $result);
    }

    #[Test]
    public function handles_rem_units(): void
    {
        $this->assertLessThan(0, compareBreakpoints('1rem', '2rem', 'asc'));
        $this->assertGreaterThan(0, compareBreakpoints('2rem', '1rem', 'asc'));
    }

    #[Test]
    public function handles_em_units(): void
    {
        $this->assertLessThan(0, compareBreakpoints('1em', '2em', 'asc'));
    }

    #[Test]
    public function handles_unitless_values(): void
    {
        $this->assertLessThan(0, compareBreakpoints('100', '200', 'asc'));
        $this->assertGreaterThan(0, compareBreakpoints('200', '100', 'asc'));
    }
}
