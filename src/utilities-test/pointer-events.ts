/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('pointer-events', async () => {
  expect(await run(['pointer-events-none', 'pointer-events-auto'])).toMatchInlineSnapshot(`
    ".pointer-events-auto {
      pointer-events: auto;
    }

    .pointer-events-none {
      pointer-events: none;
    }"
  `)
  expect(
    await run([
      '-pointer-events-none',
      '-pointer-events-auto',
      'pointer-events-[var(--value)]',
      'pointer-events-none/foo',
    ]),
  ).toEqual('')
})

