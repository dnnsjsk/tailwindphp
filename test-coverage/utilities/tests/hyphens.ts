/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('hyphens', async () => {
  expect(await run(['hyphens-none', 'hyphens-manual', 'hyphens-auto'])).toMatchInlineSnapshot(`
    ".hyphens-auto {
      -webkit-hyphens: auto;
      hyphens: auto;
    }

    .hyphens-manual {
      -webkit-hyphens: manual;
      hyphens: manual;
    }

    .hyphens-none {
      -webkit-hyphens: none;
      hyphens: none;
    }"
  `)
  expect(
    await run([
      'hyphens',
      '-hyphens-none',
      '-hyphens-manual',
      '-hyphens-auto',
      'hyphens-none/foo',
      'hyphens-manual/foo',
      'hyphens-auto/foo',
    ]),
  ).toEqual('')
})

