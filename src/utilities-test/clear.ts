/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('clear', async () => {
  expect(
    await run([
      'clear-start',
      'clear-end',
      'clear-right',
      'clear-left',
      'clear-both',
      'clear-none',
    ]),
  ).toMatchInlineSnapshot(`
    ".clear-both {
      clear: both;
    }

    .clear-end {
      clear: inline-end;
    }

    .clear-left {
      clear: left;
    }

    .clear-none {
      clear: none;
    }

    .clear-right {
      clear: right;
    }

    .clear-start {
      clear: inline-start;
    }"
  `)
  expect(
    await run([
      'clear',
      '-clear-start',
      '-clear-end',
      '-clear-right',
      '-clear-left',
      '-clear-both',
      '-clear-none',
      'clear-start/foo',
      'clear-end/foo',
      'clear-right/foo',
      'clear-left/foo',
      'clear-both/foo',
      'clear-none/foo',
    ]),
  ).toEqual('')
})

