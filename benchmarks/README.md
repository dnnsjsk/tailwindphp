# Benchmarks

Performance benchmarks for TailwindPHP, structured to match the original TailwindCSS TypeScript benchmarks.

## Running Benchmarks

```bash
# Run all benchmarks
php benchmarks/run-all.php

# Run individual benchmarks
php src/utils/segment.bench.php
php src/css-parser.bench.php
php src/ast.bench.php
php src/utilities.bench.php
```

## Benchmark Files

Benchmark files are located alongside their source files in `src/`, mirroring the TailwindCSS structure:

| PHP Benchmark | TypeScript Equivalent |
|---------------|----------------------|
| `src/utils/segment.bench.php` | `src/utils/segment.bench.ts` |
| `src/css-parser.bench.php` | `src/css-parser.bench.ts` |
| `src/ast.bench.php` | `src/ast.bench.ts` |
| `src/utilities.bench.php` | `src/index.bench.ts` (partial) |

## Comparing with TypeScript

To run the TypeScript benchmarks in the reference implementation:

```bash
cd reference/tailwindcss
pnpm install
pnpm vitest bench
```

## Sample Results

PHP 8.4.5 (Apple M1):

```
Segment Utility
---------------
segment                                  62,957 ops/s

CSS Parser
----------
css-parser on preflight.css              765 ops/s

AST Operations
--------------
toCss                                    546,301 ops/s
cloneAstNode()                           824,920 ops/s

Utility Compilation
-------------------
Simple utilities (15 classes)            11,073 ops/s
Single utility (flex)                    335,426 ops/s
```

Note: Direct comparison with TypeScript/V8 performance is not apples-to-apples, but these benchmarks help ensure we're not introducing performance regressions in the PHP implementation.
