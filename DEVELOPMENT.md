# Tailwind PHP - Development Guide

A full 1:1 port of TailwindCSS 4.x to PHP, focusing on the CSS-first approach (no JS config).

## Current Status

**Test Suite:** 698 tests passing (364 TailwindCSS compliance tests + 334 unit tests)

### Completed
- All utility functions (364/364 compliance tests passing)
- CSS parser and AST
- Theme system
- Candidate parsing
- Value parser
- Selector parser
- Default theme values
- LightningCSS-equivalent optimizations
- All utility categories: layout, flexbox, spacing, sizing, typography, borders, effects, filters, transforms, transitions, backgrounds, interactivity, accessibility, SVG, tables

### In Progress
- Variants system
- Full design system integration
- @apply directive
- @import handling

---

## Project Structure

```
tailwind-php/
├── src/                            # Source code
│   ├── _tailwindphp/               # PHP-specific helpers (NOT part of the port)
│   │   ├── LightningCss.php        # CSS optimizations (lightningcss equivalent)
│   │   ├── CandidateParser.php     # Simplified candidate parsing
│   │   └── CssFormatter.php        # CSS output formatting
│   │
│   ├── utilities/                  # Utility implementations (split from utilities.ts)
│   │   ├── accessibility.php
│   │   ├── backgrounds.php
│   │   ├── borders.php
│   │   ├── effects.php
│   │   ├── filters.php
│   │   ├── flexbox.php
│   │   ├── interactivity.php
│   │   ├── layout.php
│   │   ├── sizing.php
│   │   ├── spacing.php
│   │   ├── svg.php
│   │   ├── tables.php
│   │   ├── transforms.php
│   │   ├── transitions.php
│   │   └── typography.php
│   │
│   ├── utilities-test/             # TailwindCSS test cases (*.ts files)
│   │
│   ├── utils/                      # Helper utilities
│   │   ├── brace-expansion.php
│   │   ├── compare.php
│   │   ├── decode-arbitrary-value.php
│   │   ├── default-map.php
│   │   ├── escape.php
│   │   ├── segment.php
│   │   └── ...
│   │
│   ├── ast.php                     # AST nodes & toCss
│   ├── candidate.php               # Class name parsing
│   ├── compile.php                 # Candidate compilation
│   ├── css-parser.php              # CSS tokenizer
│   ├── default-theme.php           # Default Tailwind theme values
│   ├── design-system.php           # Central registry
│   ├── property-order.php          # CSS property ordering
│   ├── selector-parser.php         # Selector parsing
│   ├── theme.php                   # Theme management
│   ├── utilities.php               # Utility registry & builder
│   ├── value-parser.php            # Value parsing
│   ├── variants.php                # Variant handling
│   └── walk.php                    # AST traversal
│
├── tests/
│   └── TestHelper.php              # Test utilities
│
├── scripts/
│   └── extract-tests.php           # Extract tests from Tailwind source
│
├── reference/
│   └── tailwindcss/                # TailwindCSS submodule (for reference)
│
├── composer.json
├── phpunit.xml
├── README.md                       # User documentation
└── DEVELOPMENT.md                  # This file (development guide)
```

### Directory Conventions

- **`src/`** — 1:1 port of TailwindCSS source files
- **`src/_tailwindphp/`** — PHP-specific code NOT part of the port (helpers, optimizers)
- **`src/utilities/`** — Split from `utilities.ts` (6,000+ lines) for maintainability
- **`src/utilities-test/`** — Extracted test cases from TailwindCSS
- **`reference/tailwindcss/`** — Git submodule of TailwindCSS for reference

---

## Test Strategy

### TailwindCSS Compliance Tests

`src/utilities.test.php` parses test cases from `src/utilities-test/*.ts` at runtime:
- 364 tests extracted from TailwindCSS's `utilities.test.ts`
- Verifies our output matches TailwindCSS exactly
- Auto-parsed, no manual test porting needed

### Unit Tests

`tests/` contains PHPUnit tests for individual components.

### Updating Tests

When TailwindCSS updates:
```bash
php scripts/extract-tests.php
./vendor/bin/phpunit
```

---

## Porting Phases

### Phase 1: Project Setup ✓
- [x] Composer package with PSR-4 autoloading
- [x] PHPUnit configuration
- [x] Directory structure

### Phase 2: Foundation ✓
- [x] Utils (segment, escape, decode-arbitrary-value, etc.)
- [x] CSS parser
- [x] AST handling
- [x] Value parser
- [x] Selector parser

### Phase 3: Theme & Design System
- [x] Theme management
- [x] Property ordering
- [x] Default theme values
- [ ] Full design system integration
- [ ] Sort utilities

### Phase 4: Utilities ✓
- [x] All utility categories
- [x] 364/364 compliance tests passing

### Phase 5: Variants
- [ ] Responsive variants
- [ ] State variants (hover, focus, etc.)
- [ ] Dark mode
- [ ] Container queries

### Phase 6: Directives
- [ ] `@apply` directive
- [ ] `@import` handling
- [ ] `theme()` function

### Phase 7: Integration
- [ ] Main entry point (TailwindPHP.php)
- [ ] Public API
- [ ] Full integration tests

### Phase 8: Polish
- [ ] Documentation
- [ ] Performance optimization
- [ ] Publish to Packagist

---

## Key Implementation Notes

### LightningCSS Optimizations

TailwindCSS uses lightningcss (Rust) for CSS post-processing. We implement equivalent transformations in `src/_tailwindphp/LightningCss.php`:
- `calc()` simplification for angles
- Leading zero removal (0.5 → .5)
- Grid value normalization
- Transform function spacing

### Utilities Split

TailwindCSS's `utilities.ts` is 6,000+ lines. We split it into `src/utilities/` by category while maintaining the same logic. The `UtilityBuilder` class in `utilities.php` provides the registration API.

### Test Parsing

Rather than manually porting 28,000+ lines of tests, we:
1. Extract test cases from TypeScript using `scripts/extract-tests.php`
2. Parse them at runtime in `src/utilities.test.php`
3. Compare output against expected snapshots

---

## Gotchas & Patterns

### Utility Registration

Utilities are registered via `UtilityBuilder` in `src/utilities.php`. There are several registration methods:

```php
// Static utility (no value, e.g., flex, hidden)
$builder->staticUtility('flex', [['display', 'flex']]);

// Functional utility (takes a value, e.g., p-4, text-red-500)
$builder->functionalUtility('p', [
    'themeKeys' => ['--padding', '--spacing'],
    'handle' => fn($value) => [decl('padding', $value)],
]);

// Spacing utility (special handling for spacing scale)
$builder->spacingUtility('m', ['--margin', '--spacing'], fn($v) => [decl('margin', $v)]);

// Color utility (handles opacity modifiers like bg-red-500/50)
$builder->colorUtility('bg', [
    'themeKeys' => ['--background-color', '--color'],
    'handle' => fn($v) => [decl('background-color', $v)],
]);
```

### Candidate Structure

When a utility's `compileFn` is called, it receives a candidate object:

```php
[
    'kind' => 'functional',
    'root' => 'p',              // The utility name
    'value' => [
        'kind' => 'named',      // or 'arbitrary'
        'value' => '4',         // The value after the dash
        'fraction' => null,     // e.g., '1/2' for w-1/2
    ],
    'modifier' => [             // e.g., /50 in bg-red-500/50
        'kind' => 'named',
        'value' => '50',
    ],
    'important' => false,
    'raw' => 'p-4',
]
```

### Negative Values

Negative utilities (e.g., `-translate-x-4`) are handled by:
1. Setting `supportsNegative: true` in the utility config
2. The builder registers both `translate-x` and `-translate-x`
3. Negative values are wrapped: `calc({value} * -1)`

Exception: Some utilities use `handleNegativeBareValue` for custom negative handling (e.g., angles use `-45deg` not `calc(45deg * -1)`).

### Fractions

Fractions like `w-1/2` are handled when `supportsFractions: true`:
- Parsed as `['fraction' => '1/2']` in the value
- Converted to `calc(1 / 2 * 100%)` (note: spaces around `/` required for lightningcss)

### Modifiers

Modifiers (e.g., `/50` in `bg-red-500/50`) are used for:
- **Colors**: Opacity modifier (converted to `color-mix()`)
- **Container queries**: Named container (`@container/sidebar`)

The `CandidateParser` distinguishes modifiers from fractions by checking if the part after `/` is numeric.

### AST Nodes

CSS output is built using AST helper functions from `src/ast.php`:

```php
decl('padding', '1rem')                    // Declaration
styleRule('.foo', [...])                   // Style rule
atRule('@media', '(min-width: 640px)', []) // At-rule
```

The `decl()` function automatically applies LightningCSS optimizations to values.

### Theme Resolution

Theme values are resolved via `Theme::resolve()`:

```php
$theme->resolve('4', ['--padding', '--spacing']);
// Tries --padding-4, then --spacing-4, then bare --padding/--spacing multiplier
```

The resolution order matters — more specific keys should come first.

### Color Handling

Colors with opacity use `color-mix()` in OKLAB color space:

```php
withAlpha('red', '50%')  // → "color-mix(in oklab, red 50%, transparent)"
```

### Static Values in Functional Utilities

Some functional utilities have static fallbacks (e.g., `z-auto`):

```php
$builder->functionalUtility('z', [
    'themeKeys' => ['--z-index'],
    'staticValues' => [
        'auto' => [decl('z-index', 'auto')],
    ],
    'handle' => fn($v) => [decl('z-index', $v)],
]);
```

### Nested Rules

Some utilities generate nested rules (e.g., `space-x-4` uses `& > :not(:last-child)`):

```php
return [
    styleRule('& > :not(:last-child)', [
        decl('margin-left', $value),
    ]),
];
```

The `&` is replaced with the parent selector during CSS formatting.

### Testing Individual Utilities

To debug a specific utility:

```bash
./vendor/bin/phpunit --filter="translate"
```

Or add a focused test in `tests/`:

```php
public function test_specific_utility(): void
{
    $css = TestHelper::run(['p-4']);
    $this->assertStringContainsString('padding: 1rem', $css);
}
```
