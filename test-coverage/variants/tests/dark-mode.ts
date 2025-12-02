/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('dark', async () => {
  expect(await run(['dark:flex'])).toMatchInlineSnapshot(`
    "@media (prefers-color-scheme: dark) {
      .dark\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['dark/foo:flex'])).toEqual('')
})

