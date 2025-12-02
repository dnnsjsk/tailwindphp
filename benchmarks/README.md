# Benchmarks

Performance comparison between TailwindPHP (PHP) and TailwindCSS (TypeScript).

## Running Benchmarks

```bash
# Run all benchmarks (PHP + TypeScript comparison)
composer bench

# Save results to benchmarks/results.json
composer bench:save

# Run specific benchmark
php scripts/benchmark.php css-parser
php scripts/benchmark.php ast

# PHP only (no TypeScript comparison)
php scripts/benchmark.php --php-only

# List available benchmarks
php scripts/benchmark.php --list
```

## Requirements

For TypeScript benchmarks, you need:
- Node.js
- pnpm (will use version from reference/tailwindcss/package.json)
- Dependencies installed: `cd reference/tailwindcss && pnpm install`

## Sample Results

PHP 8.4.5, Node.js v22.17.0 (Apple M1):

```
  Benchmark                        PHP            TypeScript      Comparison
  ───────────────────────────────────────────────────────────────────────────

  CSS Parser
  css-parser on preflight.css      816 ops/s      15.45K ops/s    TS 19x faster

  AST Operations
  toCss                            65.85K ops/s   2.59M ops/s     TS 39x faster
```

TypeScript is significantly faster due to V8's JIT compilation. This is expected - the PHP implementation prioritizes correctness and maintainability over raw performance.

For build-time CSS generation (the intended use case), PHP performance is adequate.

## Optimizations Applied

The PHP version includes performance optimizations that maintain identical output:

- **toCss**: Array accumulation + implode instead of string concatenation, pre-computed indent strings (~50% faster)
- **CSS Parser**: Direct character comparison instead of ord() calls, tracked buffer lengths (~20-30% faster)
