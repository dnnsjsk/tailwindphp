/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('select', async () => {
  expect(await run(['select-none', 'select-text', 'select-all', 'select-auto']))
    .toMatchInlineSnapshot(`
      ".select-all {
        -webkit-user-select: all;
        user-select: all;
      }

      .select-auto {
        -webkit-user-select: auto;
        user-select: auto;
      }

      .select-none {
        -webkit-user-select: none;
        user-select: none;
      }

      .select-text {
        -webkit-user-select: text;
        user-select: text;
      }"
    `)
  expect(
    await run([
      '-select-none',
      '-select-text',
      '-select-all',
      '-select-auto',
      'select-none/foo',
      'select-text/foo',
      'select-all/foo',
      'select-auto/foo',
    ]),
  ).toEqual('')
})

