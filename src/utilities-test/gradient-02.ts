/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('to', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      [
        // --tw-gradient-to
        'to-red-500',
        'to-red-500/50',
        'to-red-500/[0.5]',
        'to-red-500/[50%]',
        'to-current',
        'to-current/50',
        'to-current/[0.5]',
        'to-current/[50%]',
        'to-inherit',
        'to-transparent',
        'to-[#0088cc]',
        'to-[#0088cc]/50',
        'to-[#0088cc]/[0.5]',
        'to-[#0088cc]/[50%]',
        'to-[var(--my-color)]',
        'to-[var(--my-color)]/50',
        'to-[var(--my-color)]/[0.5]',
        'to-[var(--my-color)]/[50%]',
        'to-[color:var(--my-color)]',
        'to-[color:var(--my-color)]/50',
        'to-[color:var(--my-color)]/[0.5]',
        'to-[color:var(--my-color)]/[50%]',

        // --tw-gradient-to-position
        'to-0%',
        'to-5%',
        'to-100%',
        'to-[50%]',
        'to-[50px]',
        'to-[length:var(--my-position)]',
        'to-[percentage:var(--my-position)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-gradient-position: initial;
          --tw-gradient-from: #0000;
          --tw-gradient-via: #0000;
          --tw-gradient-to: #0000;
          --tw-gradient-stops: initial;
          --tw-gradient-via-stops: initial;
          --tw-gradient-from-position: 0%;
          --tw-gradient-via-position: 50%;
          --tw-gradient-to-position: 100%;
        }
      }
    }

    :root, :host {
      --color-red-500: #ef4444;
    }

    .to-\\[\\#0088cc\\] {
      --tw-gradient-to: #08c;
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[\\#0088cc\\]\\/50, .to-\\[\\#0088cc\\]\\/\\[0\\.5\\], .to-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      --tw-gradient-to: oklab(59.9824% -.067 -.124 / .5);
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[color\\:var\\(--my-color\\)\\] {
      --tw-gradient-to: var(--my-color);
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      --tw-gradient-to: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        --tw-gradient-to: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .to-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-gradient-to: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-gradient-to: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .to-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-gradient-to: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-gradient-to: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .to-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[var\\(--my-color\\)\\] {
      --tw-gradient-to: var(--my-color);
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[var\\(--my-color\\)\\]\\/50 {
      --tw-gradient-to: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-\\[var\\(--my-color\\)\\]\\/50 {
        --tw-gradient-to: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .to-\\[var\\(--my-color\\)\\]\\/50 {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-gradient-to: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        --tw-gradient-to: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .to-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-gradient-to: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        --tw-gradient-to: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .to-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-current {
      --tw-gradient-to: currentcolor;
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-current\\/50 {
      --tw-gradient-to: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-current\\/50 {
        --tw-gradient-to: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .to-current\\/50 {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-current\\/\\[0\\.5\\] {
      --tw-gradient-to: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-current\\/\\[0\\.5\\] {
        --tw-gradient-to: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .to-current\\/\\[0\\.5\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-current\\/\\[50\\%\\] {
      --tw-gradient-to: currentcolor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-current\\/\\[50\\%\\] {
        --tw-gradient-to: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .to-current\\/\\[50\\%\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-inherit {
      --tw-gradient-to: inherit;
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-red-500 {
      --tw-gradient-to: var(--color-red-500);
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-red-500\\/50 {
      --tw-gradient-to: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-red-500\\/50 {
        --tw-gradient-to: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .to-red-500\\/50 {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-red-500\\/\\[0\\.5\\] {
      --tw-gradient-to: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-red-500\\/\\[0\\.5\\] {
        --tw-gradient-to: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .to-red-500\\/\\[0\\.5\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-red-500\\/\\[50\\%\\] {
      --tw-gradient-to: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .to-red-500\\/\\[50\\%\\] {
        --tw-gradient-to: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .to-red-500\\/\\[50\\%\\] {
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-transparent {
      --tw-gradient-to: transparent;
      --tw-gradient-stops: var(--tw-gradient-via-stops, var(--tw-gradient-position), var(--tw-gradient-from) var(--tw-gradient-from-position), var(--tw-gradient-to) var(--tw-gradient-to-position));
    }

    .to-0\\% {
      --tw-gradient-to-position: 0%;
    }

    .to-5\\% {
      --tw-gradient-to-position: 5%;
    }

    .to-100\\% {
      --tw-gradient-to-position: 100%;
    }

    .to-\\[50\\%\\] {
      --tw-gradient-to-position: 50%;
    }

    .to-\\[50px\\] {
      --tw-gradient-to-position: 50px;
    }

    .to-\\[length\\:var\\(--my-position\\)\\], .to-\\[percentage\\:var\\(--my-position\\)\\] {
      --tw-gradient-to-position: var(--my-position);
    }

    @property --tw-gradient-position {
      syntax: "*";
      inherits: false
    }

    @property --tw-gradient-from {
      syntax: "<color>";
      inherits: false;
      initial-value: #0000;
    }

    @property --tw-gradient-via {
      syntax: "<color>";
      inherits: false;
      initial-value: #0000;
    }

    @property --tw-gradient-to {
      syntax: "<color>";
      inherits: false;
      initial-value: #0000;
    }

    @property --tw-gradient-stops {
      syntax: "*";
      inherits: false
    }

    @property --tw-gradient-via-stops {
      syntax: "*";
      inherits: false
    }

    @property --tw-gradient-from-position {
      syntax: "<length-percentage>";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-gradient-via-position {
      syntax: "<length-percentage>";
      inherits: false;
      initial-value: 50%;
    }

    @property --tw-gradient-to-position {
      syntax: "<length-percentage>";
      inherits: false;
      initial-value: 100%;
    }"
  `)
  expect(
    await run([
      'to',
      'to-123',
      'to--123',
      'to--5%',
      'to-unknown',
      'to-unknown%',

      // --tw-gradient-to
      '-to-red-500',
      '-to-red-500/50',
      '-to-red-500/[0.5]',
      '-to-red-500/[50%]',
      '-to-current',
      '-to-current/50',
      '-to-current/[0.5]',
      '-to-current/[50%]',
      '-to-inherit',
      '-to-transparent',
      '-to-[#0088cc]',
      '-to-[#0088cc]/50',
      '-to-[#0088cc]/[0.5]',
      '-to-[#0088cc]/[50%]',

      // --tw-gradient-to-position
      '-to-0%',
      '-to-5%',
      '-to-100%',
    ]),
  ).toEqual('')
})

