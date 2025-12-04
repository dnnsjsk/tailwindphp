# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [1.0.1] - 2025-12-04

### Fixed

- CSS nesting now correctly prefixes all selectors in a selector list
  - `.parent { h1, h2, h3 { ... } }` now correctly outputs `.parent h1, .parent h2, .parent h3 { ... }`
  - Previously only the first selector was prefixed
  - Commas inside pseudo-classes like `:where()`, `:not()`, `:is()` are preserved

### Added

- 4 new tests for CSS nesting selector list handling
- `splitSelectorList()` helper for parsing comma-separated selectors

## [1.0.0] - 2025-12-04

### Added

#### Core CSS Compilation
- Full 1:1 port of TailwindCSS 4.x to PHP (v4.1.17)
- All utility classes (364 utilities across 15 categories)
- All variants (hover, focus, responsive, dark mode, container queries, etc.)
- `@apply` directive with nested selectors
- `@theme` customization with namespace clearing
- `@utility` for custom utilities
- `@custom-variant` support
- `@layer` directives (base, components, utilities)
- `theme()`, `--theme()`, `--spacing()`, `--alpha()` CSS functions
- Preflight CSS reset
- Prefix support (`tw:`)
- Important modifier (`!`)
- Arbitrary values (`[value]`) and arbitrary variants

#### Import System
- `@import` resolution for virtual modules (`tailwindcss`, `tailwindcss/preflight`, etc.)
- File-based `@import` resolution via `importPaths` option
- Nested `@import` resolution
- Import deduplication
- Custom import resolvers (callable for virtual file systems)
- CSS @import modifiers: `layer()`, `supports()`, media queries

#### Plugin System
- `@tailwindcss/typography` - The prose class for typographic defaults
- `@tailwindcss/forms` - Form element reset and styling utilities
- Custom plugin support via `PluginInterface`
- Plugin API: `addBase()`, `addUtilities()`, `matchUtilities()`, `addComponents()`, `addVariant()`, `theme()`

#### Companion Libraries
- **clsx** - Conditional class name construction (27 tests from reference)
- **tailwind-merge** - Intelligent class conflict resolution (52 tests from reference)
- **CVA** - Class Variance Authority for component variants (50 tests from reference)
- `cn()` - Combined clsx + tailwind-merge (shadcn/ui pattern)
- `variants()` - Declarative component variant configuration
- `compose()` - Merge multiple variant components
- `merge()` - Tailwind class conflict resolution
- `join()` - Simple class joining

#### CLI
- 1:1 port of @tailwindcss/cli
- `-i, --input` - Input CSS file
- `-o, --output` - Output file
- `-w, --watch` - Watch mode for development
- `-m, --minify` - Minified output for production
- `--optimize` - Optimize without minifying
- `--cwd` - Custom working directory
- `@source` directive for content scanning

#### Additional Features
- CSS minification
- File-based caching with TTL support
- `tw-animate-css` virtual module support
- `color-mix()` to `oklab()` conversion
- Vendor prefixes (autoprefixer equivalent)
- Keyframe handling and hoisting

### Technical Details

- **PHP 8.2+** required
- **3,807 tests** passing
- No external runtime dependencies
- Zero Node.js requirement

[1.0.1]: https://github.com/dnnsjsk/tailwindphp/releases/tag/v1.0.1
[1.0.0]: https://github.com/dnnsjsk/tailwindphp/releases/tag/v1.0.0
