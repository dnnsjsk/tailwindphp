/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('portrait', async () => {
  expect(await run(['portrait:flex'])).toMatchInlineSnapshot(`
    "@media (orientation: portrait) {
      .portrait\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['portrait/foo:flex'])).toEqual('')
})

test('landscape', async () => {
  expect(await run(['landscape:flex'])).toMatchInlineSnapshot(`
    "@media (orientation: landscape) {
      .landscape\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['landscape/foo:flex'])).toEqual('')
})

