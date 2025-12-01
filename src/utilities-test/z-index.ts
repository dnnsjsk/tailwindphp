/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('z-index', async () => {
  expect(await run(['z-auto', 'z-10', '-z-10', 'z-[123]', '-z-[var(--value)]']))
    .toMatchInlineSnapshot(`
      ".-z-10 {
        z-index: calc(10 * -1);
      }

      .-z-\\[var\\(--value\\)\\] {
        z-index: calc(var(--value) * -1);
      }

      .z-10 {
        z-index: 10;
      }

      .z-\\[123\\] {
        z-index: 123;
      }

      .z-auto {
        z-index: auto;
      }"
    `)
  expect(
    await run([
      'z',
      'z--1',
      '-z-auto',
      'z-unknown',
      'z-123.5',
      'z-auto/foo',
      'z-10/foo',
      '-z-10/foo',
      'z-[123]/foo',
      '-z-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --z-index-auto: 42;
        }
        @tailwind utilities;
      `,
      ['z-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --z-index-auto: 42;
    }

    .z-auto {
      z-index: var(--z-index-auto);
    }"
  `)
})

