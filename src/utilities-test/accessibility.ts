/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('sr-only', async () => {
  expect(await run(['sr-only'])).toMatchInlineSnapshot(`
    ".sr-only {
      clip-path: inset(50%);
      white-space: nowrap;
      border-width: 0;
      width: 1px;
      height: 1px;
      margin: -1px;
      padding: 0;
      position: absolute;
      overflow: hidden;
    }"
  `)
  expect(await run(['-sr-only', 'sr-only-[var(--value)]', 'sr-only/foo'])).toEqual('')
})

test('not-sr-only', async () => {
  expect(await run(['not-sr-only'])).toMatchInlineSnapshot(`
    ".not-sr-only {
      clip-path: none;
      white-space: normal;
      width: auto;
      height: auto;
      margin: 0;
      padding: 0;
      position: static;
      overflow: visible;
    }"
  `)
  expect(await run(['-not-sr-only', 'not-sr-only-[var(--value)]', 'not-sr-only/foo'])).toEqual('')
})

