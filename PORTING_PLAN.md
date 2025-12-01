# Tailwind PHP - Porting Plan

A full port of TailwindCSS 4.0 to PHP, focusing on the CSS-first approach (no JS config).

## Overview

**Goal:** Create a Composer package that compiles Tailwind CSS using pure PHP, supporting all Tailwind 4.0 CSS directives.

**Scope:**
- ✅ CSS-only configuration (`@theme`, `@utility`, `@variant`, etc.)
- ✅ All core utilities and variants
- ✅ `@apply` directive
- ✅ `theme()` function
- ❌ JS config files (removed)
- ❌ JS plugins (removed)
- ❌ `@plugin` directive (removed)
- ❌ `@config` directive (removed)

---

## Package Structure

```
tailwind-php/
├── src/
│   ├── TailwindPHP.php              # Main entry point
│   ├── Compiler.php                  # Orchestrates compilation pipeline
│   │
│   ├── Parser/
│   │   ├── CssParser.php            # CSS tokenizer/parser
│   │   ├── CandidateParser.php      # Utility class name parser
│   │   └── ValueParser.php          # CSS value parsing utilities
│   │
│   ├── Ast/
│   │   ├── Node.php                 # Base AST node interface
│   │   ├── StyleRule.php            # .selector { ... }
│   │   ├── AtRule.php               # @media, @theme, etc.
│   │   ├── Declaration.php          # property: value;
│   │   ├── Comment.php              # /* comment */
│   │   └── AstBuilder.php           # Helper for building AST
│   │
│   ├── DesignSystem/
│   │   ├── DesignSystem.php         # Central registry
│   │   ├── Theme.php                # Theme variable storage
│   │   ├── Utilities.php            # Utility registry
│   │   └── Variants.php             # Variant registry
│   │
│   ├── Utilities/
│   │   ├── StaticUtilities.php      # underline, flex, etc.
│   │   ├── ColorUtilities.php       # bg-*, text-*, border-*, etc.
│   │   ├── SpacingUtilities.php     # m-*, p-*, gap-*, etc.
│   │   ├── TypographyUtilities.php  # font-*, text-*, etc.
│   │   ├── LayoutUtilities.php      # w-*, h-*, flex-*, grid-*, etc.
│   │   ├── EffectsUtilities.php     # shadow-*, opacity-*, etc.
│   │   ├── FiltersUtilities.php     # blur-*, brightness-*, etc.
│   │   ├── TransformUtilities.php   # scale-*, rotate-*, etc.
│   │   └── InteractivityUtilities.php # cursor-*, select-*, etc.
│   │
│   ├── Variants/
│   │   ├── PseudoClassVariants.php  # hover, focus, active, etc.
│   │   ├── PseudoElementVariants.php # before, after, placeholder, etc.
│   │   ├── MediaVariants.php        # sm, md, lg, dark, print, etc.
│   │   ├── ContainerVariants.php    # @container queries
│   │   ├── AriaVariants.php         # aria-*, data-*
│   │   └── GroupVariants.php        # group-*, peer-*
│   │
│   ├── Processing/
│   │   ├── AtImport.php             # @import/@reference processing
│   │   ├── AtApply.php              # @apply directive
│   │   ├── AtTheme.php              # @theme block processing
│   │   ├── AtUtility.php            # @utility definitions
│   │   ├── AtVariant.php            # @variant/@custom-variant
│   │   ├── AtSource.php             # @source directive
│   │   └── CssFunctions.php         # theme(), --alpha(), --spacing()
│   │
│   ├── Output/
│   │   ├── CssWriter.php            # AST to CSS string
│   │   ├── Optimizer.php            # CSS optimization
│   │   └── PropertyOrder.php        # CSS property ordering
│   │
│   └── Utils/
│       ├── DefaultMap.php           # Lazy-loading cache map
│       ├── Segment.php              # String segmentation
│       └── Walker.php               # AST traversal
│
├── resources/
│   ├── preflight.css                # Preflight styles
│   ├── theme.css                    # Default theme
│   └── utilities.css                # Core utility definitions
│
├── tests/
│   ├── Unit/
│   │   ├── Parser/
│   │   ├── Ast/
│   │   ├── DesignSystem/
│   │   ├── Utilities/
│   │   ├── Variants/
│   │   └── Processing/
│   │
│   └── Integration/
│       ├── CompilerTest.php
│       ├── UtilitiesTest.php
│       └── VariantsTest.php
│
├── composer.json
├── phpunit.xml
└── README.md
```

---

## Porting Phases

### Phase 1: Foundation (Core Infrastructure)

**1.1 Project Setup**
- [ ] Create `composer.json` with autoloading
- [ ] Set up PHPUnit for testing
- [ ] Configure PHP-CS-Fixer for code style
- [ ] Set up GitHub Actions for CI

**1.2 AST System** (`src/Ast/`)
Port from: `packages/tailwindcss/src/ast.ts`

- [ ] `Node.php` - Base interface with `kind`, source location
- [ ] `StyleRule.php` - Selector + nested nodes
- [ ] `AtRule.php` - At-rule name + params + nodes
- [ ] `Declaration.php` - Property + value + important flag
- [ ] `Comment.php` - Comment content
- [ ] `AstBuilder.php` - Factory methods for creating nodes

**1.3 CSS Parser** (`src/Parser/CssParser.php`)
Port from: `packages/tailwindcss/src/css-parser.ts` (~718 lines)

- [ ] Character-by-character tokenizer
- [ ] Handle comments, strings, at-rules, selectors
- [ ] Track source locations
- [ ] Parse nested rules and declarations
- [ ] Extract license comments

**1.4 AST Walker** (`src/Utils/Walker.php`)
Port from: `packages/tailwindcss/src/walk.ts` (~182 lines)

- [ ] Enter/exit phase hooks
- [ ] Walk actions (Continue, Skip, Stop, Replace)
- [ ] Recursive traversal with context

**1.5 CSS Writer** (`src/Output/CssWriter.php`)
Port from: `ast.ts` `toCss()` function

- [ ] Convert AST back to CSS string
- [ ] Handle indentation and formatting
- [ ] Minification option

---

### Phase 2: Design System Core

**2.1 Theme System** (`src/DesignSystem/Theme.php`)
Port from: `packages/tailwindcss/src/theme.ts` (~305 lines)

- [ ] Store theme variables with namespacing
- [ ] Resolve theme values by path
- [ ] Handle theme options (INLINE, REFERENCE, DEFAULT)
- [ ] Manage keyframes

**2.2 Design System** (`src/DesignSystem/DesignSystem.php`)
Port from: `packages/tailwindcss/src/design-system.ts` (~234 lines)

- [ ] Central registry for theme, utilities, variants
- [ ] Candidate parsing interface
- [ ] Utility compilation interface
- [ ] Caching layer

**2.3 Utilities Registry** (`src/DesignSystem/Utilities.php`)
Port from: `packages/tailwindcss/src/utilities.ts` (partial)

- [ ] Static utility registration
- [ ] Functional utility registration
- [ ] Utility lookup and caching

**2.4 Variants Registry** (`src/DesignSystem/Variants.php`)
Port from: `packages/tailwindcss/src/variants.ts` (partial)

- [ ] Static variant registration
- [ ] Functional variant registration
- [ ] Compound variant support
- [ ] Variant ordering

---

### Phase 3: Parsing & Candidates

**3.1 Candidate Parser** (`src/Parser/CandidateParser.php`)
Port from: `packages/tailwindcss/src/candidate.ts` (~900 lines)

- [ ] Parse utility class names
- [ ] Extract variants (colon-separated)
- [ ] Handle arbitrary values `[...]`
- [ ] Parse modifiers `/50`, `/[50%]`
- [ ] Handle negative values `-m-4`
- [ ] Important flag `!`

**3.2 Value Parser** (`src/Parser/ValueParser.php`)
Port from: Various utility value parsing

- [ ] Parse color values
- [ ] Parse spacing values
- [ ] Parse arbitrary CSS values
- [ ] Handle CSS variables

---

### Phase 4: Core Utilities

Port from: `packages/tailwindcss/src/utilities.ts` + theme defaults

**4.1 Layout Utilities**
- [ ] Display: `block`, `flex`, `grid`, `hidden`, etc.
- [ ] Position: `static`, `relative`, `absolute`, `fixed`, `sticky`
- [ ] Positioning: `top-*`, `right-*`, `bottom-*`, `left-*`, `inset-*`
- [ ] Sizing: `w-*`, `h-*`, `min-w-*`, `max-w-*`, `size-*`
- [ ] Overflow: `overflow-*`
- [ ] Z-index: `z-*`

**4.2 Flexbox & Grid**
- [ ] Flex: `flex-*`, `grow-*`, `shrink-*`, `basis-*`
- [ ] Flex direction: `flex-row`, `flex-col`
- [ ] Flex wrap: `flex-wrap`, `flex-nowrap`
- [ ] Justify: `justify-*`
- [ ] Align: `items-*`, `content-*`, `self-*`
- [ ] Grid: `grid-cols-*`, `grid-rows-*`, `col-*`, `row-*`, `gap-*`
- [ ] Place: `place-*`
- [ ] Order: `order-*`

**4.3 Spacing**
- [ ] Margin: `m-*`, `mx-*`, `my-*`, `mt-*`, `mr-*`, `mb-*`, `ml-*`, `ms-*`, `me-*`
- [ ] Padding: `p-*`, `px-*`, `py-*`, `pt-*`, `pr-*`, `pb-*`, `pl-*`, `ps-*`, `pe-*`
- [ ] Space between: `space-x-*`, `space-y-*`

**4.4 Typography**
- [ ] Font family: `font-sans`, `font-serif`, `font-mono`
- [ ] Font size: `text-xs`, `text-sm`, `text-base`, etc.
- [ ] Font weight: `font-thin`, `font-bold`, etc.
- [ ] Font style: `italic`, `not-italic`
- [ ] Letter spacing: `tracking-*`
- [ ] Line height: `leading-*`
- [ ] Text align: `text-left`, `text-center`, etc.
- [ ] Text color: `text-*`
- [ ] Text decoration: `underline`, `line-through`, `no-underline`
- [ ] Text transform: `uppercase`, `lowercase`, `capitalize`
- [ ] Text overflow: `truncate`, `text-ellipsis`
- [ ] Whitespace: `whitespace-*`
- [ ] Word break: `break-*`

**4.5 Backgrounds**
- [ ] Background color: `bg-*`
- [ ] Background opacity: `bg-opacity-*`
- [ ] Background image: `bg-none`, `bg-gradient-*`
- [ ] Background size: `bg-auto`, `bg-cover`, `bg-contain`
- [ ] Background position: `bg-center`, `bg-top`, etc.
- [ ] Background repeat: `bg-repeat`, `bg-no-repeat`
- [ ] Gradient stops: `from-*`, `via-*`, `to-*`

**4.6 Borders**
- [ ] Border width: `border`, `border-*`
- [ ] Border color: `border-*`
- [ ] Border style: `border-solid`, `border-dashed`, etc.
- [ ] Border radius: `rounded`, `rounded-*`
- [ ] Divide: `divide-*`
- [ ] Outline: `outline-*`
- [ ] Ring: `ring-*`

**4.7 Effects**
- [ ] Box shadow: `shadow-*`
- [ ] Opacity: `opacity-*`
- [ ] Mix blend: `mix-blend-*`
- [ ] Background blend: `bg-blend-*`

**4.8 Filters**
- [ ] Blur: `blur-*`
- [ ] Brightness: `brightness-*`
- [ ] Contrast: `contrast-*`
- [ ] Drop shadow: `drop-shadow-*`
- [ ] Grayscale: `grayscale-*`
- [ ] Hue rotate: `hue-rotate-*`
- [ ] Invert: `invert-*`
- [ ] Saturate: `saturate-*`
- [ ] Sepia: `sepia-*`
- [ ] Backdrop filters: `backdrop-*`

**4.9 Transforms**
- [ ] Scale: `scale-*`
- [ ] Rotate: `rotate-*`
- [ ] Translate: `translate-*`
- [ ] Skew: `skew-*`
- [ ] Transform origin: `origin-*`

**4.10 Transitions & Animation**
- [ ] Transition: `transition-*`
- [ ] Duration: `duration-*`
- [ ] Timing: `ease-*`
- [ ] Delay: `delay-*`
- [ ] Animation: `animate-*`

**4.11 Interactivity**
- [ ] Cursor: `cursor-*`
- [ ] Pointer events: `pointer-events-*`
- [ ] Resize: `resize-*`
- [ ] Scroll: `scroll-*`
- [ ] Touch action: `touch-*`
- [ ] User select: `select-*`
- [ ] Will change: `will-change-*`

**4.12 SVG**
- [ ] Fill: `fill-*`
- [ ] Stroke: `stroke-*`

**4.13 Accessibility**
- [ ] Screen reader: `sr-only`, `not-sr-only`

---

### Phase 5: Variants

Port from: `packages/tailwindcss/src/variants.ts`

**5.1 Pseudo-class Variants**
- [ ] `hover`, `focus`, `focus-within`, `focus-visible`
- [ ] `active`, `visited`, `target`
- [ ] `first`, `last`, `only`, `odd`, `even`
- [ ] `first-of-type`, `last-of-type`, `only-of-type`
- [ ] `empty`, `disabled`, `enabled`, `checked`, `indeterminate`
- [ ] `default`, `required`, `valid`, `invalid`, `in-range`, `out-of-range`
- [ ] `placeholder-shown`, `autofill`, `read-only`

**5.2 Pseudo-element Variants**
- [ ] `before`, `after`
- [ ] `first-letter`, `first-line`
- [ ] `marker`, `selection`
- [ ] `file`, `placeholder`
- [ ] `backdrop`

**5.3 Media/Responsive Variants**
- [ ] Breakpoints: `sm`, `md`, `lg`, `xl`, `2xl`
- [ ] `dark`, `light` (color scheme)
- [ ] `motion-safe`, `motion-reduce`
- [ ] `print`
- [ ] `portrait`, `landscape`
- [ ] `contrast-more`, `contrast-less`

**5.4 Container Query Variants**
- [ ] `@container`, `@container-*`
- [ ] Named containers

**5.5 Supports Variants**
- [ ] `supports-*`

**5.6 Aria/Data Variants**
- [ ] `aria-*` (aria-checked, aria-disabled, etc.)
- [ ] `data-*`

**5.7 Group/Peer Variants**
- [ ] `group-*`
- [ ] `peer-*`

**5.8 State Variants**
- [ ] `open`
- [ ] `closed`

**5.9 Direction Variants**
- [ ] `ltr`, `rtl`

**5.10 Arbitrary Variants**
- [ ] `[&_p]`, `[&:hover_p]`

---

### Phase 6: Directive Processing

**6.1 @import Processing** (`src/Processing/AtImport.php`)
Port from: `packages/tailwindcss/src/at-import.ts`

- [ ] Resolve file paths
- [ ] Handle `@import "..." layer(...)`
- [ ] Handle `@import "..." theme(reference)`
- [ ] Circular import detection

**6.2 @theme Processing** (`src/Processing/AtTheme.php`)
Port from: `index.ts` theme extraction

- [ ] Extract theme variables from `@theme` blocks
- [ ] Handle `@theme inline`, `@theme reference`
- [ ] Namespace prefixing

**6.3 @utility Processing** (`src/Processing/AtUtility.php`)
Port from: `index.ts` utility extraction

- [ ] Register custom utilities from `@utility` blocks
- [ ] Support functional utilities with values

**6.4 @variant/@custom-variant** (`src/Processing/AtVariant.php`)
Port from: `index.ts` variant extraction

- [ ] Register custom variants
- [ ] Handle variant dependencies (topological sort)

**6.5 @apply Processing** (`src/Processing/AtApply.php`)
Port from: `packages/tailwindcss/src/apply.ts` (~350 lines)

- [ ] Find and resolve `@apply` directives
- [ ] Topological sort for dependency resolution
- [ ] Circular dependency detection
- [ ] Inline utility CSS

**6.6 CSS Functions** (`src/Processing/CssFunctions.php`)
Port from: `packages/tailwindcss/src/css-functions.ts` (~200 lines)

- [ ] `theme(--variable)` resolution
- [ ] `--alpha(color / alpha)` processing
- [ ] `--spacing(multiplier)` calculation

**6.7 @source Processing** (`src/Processing/AtSource.php`)
- [ ] Parse `@source` glob patterns
- [ ] File scanning for candidate extraction

---

### Phase 7: Compiler Integration

**7.1 Main Compiler** (`src/Compiler.php`)
Port from: `packages/tailwindcss/src/index.ts` (~867 lines)

- [ ] `compile(string $css): CompileResult`
- [ ] `compileAst(array $ast): CompileResult`
- [ ] Pipeline orchestration
- [ ] Feature detection

**7.2 Candidate Compilation** (`src/Output/CandidateCompiler.php`)
Port from: `packages/tailwindcss/src/compile.ts` (~368 lines)

- [ ] Parse candidates
- [ ] Generate AST for each
- [ ] Apply variants
- [ ] Sort output

**7.3 Property Ordering** (`src/Output/PropertyOrder.php`)
Port from: `packages/tailwindcss/src/property-order.ts`

- [ ] CSS property sort order
- [ ] Consistent output ordering

**7.4 AST Optimizer** (`src/Output/Optimizer.php`)
Port from: `ast.ts` `optimizeAst()`

- [ ] Merge duplicate rules
- [ ] Remove empty rules
- [ ] Combine media queries

---

### Phase 8: Public API

**8.1 Main Entry Point** (`src/TailwindPHP.php`)

```php
<?php

namespace TailwindPHP;

class TailwindPHP
{
    /**
     * Compile CSS with Tailwind directives
     */
    public static function compile(string $css, array $options = []): string;

    /**
     * Compile and return result object with metadata
     */
    public static function process(string $css, array $options = []): CompileResult;

    /**
     * Generate CSS for specific utility classes
     */
    public static function generate(array $classes, array $options = []): string;

    /**
     * Scan files for utility classes
     */
    public static function scan(array $patterns): array;
}
```

**8.2 Configuration**

```php
$options = [
    'content' => ['./src/**/*.php', './templates/**/*.html'],
    'minify' => true,
    'prefix' => 'tw-',
    'important' => false,
];
```

---

### Phase 9: Testing

**9.1 Unit Tests**
Port key tests from: `packages/tailwindcss/src/*.test.ts`

- [ ] CSS Parser tests
- [ ] Candidate Parser tests
- [ ] AST tests
- [ ] Theme tests
- [ ] Walker tests

**9.2 Integration Tests**
Port from: `packages/tailwindcss/src/index.test.ts` (~4800 lines)

- [ ] Full compilation tests
- [ ] All utility output tests
- [ ] All variant tests
- [ ] Directive processing tests

**9.3 Compatibility Tests**
- [ ] Compare output with original Tailwind for same input
- [ ] Test all example CSS from Tailwind docs

---

### Phase 10: Resources & Polish

**10.1 Static Resources**
Copy from: `packages/tailwindcss/`

- [ ] `preflight.css`
- [ ] `theme.css` (default theme)
- [ ] `utilities.css` (if needed)

**10.2 Documentation**
- [ ] README.md with usage examples
- [ ] API documentation
- [ ] Migration guide from Node Tailwind

**10.3 Composer Package**
- [ ] Finalize `composer.json`
- [ ] Add to Packagist
- [ ] Version tagging

---

## File Mapping Reference

| TypeScript Source | PHP Target |
|-------------------|------------|
| `src/index.ts` | `src/Compiler.php` |
| `src/css-parser.ts` | `src/Parser/CssParser.php` |
| `src/ast.ts` | `src/Ast/*.php` |
| `src/candidate.ts` | `src/Parser/CandidateParser.php` |
| `src/design-system.ts` | `src/DesignSystem/DesignSystem.php` |
| `src/theme.ts` | `src/DesignSystem/Theme.php` |
| `src/utilities.ts` | `src/DesignSystem/Utilities.php` + `src/Utilities/*.php` |
| `src/variants.ts` | `src/DesignSystem/Variants.php` + `src/Variants/*.php` |
| `src/compile.ts` | `src/Output/CandidateCompiler.php` |
| `src/apply.ts` | `src/Processing/AtApply.php` |
| `src/at-import.ts` | `src/Processing/AtImport.php` |
| `src/css-functions.ts` | `src/Processing/CssFunctions.php` |
| `src/walk.ts` | `src/Utils/Walker.php` |

---

## Estimated Complexity

| Component | TS Lines | Est. PHP Lines | Complexity |
|-----------|----------|----------------|------------|
| CSS Parser | 718 | ~800 | Medium |
| AST System | 800 | ~600 | Low |
| Candidate Parser | 900 | ~1000 | High |
| Design System | 234 | ~300 | Low |
| Theme | 305 | ~350 | Low |
| Utilities Registry | 200 | ~250 | Low |
| Utilities (all) | 800+ | ~2000 | Medium |
| Variants Registry | 200 | ~250 | Low |
| Variants (all) | 600+ | ~1500 | Medium |
| Compiler | 867 | ~900 | High |
| @apply | 350 | ~400 | Medium |
| CSS Functions | 200 | ~250 | Low |
| Walker | 182 | ~200 | Low |
| **Total** | ~6000 | ~9000 | |

---

## Next Steps

1. **Start with Phase 1** - Get the foundation right
2. **Test early and often** - Port tests alongside implementation
3. **Validate against Tailwind output** - Ensure compatibility
4. **Iterate** - Refine based on real-world usage
