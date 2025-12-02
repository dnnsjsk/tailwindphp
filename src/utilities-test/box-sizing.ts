/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('box-sizing', async () => {
  expect(await run(['box-border', 'box-content'])).toMatchInlineSnapshot(`
    ".box-border {
      box-sizing: border-box;
    }

    .box-content {
      box-sizing: content-box;
    }"
  `)
  expect(
    await run(['box', '-box-border', '-box-content', 'box-border/foo', 'box-content/foo']),
  ).toEqual('')
})

