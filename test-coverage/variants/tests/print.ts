/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('print', async () => {
  expect(await run(['print:flex'])).toMatchInlineSnapshot(`
    "@media print {
      .print\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['print/foo:flex'])).toEqual('')
})

