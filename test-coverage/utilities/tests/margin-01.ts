/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('margin', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['m-auto', 'm-4', 'm-[4px]', '-m-4', '-m-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-m-4 {
      margin: calc(var(--spacing-4) * -1);
    }

    .-m-\\[var\\(--value\\)\\] {
      margin: calc(var(--value) * -1);
    }

    .m-4 {
      margin: var(--spacing-4);
    }

    .m-\\[4px\\] {
      margin: 4px;
    }

    .m-auto {
      margin: auto;
    }"
  `)
  expect(
    await run(['m', 'm-auto/foo', 'm-4/foo', 'm-[4px]/foo', '-m-4/foo', '-m-[var(--value)]/foo']),
  ).toEqual('')
})

test('mx', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'mx-auto',
        'mx-1',
        'mx-4',
        'mx-99',
        'mx-big',
        'mx-[4px]',
        '-mx-4',
        '-mx-big',
        '-mx-[4px]',
        'mx-[var(--my-var)]',
        '-mx-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-mx-4 {
      margin-inline: calc(var(--spacing) * -4);
    }

    .-mx-\\[4px\\] {
      margin-inline: -4px;
    }

    .-mx-\\[var\\(--my-var\\)\\] {
      margin-inline: calc(var(--my-var) * -1);
    }

    .-mx-big {
      margin-inline: calc(var(--spacing-big) * -1);
    }

    .mx-1 {
      margin-inline: calc(var(--spacing) * 1);
    }

    .mx-4 {
      margin-inline: calc(var(--spacing) * 4);
    }

    .mx-99 {
      margin-inline: calc(var(--spacing) * 99);
    }

    .mx-\\[4px\\] {
      margin-inline: 4px;
    }

    .mx-\\[var\\(--my-var\\)\\] {
      margin-inline: var(--my-var);
    }

    .mx-auto {
      margin-inline: auto;
    }

    .mx-big {
      margin-inline: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'mx',
      'mx-auto/foo',
      'mx-4/foo',
      'mx-[4px]/foo',
      '-mx-4/foo',
      '-mx-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('my', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'my-1',
        'my-99',
        'my-2.5',
        'my-big',
        'my-[4px]',
        '-my-4',
        '-my-2.5',
        '-my-big',
        '-my-[4px]',
        'my-[var(--my-var)]',
        '-my-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-my-2\\.5 {
      margin-block: calc(var(--spacing) * -2.5);
    }

    .-my-4 {
      margin-block: calc(var(--spacing) * -4);
    }

    .-my-\\[4px\\] {
      margin-block: -4px;
    }

    .-my-\\[var\\(--my-var\\)\\] {
      margin-block: calc(var(--my-var) * -1);
    }

    .-my-big {
      margin-block: calc(var(--spacing-big) * -1);
    }

    .my-1 {
      margin-block: calc(var(--spacing) * 1);
    }

    .my-2\\.5 {
      margin-block: calc(var(--spacing) * 2.5);
    }

    .my-99 {
      margin-block: calc(var(--spacing) * 99);
    }

    .my-\\[4px\\] {
      margin-block: 4px;
    }

    .my-\\[var\\(--my-var\\)\\] {
      margin-block: var(--my-var);
    }

    .my-big {
      margin-block: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'my',
      'my-auto/foo',
      'my-4/foo',
      'my-[4px]/foo',
      '-my-4/foo',
      '-my-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('mt', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'mt-1',
        'mt-99',
        'mt-2.5',
        'mt-big',
        'mt-[4px]',
        '-mt-4',
        '-mt-2.5',
        '-mt-big',
        '-mt-[4px]',
        'mt-[var(--my-var)]',
        '-mt-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-mt-2\\.5 {
      margin-top: calc(var(--spacing) * -2.5);
    }

    .-mt-4 {
      margin-top: calc(var(--spacing) * -4);
    }

    .-mt-\\[4px\\] {
      margin-top: -4px;
    }

    .-mt-\\[var\\(--my-var\\)\\] {
      margin-top: calc(var(--my-var) * -1);
    }

    .-mt-big {
      margin-top: calc(var(--spacing-big) * -1);
    }

    .mt-1 {
      margin-top: calc(var(--spacing) * 1);
    }

    .mt-2\\.5 {
      margin-top: calc(var(--spacing) * 2.5);
    }

    .mt-99 {
      margin-top: calc(var(--spacing) * 99);
    }

    .mt-\\[4px\\] {
      margin-top: 4px;
    }

    .mt-\\[var\\(--my-var\\)\\] {
      margin-top: var(--my-var);
    }

    .mt-big {
      margin-top: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'mt',
      'mt-auto/foo',
      'mt-4/foo',
      'mt-[4px]/foo',
      '-mt-4/foo',
      '-mt-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('ms', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'ms-1',
        'ms-99',
        'ms-2.5',
        'ms-big',
        'ms-[4px]',
        '-ms-4',
        '-ms-2.5',
        '-ms-big',
        '-ms-[4px]',
        'ms-[var(--my-var)]',
        '-ms-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-ms-2\\.5 {
      margin-inline-start: calc(var(--spacing) * -2.5);
    }

    .-ms-4 {
      margin-inline-start: calc(var(--spacing) * -4);
    }

    .-ms-\\[4px\\] {
      margin-inline-start: -4px;
    }

    .-ms-\\[var\\(--my-var\\)\\] {
      margin-inline-start: calc(var(--my-var) * -1);
    }

    .-ms-big {
      margin-inline-start: calc(var(--spacing-big) * -1);
    }

    .ms-1 {
      margin-inline-start: calc(var(--spacing) * 1);
    }

    .ms-2\\.5 {
      margin-inline-start: calc(var(--spacing) * 2.5);
    }

    .ms-99 {
      margin-inline-start: calc(var(--spacing) * 99);
    }

    .ms-\\[4px\\] {
      margin-inline-start: 4px;
    }

    .ms-\\[var\\(--my-var\\)\\] {
      margin-inline-start: var(--my-var);
    }

    .ms-big {
      margin-inline-start: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'ms',
      'ms-auto/foo',
      'ms-4/foo',
      'ms-[4px]/foo',
      '-ms-4/foo',
      '-ms-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('me', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'me-1',
        'me-99',
        'me-2.5',
        'me-big',
        'me-[4px]',
        '-me-4',
        '-me-2.5',
        '-me-big',
        '-me-[4px]',
        'me-[var(--my-var)]',
        '-me-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-me-2\\.5 {
      margin-inline-end: calc(var(--spacing) * -2.5);
    }

    .-me-4 {
      margin-inline-end: calc(var(--spacing) * -4);
    }

    .-me-\\[4px\\] {
      margin-inline-end: -4px;
    }

    .-me-\\[var\\(--my-var\\)\\] {
      margin-inline-end: calc(var(--my-var) * -1);
    }

    .-me-big {
      margin-inline-end: calc(var(--spacing-big) * -1);
    }

    .me-1 {
      margin-inline-end: calc(var(--spacing) * 1);
    }

    .me-2\\.5 {
      margin-inline-end: calc(var(--spacing) * 2.5);
    }

    .me-99 {
      margin-inline-end: calc(var(--spacing) * 99);
    }

    .me-\\[4px\\] {
      margin-inline-end: 4px;
    }

    .me-\\[var\\(--my-var\\)\\] {
      margin-inline-end: var(--my-var);
    }

    .me-big {
      margin-inline-end: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'me',
      'me-auto/foo',
      'me-4/foo',
      'me-[4px]/foo',
      '-me-4/foo',
      '-me-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('mbs', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'mbs-1',
        'mbs-99',
        'mbs-2.5',
        'mbs-big',
        'mbs-[4px]',
        '-mbs-4',
        '-mbs-2.5',
        '-mbs-big',
        '-mbs-[4px]',
        'mbs-[var(--my-var)]',
        '-mbs-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-mbs-2\\.5 {
      margin-block-start: calc(var(--spacing) * -2.5);
    }

    .-mbs-4 {
      margin-block-start: calc(var(--spacing) * -4);
    }

    .-mbs-\\[4px\\] {
      margin-block-start: -4px;
    }

    .-mbs-\\[var\\(--my-var\\)\\] {
      margin-block-start: calc(var(--my-var) * -1);
    }

    .-mbs-big {
      margin-block-start: calc(var(--spacing-big) * -1);
    }

    .mbs-1 {
      margin-block-start: calc(var(--spacing) * 1);
    }

    .mbs-2\\.5 {
      margin-block-start: calc(var(--spacing) * 2.5);
    }

    .mbs-99 {
      margin-block-start: calc(var(--spacing) * 99);
    }

    .mbs-\\[4px\\] {
      margin-block-start: 4px;
    }

    .mbs-\\[var\\(--my-var\\)\\] {
      margin-block-start: var(--my-var);
    }

    .mbs-big {
      margin-block-start: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'mbs',
      'mbs-auto/foo',
      'mbs-4/foo',
      'mbs-[4px]/foo',
      '-mbs-4/foo',
      '-mbs-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('mbe', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'mbe-1',
        'mbe-99',
        'mbe-2.5',
        'mbe-big',
        'mbe-[4px]',
        '-mbe-4',
        '-mbe-2.5',
        '-mbe-big',
        '-mbe-[4px]',
        'mbe-[var(--my-var)]',
        '-mbe-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-mbe-2\\.5 {
      margin-block-end: calc(var(--spacing) * -2.5);
    }

    .-mbe-4 {
      margin-block-end: calc(var(--spacing) * -4);
    }

    .-mbe-\\[4px\\] {
      margin-block-end: -4px;
    }

    .-mbe-\\[var\\(--my-var\\)\\] {
      margin-block-end: calc(var(--my-var) * -1);
    }

    .-mbe-big {
      margin-block-end: calc(var(--spacing-big) * -1);
    }

    .mbe-1 {
      margin-block-end: calc(var(--spacing) * 1);
    }

    .mbe-2\\.5 {
      margin-block-end: calc(var(--spacing) * 2.5);
    }

    .mbe-99 {
      margin-block-end: calc(var(--spacing) * 99);
    }

    .mbe-\\[4px\\] {
      margin-block-end: 4px;
    }

    .mbe-\\[var\\(--my-var\\)\\] {
      margin-block-end: var(--my-var);
    }

    .mbe-big {
      margin-block-end: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'mbe',
      'mbe-auto/foo',
      'mbe-4/foo',
      'mbe-[4px]/foo',
      '-mbe-4/foo',
      '-mbe-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('mr', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'mr-1',
        'mr-99',
        'mr-2.5',
        'mr-big',
        'mr-[4px]',
        '-mr-4',
        '-mr-2.5',
        '-mr-big',
        '-mr-[4px]',
        'mr-[var(--my-var)]',
        '-mr-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-mr-2\\.5 {
      margin-right: calc(var(--spacing) * -2.5);
    }

    .-mr-4 {
      margin-right: calc(var(--spacing) * -4);
    }

    .-mr-\\[4px\\] {
      margin-right: -4px;
    }

    .-mr-\\[var\\(--my-var\\)\\] {
      margin-right: calc(var(--my-var) * -1);
    }

    .-mr-big {
      margin-right: calc(var(--spacing-big) * -1);
    }

    .mr-1 {
      margin-right: calc(var(--spacing) * 1);
    }

    .mr-2\\.5 {
      margin-right: calc(var(--spacing) * 2.5);
    }

    .mr-99 {
      margin-right: calc(var(--spacing) * 99);
    }

    .mr-\\[4px\\] {
      margin-right: 4px;
    }

    .mr-\\[var\\(--my-var\\)\\] {
      margin-right: var(--my-var);
    }

    .mr-big {
      margin-right: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'mr',
      'mr-auto/foo',
      'mr-4/foo',
      'mr-[4px]/foo',
      '-mr-4/foo',
      '-mr-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('mb', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'mb-1',
        'mb-99',
        'mb-2.5',
        'mb-big',
        'mb-[4px]',
        '-mb-4',
        '-mb-2.5',
        '-mb-big',
        '-mb-[4px]',
        'mb-[var(--my-var)]',
        '-mb-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-mb-2\\.5 {
      margin-bottom: calc(var(--spacing) * -2.5);
    }

    .-mb-4 {
      margin-bottom: calc(var(--spacing) * -4);
    }

    .-mb-\\[4px\\] {
      margin-bottom: -4px;
    }

    .-mb-\\[var\\(--my-var\\)\\] {
      margin-bottom: calc(var(--my-var) * -1);
    }

    .-mb-big {
      margin-bottom: calc(var(--spacing-big) * -1);
    }

    .mb-1 {
      margin-bottom: calc(var(--spacing) * 1);
    }

    .mb-2\\.5 {
      margin-bottom: calc(var(--spacing) * 2.5);
    }

    .mb-99 {
      margin-bottom: calc(var(--spacing) * 99);
    }

    .mb-\\[4px\\] {
      margin-bottom: 4px;
    }

    .mb-\\[var\\(--my-var\\)\\] {
      margin-bottom: var(--my-var);
    }

    .mb-big {
      margin-bottom: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'mb',
      'mb-auto/foo',
      'mb-4/foo',
      'mb-[4px]/foo',
      '-mb-4/foo',
      '-mb-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('ml', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --spacing-big: 100rem;
        }
        @tailwind utilities;
      `,
      [
        'ml-1',
        'ml-99',
        'ml-2.5',
        'ml-big',
        'ml-[4px]',
        '-ml-4',
        '-ml-2.5',
        '-ml-big',
        '-ml-[4px]',
        'ml-[var(--my-var)]',
        '-ml-[var(--my-var)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --spacing-big: 100rem;
    }

    .-ml-2\\.5 {
      margin-left: calc(var(--spacing) * -2.5);
    }

    .-ml-4 {
      margin-left: calc(var(--spacing) * -4);
    }

    .-ml-\\[4px\\] {
      margin-left: -4px;
    }

    .-ml-\\[var\\(--my-var\\)\\] {
      margin-left: calc(var(--my-var) * -1);
    }

    .-ml-big {
      margin-left: calc(var(--spacing-big) * -1);
    }

    .ml-1 {
      margin-left: calc(var(--spacing) * 1);
    }

    .ml-2\\.5 {
      margin-left: calc(var(--spacing) * 2.5);
    }

    .ml-99 {
      margin-left: calc(var(--spacing) * 99);
    }

    .ml-\\[4px\\] {
      margin-left: 4px;
    }

    .ml-\\[var\\(--my-var\\)\\] {
      margin-left: var(--my-var);
    }

    .ml-big {
      margin-left: var(--spacing-big);
    }"
  `)
  expect(
    await run([
      'ml',
      'ml-auto/foo',
      'ml-4/foo',
      'ml-[4px]/foo',
      '-ml-4/foo',
      '-ml-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('margin sort order', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['mb-4', 'me-4', 'mx-4', 'ml-4', 'ms-4', 'm-4', 'mr-4', 'mt-4', 'my-4'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .m-4 {
      margin: var(--spacing-4);
    }

    .mx-4 {
      margin-inline: var(--spacing-4);
    }

    .my-4 {
      margin-block: var(--spacing-4);
    }

    .ms-4 {
      margin-inline-start: var(--spacing-4);
    }

    .me-4 {
      margin-inline-end: var(--spacing-4);
    }

    .mt-4 {
      margin-top: var(--spacing-4);
    }

    .mr-4 {
      margin-right: var(--spacing-4);
    }

    .mb-4 {
      margin-bottom: var(--spacing-4);
    }

    .ml-4 {
      margin-left: var(--spacing-4);
    }"
  `)
  expect(
    await run([
      'm',
      'mb-4/foo',
      'me-4/foo',
      'mx-4/foo',
      'ml-4/foo',
      'ms-4/foo',
      'm-4/foo',
      'mr-4/foo',
      'mt-4/foo',
      'my-4/foo',
    ]),
  ).toEqual('')
})

