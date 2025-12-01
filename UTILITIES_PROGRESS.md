# Utilities Porting Progress

This file tracks the progress of porting utilities from TailwindCSS 4.0 to PHP.

## Summary
- **Total Tests in Original**: 267
- **Tests Ported**: 219 (4 accessibility + 20 layout + 30 flexbox + 31 spacing + 31 sizing + 31 typography + 47 borders + 25 effects)
- **Tests Remaining**: 48

## Utility Categories

### ✅ Completed

| Category | Tests | Implementation | Test File |
|----------|-------|----------------|-----------|
| Accessibility | 2 | `src/utilities/accessibility.php` | `tests/utilities/AccessibilityTest.php` |
| Pointer Events | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Visibility | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Position | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Isolation | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Float | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Clear | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Box Sizing | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Display | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Overflow | 3 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Overscroll | 3 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Scroll Behavior | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Object Fit/Position | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Break Before/Inside/After | 3 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Box Decoration | 1 | `src/utilities/layout.php` | `tests/utilities/LayoutTest.php` |
| Flexbox (flex-direction, wrap, grow, shrink) | 14 | `src/utilities/flexbox.php` | `tests/utilities/FlexboxTest.php` |
| Grid (cols, rows, flow, auto-cols/rows) | 10 | `src/utilities/flexbox.php` | `tests/utilities/FlexboxTest.php` |
| Gap | 3 | `src/utilities/flexbox.php` | `tests/utilities/FlexboxTest.php` |
| Justify/Align/Place utilities | 10 | `src/utilities/flexbox.php` | `tests/utilities/FlexboxTest.php` |
| Margin (m, mx, my, mt, mr, mb, ml, ms, me) | 12 | `src/utilities/spacing.php` | `tests/utilities/SpacingTest.php` |
| Padding (p, px, py, pt, pr, pb, pl, ps, pe) | 12 | `src/utilities/spacing.php` | `tests/utilities/SpacingTest.php` |
| Space (space-x, space-y, reverse) | 7 | `src/utilities/spacing.php` | `tests/utilities/SpacingTest.php` |
| Width (w, min-w, max-w) | 12 | `src/utilities/sizing.php` | `tests/utilities/SizingTest.php` |
| Height (h, min-h, max-h) | 12 | `src/utilities/sizing.php` | `tests/utilities/SizingTest.php` |
| Size | 5 | `src/utilities/sizing.php` | `tests/utilities/SizingTest.php` |
| Font Style (italic) | 2 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Font Weight | 3 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Text Decoration | 3 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Text Transform | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Text Align | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Text Wrap | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Whitespace | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Word Break | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Hyphens | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| List Style | 4 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Vertical Align | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Leading (line-height) | 3 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Tracking (letter-spacing) | 3 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Text Indent | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Truncate/Text Overflow | 2 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Font Variant Numeric | 1 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Font Smoothing | 2 | `src/utilities/typography.php` | `tests/utilities/TypographyTest.php` |
| Border Radius (rounded-*) | 14 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Border Width (border-*) | 11 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Border Style | 6 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Border Collapse | 2 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Outline Style | 5 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Outline Width | 2 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Outline Offset | 2 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Divide Width | 4 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Divide Style | 5 | `src/utilities/borders.php` | `tests/utilities/BordersTest.php` |
| Opacity | 3 | `src/utilities/effects.php` | `tests/utilities/EffectsTest.php` |
| Box Shadow | 7 | `src/utilities/effects.php` | `tests/utilities/EffectsTest.php` |
| Inset Shadow | 3 | `src/utilities/effects.php` | `tests/utilities/EffectsTest.php` |
| Drop Shadow | 3 | `src/utilities/effects.php` | `tests/utilities/EffectsTest.php` |
| Mix Blend Mode | 6 | `src/utilities/effects.php` | `tests/utilities/EffectsTest.php` |
| Background Blend Mode | 4 | `src/utilities/effects.php` | `tests/utilities/EffectsTest.php` |

### 🔄 In Progress

| Category | Tests | Status |
|----------|-------|--------|
| - | - | - |

### 📋 Not Started

#### High Priority (Core Layout & Spacing)
| Category | Tests | Notes |
|----------|-------|-------|
| Inset (top, right, bottom, left, etc.) | 18 | Requires theme resolution for spacing |
| Z-Index | 11 | Includes functional utilities |
| Order | 9 | Includes functional utilities |

#### Sizing
| Category | Tests | Notes |
|----------|-------|-------|
| Aspect Ratio | 1 | aspect-square, aspect-video, etc. |
| Columns | 1 | columns utility |

#### Flexbox & Grid
| Category | Tests | Notes |
|----------|-------|-------|
| Display (flex, grid) | 10 | flex, grid, inline variants |
| Grid | 3 | col, auto-cols, grid-cols, etc. |

#### Borders & Outlines
| Category | Tests | Notes |
|----------|-------|-------|
| Ring | 2 | ring utilities |

#### Backgrounds & Colors
| Category | Tests | Notes |
|----------|-------|-------|
| Color (bg) | 1 | background colors |
| Gradient | 2 | from, via, to |
| Fill | 1 | SVG fill |
| Stroke | 1 | SVG stroke |

#### Effects & Filters
| Category | Tests | Notes |
|----------|-------|-------|
| Filter | 1 | blur, brightness, etc. |
| Transition | 4 | transition, delay, duration, ease |
| Transform | 13 | translate, rotate, scale, etc. |

#### Interactivity
| Category | Tests | Notes |
|----------|-------|-------|
| Cursor | 1 | cursor utilities |
| Select | 1 | user-select utilities |
| Scroll | 22 | scroll-snap, scroll-m, scroll-p |
| Accent | 1 | accent-color |
| Caret | 1 | caret-color |

#### Other
| Category | Tests | Notes |
|----------|-------|-------|
| Content | 3 | place-content, align-content, content |
| Place | 2 | place-items, place-self |
| Align | 7 | items, justify, self, etc. |
| Forced Color | 1 | forced-color-adjust |
| Container | 1 | @container queries |
| Other (misc) | 5 | visibility, indent, decoration, etc. |

## Implementation Notes

### Dependencies
Many utilities depend on:
1. **Theme resolution** - spacing, colors, etc. from `@theme`
2. **CSS variables** - `var(--spacing-4)`, etc.
3. **Functional utilities** - utilities that accept values like `p-4`, `m-[10px]`

### Test Patterns
The original tests use two patterns:
1. `run(['utility-name'])` - Simple test with default theme
2. `compileCss(css\`@theme {...}\`, ['utility'])` - Test with custom theme

### Priority Order
1. Static utilities (no theme dependency)
2. Spacing utilities (margin, padding, gap, inset)
3. Sizing utilities (width, height)
4. Flexbox & Grid
5. Typography
6. Colors & Backgrounds
7. Effects & Filters
8. Interactivity
