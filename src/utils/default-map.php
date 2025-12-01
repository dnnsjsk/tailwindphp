<?php

declare(strict_types=1);

namespace TailwindPHP\Utils;

/**
 * A Map that can generate default values for keys that don't exist.
 * Generated default values are added to the map to avoid recomputation.
 *
 * @template TKey
 * @template TValue
 */
class DefaultMap
{
    /**
     * @var array<TKey, TValue>
     */
    private array $map = [];

    /**
     * @var callable(TKey, DefaultMap<TKey, TValue>): TValue
     */
    private $factory;

    /**
     * @param callable(TKey, DefaultMap<TKey, TValue>): TValue $factory
     */
    public function __construct(callable $factory)
    {
        $this->factory = $factory;
    }

    /**
     * @param TKey $key
     * @return TValue
     */
    public function get(mixed $key): mixed
    {
        if (!array_key_exists($key, $this->map)) {
            $this->map[$key] = ($this->factory)($key, $this);
        }

        return $this->map[$key];
    }

    /**
     * @param TKey $key
     * @param TValue $value
     */
    public function set(mixed $key, mixed $value): void
    {
        $this->map[$key] = $value;
    }

    /**
     * @param TKey $key
     * @return bool
     */
    public function has(mixed $key): bool
    {
        return array_key_exists($key, $this->map);
    }

    /**
     * @param TKey $key
     */
    public function delete(mixed $key): void
    {
        unset($this->map[$key]);
    }

    public function clear(): void
    {
        $this->map = [];
    }

    public function size(): int
    {
        return count($this->map);
    }

    /**
     * @return array<TKey, TValue>
     */
    public function entries(): array
    {
        return $this->map;
    }
}
