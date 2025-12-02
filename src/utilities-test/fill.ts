/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('fill', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --fill-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        'fill-red-500',
        'fill-red-500/50',
        'fill-red-500/2.25',
        'fill-red-500/2.5',
        'fill-red-500/2.75',
        'fill-red-500/[0.5]',
        'fill-red-500/[50%]',
        'fill-blue-500',
        'fill-current',
        'fill-current/50',
        'fill-current/[0.5]',
        'fill-current/[50%]',
        'fill-inherit',
        'fill-transparent',
        'fill-[#0088cc]',
        'fill-[#0088cc]/50',
        'fill-[#0088cc]/[0.5]',
        'fill-[#0088cc]/[50%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --fill-blue-500: #3b82f6;
    }

    .fill-\\[\\#0088cc\\] {
      fill: #08c;
    }

    .fill-\\[\\#0088cc\\]\\/50, .fill-\\[\\#0088cc\\]\\/\\[0\\.5\\], .fill-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      fill: oklab(59.9824% -.067 -.124 / .5);
    }

    .fill-blue-500 {
      fill: var(--fill-blue-500);
    }

    .fill-current, .fill-current\\/50 {
      fill: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-current\\/50 {
        fill: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .fill-current\\/\\[0\\.5\\] {
      fill: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-current\\/\\[0\\.5\\] {
        fill: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .fill-current\\/\\[50\\%\\] {
      fill: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-current\\/\\[50\\%\\] {
        fill: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .fill-inherit {
      fill: inherit;
    }

    .fill-red-500 {
      fill: var(--color-red-500);
    }

    .fill-red-500\\/2\\.5 {
      fill: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-red-500\\/2\\.5 {
        fill: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .fill-red-500\\/2\\.25 {
      fill: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-red-500\\/2\\.25 {
        fill: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .fill-red-500\\/2\\.75 {
      fill: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-red-500\\/2\\.75 {
        fill: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .fill-red-500\\/50 {
      fill: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-red-500\\/50 {
        fill: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .fill-red-500\\/\\[0\\.5\\] {
      fill: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-red-500\\/\\[0\\.5\\] {
        fill: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .fill-red-500\\/\\[50\\%\\] {
      fill: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .fill-red-500\\/\\[50\\%\\] {
        fill: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .fill-transparent {
      fill: #0000;
    }"
  `)
  expect(
    await run([
      'fill',
      'fill-unknown',
      '-fill-red-500',
      '-fill-red-500/50',
      '-fill-red-500/[0.5]',
      '-fill-red-500/[50%]',
      '-fill-current',
      '-fill-current/50',
      '-fill-current/[0.5]',
      '-fill-current/[50%]',
      '-fill-inherit',
      '-fill-transparent',
      '-fill-[#0088cc]',
      '-fill-[#0088cc]/50',
      '-fill-[#0088cc]/[0.5]',
      '-fill-[#0088cc]/[50%]',
    ]),
  ).toEqual('')
})

