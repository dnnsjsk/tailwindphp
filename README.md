# Tailwind PHP

A 1:1 port of TailwindCSS 4.x to PHP focused on **string-to-string CSS compilation**. Generate Tailwind CSS using pure PHP — no Node.js required. This entire codebase was written by Claude, with the goal of creating an automated, always up-to-date Tailwind port that tests directly against TailwindCSS's reference test files.

## Scope

This port (for now) focuses on **string-to-string CSS compilation**. Full filesystem support with `@import` resolution may come in a later version.

```php
// Input: CSS string with Tailwind directives
$input = '@tailwind utilities; @theme { --color-brand: #3b82f6; }';

// Output: Standard CSS string
$output = Tailwind::generate('<div class="bg-brand p-4">', $input);
```

**What's included:**
- All CSS compilation features (utilities, variants, directives, functions)
- No external dependencies beyond PHP

**What's NOT included (for now):**
- File system access — No `@import` file resolution, no reading CSS files
- JavaScript runtime — No `@plugin` execution, no `tailwind.config.js`
- IDE tooling — No IntelliSense, autocomplete, or source maps

If you need file-based imports or JS plugins, preprocess your CSS before passing it to this library.

## Status

✅ **1,322 tests passing** — Feature complete for core TailwindCSS functionality.

| Test Suite | Status |
|------------|--------|
| Utilities | 547/547 ✅ |
| Variants | 139/139 ✅ |
| Integration | 78/78 ✅ |
| CSS Functions | 60/60 ✅ |
| UI Spec | 68/68 ✅ |

### Not Supported

- `@import` — No file system access, preprocess imports before passing to this library
- `@plugin` / `@config` — No JavaScript runtime
- IDE tooling — No IntelliSense, autocomplete, or source maps

Everything else works.

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

### Architecture

The codebase mirrors TailwindCSS's structure — same file names, same organization:

```
src/
├── _tailwindphp/                # PHP-specific helpers (NOT part of the TailwindCSS port)
│   ├── LightningCss.php         # CSS optimizations (lightningcss Rust library equivalent)
│   ├── CandidateParser.php      # Candidate parsing for compilation
│   └── CssFormatter.php         # CSS output formatting
│
├── utilities/                   # Utility implementations (split from utilities.ts)
│   ├── accessibility.php        # sr-only, forced-colors
│   ├── backgrounds.php          # bg-*, gradient-*, from-*, via-*, to-*
│   ├── borders.php              # border-*, rounded-*, divide-*, outline-*
│   ├── effects.php              # shadow-*, opacity-*, mix-blend-*
│   ├── filters.php              # blur-*, brightness-*, contrast-*, etc.
│   ├── flexbox.php              # flex-*, grid-*, gap-*, justify-*, align-*
│   ├── interactivity.php        # cursor-*, scroll-*, touch-*, select-*
│   ├── layout.php               # display, position, z-*, overflow-*, etc.
│   ├── masks.php                # mask-linear-*, mask-radial-*, mask-conic-*, mask-x/y/t/r/b/l-*
│   ├── sizing.php               # w-*, h-*, min-*, max-*, size-*
│   ├── spacing.php              # m-*, p-*, space-*
│   ├── svg.php                  # fill-*, stroke-*
│   ├── tables.php               # border-collapse, table-layout
│   ├── transforms.php           # translate-*, rotate-*, scale-*, skew-*
│   ├── transitions.php          # transition-*, duration-*, ease-*, delay-*
│   └── typography.php           # font-*, text-*, leading-*, tracking-*, text-shadow-*
│
├── utils/                       # Helper functions (ported from utils/)
│   ├── brace-expansion.php      # Brace expansion parsing
│   ├── compare.php              # Value comparison
│   ├── compare-breakpoints.php  # Breakpoint comparison
│   ├── decode-arbitrary-value.php # Arbitrary value decoding
│   ├── default-map.php          # Default value mapping
│   ├── dimensions.php           # Dimension parsing
│   ├── escape.php               # CSS escaping
│   ├── infer-data-type.php      # Data type inference
│   ├── is-color.php             # Color detection
│   ├── is-valid-arbitrary.php   # Arbitrary value validation
│   ├── math-operators.php       # Math operation handling
│   ├── replace-shadow-colors.php # Shadow color replacement
│   ├── segment.php              # String segmentation
│   ├── to-key-path.php          # Key path conversion
│   ├── topological-sort.php     # Dependency sorting
│   └── variables.php            # CSS variable handling
│
├── index.php                    # Main entry point, compile() function
├── ast.php                      # AST nodes and toCss()
├── candidate.php                # Candidate parsing (class name → parts)
├── compile.php                  # Candidate to CSS compilation
├── css-functions.php            # theme(), --theme(), --spacing(), --alpha()
├── css-parser.php               # CSS parsing
├── default-theme.php            # Default Tailwind theme values
├── design-system.php            # Central registry for utilities/variants
├── theme.php                    # Theme value resolution
├── utilities.php                # Utility registration and lookup
├── value-parser.php             # CSS value parsing
├── variants.php                 # Variant handling (hover, focus, responsive, etc.)
└── walk.php                     # AST traversal
```

**Note:** TailwindCSS's `utilities.ts` is 6,000+ lines. We split it into `src/utilities/` (one file per category) for maintainability.

### Port Deviation Markers

All implementation files are documented with `@port-deviation` markers explaining where and why the PHP implementation differs from TypeScript:

| Marker | Meaning |
|--------|---------|
| `@port-deviation:none` | Direct 1:1 port with no deviations |
| `@port-deviation:async` | PHP uses synchronous code (no async/await) |
| `@port-deviation:storage` | Different data structures (array vs Map/Set) |
| `@port-deviation:types` | PHPDoc instead of TypeScript types |
| `@port-deviation:sourcemaps` | Source map tracking omitted |
| `@port-deviation:omitted` | Entire module/feature not ported (outside scope) |
| `@port-deviation:errors` | Different error handling approach |
| `@port-deviation:enum` | PHP constants instead of TypeScript enums |
| `@port-deviation:dispatch` | Different function dispatch pattern |
| `@port-deviation:structure` | Different code organization |
| `@port-deviation:helper` | PHP-specific helper not in original |

## Testing

Tests are automatically extracted from TailwindCSS's TypeScript test suite and compared against our PHP output. This ensures the port stays in sync with the original.

### Running Tests

```bash
# Extract tests from TypeScript source
composer extract

# Run all tests
composer test

# Run specific test file
./vendor/bin/phpunit src/utilities.test.php

# Run tests matching a pattern
./vendor/bin/phpunit --filter="translate"
```

### How It Works

1. **Extract** — Scripts in `test-coverage/` parse TailwindCSS's `.test.ts` files and extract test cases
2. **Run** — PHPUnit tests read extracted data and compare PHP output against expected CSS
3. **Verify** — Any mismatch means the PHP port has drifted from TailwindCSS behavior

### Requirements

- PHP 8.1+
- Composer

## Development

See [CLAUDE.md](CLAUDE.md) for detailed development guide, project structure, and porting phases.

## License

MIT

## Credits

This project ports [TailwindCSS](https://tailwindcss.com) by Tailwind Labs to PHP.
