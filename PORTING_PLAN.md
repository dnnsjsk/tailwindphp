# Tailwind PHP - Porting Plan

A full 1:1 port of TailwindCSS 4.0 to PHP, focusing on the CSS-first approach (no JS config).

## Overview

**Goal:** Create a Composer package that compiles Tailwind CSS using pure PHP.

**Simple API:**
```php
use TailwindPHP\TailwindPHP;

// Extract classes from HTML/content and generate CSS
$css = TailwindPHP::generate('<div class="flex items-center p-4 bg-blue-500">...</div>');
```

**Scope:**
- ✅ CSS-only configuration (`@theme`, `@utility`, `@variant`, etc.)
- ✅ All core utilities and variants
- ✅ `@apply` directive
- ✅ `theme()` function
- ❌ JS config files (removed)
- ❌ JS plugins (removed)
- ❌ `@plugin` directive (removed)
- ❌ `@config` directive (removed)
- ❌ `content` option / file scanning (not needed - pass content directly)

---

## Package Structure (1:1 with Tailwind)

```
tailwind-php/
├── src/
│   ├── index.php                    # Main entry (from index.ts)
│   ├── ast.php                      # AST nodes & toCss (from ast.ts)
│   ├── apply.php                    # @apply processing (from apply.ts)
│   ├── at-import.php                # @import handling (from at-import.ts)
│   ├── candidate.php                # Class name parsing (from candidate.ts)
│   ├── canonicalize-candidates.php  # Canonicalization (from canonicalize-candidates.ts)
│   ├── compile.php                  # Candidate compilation (from compile.ts)
│   ├── css-functions.php            # theme(), --alpha(), etc (from css-functions.ts)
│   ├── css-parser.php               # CSS tokenizer (from css-parser.ts)
│   ├── design-system.php            # Central registry (from design-system.ts)
│   ├── property-order.php           # CSS property ordering (from property-order.ts)
│   ├── selector-parser.php          # Selector parsing (from selector-parser.ts)
│   ├── sort.php                     # Sorting utilities (from sort.ts)
│   ├── theme.php                    # Theme management (from theme.ts)
│   ├── utilities.php                # All utilities (from utilities.ts)
│   ├── value-parser.php             # Value parsing (from value-parser.ts)
│   ├── variants.php                 # All variants (from variants.ts)
│   ├── walk.php                     # AST traversal (from walk.ts)
│   │
│   └── utils/
│       ├── brace-expansion.php      # Brace expansion
│       ├── compare.php              # Comparison utilities
│       ├── compare-breakpoints.php  # Breakpoint comparison
│       ├── decode-arbitrary-value.php
│       ├── default-map.php          # Lazy cache map
│       ├── dimensions.php           # Dimension parsing
│       ├── escape.php               # CSS escaping
│       ├── infer-data-type.php      # Type inference
│       ├── is-valid-arbitrary.php   # Arbitrary validation
│       ├── math-operators.php       # Math operations
│       ├── replace-shadow-colors.php
│       ├── segment.php              # String segmentation
│       ├── to-key-path.php          # Key path parsing
│       └── topological-sort.php     # Topological sorting
│
├── resources/
│   ├── preflight.css                # Preflight styles
│   ├── theme.css                    # Default theme
│   └── utilities.css                # Core utility definitions
│
├── tests/
│   ├── ast.test.php                 # from ast.test.ts
│   ├── apply.test.php               # (if needed, tests in index.test.ts)
│   ├── at-import.test.php           # from at-import.test.ts
│   ├── candidate.test.php           # from candidate.test.ts
│   ├── canonicalize-candidates.test.php
│   ├── css-functions.test.php       # from css-functions.test.ts
│   ├── css-parser.test.php          # from css-parser.test.ts
│   ├── index.test.php               # from index.test.ts (main integration)
│   ├── selector-parser.test.php     # from selector-parser.test.ts
│   ├── sort.test.php                # from sort.test.ts
│   ├── utilities.test.php           # from utilities.test.ts
│   ├── value-parser.test.php        # from value-parser.test.ts
│   ├── variants.test.php            # from variants.test.ts
│   ├── walk.test.php                # from walk.test.ts
│   │
│   └── utils/
│       ├── brace-expansion.test.php
│       ├── compare.test.php
│       ├── decode-arbitrary-value.test.php
│       ├── escape.test.php
│       ├── replace-shadow-colors.test.php
│       ├── segment.test.php
│       └── to-key-path.test.php
│
├── composer.json
├── phpunit.xml
└── README.md
```

---

## File Mapping (1:1)

| TypeScript Source | PHP Target | Test File |
|-------------------|------------|-----------|
| `index.ts` | `src/index.php` | `tests/index.test.php` |
| `ast.ts` | `src/ast.php` | `tests/ast.test.php` |
| `apply.ts` | `src/apply.php` | (in index.test.php) |
| `at-import.ts` | `src/at-import.php` | `tests/at-import.test.php` |
| `candidate.ts` | `src/candidate.php` | `tests/candidate.test.php` |
| `canonicalize-candidates.ts` | `src/canonicalize-candidates.php` | `tests/canonicalize-candidates.test.php` |
| `compile.ts` | `src/compile.php` | (in index.test.php) |
| `css-functions.ts` | `src/css-functions.php` | `tests/css-functions.test.php` |
| `css-parser.ts` | `src/css-parser.php` | `tests/css-parser.test.php` |
| `design-system.ts` | `src/design-system.php` | (in index.test.php) |
| `property-order.ts` | `src/property-order.php` | - |
| `selector-parser.ts` | `src/selector-parser.php` | `tests/selector-parser.test.php` |
| `sort.ts` | `src/sort.php` | `tests/sort.test.php` |
| `theme.ts` | `src/theme.php` | (in index.test.php) |
| `utilities.ts` | `src/utilities.php` | `tests/utilities.test.php` |
| `value-parser.ts` | `src/value-parser.php` | `tests/value-parser.test.php` |
| `variants.ts` | `src/variants.php` | `tests/variants.test.php` |
| `walk.ts` | `src/walk.php` | `tests/walk.test.php` |

### Utils (1:1)

| TypeScript Source | PHP Target | Test File |
|-------------------|------------|-----------|
| `utils/brace-expansion.ts` | `src/utils/brace-expansion.php` | `tests/utils/brace-expansion.test.php` |
| `utils/compare.ts` | `src/utils/compare.php` | `tests/utils/compare.test.php` |
| `utils/compare-breakpoints.ts` | `src/utils/compare-breakpoints.php` | - |
| `utils/decode-arbitrary-value.ts` | `src/utils/decode-arbitrary-value.php` | `tests/utils/decode-arbitrary-value.test.php` |
| `utils/default-map.ts` | `src/utils/default-map.php` | - |
| `utils/dimensions.ts` | `src/utils/dimensions.php` | - |
| `utils/escape.ts` | `src/utils/escape.php` | `tests/utils/escape.test.php` |
| `utils/infer-data-type.ts` | `src/utils/infer-data-type.php` | - |
| `utils/is-valid-arbitrary.ts` | `src/utils/is-valid-arbitrary.php` | - |
| `utils/math-operators.ts` | `src/utils/math-operators.php` | - |
| `utils/replace-shadow-colors.ts` | `src/utils/replace-shadow-colors.php` | `tests/utils/replace-shadow-colors.test.php` |
| `utils/segment.ts` | `src/utils/segment.php` | `tests/utils/segment.test.php` |
| `utils/to-key-path.ts` | `src/utils/to-key-path.php` | `tests/utils/to-key-path.test.php` |
| `utils/topological-sort.ts` | `src/utils/topological-sort.php` | - |

---

## Files to Skip (JS-specific)

- `compat/*` - JS config/plugin compatibility layer
- `plugin.ts` - JS plugin API
- `intellisense.ts` - Editor integration
- `source-maps/*` - Source map generation (defer)
- `*.bench.ts` - Benchmarks (can add later)
- `index.cts`, `plugin.cts` - CommonJS wrappers
- `node.d.ts` - TypeScript declarations
- `types.ts` - TypeScript types
- `feature-flags.ts` - Feature flags (minimal)
- `attribute-selector-parser.ts` - Can inline if small

---

## Public API

```php
<?php

namespace TailwindPHP;

class TailwindPHP
{
    /**
     * Generate CSS from content containing Tailwind classes.
     *
     * @param string $content HTML or any content with Tailwind classes
     * @param string|null $css Optional base CSS with @theme, @utility, etc.
     * @return string Generated CSS
     */
    public static function generate(string $content, ?string $css = null): string;
}
```

**Usage:**
```php
// Simple - just pass HTML
$css = TailwindPHP::generate('<div class="flex p-4 bg-blue-500">Hello</div>');

// With custom theme
$css = TailwindPHP::generate($html, '
    @theme {
        --color-primary: #3490dc;
    }
');

// With @apply in custom CSS
$css = TailwindPHP::generate($html, '
    @theme {
        --color-primary: #3490dc;
    }

    .btn {
        @apply px-4 py-2 rounded bg-primary text-white;
    }
');
```

---

## Porting Phases

### Phase 1: Project Setup ✅
- [x] Create `composer.json` with PSR-4 autoloading
- [x] Set up PHPUnit
- [x] Create directory structure
- [x] Copy static CSS files (preflight.css, theme.css)

### Phase 2: Foundation (Bottom-up)
Port in dependency order:

1. **Utils first** (no dependencies) ✅
   - [x] `utils/segment.php` + test
   - [x] `utils/escape.php` + test
   - [x] `utils/default-map.php`
   - [ ] `utils/decode-arbitrary-value.php` + test (depends on value-parser)
   - [x] `utils/to-key-path.php` + test
   - [x] `utils/brace-expansion.php` + test
   - [x] `utils/compare.php` + test
   - [x] `utils/compare-breakpoints.php`
   - [x] `utils/dimensions.php`
   - [x] `utils/infer-data-type.php`
   - [x] `utils/is-valid-arbitrary.php`
   - [x] `utils/is-color.php`
   - [x] `utils/math-operators.php`
   - [x] `utils/replace-shadow-colors.php`
   - [x] `utils/topological-sort.php`

2. **Core parsing**
   - [x] `css-parser.php` + test
   - [x] `ast.php` + test
   - [x] `walk.php` + test
   - [x] `value-parser.php` + test
   - [x] `selector-parser.php` + test

### Phase 3: Design System
- [ ] `theme.php`
- [ ] `design-system.php`
- [ ] `property-order.php`
- [ ] `sort.php` + test

### Phase 4: Candidates & Compilation
- [ ] `candidate.php` + test
- [ ] `canonicalize-candidates.php` + test
- [ ] `compile.php`

### Phase 5: Utilities & Variants
- [ ] `utilities.php` + test
- [ ] `variants.php` + test

### Phase 6: Directives
- [ ] `css-functions.php` + test
- [ ] `apply.php`
- [ ] `at-import.php` + test

### Phase 7: Main Entry
- [ ] `index.php` + test (integration tests)
- [ ] Public API wrapper

### Phase 8: Polish
- [ ] Full test coverage
- [ ] Compare output with original Tailwind
- [ ] Documentation
- [ ] Publish to Packagist

---

## Test Strategy

Each file gets a corresponding test file mirroring the TypeScript tests:

```php
// tests/css-parser.test.php
class CssParserTest extends TestCase
{
    public function test_parses_simple_rule()
    {
        $ast = parse('.foo { color: red }');

        $this->assertEquals([
            rule('.foo', [
                decl('color', 'red')
            ])
        ], $ast);
    }

    // ... port all tests from css-parser.test.ts
}
```

**Integration tests** in `index.test.php` cover the full pipeline - these are the most important for ensuring compatibility.

---

## Line Count Estimates

| File | TS Lines | Est. PHP Lines |
|------|----------|----------------|
| `css-parser.php` | 718 | ~800 |
| `ast.php` | 800 | ~900 |
| `candidate.php` | 900 | ~1000 |
| `canonicalize-candidates.php` | 1600 | ~1800 |
| `utilities.php` | 5700 | ~6500 |
| `variants.php` | 1100 | ~1200 |
| `index.php` | 867 | ~950 |
| `compile.php` | 368 | ~400 |
| `design-system.php` | 234 | ~300 |
| `theme.php` | 305 | ~350 |
| Utils (all) | ~1000 | ~1100 |
| **Total** | ~13,500 | ~15,300 |

---

## Next Steps

1. Set up project with composer.json and PHPUnit
2. Start with utils (smallest, no dependencies)
3. Port css-parser.php with tests
4. Build up from there
