<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\Utils\DefaultMap;

/**
 * Tests for default-map.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class default_map extends TestCase
{
    #[Test]
    public function get_creates_default_value(): void
    {
        $map = new DefaultMap(fn ($key) => $key . '_default');
        $this->assertSame('foo_default', $map->get('foo'));
    }

    #[Test]
    public function get_caches_value(): void
    {
        $callCount = 0;
        $map = new DefaultMap(function ($key) use (&$callCount) {
            $callCount++;

            return $key . '_' . $callCount;
        });

        $first = $map->get('foo');
        $second = $map->get('foo');

        $this->assertSame($first, $second);
        $this->assertSame(1, $callCount);
    }

    #[Test]
    public function set_stores_value(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $map->set('foo', 'custom');
        $this->assertSame('custom', $map->get('foo'));
    }

    #[Test]
    public function has_returns_true_for_existing(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $map->set('foo', 'value');
        $this->assertTrue($map->has('foo'));
    }

    #[Test]
    public function has_returns_false_for_missing(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $this->assertFalse($map->has('foo'));
    }

    #[Test]
    public function delete_removes_value(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $map->set('foo', 'value');
        $map->delete('foo');
        $this->assertFalse($map->has('foo'));
    }

    #[Test]
    public function clear_removes_all(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $map->set('foo', 'value');
        $map->set('bar', 'value');
        $map->clear();
        $this->assertSame(0, $map->size());
    }

    #[Test]
    public function size_returns_count(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $map->set('foo', 'value');
        $map->set('bar', 'value');
        $this->assertSame(2, $map->size());
    }

    #[Test]
    public function entries_returns_all(): void
    {
        $map = new DefaultMap(fn () => 'default');
        $map->set('foo', 'value1');
        $map->set('bar', 'value2');
        $entries = $map->entries();
        $this->assertCount(2, $entries);
    }

    #[Test]
    public function handles_array_keys(): void
    {
        $map = new DefaultMap(fn ($key) => count($key));
        $result = $map->get(['a', 'b', 'c']);
        $this->assertSame(3, $result);
    }

    #[Test]
    public function factory_receives_map_as_second_arg(): void
    {
        $map = new DefaultMap(function ($key, $mapInstance) {
            return $mapInstance instanceof DefaultMap;
        });
        $this->assertTrue($map->get('foo'));
    }
}
