# Tailwind PHP - Development Guide

A **1:1 port of TailwindCSS 4.x to PHP**. The goal is exact parity with the JavaScript implementation - same inputs should produce identical CSS outputs.

---

## Critical Rules for LLMs

### 1. Never Modify Test Files to Make Tests Pass

**FORBIDDEN:** Changing assertions, expected values, or normalization logic in `*.test.php` files to make failing tests pass.

**ALLOWED:** Only fix test infrastructure bugs (parsing issues, file loading) - never change expected outputs.

### 2. Always Fix at the Source

When a test fails, the fix belongs in the **source code** (`src/*.php`), not in the test file. The tests define the contract - we must match TailwindCSS output exactly.

### 3. LightningCSS Functionality

TailwindCSS uses [lightningcss](https://lightningcss.dev/) (Rust) for CSS transformations. Since we can't use Rust in PHP, equivalent functionality goes in:

```
src/_tailwindphp/LightningCss.php
```

This includes:
- CSS nesting transformation (flattening `&` selectors)
- `@media` query hoisting
- `calc()` simplification
- Leading zero removal (`0.5` → `.5`)
- Transform function spacing
- Grid value normalization

### 4. The `_tailwindphp` Directory

`src/_tailwindphp/` contains PHP-specific helpers that are **NOT** part of the TailwindCSS port:
- `LightningCss.php` - CSS optimizations (lightningcss equivalent)
- `CandidateParser.php` - Candidate parsing helpers
- `CssFormatter.php` - CSS output formatting

Everything else in `src/` should mirror TailwindCSS structure.

---

## Test System

### Test Types

1. **Extraction-based tests** (`test-coverage/` directory)
   - Tests extracted from TailwindCSS TypeScript source
   - Stored as `.ts` or `.json` files
   - PHP test files parse these at runtime
   - **Must pass completely** - no exceptions

2. **Unit tests** (`src/*.test.php`)
   - PHPUnit tests for individual components
   - Some load from `test-coverage/`, some are standalone

### Test Coverage Structure

```
test-coverage/
├── utilities/tests/     # From utilities.test.ts
├── variants/tests/      # From variants.test.ts
├── index/tests/         # From index.test.ts (JSON format)
├── css-functions/tests/ # From css-functions.test.ts
├── candidate/tests/     # From candidate.test.ts
└── extract-*.php        # Extraction scripts
```

### Extraction Scripts

Located in `test-coverage/`:
- `extract-run-tests.php` - Extracts utilities/variants tests
- `extract-index-tests.php` - Extracts index.test.ts to JSON
- `extract-css-functions-tests.php` - Extracts css-functions tests
- `extract-candidate-tests.php` - Extracts candidate tests

### Commands

```bash
# Extract tests from TypeScript source
composer extract

# Run all tests
composer test

# Extract and run tests
composer extract-and-test

# Run specific test file
./vendor/bin/phpunit src/utilities.test.php

# Run tests matching a pattern
./vendor/bin/phpunit --filter="hover"
```

---

## Development Workflow

### Standard Workflow

```bash
# 1. Extract latest tests from TypeScript
composer extract

# 2. Run tests
composer test

# 3. If tests fail, fix in src/*.php (NOT in test files)

# 4. When all tests pass, commit and push
git add -A && git commit -m "message" && git push
```

### When Tests Fail

1. **Read the failure message** - understand what output differs
2. **Check the source** - find the relevant PHP implementation
3. **Compare with TypeScript** - reference `reference/tailwindcss/`
4. **Fix the source code** - make PHP output match TypeScript
5. **Re-run tests** - verify the fix

### Priority Order

1. First ensure `utilities.test.php` passes (364 tests)
2. Then `variants.test.php` (144 tests)
3. Then `index.test.php` (integration tests)
4. Then remaining test files

---

## Project Structure

```
tailwind-php/
├── src/
│   ├── _tailwindphp/           # PHP-specific (NOT part of port)
│   │   ├── LightningCss.php    # CSS transformations
│   │   ├── CandidateParser.php
│   │   └── CssFormatter.php
│   │
│   ├── utilities/              # Split from utilities.ts
│   │   ├── accessibility.php
│   │   ├── backgrounds.php
│   │   ├── borders.php
│   │   └── ... (15 files)
│   │
│   ├── utils/                  # Helper functions
│   │
│   ├── *.php                   # Core implementation
│   └── *.test.php              # Test files
│
├── tests/
│   └── TestHelper.php          # Test utilities
│
├── test-coverage/
│   ├── */tests/                # Extracted test data
│   └── extract-*.php           # Extraction scripts
│
├── reference/
│   └── tailwindcss/            # Git submodule for reference
│
└── CLAUDE.md                   # This file
```

---

## Key Files

| File | Purpose |
|------|---------|
| `src/index.php` | Main entry point, `compile()` function |
| `src/utilities.php` | Utility registration and compilation |
| `src/variants.php` | Variant handling (hover, focus, etc.) |
| `src/compile.php` | Candidate to CSS compilation |
| `src/design-system.php` | Central registry |
| `src/theme.php` | Theme value management |
| `src/ast.php` | AST nodes and `toCss()` |
| `src/_tailwindphp/LightningCss.php` | CSS optimizations |
| `tests/TestHelper.php` | `TestHelper::run()` for tests |

---

## Common Patterns

### Adding LightningCSS Functionality

When tests fail because of CSS transformation differences:

```php
// In src/_tailwindphp/LightningCss.php

public static function someTransformation(string $value): string
{
    // Implement the transformation that lightningcss does
    return $transformedValue;
}
```

Then call it from the appropriate place in the pipeline.

### Test File Structure

Tests that load from `test-coverage/` follow this pattern:

```php
class SomeTest extends TestCase
{
    private static array $testCases = [];

    private static function loadTestCases(): void
    {
        // Load from test-coverage/*/tests/
    }

    public static function dataProvider(): array
    {
        self::loadTestCases();
        return self::$testCases;
    }

    #[DataProvider('dataProvider')]
    public function test_case(array $test): void
    {
        // Run test and compare output
    }
}
```

### CSS Comparison

When comparing CSS output:
1. Normalize whitespace
2. Normalize leading zeros
3. Handle selector escaping differences
4. Don't break pseudo-selectors (`:hover`, `:root`)

---

## Debugging Tips

### Check TypeScript Reference

```bash
# The original TailwindCSS source is in:
reference/tailwindcss/packages/tailwindcss/src/
```

### Run Single Test

```bash
./vendor/bin/phpunit --filter="test_name_here"
```

### Verbose Output

```bash
./vendor/bin/phpunit --filter="test" --testdox
```

### Print Debug Info

```php
// In test file
fwrite(STDERR, "Debug: " . print_r($value, true) . "\n");
```

---

## Current Status

| Test File | Status | Tests |
|-----------|--------|-------|
| `utilities.test.php` | ✅ **100%** | 364/364 |
| `variants.test.php` | ✅ **100%** | 144/144 |
| `index.test.php` | ✅ **100%** | 62/62 |
| `css-functions.test.php` | 🔄 Pending | - |
| `candidate.test.php` | 🔄 Pending | - |

**Total: 997 tests passing** (8 skipped for unimplemented features)

### Implemented Features

- ✅ All utility classes (364 utilities)
- ✅ All variants (hover, focus, responsive, dark mode, etc.)
- ✅ `@apply` directive with nested selectors
- ✅ `@theme` customization with namespace clearing
- ✅ `@utility` custom utilities
- ✅ `@custom-variant` support
- ✅ `@import 'tailwindcss'` module resolution
- ✅ `theme()` and `--theme()` functions
- ✅ `--spacing()` and `--alpha()` functions
- ✅ Prefix support (`tw:`)
- ✅ Shadow/ring stacking with `--tw-*` variables
- ✅ `@property` rules with `@layer properties` fallback
- ✅ Vendor prefixes (autoprefixer equivalent)
- ✅ Keyframe handling and hoisting

### Port Deviation Markers

When implementing features that differ from the TypeScript source, use the `@port-deviation` marker:

```php
/**
 * @port-deviation LightningCSS replacement
 * PHP cannot use the Rust-based lightningcss library. This method implements
 * equivalent CSS transformation logic in pure PHP.
 */
public static function flattenNesting(array &$ast): void
```

For inline deviations:
```php
// @port-deviation: PHP regex syntax differs from JS
$pattern = '/.../';
