# Benchmark Results

Comparison between PHP and TypeScript implementations.

> **Note:** These are indicative benchmarks. Direct comparison is not apples-to-apples due to language runtime differences. The goal is to ensure PHP performance is reasonable, not to match V8/JIT performance.

## Test Environment

- **PHP:** 8.4.5 (Apple M1)
- **Node:** v20.x (Apple M1)
- **Date:** 2024-12-01

---

## Segment Utility

Splits strings by separator while respecting brackets/quotes.

| Implementation | ops/sec | mean |
|----------------|---------|------|
| PHP | ~63,000 | 16μs |
| TypeScript | ~500,000+ | <2μs |

**Note:** TypeScript/V8 has significant JIT optimization advantages for string operations.

---

## CSS Parser

Parses `preflight.css` (~400 lines).

| Implementation | ops/sec | mean |
|----------------|---------|------|
| PHP | ~765 | 1.3ms |
| TypeScript | ~5,000+ | ~0.2ms |

**Note:** The TypeScript implementation uses a character-by-character approach optimized for V8. Our PHP port follows the same logic.

---

## AST Operations

### toCss (AST to CSS string)

| Implementation | ops/sec | mean |
|----------------|---------|------|
| PHP | ~546,000 | 1.8μs |
| TypeScript | ~1,000,000+ | <1μs |

### cloneAstNode

| Implementation | ops/sec | mean |
|----------------|---------|------|
| PHP | ~825,000 | 1.2μs |
| TypeScript | ~2,000,000+ | <0.5μs |

---

## Utility Compilation

Compiling utility classes to CSS.

| Test Case | PHP ops/sec | Notes |
|-----------|-------------|-------|
| Single utility (flex) | ~335,000 | Static utility, fastest |
| Single with value (p-4) | ~166,000 | Requires theme lookup |
| Arbitrary value (w-[100px]) | ~214,000 | No theme lookup needed |
| 15 simple utilities | ~11,000 | Batch processing |
| 12 realistic component | ~16,000 | Mixed utility types |

---

## Running Benchmarks

### PHP

```bash
# All benchmarks
php benchmarks/run-all.php

# Individual
php src/utils/segment.bench.php
php src/css-parser.bench.php
php src/ast.bench.php
php src/utilities.bench.php
```

### TypeScript (in reference repo)

```bash
cd reference/tailwindcss
pnpm install
pnpm vitest bench packages/tailwindcss/src/utils/segment.bench.ts
```

---

## Conclusions

1. **PHP is ~5-10x slower** for most operations compared to TypeScript/V8
2. This is expected due to JIT compilation and V8 optimizations
3. PHP performance is still **very fast** for real-world usage:
   - Single utility: ~3μs
   - 15 utilities: ~90μs
   - Full page (100s of utilities): <10ms
4. For production, consider caching compiled CSS
