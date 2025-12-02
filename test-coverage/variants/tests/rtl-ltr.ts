/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('ltr', async () => {
  expect(await run(['ltr:flex'])).toMatchInlineSnapshot(`
    ".ltr\\:flex:where(:dir(ltr), [dir="ltr"], [dir="ltr"] *) {
      display: flex;
    }"
  `)
  expect(await run(['ltr/foo:flex'])).toEqual('')
})

test('rtl', async () => {
  expect(await run(['rtl:flex'])).toMatchInlineSnapshot(`
    ".rtl\\:flex:where(:dir(rtl), [dir="rtl"], [dir="rtl"] *) {
      display: flex;
    }"
  `)
  expect(await run(['rtl/foo:flex'])).toEqual('')
})

