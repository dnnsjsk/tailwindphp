/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('isolation', async () => {
  expect(await run(['isolate', 'isolation-auto'])).toMatchInlineSnapshot(`
    ".isolate {
      isolation: isolate;
    }

    .isolation-auto {
      isolation: auto;
    }"
  `)
  expect(await run(['-isolate', '-isolation-auto', 'isolate/foo', 'isolation-auto/foo'])).toEqual(
    '',
  )
})

