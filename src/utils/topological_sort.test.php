<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\Utils\topologicalSort;

/**
 * Tests for topological-sort.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class topological_sort extends TestCase
{
    #[Test]
    public function sorts_simple_graph(): void
    {
        $graph = [
            'a' => [],
            'b' => ['a'],
            'c' => ['b'],
        ];

        $result = topologicalSort($graph, fn () => null);

        // a has no deps, should come first
        // b depends on a
        // c depends on b
        $this->assertSame(['a', 'b', 'c'], $result);
    }

    #[Test]
    public function handles_multiple_dependencies(): void
    {
        $graph = [
            'a' => [],
            'b' => [],
            'c' => ['a', 'b'],
        ];

        $result = topologicalSort($graph, fn () => null);

        // a and b have no deps, c depends on both
        $cIndex = array_search('c', $result);
        $aIndex = array_search('a', $result);
        $bIndex = array_search('b', $result);

        $this->assertGreaterThan($aIndex, $cIndex);
        $this->assertGreaterThan($bIndex, $cIndex);
    }

    #[Test]
    public function handles_empty_graph(): void
    {
        $result = topologicalSort([], fn () => null);
        $this->assertSame([], $result);
    }

    #[Test]
    public function handles_single_node(): void
    {
        $graph = ['a' => []];
        $result = topologicalSort($graph, fn () => null);
        $this->assertSame(['a'], $result);
    }

    #[Test]
    public function handles_disconnected_nodes(): void
    {
        $graph = [
            'a' => [],
            'b' => [],
            'c' => [],
        ];

        $result = topologicalSort($graph, fn () => null);
        $this->assertCount(3, $result);
        $this->assertContains('a', $result);
        $this->assertContains('b', $result);
        $this->assertContains('c', $result);
    }

    #[Test]
    public function detects_circular_dependency(): void
    {
        $graph = [
            'a' => ['b'],
            'b' => ['a'],
        ];

        $circularDetected = false;
        $circularPath = [];

        topologicalSort($graph, function ($path, $node) use (&$circularDetected, &$circularPath) {
            $circularDetected = true;
            $circularPath = array_merge($path, [$node]);
        });

        $this->assertTrue($circularDetected);
    }

    #[Test]
    public function handles_complex_graph(): void
    {
        // Diamond dependency pattern
        $graph = [
            'a' => [],
            'b' => ['a'],
            'c' => ['a'],
            'd' => ['b', 'c'],
        ];

        $result = topologicalSort($graph, fn () => null);

        $aIndex = array_search('a', $result);
        $bIndex = array_search('b', $result);
        $cIndex = array_search('c', $result);
        $dIndex = array_search('d', $result);

        $this->assertLessThan($bIndex, $aIndex);
        $this->assertLessThan($cIndex, $aIndex);
        $this->assertLessThan($dIndex, $bIndex);
        $this->assertLessThan($dIndex, $cIndex);
    }

    #[Test]
    public function ignores_unknown_dependencies(): void
    {
        $graph = [
            'a' => ['unknown'],
            'b' => ['a'],
        ];

        $result = topologicalSort($graph, fn () => null);

        // Should still sort correctly, ignoring unknown deps
        $aIndex = array_search('a', $result);
        $bIndex = array_search('b', $result);
        $this->assertLessThan($bIndex, $aIndex);
    }
}
