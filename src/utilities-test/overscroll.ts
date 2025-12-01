/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('overscroll', async () => {
  expect(await run(['overscroll-auto', 'overscroll-contain', 'overscroll-none']))
    .toMatchInlineSnapshot(`
      ".overscroll-auto {
        overscroll-behavior: auto;
      }

      .overscroll-contain {
        overscroll-behavior: contain;
      }

      .overscroll-none {
        overscroll-behavior: none;
      }"
    `)
  expect(
    await run([
      'overscroll',
      '-overscroll-auto',
      '-overscroll-contain',
      '-overscroll-none',
      'overscroll-auto/foo',
      'overscroll-contain/foo',
      'overscroll-none/foo',
    ]),
  ).toEqual('')
})

test('overscroll-x', async () => {
  expect(await run(['overscroll-x-auto', 'overscroll-x-contain', 'overscroll-x-none']))
    .toMatchInlineSnapshot(`
      ".overscroll-x-auto {
        overscroll-behavior-x: auto;
      }

      .overscroll-x-contain {
        overscroll-behavior-x: contain;
      }

      .overscroll-x-none {
        overscroll-behavior-x: none;
      }"
    `)
  expect(
    await run([
      'overscroll-x',
      '-overscroll-x-auto',
      '-overscroll-x-contain',
      '-overscroll-x-none',
      'overscroll-x-auto/foo',
      'overscroll-x-contain/foo',
      'overscroll-x-none/foo',
    ]),
  ).toEqual('')
})

test('overscroll-y', async () => {
  expect(await run(['overscroll-y-auto', 'overscroll-y-contain', 'overscroll-y-none']))
    .toMatchInlineSnapshot(`
      ".overscroll-y-auto {
        overscroll-behavior-y: auto;
      }

      .overscroll-y-contain {
        overscroll-behavior-y: contain;
      }

      .overscroll-y-none {
        overscroll-behavior-y: none;
      }"
    `)
  expect(
    await run([
      'overscroll-y',
      '-overscroll-y-auto',
      '-overscroll-y-contain',
      '-overscroll-y-none',
      'overscroll-y-auto/foo',
      'overscroll-y-contain/foo',
      'overscroll-y-none/foo',
    ]),
  ).toEqual('')
})

