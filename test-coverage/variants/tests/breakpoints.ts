/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('max-*', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          /* Explicitly ordered in a strange way */
          --breakpoint-sm: 640px;
          --breakpoint-lg: 1024px;
          --breakpoint-md: 768px;
        }
        @tailwind utilities;
      `,
      ['max-lg:flex', 'max-sm:flex', 'max-md:flex'],
    ),
  ).toMatchInlineSnapshot(`
    "@media not all and (min-width: 1024px) {
      .max-lg\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 768px) {
      .max-md\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 640px) {
      .max-sm\\:flex {
        display: flex;
      }
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme reference {
          /* Explicitly ordered in a strange way */
          --breakpoint-sm: 640px;
          --breakpoint-lg: 1024px;
          --breakpoint-md: 768px;
        }
        @tailwind utilities;
      `,
      ['max-lg/foo:flex', 'max-sm/foo:flex', 'max-md/foo:flex'],
    ),
  ).toEqual('')
})

test('min-*', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          /* Explicitly ordered in a strange way */
          --breakpoint-sm: 640px;
          --breakpoint-lg: 1024px;
          --breakpoint-md: 768px;
        }
        @tailwind utilities;
      `,
      ['min-lg:flex', 'min-sm:flex', 'min-md:flex'],
    ),
  ).toMatchInlineSnapshot(`
    "@media (min-width: 640px) {
      .min-sm\\:flex {
        display: flex;
      }
    }

    @media (min-width: 768px) {
      .min-md\\:flex {
        display: flex;
      }
    }

    @media (min-width: 1024px) {
      .min-lg\\:flex {
        display: flex;
      }
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme reference {
          /* Explicitly ordered in a strange way */
          --breakpoint-sm: 640px;
          --breakpoint-lg: 1024px;
          --breakpoint-md: 768px;
        }
        @tailwind utilities;
      `,
      ['min-lg/foo:flex', 'min-sm/foo:flex', 'min-md/foo:flex'],
    ),
  ).toEqual('')
})

test('min, max and unprefixed breakpoints', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          /* Explicitly ordered in a strange way */
          --breakpoint-sm: 640px;
          --breakpoint-lg: 1024px;
          --breakpoint-md: 768px;
        }
        @tailwind utilities;
      `,
      [
        'max-lg-sm-potato:flex',
        'min-lg-sm-potato:flex',
        'lg-sm-potato:flex',
        'max-lg:flex',
        'max-sm:flex',
        'min-lg:flex',
        'max-[1000px]:flex',
        'md:flex',
        'min-md:flex',
        'min-[700px]:flex',
        'max-md:flex',
        'min-sm:flex',
        'sm:flex',
        'lg:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@media not all and (min-width: 1024px) {
      .max-lg\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 1000px) {
      .max-\\[1000px\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 768px) {
      .max-md\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 640px) {
      .max-sm\\:flex {
        display: flex;
      }
    }

    @media (min-width: 640px) {
      .min-sm\\:flex, .sm\\:flex {
        display: flex;
      }
    }

    @media (min-width: 700px) {
      .min-\\[700px\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 768px) {
      .md\\:flex, .min-md\\:flex {
        display: flex;
      }
    }

    @media (min-width: 1024px) {
      .lg\\:flex, .min-lg\\:flex {
        display: flex;
      }
    }"
  `)
})

