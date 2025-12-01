/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('opacity', async () => {
  expect(
    await run([
      'opacity-15',
      'opacity-2.5',
      'opacity-3.25',
      'opacity-4.75',
      'opacity-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".opacity-2\\.5 {
      opacity: .025;
    }

    .opacity-3\\.25 {
      opacity: .0325;
    }

    .opacity-4\\.75 {
      opacity: .0475;
    }

    .opacity-15 {
      opacity: .15;
    }

    .opacity-\\[var\\(--value\\)\\] {
      opacity: var(--value);
    }"
  `)
  expect(
    await run([
      'opacity',
      'opacity--15',
      'opacity-1.125',
      '-opacity-15',
      '-opacity-[var(--value)]',
      'opacity-unknown',
      'opacity-15/foo',
      'opacity-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

