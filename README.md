# TailwindPHP

[![TailwindCSS](https://img.shields.io/badge/TailwindCSS-v4.1.17-38bdf8?logo=tailwindcss&logoColor=white)](https://github.com/tailwindlabs/tailwindcss)
[![Tests](https://img.shields.io/badge/Tests-3,811%20passing-brightgreen)](https://github.com/dnnsjsk/tailwindphp)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777BB4?logo=php&logoColor=white)](https://php.net)

[![clsx](https://img.shields.io/badge/clsx-v2.1.1-blue)](https://github.com/lukeed/clsx)
[![tailwind-merge](https://img.shields.io/badge/tailwind--merge-v3.4.0-blue)](https://github.com/dcastil/tailwind-merge)
[![cva](https://img.shields.io/badge/cva-v1.0.0--beta.4-blue)](https://github.com/joe-bell/cva)
[![tw-animate-css](https://img.shields.io/badge/tw--animate--css-v1.4.0-blue)](https://github.com/Wombosvideo/tw-animate-css)

A 1:1 port of TailwindCSS 4.x to PHP. Generate Tailwind CSS using pure PHP — no Node.js required.

> **Built with AI** — This entire codebase (29,000+ lines, 3,807 tests) was generated using [Claude Code](https://claude.ai/code). No manual coding was done.

## Why This Exists

TailwindCSS is written in TypeScript and requires Node.js. TailwindPHP is a complete rewrite in PHP, giving you the same functionality without the Node.js dependency.

- **WordPress & PHP frameworks** — Ship Tailwind-powered plugins and themes without requiring users to install Node.js or run build tools. Just `composer require` and go.
- **Dynamic CSS** — Generate utility CSS at runtime based on user input, database content, or template variables.
- **Simpler deployment** — No Node.js in your production stack, no build step in CI, no npm in your Docker image.

Similar to how [scssphp](https://github.com/scssphp/scssphp) brought SCSS compilation to PHP, TailwindPHP brings the full Tailwind 4.x feature set to any PHP environment.

### Batteries Included

Building TailwindPHP created an opportunity to unify the Tailwind ecosystem's best tools into a single package. The JavaScript world relies on several companion libraries that are essential to any serious Tailwind project — so we ported them too:

- **[clsx](https://github.com/lukeed/clsx)** — Conditionally construct class strings (`cn('btn', isActive && 'btn-active')`)
- **[tailwind-merge](https://github.com/dcastil/tailwind-merge)** — Intelligently merge classes without conflicts (`merge('p-2', 'p-4')` → `'p-4'`)
- **[CVA](https://github.com/joe-bell/cva)** — Create component variants declaratively (`variants(['size' => ['sm', 'md', 'lg']])`)

## Table of Contents

- [Why This Exists](#why-this-exists)
- [Scope](#scope)
- [Status](#status)
- [Installation](#installation)
- [CLI](#cli)
- [Usage](#usage)
  - [Importing CSS Files](#importing-css-files)
  - [Preflight](#preflight)
  - [Minification](#minification)
  - [Caching](#caching)
- [Classname Utilities](#classname-utilities)
  - [cn()](#cn)
  - [merge()](#merge)
  - [join()](#join)
- [Variants (CVA Port)](#variants-cva-port)
  - [variants()](#variants)
  - [compose()](#compose)
- [Plugin System](#plugin-system)
  - [Built-in Plugins](#built-in-plugins)
  - [Plugin Options](#plugin-options)
  - [Creating Custom Plugins](#creating-custom-plugins)
- [How It Works](#how-it-works)
  - [Architecture](#architecture)
  - [Port Deviation Markers](#port-deviation-markers)
- [Testing](#testing)
- [Development](#development)
- [License](#license)
- [Credits](#credits)

## Scope

TailwindPHP provides full CSS compilation with support for both inline CSS and file-based imports.

```php
// Inline CSS with Tailwind directives
$css = Tailwind::generate('<div class="bg-brand p-4">', '@import "tailwindcss"; @theme { --color-brand: #3b82f6; }');

// File-based imports
$css = Tailwind::generate([
    'content' => '<div class="bg-brand p-4">',
    'importPaths' => '/path/to/styles.css',
]);
```

**What's included:**
- All CSS compilation features (utilities, variants, directives, functions)
- File-based `@import` resolution via `importPaths` option
- Virtual modules (`tailwindcss`, `tailwindcss/preflight`, `tailwindcss/utilities`, `tw-animate-css`, etc.)
- Preflight CSS reset
- Plugin system with `@tailwindcss/typography`, `@tailwindcss/forms`, and custom plugin support
- `cn()`, `variants()`, `merge()`, `join()` — class name utilities (no separate packages needed)
- No external dependencies beyond PHP

**What's NOT included:**
- IDE tooling — No IntelliSense, autocomplete, or source maps (these are editor features, not CSS compilation)

## Status

✅ **3,811 tests passing** — Feature complete for core TailwindCSS functionality plus utility libraries.

| Test Suite | Tests | Status |
|------------|-------|--------|
| Core (utilities, variants, integration) | 1,322 | ✅ |
| API Coverage (utilities, modifiers, variants, directives, plugins) | 1,774 | ✅ |
| PHP-specific unit tests (theme, design-system, utils, helpers) | 300 | ✅ |
| Import functionality | 42 | ✅ |
| Edge cases | 57 | ✅ |
| CSS Minifier | 17 | ✅ |
| Plugin system (typography, forms) | 25 | ✅ |
| clsx (from reference test suite) | 27 | ✅ |
| tailwind-merge (from reference test suite) | 52 | ✅ |
| CVA (from reference test suite) | 50 | ✅ |
| tw-animate-css | 23 | ✅ |
| Cache | 14 | ✅ |
| CLI | 26 | ✅ |

### Performance

While this is a 1:1 port focused on correctness and maintainability, PHP-specific optimizations are applied where possible:

- **toCss**: Uses array accumulation + implode instead of string concatenation, pre-computed indent strings
- **CSS Parser**: Direct character comparison instead of ord() calls, tracked buffer lengths instead of strlen()

These optimizations maintain identical output while improving performance. TypeScript remains faster due to V8's JIT compilation, but this is expected for build-time CSS generation.

See [benchmarks/](benchmarks/) for detailed comparison.

## Installation

```bash
composer require tailwindphp/tailwindphp
```

## CLI

TailwindPHP includes a command-line interface that is a **1:1 port of [@tailwindcss/cli](https://github.com/tailwindlabs/tailwindcss/tree/next/packages/%40tailwindcss-cli)** - same options, same behavior, no Node.js required.

### Quick Start

```bash
# Build CSS from an input file
./vendor/bin/tailwindphp -i ./src/app.css -o ./dist/styles.css

# Watch for changes
./vendor/bin/tailwindphp -i ./src/app.css -o ./dist/styles.css --watch

# Build minified
./vendor/bin/tailwindphp -i ./src/app.css -o ./dist/styles.css --minify
```

### Options

```bash
tailwindphp [--input input.css] [--output output.css] [--watch] [options]

Options:
  -i, --input     Input CSS file (default: @import "tailwindcss")
  -o, --output    Output file (default: stdout)
  -w, --watch     Watch for changes and rebuild as needed
  -m, --minify    Optimize and minify the output
      --optimize  Optimize the output without minifying
      --cwd       The current working directory (default: .)
  -h, --help      Display usage information
```

### Input CSS

Create an `app.css` file with your Tailwind imports and `@source` directive:

```css
@import "tailwindcss";
@source "./templates";  /* Directory to scan for classes */
```

The `@source` directive tells TailwindPHP where to find your template files. It supports:
- Directories: `@source "./templates";`
- Glob patterns: `@source "./src/**/*.php";`
- Multiple sources: Add multiple `@source` directives

### Examples

```bash
# Build from CSS with @source directive
tailwindphp -i ./src/app.css -o ./dist/styles.css

# Build minified for production
tailwindphp -i ./src/app.css -o ./dist/styles.css -m

# Watch mode with minification
tailwindphp -i ./src/app.css -o ./dist/styles.css -w -m

# Use a different working directory
tailwindphp -i app.css -o dist/styles.css --cwd=/path/to/project
```

### Global Installation

Install globally to use `tailwindphp` from anywhere:

```bash
composer global require tailwindphp/tailwindphp

# Now available globally
tailwindphp -i ./src/app.css -o ./dist/styles.css
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
$css = Tailwind::generate($html, '@import "tailwindcss"; @theme { --color-brand: #3b82f6; }');

// Option 2: Array with 'content' and 'css' keys
$css = Tailwind::generate([
    'content' => '<div class="flex p-4 bg-brand">Hello</div>',
    'css' => '
        @import "tailwindcss";

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

### Importing CSS Files

Use `importPaths` to load CSS from the filesystem with full `@import` resolution:

```php
// Single file
$css = Tailwind::generate([
    'content' => '<div class="flex btn">Hello</div>',
    'importPaths' => '/path/to/styles.css',
]);

// Directory (loads all .css files alphabetically)
$css = Tailwind::generate([
    'content' => '<div class="flex btn">Hello</div>',
    'importPaths' => '/path/to/css/',
]);

// Multiple paths
$css = Tailwind::generate([
    'content' => '<div class="flex btn card">Hello</div>',
    'importPaths' => [
        '/path/to/base.css',
        '/path/to/components/',
    ],
]);

// Combine with inline CSS
$css = Tailwind::generate([
    'content' => '<div class="flex btn custom">Hello</div>',
    'css' => '.custom { color: red; }',
    'importPaths' => '/path/to/styles.css',
]);
```

**Nested imports are resolved automatically.** If your CSS file contains `@import "./buttons.css"`, TailwindPHP resolves the path relative to the importing file.

```css
/* /path/to/styles.css */
@import "tailwindcss";
@import "./components/buttons.css";
@import "./components/cards.css";

@theme {
    --color-brand: #3b82f6;
}
```

**Deduplication:** Multiple imports of the same file or virtual module (like `@import "tailwindcss"`) are automatically deduplicated.

**Custom resolver:** For advanced use cases (virtual file systems, databases), provide a callable:

```php
$css = Tailwind::generate([
    'content' => '<div class="flex virtual-class">Hello</div>',
    'importPaths' => function (?string $uri, ?string $fromFile): ?string {
        if ($uri === null) {
            return '@import "tailwindcss"; @import "custom.css";';
        }
        if ($uri === 'custom.css') {
            return '.virtual-class { color: purple; }';
        }
        return null; // Unknown imports are silently skipped
    },
]);
```

### Preflight

Preflight is Tailwind's opinionated set of base styles, built on top of [modern-normalize](https://github.com/sindresorhus/modern-normalize). It smooths over cross-browser inconsistencies and makes it easier to work within the constraints of the design system.

For full details on what Preflight does and why, see the [official Tailwind Preflight documentation](https://tailwindcss.com/docs/preflight).

**Key resets include:**

- **Default margins removed** — Headings, paragraphs, lists, etc. have zero margin
- **Headings unstyled** — All headings have the same font-size and font-weight as normal text
- **Lists unstyled** — `ul` and `ol` have no bullets/numbers or padding
- **Images are block-level** — No more phantom space below images
- **Border styles reset** — Empty borders so you can add borders just by setting `border-width`
- **Buttons inherit fonts** — Buttons use the parent's font family, size, and line-height
- **Hidden elements stay hidden** — Elements with a `hidden` attribute are invisible unless using `hidden="until-found"`

```php
// Full import (includes theme + preflight + utilities)
$css = Tailwind::generate([
    'content' => '<div class="flex p-4">Hello</div>',
    'css' => '@import "tailwindcss";',
]);
```

#### Extending Preflight

Add your own base styles on top of Preflight using `@layer base`:

```php
$css = Tailwind::generate([
    'content' => '<article><h1>Title</h1><p>Content</p></article>',
    'css' => '
        @import "tailwindcss";
        @layer base {
            h1 { font-size: var(--text-2xl); }
            h2 { font-size: var(--text-xl); }
            a { color: var(--color-blue-600); text-decoration-line: underline; }
        }
    ',
]);
```

#### Disabling Preflight

To disable Preflight — for example when integrating into an existing project or defining your own base styles — import only the parts of Tailwind you need.

By default, `@import "tailwindcss"` is equivalent to:

```css
@layer theme, base, components, utilities;
@import "tailwindcss/theme.css" layer(theme);
@import "tailwindcss/preflight.css" layer(base);
@import "tailwindcss/utilities.css" layer(utilities);
```

To skip Preflight, simply omit its import:

```php
$css = Tailwind::generate([
    'content' => '<div class="flex p-4">Hello</div>',
    'css' => '
        @layer theme, base, components, utilities;
        @import "tailwindcss/theme.css" layer(theme);
        @import "tailwindcss/utilities.css" layer(utilities);
    ',
]);
```

#### Import Modifiers

When importing Tailwind's files individually, modifiers like `prefix()`, `theme()`, and `important` should go on their respective imports:

```css
/* Prefix affects both theme variables and utilities */
@import "tailwindcss/theme.css" layer(theme) prefix(tw);
@import "tailwindcss/utilities.css" layer(utilities) prefix(tw);

/* important affects utilities */
@import "tailwindcss/utilities.css" layer(utilities) important;

/* theme(static) or theme(inline) affects theme variables */
@import "tailwindcss/theme.css" layer(theme) theme(static);
```

### Extract Class Names

If you need to extract Tailwind class names from content separately:

```php
use TailwindPHP\Tailwind;

$classes = Tailwind::extractCandidates('<div class="flex p-4" className="bg-blue-500">');
// ['flex', 'p-4', 'bg-blue-500']
```

### Minification

Minify CSS output for production:

```php
use TailwindPHP\Tailwind;

// Option 1: Minify during generation
$css = Tailwind::generate([
    'content' => '<div class="flex p-4">Hello</div>',
    'minify' => true,
]);

// Option 2: Minify separately
$css = Tailwind::generate('<div class="flex p-4">Hello</div>');
$minified = Tailwind::minify($css);
```

The minifier removes comments, collapses whitespace, shortens hex colors (`#ffffff` to `#fff`), removes units from zero values (`0px` to `0`), and other optimizations.

### Caching

Enable file-based caching to avoid recompiling identical content:

```php
use TailwindPHP\Tailwind;

// Cache to default directory (sys_get_temp_dir()/tailwindphp)
$css = Tailwind::generate([
    'content' => '<div class="flex p-4">Hello</div>',
    'cache' => true,
]);

// Cache to custom directory
$css = Tailwind::generate([
    'content' => '<div class="flex p-4">Hello</div>',
    'cache' => '/path/to/cache',
]);

// With TTL (time-to-live in seconds)
$css = Tailwind::generate([
    'content' => '<div class="flex p-4">Hello</div>',
    'cache' => true,
    'cacheTtl' => 3600, // Expire after 1 hour
]);
```

The cache key is computed from the content, CSS configuration, and minify option. Different inputs generate different cache files.

**Clear the cache:**

```php
use TailwindPHP\Tailwind;
use function TailwindPHP\clearCache;

// Clear default cache
Tailwind::clearCache();

// Clear custom cache directory
Tailwind::clearCache('/path/to/cache');

// Or use the function
clearCache('/path/to/cache');
```

---

## Classname Utilities

TailwindPHP includes PHP ports of the popular Tailwind companion libraries. No additional packages required.

### `cn()`

The recommended utility. Combines conditional class construction with intelligent conflict resolution.

```php
use function TailwindPHP\cn;

// Basic usage - conflicts are resolved
cn('px-2 py-1', 'px-4');
// => 'py-1 px-4' (px-4 overrides px-2)

// Conditional classes
cn('btn', ['btn-primary' => true, 'btn-disabled' => false]);
// => 'btn btn-primary'

// React-style component
function Card(array $props = []): string {
    $class = cn(
        'rounded-lg border bg-card text-card-foreground shadow-sm',
        $props['class'] ?? null
    );
    return "<div class=\"{$class}\">" . ($props['children'] ?? '') . "</div>";
}

Card(['class' => 'p-6', 'children' => 'Content']);
```

### `merge()`

Merge Tailwind classes, resolving conflicts. Later classes override earlier ones.

```php
use function TailwindPHP\merge;

merge('px-2 py-1 bg-red-500', 'px-4 bg-blue-500');
// => 'py-1 px-4 bg-blue-500'

merge('hover:bg-red-500', 'hover:bg-blue-500');
// => 'hover:bg-blue-500'
```

### `join()`

Join classes without conflict resolution. Use when you know there are no conflicts.

```php
use function TailwindPHP\join;

join('foo', 'bar', null, 'baz');
// => 'foo bar baz'
```

---

## Variants (CVA Port)

PHP port of [CVA (Class Variance Authority)](https://github.com/joe-bell/cva) for creating component style variants.

### `variants()`

Create component variants with declarative configuration.

```php
use function TailwindPHP\variants;

// Define component styles
$button = variants([
    'base' => 'inline-flex items-center justify-center rounded-md font-medium',
    'variants' => [
        'variant' => [
            'default' => 'bg-primary text-white hover:bg-primary/90',
            'outline' => 'border border-input bg-background hover:bg-accent',
            'ghost' => 'hover:bg-accent hover:text-accent-foreground',
        ],
        'size' => [
            'default' => 'h-10 px-4 py-2',
            'sm' => 'h-9 px-3',
            'lg' => 'h-11 px-8',
        ],
    ],
    'defaultVariants' => [
        'variant' => 'default',
        'size' => 'default',
    ],
]);

// Usage (React-style single props object)
$button();                              // defaults applied
$button(['variant' => 'outline']);      // override variant
$button(['size' => 'sm', 'class' => 'mt-4']); // override + custom class

// Use in a component function with cn() for easy class extension
function Button(array $props = []): string {
    static $styles = null;
    $styles ??= variants([
        'base' => 'inline-flex items-center justify-center rounded-md font-medium',
        'variants' => [
            'variant' => [
                'default' => 'bg-primary text-white hover:bg-primary/90',
                'outline' => 'border border-input hover:bg-accent',
            ],
            'size' => [
                'default' => 'h-10 px-4 py-2',
                'sm' => 'h-9 px-3',
            ],
        ],
        'defaultVariants' => ['variant' => 'default', 'size' => 'default'],
    ]);

    // cn() merges variant output with custom classes, resolving conflicts
    $class = cn($styles($props), $props['class'] ?? null);
    $text = $props['children'] ?? 'Button';
    return "<button class=\"{$class}\">{$text}</button>";
}

// Custom classes override variant defaults via cn()
Button(['variant' => 'outline', 'size' => 'sm', 'class' => 'mt-4 px-8']);
```

### `compose()`

Merge multiple variant components into one.

```php
use function TailwindPHP\variants;
use function TailwindPHP\compose;

$box = variants(['variants' => ['shadow' => ['sm' => 'shadow-sm', 'md' => 'shadow-md']]]);
$stack = variants(['variants' => ['gap' => ['1' => 'gap-1', '2' => 'gap-2']]]);
$card = compose($box, $stack);

$card(['shadow' => 'md', 'gap' => '2']); // => 'shadow-md gap-2'
```

---

## Plugin System

TailwindPHP includes PHP ports of official TailwindCSS plugins. These are 1:1 ports following the same logic as the JavaScript originals.

### Built-in Plugins

| Plugin | Description |
|--------|-------------|
| `@tailwindcss/typography` | Beautiful typographic defaults for HTML content |
| `@tailwindcss/forms` | Form element reset and styling utilities |

### Usage

Use the `@plugin` directive in your CSS:

```php
$css = Tailwind::generate([
    'content' => '<article class="prose prose-lg"><h1>Hello</h1><p>Content here</p></article>',
    'css' => '
        @plugin "@tailwindcss/typography";
        @import "tailwindcss/utilities.css";
    '
]);
```

### Plugin Options

Pass options using CSS block syntax:

```php
// Typography with custom class name
$css = '
    @plugin "@tailwindcss/typography" {
        className: "markdown";
    }
    @import "tailwindcss/utilities.css";
';

// Forms with class strategy (no base styles)
$css = '
    @plugin "@tailwindcss/forms" {
        strategy: "class";
    }
    @import "tailwindcss/utilities.css";
';
```

### Typography Plugin

Generates the `.prose` class with beautiful typographic defaults:

```php
// Basic usage
$css = Tailwind::generate('<div class="prose">...</div>', '@plugin "@tailwindcss/typography"; @import "tailwindcss/utilities.css";');

// Available classes: prose, prose-sm, prose-lg, prose-xl, prose-2xl
// Modifiers: prose-invert (dark mode), prose-slate, prose-gray, etc.
```

### Forms Plugin

Provides form element utilities:

```php
// Class strategy - explicit form classes
$css = Tailwind::generate(
    '<input class="form-input" /><select class="form-select">...</select>',
    '@plugin "@tailwindcss/forms" { strategy: "class"; } @import "tailwindcss/utilities.css";'
);

// Available classes: form-input, form-textarea, form-select, form-multiselect,
// form-checkbox, form-radio
```

### Creating Custom Plugins

You can create your own plugins by implementing the `PluginInterface`:

```php
use TailwindPHP\Plugin\PluginInterface;
use TailwindPHP\Plugin\PluginAPI;

class MyCustomPlugin implements PluginInterface
{
    public function getName(): string
    {
        return 'my-custom-plugin';
    }

    public function __invoke(PluginAPI $api, array $options = []): void
    {
        // Add static utilities
        $api->addUtilities([
            '.btn' => [
                'padding' => '0.5rem 1rem',
                'border-radius' => '0.25rem',
                'font-weight' => '600',
            ],
            '.btn-primary' => [
                'background-color' => 'blue',
                'color' => 'white',
            ],
        ]);

        // Add functional utilities with values
        $api->matchUtilities(
            [
                'tab' => function ($value) {
                    return ['tab-size' => $value];
                },
            ],
            ['values' => ['1' => '1', '2' => '2', '4' => '4', '8' => '8']]
        );

        // Add component classes
        $api->addComponents([
            '.card' => [
                'background-color' => 'white',
                'border-radius' => '0.5rem',
                'padding' => '1rem',
                'box-shadow' => '0 1px 3px rgba(0,0,0,0.1)',
            ],
        ]);

        // Add custom variants
        $api->addVariant('hocus', '&:hover, &:focus');

        // Access theme values
        $primary = $api->theme('colors.blue.500', '#3b82f6');
    }

    public function getThemeExtensions(array $options = []): array
    {
        return []; // Return theme additions if needed
    }
}
```

Register and use your plugin:

```php
use TailwindPHP\Tailwind;
use function TailwindPHP\registerPlugin;

// Register the plugin
registerPlugin(new MyCustomPlugin());

// Use it in CSS
$css = Tailwind::generate(
    '<div class="btn btn-primary card tab-4">...</div>',
    '@plugin "my-custom-plugin"; @import "tailwindcss/utilities.css";'
);
```

### Architecture

The plugin system follows the TailwindCSS plugin API pattern:

```
src/plugin.php                    # PluginInterface, PluginAPI, PluginManager
src/plugin/plugins/
├── typography-plugin.php         # @tailwindcss/typography port
└── forms-plugin.php              # @tailwindcss/forms port
```

**PluginAPI** provides the same methods as TailwindCSS:
- `addBase(array $css)` — Add base styles
- `addUtilities(array $utilities)` — Add static utilities
- `matchUtilities(array $utilities, array $options)` — Add functional utilities with values
- `addComponents(array $components)` — Add component classes
- `addVariant(string $name, string|array $variant)` — Add custom variants
- `matchVariant(string $name, callable $callback, array $options)` — Add functional variants
- `theme(string $path, mixed $default)` — Access theme values
- `config(string $path, mixed $default)` — Access config values

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
│       ├── tailwind-merge/      # tailwind-merge port (52 tests from reference)
│       └── cva/                 # CVA port (50 tests from reference)
│
├── plugin/                      # Plugin system
│   └── plugins/                 # Built-in plugin implementations
│       ├── typography-plugin.php  # @tailwindcss/typography port
│       └── forms-plugin.php       # @tailwindcss/forms port
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
├── index.php                    # Main entry point, compile(), cn(), variants(), merge(), join()
├── ast.php                      # AST nodes and toCss()
├── candidate.php                # Candidate parsing (class name → parts)
├── compile.php                  # Candidate to CSS compilation
├── css-functions.php            # theme(), --theme(), --spacing(), --alpha()
├── css-parser.php               # CSS parsing
├── design-system.php            # Central registry for utilities/variants
├── plugin.php                   # Plugin system (PluginInterface, PluginAPI, PluginManager)
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

Tests ensure the PHP port stays in sync with TailwindCSS's TypeScript implementation. We use two approaches:

### Test Types

1. **Extraction-based tests** — Tests automatically extracted from TailwindCSS's `.test.ts` files using scripts in `test-coverage/`. These cover complex utilities, variants, and integration tests.

2. **Unit test ports** — Direct PHP ports of simpler TypeScript test files (AST, parsing, escaping, etc.). These live alongside their source files as `*.test.php`.

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

### How Extraction Works

1. **Extract** — Scripts in `test-coverage/` parse TailwindCSS's `.test.ts` files and extract test cases to JSON
2. **Run** — PHPUnit tests read extracted data and compare PHP output against expected CSS
3. **Verify** — Any mismatch means the PHP port has drifted from TailwindCSS behavior

### Test Coverage

| Category | Tests | Source |
|----------|-------|--------|
| **Extraction-based** | | |
| utilities.test.php | 547 | utilities.test.ts |
| variants.test.php | 139 | variants.test.ts |
| index.test.php | 78 | index.test.ts |
| css_functions.test.php | 60 | css-functions.test.ts |
| ui_spec.test.php | 68 | ui.spec.ts |
| **Unit test ports** | | |
| css_parser.test.php | 70 | css-parser.test.ts |
| candidate.test.php | 66 | candidate.test.ts |
| decode_arbitrary_value.test.php | 60 | decode-arbitrary-value.test.ts |
| ast.test.php | 18 | ast.test.ts |
| escape.test.php | 10 | escape.test.ts |
| + 12 more unit test files | ~180 | Various .test.ts files |
| **Library tests** | | |
| clsx.test.php | 27 | clsx/test/*.js |
| tailwind_merge.test.php | 52 | tailwind-merge/tests/*.ts |
| cva.test.php | 50 | cva/src/index.test.ts |
| **API coverage tests** | 1,684 | Custom exhaustive tests |
| **Plugin tests** | 25 | Plugin functionality |

### Requirements

- PHP 8.2+
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
- [CVA](https://github.com/joe-bell/cva) by Joe Bell
