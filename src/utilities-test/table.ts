/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('table-layout', async () => {
  expect(await run(['table-auto', 'table-fixed'])).toMatchInlineSnapshot(`
    ".table-auto {
      table-layout: auto;
    }

    .table-fixed {
      table-layout: fixed;
    }"
  `)
  expect(await run(['-table-auto', '-table-fixed', 'table-auto/foo', 'table-fixed/foo'])).toEqual(
    '',
  )
})

test('caption-side', async () => {
  expect(await run(['caption-top', 'caption-bottom'])).toMatchInlineSnapshot(`
    ".caption-bottom {
      caption-side: bottom;
    }

    .caption-top {
      caption-side: top;
    }"
  `)
  expect(
    await run(['-caption-top', '-caption-bottom', 'caption-top/foo', 'caption-bottom/foo']),
  ).toEqual('')
})

