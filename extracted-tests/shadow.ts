/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('shadow', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --box-shadow-color-blue-500: #3b82f6;
          --shadow-sm: 0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1);
          --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
        }
        @tailwind utilities;
      `,
      [
        // Shadows
        'shadow-sm',
        'shadow-xl',
        'shadow-none',
        'shadow-[12px_12px_#0088cc]',
        'shadow-[12px_12px_var(--value)]',
        'shadow-[10px_10px]',
        'shadow-[var(--value)]',
        'shadow-[shadow:var(--value)]',

        'shadow-sm/25',
        'shadow-[12px_12px_#0088cc]/25',
        'shadow-[12px_12px_var(--value)]/25',
        'shadow-[10px_10px]/25',

        // Colors
        'shadow-red-500',
        'shadow-red-500/50',
        'shadow-red-500/2.25',
        'shadow-red-500/2.5',
        'shadow-red-500/2.75',
        'shadow-red-500/[0.5]',
        'shadow-red-500/[50%]',
        'shadow-blue-500',
        'shadow-current',
        'shadow-current/50',
        'shadow-current/[0.5]',
        'shadow-current/[50%]',
        'shadow-inherit',
        'shadow-transparent',
        'shadow-[#0088cc]',
        'shadow-[#0088cc]/50',
        'shadow-[#0088cc]/[0.5]',
        'shadow-[#0088cc]/[50%]',
        'shadow-[color:var(--value)]',
        'shadow-[color:var(--value)]/50',
        'shadow-[color:var(--value)]/[0.5]',
        'shadow-[color:var(--value)]/[50%]',
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
      --box-shadow-color-blue-500: #3b82f6;
    }

    .shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
      --tw-shadow-alpha: 25%;
      --tw-shadow: 12px 12px var(--tw-shadow-color, var(--value));
    }

    @supports (color: lab(from red l a b)) {
      .shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
        --tw-shadow: 12px 12px var(--tw-shadow-color, oklab(from var(--value) l a b / 25%));
      }
    }

    .shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[10px_10px\\]\\/25 {
      --tw-shadow-alpha: 25%;
      --tw-shadow: 10px 10px var(--tw-shadow-color, currentcolor);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[10px_10px\\]\\/25 {
        --tw-shadow: 10px 10px var(--tw-shadow-color, color-mix(in oklab, currentcolor 25%, transparent));
      }
    }

    .shadow-\\[10px_10px\\]\\/25 {
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[12px_12px_\\#0088cc\\]\\/25 {
      --tw-shadow-alpha: 25%;
      --tw-shadow: 12px 12px var(--tw-shadow-color, oklab(59.9824% -.067 -.124 / .25));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-sm\\/25 {
      --tw-shadow-alpha: 25%;
      --tw-shadow: 0 1px 3px 0 var(--tw-shadow-color, oklab(0% 0 0 / .25)), 0 1px 2px -1px var(--tw-shadow-color, oklab(0% 0 0 / .25));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[10px_10px\\] {
      --tw-shadow: 10px 10px var(--tw-shadow-color, currentcolor);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[12px_12px_\\#0088cc\\] {
      --tw-shadow: 12px 12px var(--tw-shadow-color, #08c);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[12px_12px_var\\(--value\\)\\] {
      --tw-shadow: 12px 12px var(--tw-shadow-color, var(--value));
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[shadow\\:var\\(--value\\)\\], .shadow-\\[var\\(--value\\)\\] {
      --tw-shadow: var(--value);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-none {
      --tw-shadow: 0 0 #0000;
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-sm {
      --tw-shadow: 0 1px 3px 0 var(--tw-shadow-color, #0000001a), 0 1px 2px -1px var(--tw-shadow-color, #0000001a);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-xl {
      --tw-shadow: 0 20px 25px -5px var(--tw-shadow-color, #0000001a), 0 8px 10px -6px var(--tw-shadow-color, #0000001a);
      box-shadow: var(--tw-inset-shadow), var(--tw-inset-ring-shadow), var(--tw-ring-offset-shadow), var(--tw-ring-shadow), var(--tw-shadow);
    }

    .shadow-\\[\\#0088cc\\] {
      --tw-shadow-color: #08c;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[\\#0088cc\\] {
        --tw-shadow-color: color-mix(in oklab, #08c var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[\\#0088cc\\]\\/50 {
      --tw-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[\\#0088cc\\]\\/50 {
        --tw-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[\\#0088cc\\]\\/\\[0\\.5\\] {
      --tw-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[\\#0088cc\\]\\/\\[0\\.5\\] {
        --tw-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[\\#0088cc\\]\\/\\[50\\%\\] {
        --tw-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[color\\:var\\(--value\\)\\] {
      --tw-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[color\\:var\\(--value\\)\\] {
        --tw-shadow-color: color-mix(in oklab, var(--value) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[color\\:var\\(--value\\)\\]\\/50 {
      --tw-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[color\\:var\\(--value\\)\\]\\/50 {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
      --tw-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
      --tw-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-blue-500 {
      --tw-shadow-color: #3b82f6;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-blue-500 {
        --tw-shadow-color: color-mix(in oklab, var(--box-shadow-color-blue-500) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-current {
      --tw-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-current {
        --tw-shadow-color: color-mix(in oklab, currentcolor var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-current\\/50 {
      --tw-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-current\\/50 {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-current\\/\\[0\\.5\\] {
      --tw-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-current\\/\\[0\\.5\\] {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-current\\/\\[50\\%\\] {
      --tw-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-current\\/\\[50\\%\\] {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-inherit {
      --tw-shadow-color: inherit;
    }

    .shadow-red-500 {
      --tw-shadow-color: #ef4444;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500 {
        --tw-shadow-color: color-mix(in oklab, var(--color-red-500) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-red-500\\/2\\.5 {
      --tw-shadow-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500\\/2\\.5 {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.5%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-red-500\\/2\\.25 {
      --tw-shadow-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500\\/2\\.25 {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.25%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-red-500\\/2\\.75 {
      --tw-shadow-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500\\/2\\.75 {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.75%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-red-500\\/50 {
      --tw-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500\\/50 {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-red-500\\/\\[0\\.5\\] {
      --tw-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500\\/\\[0\\.5\\] {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-red-500\\/\\[50\\%\\] {
      --tw-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-red-500\\/\\[50\\%\\] {
        --tw-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-shadow-alpha), transparent);
      }
    }

    .shadow-transparent {
      --tw-shadow-color: transparent;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .shadow-transparent {
        --tw-shadow-color: color-mix(in oklab, transparent var(--tw-shadow-alpha), transparent);
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
      '-shadow-xl',
      '-shadow-none',
      '-shadow-red-500',
      '-shadow-red-500/50',
      '-shadow-red-500/[0.5]',
      '-shadow-red-500/[50%]',
      '-shadow-current',
      '-shadow-current/50',
      '-shadow-current/[0.5]',
      '-shadow-current/[50%]',
      '-shadow-inherit',
      '-shadow-transparent',
      '-shadow-[#0088cc]',
      '-shadow-[#0088cc]/50',
      '-shadow-[#0088cc]/[0.5]',
      '-shadow-[#0088cc]/[50%]',
      '-shadow-[var(--value)]',
    ]),
  ).toEqual('')
})

