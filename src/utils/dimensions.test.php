<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Utils\Dimensions as DimensionsClass;

use function TailwindPHP\Utils\parseDimension;

/**
 * Tests for dimensions.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class dimensions extends TestCase
{
    // =========================================================================
    // parseDimension tests
    // =========================================================================

    #[Test]
    public function parse_dimension_with_rem(): void
    {
        $result = parseDimension('64rem');
        $this->assertSame([64.0, 'rem'], $result);
    }

    #[Test]
    public function parse_dimension_with_px(): void
    {
        $result = parseDimension('100px');
        $this->assertSame([100.0, 'px'], $result);
    }

    #[Test]
    public function parse_dimension_with_em(): void
    {
        $result = parseDimension('2.5em');
        $this->assertSame([2.5, 'em'], $result);
    }

    #[Test]
    public function parse_dimension_with_percent(): void
    {
        $result = parseDimension('50%');
        $this->assertSame([50.0, '%'], $result);
    }

    #[Test]
    public function parse_dimension_unitless(): void
    {
        $result = parseDimension('100');
        $this->assertSame([100.0, null], $result);
    }

    #[Test]
    public function parse_dimension_decimal(): void
    {
        $result = parseDimension('1.5');
        $this->assertSame([1.5, null], $result);
    }

    #[Test]
    public function parse_dimension_negative(): void
    {
        $result = parseDimension('-10px');
        $this->assertSame([-10.0, 'px'], $result);
    }

    #[Test]
    public function parse_dimension_positive_sign(): void
    {
        $result = parseDimension('+10px');
        $this->assertSame([10.0, 'px'], $result);
    }

    #[Test]
    public function parse_dimension_invalid(): void
    {
        $result = parseDimension('invalid');
        $this->assertNull($result);
    }

    #[Test]
    public function parse_dimension_empty(): void
    {
        $result = parseDimension('');
        $this->assertNull($result);
    }

    // =========================================================================
    // Dimensions class tests
    // =========================================================================

    #[Test]
    public function dimensions_get_caches(): void
    {
        $result1 = DimensionsClass::get('100px');
        $result2 = DimensionsClass::get('100px');
        $this->assertSame($result1, $result2);
    }

    #[Test]
    public function dimensions_get_returns_parsed(): void
    {
        $result = DimensionsClass::get('2rem');
        $this->assertSame([2.0, 'rem'], $result);
    }
}
