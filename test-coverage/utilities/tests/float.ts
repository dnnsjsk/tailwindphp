/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('float', async () => {
  expect(await run(['float-start', 'float-end', 'float-right', 'float-left', 'float-none']))
    .toMatchInlineSnapshot(`
      ".float-end {
        float: inline-end;
      }

      .float-left {
        float: left;
      }

      .float-none {
        float: none;
      }

      .float-right {
        float: right;
      }

      .float-start {
        float: inline-start;
      }"
    `)
  expect(
    await run([
      'float',
      '-float-start',
      '-float-end',
      '-float-right',
      '-float-left',
      '-float-none',
      'float-start/foo',
      'float-end/foo',
      'float-right/foo',
      'float-left/foo',
      'float-none/foo',
    ]),
  ).toEqual('')
})

