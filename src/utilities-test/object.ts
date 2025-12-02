/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('object', async () => {
  expect(
    await run([
      // object-fit
      'object-contain',
      'object-cover',
      'object-fill',
      'object-none',
      'object-scale-down',

      // object-position
      'object-[var(--value)]',
      'object-top',
      'object-top-left',
      'object-top-right',
      'object-bottom',
      'object-bottom-left',
      'object-bottom-right',
      'object-left',
      'object-right',
      'object-center',

      // Legacy versions in v4.0 and earlier
      'object-left-bottom',
      'object-left-top',
      'object-right-bottom',
      'object-right-top',
    ]),
  ).toMatchInlineSnapshot(`
    ".object-contain {
      object-fit: contain;
    }

    .object-cover {
      object-fit: cover;
    }

    .object-fill {
      object-fit: fill;
    }

    .object-none {
      object-fit: none;
    }

    .object-scale-down {
      object-fit: scale-down;
    }

    .object-\\[var\\(--value\\)\\] {
      object-position: var(--value);
    }

    .object-bottom {
      object-position: bottom;
    }

    .object-bottom-left {
      object-position: left bottom;
    }

    .object-bottom-right {
      object-position: right bottom;
    }

    .object-center {
      object-position: center;
    }

    .object-left {
      object-position: left;
    }

    .object-left-bottom {
      object-position: left bottom;
    }

    .object-left-top {
      object-position: left top;
    }

    .object-right {
      object-position: right;
    }

    .object-right-bottom {
      object-position: right bottom;
    }

    .object-right-top {
      object-position: right top;
    }

    .object-top {
      object-position: top;
    }

    .object-top-left {
      object-position: left top;
    }

    .object-top-right {
      object-position: right top;
    }"
  `)
  expect(
    await run([
      'object',
      // object-fit
      '-object-contain',
      '-object-cover',
      '-object-fill',
      '-object-none',
      '-object-scale-down',

      // object-position
      '-object-[var(--value)]',
      '-object-bottom',

      'object-contain/foo',
      'object-cover/foo',
      'object-fill/foo',
      'object-none/foo',
      'object-scale-down/foo',
      'object-[var(--value)]/foo',
      'object-bottom/foo',
      'object-center/foo',
      'object-left/foo',
      'object-left-bottom/foo',
      'object-left-top/foo',
      'object-right/foo',
      'object-right-bottom/foo',
      'object-right-top/foo',
      'object-top/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --object-position-center: top left;
        }
        @tailwind utilities;
      `,
      ['object-center'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --object-position-center: top left;
    }

    .object-center {
      object-position: var(--object-position-center);
    }"
  `)
})

