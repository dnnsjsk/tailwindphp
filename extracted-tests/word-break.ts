/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('word-break', async () => {
  expect(await run(['break-normal', 'break-words', 'break-all', 'break-keep']))
    .toMatchInlineSnapshot(`
      ".break-normal {
        overflow-wrap: normal;
        word-break: normal;
      }

      .break-words {
        overflow-wrap: break-word;
      }

      .break-all {
        word-break: break-all;
      }

      .break-keep {
        word-break: keep-all;
      }"
    `)
  expect(
    await run([
      '-break-normal',
      '-break-words',
      '-break-all',
      '-break-keep',
      'break-normal/foo',
      'break-words/foo',
      'break-all/foo',
      'break-keep/foo',
    ]),
  ).toEqual('')
})

