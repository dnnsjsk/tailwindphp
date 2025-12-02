/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('inert', async () => {
  expect(await run(['inert:flex', 'group-inert:flex', 'peer-inert:flex'])).toMatchInlineSnapshot(`
    ".group-inert\\:flex:is(:where(.group):is([inert], [inert] *) *), .peer-inert\\:flex:is(:where(.peer):is([inert], [inert] *) ~ *), .inert\\:flex:is([inert], [inert] *) {
      display: flex;
    }"
  `)
  expect(await run(['inert/foo:flex'])).toEqual('')
})

