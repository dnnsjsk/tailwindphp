# Variants Test Coverage

Tests extracted from `tailwindcss/packages/tailwindcss/src/variants.test.ts`

## Coverage Stats

| Metric | Value |
|--------|-------|
| Source File Lines | 2,632 |
| Original Tests | 91 |
| Test Files Generated | 17 |

## Test Categories

| Category | Tests | Lines |
|----------|-------|-------|
| pseudo-class | 36 | 607 |
| other | 30 | 1,025 |
| pseudo-element | 7 | 102 |
| breakpoints | 3 | 175 |
| rtl-ltr | 2 | 16 |
| reduced-motion | 2 | 20 |
| portrait-landscape | 2 | 20 |
| aria | 1 | 29 |
| data | 1 | 43 |
| dark-mode | 1 | 10 |
| inert | 1 | 8 |
| not | 1 | 350 |
| open | 1 | 9 |
| print | 1 | 10 |
| starting | 1 | 10 |
| supports | 1 | 73 |

## How Tests Are Generated

The tests are extracted by parsing the TypeScript test file and finding:
1. `test('name', async () => { ... })` blocks
2. `await run([...classes])` calls within each test
3. `.toMatchInlineSnapshot(\`...\`)` assertions for expected CSS output
4. `.toEqual('')` assertions for expecting empty output

## Test File Structure

```
tests/
├── pseudo-class.ts
├── pseudo-element.ts
├── breakpoints.ts
├── dark-mode.ts
├── ... (17 files total)
└── other.ts
```

Each `.ts` file contains the raw extracted test blocks from the original test file.
