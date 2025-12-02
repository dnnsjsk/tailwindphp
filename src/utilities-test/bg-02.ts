/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('bg-position', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      ['bg-position-[120px]', 'bg-position-[120px_120px]', 'bg-position-[var(--some-var)]'],
    ),
  ).toMatchInlineSnapshot(`
    ".bg-position-\\[120px\\] {
      background-position: 120px;
    }

    .bg-position-\\[120px_120px\\] {
      background-position: 120px 120px;
    }

    .bg-position-\\[var\\(--some-var\\)\\] {
      background-position: var(--some-var);
    }"
  `)
  expect(
    await run([
      'bg-position',
      'bg-position/foo',
      '-bg-position',
      '-bg-position/foo',

      'bg-position-[120px_120px]/foo',

      '-bg-position-[120px_120px]',
      '-bg-position-[120px_120px]/foo',
    ]),
  ).toEqual('')
})

test('bg-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      ['bg-size-[120px]', 'bg-size-[120px_120px]', 'bg-size-[var(--some-var)]'],
    ),
  ).toMatchInlineSnapshot(`
    ".bg-size-\\[120px\\] {
      background-size: 120px;
    }

    .bg-size-\\[120px_120px\\] {
      background-size: 120px 120px;
    }

    .bg-size-\\[var\\(--some-var\\)\\] {
      background-size: var(--some-var);
    }"
  `)
  expect(
    await run([
      'bg-size',
      'bg-size/foo',
      '-bg-size',
      '-bg-size/foo',

      'bg-size-[120px_120px]/foo',

      '-bg-size-[120px_120px]',
      '-bg-size-[120px_120px]/foo',
    ]),
  ).toEqual('')
})

test('bg-clip', async () => {
  expect(await run(['bg-clip-border', 'bg-clip-padding', 'bg-clip-content', 'bg-clip-text']))
    .toMatchInlineSnapshot(`
      ".bg-clip-border {
        background-clip: border-box;
      }

      .bg-clip-content {
        background-clip: content-box;
      }

      .bg-clip-padding {
        background-clip: padding-box;
      }

      .bg-clip-text {
        -webkit-background-clip: text;
        background-clip: text;
      }"
    `)
  expect(
    await run([
      'bg-clip',
      '-bg-clip-border',
      '-bg-clip-padding',
      '-bg-clip-content',
      '-bg-clip-text',
      'bg-clip-border/foo',
      'bg-clip-padding/foo',
      'bg-clip-content/foo',
      'bg-clip-text/foo',
    ]),
  ).toEqual('')
})

test('bg-origin', async () => {
  expect(await run(['bg-origin-border', 'bg-origin-padding', 'bg-origin-content']))
    .toMatchInlineSnapshot(`
      ".bg-origin-border {
        background-origin: border-box;
      }

      .bg-origin-content {
        background-origin: content-box;
      }

      .bg-origin-padding {
        background-origin: padding-box;
      }"
    `)
  expect(
    await run([
      'bg-origin',
      '-bg-origin-border',
      '-bg-origin-padding',
      '-bg-origin-content',
      'bg-origin-border/foo',
      'bg-origin-padding/foo',
      'bg-origin-content/foo',
    ]),
  ).toEqual('')
})

