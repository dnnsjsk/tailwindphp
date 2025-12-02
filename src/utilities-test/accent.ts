/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('accent', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --accent-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        'accent-red-500',
        'accent-red-500/50',
        'accent-red-500/2.25',
        'accent-red-500/2.5',
        'accent-red-500/2.75',
        'accent-red-500/[0.5]',
        'accent-red-500/[50%]',
        'accent-blue-500',
        'accent-current',
        'accent-current/50',
        'accent-current/[0.5]',
        'accent-current/[50%]',
        'accent-inherit',
        'accent-transparent',
        'accent-[#0088cc]',
        'accent-[#0088cc]/50',
        'accent-[#0088cc]/[0.5]',
        'accent-[#0088cc]/[50%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --accent-color-blue-500: #3b82f6;
    }

    .accent-\\[\\#0088cc\\] {
      accent-color: #08c;
    }

    .accent-\\[\\#0088cc\\]\\/50, .accent-\\[\\#0088cc\\]\\/\\[0\\.5\\], .accent-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      accent-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .accent-blue-500 {
      accent-color: var(--accent-color-blue-500);
    }

    .accent-current, .accent-current\\/50 {
      accent-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-current\\/50 {
        accent-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .accent-current\\/\\[0\\.5\\] {
      accent-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-current\\/\\[0\\.5\\] {
        accent-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .accent-current\\/\\[50\\%\\] {
      accent-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-current\\/\\[50\\%\\] {
        accent-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .accent-inherit {
      accent-color: inherit;
    }

    .accent-red-500 {
      accent-color: var(--color-red-500);
    }

    .accent-red-500\\/2\\.5 {
      accent-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-red-500\\/2\\.5 {
        accent-color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .accent-red-500\\/2\\.25 {
      accent-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-red-500\\/2\\.25 {
        accent-color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .accent-red-500\\/2\\.75 {
      accent-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-red-500\\/2\\.75 {
        accent-color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .accent-red-500\\/50 {
      accent-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-red-500\\/50 {
        accent-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .accent-red-500\\/\\[0\\.5\\] {
      accent-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-red-500\\/\\[0\\.5\\] {
        accent-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .accent-red-500\\/\\[50\\%\\] {
      accent-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .accent-red-500\\/\\[50\\%\\] {
        accent-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .accent-transparent {
      accent-color: #0000;
    }"
  `)
  expect(
    await run([
      'accent',
      '-accent-red-500',
      'accent-red-500/-50',
      '-accent-red-500/50',
      '-accent-red-500/[0.5]',
      '-accent-red-500/[50%]',
      '-accent-current',
      '-accent-current/50',
      '-accent-current/[0.5]',
      '-accent-current/[50%]',
      '-accent-inherit',
      '-accent-transparent',
      'accent-[#0088cc]/-50',
      '-accent-[#0088cc]',
      '-accent-[#0088cc]/50',
      '-accent-[#0088cc]/[0.5]',
      '-accent-[#0088cc]/[50%]',
      'accent-red-500/foo',
      'accent-red-500/50/foo',
      'accent-red-500/[0.5]/foo',
      'accent-red-500/[50%]/foo',
      'accent-current/foo',
      'accent-current/50/foo',
      'accent-current/[0.5]/foo',
      'accent-current/[50%]/foo',
      'accent-inherit/foo',
      'accent-transparent/foo',
      'accent-[#0088cc]/foo',
      'accent-[#0088cc]/50/foo',
      'accent-[#0088cc]/[0.5]/foo',
      'accent-[#0088cc]/[50%]/foo',
    ]),
  ).toEqual('')
})

