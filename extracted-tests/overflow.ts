/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('overflow', async () => {
  expect(
    await run([
      'overflow-auto',
      'overflow-hidden',
      'overflow-clip',
      'overflow-visible',
      'overflow-scroll',
    ]),
  ).toMatchInlineSnapshot(`
    ".overflow-auto {
      overflow: auto;
    }

    .overflow-clip {
      overflow: clip;
    }

    .overflow-hidden {
      overflow: hidden;
    }

    .overflow-scroll {
      overflow: scroll;
    }

    .overflow-visible {
      overflow: visible;
    }"
  `)
  expect(
    await run([
      'overflow',
      '-overflow-auto',
      '-overflow-hidden',
      '-overflow-clip',
      '-overflow-visible',
      '-overflow-scroll',
      'overflow-auto/foo',
      'overflow-hidden/foo',
      'overflow-clip/foo',
      'overflow-visible/foo',
      'overflow-scroll/foo',
    ]),
  ).toEqual('')
})

test('overflow-x', async () => {
  expect(
    await run([
      'overflow-x-auto',
      'overflow-x-hidden',
      'overflow-x-clip',
      'overflow-x-visible',
      'overflow-x-scroll',
    ]),
  ).toMatchInlineSnapshot(`
    ".overflow-x-auto {
      overflow-x: auto;
    }

    .overflow-x-clip {
      overflow-x: clip;
    }

    .overflow-x-hidden {
      overflow-x: hidden;
    }

    .overflow-x-scroll {
      overflow-x: scroll;
    }

    .overflow-x-visible {
      overflow-x: visible;
    }"
  `)
  expect(
    await run([
      'overflow-x',
      '-overflow-x-auto',
      '-overflow-x-hidden',
      '-overflow-x-clip',
      '-overflow-x-visible',
      '-overflow-x-scroll',
      'overflow-x-auto/foo',
      'overflow-x-hidden/foo',
      'overflow-x-clip/foo',
      'overflow-x-visible/foo',
      'overflow-x-scroll/foo',
    ]),
  ).toEqual('')
})

test('overflow-y', async () => {
  expect(
    await run([
      'overflow-y-auto',
      'overflow-y-hidden',
      'overflow-y-clip',
      'overflow-y-visible',
      'overflow-y-scroll',
    ]),
  ).toMatchInlineSnapshot(`
    ".overflow-y-auto {
      overflow-y: auto;
    }

    .overflow-y-clip {
      overflow-y: clip;
    }

    .overflow-y-hidden {
      overflow-y: hidden;
    }

    .overflow-y-scroll {
      overflow-y: scroll;
    }

    .overflow-y-visible {
      overflow-y: visible;
    }"
  `)
  expect(
    await run([
      'overflow-y',
      '-overflow-y-auto',
      '-overflow-y-hidden',
      '-overflow-y-clip',
      '-overflow-y-visible',
      '-overflow-y-scroll',
      'overflow-y-auto/foo',
      'overflow-y-hidden/foo',
      'overflow-y-clip/foo',
      'overflow-y-visible/foo',
      'overflow-y-scroll/foo',
    ]),
  ).toEqual('')
})

test('overflow-wrap', async () => {
  expect(await run(['wrap-anywhere', 'wrap-break-word', 'wrap-normal'])).toMatchInlineSnapshot(`
    ".wrap-anywhere {
      overflow-wrap: anywhere;
    }

    .wrap-break-word {
      overflow-wrap: break-word;
    }

    .wrap-normal {
      overflow-wrap: normal;
    }"
  `)
  expect(
    await run([
      '-wrap-anywhere',
      '-wrap-break-word',
      '-wrap-normal',
      'wrap-anywhere/foo',
      'wrap-break-word/foo',
      'wrap-normal/foo',
    ]),
  ).toEqual('')
})

