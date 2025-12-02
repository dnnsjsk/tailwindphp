# Tailwind PHP

A 1:1 port of TailwindCSS 4.x to PHP focused on **string-to-string CSS compilation**. Generate Tailwind CSS using pure PHP — no Node.js required.

## Scope

This port converts TailwindCSS input (CSS with directives) into standard CSS output. It is a **pure string transformation**:

```php
// Input: CSS string with Tailwind directives
$input = '@tailwind utilities; @theme { --color-brand: #3b82f6; }';

// Output: Standard CSS string
$output = Tailwind::generate('<div class="bg-brand p-4">', $input);
```

**What's included:**
- All CSS compilation features (utilities, variants, directives, functions)
- No external dependencies beyond PHP

**What's NOT included (outside scope):**
- File system access — No `@import` file resolution, no reading CSS files
- JavaScript runtime — No `@plugin` execution, no `tailwind.config.js`
- IDE tooling — No IntelliSense, autocomplete, or source maps

If you need file-based imports or JS plugins, preprocess your CSS before passing it to this library.

## Status

✅ **1,056 tests passing** — Feature complete for core TailwindCSS functionality.

| Test Suite | Status |
|------------|--------|
| Utilities | 364/364 ✅ |
| Variants | 139/139 ✅ |
| Integration | 62/62 ✅ |
| CSS Functions | 60/60 ✅ |

### Features

- All 364+ utility classes
- All variants (hover, focus, responsive, dark mode, etc.)
- Directives: `@apply`, `@theme`, `@tailwind`, `@utility`, `@custom-variant`
- Functions: `theme()`, `--theme()`, `--spacing()`, `--alpha()`
- `--theme()` with `initial` fallback handling
- Stacking opacity in `@theme` definitions
- `color-mix()` to `oklab()` conversion (LightningCSS equivalent)
- Shadow/ring stacking with CSS `@property` rules
- Vendor prefixes (autoprefixer equivalent)
- Keyframe handling
- Invalid `theme()` candidates filtered out

## Installation

```bash
composer require dnnsjsk/tailwind-php
```

## Usage

### Basic Usage

The simplest way to use TailwindPHP is with the `generate()` function:

```php
use TailwindPHP\Tailwind;

// Generate CSS from HTML containing Tailwind classes
$css = Tailwind::generate('<div class="flex items-center p-4 bg-blue-500">Hello</div>');
```

This parses the HTML, extracts class names, and generates only the CSS needed.

### Configuration Options

You can pass configuration as either a second parameter or an array:

```php
// Option 1: String as second parameter
$css = Tailwind::generate($html, '@tailwind utilities; @theme { --color-brand: #3b82f6; }');

// Option 2: Array with 'content' and 'css' keys
$css = Tailwind::generate([
    'content' => '<div class="flex p-4 bg-brand">Hello</div>',
    'css' => '
        @tailwind utilities;

        @theme {
            --color-brand: #3b82f6;
            --font-heading: "Inter", sans-serif;
        }

        .btn {
            @apply px-4 py-2 rounded-lg bg-brand text-white;
        }
    '
]);
```

The array format is useful when you want to keep content and configuration together.

### Extract Class Names

If you need to extract Tailwind class names from content separately:

```php
use TailwindPHP\Tailwind;

$classes = Tailwind::extractCandidates('<div class="flex p-4" className="bg-blue-500">');
// ['flex', 'p-4', 'bg-blue-500']
```

This is useful when you want to scan multiple files and combine the results before generating CSS.

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
