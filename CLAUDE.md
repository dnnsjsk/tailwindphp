# TailwindPHP - Development Guide

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
- `lib/` - Companion library ports (clsx, tailwind-merge)

Everything else in `src/` should mirror TailwindCSS structure.

### 5. Companion Libraries (clsx, tailwind-merge)

We include PHP ports of [clsx](https://github.com/lukeed/clsx) and [tailwind-merge](https://github.com/dcastil/tailwind-merge) because they are essential companion libraries in the Tailwind ecosystem:

- **clsx** - Conditional class name construction (used in virtually every Tailwind project)
- **tailwind-merge** - Intelligent class conflict resolution (`p-2 p-4` → `p-4`)
- **cn()** - Combines both (the pattern popularized by [shadcn/ui](https://ui.shadcn.com/))

By including these, TailwindPHP provides a complete Tailwind development experience without requiring Node.js for anything.

**Important for LLMs**: These libraries live in `src/_tailwindphp/lib/` with their own namespace (`TailwindPHP\Lib\*`) to clearly separate them from the core TailwindCSS port. The public API functions (`cn`, `clsx`, `twMerge`, `twJoin`) are exposed in `src/index.php` under the main `TailwindPHP` namespace.

### 6. Plugin System

TailwindPHP includes PHP ports of official TailwindCSS plugins:

- **@tailwindcss/typography** - The prose class for beautiful typographic defaults
- **@tailwindcss/forms** - Form element reset and styling utilities

These are 1:1 ports following the exact same logic as the JavaScript originals. The plugin system architecture:

```
src/plugin.php                    # Core plugin system
├── PluginInterface               # Contract for plugins (getName, __invoke, getThemeExtensions)
├── PluginAPI                     # API passed to plugins (addBase, addUtilities, addComponents, etc.)
└── PluginManager                 # Registry and execution of plugins

src/plugin/plugins/
├── typography-plugin.php         # @tailwindcss/typography port
└── forms-plugin.php              # @tailwindcss/forms port

test-coverage/plugins/
├── typography/                   # Extracted typography tests
│   ├── summary.json
│   └── tests/*.json
└── extract-typography-tests.php  # Extraction script
```

**PluginAPI Methods** (same as TailwindCSS):
- `addBase(array $css)` - Add base styles
- `addUtilities(array $utilities)` - Add static utilities
- `matchUtilities(array $utilities, array $options)` - Add functional utilities with values
- `addComponents(array $components)` - Add component classes
- `matchComponents(array $components, array $options)` - Add functional components
- `addVariant(string $name, string|array $variant)` - Add custom variants
- `matchVariant(string $name, callable $callback, array $options)` - Add functional variants
- `theme(string $path, mixed $default)` - Access theme values

**Usage in CSS:**
```css
@plugin "@tailwindcss/typography";
@plugin "@tailwindcss/forms" {
    strategy: "class";
}
@tailwind utilities;
```

**Important for LLMs**: The plugin implementations are PHP ports, not JavaScript execution. They follow the same logic and produce the same output, but are implemented natively in PHP. Tests are extracted from the reference JavaScript test files where available.

**Creating Custom Plugins:**

```php
use TailwindPHP\Plugin\PluginInterface;
use TailwindPHP\Plugin\PluginAPI;

class MyPlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'my-plugin';
    }

    public function __invoke(PluginAPI $api, array $options = []): void
    {
        // Add static utilities
        $api->addUtilities([
            '.btn' => ['padding' => '0.5rem 1rem', 'border-radius' => '0.25rem'],
        ]);

        // Add functional utilities (generates .tab-1, .tab-2, .tab-4, .tab-8)
        $api->matchUtilities(
            ['tab' => fn($value) => ['tab-size' => $value]],
            ['values' => ['1' => '1', '2' => '2', '4' => '4', '8' => '8']]
        );

        // Add components
        $api->addComponents(['.card' => ['background' => 'white', 'padding' => '1rem']]);

        // Add variants
        $api->addVariant('hocus', '&:hover, &:focus');

        // Access theme
        $color = $api->theme('colors.blue.500', '#3b82f6');
    }

    public function getThemeExtensions(array $options = []): array
    {
        return []; // Theme additions
    }
}

// Register and use
registerPlugin(new MyPlugin());
$css = Tailwind::generate($html, '@plugin "my-plugin"; @tailwind utilities;');
```

### 7. Performance Optimizations

While this is a 1:1 port focused on correctness, PHP-specific optimizations are applied where they improve performance without changing output. These are marked with `@port-deviation:performance` in file headers.

**Current optimizations:**
- **`ast.php` toCss()**: Array accumulation + implode instead of string concatenation, pre-computed indent strings, standalone function instead of closure (~50% faster)
- **`css-parser.php` parse()**: Direct character comparison instead of ord() calls, tracked buffer lengths instead of strlen() calls (~20-30% faster)

**Guidelines for performance work:**
1. Always preserve identical output - tests must pass
2. Document with `@port-deviation:performance` in file header
3. Run benchmarks (`composer bench`) to verify improvement
4. Focus on hot paths identified by profiling

### 8. Code Quality Tools

The project uses Pint (formatting) and PHPStan (static analysis):

```bash
composer lint      # Check formatting (Pint --test)
composer format    # Fix formatting (Pint)
composer analyse   # Static analysis (PHPStan level 3)
composer quality   # Run both lint + analyse
```

**PHPStan Notes:**
- Level 3 is used (higher levels conflict with function-based architecture)
- Some patterns are intentionally ignored (dynamic arrays, cross-file functions)
- All 75 source files are analyzed, test files are excluded

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

#### Library Test Extraction (test-coverage/lib/)
- `extract-clsx-tests.php` - Extracts clsx tests from reference/clsx/test/
- `extract-tailwind-merge-tests.php` - Extracts tailwind-merge tests from reference/tailwind-merge/tests/
- `verify-test-counts.php` - Verifies PHP tests match reference test counts

### Commands

```bash
# Extract tests from TypeScript source
composer extract

# Extract companion library tests
composer extract-libs

# Extract all tests (core + libs)
composer extract-all

# Run all tests
composer test

# Run library tests only
composer test-libs

# Extract and run tests
composer extract-and-test

# Run specific test file
./vendor/bin/phpunit src/utilities.test.php

# Run tests matching a pattern
./vendor/bin/phpunit --filter="hover"

# Check current versions of all references
composer versions

# Update TailwindCSS reference
composer update-tailwind

# Update companion library references
composer update-libs
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
tailwindphp/
├── src/
│   ├── _tailwindphp/           # PHP-specific (NOT part of port)
│   │   ├── LightningCss.php    # CSS transformations
│   │   ├── CandidateParser.php
│   │   ├── CssFormatter.php
│   │   └── lib/                # Companion library ports
│   │       ├── clsx/           # clsx port (27 tests)
│   │       │   ├── clsx.php
│   │       │   └── clsx.test.php
│   │       └── tailwind-merge/ # tailwind-merge port (52 tests)
│   │           ├── index.php   # Main entry, cn(), twMerge(), twJoin()
│   │           ├── config.php  # Default Tailwind v4 config
│   │           ├── merger.php  # ClassNameMerger logic
│   │           ├── lru-cache.php
│   │           └── tailwind_merge.test.php
│   │
│   ├── plugin/                 # Plugin system
│   │   └── plugins/            # Built-in plugin implementations
│   │       ├── typography-plugin.php  # @tailwindcss/typography port
│   │       ├── typography_plugin.test.php
│   │       └── forms-plugin.php       # @tailwindcss/forms port
│   │
│   ├── utilities/              # Split from utilities.ts
│   │   ├── accessibility.php
│   │   ├── backgrounds.php
│   │   ├── borders.php
│   │   └── ... (15 files)
│   │
│   ├── utils/                  # Helper functions
│   │
│   ├── index.php               # Main entry + public API (cn, clsx, twMerge, twJoin)
│   ├── plugin.php              # Plugin system (PluginInterface, PluginAPI, PluginManager)
│   ├── *.php                   # Core implementation
│   └── *.test.php              # Unit test files
│
├── tests/
│   ├── TestHelper.php          # Test utilities
│   └── ui_spec.test.php        # Browser/integration tests (from tests/ui.spec.ts)
│
├── test-coverage/
│   ├── */tests/                # Extracted test data
│   ├── lib/                    # Library test extraction scripts
│   │   ├── extract-clsx-tests.php
│   │   ├── extract-tailwind-merge-tests.php
│   │   └── verify-test-counts.php
│   └── extract-*.php           # Core extraction scripts
│
├── resources/
│   ├── theme.css               # Default Tailwind theme (copied from reference)
│   ├── preflight.css           # CSS reset (copied from reference)
│   ├── utilities.css           # Utilities directive
│   └── index.css               # Main entry point
│
├── reference/
│   ├── tailwindcss/            # Git submodule - TailwindCSS source
│   ├── tailwindcss-typography/ # Git submodule - @tailwindcss/typography source
│   ├── tailwindcss-forms/      # Git submodule - @tailwindcss/forms source
│   ├── clsx/                   # Git submodule - clsx source
│   └── tailwind-merge/         # Git submodule - tailwind-merge source
│
├── scripts/
│   ├── update-tailwind.php     # Update TailwindCSS reference + copy CSS files
│   └── update-libs.php         # Update companion library references
│
└── CLAUDE.md                   # This file
```

---

## Key Files

| File | Purpose |
|------|---------|
| `src/index.php` | Main entry point, `compile()`, `cn()`, `clsx()`, `twMerge()` |
| `src/utilities.php` | Utility registration and compilation |
| `src/variants.php` | Variant handling (hover, focus, etc.) |
| `src/compile.php` | Candidate to CSS compilation |
| `src/design-system.php` | Central registry |
| `src/theme.php` | Theme value management |
| `src/ast.php` | AST nodes and `toCss()` |
| `src/plugin.php` | Plugin system (PluginInterface, PluginAPI, PluginManager) |
| `src/plugin/plugins/typography-plugin.php` | @tailwindcss/typography port |
| `src/plugin/plugins/forms-plugin.php` | @tailwindcss/forms port |
| `src/_tailwindphp/LightningCss.php` | CSS optimizations |
| `src/_tailwindphp/lib/clsx/clsx.php` | clsx implementation |
| `src/_tailwindphp/lib/tailwind-merge/index.php` | tailwind-merge + cn() implementation |
| `tests/TestHelper.php` | `TestHelper::run()` for tests |
| `scripts/update-libs.php` | Update companion library references |

---

## Public API

### Core CSS Compilation

```php
use TailwindPHP\Tailwind;

// Generate CSS from HTML
$css = Tailwind::generate('<div class="flex p-4">');

// With custom CSS input
$css = Tailwind::generate($html, '@tailwind utilities; @theme { --color-brand: #3b82f6; }');

// Extract class candidates from content
$classes = Tailwind::extractCandidates('<div class="flex p-4">');
```

### Class Name Utilities

```php
use function TailwindPHP\cn;
use function TailwindPHP\clsx;
use function TailwindPHP\twMerge;
use function TailwindPHP\twJoin;

// cn() - Recommended: combines clsx + twMerge (shadcn/ui pattern)
cn('px-2 py-1', 'px-4');                    // => 'py-1 px-4'
cn('btn', ['btn-primary' => $active]);       // => 'btn btn-primary' (if $active)

// clsx() - Conditional class construction
clsx('foo', ['bar' => true, 'baz' => false]); // => 'foo bar'
clsx(['a', 'b'], 'c');                        // => 'a b c'

// twMerge() - Conflict resolution only
twMerge('px-2 py-1', 'px-4');                // => 'py-1 px-4'
twMerge('hover:bg-red-500', 'hover:bg-blue-500'); // => 'hover:bg-blue-500'

// twJoin() - Simple joining without conflict resolution
twJoin('foo', 'bar', null);                  // => 'foo bar'
```

### Direct Library Access

For advanced use cases, you can access the library implementations directly:

```php
use function TailwindPHP\Lib\Clsx\clsx;
use function TailwindPHP\Lib\TailwindMerge\twMerge;
use function TailwindPHP\Lib\TailwindMerge\cn;
```

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

### Updating Library References

When clsx or tailwind-merge releases a new version:

```bash
# Check current versions
composer versions

# Update all libraries to latest
composer update-libs

# Update specific library
php scripts/update-libs.php clsx
php scripts/update-libs.php tailwind-merge
```

The update script will:
1. Fetch the latest tag from the reference repo
2. Checkout that tag
3. Re-extract tests from the new source
4. Update README badges automatically
5. Run tests to verify compatibility

If tests fail after updating, it means the library made breaking changes that require updating the PHP implementation.

### Library Test Extraction

Tests for companion libraries are extracted from their original JavaScript/TypeScript test files:

**clsx**: Tests extracted from `reference/clsx/test/index.js` and `classnames.js`
- Parser handles JavaScript function call syntax
- Converts JS truthiness semantics to PHP (empty arrays are truthy in JS)
- Strips JS comments before parsing

**tailwind-merge**: Tests extracted from `reference/tailwind-merge/tests/*.test.ts`
- Parser handles TypeScript test syntax (`test()`, `expect().toBe()`)
- Processes escape sequences in string literals
- Some tests N/A (require custom config, extendTailwindMerge)

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

**Total: 3,107 tests (all passing)**

### Core Tests (extracted from TypeScript test suites)

| Test File | Status | Tests |
|-----------|--------|-------|
| `utilities.test.php` | ✅ | 547 (includes 183 compileCss tests) |
| `variants.test.php` | ✅ | 139 |
| `index.test.php` | ✅ | 78 (5 N/A - outside scope) |
| `css_functions.test.php` | ✅ | 60 (7 N/A for JS tooling) |
| `ui_spec.test.php` | ✅ | 68 |

### Plugin Tests (`src/plugin/`)

| Test File | Status | Tests |
|-----------|--------|-------|
| `plugin.test.php` | ✅ | 9 (core plugin functionality) |
| `typography_plugin.test.php` | ✅ | 16 (13 N/A - v3-specific behavior) |

**Plugin System:**
- `plugin.php` - Core plugin system (PluginInterface, PluginAPI, PluginManager)
- `typography-plugin.php` - @tailwindcss/typography port
- `forms-plugin.php` - @tailwindcss/forms port

Typography tests are extracted from `reference/tailwindcss-typography/src/index.test.js`. Tests marked N/A use v3-specific behavior (dark mode via `.dark` selector, responsive variants) that differs in v4.

### Library Tests (`src/_tailwindphp/lib/`)

PHP ports of utility libraries with tests extracted from their reference implementations:

| Library | Test File | Status | Tests | Reference |
|---------|-----------|--------|-------|-----------|
| clsx | `clsx.test.php` | ✅ | 27 | 27/27 (100%) |
| tailwind-merge | `tailwind_merge.test.php` | ✅ | 52 | 52/67 applicable (78%) |

**clsx** - Class name string builder (`TailwindPHP\Lib\Clsx\clsx`)
- Reference: https://github.com/lukeed/clsx
- All 27 tests ported from index.js (12) and classnames.js (15)

**tailwind-merge** - Tailwind class conflict resolver (`TailwindPHP\Lib\TailwindMerge\twMerge`)
- Reference: https://github.com/dcastil/tailwind-merge
- 52 tests from 14 applicable test files
- 32 tests N/A (require custom config, extendTailwindMerge, etc.)
- Includes `cn()` function that combines clsx + twMerge

### API Coverage Tests (`tests/tailwindphp/`)

| Test File | Status | Tests |
|-----------|--------|-------|
| `UtilitiesTest.php` | ✅ | 904 |
| `ModifiersTest.php` | ✅ | 338 |
| `VariantsTest.php` | ✅ | 282 |
| `DirectivesTest.php` | ✅ | 160 |

These tests provide exhaustive coverage of the TailwindPHP public API including:
- All utility classes with various values and modifiers
- Color opacity combinations (5-95 in increments of 5)
- All responsive, state, and pseudo-class variants
- Deep variant stacking (3-4 levels)
- Arbitrary values for all utility types
- Container queries, aria/data attributes
- @apply, @theme, @utility directives

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
