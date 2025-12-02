/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('whitespace', async () => {
  expect(
    await run([
      'whitespace-normal',
      'whitespace-nowrap',
      'whitespace-pre',
      'whitespace-pre-line',
      'whitespace-pre-wrap',
      'whitespace-break-spaces',
    ]),
  ).toMatchInlineSnapshot(`
    ".whitespace-break-spaces {
      white-space: break-spaces;
    }

    .whitespace-normal {
      white-space: normal;
    }

    .whitespace-nowrap {
      white-space: nowrap;
    }

    .whitespace-pre {
      white-space: pre;
    }

    .whitespace-pre-line {
      white-space: pre-line;
    }

    .whitespace-pre-wrap {
      white-space: pre-wrap;
    }"
  `)
  expect(
    await run([
      'whitespace',
      '-whitespace-normal',
      '-whitespace-nowrap',
      '-whitespace-pre',
      '-whitespace-pre-line',
      '-whitespace-pre-wrap',
      '-whitespace-break-spaces',
      'whitespace-normal/foo',
      'whitespace-nowrap/foo',
      'whitespace-pre/foo',
      'whitespace-pre-line/foo',
      'whitespace-pre-wrap/foo',
      'whitespace-break-spaces/foo',
    ]),
  ).toEqual('')
})

