/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('position', async () => {
  expect(await run(['static', 'fixed', 'absolute', 'relative', 'sticky'])).toMatchInlineSnapshot(`
    ".absolute {
      position: absolute;
    }

    .fixed {
      position: fixed;
    }

    .relative {
      position: relative;
    }

    .static {
      position: static;
    }

    .sticky {
      position: sticky;
    }"
  `)
  expect(
    await run([
      '-static',
      '-fixed',
      '-absolute',
      '-relative',
      '-sticky',
      'static/foo',
      'fixed/foo',
      'absolute/foo',
      'relative/foo',
      'sticky/foo',
    ]),
  ).toEqual('')
})

