/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('data', async () => {
  expect(
    await run([
      'data-disabled:flex',
      'data-[potato=salad]:flex',
      'data-[potato_=_"salad"]:flex',
      'data-[potato_^=_"salad"]:flex',
      'data-[potato="^_="]:flex',
      'data-[foo=1]:flex',
      'data-[foo=bar_baz]:flex',
      "data-[foo$='bar'_i]:flex",
      'data-[foo$=bar_baz_i]:flex',

      'group-data-[disabled]:flex',
      'group-data-[disabled]/parent-name:flex',
      'group-data-[foo=1]:flex',
      'group-data-[foo=1]/parent-name:flex',
      'group-data-[foo=bar baz]/parent-name:flex',
      "group-data-[foo$='bar'_i]/parent-name:flex",
      'group-data-[foo$=bar_baz_i]/parent-name:flex',

      'peer-data-[disabled]:flex',
      'peer-data-[disabled]/sibling-name:flex',
      'peer-data-[foo=1]:flex',
      'peer-data-[foo=1]/sibling-name:flex',
      'peer-data-[foo=bar baz]/sibling-name:flex',
      "peer-data-[foo$='bar'_i]/sibling-name:flex",
      'peer-data-[foo$=bar_baz_i]/sibling-name:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".group-data-\\[disabled\\]\\:flex:is(:where(.group)[data-disabled] *), .group-data-\\[disabled\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[data-disabled] *), .group-data-\\[foo\\$\\=\\'bar\\'_i\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[data-foo$="bar" i] *), .group-data-\\[foo\\$\\=bar_baz_i\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[data-foo$="bar baz" i] *), .group-data-\\[foo\\=1\\]\\:flex:is(:where(.group)[data-foo="1"] *), .group-data-\\[foo\\=1\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[data-foo="1"] *), .group-data-\\[foo\\=bar\\ baz\\]\\/parent-name\\:flex:is(:where(.group\\/parent-name)[data-foo="bar baz"] *), .peer-data-\\[disabled\\]\\:flex:is(:where(.peer)[data-disabled] ~ *), .peer-data-\\[disabled\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[data-disabled] ~ *), .peer-data-\\[foo\\$\\=\\'bar\\'_i\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[data-foo$="bar" i] ~ *), .peer-data-\\[foo\\$\\=bar_baz_i\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[data-foo$="bar baz" i] ~ *), .peer-data-\\[foo\\=1\\]\\:flex:is(:where(.peer)[data-foo="1"] ~ *), .peer-data-\\[foo\\=1\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[data-foo="1"] ~ *), .peer-data-\\[foo\\=bar\\ baz\\]\\/sibling-name\\:flex:is(:where(.peer\\/sibling-name)[data-foo="bar baz"] ~ *), .data-disabled\\:flex[data-disabled], .data-\\[foo\\$\\=\\'bar\\'_i\\]\\:flex[data-foo$="bar" i], .data-\\[foo\\$\\=bar_baz_i\\]\\:flex[data-foo$="bar baz" i], .data-\\[foo\\=1\\]\\:flex[data-foo="1"], .data-\\[foo\\=bar_baz\\]\\:flex[data-foo="bar baz"], .data-\\[potato_\\=_\\"salad\\"\\]\\:flex[data-potato="salad"], .data-\\[potato_\\^\\=_\\"salad\\"\\]\\:flex[data-potato^="salad"], .data-\\[potato\\=\\"\\^_\\=\\"\\]\\:flex[data-potato="^ ="], .data-\\[potato\\=salad\\]\\:flex[data-potato="salad"] {
      display: flex;
    }"
  `)
  expect(
    await run([
      'data-[]:flex',
      'data-[foo_^_=_"bar"]:flex', // Can't have spaces between `^` and `=`
      'data-disabled/foo:flex',
      'data-[potato=salad]/foo:flex',
    ]),
  ).toEqual('')
})

