/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('appearance', async () => {
  expect(await run(['appearance-none', 'appearance-auto'])).toMatchInlineSnapshot(`
    ".appearance-auto {
      appearance: auto;
    }

    .appearance-none {
      appearance: none;
    }"
  `)
  expect(
    await run([
      'appearance',
      '-appearance-none',
      '-appearance-auto',
      'appearance-none/foo',
      'appearance-auto/foo',
    ]),
  ).toEqual('')
})

