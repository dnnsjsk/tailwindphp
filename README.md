# Tailwind PHP

[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4.1.17-38bdf8?logo=tailwindcss&logoColor=white)](https://github.com/tailwindlabs/tailwindcss)
[![Tests](https://img.shields.io/badge/Tests-3,083%20passing-brightgreen)](https://github.com/dnnsjsk/tailwind-php)
[![PHP](https://img.shields.io/badge/PHP-8.0+-777BB4?logo=php&logoColor=white)](https://php.net)
[![clsx](https://img.shields.io/badge/clsx-v2.1.1-blue)](https://github.com/lukeed/clsx)
[![tailwind-merge](https://img.shields.io/badge/tailwind--merge-v3.4.0-blue)](https://github.com/dcastil/tailwind-merge)

A 1:1 port of TailwindCSS 4.x to PHP focused on **string-to-string CSS compilation**. Generate Tailwind CSS using pure PHP — no Node.js required. This entire codebase was written by Claude, with the goal of creating an automated, always up-to-date Tailwind port that tests directly against TailwindCSS's reference test files.

**Includes PHP ports of [clsx](https://github.com/lukeed/clsx) and [tailwind-merge](https://github.com/dcastil/tailwind-merge)** — the most popular companion libraries for Tailwind CSS.

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
- `cn()`, `clsx()`, `twMerge()` — class name utilities (no separate packages needed)
- No external dependencies beyond PHP

**What's NOT included (for now):**
- File system access — No `@import` file resolution, no reading CSS files
- JavaScript runtime — No `@plugin` execution, no `tailwind.config.js`
- IDE tooling — No IntelliSense, autocomplete, or source maps

If you need file-based imports or JS plugins, preprocess your CSS before passing it to this library.

## Status

✅ **3,083 tests passing** — Feature complete for core TailwindCSS functionality plus utility libraries.

| Test Suite | Tests | Status |
|------------|-------|--------|
| Core (utilities, variants, integration) | 1,322 | ✅ |
| API Coverage (utilities, modifiers, variants, directives) | 1,684 | ✅ |
| clsx (from reference test suite) | 27 | ✅ |
| tailwind-merge (from reference test suite) | 52 | ✅ |

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

### Extract Class Names

If you need to extract Tailwind class names from content separately:

```php
use TailwindPHP\Tailwind;

$classes = Tailwind::extractCandidates('<div class="flex p-4" className="bg-blue-500">');
// ['flex', 'p-4', 'bg-blue-500']
```

---

## Class Name Utilities

TailwindPHP includes PHP ports of the two most popular class name libraries in the Tailwind ecosystem. No additional packages required.

### `cn()`

Combines `clsx` (conditional classes) with `twMerge` (conflict resolution). Same pattern as [shadcn/ui](https://ui.shadcn.com/).

```php
use function TailwindPHP\cn;

// Basic usage
cn('px-2 py-1', 'px-4');
// => 'py-1 px-4' (px-4 overrides px-2)

// Conditional classes
cn('btn', ['btn-primary' => true, 'btn-disabled' => false]);
// => 'btn btn-primary'

// Override defaults with conditionals
cn('text-gray-500', ['text-red-500' => $hasError]);
// => 'text-red-500' (if $hasError is true)

// Complex component example
function Button($variant = 'default', $size = 'md', $disabled = false, $className = '') {
    return cn(
        'inline-flex items-center justify-center rounded-md font-medium transition-colors',
        'focus-visible:outline-none focus-visible:ring-2',
        [
            'bg-primary text-white hover:bg-primary/90' => $variant === 'default',
            'bg-destructive text-white hover:bg-destructive/90' => $variant === 'destructive',
            'border border-input bg-background hover:bg-accent' => $variant === 'outline',
        ],
        [
            'h-9 px-3 text-sm' => $size === 'sm',
            'h-10 px-4' => $size === 'md',
            'h-11 px-8 text-lg' => $size === 'lg',
        ],
        ['opacity-50 pointer-events-none' => $disabled],
        $className // Allow overrides from caller
    );
}
```

### `clsx()`

Port of [lukeed/clsx](https://github.com/lukeed/clsx). Construct class strings from various inputs.

```php
use function TailwindPHP\clsx;

// Strings
clsx('foo', 'bar');
// => 'foo bar'

// Objects (associative arrays) - keys with truthy values are included
clsx(['foo' => true, 'bar' => false, 'baz' => true]);
// => 'foo baz'

// Arrays
clsx(['foo', 'bar']);
// => 'foo bar'

// Mixed
clsx('foo', ['bar' => true], ['baz', 'qux']);
// => 'foo bar baz qux'

// Falsy values are ignored
clsx('foo', null, false, 'bar', undefined, 0, '');
// => 'foo bar'
```

### `twMerge()`

Port of [dcastil/tailwind-merge](https://github.com/dcastil/tailwind-merge). Merge Tailwind classes without style conflicts.

```php
use function TailwindPHP\twMerge;

// Later classes override earlier conflicting ones
twMerge('px-2 py-1 bg-red-500', 'px-4 bg-blue-500');
// => 'py-1 px-4 bg-blue-500'

// Works with variants
twMerge('hover:bg-red-500', 'hover:bg-blue-500');
// => 'hover:bg-blue-500'

// Handles arbitrary values
twMerge('text-[14px]', 'text-[16px]');
// => 'text-[16px]'

// Non-conflicting classes are preserved
twMerge('flex', 'items-center', 'justify-between');
// => 'flex items-center justify-between'
```

### `twJoin()`

Join classes without conflict resolution.

```php
use function TailwindPHP\twJoin;

twJoin('foo', 'bar', null, 'baz');
// => 'foo bar baz'
```

### Why include these?

These are common companion libraries in the Tailwind ecosystem. Including PHP ports means no Node.js dependency for anything.

---

## How It Works

### Architecture

The codebase mirrors TailwindCSS's structure — same file names, same organization:

```
src/
├── _tailwindphp/                # PHP-specific helpers (NOT part of the TailwindCSS port)
│   ├── LightningCss.php         # CSS optimizations (lightningcss Rust library equivalent)
│   ├── CandidateParser.php      # Candidate parsing for compilation
│   ├── CssFormatter.php         # CSS output formatting
│   └── lib/                     # Companion library ports
│       ├── clsx/                # clsx port (27 tests from reference)
│       └── tailwind-merge/      # tailwind-merge port (52 tests from reference)
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
│
├── index.php                    # Main entry point, compile(), cn(), clsx(), twMerge()
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

# Run library tests only
./vendor/bin/phpunit src/_tailwindphp/lib/
```

### How It Works

1. **Extract** — Scripts in `test-coverage/` parse TailwindCSS's `.test.ts` files and extract test cases
2. **Run** — PHPUnit tests read extracted data and compare PHP output against expected CSS
3. **Verify** — Any mismatch means the PHP port has drifted from TailwindCSS behavior

### Library Test Coverage

The companion libraries also have tests extracted from their reference implementations:

| Library | Reference | Tests | Coverage |
|---------|-----------|-------|----------|
| clsx | [lukeed/clsx](https://github.com/lukeed/clsx) | 27 | 100% |
| tailwind-merge | [dcastil/tailwind-merge](https://github.com/dcastil/tailwind-merge) | 52 | 78% of applicable* |

*Some tailwind-merge tests require custom configuration or exports not applicable to PHP.

### Requirements

- PHP 8.0+
- Composer

## Development

See [CLAUDE.md](CLAUDE.md) for detailed development guide, project structure, and porting phases.

## License

MIT

## Credits

This project ports:
- [TailwindCSS](https://tailwindcss.com) by Tailwind Labs
- [clsx](https://github.com/lukeed/clsx) by Luke Edwards
- [tailwind-merge](https://github.com/dcastil/tailwind-merge) by Dany Castillo
