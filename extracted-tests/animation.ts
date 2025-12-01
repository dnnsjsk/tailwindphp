/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('animate', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --animate-spin: spin 1s linear infinite;
        }
        @tailwind utilities;
      `,
      ['animate-spin', 'animate-none', 'animate-[bounce_1s_infinite]', 'animate-not-found'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --animate-spin: spin 1s linear infinite;
    }

    .animate-\\[bounce_1s_infinite\\] {
      animation: 1s infinite bounce;
    }

    .animate-none {
      animation: none;
    }

    .animate-spin {
      animation: var(--animate-spin);
    }"
  `)
  expect(
    await run([
      'animate',
      '-animate-spin',
      '-animate-none',
      '-animate-[bounce_1s_infinite]',
      '-animate-not-found',
      'animate-spin/foo',
      'animate-none/foo',
      'animate-[bounce_1s_infinite]/foo',
      'animate-not-found/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --animate-none: bounce 1s infinite;
        }
        @tailwind utilities;
      `,
      ['animate-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --animate-none: bounce 1s infinite;
    }

    .animate-none {
      animation: var(--animate-none);
    }"
  `)
})

