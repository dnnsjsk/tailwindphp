# Tailwind PHP

A full port of TailwindCSS 4.x to PHP. Generate Tailwind CSS using pure PHP — no Node.js required.

## Status

✅ **997 tests passing** — Feature complete for core TailwindCSS functionality.

| Test Suite | Status |
|------------|--------|
| Utilities | 364/364 ✅ |
| Variants | 144/144 ✅ |
| Integration (index) | 62/62 ✅ |

### Features

- All 364+ utility classes
- All variants (hover, focus, responsive, dark mode, etc.)
- Directives: `@apply`, `@theme`, `@tailwind`, `@utility`, `@custom-variant`
- Functions: `theme()`, `--theme()`, `--spacing()`, `--alpha()`
- Shadow/ring stacking with CSS `@property` rules
- Vendor prefixes (autoprefixer equivalent)
- Keyframe handling

## Installation

```bash
composer require dnnsjsk/tailwind-php
```

## Usage

```php
use TailwindPHP\Tailwind;

// Generate CSS from HTML containing Tailwind classes
$css = Tailwind::generate('<div class="flex items-center p-4 bg-blue-500">Hello</div>');
```

## How It Works

This is a 1:1 port of TailwindCSS 4.x's core functionality to PHP. The goal is feature parity with TailwindCSS while eliminating the Node.js dependency.

### Architecture

The codebase mirrors TailwindCSS's structure — same file names, same organization:

```
src/
├── _tailwindphp/        # PHP-specific helpers (not part of the port)
├── utilities/           # Utility implementations (layout, spacing, etc.)
├── utils/               # Helper functions (escape, segment, etc.)
├── ast.php              # AST node types and CSS generation
├── candidate.php        # Class name parsing
├── compile.php          # Candidate to CSS compilation
├── default-theme.php    # Default Tailwind theme values
├── theme.php            # Theme value resolution
├── design-system.php    # Central registry
└── variants.php         # Variant handling (hover, focus, etc.)
```

**Note:** TailwindCSS's `utilities.ts` is 6,000+ lines. We split it into `src/utilities/` (one file per category) for maintainability.

### PHP-Specific Code

The `src/_tailwindphp/` folder contains PHP-specific helpers that are NOT part of the TailwindCSS port:

- **LightningCss.php** — CSS optimizations (TailwindCSS uses lightningcss, a Rust library)
- **CandidateParser.php** — Simplified candidate parsing for compilation
- **CssFormatter.php** — CSS output formatting

This separation keeps the 1:1 port clean while providing necessary PHP implementations.

### Port Deviation Markers

All implementation files are documented with `@port-deviation` markers explaining where and why the PHP implementation differs from TypeScript:

| Marker | Meaning |
|--------|---------|
| `@port-deviation:none` | Direct 1:1 port with no deviations |
| `@port-deviation:async` | PHP uses synchronous code (no async/await) |
| `@port-deviation:storage` | Different data structures (array vs Map/Set) |
| `@port-deviation:types` | PHPDoc instead of TypeScript types |
| `@port-deviation:sourcemaps` | Source map tracking omitted |
| `@port-deviation:replacement` | PHP implementation replacing external library |
| `@port-deviation:omitted` | Entire module not ported (not needed) |

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

### Running Tests

```bash
# Run all tests
./vendor/bin/phpunit

# Run only compliance tests
./vendor/bin/phpunit src/utilities.test.php

# Run specific test
./vendor/bin/phpunit --filter="translate"
```

### TailwindCSS Compliance Tests

TailwindCSS's `utilities.test.ts` is 28,000+ lines. Instead of porting manually, we:

1. **Pre-extract** — `scripts/extract-tests.php` splits the TypeScript test file into smaller `.ts` files under `src/utilities-test/`
2. **Parse at runtime** — `src/utilities.test.php` reads those files and parses input classes and expected CSS output
3. **Compare** — Each test runs through our PHP implementation and compares against TailwindCSS's expected output

### Requirements

- PHP 8.1+
- Composer

## Development

See [CLAUDE.md](CLAUDE.md) for detailed development guide, project structure, and porting phases.

## License

MIT

## Credits

This project ports [TailwindCSS](https://tailwindcss.com) by Tailwind Labs to PHP.
