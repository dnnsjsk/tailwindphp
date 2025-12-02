/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('perspective-origin', async () => {
  expect(
    await run([
      'perspective-origin-center',
      'perspective-origin-top',
      'perspective-origin-top-right',
      'perspective-origin-right',
      'perspective-origin-bottom-right',
      'perspective-origin-bottom',
      'perspective-origin-bottom-left',
      'perspective-origin-left',
      'perspective-origin-top-left',
      'perspective-origin-[50px_100px]',
      'perspective-origin-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".perspective-origin-\\[50px_100px\\] {
      perspective-origin: 50px 100px;
    }

    .perspective-origin-\\[var\\(--value\\)\\] {
      perspective-origin: var(--value);
    }

    .perspective-origin-bottom {
      perspective-origin: bottom;
    }

    .perspective-origin-bottom-left {
      perspective-origin: 0 100%;
    }

    .perspective-origin-bottom-right {
      perspective-origin: 100% 100%;
    }

    .perspective-origin-center {
      perspective-origin: center;
    }

    .perspective-origin-left {
      perspective-origin: 0;
    }

    .perspective-origin-right {
      perspective-origin: 100%;
    }

    .perspective-origin-top {
      perspective-origin: top;
    }

    .perspective-origin-top-left {
      perspective-origin: 0 0;
    }

    .perspective-origin-top-right {
      perspective-origin: 100% 0;
    }"
  `)
  expect(
    await run([
      '-perspective-origin-center',
      '-perspective-origin-[var(--value)]',
      'perspective-origin-center/foo',
      'perspective-origin-top/foo',
      'perspective-origin-top-right/foo',
      'perspective-origin-right/foo',
      'perspective-origin-bottom-right/foo',
      'perspective-origin-bottom/foo',
      'perspective-origin-bottom-left/foo',
      'perspective-origin-left/foo',
      'perspective-origin-top-left/foo',
      'perspective-origin-[50px_100px]/foo',
      'perspective-origin-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --perspective-origin-top: 10px 20px;
        }
        @tailwind utilities;
      `,
      ['perspective-origin-top'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --perspective-origin-top: 10px 20px;
    }

    .perspective-origin-top {
      perspective-origin: var(--perspective-origin-top);
      perspective: var(--perspective-origin-top);
    }"
  `)
})

test('perspective', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --perspective-dramatic: 100px;
          --perspective-normal: 500px;
        }
        @tailwind utilities;
      `,
      ['perspective-normal', 'perspective-dramatic', 'perspective-none', 'perspective-[456px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --perspective-dramatic: 100px;
      --perspective-normal: 500px;
    }

    .perspective-\\[456px\\] {
      perspective: 456px;
    }

    .perspective-dramatic {
      perspective: var(--perspective-dramatic);
    }

    .perspective-none {
      perspective: none;
    }

    .perspective-normal {
      perspective: var(--perspective-normal);
    }"
  `)
  expect(
    await run([
      'perspective',
      '-perspective',
      'perspective-potato',
      'perspective-123',
      'perspective-normal/foo',
      'perspective-dramatic/foo',
      'perspective-none/foo',
      'perspective-[456px]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --perspective-none: 400px;
        }
        @tailwind utilities;
      `,
      ['perspective-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --perspective-none: 400px;
    }

    .perspective-none {
      perspective: var(--perspective-none);
    }"
  `)
})

test('p', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['p-1', 'p-4', 'p-99', 'p-big', 'p-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .p-1 {
      padding: calc(var(--spacing) * 1);
    }

    .p-4 {
      padding: calc(var(--spacing) * 4);
    }

    .p-99 {
      padding: calc(var(--spacing) * 99);
    }

    .p-\\[4px\\] {
      padding: 4px;
    }

    .p-big {
      padding: var(--spacing-big);
    }"
  `)
  expect(await run(['p', '-p-4', '-p-[4px]', 'p-4/foo', 'p-[4px]/foo'])).toEqual('')
})

test('px', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['px-1', 'px-99', 'px-2.5', 'px-big', 'px-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .px-1 {
      padding-inline: calc(var(--spacing) * 1);
    }

    .px-2\\.5 {
      padding-inline: calc(var(--spacing) * 2.5);
    }

    .px-99 {
      padding-inline: calc(var(--spacing) * 99);
    }

    .px-\\[4px\\] {
      padding-inline: 4px;
    }

    .px-big {
      padding-inline: var(--spacing-big);
    }"
  `)
  expect(await run(['px', '-px-4', '-px-[4px]', 'px-4/foo', 'px-[4px]/foo'])).toEqual('')
})

test('py', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['py-1', 'py-4', 'py-99', 'py-big', 'py-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .py-1 {
      padding-block: calc(var(--spacing) * 1);
    }

    .py-4 {
      padding-block: calc(var(--spacing) * 4);
    }

    .py-99 {
      padding-block: calc(var(--spacing) * 99);
    }

    .py-\\[4px\\] {
      padding-block: 4px;
    }

    .py-big {
      padding-block: var(--spacing-big);
    }"
  `)
  expect(await run(['py', '-py-4', '-py-[4px]', 'py-4/foo', 'py-[4px]/foo'])).toEqual('')
})

test('pt', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['pt-1', 'pt-4', 'pt-99', 'pt-big', 'pt-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .pt-1 {
      padding-top: calc(var(--spacing) * 1);
    }

    .pt-4 {
      padding-top: calc(var(--spacing) * 4);
    }

    .pt-99 {
      padding-top: calc(var(--spacing) * 99);
    }

    .pt-\\[4px\\] {
      padding-top: 4px;
    }

    .pt-big {
      padding-top: var(--spacing-big);
    }"
  `)
  expect(await run(['pt', '-pt-4', '-pt-[4px]', 'pt-4/foo', 'pt-[4px]/foo'])).toEqual('')
})

test('ps', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['ps-1', 'ps-4', 'ps-99', 'ps-big', 'ps-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .ps-1 {
      padding-inline-start: calc(var(--spacing) * 1);
    }

    .ps-4 {
      padding-inline-start: calc(var(--spacing) * 4);
    }

    .ps-99 {
      padding-inline-start: calc(var(--spacing) * 99);
    }

    .ps-\\[4px\\] {
      padding-inline-start: 4px;
    }

    .ps-big {
      padding-inline-start: var(--spacing-big);
    }"
  `)
  expect(await run(['ps', '-ps-4', '-ps-[4px]', 'ps-4/foo', 'ps-[4px]/foo'])).toEqual('')
})

test('pe', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['pe-1', 'pe-4', 'pe-99', 'pe-big', 'pe-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .pe-1 {
      padding-inline-end: calc(var(--spacing) * 1);
    }

    .pe-4 {
      padding-inline-end: calc(var(--spacing) * 4);
    }

    .pe-99 {
      padding-inline-end: calc(var(--spacing) * 99);
    }

    .pe-\\[4px\\] {
      padding-inline-end: 4px;
    }

    .pe-big {
      padding-inline-end: var(--spacing-big);
    }"
  `)
  expect(await run(['pe', '-pe-4', '-pe-[4px]', 'pe-4/foo', 'pe-[4px]/foo'])).toEqual('')
})

test('pr', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['pr-1', 'pr-4', 'pr-99', 'pr-big', 'pr-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .pr-1 {
      padding-right: calc(var(--spacing) * 1);
    }

    .pr-4 {
      padding-right: calc(var(--spacing) * 4);
    }

    .pr-99 {
      padding-right: calc(var(--spacing) * 99);
    }

    .pr-\\[4px\\] {
      padding-right: 4px;
    }

    .pr-big {
      padding-right: var(--spacing-big);
    }"
  `)
  expect(await run(['pr', '-pr-4', '-pr-[4px]', 'pr-4/foo', 'pr-[4px]/foo'])).toEqual('')
})

test('pb', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['pb-1', 'pb-4', 'pb-99', 'pb-big', 'pb-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .pb-1 {
      padding-bottom: calc(var(--spacing) * 1);
    }

    .pb-4 {
      padding-bottom: calc(var(--spacing) * 4);
    }

    .pb-99 {
      padding-bottom: calc(var(--spacing) * 99);
    }

    .pb-\\[4px\\] {
      padding-bottom: 4px;
    }

    .pb-big {
      padding-bottom: var(--spacing-big);
    }"
  `)
  expect(await run(['pb', '-pb-4', '-pb-[4px]', 'pb-4/foo', 'pb-[4px]/foo'])).toEqual('')
})

test('pl', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      ['pl-1', 'pl-4', 'pl-99', 'pl-big', 'pl-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .pl-1 {
      padding-left: calc(var(--spacing) * 1);
    }

    .pl-4 {
      padding-left: calc(var(--spacing) * 4);
    }

    .pl-99 {
      padding-left: calc(var(--spacing) * 99);
    }

    .pl-\\[4px\\] {
      padding-left: 4px;
    }

    .pl-big {
      padding-left: var(--spacing-big);
    }"
  `)
  expect(await run(['pl', '-pl-4', '-pl-[4px]', 'pl-4/foo', 'pl-[4px]/foo'])).toEqual('')
})

test('placeholder', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      [
        'placeholder-red-500',
        'placeholder-red-500/50',
        'placeholder-red-500/2.25',
        'placeholder-red-500/2.5',
        'placeholder-red-500/2.75',
        'placeholder-red-500/[0.5]',
        'placeholder-red-500/[50%]',
        'placeholder-current',
        'placeholder-current/50',
        'placeholder-current/[0.5]',
        'placeholder-current/[50%]',
        'placeholder-inherit',
        'placeholder-transparent',
        'placeholder-[#0088cc]',
        'placeholder-[#0088cc]/50',
        'placeholder-[#0088cc]/[0.5]',
        'placeholder-[#0088cc]/[50%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
    }

    .placeholder-\\[\\#0088cc\\]::placeholder {
      color: #08c;
    }

    .placeholder-\\[\\#0088cc\\]\\/50::placeholder, .placeholder-\\[\\#0088cc\\]\\/\\[0\\.5\\]::placeholder, .placeholder-\\[\\#0088cc\\]\\/\\[50\\%\\]::placeholder {
      color: oklab(59.9824% -.067 -.124 / .5);
    }

    .placeholder-current::placeholder, .placeholder-current\\/50::placeholder {
      color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-current\\/50::placeholder {
        color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .placeholder-current\\/\\[0\\.5\\]::placeholder {
      color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-current\\/\\[0\\.5\\]::placeholder {
        color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .placeholder-current\\/\\[50\\%\\]::placeholder {
      color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-current\\/\\[50\\%\\]::placeholder {
        color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .placeholder-inherit::placeholder {
      color: inherit;
    }

    .placeholder-red-500::placeholder {
      color: var(--color-red-500);
    }

    .placeholder-red-500\\/2\\.5::placeholder {
      color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-red-500\\/2\\.5::placeholder {
        color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .placeholder-red-500\\/2\\.25::placeholder {
      color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-red-500\\/2\\.25::placeholder {
        color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .placeholder-red-500\\/2\\.75::placeholder {
      color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-red-500\\/2\\.75::placeholder {
        color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .placeholder-red-500\\/50::placeholder {
      color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-red-500\\/50::placeholder {
        color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .placeholder-red-500\\/\\[0\\.5\\]::placeholder {
      color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-red-500\\/\\[0\\.5\\]::placeholder {
        color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .placeholder-red-500\\/\\[50\\%\\]::placeholder {
      color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .placeholder-red-500\\/\\[50\\%\\]::placeholder {
        color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .placeholder-transparent::placeholder {
      color: #0000;
    }"
  `)
  expect(
    await run([
      'placeholder',
      '-placeholder-red-500',
      '-placeholder-red-500/50',
      '-placeholder-red-500/[0.5]',
      '-placeholder-red-500/[50%]',
      '-placeholder-current',
      '-placeholder-current/50',
      '-placeholder-current/[0.5]',
      '-placeholder-current/[50%]',
      '-placeholder-inherit',
      '-placeholder-transparent',
      '-placeholder-[#0088cc]',
      '-placeholder-[#0088cc]/50',
      '-placeholder-[#0088cc]/[0.5]',
      '-placeholder-[#0088cc]/[50%]',
    ]),
  ).toEqual('')
})

