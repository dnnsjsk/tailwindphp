/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('*', async () => {
  expect(await run(['*:flex'])).toMatchInlineSnapshot(`
    ":is(.\\*\\:flex > *) {
      display: flex;
    }"
  `)
  expect(await run(['*/foo:flex'])).toEqual('')
})

test('**', async () => {
  expect(await run(['**:flex'])).toMatchInlineSnapshot(`
    ":is(.\\*\\*\\:flex *) {
      display: flex;
    }"
  `)
  expect(await run(['**/foo:flex'])).toEqual('')
})

test('details-content', async () => {
  expect(await run(['details-content:flex'])).toMatchInlineSnapshot(`
    ".details-content\\:flex::details-content {
      display: flex;
    }"
  `)
  expect(await run(['details-content/foo:flex'])).toEqual('')
})

test('only', async () => {
  expect(await run(['only:flex', 'group-only:flex', 'peer-only:flex'])).toMatchInlineSnapshot(`
    ".group-only\\:flex:is(:where(.group):only-child *), .peer-only\\:flex:is(:where(.peer):only-child ~ *), .only\\:flex:only-child {
      display: flex;
    }"
  `)
  expect(await run(['only/foo:flex'])).toEqual('')
})

test('only-of-type', async () => {
  expect(await run(['only-of-type:flex', 'group-only-of-type:flex', 'peer-only-of-type:flex']))
    .toMatchInlineSnapshot(`
      ".group-only-of-type\\:flex:is(:where(.group):only-of-type *), .peer-only-of-type\\:flex:is(:where(.peer):only-of-type ~ *), .only-of-type\\:flex:only-of-type {
        display: flex;
      }"
    `)
  expect(await run(['only-of-type/foo:flex'])).toEqual('')
})

test('optional', async () => {
  expect(await run(['optional:flex', 'group-optional:flex', 'peer-optional:flex']))
    .toMatchInlineSnapshot(`
      ".group-optional\\:flex:is(:where(.group):optional *), .peer-optional\\:flex:is(:where(.peer):optional ~ *), .optional\\:flex:optional {
        display: flex;
      }"
    `)
  expect(await run(['optional/foo:flex'])).toEqual('')
})

test('user-valid', async () => {
  expect(await run(['user-valid:flex', 'group-user-valid:flex', 'peer-user-valid:flex']))
    .toMatchInlineSnapshot(`
      ".group-user-valid\\:flex:is(:where(.group):user-valid *), .peer-user-valid\\:flex:is(:where(.peer):user-valid ~ *) {
        display: flex;
      }

      .user-valid\\:flex:user-valid {
        display: flex;
      }"
    `)
  expect(await run(['user-valid/foo:flex'])).toEqual('')
})

test('user-invalid', async () => {
  expect(await run(['user-invalid:flex', 'group-user-invalid:flex', 'peer-user-invalid:flex']))
    .toMatchInlineSnapshot(`
      ".group-user-invalid\\:flex:is(:where(.group):user-invalid *), .peer-user-invalid\\:flex:is(:where(.peer):user-invalid ~ *) {
        display: flex;
      }

      .user-invalid\\:flex:user-invalid {
        display: flex;
      }"
    `)
  expect(await run(['invalid/foo:flex'])).toEqual('')
})

test('custom breakpoint', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --breakpoint-10xl: 5000px;
        }
        @tailwind utilities;
      `,
      ['10xl:flex'],
    ),
  ).toMatchInlineSnapshot(`
    "@media (min-width: 5000px) {
      .\\31 0xl\\:flex {
        display: flex;
      }
    }"
  `)
})

test('sorting stacked min-* and max-* variants', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          /* Explicitly ordered in a strange way */
          --breakpoint-sm: 640px;
          --breakpoint-lg: 1024px;
          --breakpoint-md: 768px;
          --breakpoint-xl: 1280px;
          --breakpoint-xs: 280px;
        }
        @tailwind utilities;
      `,
      ['min-sm:max-lg:flex', 'min-sm:max-xl:flex', 'min-md:max-lg:flex', 'min-xs:max-sm:flex'],
    ),
  ).toMatchInlineSnapshot(`
    "@media (min-width: 280px) {
      @media not all and (min-width: 640px) {
        .min-xs\\:max-sm\\:flex {
          display: flex;
        }
      }
    }

    @media (min-width: 640px) {
      @media not all and (min-width: 1280px) {
        .min-sm\\:max-xl\\:flex {
          display: flex;
        }
      }

      @media not all and (min-width: 1024px) {
        .min-sm\\:max-lg\\:flex {
          display: flex;
        }
      }
    }

    @media (min-width: 768px) {
      @media not all and (min-width: 1024px) {
        .min-md\\:max-lg\\:flex {
          display: flex;
        }
      }
    }"
  `)
})

test('stacked min-* and max-* variants should come after unprefixed variants', async () => {
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
      ['sm:flex', 'min-sm:max-lg:flex', 'md:flex', 'min-md:max-lg:flex'],
    ),
  ).toMatchInlineSnapshot(`
    "@media (min-width: 640px) {
      .sm\\:flex {
        display: flex;
      }

      @media not all and (min-width: 1024px) {
        .min-sm\\:max-lg\\:flex {
          display: flex;
        }
      }
    }

    @media (min-width: 768px) {
      .md\\:flex {
        display: flex;
      }

      @media not all and (min-width: 1024px) {
        .min-md\\:max-lg\\:flex {
          display: flex;
        }
      }
    }"
  `)
})

test('sorting `min` and `max` should sort by unit, then by value, then alphabetically', async () => {
  expect(
    await run([
      'min-[10px]:flex',
      'min-[12px]:flex',
      'min-[10em]:flex',
      'min-[12em]:flex',
      'min-[10rem]:flex',
      'min-[12rem]:flex',
      'max-[10px]:flex',
      'max-[12px]:flex',
      'max-[10em]:flex',
      'max-[12em]:flex',
      'max-[10rem]:flex',
      'max-[12rem]:flex',
      'min-[calc(1000px+12em)]:flex',
      'max-[calc(1000px+12em)]:flex',
      'min-[calc(50vh+12em)]:flex',
      'max-[calc(50vh+12em)]:flex',
      'min-[10vh]:flex',
      'min-[12vh]:flex',
      'max-[10vh]:flex',
      'max-[12vh]:flex',
    ]),
  ).toMatchInlineSnapshot(`
    "@media not all and (min-width: calc(1000px + 12em)) {
      .max-\\[calc\\(1000px\\+12em\\)\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: calc(50vh + 12em)) {
      .max-\\[calc\\(50vh\\+12em\\)\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 12em) {
      .max-\\[12em\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 10em) {
      .max-\\[10em\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 12px) {
      .max-\\[12px\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 10px) {
      .max-\\[10px\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 12rem) {
      .max-\\[12rem\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 10rem) {
      .max-\\[10rem\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 12vh) {
      .max-\\[12vh\\]\\:flex {
        display: flex;
      }
    }

    @media not all and (min-width: 10vh) {
      .max-\\[10vh\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: calc(1000px + 12em)) {
      .min-\\[calc\\(1000px\\+12em\\)\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: calc(50vh + 12em)) {
      .min-\\[calc\\(50vh\\+12em\\)\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 10em) {
      .min-\\[10em\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 12em) {
      .min-\\[12em\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 10px) {
      .min-\\[10px\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 12px) {
      .min-\\[12px\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 10rem) {
      .min-\\[10rem\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 12rem) {
      .min-\\[12rem\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 10vh) {
      .min-\\[10vh\\]\\:flex {
        display: flex;
      }
    }

    @media (min-width: 12vh) {
      .min-\\[12vh\\]\\:flex {
        display: flex;
      }
    }"
  `)
})

test('in', async () => {
  expect(
    await run([
      'in-[p]:flex',
      'in-[.group]:flex',
      'not-in-[p]:flex',
      'not-in-[.group]:flex',
      'in-data-visible:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".not-in-\\[\\.group\\]\\:flex:not(:where(.group) *), .not-in-\\[p\\]\\:flex:not(:where(:is(p)) *), :where([data-visible]) .in-data-visible\\:flex, :where(.group) .in-\\[\\.group\\]\\:flex, :where(:is(p)) .in-\\[p\\]\\:flex {
      display: flex;
    }"
  `)
  expect(await run(['in-p:flex', 'in-foo-bar:flex'])).toEqual('')
})

test('contrast-more', async () => {
  expect(await run(['contrast-more:flex'])).toMatchInlineSnapshot(`
    "@media (prefers-contrast: more) {
      .contrast-more\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['contrast-more/foo:flex'])).toEqual('')
})

test('contrast-less', async () => {
  expect(await run(['contrast-less:flex'])).toMatchInlineSnapshot(`
    "@media (prefers-contrast: less) {
      .contrast-less\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['contrast-less/foo:flex'])).toEqual('')
})

test('forced-colors', async () => {
  expect(await run(['forced-colors:flex'])).toMatchInlineSnapshot(`
    "@media (forced-colors: active) {
      .forced-colors\\:flex {
        display: flex;
      }
    }"
  `)
  expect(await run(['forced-colors/foo:flex'])).toEqual('')
})

test('inverted-colors', async () => {
  expect(await run(['inverted-colors:flex'])).toMatchInlineSnapshot(`
    "@media (inverted-colors: inverted) {
      .inverted-colors\\:flex {
        display: flex;
      }
    }"
  `)
})

test('pointer-none', async () => {
  expect(await run(['pointer-none:flex'])).toMatchInlineSnapshot(`
    "@media (pointer: none) {
      .pointer-none\\:flex {
        display: flex;
      }
    }"
  `)
})

test('pointer-coarse', async () => {
  expect(await run(['pointer-coarse:flex'])).toMatchInlineSnapshot(`
    "@media (pointer: coarse) {
      .pointer-coarse\\:flex {
        display: flex;
      }
    }"
  `)
})

test('pointer-fine', async () => {
  expect(await run(['pointer-fine:flex'])).toMatchInlineSnapshot(`
    "@media (pointer: fine) {
      .pointer-fine\\:flex {
        display: flex;
      }
    }"
  `)
})

test('any-pointer-none', async () => {
  expect(await run(['any-pointer-none:flex'])).toMatchInlineSnapshot(`
    "@media (any-pointer: none) {
      .any-pointer-none\\:flex {
        display: flex;
      }
    }"
  `)
})

test('any-pointer-coarse', async () => {
  expect(await run(['any-pointer-coarse:flex'])).toMatchInlineSnapshot(`
    "@media (any-pointer: coarse) {
      .any-pointer-coarse\\:flex {
        display: flex;
      }
    }"
  `)
})

test('any-pointer-fine', async () => {
  expect(await run(['any-pointer-fine:flex'])).toMatchInlineSnapshot(`
    "@media (any-pointer: fine) {
      .any-pointer-fine\\:flex {
        display: flex;
      }
    }"
  `)
})

test('scripting-none', async () => {
  expect(await run(['noscript:flex'])).toMatchInlineSnapshot(`
    "@media (scripting: none) {
      .noscript\\:flex {
        display: flex;
      }
    }"
  `)
})

test('nth', async () => {
  expect(
    await run([
      'nth-3:flex',
      'nth-[2n+1]:flex',
      'nth-[2n+1_of_.foo]:flex',
      'nth-last-3:flex',
      'nth-last-[2n+1]:flex',
      'nth-last-[2n+1_of_.foo]:flex',
      'nth-of-type-3:flex',
      'nth-of-type-[2n+1]:flex',
      'nth-last-of-type-3:flex',
      'nth-last-of-type-[2n+1]:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".nth-3\\:flex:nth-child(3), .nth-\\[2n\\+1\\]\\:flex:nth-child(odd), .nth-\\[2n\\+1_of_\\.foo\\]\\:flex:nth-child(odd of .foo), .nth-last-3\\:flex:nth-last-child(3), .nth-last-\\[2n\\+1\\]\\:flex:nth-last-child(odd), .nth-last-\\[2n\\+1_of_\\.foo\\]\\:flex:nth-last-child(odd of .foo), .nth-of-type-3\\:flex:nth-of-type(3), .nth-of-type-\\[2n\\+1\\]\\:flex:nth-of-type(odd), .nth-last-of-type-3\\:flex:nth-last-of-type(3), .nth-last-of-type-\\[2n\\+1\\]\\:flex:nth-last-of-type(odd) {
      display: flex;
    }"
  `)

  expect(
    await run([
      'nth-foo:flex',
      'nth-of-type-foo:flex',
      'nth-last-foo:flex',
      'nth-last-of-type-foo:flex',
    ]),
  ).toEqual('')
  expect(
    await run([
      'nth--3:flex',
      'nth-3/foo:flex',
      'nth-[2n+1]/foo:flex',
      'nth-[2n+1_of_.foo]/foo:flex',
      'nth-last--3:flex',
      'nth-last-3/foo:flex',
      'nth-last-[2n+1]/foo:flex',
      'nth-last-[2n+1_of_.foo]/foo:flex',
      'nth-of-type--3:flex',
      'nth-of-type-3/foo:flex',
      'nth-of-type-[2n+1]/foo:flex',
      'nth-last-of-type--3:flex',
      'nth-last-of-type-3/foo:flex',
      'nth-last-of-type-[2n+1]/foo:flex',
    ]),
  ).toEqual('')
})

test('container queries', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --container-lg: 1024px;
          --container-foo-bar: 1440px;
        }
        @tailwind utilities;
      `,
      [
        '@lg:flex',
        '@lg/name:flex',
        '@[123px]:flex',
        '@[456px]/name:flex',
        '@foo-bar:flex',
        '@foo-bar/name:flex',

        '@min-lg:flex',
        '@min-lg/name:flex',
        '@min-[123px]:flex',
        '@min-[456px]/name:flex',
        '@min-foo-bar:flex',
        '@min-foo-bar/name:flex',

        '@max-lg:flex',
        '@max-lg/name:flex',
        '@max-[123px]:flex',
        '@max-[456px]/name:flex',
        '@max-foo-bar:flex',
        '@max-foo-bar/name:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@container name not (min-width: 1440px) {
      .\\@max-foo-bar\\/name\\:flex {
        display: flex;
      }
    }

    @container not (min-width: 1440px) {
      .\\@max-foo-bar\\:flex {
        display: flex;
      }
    }

    @container name not (min-width: 1024px) {
      .\\@max-lg\\/name\\:flex {
        display: flex;
      }
    }

    @container not (min-width: 1024px) {
      .\\@max-lg\\:flex {
        display: flex;
      }
    }

    @container name not (min-width: 456px) {
      .\\@max-\\[456px\\]\\/name\\:flex {
        display: flex;
      }
    }

    @container not (min-width: 123px) {
      .\\@max-\\[123px\\]\\:flex {
        display: flex;
      }
    }

    @container (min-width: 123px) {
      .\\@\\[123px\\]\\:flex, .\\@min-\\[123px\\]\\:flex {
        display: flex;
      }
    }

    @container name (min-width: 456px) {
      .\\@\\[456px\\]\\/name\\:flex, .\\@min-\\[456px\\]\\/name\\:flex {
        display: flex;
      }
    }

    @container name (min-width: 1024px) {
      .\\@lg\\/name\\:flex {
        display: flex;
      }
    }

    @container (min-width: 1024px) {
      .\\@lg\\:flex {
        display: flex;
      }
    }

    @container name (min-width: 1024px) {
      .\\@min-lg\\/name\\:flex {
        display: flex;
      }
    }

    @container (min-width: 1024px) {
      .\\@min-lg\\:flex {
        display: flex;
      }
    }

    @container name (min-width: 1440px) {
      .\\@foo-bar\\/name\\:flex {
        display: flex;
      }
    }

    @container (min-width: 1440px) {
      .\\@foo-bar\\:flex {
        display: flex;
      }
    }

    @container name (min-width: 1440px) {
      .\\@min-foo-bar\\/name\\:flex {
        display: flex;
      }
    }

    @container (min-width: 1440px) {
      .\\@min-foo-bar\\:flex {
        display: flex;
      }
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --container-lg: 1024px;
          --container-foo-bar: 1440px;
        }
        @tailwind utilities;
      `,
      [
        '@-lg:flex',
        '@-lg/name:flex',
        '@-[123px]:flex',
        '@-[456px]/name:flex',
        '@-foo-bar:flex',
        '@-foo-bar/name:flex',

        '@-min-lg:flex',
        '@-min-lg/name:flex',
        '@-min-[123px]:flex',
        '@-min-[456px]/name:flex',
        '@-min-foo-bar:flex',
        '@-min-foo-bar/name:flex',

        '@-max-lg:flex',
        '@-max-lg/name:flex',
        '@-max-[123px]:flex',
        '@-max-[456px]/name:flex',
        '@-max-foo-bar:flex',
        '@-max-foo-bar/name:flex',
      ],
    ),
  ).toEqual('')
})

test('variant order', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --breakpoint-sm: 640px;
          --breakpoint-md: 768px;
          --breakpoint-lg: 1024px;
          --breakpoint-xl: 1280px;
          --breakpoint-2xl: 1536px;
        }
        @tailwind utilities;
      `,
      [
        '[&_p]:flex',
        '2xl:flex',
        'active:flex',
        'after:flex',
        'aria-[custom=true]:flex',
        'aria-busy:flex',
        'aria-checked:flex',
        'aria-disabled:flex',
        'aria-expanded:flex',
        'aria-hidden:flex',
        'aria-pressed:flex',
        'aria-readonly:flex',
        'aria-required:flex',
        'aria-selected:flex',
        'autofill:flex',
        'backdrop:flex',
        'before:flex',
        'checked:flex',
        'contrast-less:flex',
        'contrast-more:flex',
        'dark:flex',
        'data-custom:flex',
        'data-[custom=true]:flex',
        'default:flex',
        'details-content:flex',
        'disabled:flex',
        'empty:flex',
        'enabled:flex',
        'even:flex',
        'file:flex',
        'first-letter:flex',
        'first-line:flex',
        'first-of-type:flex',
        'first:flex',
        'focus-visible:flex',
        'focus-within:flex',
        'focus:flex',
        'forced-colors:flex',
        'group-hover:flex',
        'has-[:hover]:flex',
        'hover:flex',
        'in-range:flex',
        'indeterminate:flex',
        'invalid:flex',
        'landscape:flex',
        'last-of-type:flex',
        'last:flex',
        'lg:flex',
        'ltr:flex',
        'marker:flex',
        'md:flex',
        'motion-reduce:flex',
        'motion-safe:flex',
        'odd:flex',
        'only-of-type:flex',
        'only:flex',
        'open:flex',
        'optional:flex',
        'out-of-range:flex',
        'peer-hover:flex',
        'placeholder-shown:flex',
        'placeholder:flex',
        'portrait:flex',
        'print:flex',
        'read-only:flex',
        'required:flex',
        'rtl:flex',
        'selection:flex',
        'sm:flex',
        'supports-[display:flex]:flex',
        'target:flex',
        'valid:flex',
        'visited:flex',
        'xl:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-content: "";
        }
      }
    }

    @media (hover: hover) {
      .group-hover\\:flex:is(:where(.group):hover *), .peer-hover\\:flex:is(:where(.peer):hover ~ *) {
        display: flex;
      }
    }

    .first-letter\\:flex:first-letter, .first-line\\:flex:first-line {
      display: flex;
    }

    .marker\\:flex ::marker {
      display: flex;
    }

    .marker\\:flex::marker {
      display: flex;
    }

    .marker\\:flex ::-webkit-details-marker {
      display: flex;
    }

    .marker\\:flex::-webkit-details-marker {
      display: flex;
    }

    .selection\\:flex ::selection {
      display: flex;
    }

    .selection\\:flex::selection {
      display: flex;
    }

    .file\\:flex::file-selector-button {
      display: flex;
    }

    .placeholder\\:flex::placeholder, .backdrop\\:flex::backdrop {
      display: flex;
    }

    .details-content\\:flex::details-content {
      display: flex;
    }

    .before\\:flex:before, .after\\:flex:after {
      content: var(--tw-content);
      display: flex;
    }

    .first\\:flex:first-child, .last\\:flex:last-child, .only\\:flex:only-child, .odd\\:flex:nth-child(odd), .even\\:flex:nth-child(2n), .first-of-type\\:flex:first-of-type, .last-of-type\\:flex:last-of-type, .only-of-type\\:flex:only-of-type, .visited\\:flex:visited, .target\\:flex:target, .open\\:flex:is([open], :popover-open, :open), .default\\:flex:default, .checked\\:flex:checked, .indeterminate\\:flex:indeterminate, .placeholder-shown\\:flex:placeholder-shown, .autofill\\:flex:autofill, .optional\\:flex:optional, .required\\:flex:required, .valid\\:flex:valid, .invalid\\:flex:invalid, .in-range\\:flex:in-range, .out-of-range\\:flex:out-of-range, .read-only\\:flex:read-only, .empty\\:flex:empty, .focus-within\\:flex:focus-within {
      display: flex;
    }

    @media (hover: hover) {
      .hover\\:flex:hover {
        display: flex;
      }
    }

    .focus\\:flex:focus, .focus-visible\\:flex:focus-visible, .active\\:flex:active, .enabled\\:flex:enabled, .disabled\\:flex:disabled, .has-\\[\\:hover\\]\\:flex:has(:hover), .aria-busy\\:flex[aria-busy="true"], .aria-checked\\:flex[aria-checked="true"], .aria-disabled\\:flex[aria-disabled="true"], .aria-expanded\\:flex[aria-expanded="true"], .aria-hidden\\:flex[aria-hidden="true"], .aria-pressed\\:flex[aria-pressed="true"], .aria-readonly\\:flex[aria-readonly="true"], .aria-required\\:flex[aria-required="true"], .aria-selected\\:flex[aria-selected="true"], .aria-\\[custom\\=true\\]\\:flex[aria-custom="true"], .data-custom\\:flex[data-custom], .data-\\[custom\\=true\\]\\:flex[data-custom="true"] {
      display: flex;
    }

    @supports (display: flex) {
      .supports-\\[display\\:flex\\]\\:flex {
        display: flex;
      }
    }

    @media (prefers-reduced-motion: no-preference) {
      .motion-safe\\:flex {
        display: flex;
      }
    }

    @media (prefers-reduced-motion: reduce) {
      .motion-reduce\\:flex {
        display: flex;
      }
    }

    @media (prefers-contrast: more) {
      .contrast-more\\:flex {
        display: flex;
      }
    }

    @media (prefers-contrast: less) {
      .contrast-less\\:flex {
        display: flex;
      }
    }

    @media (min-width: 640px) {
      .sm\\:flex {
        display: flex;
      }
    }

    @media (min-width: 768px) {
      .md\\:flex {
        display: flex;
      }
    }

    @media (min-width: 1024px) {
      .lg\\:flex {
        display: flex;
      }
    }

    @media (min-width: 1280px) {
      .xl\\:flex {
        display: flex;
      }
    }

    @media (min-width: 1536px) {
      .\\32 xl\\:flex {
        display: flex;
      }
    }

    @media (orientation: portrait) {
      .portrait\\:flex {
        display: flex;
      }
    }

    @media (orientation: landscape) {
      .landscape\\:flex {
        display: flex;
      }
    }

    .ltr\\:flex:where(:dir(ltr), [dir="ltr"], [dir="ltr"] *), .rtl\\:flex:where(:dir(rtl), [dir="rtl"], [dir="rtl"] *) {
      display: flex;
    }

    @media (prefers-color-scheme: dark) {
      .dark\\:flex {
        display: flex;
      }
    }

    @media print {
      .print\\:flex {
        display: flex;
      }
    }

    @media (forced-colors: active) {
      .forced-colors\\:flex {
        display: flex;
      }
    }

    .\\[\\&_p\\]\\:flex p {
      display: flex;
    }

    @property --tw-content {
      syntax: "*";
      inherits: false;
      initial-value: "";
    }"
  `)
})

