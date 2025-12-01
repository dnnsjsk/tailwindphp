/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('box-decoration', async () => {
  expect(await run(['box-decoration-slice', 'box-decoration-clone'])).toMatchInlineSnapshot(`
    ".box-decoration-clone {
      -webkit-box-decoration-break: clone;
      box-decoration-break: clone;
    }

    .box-decoration-slice {
      -webkit-box-decoration-break: slice;
      box-decoration-break: slice;
    }"
  `)
  expect(
    await run([
      'box',
      'box-decoration',
      '-box-decoration-slice',
      '-box-decoration-clone',
      'box-decoration-slice/foo',
      'box-decoration-clone/foo',
    ]),
  ).toEqual('')
})

