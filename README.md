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

## Testing

```bash
# Run all tests
./vendor/bin/phpunit

# Run compliance tests only
./vendor/bin/phpunit src/utilities.test.php
```

We test against TailwindCSS's actual test suite (364 tests extracted from `utilities.test.ts`).

## Roadmap

- [x] All utility classes (364/364 tests passing)
- [ ] Variants (hover, focus, responsive, dark mode)
- [ ] `@apply` directive
- [ ] `@theme` customization
- [ ] `theme()` function

## Development

See [DEVELOPMENT.md](DEVELOPMENT.md) for detailed development guide, project structure, and porting phases.

## License

MIT

## Credits

This project ports [TailwindCSS](https://tailwindcss.com) by Tailwind Labs to PHP.
