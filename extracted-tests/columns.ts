/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('columns', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --container-3xs: 16rem;
          --container-7xl: 80rem;
        }
        @tailwind utilities;
      `,
      [
        'columns-auto',
        'columns-3xs',
        'columns-7xl',
        'columns-4',
        'columns-99',
        'columns-[123]',
        'columns-[var(--value)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --container-3xs: 16rem;
      --container-7xl: 80rem;
    }

    .columns-3xs {
      columns: var(--container-3xs);
    }

    .columns-4 {
      columns: 4;
    }

    .columns-7xl {
      columns: var(--container-7xl);
    }

    .columns-99 {
      columns: 99;
    }

    .columns-\\[123\\] {
      columns: 123;
    }

    .columns-\\[var\\(--value\\)\\] {
      columns: var(--value);
    }

    .columns-auto {
      columns: auto;
    }"
  `)
  expect(
    await run([
      'columns',
      'columns--4',
      '-columns-4',
      '-columns-[123]',
      '-columns-[var(--value)]',
      'columns-unknown',
      'columns-auto/foo',
      'columns-3xs/foo',
      'columns-7xl/foo',
      'columns-4/foo',
      'columns-99/foo',
      'columns-[123]/foo',
      'columns-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --columns-auto: 3;
        }
        @tailwind utilities;
      `,
      ['columns-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --columns-auto: 3;
    }

    .columns-auto {
      columns: var(--columns-auto);
    }"
  `)
})

