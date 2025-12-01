# Tailwind PHP - Porting Plan

A full 1:1 port of TailwindCSS 4.0 to PHP, focusing on the CSS-first approach (no JS config).

## Current Status

**Test Suite:** 698 tests passing (364 TailwindCSS compliance tests + 334 unit tests)

### Completed
- All utility functions (364/364 compliance tests passing)
- CSS parser and AST
- Theme system
- Candidate parsing
- Value parser
- Selector parser
- All utility categories: layout, flexbox, spacing, sizing, typography, borders, effects, filters, transforms, transitions, backgrounds, interactivity, accessibility, SVG, tables

### In Progress
- Variants system
- Full design system integration
- @apply directive
- @import handling

---

## Overview

**Goal:** Create a Composer package that compiles Tailwind CSS using pure PHP.

**Simple API:**
```php
use TailwindPHP\TailwindPHP;

// Extract classes from HTML/content and generate CSS
$css = TailwindPHP::generate('<div class="flex items-center p-4 bg-blue-500">...</div>');
```

**Scope:**
- CSS-only configuration (`@theme`, `@utility`, `@variant`, etc.)
- All core utilities and variants
- `@apply` directive
- `theme()` function
- No JS config files
- No JS plugins

---

## Package Structure

```
tailwind-php/
├── src/
│   ├── TailwindPHP.php              # Main public API
│   ├── ast.php                      # AST nodes & toCss
│   ├── candidate.php                # Class name parsing
│   ├── compile.php                  # Candidate compilation
│   ├── css-parser.php               # CSS tokenizer
│   ├── design-system.php            # Central registry
│   ├── property-order.php           # CSS property ordering
│   ├── selector-parser.php          # Selector parsing
│   ├── theme.php                    # Theme management
│   ├── utilities.php                # Utility registry & builder
│   ├── value-parser.php             # Value parsing
│   ├── variants.php                 # Variant handling
│   ├── walk.php                     # AST traversal
│   │
│   ├── utilities/                   # Utility implementations
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
│   └── utils/                       # Helper utilities
│       ├── brace-expansion.php
│       ├── compare.php
│       ├── decode-arbitrary-value.php
│       ├── default-map.php
│       ├── escape.php
│       ├── segment.php
│       └── ...
│
├── tests/
│   ├── TestHelper.php               # Test utilities
│   └── ...
│
├── extracted-tests/                 # TailwindCSS test cases (*.ts)
├── scripts/
│   └── extract-tests.php            # Extract tests from Tailwind source
│
├── composer.json
├── phpunit.xml
└── README.md
```

---

## Test Strategy

The test suite consists of:

1. **TailwindCSS Compliance Tests** (`src/utilities.test.php`)
   - 364 tests parsed from TailwindCSS source
   - Verifies output matches TailwindCSS exactly
   - Auto-parsed from `extracted-tests/*.ts`

2. **Unit Tests** (`tests/`)
   - Tests for individual components (parser, AST, etc.)

To update compliance tests when TailwindCSS updates:
```bash
php scripts/extract-tests.php
```

---

## Porting Phases

### Phase 1: Project Setup
- [x] Composer package with PSR-4 autoloading
- [x] PHPUnit configuration
- [x] Directory structure

### Phase 2: Foundation
- [x] Utils (segment, escape, decode-arbitrary-value, etc.)
- [x] CSS parser
- [x] AST handling
- [x] Value parser
- [x] Selector parser

### Phase 3: Theme & Design System
- [x] Theme management
- [x] Property ordering
- [ ] Full design system integration
- [ ] Sort utilities

### Phase 4: Utilities (Complete)
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
- [ ] Main entry point
- [ ] Public API
- [ ] Full integration tests

### Phase 8: Polish
- [ ] Documentation
- [ ] Performance optimization
- [ ] Publish to Packagist
