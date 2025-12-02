/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('align-content', async () => {
  expect(
    await run([
      'content-normal',
      'content-center',
      'content-center-safe',
      'content-start',
      'content-end',
      'content-end-safe',
      'content-between',
      'content-around',
      'content-evenly',
      'content-baseline',
      'content-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".content-around {
      align-content: space-around;
    }

    .content-baseline {
      align-content: baseline;
    }

    .content-between {
      align-content: space-between;
    }

    .content-center {
      align-content: center;
    }

    .content-center-safe {
      align-content: safe center;
    }

    .content-end {
      align-content: flex-end;
    }

    .content-end-safe {
      align-content: safe flex-end;
    }

    .content-evenly {
      align-content: space-evenly;
    }

    .content-normal {
      align-content: normal;
    }

    .content-start {
      align-content: flex-start;
    }

    .content-stretch {
      align-content: stretch;
    }"
  `)
  expect(
    await run([
      'content',
      '-content-normal',
      '-content-center',
      '-content-start',
      '-content-end',
      '-content-between',
      '-content-around',
      '-content-evenly',
      '-content-baseline',
      '-content-stretch',
      'content-normal/foo',
      'content-center/foo',
      'content-start/foo',
      'content-end/foo',
      'content-between/foo',
      'content-around/foo',
      'content-evenly/foo',
      'content-baseline/foo',
      'content-stretch/foo',
    ]),
  ).toEqual('')
})

test('items', async () => {
  expect(
    await run([
      'items-start',
      'items-end',
      'items-end-safe',
      'items-center',
      'items-center-safe',
      'items-baseline',
      'items-baseline-last',
      'items-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".items-baseline {
      align-items: baseline;
    }

    .items-baseline-last {
      align-items: last baseline;
    }

    .items-center {
      align-items: center;
    }

    .items-center-safe {
      align-items: safe center;
    }

    .items-end {
      align-items: flex-end;
    }

    .items-end-safe {
      align-items: safe flex-end;
    }

    .items-start {
      align-items: flex-start;
    }

    .items-stretch {
      align-items: stretch;
    }"
  `)
  expect(
    await run([
      'items',
      '-items-start',
      '-items-end',
      '-items-center',
      '-items-baseline',
      '-items-first-baseline',
      '-items-last-baseline',
      '-items-stretch',
      'items-start/foo',
      'items-end/foo',
      'items-center/foo',
      'items-baseline/foo',
      'items-stretch/foo',
    ]),
  ).toEqual('')
})

test('justify', async () => {
  expect(
    await run([
      'justify-normal',
      'justify-start',
      'justify-end',
      'justify-end-safe',
      'justify-center',
      'justify-center-safe',
      'justify-between',
      'justify-around',
      'justify-evenly',
      'justify-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".justify-around {
      justify-content: space-around;
    }

    .justify-between {
      justify-content: space-between;
    }

    .justify-center {
      justify-content: center;
    }

    .justify-center-safe {
      justify-content: safe center;
    }

    .justify-end {
      justify-content: flex-end;
    }

    .justify-end-safe {
      justify-content: safe flex-end;
    }

    .justify-evenly {
      justify-content: space-evenly;
    }

    .justify-normal {
      justify-content: normal;
    }

    .justify-start {
      justify-content: flex-start;
    }

    .justify-stretch {
      justify-content: stretch;
    }"
  `)
  expect(
    await run([
      'justify',
      '-justify-normal',
      '-justify-start',
      '-justify-end',
      '-justify-center',
      '-justify-between',
      '-justify-around',
      '-justify-evenly',
      '-justify-stretch',
      'justify-normal/foo',
      'justify-start/foo',
      'justify-end/foo',
      'justify-center/foo',
      'justify-between/foo',
      'justify-around/foo',
      'justify-evenly/foo',
      'justify-stretch/foo',
    ]),
  ).toEqual('')
})

test('justify-items', async () => {
  expect(
    await run([
      'justify-items-start',
      'justify-items-end',
      'justify-items-end-safe',
      'justify-items-center',
      'justify-items-center-safe',
      'justify-items-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".justify-items-center {
      justify-items: center;
    }

    .justify-items-center-safe {
      justify-items: safe center;
    }

    .justify-items-end {
      justify-items: end;
    }

    .justify-items-end-safe {
      justify-items: safe end;
    }

    .justify-items-start {
      justify-items: start;
    }

    .justify-items-stretch {
      justify-items: stretch;
    }"
  `)
  expect(
    await run([
      'justify-items',
      '-justify-items-start',
      '-justify-items-end',
      '-justify-items-center',
      '-justify-items-stretch',
      'justify-items-start/foo',
      'justify-items-end/foo',
      'justify-items-center/foo',
      'justify-items-stretch/foo',
    ]),
  ).toEqual('')
})

test('self', async () => {
  expect(
    await run([
      'self-auto',
      'self-start',
      'self-end',
      'self-end-safe',
      'self-center',
      'self-center-safe',
      'self-stretch',
      'self-baseline',
      'self-baseline-last',
    ]),
  ).toMatchInlineSnapshot(`
    ".self-auto {
      align-self: auto;
    }

    .self-baseline {
      align-self: baseline;
    }

    .self-baseline-last {
      align-self: last baseline;
    }

    .self-center {
      align-self: center;
    }

    .self-center-safe {
      align-self: safe center;
    }

    .self-end {
      align-self: flex-end;
    }

    .self-end-safe {
      align-self: safe flex-end;
    }

    .self-start {
      align-self: flex-start;
    }

    .self-stretch {
      align-self: stretch;
    }"
  `)
  expect(
    await run([
      'self',
      '-self-auto',
      '-self-start',
      '-self-end',
      '-self-center',
      '-self-stretch',
      '-self-baseline',
      'self-auto/foo',
      'self-start/foo',
      'self-end/foo',
      'self-center/foo',
      'self-stretch/foo',
      'self-baseline/foo',
    ]),
  ).toEqual('')
})

test('justify-self', async () => {
  expect(
    await run([
      'justify-self-auto',
      'justify-self-start',
      'justify-self-end',
      'justify-self-end-safe',
      'justify-self-center',
      'justify-self-center-safe',
      'justify-self-stretch',
      'justify-self-baseline',
    ]),
  ).toMatchInlineSnapshot(`
    ".justify-self-auto {
      justify-self: auto;
    }

    .justify-self-center {
      justify-self: center;
    }

    .justify-self-center-safe {
      justify-self: safe center;
    }

    .justify-self-end {
      justify-self: flex-end;
    }

    .justify-self-end-safe {
      justify-self: safe flex-end;
    }

    .justify-self-start {
      justify-self: flex-start;
    }

    .justify-self-stretch {
      justify-self: stretch;
    }"
  `)
  expect(
    await run([
      'justify-self',
      '-justify-self-auto',
      '-justify-self-start',
      '-justify-self-end',
      '-justify-self-center',
      '-justify-self-stretch',
      '-justify-self-baseline',
      'justify-self-auto/foo',
      'justify-self-start/foo',
      'justify-self-end/foo',
      'justify-self-center/foo',
      'justify-self-stretch/foo',
      'justify-self-baseline/foo',
    ]),
  ).toEqual('')
})

test('align', async () => {
  expect(
    await run([
      'align-baseline',
      'align-top',
      'align-middle',
      'align-bottom',
      'align-text-top',
      'align-text-bottom',
      'align-sub',
      'align-super',

      'align-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".align-\\[var\\(--value\\)\\] {
      vertical-align: var(--value);
    }

    .align-baseline {
      vertical-align: baseline;
    }

    .align-bottom {
      vertical-align: bottom;
    }

    .align-middle {
      vertical-align: middle;
    }

    .align-sub {
      vertical-align: sub;
    }

    .align-super {
      vertical-align: super;
    }

    .align-text-bottom {
      vertical-align: text-bottom;
    }

    .align-text-top {
      vertical-align: text-top;
    }

    .align-top {
      vertical-align: top;
    }"
  `)
  expect(
    await run([
      'align',
      '-align-baseline',
      '-align-top',
      '-align-middle',
      '-align-bottom',
      '-align-text-top',
      '-align-text-bottom',
      '-align-sub',
      '-align-super',

      '-align-[var(--value)]',

      'align-baseline/foo',
      'align-top/foo',
      'align-middle/foo',
      'align-bottom/foo',
      'align-text-top/foo',
      'align-text-bottom/foo',
      'align-sub/foo',
      'align-super/foo',
      'align-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

