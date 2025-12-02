/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('starting', async () => {
  expect(await run(['starting:opacity-0'])).toMatchInlineSnapshot(`
    "@starting-style {
      .starting\\:opacity-0 {
        opacity: 0;
      }
    }"
  `)
  expect(await run(['starting/foo:flex'])).toEqual('')
})

