/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('resize', async () => {
  expect(await run(['resize-none', 'resize', 'resize-x', 'resize-y'])).toMatchInlineSnapshot(`
    ".resize {
      resize: both;
    }

    .resize-none {
      resize: none;
    }

    .resize-x {
      resize: horizontal;
    }

    .resize-y {
      resize: vertical;
    }"
  `)
  expect(
    await run([
      '-resize-none',
      '-resize',
      '-resize-x',
      '-resize-y',
      'resize-none/foo',
      'resize/foo',
      'resize-x/foo',
      'resize-y/foo',
    ]),
  ).toEqual('')
})

