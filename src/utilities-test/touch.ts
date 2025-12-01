/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('touch-action', async () => {
  expect(await run(['touch-auto', 'touch-none', 'touch-manipulation'])).toMatchInlineSnapshot(`
    ".touch-auto {
      touch-action: auto;
    }

    .touch-manipulation {
      touch-action: manipulation;
    }

    .touch-none {
      touch-action: none;
    }"
  `)
  expect(
    await run([
      '-touch-auto',
      '-touch-none',
      '-touch-manipulation',
      'touch-auto/foo',
      'touch-none/foo',
      'touch-manipulation/foo',
    ]),
  ).toEqual('')
})

test('touch-pan', async () => {
  expect(
    await run([
      'touch-pan-x',
      'touch-pan-left',
      'touch-pan-right',
      'touch-pan-y',
      'touch-pan-up',
      'touch-pan-down',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-pan-x: initial;
          --tw-pan-y: initial;
          --tw-pinch-zoom: initial;
        }
      }
    }

    .touch-pan-left {
      --tw-pan-x: pan-left;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    .touch-pan-right {
      --tw-pan-x: pan-right;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    .touch-pan-x {
      --tw-pan-x: pan-x;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    .touch-pan-down {
      --tw-pan-y: pan-down;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    .touch-pan-up {
      --tw-pan-y: pan-up;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    .touch-pan-y {
      --tw-pan-y: pan-y;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    @property --tw-pan-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-pan-y {
      syntax: "*";
      inherits: false
    }

    @property --tw-pinch-zoom {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      '-touch-pan-x',
      '-touch-pan-left',
      '-touch-pan-right',
      '-touch-pan-y',
      '-touch-pan-up',
      '-touch-pan-down',
      'touch-pan-x/foo',
      'touch-pan-left/foo',
      'touch-pan-right/foo',
      'touch-pan-y/foo',
      'touch-pan-up/foo',
      'touch-pan-down/foo',
    ]),
  ).toEqual('')
})

test('touch-pinch-zoom', async () => {
  expect(await run(['touch-pinch-zoom'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-pan-x: initial;
          --tw-pan-y: initial;
          --tw-pinch-zoom: initial;
        }
      }
    }

    .touch-pinch-zoom {
      --tw-pinch-zoom: pinch-zoom;
      touch-action: var(--tw-pan-x, ) var(--tw-pan-y, ) var(--tw-pinch-zoom, );
    }

    @property --tw-pan-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-pan-y {
      syntax: "*";
      inherits: false
    }

    @property --tw-pinch-zoom {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(await run(['-touch-pinch-zoom', 'touch-pinch-zoom/foo'])).toEqual('')
})

