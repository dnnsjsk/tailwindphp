/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('open', async () => {
  expect(await run(['open:flex', 'group-open:flex', 'peer-open:flex', 'not-open:flex']))
    .toMatchInlineSnapshot(`
      ".not-open\\:flex:not(:is([open], :popover-open, :open)), .group-open\\:flex:is(:where(.group):is([open], :popover-open, :open) *), .peer-open\\:flex:is(:where(.peer):is([open], :popover-open, :open) ~ *), .open\\:flex:is([open], :popover-open, :open) {
        display: flex;
      }"
    `)
  expect(await run(['open/foo:flex'])).toEqual('')
})

