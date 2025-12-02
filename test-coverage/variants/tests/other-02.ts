/**
 * Extracted from tailwindcss/packages/tailwindcss/src/variants.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('variants with the same root are sorted deterministically', async () => {
  function permute(arr: string[]): string[][] {
    if (arr.length <= 1) return [arr]

    return arr.flatMap((item, i) =>
      permute([...arr.slice(0, i), ...arr.slice(i + 1)]).map((permutation) => [
        item,
        ...permutation,
      ]),
    )
  }

  let classLists = permute([
    'data-hover:flex',
    'data-focus:flex',
    'data-active:flex',
    'data-[foo]:flex',
    'data-[bar]:flex',
    'data-[baz]:flex',
  ])

  for (let classList of classLists) {
    let output = await compileCss('@tailwind utilities;', classList)

    expect(output.trim()).toEqual(
      dedent(`
        .data-active\\:flex[data-active], .data-focus\\:flex[data-focus], .data-hover\\:flex[data-hover], .data-\\[bar\\]\\:flex[data-bar], .data-\\[baz\\]\\:flex[data-baz], .data-\\[foo\\]\\:flex[data-foo] {
          display: flex;
        }
      `),
    )
  }
})

test('matchVariant sorts deterministically', async () => {
  function permute(arr: string[]): string[][] {
    if (arr.length <= 1) return [arr]

    return arr.flatMap((item, i) =>
      permute([...arr.slice(0, i), ...arr.slice(i + 1)]).map((permutation) => [
        item,
        ...permutation,
      ]),
    )
  }

  let classLists = permute([
    'is-data:flex',
    'is-data-foo:flex',
    'is-data-bar:flex',
    'is-data-[potato]:flex',
    'is-data-[sandwich]:flex',
  ])

  for (let classList of classLists) {
    let output = await compileCss('@tailwind utilities; @plugin "./plugin.js";', classList, {
      async loadModule(id: string) {
        return {
          path: '',
          base: '/',
          module: createPlugin(({ matchVariant }) => {
            matchVariant('is-data', (value) => `&:is([data-${value}])`, {
              values: {
                DEFAULT: 'default',
                foo: 'foo',
                bar: 'bar',
              },
            })
          }),
        }
      },
    })

    expect(output.trim()).toEqual(
      dedent(`
        .is-data\\:flex[data-default], .is-data-foo\\:flex[data-foo], .is-data-bar\\:flex[data-bar], .is-data-\\[potato\\]\\:flex[data-potato], .is-data-\\[sandwich\\]\\:flex[data-sandwich] {
          display: flex;
        }
      `),
    )
  }
})

test('move modifier of compound variant to sub-variant if its also a compound variant', async () => {
  expect(
    await run([
      'not-group-focus/name:flex',
      'has-group-focus/name:flex',
      'in-group-focus/name:flex',

      // Keep the `name` on the `group`, don't move it to the `peer` because
      // that would be a breaking change.
      'group-peer-focus/name:flex',
    ]),
  ).toMatchInlineSnapshot(`
    ".not-group-focus\\/name\\:flex:not(:is(:where(.group\\/name):focus *)), .group-peer-focus\\/name\\:flex:is(:where(.group\\/name):is(:where(.peer):focus ~ *) *), :where(:is(:where(.group\\/name):focus *)) .in-group-focus\\/name\\:flex, .has-group-focus\\/name\\:flex:has(:is(:where(.group\\/name):focus *)) {
      display: flex;
    }"
  `)
})

