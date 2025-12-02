/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('col', async () => {
  expect(
    await run([
      'col-11',
      '-col-12',
      'col-auto',
      'col-span-4',
      'col-span-17',
      'col-span-full',
      'col-[span_123/span_123]',
      'col-span-[var(--my-variable)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".-col-12 {
      grid-column: calc(12 * -1);
    }

    .col-11 {
      grid-column: 11;
    }

    .col-\\[span_123\\/span_123\\] {
      grid-column: span 123 / span 123;
    }

    .col-auto {
      grid-column: auto;
    }

    .col-span-4 {
      grid-column: span 4 / span 4;
    }

    .col-span-17 {
      grid-column: span 17 / span 17;
    }

    .col-span-\\[var\\(--my-variable\\)\\] {
      grid-column: span var(--my-variable) / span var(--my-variable);
    }

    .col-span-full {
      grid-column: 1 / -1;
    }"
  `)
  expect(
    await run([
      'col',
      'col-span',
      'col-span--1',
      '-col-span-4',
      'col-span-unknown',
      'col-auto/foo',
      'col-span-4/foo',
      'col-span-17/foo',
      'col-span-full/foo',
      'col-[span_123/span_123]/foo',
      'col-span-[var(--my-variable)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-column-auto: 5;
        }
        @tailwind utilities;
      `,
      ['col-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-column-auto: 5;
    }

    .col-auto {
      grid-column: var(--grid-column-auto);
    }"
  `)
})

test('col-start', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --grid-column-start-custom: 1 column-start;
        }
        @tailwind utilities;
      `,
      [
        'col-start-auto',
        'col-start-4',
        'col-start-99',
        'col-start-[123]',
        '-col-start-4',
        'col-start-custom',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-column-start-custom: 1 column-start;
    }

    .-col-start-4 {
      grid-column-start: calc(4 * -1);
    }

    .col-start-4 {
      grid-column-start: 4;
    }

    .col-start-99 {
      grid-column-start: 99;
    }

    .col-start-\\[123\\] {
      grid-column-start: 123;
    }

    .col-start-auto {
      grid-column-start: auto;
    }

    .col-start-custom {
      grid-column-start: var(--grid-column-start-custom);
    }"
  `)
  expect(
    await run([
      'col-start',
      'col-start--1',
      'col-start-unknown',
      'col-start-auto/foo',
      'col-start-4/foo',
      'col-start-99/foo',
      'col-start-[123]/foo',
      '-col-start-4/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-column-start-auto: 7;
        }
        @tailwind utilities;
      `,
      ['col-start-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-column-start-auto: 7;
    }

    .col-start-auto {
      grid-column-start: var(--grid-column-start-auto);
    }"
  `)
})

test('col-end', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --grid-column-end-custom: 1 column-end;
        }
        @tailwind utilities;
      `,
      ['col-end-auto', 'col-end-4', 'col-end-99', 'col-end-[123]', '-col-end-4', 'col-end-custom'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-column-end-custom: 1 column-end;
    }

    .-col-end-4 {
      grid-column-end: calc(4 * -1);
    }

    .col-end-4 {
      grid-column-end: 4;
    }

    .col-end-99 {
      grid-column-end: 99;
    }

    .col-end-\\[123\\] {
      grid-column-end: 123;
    }

    .col-end-auto {
      grid-column-end: auto;
    }

    .col-end-custom {
      grid-column-end: var(--grid-column-end-custom);
    }"
  `)
  expect(
    await run([
      'col-end',
      'col-end--1',
      'col-end-unknown',
      'col-end-auto/foo',
      'col-end-4/foo',
      'col-end-99/foo',
      'col-end-[123]/foo',
      '-col-end-4/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-column-end-auto: 3;
        }
        @tailwind utilities;
      `,
      ['col-end-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-column-end-auto: 3;
    }

    .col-end-auto {
      grid-column-end: var(--grid-column-end-auto);
    }"
  `)
})

test('row', async () => {
  expect(
    await run([
      'row-11',
      '-row-12',
      'row-auto',
      'row-span-4',
      'row-span-17',
      'row-span-full',
      'row-[span_123/span_123]',
      'row-span-[var(--my-variable)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".-row-12 {
      grid-row: calc(12 * -1);
    }

    .row-11 {
      grid-row: 11;
    }

    .row-\\[span_123\\/span_123\\] {
      grid-row: span 123 / span 123;
    }

    .row-auto {
      grid-row: auto;
    }

    .row-span-4 {
      grid-row: span 4 / span 4;
    }

    .row-span-17 {
      grid-row: span 17 / span 17;
    }

    .row-span-\\[var\\(--my-variable\\)\\] {
      grid-row: span var(--my-variable) / span var(--my-variable);
    }

    .row-span-full {
      grid-row: 1 / -1;
    }"
  `)
  expect(
    await run([
      'row',
      'row-span',
      'row-span--1',
      '-row-span-4',
      'row-span-unknown',
      'row-auto/foo',
      'row-span-4/foo',
      'row-span-17/foo',
      'row-span-full/foo',
      'row-[span_123/span_123]/foo',
      'row-span-[var(--my-variable)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-row-auto: 9;
        }
        @tailwind utilities;
      `,
      ['row-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-row-auto: 9;
    }

    .row-auto {
      grid-row: var(--grid-row-auto);
    }"
  `)
})

test('row-start', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --grid-row-start-custom: 1 row-start;
        }
        @tailwind utilities;
      `,
      [
        'row-start-auto',
        'row-start-4',
        'row-start-99',
        'row-start-[123]',
        '-row-start-4',
        'row-start-custom',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-row-start-custom: 1 row-start;
    }

    .-row-start-4 {
      grid-row-start: calc(4 * -1);
    }

    .row-start-4 {
      grid-row-start: 4;
    }

    .row-start-99 {
      grid-row-start: 99;
    }

    .row-start-\\[123\\] {
      grid-row-start: 123;
    }

    .row-start-auto {
      grid-row-start: auto;
    }

    .row-start-custom {
      grid-row-start: var(--grid-row-start-custom);
    }"
  `)
  expect(
    await run([
      'row-start',
      'row-start--1',
      'row-start-unknown',
      'row-start-auto/foo',
      'row-start-4/foo',
      'row-start-99/foo',
      'row-start-[123]/foo',
      '-row-start-4/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-row-start-auto: 11;
        }
        @tailwind utilities;
      `,
      ['row-start-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-row-start-auto: 11;
    }

    .row-start-auto {
      grid-row-start: var(--grid-row-start-auto);
    }"
  `)
})

test('row-end', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --grid-row-end-custom: 1 row-end;
        }
        @tailwind utilities;
      `,
      ['row-end-auto', 'row-end-4', 'row-end-99', 'row-end-[123]', '-row-end-4', 'row-end-custom'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-row-end-custom: 1 row-end;
    }

    .-row-end-4 {
      grid-row-end: calc(4 * -1);
    }

    .row-end-4 {
      grid-row-end: 4;
    }

    .row-end-99 {
      grid-row-end: 99;
    }

    .row-end-\\[123\\] {
      grid-row-end: 123;
    }

    .row-end-auto {
      grid-row-end: auto;
    }

    .row-end-custom {
      grid-row-end: var(--grid-row-end-custom);
    }"
  `)
  expect(
    await run([
      'row-end',
      'row-end--1',
      'row-end-unknown',
      'row-end-auto/foo',
      'row-end-4/foo',
      'row-end-99/foo',
      'row-end-[123]/foo',
      '-row-end-4/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-row-end-auto: 13;
        }
        @tailwind utilities;
      `,
      ['row-end-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-row-end-auto: 13;
    }

    .row-end-auto {
      grid-row-end: var(--grid-row-end-auto);
    }"
  `)
})

test('color-scheme', async () => {
  expect(
    await run([
      'scheme-normal',
      'scheme-dark',
      'scheme-light',
      'scheme-light-dark',
      'scheme-only-dark',
      'scheme-only-light',
    ]),
  ).toMatchInlineSnapshot(`
    ".scheme-dark {
      color-scheme: dark;
    }

    .scheme-light {
      color-scheme: light;
    }

    .scheme-light-dark {
      color-scheme: light dark;
    }

    .scheme-normal {
      color-scheme: normal;
    }

    .scheme-only-dark {
      color-scheme: dark only;
    }

    .scheme-only-light {
      color-scheme: light only;
    }"
  `)
  expect(
    await run([
      'scheme',
      '-scheme-dark',
      '-scheme-light',
      '-scheme-light-dark',
      '-scheme-dark-only',
      '-scheme-light-only',
    ]),
  ).toEqual('')
})

test('auto-cols', async () => {
  expect(
    await run([
      'auto-cols-auto',
      'auto-cols-min',
      'auto-cols-max',
      'auto-cols-fr',
      'auto-cols-[2fr]',
    ]),
  ).toMatchInlineSnapshot(`
    ".auto-cols-\\[2fr\\] {
      grid-auto-columns: 2fr;
    }

    .auto-cols-auto {
      grid-auto-columns: auto;
    }

    .auto-cols-fr {
      grid-auto-columns: minmax(0, 1fr);
    }

    .auto-cols-max {
      grid-auto-columns: max-content;
    }

    .auto-cols-min {
      grid-auto-columns: min-content;
    }"
  `)
  expect(
    await run([
      'auto-cols',
      '-auto-cols-auto',
      '-auto-cols-[2fr]',
      'auto-cols-auto/foo',
      'auto-cols-min/foo',
      'auto-cols-max/foo',
      'auto-cols-fr/foo',
      'auto-cols-[2fr]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-auto-columns-auto: 2fr;
        }
        @tailwind utilities;
      `,
      ['auto-cols-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-auto-columns-auto: 2fr;
    }

    .auto-cols-auto {
      grid-auto-columns: var(--grid-auto-columns-auto);
    }"
  `)
})

test('grid-flow', async () => {
  expect(
    await run([
      'grid-flow-row',
      'grid-flow-col',
      'grid-flow-dense',
      'grid-flow-row-dense',
      'grid-flow-col-dense',
    ]),
  ).toMatchInlineSnapshot(`
    ".grid-flow-col {
      grid-auto-flow: column;
    }

    .grid-flow-col-dense {
      grid-auto-flow: column dense;
    }

    .grid-flow-dense {
      grid-auto-flow: dense;
    }

    .grid-flow-row {
      grid-auto-flow: row;
    }

    .grid-flow-row-dense {
      grid-auto-flow: row dense;
    }"
  `)
  expect(
    await run([
      'grid-flow',
      '-grid-flow-row',
      '-grid-flow-col',
      '-grid-flow-dense',
      '-grid-flow-row-dense',
      '-grid-flow-col-dense',
      'grid-flow-row/foo',
      'grid-flow-col/foo',
      'grid-flow-dense/foo',
      'grid-flow-row-dense/foo',
      'grid-flow-col-dense/foo',
    ]),
  ).toEqual('')
})

test('auto-rows', async () => {
  expect(
    await run([
      'auto-rows-auto',
      'auto-rows-min',
      'auto-rows-max',
      'auto-rows-fr',
      'auto-rows-[2fr]',
    ]),
  ).toMatchInlineSnapshot(`
    ".auto-rows-\\[2fr\\] {
      grid-auto-rows: 2fr;
    }

    .auto-rows-auto {
      grid-auto-rows: auto;
    }

    .auto-rows-fr {
      grid-auto-rows: minmax(0, 1fr);
    }

    .auto-rows-max {
      grid-auto-rows: max-content;
    }

    .auto-rows-min {
      grid-auto-rows: min-content;
    }"
  `)
  expect(
    await run([
      'auto-rows',
      '-auto-rows-auto',
      '-auto-rows-[2fr]',
      'auto-rows-auto/foo',
      'auto-rows-min/foo',
      'auto-rows-max/foo',
      'auto-rows-fr/foo',
      'auto-rows-[2fr]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-auto-rows-auto: 2fr;
        }
        @tailwind utilities;
      `,
      ['auto-rows-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-auto-rows-auto: 2fr;
    }

    .auto-rows-auto {
      grid-auto-rows: var(--grid-auto-rows-auto);
    }"
  `)
})

test('grid-cols', async () => {
  expect(
    await run([
      'grid-cols-none',
      'grid-cols-subgrid',
      'grid-cols-12',
      'grid-cols-99',
      'grid-cols-[123]',
    ]),
  ).toMatchInlineSnapshot(`
    ".grid-cols-12 {
      grid-template-columns: repeat(12, minmax(0, 1fr));
    }

    .grid-cols-99 {
      grid-template-columns: repeat(99, minmax(0, 1fr));
    }

    .grid-cols-\\[123\\] {
      grid-template-columns: 123px;
    }

    .grid-cols-none {
      grid-template-columns: none;
    }

    .grid-cols-subgrid {
      grid-template-columns: subgrid;
    }"
  `)
  expect(
    await run([
      'grid-cols',
      'grid-cols-0',
      '-grid-cols-none',
      '-grid-cols-subgrid',
      'grid-cols--12',
      '-grid-cols-12',
      '-grid-cols-[123]',
      'grid-cols-unknown',
      'grid-cols-none/foo',
      'grid-cols-subgrid/foo',
      'grid-cols-12/foo',
      'grid-cols-99/foo',
      'grid-cols-[123]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-template-columns-none: 200px 1fr;
        }
        @tailwind utilities;
      `,
      ['grid-cols-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-template-columns-none: 200px 1fr;
    }

    .grid-cols-none {
      grid-template-columns: var(--grid-template-columns-none);
    }"
  `)
})

test('grid-rows', async () => {
  expect(
    await run([
      'grid-rows-none',
      'grid-rows-subgrid',
      'grid-rows-12',
      'grid-rows-99',
      'grid-rows-[123]',
    ]),
  ).toMatchInlineSnapshot(`
    ".grid-rows-12 {
      grid-template-rows: repeat(12, minmax(0, 1fr));
    }

    .grid-rows-99 {
      grid-template-rows: repeat(99, minmax(0, 1fr));
    }

    .grid-rows-\\[123\\] {
      grid-template-rows: 123px;
    }

    .grid-rows-none {
      grid-template-rows: none;
    }

    .grid-rows-subgrid {
      grid-template-rows: subgrid;
    }"
  `)
  expect(
    await run([
      'grid-rows',
      'grid-rows-0',
      '-grid-rows-none',
      '-grid-rows-subgrid',
      'grid-rows--12',
      '-grid-rows-12',
      '-grid-rows-[123]',
      'grid-rows-unknown',
      'grid-rows-none/foo',
      'grid-rows-subgrid/foo',
      'grid-rows-12/foo',
      'grid-rows-99/foo',
      'grid-rows-[123]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --grid-template-rows-none: 200px 1fr;
        }
        @tailwind utilities;
      `,
      ['grid-rows-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --grid-template-rows-none: 200px 1fr;
    }

    .grid-rows-none {
      grid-template-rows: var(--grid-template-rows-none);
    }"
  `)
})

