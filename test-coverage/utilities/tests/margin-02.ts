/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('min-width', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
          --container-xl: 36rem;
        }
        @tailwind utilities;
      `,
      [
        'min-w-full',
        'min-w-auto',
        'min-w-min',
        'min-w-max',
        'min-w-fit',
        'min-w-4',
        'min-w-xl',
        'min-w-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
      --container-xl: 36rem;
    }

    .min-w-4 {
      min-width: var(--spacing-4);
    }

    .min-w-\\[4px\\] {
      min-width: 4px;
    }

    .min-w-auto {
      min-width: auto;
    }

    .min-w-fit {
      min-width: fit-content;
    }

    .min-w-full {
      min-width: 100%;
    }

    .min-w-max {
      min-width: max-content;
    }

    .min-w-min {
      min-width: min-content;
    }

    .min-w-xl {
      min-width: var(--container-xl);
    }"
  `)
  expect(
    await run([
      'min-w',
      '-min-w-4',
      '-min-w-[4px]',
      'min-w-auto/foo',
      'min-w-full/foo',
      'min-w-min/foo',
      'min-w-max/foo',
      'min-w-fit/foo',
      'min-w-4/foo',
      'min-w-xl/foo',
      'min-w-[4px]/foo',
    ]),
  ).toEqual('')
})

test('max-width', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
          --container-xl: 36rem;
        }
        @tailwind utilities;
      `,
      ['max-w-none', 'max-w-full', 'max-w-max', 'max-w-fit', 'max-w-4', 'max-w-xl', 'max-w-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
      --container-xl: 36rem;
    }

    .max-w-4 {
      max-width: var(--spacing-4);
    }

    .max-w-\\[4px\\] {
      max-width: 4px;
    }

    .max-w-fit {
      max-width: fit-content;
    }

    .max-w-full {
      max-width: 100%;
    }

    .max-w-max {
      max-width: max-content;
    }

    .max-w-none {
      max-width: none;
    }

    .max-w-xl {
      max-width: var(--container-xl);
    }"
  `)
  expect(
    await run([
      'max-w',
      'max-w-auto',
      '-max-w-4',
      '-max-w-[4px]',
      'max-w-none/foo',
      'max-w-full/foo',
      'max-w-max/foo',
      'max-w-max/foo',
      'max-w-fit/foo',
      'max-w-4/foo',
      'max-w-xl/foo',
      'max-w-[4px]/foo',
    ]),
  ).toEqual('')
})

test('min-height', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'min-h-full',
        'min-h-auto',
        'min-h-screen',
        'min-h-svh',
        'min-h-lvh',
        'min-h-dvh',
        'min-h-min',
        'min-h-lh',
        'min-h-max',
        'min-h-fit',
        'min-h-4',
        'min-h-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .min-h-4 {
      min-height: var(--spacing-4);
    }

    .min-h-\\[4px\\] {
      min-height: 4px;
    }

    .min-h-auto {
      min-height: auto;
    }

    .min-h-dvh {
      min-height: 100dvh;
    }

    .min-h-fit {
      min-height: fit-content;
    }

    .min-h-full {
      min-height: 100%;
    }

    .min-h-lh {
      min-height: 1lh;
    }

    .min-h-lvh {
      min-height: 100lvh;
    }

    .min-h-max {
      min-height: max-content;
    }

    .min-h-min {
      min-height: min-content;
    }

    .min-h-screen {
      min-height: 100vh;
    }

    .min-h-svh {
      min-height: 100svh;
    }"
  `)
  expect(
    await run([
      'min-h',
      '-min-h-4',
      '-min-h-[4px]',
      'min-h-auto/foo',
      'min-h-full/foo',
      'min-h-screen/foo',
      'min-h-svh/foo',
      'min-h-lvh/foo',
      'min-h-dvh/foo',
      'min-h-lh/foo',
      'min-h-min/foo',
      'min-h-max/foo',
      'min-h-fit/foo',
      'min-h-4/foo',
      'min-h-[4px]/foo',
    ]),
  ).toEqual('')
})

test('max-height', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'max-h-none',
        'max-h-full',
        'max-h-screen',
        'max-h-svh',
        'max-h-lvh',
        'max-h-dvh',
        'max-h-lh',
        'max-h-min',
        'max-h-max',
        'max-h-fit',
        'max-h-4',
        'max-h-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .max-h-4 {
      max-height: var(--spacing-4);
    }

    .max-h-\\[4px\\] {
      max-height: 4px;
    }

    .max-h-dvh {
      max-height: 100dvh;
    }

    .max-h-fit {
      max-height: fit-content;
    }

    .max-h-full {
      max-height: 100%;
    }

    .max-h-lh {
      max-height: 1lh;
    }

    .max-h-lvh {
      max-height: 100lvh;
    }

    .max-h-max {
      max-height: max-content;
    }

    .max-h-min {
      max-height: min-content;
    }

    .max-h-none {
      max-height: none;
    }

    .max-h-screen {
      max-height: 100vh;
    }

    .max-h-svh {
      max-height: 100svh;
    }"
  `)
  expect(
    await run([
      'max-h',
      'max-h-auto',
      '-max-h-4',
      '-max-h-[4px]',
      'max-h-none/foo',
      'max-h-full/foo',
      'max-h-screen/foo',
      'max-h-svh/foo',
      'max-h-lvh/foo',
      'max-h-dvh/foo',
      'max-h-lh/foo',
      'max-h-min/foo',
      'max-h-max/foo',
      'max-h-fit/foo',
      'max-h-4/foo',
      'max-h-[4px]/foo',
    ]),
  ).toEqual('')
})

test('min-inline-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
          --container-xl: 36rem;
        }
        @tailwind utilities;
      `,
      [
        'min-inline-full',
        'min-inline-auto',
        'min-inline-min',
        'min-inline-max',
        'min-inline-fit',
        'min-inline-4',
        'min-inline-xl',
        'min-inline-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
      --container-xl: 36rem;
    }

    .min-inline-4 {
      min-inline-size: var(--spacing-4);
    }

    .min-inline-\\[4px\\] {
      min-inline-size: 4px;
    }

    .min-inline-auto {
      min-inline-size: auto;
    }

    .min-inline-fit {
      min-inline-size: fit-content;
    }

    .min-inline-full {
      min-inline-size: 100%;
    }

    .min-inline-max {
      min-inline-size: max-content;
    }

    .min-inline-min {
      min-inline-size: min-content;
    }

    .min-inline-xl {
      min-inline-size: var(--container-xl);
    }"
  `)
  expect(
    await run([
      'min-inline',
      '-min-inline-4',
      '-min-inline-[4px]',
      'min-inline-auto/foo',
      'min-inline-full/foo',
      'min-inline-min/foo',
      'min-inline-max/foo',
      'min-inline-fit/foo',
      'min-inline-4/foo',
      'min-inline-xl/foo',
      'min-inline-[4px]/foo',
    ]),
  ).toEqual('')
})

test('max-inline-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
          --container-xl: 36rem;
        }
        @tailwind utilities;
      `,
      [
        'max-inline-none',
        'max-inline-full',
        'max-inline-max',
        'max-inline-fit',
        'max-inline-4',
        'max-inline-xl',
        'max-inline-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
      --container-xl: 36rem;
    }

    .max-inline-4 {
      max-inline-size: var(--spacing-4);
    }

    .max-inline-\\[4px\\] {
      max-inline-size: 4px;
    }

    .max-inline-fit {
      max-inline-size: fit-content;
    }

    .max-inline-full {
      max-inline-size: 100%;
    }

    .max-inline-max {
      max-inline-size: max-content;
    }

    .max-inline-none {
      max-inline-size: none;
    }

    .max-inline-xl {
      max-inline-size: var(--container-xl);
    }"
  `)
  expect(
    await run([
      'max-inline',
      'max-inline-auto',
      '-max-inline-4',
      '-max-inline-[4px]',
      'max-inline-none/foo',
      'max-inline-full/foo',
      'max-inline-max/foo',
      'max-inline-max/foo',
      'max-inline-fit/foo',
      'max-inline-4/foo',
      'max-inline-xl/foo',
      'max-inline-[4px]/foo',
    ]),
  ).toEqual('')
})

test('min-block-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'min-block-full',
        'min-block-auto',
        'min-block-screen',
        'min-block-svh',
        'min-block-lvh',
        'min-block-dvh',
        'min-block-min',
        'min-block-lh',
        'min-block-max',
        'min-block-fit',
        'min-block-4',
        'min-block-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .min-block-4 {
      min-block-size: var(--spacing-4);
    }

    .min-block-\\[4px\\] {
      min-block-size: 4px;
    }

    .min-block-auto {
      min-block-size: auto;
    }

    .min-block-dvh {
      min-block-size: 100dvh;
    }

    .min-block-fit {
      min-block-size: fit-content;
    }

    .min-block-full {
      min-block-size: 100%;
    }

    .min-block-lh {
      min-block-size: 1lh;
    }

    .min-block-lvh {
      min-block-size: 100lvh;
    }

    .min-block-max {
      min-block-size: max-content;
    }

    .min-block-min {
      min-block-size: min-content;
    }

    .min-block-screen {
      min-block-size: 100vh;
    }

    .min-block-svh {
      min-block-size: 100svh;
    }"
  `)
  expect(
    await run([
      'min-block',
      '-min-block-4',
      '-min-block-[4px]',
      'min-block-auto/foo',
      'min-block-full/foo',
      'min-block-screen/foo',
      'min-block-svh/foo',
      'min-block-lvh/foo',
      'min-block-dvh/foo',
      'min-block-lh/foo',
      'min-block-min/foo',
      'min-block-max/foo',
      'min-block-fit/foo',
      'min-block-4/foo',
      'min-block-[4px]/foo',
    ]),
  ).toEqual('')
})

test('max-block-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'max-block-none',
        'max-block-full',
        'max-block-screen',
        'max-block-svh',
        'max-block-lvh',
        'max-block-dvh',
        'max-block-lh',
        'max-block-min',
        'max-block-max',
        'max-block-fit',
        'max-block-4',
        'max-block-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .max-block-4 {
      max-block-size: var(--spacing-4);
    }

    .max-block-\\[4px\\] {
      max-block-size: 4px;
    }

    .max-block-dvh {
      max-block-size: 100dvh;
    }

    .max-block-fit {
      max-block-size: fit-content;
    }

    .max-block-full {
      max-block-size: 100%;
    }

    .max-block-lh {
      max-block-size: 1lh;
    }

    .max-block-lvh {
      max-block-size: 100lvh;
    }

    .max-block-max {
      max-block-size: max-content;
    }

    .max-block-min {
      max-block-size: min-content;
    }

    .max-block-none {
      max-block-size: none;
    }

    .max-block-screen {
      max-block-size: 100vh;
    }

    .max-block-svh {
      max-block-size: 100svh;
    }"
  `)
  expect(
    await run([
      'max-block',
      'max-block-auto',
      '-max-block-4',
      '-max-block-[4px]',
      'max-block-none/foo',
      'max-block-full/foo',
      'max-block-screen/foo',
      'max-block-svh/foo',
      'max-block-lvh/foo',
      'max-block-dvh/foo',
      'max-block-lh/foo',
      'max-block-min/foo',
      'max-block-max/foo',
      'max-block-fit/foo',
      'max-block-4/foo',
      'max-block-[4px]/foo',
    ]),
  ).toEqual('')
})

