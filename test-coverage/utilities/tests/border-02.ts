/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('rounded-tr', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --radius-none: 0px;
          --radius-full: 9999px;
          --radius-sm: 0.125rem;
          --radius: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['rounded-tr', 'rounded-tr-full', 'rounded-tr-none', 'rounded-tr-sm', 'rounded-tr-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-tr {
      border-top-right-radius: var(--radius);
    }

    .rounded-tr-\\[4px\\] {
      border-top-right-radius: 4px;
    }

    .rounded-tr-full {
      border-top-right-radius: var(--radius-full);
    }

    .rounded-tr-none {
      border-top-right-radius: var(--radius-none);
    }

    .rounded-tr-sm {
      border-top-right-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-tr',
      '-rounded-tr-full',
      '-rounded-tr-none',
      '-rounded-tr-sm',
      '-rounded-tr-[4px]',
      'rounded-tr/foo',
      'rounded-tr-full/foo',
      'rounded-tr-none/foo',
      'rounded-tr-sm/foo',
      'rounded-tr-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-br', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --radius-none: 0px;
          --radius-full: 9999px;
          --radius-sm: 0.125rem;
          --radius: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['rounded-br', 'rounded-br-full', 'rounded-br-none', 'rounded-br-sm', 'rounded-br-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-br {
      border-bottom-right-radius: var(--radius);
    }

    .rounded-br-\\[4px\\] {
      border-bottom-right-radius: 4px;
    }

    .rounded-br-full {
      border-bottom-right-radius: var(--radius-full);
    }

    .rounded-br-none {
      border-bottom-right-radius: var(--radius-none);
    }

    .rounded-br-sm {
      border-bottom-right-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-br',
      '-rounded-br-full',
      '-rounded-br-none',
      '-rounded-br-sm',
      '-rounded-br-[4px]',
      'rounded-br/foo',
      'rounded-br-full/foo',
      'rounded-br-none/foo',
      'rounded-br-sm/foo',
      'rounded-br-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-bl', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --radius-none: 0px;
          --radius-full: 9999px;
          --radius-sm: 0.125rem;
          --radius: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['rounded-bl', 'rounded-bl-full', 'rounded-bl-none', 'rounded-bl-sm', 'rounded-bl-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-bl {
      border-bottom-left-radius: var(--radius);
    }

    .rounded-bl-\\[4px\\] {
      border-bottom-left-radius: 4px;
    }

    .rounded-bl-full {
      border-bottom-left-radius: var(--radius-full);
    }

    .rounded-bl-none {
      border-bottom-left-radius: var(--radius-none);
    }

    .rounded-bl-sm {
      border-bottom-left-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-bl',
      '-rounded-bl-full',
      '-rounded-bl-none',
      '-rounded-bl-sm',
      '-rounded-bl-[4px]',
      'rounded-bl/foo',
      'rounded-bl-full/foo',
      'rounded-bl-none/foo',
      'rounded-bl-sm/foo',
      'rounded-bl-[4px]/foo',
    ]),
  ).toEqual('')
})

test('border-style', async () => {
  expect(
    await run([
      'border-solid',
      'border-dashed',
      'border-dotted',
      'border-double',
      'border-hidden',
      'border-none',
    ]),
  ).toMatchInlineSnapshot(`
    ".border-dashed {
      --tw-border-style: dashed;
      border-style: dashed;
    }

    .border-dotted {
      --tw-border-style: dotted;
      border-style: dotted;
    }

    .border-double {
      --tw-border-style: double;
      border-style: double;
    }

    .border-hidden {
      --tw-border-style: hidden;
      border-style: hidden;
    }

    .border-none {
      --tw-border-style: none;
      border-style: none;
    }

    .border-solid {
      --tw-border-style: solid;
      border-style: solid;
    }"
  `)
  expect(
    await run([
      '-border-solid',
      '-border-dashed',
      '-border-dotted',
      '-border-double',
      '-border-hidden',
      '-border-none',
      'border-solid/foo',
      'border-dashed/foo',
      'border-dotted/foo',
      'border-double/foo',
      'border-hidden/foo',
      'border-none/foo',
    ]),
  ).toEqual('')
})

test('border with custom default border width', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --default-border-width: 2px;
        }
        @tailwind utilities;
      `,
      ['border'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-border-style: solid;
        }
      }
    }

    .border {
      border-style: var(--tw-border-style);
      border-width: 2px;
    }

    @property --tw-border-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(await run(['-border', 'border/foo'])).toEqual('')
})

