# Tailwind PHP

A full port of TailwindCSS 4.x to PHP. Generate Tailwind CSS using pure PHP — no Node.js required.

## Status

**Work in Progress** — Utilities are complete, variants and directives are in development.

- 698 tests passing
- 364/364 TailwindCSS compliance tests passing
- All utility categories implemented

## Installation

```bash
composer require fabrikat/tailwind-php
```

## Usage

```php
use TailwindPHP\TailwindPHP;

// Generate CSS from HTML containing Tailwind classes
$css = TailwindPHP::generate('<div class="flex items-center p-4 bg-blue-500">Hello</div>');
```

## How It Works

This is a 1:1 port of TailwindCSS 4.x's core functionality to PHP. The goal is feature parity with TailwindCSS while eliminating the Node.js dependency.

### Architecture

The codebase mirrors TailwindCSS's structure exactly — same file names, same organization. Files that don't apply to PHP (like TypeScript-specific modules) are kept as empty placeholder files to maintain the 1:1 mapping and make it easy to reference the original source.

```
src/
├── utilities/           # All utility implementations (layout, spacing, etc.)
├── utils/               # Helper functions (escape, segment, etc.)
├── css-parser.php       # CSS tokenizer and parser
├── ast.php              # AST node types and CSS generation
├── candidate.php        # Class name parsing
├── compile.php          # Candidate to CSS compilation
├── theme.php            # Theme value resolution
├── design-system.php    # Central registry
└── variants.php         # Variant handling (hover, focus, etc.)
```

**Note on utilities.php:** TailwindCSS's `utilities.ts` is 6,000+ lines. For maintainability, we split it into separate files under `src/utilities/` (one per category), while keeping the same logic and structure.

### Utility Categories

All TailwindCSS utility categories are implemented:

- **Layout** — display, position, z-index, float, clear, overflow, etc.
- **Flexbox & Grid** — flex, grid, gap, justify, align, place, etc.
- **Spacing** — margin, padding, space-between
- **Sizing** — width, height, min/max variants, size
- **Typography** — font, text, leading, tracking, etc.
- **Backgrounds** — colors, gradients, images
- **Borders** — width, radius, style, divide, outline
- **Effects** — shadow, opacity, blend modes
- **Filters** — blur, brightness, contrast, etc.
- **Transforms** — translate, rotate, scale, skew
- **Transitions** — duration, timing, delay
- **Interactivity** — cursor, scroll, touch, select
- **SVG** — fill, stroke
- **Tables** — border-collapse, table-layout
- **Accessibility** — sr-only, forced-colors

## Testing

We test against TailwindCSS's actual test suite to ensure compatibility. The approach varies by file size:

### TailwindCSS Compliance Tests

**For utilities.test.ts (28,000+ lines):** We extract and split the tests into smaller files under `extracted-tests/`, then parse them at runtime. This makes the massive test file manageable while still testing every single case.

The test suite includes 364 tests extracted directly from [TailwindCSS's utilities.test.ts](https://github.com/tailwindlabs/tailwindcss/blob/main/packages/tailwindcss/src/utilities.test.ts).

**For other test files:** We port them directly as PHPUnit tests, following the same 1:1 structure.

These tests ensure our PHP output matches TailwindCSS exactly:

```php
// Test case parsed from TailwindCSS source
$css = TestHelper::run(['flex', 'items-center', 'p-4']);

// Compared against expected TailwindCSS output
$this->assertEquals($expectedCss, $css);
```

#### How Compliance Testing Works

1. **Extract** — `scripts/extract-tests.php` parses TailwindCSS's TypeScript test file and extracts test cases to `extracted-tests/*.ts`

2. **Parse** — `src/utilities.test.php` parses the extracted `.ts` files at runtime, extracting:
   - Input classes (e.g., `['flex', 'p-4', 'bg-blue-500']`)
   - Expected CSS output (from `toMatchInlineSnapshot`)

3. **Compare** — For each test case:
   - Run input classes through our PHP implementation
   - Parse both expected and actual CSS into normalized rules
   - Compare selectors and declarations

4. **Normalize** — CSS values are normalized to match lightningcss output:
   - `calc()` simplification for angles
   - Leading zero removal (`.5` vs `0.5`)
   - Space normalization in grid/transform values

#### Updating Tests

When TailwindCSS updates, re-extract the tests:

```bash
php scripts/extract-tests.php
./vendor/bin/phpunit src/utilities.test.php
```

### Running Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run only compliance tests
./vendor/bin/phpunit src/utilities.test.php

# Run specific test
./vendor/bin/phpunit --filter="translate"
```

## Development

### Requirements

- PHP 8.1+
- Composer

### Project Structure

```
tailwind-php/
├── src/                    # Source code
│   ├── utilities/          # Utility implementations
│   └── utils/              # Helper functions
├── tests/                  # Unit tests
├── extracted-tests/        # TailwindCSS test cases (.ts files)
├── scripts/                # Build scripts
│   └── extract-tests.php   # Extract tests from TailwindCSS
└── tailwindcss/            # TailwindCSS submodule (for reference)
```

### Adding New Utilities

1. Add implementation to appropriate file in `src/utilities/`
2. Run compliance tests: `./vendor/bin/phpunit src/utilities.test.php`
3. Fix any failing tests

## Roadmap

- [x] All utility classes (364/364 tests passing)
- [ ] Variants (hover, focus, responsive, dark mode)
- [ ] `@apply` directive
- [ ] `@theme` customization
- [ ] `theme()` function
- [ ] Full API documentation

## License

MIT

## Credits

This project ports [TailwindCSS](https://tailwindcss.com) by Tailwind Labs to PHP.
