/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('break-before', async () => {
  expect(
    await run([
      'break-before-auto',
      'break-before-avoid',
      'break-before-all',
      'break-before-avoid-page',
      'break-before-page',
      'break-before-left',
      'break-before-right',
      'break-before-column',
    ]),
  ).toMatchInlineSnapshot(`
    ".break-before-all {
      break-before: all;
    }

    .break-before-auto {
      break-before: auto;
    }

    .break-before-avoid {
      break-before: avoid;
    }

    .break-before-avoid-page {
      break-before: avoid-page;
    }

    .break-before-column {
      break-before: column;
    }

    .break-before-left {
      break-before: left;
    }

    .break-before-page {
      break-before: page;
    }

    .break-before-right {
      break-before: right;
    }"
  `)
  expect(
    await run([
      'break-before',
      '-break-before-auto',
      '-break-before-avoid',
      '-break-before-all',
      '-break-before-avoid-page',
      '-break-before-page',
      '-break-before-left',
      '-break-before-right',
      '-break-before-column',
      'break-before-auto/foo',
      'break-before-avoid/foo',
      'break-before-all/foo',
      'break-before-avoid-page/foo',
      'break-before-page/foo',
      'break-before-left/foo',
      'break-before-right/foo',
      'break-before-column/foo',
    ]),
  ).toEqual('')
})

test('break-inside', async () => {
  expect(
    await run([
      'break-inside-auto',
      'break-inside-avoid',
      'break-inside-avoid-page',
      'break-inside-avoid-column',
    ]),
  ).toMatchInlineSnapshot(`
    ".break-inside-auto {
      break-inside: auto;
    }

    .break-inside-avoid {
      break-inside: avoid;
    }

    .break-inside-avoid-column {
      break-inside: avoid-column;
    }

    .break-inside-avoid-page {
      break-inside: avoid-page;
    }"
  `)
  expect(
    await run([
      'break-inside',
      '-break-inside-auto',
      '-break-inside-avoid',
      '-break-inside-avoid-page',
      '-break-inside-avoid-column',
      'break-inside-auto/foo',
      'break-inside-avoid/foo',
      'break-inside-avoid-page/foo',
      'break-inside-avoid-column/foo',
    ]),
  ).toEqual('')
})

test('break-after', async () => {
  expect(
    await run([
      'break-after-auto',
      'break-after-avoid',
      'break-after-all',
      'break-after-avoid-page',
      'break-after-page',
      'break-after-left',
      'break-after-right',
      'break-after-column',
    ]),
  ).toMatchInlineSnapshot(`
    ".break-after-all {
      break-after: all;
    }

    .break-after-auto {
      break-after: auto;
    }

    .break-after-avoid {
      break-after: avoid;
    }

    .break-after-avoid-page {
      break-after: avoid-page;
    }

    .break-after-column {
      break-after: column;
    }

    .break-after-left {
      break-after: left;
    }

    .break-after-page {
      break-after: page;
    }

    .break-after-right {
      break-after: right;
    }"
  `)
  expect(
    await run([
      'break-after',
      '-break-after-auto',
      '-break-after-avoid',
      '-break-after-all',
      '-break-after-avoid-page',
      '-break-after-page',
      '-break-after-left',
      '-break-after-right',
      '-break-after-column',
      'break-after-auto/foo',
      'break-after-avoid/foo',
      'break-after-all/foo',
      'break-after-avoid-page/foo',
      'break-after-page/foo',
      'break-after-left/foo',
      'break-after-right/foo',
      'break-after-column/foo',
    ]),
  ).toEqual('')
})

