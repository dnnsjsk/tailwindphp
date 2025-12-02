/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('aria', async () => {
  expect(
    await run([
      'aria-checked:flex',
      'aria-[invalid=spelling]:flex',
      'aria-[valuenow=1]:flex',
      'aria-[valuenow_=_"1"]:flex',

      'group-aria-[modal]:flex',
      'group-aria-checked:flex',
      'group-aria-[valuenow=1]:flex',
      'group-aria-[modal]/parent-name:flex',
      'group-aria-checked/parent-name:flex',
      'group-aria-[valuenow=1]/parent-name:flex',

      'peer-aria-[modal]:flex',
      'peer-aria-checked:flex',
      'peer-aria-[valuenow=1]:flex',
      'peer-aria-[modal]/sibling-name:flex',
      'peer-aria-checked/sibling-name:flex',
      'peer-aria-[valuenow=1]/sibling-name:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".group-aria-checked\\:flex:is(:where(.group)[aria-checked="true"] *), .group-aria-checked\\/parent-name\\:flex:is(:where(.group\\/parent-name)[aria-checked="true"] *), .group-aria-\\[modal\\]\\:flex:is(:where(.group)[aria-modal] *), .group-aria-\\[modal\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[aria-modal] *), .group-aria-\\[valuenow\\=1\\]\\:flex:is(:where(.group)[aria-valuenow="1"] *), .group-aria-\\[valuenow\\=1\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[aria-valuenow="1"] *), .peer-aria-checked\\:flex:is(:where(.peer)[aria-checked="true"] ~ *), .peer-aria-checked\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[aria-checked="true"] ~ *), .peer-aria-\\[modal\\]\\:flex:is(:where(.peer)[aria-modal] ~ *), .peer-aria-\\[modal\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[aria-modal] ~ *), .peer-aria-\\[valuenow\\=1\\]\\:flex:is(:where(.peer)[aria-valuenow="1"] ~ *), .peer-aria-\\[valuenow\\=1\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[aria-valuenow="1"] ~ *), .aria-checked\\:flex[aria-checked="true"], .aria-\\[invalid\\=spelling\\]\\:flex[aria-invalid="spelling"], .aria-\\[valuenow_\\=_\\"1\\"\\]\\:flex[aria-valuenow="1"], .aria-\\[valuenow\\=1\\]\\:flex[aria-valuenow="1"] {
      display: flex;
    }"
  `)
  expect(await run(['aria-checked/foo:flex', 'aria-[invalid=spelling]/foo:flex'])).toEqual('')
})

