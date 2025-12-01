<?php

declare(strict_types=1);

namespace TailwindPHP\Benchmarks;

/**
 * Simple benchmarking utility for PHP.
 *
 * Mimics vitest's bench() API for comparable results with TypeScript benchmarks.
 */
class Benchmark
{
    private array $results = [];
    private string $currentGroup = '';

    /**
     * Run a benchmark.
     *
     * @param string $name Benchmark name
     * @param callable $fn Function to benchmark
     * @param int $iterations Number of iterations (default: 1000)
     */
    public function bench(string $name, callable $fn, int $iterations = 1000): void
    {
        // Warm up
        for ($i = 0; $i < 10; $i++) {
            $fn();
        }

        // Collect samples
        $times = [];
        for ($i = 0; $i < $iterations; $i++) {
            $start = hrtime(true);
            $fn();
            $end = hrtime(true);
            $times[] = ($end - $start) / 1e6; // Convert to milliseconds
        }

        sort($times);

        // Calculate statistics
        $mean = array_sum($times) / count($times);
        $median = $times[(int)(count($times) / 2)];
        $min = $times[0];
        $max = $times[count($times) - 1];

        // Standard deviation
        $variance = array_sum(array_map(fn($t) => ($t - $mean) ** 2, $times)) / count($times);
        $stddev = sqrt($variance);

        // Ops per second
        $opsPerSec = 1000 / $mean;

        $this->results[] = [
            'group' => $this->currentGroup,
            'name' => $name,
            'iterations' => $iterations,
            'mean' => $mean,
            'median' => $median,
            'min' => $min,
            'max' => $max,
            'stddev' => $stddev,
            'ops_per_sec' => $opsPerSec,
        ];
    }

    /**
     * Group benchmarks together.
     *
     * @param string $name Group name
     * @param callable $fn Function containing bench() calls
     */
    public function describe(string $name, callable $fn): void
    {
        $this->currentGroup = $name;
        $fn($this);
        $this->currentGroup = '';
    }

    /**
     * Print results to console.
     */
    public function printResults(): void
    {
        $currentGroup = '';

        foreach ($this->results as $result) {
            if ($result['group'] !== $currentGroup) {
                if ($result['group'] !== '') {
                    echo "\n{$result['group']}\n";
                    echo str_repeat('-', strlen($result['group'])) . "\n";
                }
                $currentGroup = $result['group'];
            }

            $name = str_pad($result['name'], 40);
            $ops = number_format($result['ops_per_sec'], 0);
            $mean = number_format($result['mean'] * 1000, 2); // Convert to microseconds
            $stddev = number_format($result['stddev'] * 1000, 2);

            echo "{$name} {$ops} ops/s ± {$stddev}μs (mean: {$mean}μs)\n";
        }
    }

    /**
     * Get results as array (for JSON export).
     */
    public function getResults(): array
    {
        return $this->results;
    }

    /**
     * Export results to JSON file.
     */
    public function exportJson(string $filepath): void
    {
        $data = [
            'timestamp' => date('c'),
            'php_version' => PHP_VERSION,
            'results' => $this->results,
        ];

        file_put_contents($filepath, json_encode($data, JSON_PRETTY_PRINT));
    }
}
