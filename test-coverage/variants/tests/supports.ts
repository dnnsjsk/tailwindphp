/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('supports', async () => {
  expect(
    await run([
      'supports-gap:grid',
      'supports-[display:grid]:flex',
      'supports-[selector(A_>_B)]:flex',
      'supports-[font-format(opentype)]:grid',
      'supports-[(display:grid)_and_font-format(opentype)]:grid',
      'supports-[font-tech(color-COLRv1)]:flex',
      'supports-[var(--test)]:flex',
      'supports-[--test]:flex',
    ]),
  ).toMatchInlineSnapshot(`
    "@supports (gap: var(--tw)) {
      .supports-gap\\:grid {
        display: grid;
      }
    }

    @supports (display: grid) and font-format(opentype) {
      .supports-\\[\\(display\\:grid\\)_and_font-format\\(opentype\\)\\]\\:grid {
        display: grid;
      }
    }

    @supports (--test: var(--tw)) {
      .supports-\\[--test\\]\\:flex {
        display: flex;
      }
    }

    @supports (display: grid) {
      .supports-\\[display\\:grid\\]\\:flex {
        display: flex;
      }
    }

    @supports font-format(opentype) {
      .supports-\\[font-format\\(opentype\\)\\]\\:grid {
        display: grid;
      }
    }

    @supports font-tech(color-COLRv1) {
      .supports-\\[font-tech\\(color-COLRv1\\)\\]\\:flex {
        display: flex;
      }
    }

    @supports selector(A > B) {
      .supports-\\[selector\\(A_\\>_B\\)\\]\\:flex {
        display: flex;
      }
    }

    @supports var(--test) {
      .supports-\\[var\\(--test\\)\\]\\:flex {
        display: flex;
      }
    }"
  `)
  expect(
    await run([
      'supports-gap/foo:grid',
      'supports-[display:grid]/foo:flex',
      'supports-[selector(A_>_B)]/foo:flex',
      'supports-[font-format(opentype)]/foo:grid',
      'supports-[(display:grid)_and_font-format(opentype)]/foo:grid',
      'supports-[font-tech(color-COLRv1)]/foo:flex',
      'supports-[var(--test)]/foo:flex',
    ]),
  ).toEqual('')
})

