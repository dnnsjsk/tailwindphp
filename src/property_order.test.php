<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use const TailwindPHP\PropertyOrder\PROPERTY_ORDER;

/**
 * Tests for property-order.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class property_order extends TestCase
{
    #[Test]
    public function property_order_constant_exists(): void
    {
        $this->assertIsArray(PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_has_many_properties(): void
    {
        // Should have hundreds of properties
        $this->assertGreaterThan(100, count(PROPERTY_ORDER));
    }

    #[Test]
    public function property_order_contains_display(): void
    {
        $this->assertContains('display', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_contains_position(): void
    {
        $this->assertContains('position', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_contains_margin(): void
    {
        $this->assertContains('margin', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_contains_padding(): void
    {
        $this->assertContains('padding', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_contains_flex(): void
    {
        $this->assertContains('flex', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_contains_grid_properties(): void
    {
        $this->assertContains('grid-column', PROPERTY_ORDER);
        $this->assertContains('grid-row', PROPERTY_ORDER);
        $this->assertContains('grid-template-columns', PROPERTY_ORDER);
        $this->assertContains('grid-template-rows', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_contains_tw_custom_properties(): void
    {
        // Should contain Tailwind-specific custom properties
        $this->assertContains('--tw-translate-x', PROPERTY_ORDER);
        $this->assertContains('--tw-shadow', PROPERTY_ORDER);
        $this->assertContains('--tw-blur', PROPERTY_ORDER);
    }

    #[Test]
    public function property_order_position_before_margin(): void
    {
        $positionIndex = array_search('position', PROPERTY_ORDER);
        $marginIndex = array_search('margin', PROPERTY_ORDER);

        $this->assertLessThan($marginIndex, $positionIndex);
    }

    #[Test]
    public function property_order_margin_before_padding(): void
    {
        $marginIndex = array_search('margin', PROPERTY_ORDER);
        $paddingIndex = array_search('padding', PROPERTY_ORDER);

        $this->assertLessThan($paddingIndex, $marginIndex);
    }

    #[Test]
    public function property_order_display_before_width(): void
    {
        $displayIndex = array_search('display', PROPERTY_ORDER);
        $widthIndex = array_search('width', PROPERTY_ORDER);

        $this->assertLessThan($widthIndex, $displayIndex);
    }

    #[Test]
    public function property_order_all_strings(): void
    {
        foreach (PROPERTY_ORDER as $property) {
            $this->assertIsString($property);
        }
    }

    #[Test]
    public function property_order_no_duplicates(): void
    {
        $unique = array_unique(PROPERTY_ORDER);
        $this->assertCount(count(PROPERTY_ORDER), $unique);
    }
}
