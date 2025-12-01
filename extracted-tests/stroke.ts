/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('stroke', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --stroke-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        // Color
        'stroke-red-500',
        'stroke-red-500/50',
        'stroke-red-500/2.25',
        'stroke-red-500/2.5',
        'stroke-red-500/2.75',
        'stroke-red-500/[0.5]',
        'stroke-red-500/[50%]',
        'stroke-blue-500',
        'stroke-current',
        'stroke-current/50',
        'stroke-current/[0.5]',
        'stroke-current/[50%]',
        'stroke-inherit',
        'stroke-transparent',
        'stroke-[#0088cc]',
        'stroke-[#0088cc]/50',
        'stroke-[#0088cc]/[0.5]',
        'stroke-[#0088cc]/[50%]',
        'stroke-[var(--my-color)]',
        'stroke-[var(--my-color)]/50',
        'stroke-[var(--my-color)]/[0.5]',
        'stroke-[var(--my-color)]/[50%]',
        'stroke-[color:var(--my-color)]',
        'stroke-[color:var(--my-color)]/50',
        'stroke-[color:var(--my-color)]/[0.5]',
        'stroke-[color:var(--my-color)]/[50%]',
        'stroke-none',

        // Width
        'stroke-0',
        'stroke-1',
        'stroke-2',
        'stroke-[1.5]',
        'stroke-[12px]',
        'stroke-[50%]',
        'stroke-[number:var(--my-width)]',
        'stroke-[length:var(--my-width)]',
        'stroke-[percentage:var(--my-width)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --stroke-blue-500: #3b82f6;
    }

    .stroke-\\[\\#0088cc\\] {
      stroke: #08c;
    }

    .stroke-\\[\\#0088cc\\]\\/50, .stroke-\\[\\#0088cc\\]\\/\\[0\\.5\\], .stroke-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      stroke: oklab(59.9824% -.067 -.124 / .5);
    }

    .stroke-\\[color\\:var\\(--my-color\\)\\], .stroke-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      stroke: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        stroke: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .stroke-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      stroke: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        stroke: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .stroke-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      stroke: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        stroke: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .stroke-\\[var\\(--my-color\\)\\], .stroke-\\[var\\(--my-color\\)\\]\\/50 {
      stroke: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-\\[var\\(--my-color\\)\\]\\/50 {
        stroke: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .stroke-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      stroke: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        stroke: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .stroke-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      stroke: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        stroke: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .stroke-blue-500 {
      stroke: var(--stroke-blue-500);
    }

    .stroke-current, .stroke-current\\/50 {
      stroke: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-current\\/50 {
        stroke: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .stroke-current\\/\\[0\\.5\\] {
      stroke: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-current\\/\\[0\\.5\\] {
        stroke: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .stroke-current\\/\\[50\\%\\] {
      stroke: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-current\\/\\[50\\%\\] {
        stroke: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .stroke-inherit {
      stroke: inherit;
    }

    .stroke-none {
      stroke: none;
    }

    .stroke-red-500 {
      stroke: var(--color-red-500);
    }

    .stroke-red-500\\/2\\.5 {
      stroke: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-red-500\\/2\\.5 {
        stroke: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .stroke-red-500\\/2\\.25 {
      stroke: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-red-500\\/2\\.25 {
        stroke: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .stroke-red-500\\/2\\.75 {
      stroke: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-red-500\\/2\\.75 {
        stroke: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .stroke-red-500\\/50 {
      stroke: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-red-500\\/50 {
        stroke: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .stroke-red-500\\/\\[0\\.5\\] {
      stroke: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-red-500\\/\\[0\\.5\\] {
        stroke: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .stroke-red-500\\/\\[50\\%\\] {
      stroke: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .stroke-red-500\\/\\[50\\%\\] {
        stroke: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .stroke-transparent {
      stroke: #0000;
    }

    .stroke-0 {
      stroke-width: 0;
    }

    .stroke-1 {
      stroke-width: 1px;
    }

    .stroke-2 {
      stroke-width: 2px;
    }

    .stroke-\\[1\\.5\\] {
      stroke-width: 1.5px;
    }

    .stroke-\\[12px\\] {
      stroke-width: 12px;
    }

    .stroke-\\[50\\%\\] {
      stroke-width: 50%;
    }

    .stroke-\\[length\\:var\\(--my-width\\)\\], .stroke-\\[number\\:var\\(--my-width\\)\\], .stroke-\\[percentage\\:var\\(--my-width\\)\\] {
      stroke-width: var(--my-width);
    }"
  `)
  expect(
    await run([
      'stroke',
      'stroke-unknown',

      // Color
      '-stroke-red-500',
      '-stroke-red-500/50',
      '-stroke-red-500/[0.5]',
      '-stroke-red-500/[50%]',
      '-stroke-current',
      '-stroke-current/50',
      '-stroke-current/[0.5]',
      '-stroke-current/[50%]',
      '-stroke-inherit',
      '-stroke-transparent',
      '-stroke-[#0088cc]',
      '-stroke-[#0088cc]/50',
      '-stroke-[#0088cc]/[0.5]',
      '-stroke-[#0088cc]/[50%]',

      // Width
      '-stroke-0',
      'stroke--1',
    ]),
  ).toEqual('')
})

