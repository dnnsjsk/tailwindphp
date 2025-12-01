/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('flex', async () => {
  expect(
    await run([
      'flex-1',
      'flex-99',
      'flex-1/2',
      'flex-auto',
      'flex-initial',
      'flex-none',
      'flex-[123]',
    ]),
  ).toMatchInlineSnapshot(`
    ".flex-1 {
      flex: 1;
    }

    .flex-1\\/2 {
      flex: 50%;
    }

    .flex-99 {
      flex: 99;
    }

    .flex-\\[123\\] {
      flex: 123;
    }

    .flex-auto {
      flex: auto;
    }

    .flex-initial {
      flex: 0 auto;
    }

    .flex-none {
      flex: none;
    }"
  `)
  expect(
    await run([
      '-flex-1',
      'flex--1',
      '-flex-auto',
      '-flex-initial',
      '-flex-none',
      '-flex-[123]',
      'flex-unknown',
      'flex-1/foo',
      'flex-99/foo',
      'flex--1/2',
      'flex--1/-2',
      'flex-1/-2',
      'flex-1/2/foo',
      'flex-auto/foo',
      'flex-initial/foo',
      'flex-none/foo',
      'flex-[123]/foo',
    ]),
  ).toEqual('')
})

test('flex-shrink', async () => {
  expect(await run(['shrink', 'shrink-0', 'shrink-[123]'])).toMatchInlineSnapshot(`
    ".shrink {
      flex-shrink: 1;
    }

    .shrink-0 {
      flex-shrink: 0;
    }

    .shrink-\\[123\\] {
      flex-shrink: 123;
    }"
  `)
  expect(
    await run([
      '-shrink',
      'shrink--1',
      'shrink-1.5',
      '-shrink-0',
      '-shrink-[123]',
      'shrink-unknown',
      'shrink/foo',
      'shrink-0/foo',
      'shrink-[123]/foo',
    ]),
  ).toEqual('')
})

test('flex-grow', async () => {
  expect(await run(['grow', 'grow-0', 'grow-[123]'])).toMatchInlineSnapshot(`
    ".grow {
      flex-grow: 1;
    }

    .grow-0 {
      flex-grow: 0;
    }

    .grow-\\[123\\] {
      flex-grow: 123;
    }"
  `)
  expect(
    await run([
      '-grow',
      'grow--1',
      'grow-1.5',
      '-grow-0',
      '-grow-[123]',
      'grow-unknown',
      'grow/foo',
      'grow-0/foo',
      'grow-[123]/foo',
    ]),
  ).toEqual('')
})

test('flex-basis', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --container-xl: 36rem;
        }
        @tailwind utilities;
      `,
      ['basis-auto', 'basis-full', 'basis-xl', 'basis-11/12', 'basis-[123px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --container-xl: 36rem;
    }

    .basis-11\\/12 {
      flex-basis: 91.6667%;
    }

    .basis-\\[123px\\] {
      flex-basis: 123px;
    }

    .basis-auto {
      flex-basis: auto;
    }

    .basis-full {
      flex-basis: 100%;
    }

    .basis-xl {
      flex-basis: var(--container-xl);
    }"
  `)
  expect(
    await run([
      'basis',
      'basis--1',
      'basis--1/2',
      'basis--1/-2',
      'basis-1/-2',
      '-basis-full',
      '-basis-[123px]',
      'basis-auto/foo',
      'basis-full/foo',
      'basis-xl/foo',
      'basis-11/12/foo',
      'basis-[123px]/foo',
    ]),
  ).toEqual('')
})

test('flex-direction', async () => {
  expect(await run(['flex-row', 'flex-row-reverse', 'flex-col', 'flex-col-reverse']))
    .toMatchInlineSnapshot(`
      ".flex-col {
        flex-direction: column;
      }

      .flex-col-reverse {
        flex-direction: column-reverse;
      }

      .flex-row {
        flex-direction: row;
      }

      .flex-row-reverse {
        flex-direction: row-reverse;
      }"
    `)
  expect(
    await run([
      '-flex-row',
      '-flex-row-reverse',
      '-flex-col',
      '-flex-col-reverse',
      'flex-row/foo',
      'flex-row-reverse/foo',
      'flex-col/foo',
      'flex-col-reverse/foo',
    ]),
  ).toEqual('')
})

test('flex-wrap', async () => {
  expect(await run(['flex-wrap', 'flex-wrap-reverse', 'flex-nowrap'])).toMatchInlineSnapshot(`
    ".flex-nowrap {
      flex-wrap: nowrap;
    }

    .flex-wrap {
      flex-wrap: wrap;
    }

    .flex-wrap-reverse {
      flex-wrap: wrap-reverse;
    }"
  `)
  expect(
    await run([
      '-flex-wrap',
      '-flex-wrap-reverse',
      '-flex-nowrap',
      'flex-wrap/foo',
      'flex-wrap-reverse/foo',
      'flex-nowrap/foo',
    ]),
  ).toEqual('')
})

