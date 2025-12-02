/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('motion-safe', async () => {
  expect(await run(['motion-safe:flex'])).toMatchInlineSnapshot(`
    "@media (prefers-reduced-motion: no-preference) {
      .motion-safe\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['motion-safe/foo:flex'])).toEqual('')
})

test('motion-reduce', async () => {
  expect(await run(['motion-reduce:flex'])).toMatchInlineSnapshot(`
    "@media (prefers-reduced-motion: reduce) {
      .motion-reduce\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['motion-reduce/foo:flex'])).toEqual('')
})

