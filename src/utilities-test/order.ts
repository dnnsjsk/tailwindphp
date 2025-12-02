/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('order', async () => {
  expect(
    await run([
      'order-4',
      '-order-4',
      'order-[123]',
      '-order-[var(--value)]',
      'order-first',
      'order-last',
      'order-none',
    ]),
  ).toMatchInlineSnapshot(`
    ".-order-4 {
      order: calc(4 * -1);
    }

    .-order-\\[var\\(--value\\)\\] {
      order: calc(var(--value) * -1);
    }

    .order-4 {
      order: 4;
    }

    .order-\\[123\\] {
      order: 123;
    }

    .order-first {
      order: -9999;
    }

    .order-last {
      order: 9999;
    }

    .order-none {
      order: 0;
    }"
  `)
  expect(
    await run([
      'order',
      'order--4',
      '-order-first',
      '-order-last',
      '-order-none',
      'order-unknown',
      'order-123.5',
      'order-4/foo',
      '-order-4/foo',
      'order-[123]/foo',
      '-order-[var(--value)]/foo',
      'order-first/foo',
      'order-last/foo',
      'order-none/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --order-first: 1;
        }
        @tailwind utilities;
      `,
      ['order-first'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --order-first: 1;
    }

    .order-first {
      order: var(--order-first);
    }"
  `)

  expect(
    await compileCss(
      css`
        @theme {
          --order-last: -1;
        }
        @tailwind utilities;
      `,
      ['order-last'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --order-last: -1;
    }

    .order-last {
      order: var(--order-last);
    }"
  `)
})

