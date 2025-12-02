# Tailwind PHP - Development Guide

A **1:1 port of TailwindCSS 4.x to PHP** focused on **string-to-string CSS compilation**. See [README.md](README.md) for scope and features.

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
├── ui-spec/tests/       # From tests/ui.spec.ts (Playwright browser tests)
└── extract-*.php        # Extraction scripts
```

### Extraction Scripts

Located in `test-coverage/`:
- `extract-run-tests.php` - Extracts utilities/variants tests
- `extract-index-tests.php` - Extracts index.test.ts to JSON
- `extract-css-functions-tests.php` - Extracts css-functions tests
- `extract-candidate-tests.php` - Extracts candidate tests
- `extract-ui-spec-tests.php` - Extracts ui.spec.ts browser tests

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

**Total: 1,139 tests (1,082 passing, 57 skipped)**

### Core Tests (extracted from TypeScript test suites)

| Test File | Status | Tests |
|-----------|--------|-------|
| `utilities.test.php` | ✅ | 364 |
| `variants.test.php` | ✅ | 139 |
| `index.test.php` | ✅ | 78 (9 skipped - outside scope/pending) |
| `css_functions.test.php` | ✅ | 60 (7 N/A for JS tooling) |
| `ui_spec.test.php` | ✅ | 68 (48 skipped - pending utilities) |

### Unit Tests (ported from TypeScript)

| Test File | Status | Tests |
|-----------|--------|-------|
| `css_parser.test.php` | ✅ | 70 |
| `candidate.test.php` | ✅ | 66 |
| `decode_arbitrary_value.test.php` | ✅ | 60 |
| `constant_fold_declaration.test.php` | ✅ | 57 |
| `selector_parser.test.php` | ✅ | 22 |
| `attribute_selector_parser.test.php` | ✅ | 20 |
| `value_parser.test.php` | ✅ | 19 |
| `ast.test.php` | ✅ | 18 |
| `walk.test.php` | ✅ | 15 |
| `compare.test.php` | ✅ | 14 |
| `brace_expansion.test.php` | ✅ | 13 |
| `segment.test.php` | ✅ | 12 |
| `replace_shadow_colors.test.php` | ✅ | 12 |
| `escape.test.php` | ✅ | 10 |
| `prefix.test.php` | ✅ | 9 |
| `expand_declaration.test.php` | ✅ | 4 |
| `important.test.php` | ✅ | 4 |

### Outside Scope (0 tests - intentionally empty)

These PHP test files exist but contain no tests because the TypeScript originals test features outside the scope of this port (file system, JS runtime, IDE tooling):

| PHP Test File | TypeScript Original | Reason |
|---------------|---------------------|--------|
| `at_import.test.php` | `at-import.test.ts` | File system - async file resolution |
| `canonicalize_candidates.test.php` | `canonicalize-candidates.test.ts` | IDE tooling - Prettier plugin |
| `intellisense.test.php` | `intellisense.test.ts` | IDE tooling - VS Code extension |
| `plugin.test.php` | `plugin-api.test.ts` | JS runtime - Plugin API |
| `sort.test.php` | `sort.test.ts` | IDE tooling - class sorting |
| `to_key_path.test.php` | `to-key-path.test.ts` | Not needed |

Other TypeScript test files not ported: `config.test.ts`, `resolve-config.test.ts`, `container-config.test.ts`, `screens-config.test.ts`, `flatten-color-palette.test.ts`, `apply-config-to-theme.test.ts`, `apply-keyframes-to-theme.test.ts`, `legacy-utilities.test.ts`, `source-map.test.ts`, `line-table.test.ts`, `translation-map.test.ts`.

Within `css_functions.test.php`, tests containing `@plugin`, `@config`, or `@import './file'` are marked as N/A (passed without assertion) since these features are outside scope.

### Implemented Features

- ✅ All utility classes (364 utilities)
- ✅ All variants (hover, focus, responsive, dark mode, etc.)
- ✅ `@apply` directive with nested selectors
- ✅ `@theme` customization with namespace clearing
- ✅ `@utility` custom utilities
- ✅ `@custom-variant` support
- ✅ `@import 'tailwindcss'` module resolution (inline, not file-based)
- ✅ `theme()` function with dot notation (`colors.red.500`)
- ✅ `--theme()` function with initial fallback handling
- ✅ `--spacing()` and `--alpha()` functions
- ✅ `color-mix()` to `oklab()` conversion (LightningCSS equivalent)
- ✅ Stacking opacity in `@theme` definitions
- ✅ Prefix support (`tw:`)
- ✅ Shadow/ring stacking with `--tw-*` variables
- ✅ `@property` rules with `@layer properties` fallback
- ✅ Vendor prefixes (autoprefixer equivalent)
- ✅ Keyframe handling and hoisting
- ✅ Invalid `theme()` candidates filtered out

### Port Deviation Markers

All 44 implementation files are documented with `@port-deviation` markers explaining where and why the PHP implementation differs from TypeScript.

#### Deviation Types

| Marker | Meaning |
|--------|---------|
| `@port-deviation:none` | Direct 1:1 port with no significant deviations |
| `@port-deviation:async` | PHP uses synchronous code instead of async/await |
| `@port-deviation:storage` | Different data structures (PHP array vs JS Map/Set) |
| `@port-deviation:types` | PHPDoc annotations instead of TypeScript types |
| `@port-deviation:sourcemaps` | Source map tracking omitted |
| `@port-deviation:enum` | PHP constants instead of TypeScript enums |
| `@port-deviation:caching` | Different caching strategy |
| `@port-deviation:errors` | Different error handling approach |
| `@port-deviation:stub` | Placeholder for unneeded functionality |
| `@port-deviation:replacement` | PHP implementation replacing external library (e.g., lightningcss) |
| `@port-deviation:helper` | PHP-specific helper not in original |
| `@port-deviation:omitted` | Entire module not ported (not needed for PHP) |

#### Usage Examples

File header deviation:
```php
/**
 * Port of: packages/tailwindcss/src/compile.ts
 *
 * @port-deviation:bigint TypeScript uses BigInt for variant order bitmask.
 * PHP uses regular integers (sufficient for current variant count).
 *
 * @port-deviation:sorting TypeScript uses Map<AstNode, ...> for nodeSorting.
 * PHP embeds sorting info directly in nodes via '__sorting' key.
 */
```

Inline deviation:
```php
// @port-deviation: PHP regex syntax differs from JS
$pattern = '/.../';
```
