/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('marker', async () => {
  expect(await run(['marker:flex'])).toMatchInlineSnapshot(`
    ".marker\\:flex ::marker {
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
    }"
  `)
  expect(await run(['marker/foo:flex'])).toEqual('')
})

test('selection', async () => {
  expect(await run(['selection:flex'])).toMatchInlineSnapshot(`
    ".selection\\:flex ::selection {
      display: flex;
    }

    .selection\\:flex::selection {
      display: flex;
    }"
  `)
  expect(await run(['selection/foo:flex'])).toEqual('')
})

test('file', async () => {
  expect(await run(['file:flex'])).toMatchInlineSnapshot(`
    ".file\\:flex::file-selector-button {
      display: flex;
    }"
  `)
  expect(await run(['file/foo:flex'])).toEqual('')
})

test('placeholder', async () => {
  expect(await run(['placeholder:flex'])).toMatchInlineSnapshot(`
    ".placeholder\\:flex::placeholder {
      display: flex;
    }"
  `)
  expect(await run(['placeholder/foo:flex'])).toEqual('')
})

test('backdrop', async () => {
  expect(await run(['backdrop:flex'])).toMatchInlineSnapshot(`
    ".backdrop\\:flex::backdrop {
      display: flex;
    }"
  `)
  expect(await run(['backdrop/foo:flex'])).toEqual('')
})

test('before', async () => {
  expect(await run(['before:flex'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-content: "";
        }
      }
    }

    .before\\:flex:before {
      content: var(--tw-content);
      display: flex;
    }

    @property --tw-content {
      syntax: "*";
      inherits: false;
      initial-value: "";
    }"
  `)
  expect(await run(['before/foo:flex'])).toEqual('')
})

test('after', async () => {
  expect(await run(['after:flex'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-content: "";
        }
      }
    }

    .after\\:flex:after {
      content: var(--tw-content);
      display: flex;
    }

    @property --tw-content {
      syntax: "*";
      inherits: false;
      initial-value: "";
    }"
  `)
  expect(await run(['after/foo:flex'])).toEqual('')
})

