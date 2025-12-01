/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('origin', async () => {
  expect(
    await run([
      'origin-center',
      'origin-top',
      'origin-top-right',
      'origin-right',
      'origin-bottom-right',
      'origin-bottom',
      'origin-bottom-left',
      'origin-left',
      'origin-top-left',
      'origin-[50px_100px]',
      'origin-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    ".origin-\\[50px_100px\\] {
      transform-origin: 50px 100px;
    }

    .origin-\\[var\\(--value\\)\\] {
      transform-origin: var(--value);
    }

    .origin-bottom {
      transform-origin: bottom;
    }

    .origin-bottom-left {
      transform-origin: 0 100%;
    }

    .origin-bottom-right {
      transform-origin: 100% 100%;
    }

    .origin-center {
      transform-origin: center;
    }

    .origin-left {
      transform-origin: 0;
    }

    .origin-right {
      transform-origin: 100%;
    }

    .origin-top {
      transform-origin: top;
    }

    .origin-top-left {
      transform-origin: 0 0;
    }

    .origin-top-right {
      transform-origin: 100% 0;
    }"
  `)
  expect(
    await run([
      '-origin-center',
      '-origin-[var(--value)]',
      'origin-center/foo',
      'origin-top/foo',
      'origin-top-right/foo',
      'origin-right/foo',
      'origin-bottom-right/foo',
      'origin-bottom/foo',
      'origin-bottom-left/foo',
      'origin-left/foo',
      'origin-top-left/foo',
      'origin-[50px_100px]/foo',
      'origin-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --transform-origin-top: 10px 20px;
        }
        @tailwind utilities;
      `,
      ['origin-top'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --transform-origin-top: 10px 20px;
    }

    .origin-top {
      transform-origin: var(--transform-origin-top);
    }"
  `)
})

test('translate', async () => {
  expect(
    await run([
      'translate-1/2',
      'translate-full',
      '-translate-full',
      'translate-[123px]',
      '-translate-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-translate-x: 0;
          --tw-translate-y: 0;
          --tw-translate-z: 0;
        }
      }
    }

    .-translate-\\[var\\(--value\\)\\] {
      --tw-translate-x: calc(var(--value) * -1);
      --tw-translate-y: calc(var(--value) * -1);
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .-translate-full {
      --tw-translate-x: -100%;
      --tw-translate-y: -100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-1\\/2 {
      --tw-translate-x: calc(1 / 2 * 100%);
      --tw-translate-y: calc(1 / 2 * 100%);
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-\\[123px\\] {
      --tw-translate-x: 123px;
      --tw-translate-y: 123px;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-full {
      --tw-translate-x: 100%;
      --tw-translate-y: 100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    @property --tw-translate-x {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-y {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-z {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'translate',
      'translate--1',
      'translate--1/2',
      'translate--1/-2',
      'translate-1/-2',
      'translate-1/2/foo',
      'translate-full/foo',
      '-translate-full/foo',
      'translate-[123px]/foo',
      '-translate-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('translate-x', async () => {
  expect(
    await run([
      'translate-x-full',
      '-translate-x-full',
      'translate-x-px',
      '-translate-x-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-translate-x: 0;
          --tw-translate-y: 0;
          --tw-translate-z: 0;
        }
      }
    }

    .-translate-x-\\[var\\(--value\\)\\] {
      --tw-translate-x: calc(var(--value) * -1);
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .-translate-x-full {
      --tw-translate-x: -100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-x-full {
      --tw-translate-x: 100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-x-px {
      --tw-translate-x: 1px;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    @property --tw-translate-x {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-y {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-z {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'translate-x',
      'translate-x--1',
      'translate-x--1/2',
      'translate-x--1/-2',
      'translate-x-1/-2',
      'translate-x-full/foo',
      '-translate-x-full/foo',
      'translate-x-px/foo',
      '-translate-x-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['translate-x-full', '-translate-x-full', 'translate-x-px', '-translate-x-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-translate-x: 0;
          --tw-translate-y: 0;
          --tw-translate-z: 0;
        }
      }
    }

    .-translate-x-\\[var\\(--value\\)\\] {
      --tw-translate-x: calc(var(--value) * -1);
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .-translate-x-full {
      --tw-translate-x: -100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-x-full {
      --tw-translate-x: 100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-x-px {
      --tw-translate-x: 1px;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    @property --tw-translate-x {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-y {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-z {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'perspective',
      '-perspective',
      'perspective-potato',
      'perspective-123',
      'perspective-normal/foo',
      'perspective-dramatic/foo',
      'perspective-none/foo',
      'perspective-[456px]/foo',
    ]),
  ).toEqual('')
})

test('translate-y', async () => {
  expect(
    await run([
      'translate-y-full',
      '-translate-y-full',
      'translate-y-px',
      '-translate-y-[var(--value)]',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-translate-x: 0;
          --tw-translate-y: 0;
          --tw-translate-z: 0;
        }
      }
    }

    .-translate-y-\\[var\\(--value\\)\\] {
      --tw-translate-y: calc(var(--value) * -1);
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .-translate-y-full {
      --tw-translate-y: -100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-y-full {
      --tw-translate-y: 100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-y-px {
      --tw-translate-y: 1px;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    @property --tw-translate-x {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-y {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-z {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'translate-y',
      'translate-y--1',
      'translate-y--1/2',
      'translate-y--1/-2',
      'translate-y-1/-2',
      'translate-y-full/foo',
      '-translate-y-full/foo',
      'translate-y-px/foo',
      '-translate-y-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      ['translate-y-full', '-translate-y-full', 'translate-y-px', '-translate-y-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-translate-x: 0;
          --tw-translate-y: 0;
          --tw-translate-z: 0;
        }
      }
    }

    .-translate-y-\\[var\\(--value\\)\\] {
      --tw-translate-y: calc(var(--value) * -1);
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .-translate-y-full {
      --tw-translate-y: -100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-y-full {
      --tw-translate-y: 100%;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    .translate-y-px {
      --tw-translate-y: 1px;
      translate: var(--tw-translate-x) var(--tw-translate-y);
    }

    @property --tw-translate-x {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-y {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-z {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(
    await run([
      'perspective',
      '-perspective',
      'perspective-potato',
      'perspective-123',
      'perspective-normal/foo',
      'perspective-dramatic/foo',
      'perspective-none/foo',
      'perspective-[456px]/foo',
    ]),
  ).toEqual('')
})

test('translate-z', async () => {
  expect(await run(['-translate-z-px', 'translate-z-px', '-translate-z-[var(--value)]']))
    .toMatchInlineSnapshot(`
      "@layer properties {
        @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
          *, :before, :after, ::backdrop {
            --tw-translate-x: 0;
            --tw-translate-y: 0;
            --tw-translate-z: 0;
          }
        }
      }

      .-translate-z-\\[var\\(--value\\)\\] {
        --tw-translate-z: calc(var(--value) * -1);
        translate: var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z);
      }

      .-translate-z-px {
        --tw-translate-z: -1px;
        translate: var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z);
      }

      .translate-z-px {
        --tw-translate-z: 1px;
        translate: var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z);
      }

      @property --tw-translate-x {
        syntax: "*";
        inherits: false;
        initial-value: 0;
      }

      @property --tw-translate-y {
        syntax: "*";
        inherits: false;
        initial-value: 0;
      }

      @property --tw-translate-z {
        syntax: "*";
        inherits: false;
        initial-value: 0;
      }"
    `)
  expect(
    await run([
      'translate-z',
      'translate-z--1',
      'translate-z--1/2',
      'translate-z--1/-2',
      'translate-z-1/-2',
      'translate-z-full',
      '-translate-z-full',
      'translate-z-1/2',
      'translate-y-px/foo',
      '-translate-z-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('translate-3d', async () => {
  expect(await run(['translate-3d'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-translate-x: 0;
          --tw-translate-y: 0;
          --tw-translate-z: 0;
        }
      }
    }

    .translate-3d {
      translate: var(--tw-translate-x) var(--tw-translate-y) var(--tw-translate-z);
    }

    @property --tw-translate-x {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-y {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-translate-z {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(await run(['-translate-3d', 'translate-3d/foo'])).toEqual('')
})

test('rotate', async () => {
  expect(
    await run([
      'rotate-45',
      '-rotate-45',
      'rotate-[123deg]',
      'rotate-[0.3_0.7_1_45deg]',
      'rotate-(--var)',
      '-rotate-[123deg]',
      '-rotate-(--var)',
    ]),
  ).toMatchInlineSnapshot(`
    ".-rotate-\\(--var\\) {
      rotate: calc(var(--var) * -1);
    }

    .-rotate-45 {
      rotate: -45deg;
    }

    .-rotate-\\[123deg\\] {
      rotate: -123deg;
    }

    .rotate-\\(--var\\) {
      rotate: var(--var);
    }

    .rotate-45 {
      rotate: 45deg;
    }

    .rotate-\\[0\\.3_0\\.7_1_45deg\\] {
      rotate: .3 .7 1 45deg;
    }

    .rotate-\\[123deg\\] {
      rotate: 123deg;
    }"
  `)
  expect(
    await run([
      'rotate',
      'rotate-z',
      'rotate--2',
      'rotate-unknown',
      'rotate-45/foo',
      '-rotate-45/foo',
      'rotate-[123deg]/foo',
      'rotate-[0.3_0.7_1_45deg]/foo',
    ]),
  ).toEqual('')
})

test('rotate-x', async () => {
  expect(
    await run([
      'rotate-x-45',
      '-rotate-x-45',
      'rotate-x-[123deg]',
      'rotate-x-(--var)',
      '-rotate-x-(--var)',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-rotate-x: initial;
          --tw-rotate-y: initial;
          --tw-rotate-z: initial;
          --tw-skew-x: initial;
          --tw-skew-y: initial;
        }
      }
    }

    .-rotate-x-\\(--var\\) {
      --tw-rotate-x: rotateX(calc(var(--var) * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .-rotate-x-45 {
      --tw-rotate-x: rotateX(calc(45deg * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-x-\\(--var\\) {
      --tw-rotate-x: rotateX(var(--var));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-x-45 {
      --tw-rotate-x: rotateX(45deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-x-\\[123deg\\] {
      --tw-rotate-x: rotateX(123deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    @property --tw-rotate-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-y {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-z {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-y {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'rotate-x',
      'rotate-x--1',
      '-rotate-x',
      'rotate-x-potato',
      'rotate-x-45/foo',
      '-rotate-x-45/foo',
      'rotate-x-[123deg]/foo',
    ]),
  ).toEqual('')
})

test('rotate-y', async () => {
  expect(
    await run([
      'rotate-y-45',
      'rotate-y-[123deg]',
      'rotate-y-(--var)',
      '-rotate-y-45',
      '-rotate-y-[123deg]',
      '-rotate-y-(--var)',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-rotate-x: initial;
          --tw-rotate-y: initial;
          --tw-rotate-z: initial;
          --tw-skew-x: initial;
          --tw-skew-y: initial;
        }
      }
    }

    .-rotate-y-\\(--var\\) {
      --tw-rotate-y: rotateY(calc(var(--var) * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .-rotate-y-45 {
      --tw-rotate-y: rotateY(calc(45deg * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .-rotate-y-\\[123deg\\] {
      --tw-rotate-y: rotateY(calc(123deg * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-y-\\(--var\\) {
      --tw-rotate-y: rotateY(var(--var));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-y-45 {
      --tw-rotate-y: rotateY(45deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-y-\\[123deg\\] {
      --tw-rotate-y: rotateY(123deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    @property --tw-rotate-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-y {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-z {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-y {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'rotate-y',
      'rotate-y--1',
      '-rotate-y',
      'rotate-y-potato',
      'rotate-y-45/foo',
      '-rotate-y-45/foo',
      'rotate-y-[123deg]/foo',
    ]),
  ).toEqual('')
})

test('rotate-z', async () => {
  expect(
    await run([
      'rotate-z-45',
      'rotate-z-[123deg]',
      'rotate-z-(--var)',
      '-rotate-z-45',
      '-rotate-z-[123deg]',
      '-rotate-z-(--var)',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-rotate-x: initial;
          --tw-rotate-y: initial;
          --tw-rotate-z: initial;
          --tw-skew-x: initial;
          --tw-skew-y: initial;
        }
      }
    }

    .-rotate-z-\\(--var\\) {
      --tw-rotate-z: rotateZ(calc(var(--var) * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .-rotate-z-45 {
      --tw-rotate-z: rotateZ(calc(45deg * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .-rotate-z-\\[123deg\\] {
      --tw-rotate-z: rotateZ(calc(123deg * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-z-\\(--var\\) {
      --tw-rotate-z: rotateZ(var(--var));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-z-45 {
      --tw-rotate-z: rotateZ(45deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .rotate-z-\\[123deg\\] {
      --tw-rotate-z: rotateZ(123deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    @property --tw-rotate-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-y {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-z {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-y {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'rotate-z',
      'rotate-z--1',
      '-rotate-z',
      'rotate-z-potato',
      'rotate-z-45/foo',
      '-rotate-z-45/foo',
      'rotate-z-[123deg]/foo',
    ]),
  ).toEqual('')
})

test('skew', async () => {
  expect(await run(['skew-6', '-skew-6', 'skew-[123deg]'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-rotate-x: initial;
          --tw-rotate-y: initial;
          --tw-rotate-z: initial;
          --tw-skew-x: initial;
          --tw-skew-y: initial;
        }
      }
    }

    .-skew-6 {
      --tw-skew-x: skewX(calc(6deg * -1));
      --tw-skew-y: skewY(calc(6deg * -1));
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .skew-6 {
      --tw-skew-x: skewX(6deg);
      --tw-skew-y: skewY(6deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    .skew-\\[123deg\\] {
      --tw-skew-x: skewX(123deg);
      --tw-skew-y: skewY(123deg);
      transform: var(--tw-rotate-x, ) var(--tw-rotate-y, ) var(--tw-rotate-z, ) var(--tw-skew-x, ) var(--tw-skew-y, );
    }

    @property --tw-rotate-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-y {
      syntax: "*";
      inherits: false
    }

    @property --tw-rotate-z {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-x {
      syntax: "*";
      inherits: false
    }

    @property --tw-skew-y {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'skew',
      'skew--1',
      'skew-unknown',
      'skew-6/foo',
      '-skew-6/foo',
      'skew-[123deg]/foo',
    ]),
  ).toEqual('')
})

