/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('not', async () => {
  expect(
    await compileCss(
      css`
        @custom-variant hocus {
          &:hover,
          &:focus {
            @slot;
          }
        }

        @custom-variant device-hocus {
          @media (hover: hover) {
            &:hover,
            &:focus {
              @slot;
            }
          }
        }

        @theme {
          --breakpoint-sm: 640px;
        }

        @tailwind utilities;
      `,
      [
        'not-[:checked]:flex',
        'not-[@media_print]:flex',
        'not-[@media(orientation:portrait)]:flex',
        'not-[@media_(orientation:landscape)]:flex',
        'not-[@media_not_(orientation:portrait)]:flex',
        'not-hocus:flex',
        'not-device-hocus:flex',

        'group-not-[:checked]:flex',
        'group-not-[:checked]/parent-name:flex',
        'group-not-checked:flex',
        'group-not-hocus:flex',
        // 'group-not-hover:flex',
        // 'group-not-device-hocus:flex',
        'group-not-hocus/parent-name:flex',

        'peer-not-[:checked]:flex',
        'peer-not-[:checked]/sibling-name:flex',
        'peer-not-checked:flex',
        'peer-not-hocus:flex',
        'peer-not-hocus/sibling-name:flex',

        // Not versions of built-in variants
        'not-first:flex',
        'not-last:flex',
        'not-only:flex',
        'not-odd:flex',
        'not-even:flex',
        'not-first-of-type:flex',
        'not-last-of-type:flex',
        'not-only-of-type:flex',
        'not-visited:flex',
        'not-target:flex',
        'not-open:flex',
        'not-default:flex',
        'not-checked:flex',
        'not-indeterminate:flex',
        'not-placeholder-shown:flex',
        'not-autofill:flex',
        'not-optional:flex',
        'not-required:flex',
        'not-valid:flex',
        'not-invalid:flex',
        'not-in-range:flex',
        'not-out-of-range:flex',
        'not-read-only:flex',
        'not-empty:flex',
        'not-focus-within:flex',
        'not-hover:flex',
        'not-focus:flex',
        'not-focus-visible:flex',
        'not-active:flex',
        'not-enabled:flex',
        'not-disabled:flex',
        'not-inert:flex',

        'not-ltr:flex',
        'not-rtl:flex',
        'not-motion-safe:flex',
        'not-motion-reduce:flex',
        'not-dark:flex',
        'not-print:flex',
        'not-supports-grid:flex',
        'not-has-checked:flex',
        'not-aria-selected:flex',
        'not-data-foo:flex',
        'not-portrait:flex',
        'not-landscape:flex',
        'not-contrast-more:flex',
        'not-contrast-less:flex',
        'not-forced-colors:flex',
        'not-nth-2:flex',
        'not-noscript:flex',

        'not-sm:flex',
        'not-min-sm:flex',
        'not-min-[130px]:flex',
        'not-max-sm:flex',
        'not-max-[130px]:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ".not-first\\:flex:not(:first-child), .not-last\\:flex:not(:last-child), .not-only\\:flex:not(:only-child), .not-odd\\:flex:not(:nth-child(odd)), .not-even\\:flex:not(:nth-child(2n)), .not-first-of-type\\:flex:not(:first-of-type), .not-last-of-type\\:flex:not(:last-of-type), .not-only-of-type\\:flex:not(:only-of-type), .not-visited\\:flex:not(:visited), .not-target\\:flex:not(:target), .not-open\\:flex:not(:is([open], :popover-open, :open)), .not-default\\:flex:not(:default), .not-checked\\:flex:not(:checked), .not-indeterminate\\:flex:not(:indeterminate), .not-placeholder-shown\\:flex:not(:placeholder-shown), .not-autofill\\:flex:not(:autofill), .not-optional\\:flex:not(:optional), .not-required\\:flex:not(:required), .not-valid\\:flex:not(:valid), .not-invalid\\:flex:not(:invalid), .not-in-range\\:flex:not(:in-range), .not-out-of-range\\:flex:not(:out-of-range), .not-read-only\\:flex:not(:read-only), .not-empty\\:flex:not(:empty), .not-focus-within\\:flex:not(:focus-within), .not-hover\\:flex:not(:hover) {
      display: flex;
    }

    @media not all and (hover: hover) {
      .not-hover\\:flex {
        display: flex;
      }
    }

    .not-focus\\:flex:not(:focus), .not-focus-visible\\:flex:not(:focus-visible), .not-active\\:flex:not(:active), .not-enabled\\:flex:not(:enabled), .not-disabled\\:flex:not(:disabled), .not-inert\\:flex:not(:is([inert], [inert] *)), .not-has-checked\\:flex:not(:has(:checked)), .not-aria-selected\\:flex:not([aria-selected="true"]), .not-data-foo\\:flex:not([data-foo]), .not-nth-2\\:flex:not(:nth-child(2)) {
      display: flex;
    }

    @supports not (grid: var(--tw)) {
      .not-supports-grid\\:flex {
        display: flex;
      }
    }

    @media not all and (prefers-reduced-motion: no-preference) {
      .not-motion-safe\\:flex {
        display: flex;
      }
    }

    @media not all and (prefers-reduced-motion: reduce) {
      .not-motion-reduce\\:flex {
        display: flex;
      }
    }

    @media not all and (prefers-contrast: more) {
      .not-contrast-more\\:flex {
        display: flex;
      }
    }

    @media not all and (prefers-contrast: less) {
      .not-contrast-less\\:flex {
        display: flex;
      }
    }

    @media (min-width: 640px) {
      .not-max-sm\\:flex {
        display: flex;
      }
    }

    @media (min-width: 130px) {
      .not-max-\\[130px\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 130px) {
      .not-min-\\[130px\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 640px) {
      .not-min-sm\\:flex, .not-sm\\:flex {
        display: flex;
      }
    }

    @media not all and (orientation: portrait) {
      .not-portrait\\:flex {
        display: flex;
      }
    }

    @media not all and (orientation: landscape) {
      .not-landscape\\:flex {
        display: flex;
      }
    }

    .not-ltr\\:flex:not(:where(:dir(ltr), [dir="ltr"], [dir="ltr"] *)), .not-rtl\\:flex:not(:where(:dir(rtl), [dir="rtl"], [dir="rtl"] *)) {
      display: flex;
    }

    @media not all and (prefers-color-scheme: dark) {
      .not-dark\\:flex {
        display: flex;
      }
    }

    @media not print {
      .not-print\\:flex {
        display: flex;
      }
    }

    @media not all and (forced-colors: active) {
      .not-forced-colors\\:flex {
        display: flex;
      }
    }

    @media not all and (scripting: none) {
      .not-noscript\\:flex {
        display: flex;
      }
    }

    .not-hocus\\:flex:not(:hover, :focus), .not-device-hocus\\:flex:not(:hover, :focus) {
      display: flex;
    }

    @media not all and (hover: hover) {
      .not-device-hocus\\:flex {
        display: flex;
      }
    }

    .not-\\[\\:checked\\]\\:flex:not(:checked) {
      display: flex;
    }

    @media not all and (orientation: landscape) {
      .not-\\[\\@media_\\(orientation\\:landscape\\)\\]\\:flex {
        display: flex;
      }
    }

    @media (orientation: portrait) {
      .not-\\[\\@media_not_\\(orientation\\:portrait\\)\\]\\:flex {
        display: flex;
      }
    }

    @media not print {
      .not-\\[\\@media_print\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (orientation: portrait) {
      .not-\\[\\@media\\(orientation\\:portrait\\)\\]\\:flex {
        display: flex;
      }
    }

    .group-not-checked\\:flex:is(:where(.group):not(:checked) *), .group-not-hocus\\:flex:is(:where(.group):not(:hover, :focus) *), .group-not-hocus\\/parent-name\\:flex:is(:where(.group\\/parent-name):not(:hover, :focus) *), .group-not-\\[\\:checked\\]\\:flex:is(:where(.group):not(:checked) *), .group-not-\\[\\:checked\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name):not(:checked) *), .peer-not-checked\\:flex:is(:where(.peer):not(:checked) ~ *), .peer-not-hocus\\:flex:is(:where(.peer):not(:hover, :focus) ~ *), .peer-not-hocus\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):not(:hover, :focus) ~ *), .peer-not-\\[\\:checked\\]\\:flex:is(:where(.peer):not(:checked) ~ *), .peer-not-\\[\\:checked\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):not(:checked) ~ *) {
      display: flex;
    }"
  `)

  expect(
    await compileCss(
      css`
        @custom-variant nested-at-rules {
          @media foo {
            @media bar {
              @slot;
            }
          }
        }
        @custom-variant multiple-media-conditions {
          @media foo, bar {
            @slot;
          }
        }
        @custom-variant nested-style-rules {
          &:hover {
            &:focus {
              @slot;
            }
          }
        }
        @custom-variant parallel-style-rules {
          &:hover {
            @slot;
          }
          &:focus {
            @slot;
          }
        }
        @custom-variant parallel-at-rules {
          @media foo {
            @slot;
          }
          @media bar {
            @slot;
          }
        }
        @custom-variant parallel-mixed-rules {
          &:hover {
            @slot;
          }
          @media bar {
            @slot;
          }
        }
        @tailwind utilities;
      `,
      [
        'not-[>img]:flex',
        'not-[+img]:flex',
        'not-[~img]:flex',
        'not-[:checked]/foo:flex',
        'not-[@media_screen,print]:flex',
        'not-[@media_not_screen,print]:flex',
        'not-[@media_not_screen,not_print]:flex',

        'not-nested-at-rules:flex',
        'not-nested-style-rules:flex',
        'not-multiple-media-conditions:flex',
        'not-starting:flex',

        'not-parallel-style-rules:flex',
        'not-parallel-at-rules:flex',
        'not-parallel-mixed-rules:flex',

        // The following built-in variants don't have not-* versions because
        // there is no sensible negative version of them.

        // These just don't make sense as not-*
        'not-force:flex',
        'not-*:flex',

        // These contain pseudo-elements
        'not-first-letter:flex',
        'not-first-line:flex',
        'not-marker:flex',
        'not-selection:flex',
        'not-file:flex',
        'not-placeholder:flex',
        'not-backdrop:flex',
        'not-before:flex',
        'not-after:flex',

        // This is not a conditional at rule
        'not-starting:flex',
      ],
    ),
  ).toEqual('')
})

