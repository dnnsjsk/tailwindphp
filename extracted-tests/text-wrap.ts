/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('text-wrap', async () => {
  expect(await run(['text-wrap', 'text-nowrap', 'text-balance', 'text-pretty']))
    .toMatchInlineSnapshot(`
      ".text-balance {
        text-wrap: balance;
      }

      .text-nowrap {
        text-wrap: nowrap;
      }

      .text-pretty {
        text-wrap: pretty;
      }

      .text-wrap {
        text-wrap: wrap;
      }"
    `)
  expect(
    await run([
      '-text-wrap',
      '-text-nowrap',
      '-text-balance',
      '-text-pretty',
      'text-wrap/foo',
      'text-nowrap/foo',
      'text-balance/foo',
      'text-pretty/foo',
    ]),
  ).toEqual('')
})

