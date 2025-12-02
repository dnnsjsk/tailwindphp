/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('forced-color-adjust', async () => {
  expect(await run(['forced-color-adjust-none', 'forced-color-adjust-auto']))
    .toMatchInlineSnapshot(`
      ".forced-color-adjust-auto {
        forced-color-adjust: auto;
      }

      .forced-color-adjust-none {
        forced-color-adjust: none;
      }"
    `)
  expect(
    await run([
      'forced',
      'forced-color',
      'forced-color-adjust',
      '-forced-color-adjust-none',
      '-forced-color-adjust-auto',
      'forced-color-adjust-none/foo',
      'forced-color-adjust-auto/foo',
    ]),
  ).toEqual('')
})

