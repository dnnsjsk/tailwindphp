# Utilities Test Coverage

Tests extracted from `tailwindcss/packages/tailwindcss/src/utilities.test.ts`

## Coverage Stats

| Metric | Value |
|--------|-------|
| Source File Lines | 28,711 |
| Original Tests | 267 |
| Parsed Tests | 364 |
| Test Files Generated | 74 |

## Test Categories

| Category | Tests | Lines |
|----------|-------|-------|
| margin | 42 | 7,115 |
| scroll | 22 | 789 |
| border | 21 | 1,183 |
| transform | 19 | 1,533 |
| text | 17 | 1,483 |
| grid | 12 | 822 |
| padding | 12 | 690 |
| inset | 11 | 1,746 |
| divide | 8 | 507 |
| align | 7 | 477 |
| flex | 6 | 228 |
| bg | 5 | 1,245 |
| transition | 5 | 338 |
| sizing | 4 | 350 |
| space | 4 | 144 |
| overflow | 4 | 163 |
| break | 3 | 173 |
| place | 3 | 189 |
| gap | 3 | 82 |
| gradient | 3 | 1,162 |
| touch | 3 | 142 |
| overscroll | 3 | 81 |
| accessibility | 2 | 31 |
| table | 2 | 28 |
| ring | 2 | 785 |
| outline | 2 | 414 |
| other | 9 | 715 |
| ... and 20 more categories | | |

## How Tests Are Generated

The tests are extracted by parsing the TypeScript test file and finding:
1. `test('name', async () => { ... })` blocks
2. `await run([...classes])` calls within each test
3. `.toMatchInlineSnapshot(\`...\`)` assertions for expected CSS output
4. `.toEqual('')` assertions for expecting empty output

Each test case captures:
- Test name
- Input classes array
- Expected CSS output

## Test File Structure

```
tests/
├── accent.ts
├── accessibility.ts
├── align.ts
├── animation.ts
├── ... (74 files total)
└── z-index.ts
```

Each `.ts` file contains the raw extracted test blocks from the original test file.
