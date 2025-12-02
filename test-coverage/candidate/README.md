# Candidate Parser Test Coverage

Tests extracted from `tailwindcss/packages/tailwindcss/src/candidate.test.ts`

## Coverage Stats

| Metric | Value |
|--------|-------|
| Source File Lines | 2139 |
| Original Tests | 72 |
| Extracted Cases | 94 |

## Test Categories

| Category | Cases |
|----------|-------|
| arbitrary | 33 |
| basic | 1 |
| important | 1 |
| invalid | 1 |
| modifiers | 26 |
| negative | 1 |
| other | 9 |
| variants | 22 |

## Test Pattern

These tests verify the candidate parser output. Each test:
1. Parses a candidate string (e.g., `hover:flex`, `md:text-red-500`)
2. Expects a specific parsed structure with kind, root, variants, etc.

The PHP tests should verify that `parseCandidate()` returns equivalent structures.