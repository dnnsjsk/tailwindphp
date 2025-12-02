/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('first-letter', async () => {
  expect(await run(['first-letter:flex'])).toMatchInlineSnapshot(`
    ".first-letter\\:flex:first-letter {
      display: flex;
    }"
  `)
  expect(await run(['first-letter/foo:flex'])).toEqual('')
})

test('first-line', async () => {
  expect(await run(['first-line:flex'])).toMatchInlineSnapshot(`
    ".first-line\\:flex:first-line {
      display: flex;
    }"
  `)
  expect(await run(['first-line/foo:flex'])).toEqual('')
})

test('first', async () => {
  expect(await run(['first:flex', 'group-first:flex', 'peer-first:flex'])).toMatchInlineSnapshot(`
    ".group-first\\:flex:is(:where(.group):first-child *), .peer-first\\:flex:is(:where(.peer):first-child ~ *), .first\\:flex:first-child {
      display: flex;
    }"
  `)
  expect(await run(['first/foo:flex'])).toEqual('')
})

test('last', async () => {
  expect(await run(['last:flex', 'group-last:flex', 'peer-last:flex'])).toMatchInlineSnapshot(`
    ".group-last\\:flex:is(:where(.group):last-child *), .peer-last\\:flex:is(:where(.peer):last-child ~ *), .last\\:flex:last-child {
      display: flex;
    }"
  `)
  expect(await run(['last/foo:flex'])).toEqual('')
})

test('odd', async () => {
  expect(await run(['odd:flex', 'group-odd:flex', 'peer-odd:flex'])).toMatchInlineSnapshot(`
    ".group-odd\\:flex:is(:where(.group):nth-child(odd) *), .peer-odd\\:flex:is(:where(.peer):nth-child(odd) ~ *), .odd\\:flex:nth-child(odd) {
      display: flex;
    }"
  `)
  expect(await run(['odd/foo:flex'])).toEqual('')
})

test('even', async () => {
  expect(await run(['even:flex', 'group-even:flex', 'peer-even:flex'])).toMatchInlineSnapshot(`
    ".group-even\\:flex:is(:where(.group):nth-child(2n) *), .peer-even\\:flex:is(:where(.peer):nth-child(2n) ~ *), .even\\:flex:nth-child(2n) {
      display: flex;
    }"
  `)
  expect(await run(['even/foo:flex'])).toEqual('')
})

test('first-of-type', async () => {
  expect(await run(['first-of-type:flex', 'group-first-of-type:flex', 'peer-first-of-type:flex']))
    .toMatchInlineSnapshot(`
      ".group-first-of-type\\:flex:is(:where(.group):first-of-type *), .peer-first-of-type\\:flex:is(:where(.peer):first-of-type ~ *), .first-of-type\\:flex:first-of-type {
        display: flex;
      }"
    `)
  expect(await run(['first-of-type/foo:flex'])).toEqual('')
})

test('last-of-type', async () => {
  expect(await run(['last-of-type:flex', 'group-last-of-type:flex', 'peer-last-of-type:flex']))
    .toMatchInlineSnapshot(`
      ".group-last-of-type\\:flex:is(:where(.group):last-of-type *), .peer-last-of-type\\:flex:is(:where(.peer):last-of-type ~ *), .last-of-type\\:flex:last-of-type {
        display: flex;
      }"
    `)
  expect(await run(['last-of-type/foo:flex'])).toEqual('')
})

test('visited', async () => {
  expect(await run(['visited:flex', 'group-visited:flex', 'peer-visited:flex']))
    .toMatchInlineSnapshot(`
      ".group-visited\\:flex:is(:where(.group):visited *), .peer-visited\\:flex:is(:where(.peer):visited ~ *), .visited\\:flex:visited {
        display: flex;
      }"
    `)
  expect(await run(['visited/foo:flex'])).toEqual('')
})

test('target', async () => {
  expect(await run(['target:flex', 'group-target:flex', 'peer-target:flex']))
    .toMatchInlineSnapshot(`
      ".group-target\\:flex:is(:where(.group):target *), .peer-target\\:flex:is(:where(.peer):target ~ *), .target\\:flex:target {
        display: flex;
      }"
    `)
  expect(await run(['target/foo:flex'])).toEqual('')
})

test('default', async () => {
  expect(await run(['default:flex', 'group-default:flex', 'peer-default:flex']))
    .toMatchInlineSnapshot(`
      ".group-default\\:flex:is(:where(.group):default *), .peer-default\\:flex:is(:where(.peer):default ~ *), .default\\:flex:default {
        display: flex;
      }"
    `)
  expect(await run(['default/foo:flex'])).toEqual('')
})

test('checked', async () => {
  expect(await run(['checked:flex', 'group-checked:flex', 'peer-checked:flex']))
    .toMatchInlineSnapshot(`
      ".group-checked\\:flex:is(:where(.group):checked *), .peer-checked\\:flex:is(:where(.peer):checked ~ *), .checked\\:flex:checked {
        display: flex;
      }"
    `)
  expect(await run(['checked/foo:flex'])).toEqual('')
})

test('indeterminate', async () => {
  expect(await run(['indeterminate:flex', 'group-indeterminate:flex', 'peer-indeterminate:flex']))
    .toMatchInlineSnapshot(`
      ".group-indeterminate\\:flex:is(:where(.group):indeterminate *), .peer-indeterminate\\:flex:is(:where(.peer):indeterminate ~ *), .indeterminate\\:flex:indeterminate {
        display: flex;
      }"
    `)
  expect(await run(['indeterminate/foo:flex'])).toEqual('')
})

test('placeholder-shown', async () => {
  expect(
    await run([
      'placeholder-shown:flex',
      'group-placeholder-shown:flex',
      'peer-placeholder-shown:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".group-placeholder-shown\\:flex:is(:where(.group):placeholder-shown *), .peer-placeholder-shown\\:flex:is(:where(.peer):placeholder-shown ~ *), .placeholder-shown\\:flex:placeholder-shown {
      display: flex;
    }"
  `)
  expect(await run(['placeholder-shown/foo:flex'])).toEqual('')
})

test('autofill', async () => {
  expect(await run(['autofill:flex', 'group-autofill:flex', 'peer-autofill:flex']))
    .toMatchInlineSnapshot(`
      ".group-autofill\\:flex:is(:where(.group):autofill *), .peer-autofill\\:flex:is(:where(.peer):autofill ~ *), .autofill\\:flex:autofill {
        display: flex;
      }"
    `)
  expect(await run(['autofill/foo:flex'])).toEqual('')
})

test('required', async () => {
  expect(await run(['required:flex', 'group-required:flex', 'peer-required:flex']))
    .toMatchInlineSnapshot(`
      ".group-required\\:flex:is(:where(.group):required *), .peer-required\\:flex:is(:where(.peer):required ~ *), .required\\:flex:required {
        display: flex;
      }"
    `)
  expect(await run(['required/foo:flex'])).toEqual('')
})

test('valid', async () => {
  expect(await run(['valid:flex', 'group-valid:flex', 'peer-valid:flex'])).toMatchInlineSnapshot(`
    ".group-valid\\:flex:is(:where(.group):valid *), .peer-valid\\:flex:is(:where(.peer):valid ~ *), .valid\\:flex:valid {
      display: flex;
    }"
  `)
  expect(await run(['valid/foo:flex'])).toEqual('')
})

test('invalid', async () => {
  expect(await run(['invalid:flex', 'group-invalid:flex', 'peer-invalid:flex']))
    .toMatchInlineSnapshot(`
      ".group-invalid\\:flex:is(:where(.group):invalid *), .peer-invalid\\:flex:is(:where(.peer):invalid ~ *), .invalid\\:flex:invalid {
        display: flex;
      }"
    `)
  expect(await run(['invalid/foo:flex'])).toEqual('')
})

test('in-range', async () => {
  expect(await run(['in-range:flex', 'group-in-range:flex', 'peer-in-range:flex']))
    .toMatchInlineSnapshot(`
      ".group-in-range\\:flex:is(:where(.group):in-range *), .peer-in-range\\:flex:is(:where(.peer):in-range ~ *), .in-range\\:flex:in-range {
        display: flex;
      }"
    `)
  expect(await run(['in-range/foo:flex'])).toEqual('')
})

test('out-of-range', async () => {
  expect(await run(['out-of-range:flex', 'group-out-of-range:flex', 'peer-out-of-range:flex']))
    .toMatchInlineSnapshot(`
      ".group-out-of-range\\:flex:is(:where(.group):out-of-range *), .peer-out-of-range\\:flex:is(:where(.peer):out-of-range ~ *), .out-of-range\\:flex:out-of-range {
        display: flex;
      }"
    `)
  expect(await run(['out-of-range/foo:flex'])).toEqual('')
})

test('read-only', async () => {
  expect(await run(['read-only:flex', 'group-read-only:flex', 'peer-read-only:flex']))
    .toMatchInlineSnapshot(`
      ".group-read-only\\:flex:is(:where(.group):read-only *), .peer-read-only\\:flex:is(:where(.peer):read-only ~ *), .read-only\\:flex:read-only {
        display: flex;
      }"
    `)
  expect(await run(['read-only/foo:flex'])).toEqual('')
})

test('empty', async () => {
  expect(await run(['empty:flex', 'group-empty:flex', 'peer-empty:flex'])).toMatchInlineSnapshot(`
    ".group-empty\\:flex:is(:where(.group):empty *), .peer-empty\\:flex:is(:where(.peer):empty ~ *), .empty\\:flex:empty {
      display: flex;
    }"
  `)
  expect(await run(['empty/foo:flex'])).toEqual('')
})

test('focus-within', async () => {
  expect(await run(['focus-within:flex', 'group-focus-within:flex', 'peer-focus-within:flex']))
    .toMatchInlineSnapshot(`
      ".group-focus-within\\:flex:is(:where(.group):focus-within *), .peer-focus-within\\:flex:is(:where(.peer):focus-within ~ *), .focus-within\\:flex:focus-within {
        display: flex;
      }"
    `)
  expect(await run(['focus-within/foo:flex'])).toEqual('')
})

test('hover', async () => {
  expect(await run(['hover:flex', 'group-hover:flex', 'peer-hover:flex'])).toMatchInlineSnapshot(`
    "@media (hover: hover) {
      .group-hover\\:flex:is(:where(.group):hover *), .peer-hover\\:flex:is(:where(.peer):hover ~ *), .hover\\:flex:hover {
        display: flex;
      }
    }"
  `)
  expect(await run(['hover/foo:flex'])).toEqual('')
})

test('focus', async () => {
  expect(await run(['focus:flex', 'group-focus:flex', 'peer-focus:flex'])).toMatchInlineSnapshot(`
    ".group-focus\\:flex:is(:where(.group):focus *), .peer-focus\\:flex:is(:where(.peer):focus ~ *), .focus\\:flex:focus {
      display: flex;
    }"
  `)
  expect(await run(['focus/foo:flex'])).toEqual('')
})

test('group-hover group-focus sorting', async () => {
  expect(await run(['group-hover:flex', 'group-focus:flex'])).toMatchInlineSnapshot(`
    "@media (hover: hover) {
      .group-hover\\:flex:is(:where(.group):hover *) {
        display: flex;
      }
    }

    .group-focus\\:flex:is(:where(.group):focus *) {
      display: flex;
    }"
  `)
  expect(await run(['group-focus:flex', 'group-hover:flex'])).toMatchInlineSnapshot(`
    "@media (hover: hover) {
      .group-hover\\:flex:is(:where(.group):hover *) {
        display: flex;
      }
    }

    .group-focus\\:flex:is(:where(.group):focus *) {
      display: flex;
    }"
  `)
})

test('focus-visible', async () => {
  expect(await run(['focus-visible:flex', 'group-focus-visible:flex', 'peer-focus-visible:flex']))
    .toMatchInlineSnapshot(`
      ".group-focus-visible\\:flex:is(:where(.group):focus-visible *), .peer-focus-visible\\:flex:is(:where(.peer):focus-visible ~ *), .focus-visible\\:flex:focus-visible {
        display: flex;
      }"
    `)
  expect(await run(['focus-visible/foo:flex'])).toEqual('')
})

test('active', async () => {
  expect(await run(['active:flex', 'group-active:flex', 'peer-active:flex']))
    .toMatchInlineSnapshot(`
      ".group-active\\:flex:is(:where(.group):active *), .peer-active\\:flex:is(:where(.peer):active ~ *), .active\\:flex:active {
        display: flex;
      }"
    `)
  expect(await run(['active/foo:flex'])).toEqual('')
})

test('enabled', async () => {
  expect(await run(['enabled:flex', 'group-enabled:flex', 'peer-enabled:flex']))
    .toMatchInlineSnapshot(`
      ".group-enabled\\:flex:is(:where(.group):enabled *), .peer-enabled\\:flex:is(:where(.peer):enabled ~ *), .enabled\\:flex:enabled {
        display: flex;
      }"
    `)
  expect(await run(['enabled/foo:flex'])).toEqual('')
})

test('disabled', async () => {
  expect(await run(['disabled:flex', 'group-disabled:flex', 'peer-disabled:flex']))
    .toMatchInlineSnapshot(`
      ".group-disabled\\:flex:is(:where(.group):disabled *), .peer-disabled\\:flex:is(:where(.peer):disabled ~ *), .disabled\\:flex:disabled {
        display: flex;
      }"
    `)
  expect(await run(['disabled/foo:flex'])).toEqual('')
})

test('group-[...]', async () => {
  expect(
    await run([
      'group-[&_p]:flex',
      'group-[&_p]:hover:flex',
      'hover:group-[&_p]:flex',
      'hover:group-[&_p]:hover:flex',
      'group-[&:hover]:group-[&_p]:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".group-\\[\\&_p\\]\\:flex:is(:where(.group) p *), .group-\\[\\&\\:hover\\]\\:group-\\[\\&_p\\]\\:flex:is(:where(.group):hover *):is(:where(.group) p *) {
      display: flex;
    }

    @media (hover: hover) {
      .group-\\[\\&_p\\]\\:hover\\:flex:is(:where(.group) p *):hover, .hover\\:group-\\[\\&_p\\]\\:flex:hover:is(:where(.group) p *) {
        display: flex;
      }

      @media (hover: hover) {
        .hover\\:group-\\[\\&_p\\]\\:hover\\:flex:hover:is(:where(.group) p *):hover {
          display: flex;
        }
      }
    }"
  `)

  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      ['group-[]:flex', 'group-hover/[]:flex', 'group-[@media_foo]:flex', 'group-[>img]:flex'],
    ),
  ).toEqual('')
})

test('group-*', async () => {
  expect(
    await compileCss(
      css`
        @custom-variant hocus {
          &:hover,
          &:focus {
            @slot;
          }
        }
        @tailwind utilities;
      `,
      [
        'group-hover:flex',
        'group-focus:flex',
        'group-hocus:flex',

        'group-hover:group-focus:flex',
        'group-focus:group-hover:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@media (hover: hover) {
      .group-hover\\:flex:is(:where(.group):hover *) {
        display: flex;
      }
    }

    .group-focus\\:flex:is(:where(.group):focus *) {
      display: flex;
    }

    @media (hover: hover) {
      .group-focus\\:group-hover\\:flex:is(:where(.group):focus *):is(:where(.group):hover *), .group-hover\\:group-focus\\:flex:is(:where(.group):hover *):is(:where(.group):focus *) {
        display: flex;
      }
    }

    .group-hocus\\:flex:is(:is(:where(.group):hover, :where(.group):focus) *) {
      display: flex;
    }"
  `)

  expect(
    await compileCss(
      css`
        @custom-variant custom-at-rule (@media foo);
        @custom-variant nested-selectors {
          &:hover {
            &:focus {
              @slot;
            }
          }
        }
        @tailwind utilities;
      `,
      ['group-custom-at-rule:flex', 'group-nested-selectors:flex'],
    ),
  ).toEqual('')
})

test('peer-[...]', async () => {
  expect(
    await run([
      'peer-[&_p]:flex',
      'peer-[&_p]:hover:flex',
      'hover:peer-[&_p]:flex',
      'hover:peer-[&_p]:focus:flex',
      'peer-[&:hover]:peer-[&_p]:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".peer-\\[\\&_p\\]\\:flex:is(:where(.peer) p ~ *), .peer-\\[\\&\\:hover\\]\\:peer-\\[\\&_p\\]\\:flex:is(:where(.peer):hover ~ *):is(:where(.peer) p ~ *) {
      display: flex;
    }

    @media (hover: hover) {
      .hover\\:peer-\\[\\&_p\\]\\:flex:hover:is(:where(.peer) p ~ *), .peer-\\[\\&_p\\]\\:hover\\:flex:is(:where(.peer) p ~ *):hover, .hover\\:peer-\\[\\&_p\\]\\:focus\\:flex:hover:is(:where(.peer) p ~ *):focus {
        display: flex;
      }
    }"
  `)

  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      ['peer-[]:flex', 'peer-hover/[]:flex', 'peer-[@media_foo]:flex', 'peer-[>img]:flex'],
    ),
  ).toEqual('')
})

test('peer-*', async () => {
  expect(
    await compileCss(
      css`
        @custom-variant hocus {
          &:hover,
          &:focus {
            @slot;
          }
        }
        @tailwind utilities;
      `,
      [
        'peer-hover:flex',
        'peer-focus:flex',
        'peer-hocus:flex',
        'peer-hover:peer-focus:flex',
        'peer-focus:peer-hover:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@media (hover: hover) {
      .peer-hover\\:flex:is(:where(.peer):hover ~ *) {
        display: flex;
      }
    }

    .peer-focus\\:flex:is(:where(.peer):focus ~ *) {
      display: flex;
    }

    @media (hover: hover) {
      .peer-focus\\:peer-hover\\:flex:is(:where(.peer):focus ~ *):is(:where(.peer):hover ~ *), .peer-hover\\:peer-focus\\:flex:is(:where(.peer):hover ~ *):is(:where(.peer):focus ~ *) {
        display: flex;
      }
    }

    .peer-hocus\\:flex:is(:is(:where(.peer):hover, :where(.peer):focus) ~ *) {
      display: flex;
    }"
  `)

  expect(
    await compileCss(
      css`
        @custom-variant custom-at-rule (@media foo);
        @custom-variant nested-selectors {
          &:hover {
            &:focus {
              @slot;
            }
          }
        }
        @tailwind utilities;
      `,
      ['peer-custom-at-rule:flex', 'peer-nested-selectors:flex'],
    ),
  ).toEqual('')
})

test('default breakpoints', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          /* Breakpoints */
          --breakpoint-sm: 640px;
          --breakpoint-md: 768px;
          --breakpoint-lg: 1024px;
          --breakpoint-xl: 1280px;
          --breakpoint-2xl: 1536px;
        }
        @tailwind utilities;
      `,
      ['sm:flex', 'md:flex', 'lg:flex', 'xl:flex', '2xl:flex'],
    ),
  ).toMatchInlineSnapshot(`
    "@media (min-width: 640px) {
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
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme reference {
          /* Breakpoints */
          --breakpoint-sm: 640px;
          --breakpoint-md: 768px;
          --breakpoint-lg: 1024px;
          --breakpoint-xl: 1280px;
          --breakpoint-2xl: 1536px;
        }
        @tailwind utilities;
      `,
      ['sm/foo:flex', 'md/foo:flex', 'lg/foo:flex', 'xl/foo:flex', '2xl/foo:flex'],
    ),
  ).toEqual('')
})

test('has', async () => {
  expect(
    await compileCss(
      css`
        @custom-variant hocus {
          &:hover,
          &:focus {
            @slot;
          }
        }
        @tailwind utilities;
      `,
      [
        'has-checked:flex',
        'has-[:checked]:flex',
        'has-[>img]:flex',
        'has-[+img]:flex',
        'has-[~img]:flex',
        'has-[&>img]:flex',
        'has-hocus:flex',

        'group-has-[:checked]:flex',
        'group-has-[:checked]/parent-name:flex',
        'group-has-checked:flex',
        'group-has-checked/parent-name:flex',
        'group-has-[>img]:flex',
        'group-has-[>img]/parent-name:flex',
        'group-has-[+img]:flex',
        'group-has-[~img]:flex',
        'group-has-[&>img]:flex',
        'group-has-[&>img]/parent-name:flex',
        'group-has-hocus:flex',
        'group-has-hocus/parent-name:flex',

        'peer-has-[:checked]:flex',
        'peer-has-[:checked]/sibling-name:flex',
        'peer-has-checked:flex',
        'peer-has-checked/sibling-name:flex',
        'peer-has-[>img]:flex',
        'peer-has-[>img]/sibling-name:flex',
        'peer-has-[+img]:flex',
        'peer-has-[~img]:flex',
        'peer-has-[&>img]:flex',
        'peer-has-[&>img]/sibling-name:flex',
        'peer-has-hocus:flex',
        'peer-has-hocus/sibling-name:flex',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ".group-has-checked\\:flex:is(:where(.group):has(:checked) *), .group-has-checked\\/parent-name\\:flex:is(:where(.group\\/parent-name):has(:checked) *), .group-has-hocus\\:flex:is(:where(.group):has(:hover, :focus) *), .group-has-hocus\\/parent-name\\:flex:is(:where(.group\\/parent-name):has(:hover, :focus) *), .group-has-\\[\\:checked\\]\\:flex:is(:where(.group):has(:checked) *), .group-has-\\[\\:checked\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name):has(:checked) *), .group-has-\\[\\&\\>img\\]\\:flex:is(:where(.group):has(* > img) *), .group-has-\\[\\&\\>img\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name):has(* > img) *), .group-has-\\[\\+img\\]\\:flex:is(:where(.group):has( + img) *), .group-has-\\[\\>img\\]\\:flex:is(:where(.group):has( > img) *), .group-has-\\[\\>img\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name):has( > img) *), .group-has-\\[\\~img\\]\\:flex:is(:where(.group):has( ~ img) *), .peer-has-checked\\:flex:is(:where(.peer):has(:checked) ~ *), .peer-has-checked\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):has(:checked) ~ *), .peer-has-hocus\\:flex:is(:where(.peer):has(:hover, :focus) ~ *), .peer-has-hocus\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):has(:hover, :focus) ~ *), .peer-has-\\[\\:checked\\]\\:flex:is(:where(.peer):has(:checked) ~ *), .peer-has-\\[\\:checked\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):has(:checked) ~ *), .peer-has-\\[\\&\\>img\\]\\:flex:is(:where(.peer):has(* > img) ~ *), .peer-has-\\[\\&\\>img\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):has(* > img) ~ *), .peer-has-\\[\\+img\\]\\:flex:is(:where(.peer):has( + img) ~ *), .peer-has-\\[\\>img\\]\\:flex:is(:where(.peer):has( > img) ~ *), .peer-has-\\[\\>img\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name):has( > img) ~ *), .peer-has-\\[\\~img\\]\\:flex:is(:where(.peer):has( ~ img) ~ *), .has-checked\\:flex:has(:checked), .has-hocus\\:flex:has(:hover, :focus), .has-\\[\\:checked\\]\\:flex:has(:checked), .has-\\[\\&\\>img\\]\\:flex:has(* > img), .has-\\[\\+img\\]\\:flex:has( + img), .has-\\[\\>img\\]\\:flex:has( > img), .has-\\[\\~img\\]\\:flex:has( ~ img) {
      display: flex;
    }"
  `)

  expect(
    await compileCss(
      css`
        @custom-variant custom-at-rule (@media foo);
        @custom-variant nested-selectors {
          &:hover {
            &:focus {
              @slot;
            }
          }
        }
        @tailwind utilities;
      `,
      [
        'has-[:checked]/foo:flex',
        'has-[@media_print]:flex',
        'has-custom-at-rule:flex',
        'has-nested-selectors:flex',
      ],
    ),
  ).toEqual('')
})

