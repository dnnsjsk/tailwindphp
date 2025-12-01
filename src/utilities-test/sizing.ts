/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'size-auto',
        'size-full',
        'size-min',
        'size-max',
        'size-fit',
        'size-4',
        'size-1/2',
        'size-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .size-1\\/2 {
      width: 50%;
      height: 50%;
    }

    .size-4 {
      width: var(--spacing-4);
      height: var(--spacing-4);
    }

    .size-\\[4px\\] {
      width: 4px;
      height: 4px;
    }

    .size-auto {
      width: auto;
      height: auto;
    }

    .size-fit {
      width: fit-content;
      height: fit-content;
    }

    .size-full {
      width: 100%;
      height: 100%;
    }

    .size-max {
      width: max-content;
      height: max-content;
    }

    .size-min {
      width: min-content;
      height: min-content;
    }"
  `)
  expect(
    await run([
      'size',
      'size--1',
      'size--1/2',
      'size--1/-2',
      'size-1/-2',
      '-size-4',
      '-size-1/2',
      '-size-[4px]',
      'size-auto/foo',
      'size-full/foo',
      'size-min/foo',
      'size-max/foo',
      'size-fit/foo',
      'size-4/foo',
      'size-1/2/foo',
      'size-[4px]/foo',
    ]),
  ).toEqual('')
})

test('width', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
          --width-xl: 36rem;
        }
        @tailwind utilities;
      `,
      [
        'w-full',
        'w-auto',
        'w-screen',
        'w-svw',
        'w-lvw',
        'w-dvw',
        'w-min',
        'w-max',
        'w-fit',
        'w-4',
        'w-xl',
        'w-1/2',
        'w-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
      --width-xl: 36rem;
    }

    .w-1\\/2 {
      width: 50%;
    }

    .w-4 {
      width: var(--spacing-4);
    }

    .w-\\[4px\\] {
      width: 4px;
    }

    .w-auto {
      width: auto;
    }

    .w-dvw {
      width: 100dvw;
    }

    .w-fit {
      width: fit-content;
    }

    .w-full {
      width: 100%;
    }

    .w-lvw {
      width: 100lvw;
    }

    .w-max {
      width: max-content;
    }

    .w-min {
      width: min-content;
    }

    .w-screen {
      width: 100vw;
    }

    .w-svw {
      width: 100svw;
    }

    .w-xl {
      width: var(--width-xl);
    }"
  `)
  expect(
    await run([
      'w',
      'w--1',
      'w--1/2',
      'w--1/-2',
      'w-1/-2',
      '-w-4',
      '-w-1/2',
      '-w-[4px]',
      'w-full/foo',
      'w-auto/foo',
      'w-screen/foo',
      'w-svw/foo',
      'w-lvw/foo',
      'w-dvw/foo',
      'w-min/foo',
      'w-max/foo',
      'w-fit/foo',
      'w-4/foo',
      'w-xl/foo',
      'w-1/2/foo',
      'w-[4px]/foo',
    ]),
  ).toEqual('')
})

test('height', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'h-full',
        'h-auto',
        'h-screen',
        'h-svh',
        'h-lvh',
        'h-dvh',
        'h-min',
        'h-lh',
        'h-max',
        'h-fit',
        'h-4',
        'h-1/2',
        'h-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .h-1\\/2 {
      height: 50%;
    }

    .h-4 {
      height: var(--spacing-4);
    }

    .h-\\[4px\\] {
      height: 4px;
    }

    .h-auto {
      height: auto;
    }

    .h-dvh {
      height: 100dvh;
    }

    .h-fit {
      height: fit-content;
    }

    .h-full {
      height: 100%;
    }

    .h-lh {
      height: 1lh;
    }

    .h-lvh {
      height: 100lvh;
    }

    .h-max {
      height: max-content;
    }

    .h-min {
      height: min-content;
    }

    .h-screen {
      height: 100vh;
    }

    .h-svh {
      height: 100svh;
    }"
  `)
  expect(
    await run([
      'h',
      '-h-4',
      'h--1',
      'h--1/2',
      'h--1/-2',
      'h-1/-2',
      '-h-1/2',
      '-h-[4px]',
      'h-full/foo',
      'h-auto/foo',
      'h-screen/foo',
      'h-svh/foo',
      'h-lvh/foo',
      'h-dvh/foo',
      'h-lh/foo',
      'h-min/foo',
      'h-max/foo',
      'h-fit/foo',
      'h-4/foo',
      'h-1/2/foo',
      'h-[4px]/foo',
    ]),
  ).toEqual('')
})

test('will-change', async () => {
  expect(
    await run([
      'will-change-auto',
      'will-change-contents',
      'will-change-transform',
      'will-change-scroll',
      'will-change-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".will-change-\\[var\\(--value\\)\\] {
      will-change: var(--value);
    }

    .will-change-auto {
      will-change: auto;
    }

    .will-change-contents {
      will-change: contents;
    }

    .will-change-scroll {
      will-change: scroll-position;
    }

    .will-change-transform {
      will-change: transform;
    }"
  `)
  expect(
    await run([
      'will-change',
      '-will-change-auto',
      '-will-change-contents',
      '-will-change-transform',
      '-will-change-scroll',
      '-will-change-[var(--value)]',
      'will-change-auto/foo',
      'will-change-contents/foo',
      'will-change-transform/foo',
      'will-change-scroll/foo',
      'will-change-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

