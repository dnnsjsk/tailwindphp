/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('outline', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --outline-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        'outline',
        'outline-hidden',

        // outline-style
        'outline-none',
        'outline-solid',
        'outline-dashed',
        'outline-dotted',
        'outline-double',

        // outline-color
        'outline-red-500',
        'outline-red-500/50',
        'outline-red-500/[0.5]',
        'outline-red-500/[50%]',
        'outline-blue-500',
        'outline-current',
        'outline-current/50',
        'outline-current/[0.5]',
        'outline-current/[50%]',
        'outline-inherit',
        'outline-transparent',
        'outline-[#0088cc]',
        'outline-[#0088cc]/50',
        'outline-[#0088cc]/[0.5]',
        'outline-[#0088cc]/[50%]',
        'outline-[black]',
        'outline-[black]/50',
        'outline-[black]/[0.5]',
        'outline-[black]/[50%]',
        'outline-[var(--value)]',
        'outline-[var(--value)]/50',
        'outline-[var(--value)]/[0.5]',
        'outline-[var(--value)]/[50%]',
        'outline-[color:var(--value)]',
        'outline-[color:var(--value)]/50',
        'outline-[color:var(--value)]/[0.5]',
        'outline-[color:var(--value)]/[50%]',

        // outline-width
        'outline-0',
        'outline-[1.5]',
        'outline-[12px]',
        'outline-[50%]',
        'outline-[number:var(--my-width)]',
        'outline-[length:var(--my-width)]',
        'outline-[percentage:var(--my-width)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-outline-style: solid;
        }
      }
    }

    :root, :host {
      --color-red-500: #ef4444;
      --outline-color-blue-500: #3b82f6;
    }

    .outline-hidden {
      --tw-outline-style: none;
      outline-style: none;
    }

    @media (forced-colors: active) {
      .outline-hidden {
        outline-offset: 2px;
        outline: 2px solid #0000;
      }
    }

    .outline {
      outline-style: var(--tw-outline-style);
      outline-width: 1px;
    }

    .outline-0 {
      outline-style: var(--tw-outline-style);
      outline-width: 0;
    }

    .outline-\\[1\\.5\\] {
      outline-style: var(--tw-outline-style);
      outline-width: 1.5px;
    }

    .outline-\\[12px\\] {
      outline-style: var(--tw-outline-style);
      outline-width: 12px;
    }

    .outline-\\[50\\%\\] {
      outline-style: var(--tw-outline-style);
      outline-width: 50%;
    }

    .outline-\\[length\\:var\\(--my-width\\)\\], .outline-\\[number\\:var\\(--my-width\\)\\], .outline-\\[percentage\\:var\\(--my-width\\)\\] {
      outline-style: var(--tw-outline-style);
      outline-width: var(--my-width);
    }

    .outline-\\[\\#0088cc\\] {
      outline-color: #08c;
    }

    .outline-\\[\\#0088cc\\]\\/50, .outline-\\[\\#0088cc\\]\\/\\[0\\.5\\], .outline-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      outline-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .outline-\\[black\\] {
      outline-color: #000;
    }

    .outline-\\[black\\]\\/50 {
      outline-color: oklab(0% none none / .5);
    }

    .outline-\\[black\\]\\/\\[0\\.5\\] {
      outline-color: oklab(0% none none / .5);
    }

    .outline-\\[black\\]\\/\\[50\\%\\] {
      outline-color: oklab(0% none none / .5);
    }

    .outline-\\[color\\:var\\(--value\\)\\], .outline-\\[color\\:var\\(--value\\)\\]\\/50 {
      outline-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-\\[color\\:var\\(--value\\)\\]\\/50 {
        outline-color: color-mix(in oklab, var(--value) 50%, transparent);
      }
    }

    .outline-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
      outline-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
        outline-color: color-mix(in oklab, var(--value) 50%, transparent);
      }
    }

    .outline-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
      outline-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
        outline-color: color-mix(in oklab, var(--value) 50%, transparent);
      }
    }

    .outline-\\[var\\(--value\\)\\], .outline-\\[var\\(--value\\)\\]\\/50 {
      outline-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-\\[var\\(--value\\)\\]\\/50 {
        outline-color: color-mix(in oklab, var(--value) 50%, transparent);
      }
    }

    .outline-\\[var\\(--value\\)\\]\\/\\[0\\.5\\] {
      outline-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-\\[var\\(--value\\)\\]\\/\\[0\\.5\\] {
        outline-color: color-mix(in oklab, var(--value) 50%, transparent);
      }
    }

    .outline-\\[var\\(--value\\)\\]\\/\\[50\\%\\] {
      outline-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-\\[var\\(--value\\)\\]\\/\\[50\\%\\] {
        outline-color: color-mix(in oklab, var(--value) 50%, transparent);
      }
    }

    .outline-blue-500 {
      outline-color: var(--outline-color-blue-500);
    }

    .outline-current, .outline-current\\/50 {
      outline-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-current\\/50 {
        outline-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .outline-current\\/\\[0\\.5\\] {
      outline-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-current\\/\\[0\\.5\\] {
        outline-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .outline-current\\/\\[50\\%\\] {
      outline-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-current\\/\\[50\\%\\] {
        outline-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .outline-inherit {
      outline-color: inherit;
    }

    .outline-red-500 {
      outline-color: var(--color-red-500);
    }

    .outline-red-500\\/50 {
      outline-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-red-500\\/50 {
        outline-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .outline-red-500\\/\\[0\\.5\\] {
      outline-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-red-500\\/\\[0\\.5\\] {
        outline-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .outline-red-500\\/\\[50\\%\\] {
      outline-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .outline-red-500\\/\\[50\\%\\] {
        outline-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .outline-transparent {
      outline-color: #0000;
    }

    .outline-dashed {
      --tw-outline-style: dashed;
      outline-style: dashed;
    }

    .outline-dotted {
      --tw-outline-style: dotted;
      outline-style: dotted;
    }

    .outline-double {
      --tw-outline-style: double;
      outline-style: double;
    }

    .outline-none {
      --tw-outline-style: none;
      outline-style: none;
    }

    .outline-solid {
      --tw-outline-style: solid;
      outline-style: solid;
    }

    @property --tw-outline-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --default-outline-width: 2px;
        }
        @tailwind utilities;
      `,
      ['outline'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-outline-style: solid;
        }
      }
    }

    .outline {
      outline-style: var(--tw-outline-style);
      outline-width: 2px;
    }

    @property --tw-outline-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(
    await run([
      '-outline',

      // outline-style
      '-outline-none',
      '-outline-dashed',
      '-outline-dotted',
      '-outline-double',

      // outline-color
      '-outline-red-500',
      '-outline-red-500/50',
      '-outline-red-500/[0.5]',
      '-outline-red-500/[50%]',
      '-outline-current',
      '-outline-current/50',
      '-outline-current/[0.5]',
      '-outline-current/[50%]',
      '-outline-inherit',
      '-outline-transparent',
      '-outline-[#0088cc]',
      '-outline-[#0088cc]/50',
      '-outline-[#0088cc]/[0.5]',
      '-outline-[#0088cc]/[50%]',
      '-outline-[black]',

      // outline-width
      '-outline-0',
      'outline--10',

      'outline/foo',
      'outline-none/foo',
      'outline-solid/foo',
      'outline-dashed/foo',
      'outline-dotted/foo',
      'outline-double/foo',
    ]),
  ).toEqual('')
})

test('outline-offset', async () => {
  expect(
    await run([
      'outline-offset-4',
      '-outline-offset-4',
      'outline-offset-[var(--value)]',
      '-outline-offset-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".-outline-offset-4 {
      outline-offset: calc(4px * -1);
    }

    .-outline-offset-\\[var\\(--value\\)\\] {
      outline-offset: calc(var(--value) * -1);
    }

    .outline-offset-4 {
      outline-offset: 4px;
    }

    .outline-offset-\\[var\\(--value\\)\\] {
      outline-offset: var(--value);
    }"
  `)
  expect(
    await run([
      'outline-offset',
      'outline-offset--4',
      'outline-offset-unknown',
      'outline-offset-4/foo',
      '-outline-offset-4/foo',
      'outline-offset-[var(--value)]/foo',
      '-outline-offset-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

