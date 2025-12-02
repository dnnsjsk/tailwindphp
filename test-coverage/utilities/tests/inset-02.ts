/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('inset-shadow', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --inset-shadow: inset 0 2px 4px rgb(0 0 0 / 0.05);
          --inset-shadow-sm: inset 0 1px 1px rgb(0 0 0 / 0.05);
        }
        @tailwind utilities;
      `,
      [
        // Shadows
        'inset-shadow',
        'inset-shadow-sm',
        'inset-shadow-none',
        'inset-shadow-[12px_12px_#0088cc]',
        'inset-shadow-[12px_12px_var(--value)]',
        'inset-shadow-[10px_10px]',
        'inset-shadow-[var(--value)]',
        'inset-shadow-[shadow:var(--value)]',
        'inset-shadow-[12px_12px_#0088cc,12px_12px_var(--value,#0088cc)]',

        'inset-shadow-sm/25',
        'inset-shadow-[12px_12px_#0088cc]/25',
        'inset-shadow-[12px_12px_var(--value)]/25',
        'inset-shadow-[10px_10px]/25',
        'inset-shadow-[12px_12px_#0088cc,12px_12px_var(--value,#0088cc)]/25',

        // Colors
        'inset-shadow-red-500',
        'inset-shadow-red-500/50',
        'inset-shadow-red-500/2.25',
        'inset-shadow-red-500/2.5',
        'inset-shadow-red-500/2.75',
        'inset-shadow-red-500/[0.5]',
        'inset-shadow-red-500/[50%]',
        'inset-shadow-current',
        'inset-shadow-current/50',
        'inset-shadow-current/[0.5]',
        'inset-shadow-current/[50%]',
        'inset-shadow-inherit',
        'inset-shadow-transparent',
        'inset-shadow-[#0088cc]',
        'inset-shadow-[#0088cc]/50',
        'inset-shadow-[#0088cc]/[0.5]',
        'inset-shadow-[#0088cc]/[50%]',
        'inset-shadow-[color:var(--value)]',
        'inset-shadow-[color:var(--value)]/50',
        'inset-shadow-[color:var(--value)]/[0.5]',
        'inset-shadow-[color:var(--value)]/[50%]',
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
    }

    .inset-shadow-\\[12px_12px_\\#0088cc\\,12px_12px_var\\(--value\\,\\#0088cc\\)\\]\\/25 {
      --tw-inset-shadow-alpha: 25%;
      --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, #08c), inset 12px 12px var(--tw-inset-shadow-color, var(--value, #08c));
    }

    @supports (color: lab(from red l a b)) {
      .inset-shadow-\\[12px_12px_\\#0088cc\\,12px_12px_var\\(--value\\,\\#0088cc\\)\\]\\/25 {
        --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, oklab(59.9824% -.067 -.124 / .25)), inset 12px 12px var(--tw-inset-shadow-color, oklab(from var(--value, #08c) l a b / 25%));
      }
    }

    .inset-shadow-\\[12px_12px_\\#0088cc\\,12px_12px_var\\(--value\\,\\#0088cc\\)\\]\\/25 {
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
      --tw-inset-shadow-alpha: 25%;
      --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, var(--value));
    }

    @supports (color: lab(from red l a b)) {
      .inset-shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
        --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, oklab(from var(--value) l a b / 25%));
      }
    }

    .inset-shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[10px_10px\\]\\/25 {
      --tw-inset-shadow-alpha: 25%;
      --tw-inset-shadow: inset 10px 10px var(--tw-inset-shadow-color, currentcolor);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[10px_10px\\]\\/25 {
        --tw-inset-shadow: inset 10px 10px var(--tw-inset-shadow-color, color-mix(in oklab, currentcolor 25%, transparent));
      }
    }

    .inset-shadow-\\[10px_10px\\]\\/25 {
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[12px_12px_\\#0088cc\\]\\/25 {
      --tw-inset-shadow-alpha: 25%;
      --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, oklab(59.9824% -.067 -.124 / .25));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-sm\\/25 {
      --tw-inset-shadow-alpha: 25%;
      --tw-inset-shadow: inset 0 1px 1px var(--tw-inset-shadow-color, oklab(0% 0 0 / .25));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow {
      --tw-inset-shadow: inset 0 2px 4px var(--tw-inset-shadow-color, #0000000d);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[10px_10px\\] {
      --tw-inset-shadow: inset 10px 10px var(--tw-inset-shadow-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[12px_12px_\\#0088cc\\,12px_12px_var\\(--value\\,\\#0088cc\\)\\] {
      --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, #08c), inset 12px 12px var(--tw-inset-shadow-color, var(--value, #08c));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[12px_12px_\\#0088cc\\] {
      --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, #08c);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[12px_12px_var\\(--value\\)\\] {
      --tw-inset-shadow: inset 12px 12px var(--tw-inset-shadow-color, var(--value));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[shadow\\:var\\(--value\\)\\], .inset-shadow-\\[var\\(--value\\)\\] {
      --tw-inset-shadow: inset var(--value);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-none {
      --tw-inset-shadow: 0 0 #0000;
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-sm {
      --tw-inset-shadow: inset 0 1px 1px var(--tw-inset-shadow-color, #0000000d);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-shadow-\\[\\#0088cc\\] {
      --tw-inset-shadow-color: #08c;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[\\#0088cc\\] {
        --tw-inset-shadow-color: color-mix(in oklab, #08c var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[\\#0088cc\\]\\/50 {
      --tw-inset-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[\\#0088cc\\]\\/50 {
        --tw-inset-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[\\#0088cc\\]\\/\\[0\\.5\\] {
      --tw-inset-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[\\#0088cc\\]\\/\\[0\\.5\\] {
        --tw-inset-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-inset-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[\\#0088cc\\]\\/\\[50\\%\\] {
        --tw-inset-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[color\\:var\\(--value\\)\\] {
      --tw-inset-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[color\\:var\\(--value\\)\\] {
        --tw-inset-shadow-color: color-mix(in oklab, var(--value) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[color\\:var\\(--value\\)\\]\\/50 {
      --tw-inset-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[color\\:var\\(--value\\)\\]\\/50 {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
      --tw-inset-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
      --tw-inset-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-current {
      --tw-inset-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-current {
        --tw-inset-shadow-color: color-mix(in oklab, currentcolor var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-current\\/50 {
      --tw-inset-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-current\\/50 {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-current\\/\\[0\\.5\\] {
      --tw-inset-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-current\\/\\[0\\.5\\] {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-current\\/\\[50\\%\\] {
      --tw-inset-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-current\\/\\[50\\%\\] {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-inherit {
      --tw-inset-shadow-color: inherit;
    }

    .inset-shadow-red-500 {
      --tw-inset-shadow-color: #ef4444;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500 {
        --tw-inset-shadow-color: color-mix(in oklab, var(--color-red-500) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-red-500\\/2\\.5 {
      --tw-inset-shadow-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500\\/2\\.5 {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.5%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-red-500\\/2\\.25 {
      --tw-inset-shadow-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500\\/2\\.25 {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.25%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-red-500\\/2\\.75 {
      --tw-inset-shadow-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500\\/2\\.75 {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.75%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-red-500\\/50 {
      --tw-inset-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500\\/50 {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-red-500\\/\\[0\\.5\\] {
      --tw-inset-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500\\/\\[0\\.5\\] {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-red-500\\/\\[50\\%\\] {
      --tw-inset-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-red-500\\/\\[50\\%\\] {
        --tw-inset-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-inset-shadow-alpha), transparent);
      }
    }

    .inset-shadow-transparent {
      --tw-inset-shadow-color: transparent;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-shadow-transparent {
        --tw-inset-shadow-color: color-mix(in oklab, transparent var(--tw-inset-shadow-alpha), transparent);
      }
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
      '-inset-shadow-sm',
      '-inset-shadow-none',
      '-inset-shadow-red-500',
      '-inset-shadow-red-500/50',
      '-inset-shadow-red-500/[0.5]',
      '-inset-shadow-red-500/[50%]',
      '-inset-shadow-current',
      '-inset-shadow-current/50',
      '-inset-shadow-current/[0.5]',
      '-inset-shadow-current/[50%]',
      '-inset-shadow-inherit',
      '-inset-shadow-transparent',
      '-inset-shadow-[#0088cc]',
      '-inset-shadow-[#0088cc]/50',
      '-inset-shadow-[#0088cc]/[0.5]',
      '-inset-shadow-[#0088cc]/[50%]',
      '-inset-shadow-[var(--value)]',
    ]),
  ).toEqual('')
})

test('inset-ring', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      [
        // ring color
        'inset-ring-red-500',
        'inset-ring-red-500/50',
        'inset-ring-red-500/2.25',
        'inset-ring-red-500/2.5',
        'inset-ring-red-500/2.75',
        'inset-ring-red-500/[0.5]',
        'inset-ring-red-500/[50%]',
        'inset-ring-current',
        'inset-ring-current/50',
        'inset-ring-current/[0.5]',
        'inset-ring-current/[50%]',
        'inset-ring-inherit',
        'inset-ring-transparent',
        'inset-ring-[#0088cc]',
        'inset-ring-[#0088cc]/50',
        'inset-ring-[#0088cc]/[0.5]',
        'inset-ring-[#0088cc]/[50%]',
        'inset-ring-[var(--my-color)]',
        'inset-ring-[var(--my-color)]/50',
        'inset-ring-[var(--my-color)]/[0.5]',
        'inset-ring-[var(--my-color)]/[50%]',
        'inset-ring-[color:var(--my-color)]',
        'inset-ring-[color:var(--my-color)]/50',
        'inset-ring-[color:var(--my-color)]/[0.5]',
        'inset-ring-[color:var(--my-color)]/[50%]',

        // ring width
        'inset-ring',
        'inset-ring-0',
        'inset-ring-1',
        'inset-ring-2',
        'inset-ring-4',
        'inset-ring-[12px]',
        'inset-ring-[length:var(--my-width)]',
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
    }

    .inset-ring {
      --tw-inset-ring-shadow: inset 0 0 0 1px var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-0 {
      --tw-inset-ring-shadow: inset 0 0 0 0px var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-1 {
      --tw-inset-ring-shadow: inset 0 0 0 1px var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-2 {
      --tw-inset-ring-shadow: inset 0 0 0 2px var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-4 {
      --tw-inset-ring-shadow: inset 0 0 0 4px var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-\\[12px\\] {
      --tw-inset-ring-shadow: inset 0 0 0 12px var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-\\[length\\:var\\(--my-width\\)\\] {
      --tw-inset-ring-shadow: inset 0 0 0 var(--my-width) var(--tw-inset-ring-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .inset-ring-\\[\\#0088cc\\] {
      --tw-inset-ring-color: #08c;
    }

    .inset-ring-\\[\\#0088cc\\]\\/50, .inset-ring-\\[\\#0088cc\\]\\/\\[0\\.5\\], .inset-ring-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-inset-ring-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .inset-ring-\\[color\\:var\\(--my-color\\)\\], .inset-ring-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      --tw-inset-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        --tw-inset-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .inset-ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-inset-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-inset-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .inset-ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-inset-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-inset-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .inset-ring-\\[var\\(--my-color\\)\\], .inset-ring-\\[var\\(--my-color\\)\\]\\/50 {
      --tw-inset-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-\\[var\\(--my-color\\)\\]\\/50 {
        --tw-inset-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .inset-ring-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-inset-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-inset-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .inset-ring-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-inset-ring-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-inset-ring-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .inset-ring-current, .inset-ring-current\\/50 {
      --tw-inset-ring-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-current\\/50 {
        --tw-inset-ring-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .inset-ring-current\\/\\[0\\.5\\] {
      --tw-inset-ring-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-current\\/\\[0\\.5\\] {
        --tw-inset-ring-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .inset-ring-current\\/\\[50\\%\\] {
      --tw-inset-ring-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-current\\/\\[50\\%\\] {
        --tw-inset-ring-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .inset-ring-inherit {
      --tw-inset-ring-color: inherit;
    }

    .inset-ring-red-500 {
      --tw-inset-ring-color: var(--color-red-500);
    }

    .inset-ring-red-500\\/2\\.5 {
      --tw-inset-ring-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-red-500\\/2\\.5 {
        --tw-inset-ring-color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .inset-ring-red-500\\/2\\.25 {
      --tw-inset-ring-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-red-500\\/2\\.25 {
        --tw-inset-ring-color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .inset-ring-red-500\\/2\\.75 {
      --tw-inset-ring-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-red-500\\/2\\.75 {
        --tw-inset-ring-color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .inset-ring-red-500\\/50 {
      --tw-inset-ring-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-red-500\\/50 {
        --tw-inset-ring-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .inset-ring-red-500\\/\\[0\\.5\\] {
      --tw-inset-ring-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-red-500\\/\\[0\\.5\\] {
        --tw-inset-ring-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .inset-ring-red-500\\/\\[50\\%\\] {
      --tw-inset-ring-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .inset-ring-red-500\\/\\[50\\%\\] {
        --tw-inset-ring-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .inset-ring-transparent {
      --tw-inset-ring-color: transparent;
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
      '-inset-ring-red-500',
      '-inset-ring-red-500/50',
      '-inset-ring-red-500/[0.5]',
      '-inset-ring-red-500/[50%]',
      '-inset-ring-current',
      '-inset-ring-current/50',
      '-inset-ring-current/[0.5]',
      '-inset-ring-current/[50%]',
      '-inset-ring-inherit',
      '-inset-ring-transparent',
      '-inset-ring-[#0088cc]',
      '-inset-ring-[#0088cc]/50',
      '-inset-ring-[#0088cc]/[0.5]',
      '-inset-ring-[#0088cc]/[50%]',

      // ring width
      '-inset-ring',
      'inset-ring--1',
      '-inset-ring-0',
      '-inset-ring-1',
      '-inset-ring-2',
      '-inset-ring-4',

      'inset-ring/foo',
      'inset-ring-0/foo',
      'inset-ring-1/foo',
      'inset-ring-2/foo',
      'inset-ring-4/foo',
      'inset-ring-[12px]/foo',
      'inset-ring-[length:var(--my-width)]/foo',
    ]),
  ).toEqual('')
})

