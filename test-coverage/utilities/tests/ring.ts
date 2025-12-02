/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('ring', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --ring-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        // ring color
        'ring-inset',
        'ring-red-500',
        'ring-red-500/50',
        'ring-red-500/2.25',
        'ring-red-500/2.5',
        'ring-red-500/2.75',
        'ring-red-500/[0.5]',
        'ring-red-500/[50%]',
        'ring-blue-500',
        'ring-current',
        'ring-current/50',
        'ring-current/[0.5]',
        'ring-current/[50%]',
        'ring-inherit',
        'ring-transparent',
        'ring-[#0088cc]',
        'ring-[#0088cc]/50',
        'ring-[#0088cc]/[0.5]',
        'ring-[#0088cc]/[50%]',
        'ring-[var(--my-color)]',
        'ring-[var(--my-color)]/50',
        'ring-[var(--my-color)]/[0.5]',
        'ring-[var(--my-color)]/[50%]',
        'ring-[color:var(--my-color)]',
        'ring-[color:var(--my-color)]/50',
        'ring-[color:var(--my-color)]/[0.5]',
        'ring-[color:var(--my-color)]/[50%]',

        // ring width
        'ring',
        'ring-0',
        'ring-1',
        'ring-2',
        'ring-4',
        'ring-[12px]',
        'ring-[length:var(--my-width)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-shadow: 0 0 #0000;
          --tw-shadow-color: initial;
          --tw-shadow-alpha: 100%;
          --tw-inset-shadow: 0 0 #0000;
          --tw-inset-shadow-color: initial;
          --tw-inset-shadow-alpha: 100%;
          --tw-ring-color: initial;
          --tw-ring-shadow: 0 0 #0000;
          --tw-inset-ring-color: initial;
          --tw-inset-ring-shadow: 0 0 #0000;
          --tw-ring-inset: initial;
          --tw-ring-offset-width: 0px;
          --tw-ring-offset-color: #fff;
          --tw-ring-offset-shadow: 0 0 #0000;
        }
      }
    }

    :root, :host {
      --color-red-500: #ef4444;
      --ring-color-blue-500: #3b82f6;
    }

    .ring {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-0 {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(0px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-1 {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-2 {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-4 {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(4px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-\\[12px\\] {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(12px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-\\[length\\:var\\(--my-width\\)\\] {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(var(--my-width)  + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .ring-\\[\\#0088cc\\] {
      --tw-ring-color: #08c;
    }

    .ring-\\[\\#0088cc\\]\\/50, .ring-\\[\\#0088cc\\]\\/\\[0\\.5\\], .ring-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-ring-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .ring-\\[color\\:var\\(--my-color\\)\\], .ring-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      --tw-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        --tw-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-\\[var\\(--my-color\\)\\], .ring-\\[var\\(--my-color\\)\\]\\/50 {
      --tw-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-\\[var\\(--my-color\\)\\]\\/50 {
        --tw-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-blue-500 {
      --tw-ring-color: var(--ring-color-blue-500);
    }

    .ring-current, .ring-current\\/50 {
      --tw-ring-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-current\\/50 {
        --tw-ring-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .ring-current\\/\\[0\\.5\\] {
      --tw-ring-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-current\\/\\[0\\.5\\] {
        --tw-ring-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .ring-current\\/\\[50\\%\\] {
      --tw-ring-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-current\\/\\[50\\%\\] {
        --tw-ring-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .ring-inherit {
      --tw-ring-color: inherit;
    }

    .ring-red-500 {
      --tw-ring-color: var(--color-red-500);
    }

    .ring-red-500\\/2\\.5 {
      --tw-ring-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-red-500\\/2\\.5 {
        --tw-ring-color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .ring-red-500\\/2\\.25 {
      --tw-ring-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-red-500\\/2\\.25 {
        --tw-ring-color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .ring-red-500\\/2\\.75 {
      --tw-ring-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-red-500\\/2\\.75 {
        --tw-ring-color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .ring-red-500\\/50 {
      --tw-ring-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-red-500\\/50 {
        --tw-ring-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .ring-red-500\\/\\[0\\.5\\] {
      --tw-ring-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-red-500\\/\\[0\\.5\\] {
        --tw-ring-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .ring-red-500\\/\\[50\\%\\] {
      --tw-ring-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-red-500\\/\\[50\\%\\] {
        --tw-ring-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .ring-transparent {
      --tw-ring-color: transparent;
    }

    .ring-inset {
      --tw-ring-inset: inset;
    }

    @property --tw-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-inset-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-inset-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-inset-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-ring-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-ring-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-inset-ring-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-inset-ring-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-ring-inset {
      syntax: "*";
      inherits: false
    }

    @property --tw-ring-offset-width {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-ring-offset-color {
      syntax: "*";
      inherits: false;
      initial-value: #fff;
    }

    @property --tw-ring-offset-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --default-ring-width: 2px;
        }
        @tailwind utilities;
      `,
      ['ring'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-shadow: 0 0 #0000;
          --tw-shadow-color: initial;
          --tw-shadow-alpha: 100%;
          --tw-inset-shadow: 0 0 #0000;
          --tw-inset-shadow-color: initial;
          --tw-inset-shadow-alpha: 100%;
          --tw-ring-color: initial;
          --tw-ring-shadow: 0 0 #0000;
          --tw-inset-ring-color: initial;
          --tw-inset-ring-shadow: 0 0 #0000;
          --tw-ring-inset: initial;
          --tw-ring-offset-width: 0px;
          --tw-ring-offset-color: #fff;
          --tw-ring-offset-shadow: 0 0 #0000;
        }
      }
    }

    .ring {
      --tw-ring-shadow: var(--tw-ring-inset, ) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    @property --tw-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-inset-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-inset-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-inset-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-ring-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-ring-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-inset-ring-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-inset-ring-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }

    @property --tw-ring-inset {
      syntax: "*";
      inherits: false
    }

    @property --tw-ring-offset-width {
      syntax: "<length>";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-ring-offset-color {
      syntax: "*";
      inherits: false;
      initial-value: #fff;
    }

    @property --tw-ring-offset-shadow {
      syntax: "*";
      inherits: false;
      initial-value: 0 0 #0000;
    }"
  `)
  expect(
    await run([
      // ring color
      '-ring-inset',
      '-ring-red-500',
      '-ring-red-500/50',
      '-ring-red-500/[0.5]',
      '-ring-red-500/[50%]',
      '-ring-current',
      '-ring-current/50',
      '-ring-current/[0.5]',
      '-ring-current/[50%]',
      '-ring-inherit',
      '-ring-transparent',
      '-ring-[#0088cc]',
      '-ring-[#0088cc]/50',
      '-ring-[#0088cc]/[0.5]',
      '-ring-[#0088cc]/[50%]',

      // ring width
      '-ring',
      'ring--1',
      '-ring-0',
      '-ring-1',
      '-ring-2',
      '-ring-4',

      'ring/foo',
      'ring-0/foo',
      'ring-1/foo',
      'ring-2/foo',
      'ring-4/foo',
      'ring-[12px]/foo',
      'ring-[length:var(--my-width)]/foo',
    ]),
  ).toEqual('')
})

test('ring-offset', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --ring-offset-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        // ring color
        'ring-offset-inset',
        'ring-offset-red-500',
        'ring-offset-red-500/50',
        'ring-offset-red-500/[0.5]',
        'ring-offset-red-500/[50%]',
        'ring-offset-blue-500',
        'ring-offset-current',
        'ring-offset-current/50',
        'ring-offset-current/[0.5]',
        'ring-offset-current/[50%]',
        'ring-offset-inherit',
        'ring-offset-transparent',
        'ring-offset-[#0088cc]',
        'ring-offset-[#0088cc]/50',
        'ring-offset-[#0088cc]/[0.5]',
        'ring-offset-[#0088cc]/[50%]',

        'ring-offset-[var(--my-color)]',
        'ring-offset-[var(--my-color)]/50',
        'ring-offset-[var(--my-color)]/[0.5]',
        'ring-offset-[var(--my-color)]/[50%]',
        'ring-offset-[color:var(--my-color)]',
        'ring-offset-[color:var(--my-color)]/50',
        'ring-offset-[color:var(--my-color)]/[0.5]',
        'ring-offset-[color:var(--my-color)]/[50%]',

        // ring width
        'ring-offset-0',
        'ring-offset-1',
        'ring-offset-2',
        'ring-offset-4',
        'ring-offset-[12px]',
        'ring-offset-[length:var(--my-width)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --ring-offset-color-blue-500: #3b82f6;
    }

    .ring-offset-0 {
      --tw-ring-offset-width: 0px;
      --tw-ring-offset-shadow: var(--tw-ring-inset, ) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    }

    .ring-offset-1 {
      --tw-ring-offset-width: 1px;
      --tw-ring-offset-shadow: var(--tw-ring-inset, ) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    }

    .ring-offset-2 {
      --tw-ring-offset-width: 2px;
      --tw-ring-offset-shadow: var(--tw-ring-inset, ) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    }

    .ring-offset-4 {
      --tw-ring-offset-width: 4px;
      --tw-ring-offset-shadow: var(--tw-ring-inset, ) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    }

    .ring-offset-\\[12px\\] {
      --tw-ring-offset-width: 12px;
      --tw-ring-offset-shadow: var(--tw-ring-inset, ) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    }

    .ring-offset-\\[length\\:var\\(--my-width\\)\\] {
      --tw-ring-offset-width: var(--my-width);
      --tw-ring-offset-shadow: var(--tw-ring-inset, ) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);
    }

    .ring-offset-\\[\\#0088cc\\] {
      --tw-ring-offset-color: #08c;
    }

    .ring-offset-\\[\\#0088cc\\]\\/50, .ring-offset-\\[\\#0088cc\\]\\/\\[0\\.5\\], .ring-offset-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-ring-offset-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .ring-offset-\\[color\\:var\\(--my-color\\)\\], .ring-offset-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      --tw-ring-offset-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        --tw-ring-offset-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-offset-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-ring-offset-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-ring-offset-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-offset-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-ring-offset-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-ring-offset-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-offset-\\[var\\(--my-color\\)\\], .ring-offset-\\[var\\(--my-color\\)\\]\\/50 {
      --tw-ring-offset-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-\\[var\\(--my-color\\)\\]\\/50 {
        --tw-ring-offset-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-offset-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-ring-offset-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-ring-offset-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-offset-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-ring-offset-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-ring-offset-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .ring-offset-blue-500 {
      --tw-ring-offset-color: var(--ring-offset-color-blue-500);
    }

    .ring-offset-current, .ring-offset-current\\/50 {
      --tw-ring-offset-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-current\\/50 {
        --tw-ring-offset-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .ring-offset-current\\/\\[0\\.5\\] {
      --tw-ring-offset-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-current\\/\\[0\\.5\\] {
        --tw-ring-offset-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .ring-offset-current\\/\\[50\\%\\] {
      --tw-ring-offset-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-current\\/\\[50\\%\\] {
        --tw-ring-offset-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .ring-offset-inherit {
      --tw-ring-offset-color: inherit;
    }

    .ring-offset-red-500 {
      --tw-ring-offset-color: var(--color-red-500);
    }

    .ring-offset-red-500\\/50 {
      --tw-ring-offset-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-red-500\\/50 {
        --tw-ring-offset-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .ring-offset-red-500\\/\\[0\\.5\\] {
      --tw-ring-offset-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-red-500\\/\\[0\\.5\\] {
        --tw-ring-offset-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .ring-offset-red-500\\/\\[50\\%\\] {
      --tw-ring-offset-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .ring-offset-red-500\\/\\[50\\%\\] {
        --tw-ring-offset-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .ring-offset-transparent {
      --tw-ring-offset-color: transparent;
    }"
  `)
  expect(
    await run([
      'ring-offset',
      // ring color
      '-ring-offset-inset',
      '-ring-offset-red-500',
      '-ring-offset-red-500/50',
      '-ring-offset-red-500/[0.5]',
      '-ring-offset-red-500/[50%]',
      '-ring-offset-current',
      '-ring-offset-current/50',
      '-ring-offset-current/[0.5]',
      '-ring-offset-current/[50%]',
      '-ring-offset-inherit',
      '-ring-offset-transparent',
      '-ring-offset-[#0088cc]',
      '-ring-offset-[#0088cc]/50',
      '-ring-offset-[#0088cc]/[0.5]',
      '-ring-offset-[#0088cc]/[50%]',

      // ring width
      'ring-offset--1',
      '-ring-offset-0',
      '-ring-offset-1',
      '-ring-offset-2',
      '-ring-offset-4',

      'ring-offset-0/foo',
      'ring-offset-1/foo',
      'ring-offset-2/foo',
      'ring-offset-4/foo',
      'ring-offset-[12px]/foo',
      'ring-offset-[length:var(--my-width)]/foo',
    ]),
  ).toEqual('')
})

