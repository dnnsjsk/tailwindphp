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

We test against TailwindCSS's actual test suite to ensure compatibility.

### TailwindCSS Compliance Tests

TailwindCSS's `utilities.test.ts` is 28,000+ lines. Instead of porting this massive file manually, we:

1. **Pre-extract** — `scripts/extract-tests.php` splits the TypeScript test file into smaller `.ts` files under `src/utilities-test/` (grouped by category). This only needs to run when TailwindCSS updates.

2. **Parse at runtime** — `src/utilities.test.php` reads those `.ts` files and parses out:
   - Input classes (e.g., `['flex', 'p-4', 'bg-blue-500']`)
   - Expected CSS output (from `toMatchInlineSnapshot`)

3. **Compare** — Each test runs the input classes through our PHP implementation and compares against TailwindCSS's expected output.

The test suite includes 364 tests extracted from [TailwindCSS's utilities.test.ts](https://github.com/tailwindlabs/tailwindcss/blob/main/packages/tailwindcss/src/utilities.test.ts).

**For other test files:** We port them directly as PHPUnit tests, following the same 1:1 structure.

#### CSS Normalization

CSS values are normalized to match lightningcss output (which TailwindCSS uses):
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
│   ├── utilities-test/     # TailwindCSS test cases (.ts files)
│   └── utils/              # Helper functions
├── tests/                  # Unit tests
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
