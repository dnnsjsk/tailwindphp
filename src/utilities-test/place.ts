/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('place-content', async () => {
  expect(
    await run([
      'place-content-center',
      'place-content-center-safe',
      'place-content-start',
      'place-content-end',
      'place-content-end-safe',
      'place-content-between',
      'place-content-around',
      'place-content-evenly',
      'place-content-baseline',
      'place-content-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".place-content-around {
      place-content: space-around;
    }

    .place-content-baseline {
      place-content: baseline start;
    }

    .place-content-between {
      place-content: space-between;
    }

    .place-content-center {
      place-content: center;
    }

    .place-content-center-safe {
      place-content: safe center;
    }

    .place-content-end {
      place-content: end;
    }

    .place-content-end-safe {
      place-content: safe end;
    }

    .place-content-evenly {
      place-content: space-evenly;
    }

    .place-content-start {
      place-content: start;
    }

    .place-content-stretch {
      place-content: stretch;
    }"
  `)
  expect(
    await run([
      'place-content',
      '-place-content-center',
      '-place-content-start',
      '-place-content-end',
      '-place-content-between',
      '-place-content-around',
      '-place-content-evenly',
      '-place-content-baseline',
      '-place-content-stretch',
      'place-content-center/foo',
      'place-content-start/foo',
      'place-content-end/foo',
      'place-content-between/foo',
      'place-content-around/foo',
      'place-content-evenly/foo',
      'place-content-baseline/foo',
      'place-content-stretch/foo',
    ]),
  ).toEqual('')
})

test('place-items', async () => {
  expect(
    await run([
      'place-items-start',
      'place-items-end',
      'place-items-end-safe',
      'place-items-center',
      'place-items-center-safe',
      'place-items-baseline',
      'place-items-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".place-items-baseline {
      place-items: baseline;
    }

    .place-items-center {
      place-items: center;
    }

    .place-items-center-safe {
      place-items: safe center;
    }

    .place-items-end {
      place-items: end;
    }

    .place-items-end-safe {
      place-items: safe end;
    }

    .place-items-start {
      place-items: start;
    }

    .place-items-stretch {
      place-items: stretch stretch;
    }"
  `)
  expect(
    await run([
      'place-items',
      '-place-items-start',
      '-place-items-end',
      '-place-items-center',
      '-place-items-baseline',
      '-place-items-stretch',
      'place-items-start/foo',
      'place-items-end/foo',
      'place-items-center/foo',
      'place-items-baseline/foo',
      'place-items-stretch/foo',
    ]),
  ).toEqual('')
})

test('place-self', async () => {
  expect(
    await run([
      'place-self-auto',
      'place-self-start',
      'place-self-end',
      'place-self-end-safe',
      'place-self-center',
      'place-self-center-safe',
      'place-self-stretch',
    ]),
  ).toMatchInlineSnapshot(`
    ".place-self-auto {
      place-self: auto;
    }

    .place-self-center {
      place-self: center;
    }

    .place-self-center-safe {
      place-self: safe center;
    }

    .place-self-end {
      place-self: end;
    }

    .place-self-end-safe {
      place-self: safe end;
    }

    .place-self-start {
      place-self: start;
    }

    .place-self-stretch {
      place-self: stretch stretch;
    }"
  `)
  expect(
    await run([
      'place-self',
      '-place-self-auto',
      '-place-self-start',
      '-place-self-end',
      '-place-self-center',
      '-place-self-stretch',
      'place-self-auto/foo',
      'place-self-start/foo',
      'place-self-end/foo',
      'place-self-center/foo',
      'place-self-stretch/foo',
    ]),
  ).toEqual('')
})

