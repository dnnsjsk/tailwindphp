/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('border-collapse', async () => {
  expect(await run(['border-collapse', 'border-separate'])).toMatchInlineSnapshot(`
    ".border-collapse {
      border-collapse: collapse;
    }

    .border-separate {
      border-collapse: separate;
    }"
  `)
  expect(
    await run([
      '-border-collapse',
      '-border-separate',
      'border-collapse/foo',
      'border-separate/foo',
    ]),
  ).toEqual('')
})

test('border-spacing', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-1: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['border-spacing-1', 'border-spacing-[123px]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-border-spacing-x: 0;
          --tw-border-spacing-y: 0;
        }
      }
    }

    :root, :host {
      --spacing-1: .25rem;
    }

    .border-spacing-1 {
      --tw-border-spacing-x: var(--spacing-1);
      --tw-border-spacing-y: var(--spacing-1);
      border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
    }

    .border-spacing-\\[123px\\] {
      --tw-border-spacing-x: 123px;
      --tw-border-spacing-y: 123px;
      border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
    }

    @property --tw-border-spacing-x {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-spacing-y {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'border-spacing',
      '-border-spacing-1',
      '-border-spacing-[123px]',
      'border-spacing-1/foo',
      'border-spacing-[123px]/foo',
    ]),
  ).toEqual('')
})

test('border-spacing-x', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-1: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['border-spacing-x-1', 'border-spacing-x-[123px]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-border-spacing-x: 0;
          --tw-border-spacing-y: 0;
        }
      }
    }

    :root, :host {
      --spacing-1: .25rem;
    }

    .border-spacing-x-1 {
      --tw-border-spacing-x: var(--spacing-1);
      border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
    }

    .border-spacing-x-\\[123px\\] {
      --tw-border-spacing-x: 123px;
      border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
    }

    @property --tw-border-spacing-x {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-spacing-y {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'border-spacing-x',
      '-border-spacing-x-1',
      '-border-spacing-x-[123px]',
      'border-spacing-x-1/foo',
      'border-spacing-x-[123px]/foo',
    ]),
  ).toEqual('')
})

test('border-spacing-y', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-1: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['border-spacing-y-1', 'border-spacing-y-[123px]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-border-spacing-x: 0;
          --tw-border-spacing-y: 0;
        }
      }
    }

    :root, :host {
      --spacing-1: .25rem;
    }

    .border-spacing-y-1 {
      --tw-border-spacing-y: var(--spacing-1);
      border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
    }

    .border-spacing-y-\\[123px\\] {
      --tw-border-spacing-y: 123px;
      border-spacing: var(--tw-border-spacing-x) var(--tw-border-spacing-y);
    }

    @property --tw-border-spacing-x {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-spacing-y {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'border-spacing-x',
      '-border-spacing-y-1',
      '-border-spacing-y-[123px]',
      'border-spacing-y-1/foo',
      'border-spacing-y-[123px]/foo',
    ]),
  ).toEqual('')
})

test('rounded', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --radius-sm: 0.125rem;
          --radius: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['rounded', 'rounded-full', 'rounded-none', 'rounded-sm', 'rounded-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded {
      border-radius: var(--radius);
    }

    .rounded-\\[4px\\] {
      border-radius: 4px;
    }

    .rounded-full {
      border-radius: 3.40282e38px;
    }

    .rounded-none {
      border-radius: 0;
    }

    .rounded-sm {
      border-radius: var(--radius-sm);
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --radius-full: 99999px;
        }
        @tailwind utilities;
      `,
      ['rounded-full'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-full: 99999px;
    }

    .rounded-full {
      border-radius: var(--radius-full);
    }"
  `)
  expect(
    await run([
      '-rounded',
      '-rounded-full',
      '-rounded-none',
      '-rounded-sm',
      '-rounded-[4px]',
      'rounded/foo',
      'rounded-full/foo',
      'rounded-none/foo',
      'rounded-sm/foo',
      'rounded-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-s', async () => {
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
      ['rounded-s', 'rounded-s-full', 'rounded-s-none', 'rounded-s-sm', 'rounded-s-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-s {
      border-start-start-radius: var(--radius);
      border-end-start-radius: var(--radius);
    }

    .rounded-s-\\[4px\\] {
      border-start-start-radius: 4px;
      border-end-start-radius: 4px;
    }

    .rounded-s-full {
      border-start-start-radius: var(--radius-full);
      border-end-start-radius: var(--radius-full);
    }

    .rounded-s-none {
      border-start-start-radius: var(--radius-none);
      border-end-start-radius: var(--radius-none);
    }

    .rounded-s-sm {
      border-start-start-radius: var(--radius-sm);
      border-end-start-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-s',
      '-rounded-s-full',
      '-rounded-s-none',
      '-rounded-s-sm',
      '-rounded-s-[4px]',
      'rounded-s/foo',
      'rounded-s-full/foo',
      'rounded-s-none/foo',
      'rounded-s-sm/foo',
      'rounded-s-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-e', async () => {
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
      ['rounded-e', 'rounded-e-full', 'rounded-e-none', 'rounded-e-sm', 'rounded-e-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-e {
      border-start-end-radius: var(--radius);
      border-end-end-radius: var(--radius);
    }

    .rounded-e-\\[4px\\] {
      border-start-end-radius: 4px;
      border-end-end-radius: 4px;
    }

    .rounded-e-full {
      border-start-end-radius: var(--radius-full);
      border-end-end-radius: var(--radius-full);
    }

    .rounded-e-none {
      border-start-end-radius: var(--radius-none);
      border-end-end-radius: var(--radius-none);
    }

    .rounded-e-sm {
      border-start-end-radius: var(--radius-sm);
      border-end-end-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-e',
      '-rounded-e-full',
      '-rounded-e-none',
      '-rounded-e-sm',
      '-rounded-e-[4px]',
      'rounded-e/foo',
      'rounded-e-full/foo',
      'rounded-e-none/foo',
      'rounded-e-sm/foo',
      'rounded-e-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-t', async () => {
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
      ['rounded-t', 'rounded-t-full', 'rounded-t-none', 'rounded-t-sm', 'rounded-t-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-t {
      border-top-left-radius: var(--radius);
      border-top-right-radius: var(--radius);
    }

    .rounded-t-\\[4px\\] {
      border-top-left-radius: 4px;
      border-top-right-radius: 4px;
    }

    .rounded-t-full {
      border-top-left-radius: var(--radius-full);
      border-top-right-radius: var(--radius-full);
    }

    .rounded-t-none {
      border-top-left-radius: var(--radius-none);
      border-top-right-radius: var(--radius-none);
    }

    .rounded-t-sm {
      border-top-left-radius: var(--radius-sm);
      border-top-right-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-t',
      '-rounded-t-full',
      '-rounded-t-none',
      '-rounded-t-sm',
      '-rounded-t-[4px]',
      'rounded-t/foo',
      'rounded-t-full/foo',
      'rounded-t-none/foo',
      'rounded-t-sm/foo',
      'rounded-t-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-r', async () => {
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
      ['rounded-r', 'rounded-r-full', 'rounded-r-none', 'rounded-r-sm', 'rounded-r-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-r {
      border-top-right-radius: var(--radius);
      border-bottom-right-radius: var(--radius);
    }

    .rounded-r-\\[4px\\] {
      border-top-right-radius: 4px;
      border-bottom-right-radius: 4px;
    }

    .rounded-r-full {
      border-top-right-radius: var(--radius-full);
      border-bottom-right-radius: var(--radius-full);
    }

    .rounded-r-none {
      border-top-right-radius: var(--radius-none);
      border-bottom-right-radius: var(--radius-none);
    }

    .rounded-r-sm {
      border-top-right-radius: var(--radius-sm);
      border-bottom-right-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-r',
      '-rounded-r-full',
      '-rounded-r-none',
      '-rounded-r-sm',
      '-rounded-r-[4px]',
      'rounded-r/foo',
      'rounded-r-full/foo',
      'rounded-r-none/foo',
      'rounded-r-sm/foo',
      'rounded-r-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-b', async () => {
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
      ['rounded-b', 'rounded-b-full', 'rounded-b-none', 'rounded-b-sm', 'rounded-b-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-b {
      border-bottom-right-radius: var(--radius);
      border-bottom-left-radius: var(--radius);
    }

    .rounded-b-\\[4px\\] {
      border-bottom-right-radius: 4px;
      border-bottom-left-radius: 4px;
    }

    .rounded-b-full {
      border-bottom-right-radius: var(--radius-full);
      border-bottom-left-radius: var(--radius-full);
    }

    .rounded-b-none {
      border-bottom-right-radius: var(--radius-none);
      border-bottom-left-radius: var(--radius-none);
    }

    .rounded-b-sm {
      border-bottom-right-radius: var(--radius-sm);
      border-bottom-left-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-b',
      '-rounded-b-full',
      '-rounded-b-none',
      '-rounded-b-sm',
      '-rounded-b-[4px]',
      'rounded-b/foo',
      'rounded-b-full/foo',
      'rounded-b-none/foo',
      'rounded-b-sm/foo',
      'rounded-b-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-l', async () => {
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
      ['rounded-l', 'rounded-l-full', 'rounded-l-none', 'rounded-l-sm', 'rounded-l-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-l {
      border-top-left-radius: var(--radius);
      border-bottom-left-radius: var(--radius);
    }

    .rounded-l-\\[4px\\] {
      border-top-left-radius: 4px;
      border-bottom-left-radius: 4px;
    }

    .rounded-l-full {
      border-top-left-radius: var(--radius-full);
      border-bottom-left-radius: var(--radius-full);
    }

    .rounded-l-none {
      border-top-left-radius: var(--radius-none);
      border-bottom-left-radius: var(--radius-none);
    }

    .rounded-l-sm {
      border-top-left-radius: var(--radius-sm);
      border-bottom-left-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-l',
      '-rounded-l-full',
      '-rounded-l-none',
      '-rounded-l-sm',
      '-rounded-l-[4px]',
      'rounded-l/foo',
      'rounded-l-full/foo',
      'rounded-l-none/foo',
      'rounded-l-sm/foo',
      'rounded-l-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-ss', async () => {
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
      ['rounded-ss', 'rounded-ss-full', 'rounded-ss-none', 'rounded-ss-sm', 'rounded-ss-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-ss {
      border-start-start-radius: var(--radius);
    }

    .rounded-ss-\\[4px\\] {
      border-start-start-radius: 4px;
    }

    .rounded-ss-full {
      border-start-start-radius: var(--radius-full);
    }

    .rounded-ss-none {
      border-start-start-radius: var(--radius-none);
    }

    .rounded-ss-sm {
      border-start-start-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-ss',
      '-rounded-ss-full',
      '-rounded-ss-none',
      '-rounded-ss-sm',
      '-rounded-ss-[4px]',
      'rounded-ss/foo',
      'rounded-ss-full/foo',
      'rounded-ss-none/foo',
      'rounded-ss-sm/foo',
      'rounded-ss-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-se', async () => {
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
      ['rounded-se', 'rounded-se-full', 'rounded-se-none', 'rounded-se-sm', 'rounded-se-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-se {
      border-start-end-radius: var(--radius);
    }

    .rounded-se-\\[4px\\] {
      border-start-end-radius: 4px;
    }

    .rounded-se-full {
      border-start-end-radius: var(--radius-full);
    }

    .rounded-se-none {
      border-start-end-radius: var(--radius-none);
    }

    .rounded-se-sm {
      border-start-end-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-se',
      '-rounded-se-full',
      '-rounded-se-none',
      '-rounded-se-sm',
      '-rounded-se-[4px]',
      'rounded-se/foo',
      'rounded-se-full/foo',
      'rounded-se-none/foo',
      'rounded-se-sm/foo',
      'rounded-se-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-ee', async () => {
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
      ['rounded-ee', 'rounded-ee-full', 'rounded-ee-none', 'rounded-ee-sm', 'rounded-ee-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-ee {
      border-end-end-radius: var(--radius);
    }

    .rounded-ee-\\[4px\\] {
      border-end-end-radius: 4px;
    }

    .rounded-ee-full {
      border-end-end-radius: var(--radius-full);
    }

    .rounded-ee-none {
      border-end-end-radius: var(--radius-none);
    }

    .rounded-ee-sm {
      border-end-end-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-ee',
      '-rounded-ee-full',
      '-rounded-ee-none',
      '-rounded-ee-sm',
      '-rounded-ee-[4px]',
      'rounded-ee/foo',
      'rounded-ee-full/foo',
      'rounded-ee-none/foo',
      'rounded-ee-sm/foo',
      'rounded-ee-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-es', async () => {
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
      ['rounded-es', 'rounded-es-full', 'rounded-es-none', 'rounded-es-sm', 'rounded-es-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-es {
      border-end-start-radius: var(--radius);
    }

    .rounded-es-\\[4px\\] {
      border-end-start-radius: 4px;
    }

    .rounded-es-full {
      border-end-start-radius: var(--radius-full);
    }

    .rounded-es-none {
      border-end-start-radius: var(--radius-none);
    }

    .rounded-es-sm {
      border-end-start-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-es',
      '-rounded-es-full',
      '-rounded-es-none',
      '-rounded-es-sm',
      '-rounded-es-[4px]',
      'rounded-es/foo',
      'rounded-es-full/foo',
      'rounded-es-none/foo',
      'rounded-es-sm/foo',
      'rounded-es-[4px]/foo',
    ]),
  ).toEqual('')
})

test('rounded-tl', async () => {
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
      ['rounded-tl', 'rounded-tl-full', 'rounded-tl-none', 'rounded-tl-sm', 'rounded-tl-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --radius-none: 0px;
      --radius-full: 9999px;
      --radius-sm: .125rem;
      --radius: .25rem;
    }

    .rounded-tl {
      border-top-left-radius: var(--radius);
    }

    .rounded-tl-\\[4px\\] {
      border-top-left-radius: 4px;
    }

    .rounded-tl-full {
      border-top-left-radius: var(--radius-full);
    }

    .rounded-tl-none {
      border-top-left-radius: var(--radius-none);
    }

    .rounded-tl-sm {
      border-top-left-radius: var(--radius-sm);
    }"
  `)
  expect(
    await run([
      '-rounded-tl',
      '-rounded-tl-full',
      '-rounded-tl-none',
      '-rounded-tl-sm',
      '-rounded-tl-[4px]',
      'rounded-tl/foo',
      'rounded-tl-full/foo',
      'rounded-tl-none/foo',
      'rounded-tl-sm/foo',
      'rounded-tl-[4px]/foo',
    ]),
  ).toEqual('')
})

