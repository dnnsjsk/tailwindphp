/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('text', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
          --color-red-500: #ef4444;
          --text-color-blue-500: #3b82f6;
          --text-sm: 0.875rem;
          --text-sm--line-height: 1.25rem;
          --leading-snug: 1.375;
        }
        @tailwind utilities;
      `,
      [
        // color
        'text-red-500',
        'text-red-500/50',
        'text-red-500/2.25',
        'text-red-500/2.5',
        'text-red-500/2.75',
        'text-red-500/[0.5]',
        'text-red-500/[50%]',
        'text-blue-500',
        'text-current',
        'text-current/50',
        'text-current/[0.5]',
        'text-current/[50%]',
        'text-inherit',
        'text-transparent',
        'text-[#0088cc]',
        'text-[#0088cc]/50',
        'text-[#0088cc]/[0.5]',
        'text-[#0088cc]/[50%]',

        'text-[var(--my-color)]',
        'text-[var(--my-color)]/50',
        'text-[var(--my-color)]/[0.5]',
        'text-[var(--my-color)]/[50%]',
        'text-[color:var(--my-color)]',
        'text-[color:var(--my-color)]/50',
        'text-[color:var(--my-color)]/[0.5]',
        'text-[color:var(--my-color)]/[50%]',

        // font-size / line-height / letter-spacing / font-weight
        'text-sm',
        'text-sm/6',
        'text-sm/none',
        'text-[10px]/none',
        'text-sm/snug',
        'text-sm/[4px]',
        'text-[12px]',
        'text-[12px]/6',
        'text-[50%]',
        'text-[50%]/6',
        'text-[xx-large]',
        'text-[xx-large]/6',
        'text-[larger]',
        'text-[larger]/6',
        'text-[length:var(--my-size)]',
        'text-[percentage:var(--my-size)]',
        'text-[absolute-size:var(--my-size)]',
        'text-[relative-size:var(--my-size)]',
        'text-[clamp(1rem,2rem,3rem)]',
        'text-[clamp(1rem,var(--size),3rem)]',
        'text-[clamp(1rem,var(--size),3rem)]/9',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing: .25rem;
      --color-red-500: #ef4444;
      --text-color-blue-500: #3b82f6;
      --text-sm: .875rem;
      --text-sm--line-height: 1.25rem;
      --leading-snug: 1.375;
    }

    .text-\\[10px\\]\\/none {
      font-size: 10px;
      line-height: 1;
    }

    .text-\\[12px\\]\\/6 {
      font-size: 12px;
      line-height: calc(var(--spacing) * 6);
    }

    .text-\\[50\\%\\]\\/6 {
      font-size: 50%;
      line-height: calc(var(--spacing) * 6);
    }

    .text-\\[clamp\\(1rem\\,var\\(--size\\)\\,3rem\\)\\]\\/9 {
      font-size: clamp(1rem, var(--size), 3rem);
      line-height: calc(var(--spacing) * 9);
    }

    .text-\\[larger\\]\\/6 {
      font-size: larger;
      line-height: calc(var(--spacing) * 6);
    }

    .text-\\[xx-large\\]\\/6 {
      font-size: xx-large;
      line-height: calc(var(--spacing) * 6);
    }

    .text-sm {
      font-size: var(--text-sm);
      line-height: var(--tw-leading, var(--text-sm--line-height));
    }

    .text-sm\\/6 {
      font-size: var(--text-sm);
      line-height: calc(var(--spacing) * 6);
    }

    .text-sm\\/\\[4px\\] {
      font-size: var(--text-sm);
      line-height: 4px;
    }

    .text-sm\\/none {
      font-size: var(--text-sm);
      line-height: 1;
    }

    .text-sm\\/snug {
      font-size: var(--text-sm);
      line-height: var(--leading-snug);
    }

    .text-\\[12px\\] {
      font-size: 12px;
    }

    .text-\\[50\\%\\] {
      font-size: 50%;
    }

    .text-\\[absolute-size\\:var\\(--my-size\\)\\] {
      font-size: var(--my-size);
    }

    .text-\\[clamp\\(1rem\\,2rem\\,3rem\\)\\] {
      font-size: 2rem;
    }

    .text-\\[clamp\\(1rem\\,var\\(--size\\)\\,3rem\\)\\] {
      font-size: clamp(1rem, var(--size), 3rem);
    }

    .text-\\[larger\\] {
      font-size: larger;
    }

    .text-\\[length\\:var\\(--my-size\\)\\], .text-\\[percentage\\:var\\(--my-size\\)\\], .text-\\[relative-size\\:var\\(--my-size\\)\\] {
      font-size: var(--my-size);
    }

    .text-\\[xx-large\\] {
      font-size: xx-large;
    }

    .text-\\[\\#0088cc\\] {
      color: #08c;
    }

    .text-\\[\\#0088cc\\]\\/50, .text-\\[\\#0088cc\\]\\/\\[0\\.5\\], .text-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      color: oklab(59.9824% -.067 -.124 / .5);
    }

    .text-\\[color\\:var\\(--my-color\\)\\], .text-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .text-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .text-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .text-\\[var\\(--my-color\\)\\], .text-\\[var\\(--my-color\\)\\]\\/50 {
      color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-\\[var\\(--my-color\\)\\]\\/50 {
        color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .text-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .text-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .text-blue-500 {
      color: var(--text-color-blue-500);
    }

    .text-current, .text-current\\/50 {
      color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-current\\/50 {
        color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .text-current\\/\\[0\\.5\\] {
      color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-current\\/\\[0\\.5\\] {
        color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .text-current\\/\\[50\\%\\] {
      color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-current\\/\\[50\\%\\] {
        color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .text-inherit {
      color: inherit;
    }

    .text-red-500 {
      color: var(--color-red-500);
    }

    .text-red-500\\/2\\.5 {
      color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-red-500\\/2\\.5 {
        color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .text-red-500\\/2\\.25 {
      color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-red-500\\/2\\.25 {
        color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .text-red-500\\/2\\.75 {
      color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-red-500\\/2\\.75 {
        color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .text-red-500\\/50 {
      color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-red-500\\/50 {
        color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .text-red-500\\/\\[0\\.5\\] {
      color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-red-500\\/\\[0\\.5\\] {
        color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .text-red-500\\/\\[50\\%\\] {
      color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-red-500\\/\\[50\\%\\] {
        color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .text-transparent {
      color: #0000;
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme inline reference {
          --text-sm: 0.875rem;
        }
        @tailwind utilities;
      `,
      [
        'text',
        // color
        '-text-red-500',
        '-text-red-500/50',
        '-text-red-500/[0.5]',
        '-text-red-500/[50%]',
        '-text-current',
        '-text-current/50',
        '-text-current/[0.5]',
        '-text-current/[50%]',
        '-text-inherit',
        '-text-transparent',
        '-text-[#0088cc]',
        '-text-[#0088cc]/50',
        '-text-[#0088cc]/[0.5]',
        '-text-[#0088cc]/[50%]',

        // font-size / line-height / letter-spacing / font-weight
        '-text-sm',
        '-text-sm/6',
        'text-sm/foo',
        '-text-sm/[4px]',
        'text-[10px]/foo',
      ],
    ),
  ).toEqual('')
})

test('text-shadow', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --text-shadow-2xs: 0px 1px 0px rgb(0 0 0 / 0.1);
          --text-shadow-sm: 0px 1px 2px rgb(0 0 0 / 0.06), 0px 2px 2px rgb(0 0 0 / 0.06);
        }
        @tailwind utilities;
      `,
      [
        // Shadows
        'text-shadow-2xs',
        'text-shadow-sm',
        'text-shadow-none',
        'text-shadow-[12px_12px_#0088cc]',
        'text-shadow-[12px_12px_var(--value)]',
        'text-shadow-[10px_10px]',
        'text-shadow-[var(--value)]',
        'text-shadow-[shadow:var(--value)]',

        'text-shadow-sm/25',
        'text-shadow-[12px_12px_#0088cc]/25',
        'text-shadow-[12px_12px_var(--value)]/25',
        'text-shadow-[10px_10px]/25',

        // Colors
        'text-shadow-red-500',
        'text-shadow-red-500/50',
        'text-shadow-red-500/2.25',
        'text-shadow-red-500/2.5',
        'text-shadow-red-500/2.75',
        'text-shadow-red-500/[0.5]',
        'text-shadow-red-500/[50%]',
        'text-shadow-current',
        'text-shadow-current/50',
        'text-shadow-current/[0.5]',
        'text-shadow-current/[50%]',
        'text-shadow-inherit',
        'text-shadow-transparent',
        'text-shadow-[#0088cc]',
        'text-shadow-[#0088cc]/50',
        'text-shadow-[#0088cc]/[0.5]',
        'text-shadow-[#0088cc]/[50%]',
        'text-shadow-[color:var(--value)]',
        'text-shadow-[color:var(--value)]/50',
        'text-shadow-[color:var(--value)]/[0.5]',
        'text-shadow-[color:var(--value)]/[50%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-text-shadow-color: initial;
          --tw-text-shadow-alpha: 100%;
        }
      }
    }

    :root, :host {
      --color-red-500: #ef4444;
    }

    .text-shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
      --tw-text-shadow-alpha: 25%;
      text-shadow: 12px 12px var(--tw-text-shadow-color, var(--value));
    }

    @supports (color: lab(from red l a b)) {
      .text-shadow-\\[12px_12px_var\\(--value\\)\\]\\/25 {
        text-shadow: 12px 12px var(--tw-text-shadow-color, oklab(from var(--value) l a b / 25%));
      }
    }

    .text-shadow-\\[10px_10px\\]\\/25 {
      --tw-text-shadow-alpha: 25%;
      text-shadow: 10px 10px var(--tw-text-shadow-color, currentcolor);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[10px_10px\\]\\/25 {
        text-shadow: 10px 10px var(--tw-text-shadow-color, color-mix(in oklab, currentcolor 25%, transparent));
      }
    }

    .text-shadow-\\[12px_12px_\\#0088cc\\]\\/25 {
      --tw-text-shadow-alpha: 25%;
      text-shadow: 12px 12px var(--tw-text-shadow-color, oklab(59.9824% -.067 -.124 / .25));
    }

    .text-shadow-sm\\/25 {
      --tw-text-shadow-alpha: 25%;
      text-shadow: 0px 1px 2px var(--tw-text-shadow-color, oklab(0% 0 0 / .25)), 0px 2px 2px var(--tw-text-shadow-color, oklab(0% 0 0 / .25));
    }

    .text-shadow-2xs {
      text-shadow: 0px 1px 0px var(--tw-text-shadow-color, #0000001a);
    }

    .text-shadow-\\[\\#0088cc\\] {
      --tw-text-shadow-color: #08c;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[\\#0088cc\\] {
        --tw-text-shadow-color: color-mix(in oklab, #08c var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[\\#0088cc\\]\\/50 {
      --tw-text-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[\\#0088cc\\]\\/50 {
        --tw-text-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[\\#0088cc\\]\\/\\[0\\.5\\] {
      --tw-text-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[\\#0088cc\\]\\/\\[0\\.5\\] {
        --tw-text-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-text-shadow-color: #0088cc80;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[\\#0088cc\\]\\/\\[50\\%\\] {
        --tw-text-shadow-color: color-mix(in oklab, oklab(59.9824% -.067 -.124 / .5) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[10px_10px\\] {
      text-shadow: 10px 10px var(--tw-text-shadow-color, currentcolor);
    }

    .text-shadow-\\[12px_12px_\\#0088cc\\] {
      text-shadow: 12px 12px var(--tw-text-shadow-color, #08c);
    }

    .text-shadow-\\[12px_12px_var\\(--value\\)\\] {
      text-shadow: 12px 12px var(--tw-text-shadow-color, var(--value));
    }

    .text-shadow-\\[color\\:var\\(--value\\)\\] {
      --tw-text-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[color\\:var\\(--value\\)\\] {
        --tw-text-shadow-color: color-mix(in oklab, var(--value) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[color\\:var\\(--value\\)\\]\\/50 {
      --tw-text-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[color\\:var\\(--value\\)\\]\\/50 {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
      --tw-text-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[0\\.5\\] {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
      --tw-text-shadow-color: var(--value);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-\\[color\\:var\\(--value\\)\\]\\/\\[50\\%\\] {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--value) 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-\\[shadow\\:var\\(--value\\)\\], .text-shadow-\\[var\\(--value\\)\\] {
      text-shadow: var(--value);
    }

    .text-shadow-current {
      --tw-text-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-current {
        --tw-text-shadow-color: color-mix(in oklab, currentcolor var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-current\\/50 {
      --tw-text-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-current\\/50 {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-current\\/\\[0\\.5\\] {
      --tw-text-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-current\\/\\[0\\.5\\] {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-current\\/\\[50\\%\\] {
      --tw-text-shadow-color: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-current\\/\\[50\\%\\] {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, currentcolor 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-inherit {
      --tw-text-shadow-color: inherit;
    }

    .text-shadow-none {
      text-shadow: none;
    }

    .text-shadow-red-500 {
      --tw-text-shadow-color: #ef4444;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500 {
        --tw-text-shadow-color: color-mix(in oklab, var(--color-red-500) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-red-500\\/2\\.5 {
      --tw-text-shadow-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500\\/2\\.5 {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.5%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-red-500\\/2\\.25 {
      --tw-text-shadow-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500\\/2\\.25 {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.25%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-red-500\\/2\\.75 {
      --tw-text-shadow-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500\\/2\\.75 {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 2.75%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-red-500\\/50 {
      --tw-text-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500\\/50 {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-red-500\\/\\[0\\.5\\] {
      --tw-text-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500\\/\\[0\\.5\\] {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-red-500\\/\\[50\\%\\] {
      --tw-text-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-red-500\\/\\[50\\%\\] {
        --tw-text-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-text-shadow-alpha), transparent);
      }
    }

    .text-shadow-sm {
      text-shadow: 0px 1px 2px var(--tw-text-shadow-color, #0000000f), 0px 2px 2px var(--tw-text-shadow-color, #0000000f);
    }

    .text-shadow-transparent {
      --tw-text-shadow-color: transparent;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .text-shadow-transparent {
        --tw-text-shadow-color: color-mix(in oklab, transparent var(--tw-text-shadow-alpha), transparent);
      }
    }

    @property --tw-text-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-text-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
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

