/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('caret', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --caret-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        'caret-red-500',
        'caret-red-500/50',
        'caret-red-500/2.25',
        'caret-red-500/2.5',
        'caret-red-500/2.75',
        'caret-red-500/[0.5]',
        'caret-red-500/[50%]',
        'caret-blue-500',
        'caret-current',
        'caret-current/50',
        'caret-current/[0.5]',
        'caret-current/[50%]',
        'caret-inherit',
        'caret-transparent',
        'caret-[#0088cc]',
        'caret-[#0088cc]/50',
        'caret-[#0088cc]/[0.5]',
        'caret-[#0088cc]/[50%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --caret-color-blue-500: #3b82f6;
    }

    .caret-\\[\\#0088cc\\] {
      caret-color: #08c;
    }

    .caret-\\[\\#0088cc\\]\\/50, .caret-\\[\\#0088cc\\]\\/\\[0\\.5\\], .caret-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      caret-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .caret-blue-500 {
      caret-color: var(--caret-color-blue-500);
    }

    .caret-current, .caret-current\\/50 {
      caret-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-current\\/50 {
        caret-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .caret-current\\/\\[0\\.5\\] {
      caret-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-current\\/\\[0\\.5\\] {
        caret-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .caret-current\\/\\[50\\%\\] {
      caret-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-current\\/\\[50\\%\\] {
        caret-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .caret-inherit {
      caret-color: inherit;
    }

    .caret-red-500 {
      caret-color: var(--color-red-500);
    }

    .caret-red-500\\/2\\.5 {
      caret-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-red-500\\/2\\.5 {
        caret-color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .caret-red-500\\/2\\.25 {
      caret-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-red-500\\/2\\.25 {
        caret-color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .caret-red-500\\/2\\.75 {
      caret-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-red-500\\/2\\.75 {
        caret-color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .caret-red-500\\/50 {
      caret-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-red-500\\/50 {
        caret-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .caret-red-500\\/\\[0\\.5\\] {
      caret-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-red-500\\/\\[0\\.5\\] {
        caret-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .caret-red-500\\/\\[50\\%\\] {
      caret-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .caret-red-500\\/\\[50\\%\\] {
        caret-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .caret-transparent {
      caret-color: #0000;
    }"
  `)
  expect(
    await run([
      'caret',
      '-caret-red-500',
      '-caret-red-500/50',
      '-caret-red-500/[0.5]',
      '-caret-red-500/[50%]',
      '-caret-current',
      '-caret-current/50',
      '-caret-current/[0.5]',
      '-caret-current/[50%]',
      '-caret-inherit',
      '-caret-transparent',
      '-caret-[#0088cc]',
      '-caret-[#0088cc]/50',
      '-caret-[#0088cc]/[0.5]',
      '-caret-[#0088cc]/[50%]',
      'caret-red-500/foo',
      'caret-red-500/50/foo',
      'caret-red-500/[0.5]/foo',
      'caret-red-500/[50%]/foo',
      'caret-current/foo',
      'caret-current/50/foo',
      'caret-current/[0.5]/foo',
      'caret-current/[50%]/foo',
      'caret-inherit/foo',
      'caret-transparent/foo',
      'caret-[#0088cc]/foo',
      'caret-[#0088cc]/50/foo',
      'caret-[#0088cc]/[0.5]/foo',
      'caret-[#0088cc]/[50%]/foo',
    ]),
  ).toEqual('')
})

