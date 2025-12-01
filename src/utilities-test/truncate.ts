/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('truncate', async () => {
  expect(await run(['truncate'])).toMatchInlineSnapshot(`
    ".truncate {
      text-overflow: ellipsis;
      white-space: nowrap;
      overflow: hidden;
    }"
  `)
  expect(await run(['-truncate', 'truncate/foo'])).toEqual('')
})

