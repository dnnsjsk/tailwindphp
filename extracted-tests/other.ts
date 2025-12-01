/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('visibility', async () => {
  expect(await run(['visible', 'invisible', 'collapse'])).toMatchInlineSnapshot(`
    ".collapse {
      visibility: collapse;
    }

    .invisible {
      visibility: hidden;
    }

    .visible {
      visibility: visible;
    }"
  `)
  expect(
    await run([
      '-visible',
      '-invisible',
      '-collapse',
      'visible/foo',
      'invisible/foo',
      'collapse/foo',
    ]),
  ).toEqual('')
})

test('field-sizing', async () => {
  expect(await run(['field-sizing-content', 'field-sizing-fixed'])).toMatchInlineSnapshot(`
    ".field-sizing-content {
      field-sizing: content;
    }

    .field-sizing-fixed {
      field-sizing: fixed;
    }"
  `)
  expect(
    await run(['field-sizing-[other]', '-field-sizing-content', '-field-sizing-fixed']),
  ).toEqual('')
})

test('--tw-scroll-snap-strictness', async () => {
  expect(await run(['snap-mandatory', 'snap-proximity'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-scroll-snap-strictness: proximity;
        }
      }
    }

    .snap-mandatory {
      --tw-scroll-snap-strictness: mandatory;
    }

    .snap-proximity {
      --tw-scroll-snap-strictness: proximity;
    }

    @property --tw-scroll-snap-strictness {
      syntax: "*";
      inherits: false;
      initial-value: proximity;
    }"
  `)
  expect(
    await run(['-snap-mandatory', '-snap-proximity', 'snap-mandatory/foo', 'snap-proximity/foo']),
  ).toEqual('')
})

test('indent', async () => {
  expect(await run(['indent-[4px]', '-indent-[4px]'])).toMatchInlineSnapshot(`
    ".-indent-\\[4px\\] {
      text-indent: -4px;
    }

    .indent-\\[4px\\] {
      text-indent: 4px;
    }"
  `)
  expect(await run(['indent', 'indent-[4px]/foo', '-indent-[4px]/foo'])).toEqual('')
})

test('decoration', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --text-decoration-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        // text-decoration-color
        'decoration-red-500',
        'decoration-red-500/50',
        'decoration-red-500/[0.5]',
        'decoration-red-500/[50%]',
        'decoration-blue-500',
        'decoration-current',
        'decoration-current/50',
        'decoration-current/[0.5]',
        'decoration-current/[50%]',
        'decoration-inherit',
        'decoration-transparent',
        'decoration-[#0088cc]',
        'decoration-[#0088cc]/50',
        'decoration-[#0088cc]/[0.5]',
        'decoration-[#0088cc]/[50%]',
        'decoration-[var(--my-color)]',
        'decoration-[var(--my-color)]/50',
        'decoration-[var(--my-color)]/[0.5]',
        'decoration-[var(--my-color)]/[50%]',
        'decoration-[color:var(--my-color)]',
        'decoration-[color:var(--my-color)]/50',
        'decoration-[color:var(--my-color)]/[0.5]',
        'decoration-[color:var(--my-color)]/[50%]',

        // text-decoration-style
        'decoration-solid',
        'decoration-double',
        'decoration-dotted',
        'decoration-dashed',
        'decoration-wavy',

        // text-decoration-thickness
        'decoration-auto',
        'decoration-from-font',
        'decoration-0',
        'decoration-1',
        'decoration-2',
        'decoration-4',
        'decoration-123',
        'decoration-[12px]',
        'decoration-[50%]',
        'decoration-[length:var(--my-thickness)]',
        'decoration-[percentage:var(--my-thickness)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --text-decoration-color-blue-500: #3b82f6;
    }

    .decoration-\\[\\#0088cc\\] {
      text-decoration-color: #08c;
    }

    .decoration-\\[\\#0088cc\\]\\/50, .decoration-\\[\\#0088cc\\]\\/\\[0\\.5\\], .decoration-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      text-decoration-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .decoration-\\[color\\:var\\(--my-color\\)\\], .decoration-\\[color\\:var\\(--my-color\\)\\]\\/50 {
      -webkit-text-decoration-color: var(--my-color);
      -webkit-text-decoration-color: var(--my-color);
      text-decoration-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-\\[color\\:var\\(--my-color\\)\\]\\/50 {
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .decoration-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      -webkit-text-decoration-color: var(--my-color);
      -webkit-text-decoration-color: var(--my-color);
      text-decoration-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-\\[color\\:var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .decoration-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      -webkit-text-decoration-color: var(--my-color);
      -webkit-text-decoration-color: var(--my-color);
      text-decoration-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-\\[color\\:var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .decoration-\\[var\\(--my-color\\)\\], .decoration-\\[var\\(--my-color\\)\\]\\/50 {
      -webkit-text-decoration-color: var(--my-color);
      -webkit-text-decoration-color: var(--my-color);
      text-decoration-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-\\[var\\(--my-color\\)\\]\\/50 {
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .decoration-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
      -webkit-text-decoration-color: var(--my-color);
      -webkit-text-decoration-color: var(--my-color);
      text-decoration-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-\\[var\\(--my-color\\)\\]\\/\\[0\\.5\\] {
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .decoration-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
      -webkit-text-decoration-color: var(--my-color);
      -webkit-text-decoration-color: var(--my-color);
      text-decoration-color: var(--my-color);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-\\[var\\(--my-color\\)\\]\\/\\[50\\%\\] {
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--my-color) 50%, transparent);
      }
    }

    .decoration-blue-500 {
      -webkit-text-decoration-color: var(--text-decoration-color-blue-500);
      -webkit-text-decoration-color: var(--text-decoration-color-blue-500);
      text-decoration-color: var(--text-decoration-color-blue-500);
    }

    .decoration-current, .decoration-current\\/50 {
      text-decoration-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-current\\/50 {
        -webkit-text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
        text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .decoration-current\\/\\[0\\.5\\] {
      text-decoration-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-current\\/\\[0\\.5\\] {
        -webkit-text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
        text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .decoration-current\\/\\[50\\%\\] {
      text-decoration-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-current\\/\\[50\\%\\] {
        -webkit-text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
        text-decoration-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .decoration-inherit {
      -webkit-text-decoration-color: inherit;
      -webkit-text-decoration-color: inherit;
      text-decoration-color: inherit;
    }

    .decoration-red-500 {
      -webkit-text-decoration-color: var(--color-red-500);
      -webkit-text-decoration-color: var(--color-red-500);
      text-decoration-color: var(--color-red-500);
    }

    .decoration-red-500\\/50 {
      text-decoration-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-red-500\\/50 {
        -webkit-text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .decoration-red-500\\/\\[0\\.5\\] {
      text-decoration-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-red-500\\/\\[0\\.5\\] {
        -webkit-text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .decoration-red-500\\/\\[50\\%\\] {
      text-decoration-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .decoration-red-500\\/\\[50\\%\\] {
        -webkit-text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
        -webkit-text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
        text-decoration-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .decoration-transparent {
      text-decoration-color: #0000;
    }

    .decoration-dashed {
      text-decoration-style: dashed;
    }

    .decoration-dotted {
      text-decoration-style: dotted;
    }

    .decoration-double {
      text-decoration-style: double;
    }

    .decoration-solid {
      text-decoration-style: solid;
    }

    .decoration-wavy {
      text-decoration-style: wavy;
    }

    .decoration-0 {
      text-decoration-thickness: 0;
    }

    .decoration-1 {
      text-decoration-thickness: 1px;
    }

    .decoration-2 {
      text-decoration-thickness: 2px;
    }

    .decoration-4 {
      text-decoration-thickness: 4px;
    }

    .decoration-123 {
      text-decoration-thickness: 123px;
    }

    .decoration-\\[12px\\] {
      text-decoration-thickness: 12px;
    }

    .decoration-\\[50\\%\\] {
      text-decoration-thickness: .5em;
    }

    .decoration-\\[length\\:var\\(--my-thickness\\)\\], .decoration-\\[percentage\\:var\\(--my-thickness\\)\\] {
      text-decoration-thickness: var(--my-thickness);
    }

    .decoration-auto {
      text-decoration-thickness: auto;
    }

    .decoration-from-font {
      text-decoration-thickness: from-font;
    }"
  `)
  expect(
    await run([
      'decoration',
      // text-decoration-color
      '-decoration-red-500',
      '-decoration-red-500/50',
      '-decoration-red-500/[0.5]',
      '-decoration-red-500/[50%]',
      '-decoration-current',
      '-decoration-current/50',
      '-decoration-current/[0.5]',
      '-decoration-current/[50%]',
      '-decoration-transparent',
      '-decoration-[#0088cc]',
      '-decoration-[#0088cc]/50',
      '-decoration-[#0088cc]/[0.5]',
      '-decoration-[#0088cc]/[50%]',

      // text-decoration-style
      '-decoration-solid',
      '-decoration-double',
      '-decoration-dotted',
      '-decoration-dashed',
      '-decoration-wavy',

      // text-decoration-thickness
      'decoration--2',
      '-decoration-auto',
      '-decoration-from-font',
      '-decoration-0',
      '-decoration-1',
      '-decoration-2',
      '-decoration-4',
      '-decoration-123',

      'decoration-solid/foo',
      'decoration-double/foo',
      'decoration-dotted/foo',
      'decoration-dashed/foo',
      'decoration-wavy/foo',
      'decoration-auto/foo',
      'decoration-from-font/foo',
      'decoration-0/foo',
      'decoration-1/foo',
      'decoration-2/foo',
      'decoration-4/foo',
      'decoration-123/foo',
      'decoration-[12px]/foo',
      'decoration-[50%]/foo',
      'decoration-[length:var(--my-thickness)]/foo',
      'decoration-[percentage:var(--my-thickness)]/foo',
    ]),
  ).toEqual('')
})

test('contain', async () => {
  expect(
    await run([
      'contain-none',
      'contain-content',
      'contain-strict',
      'contain-size',
      'contain-inline-size',
      'contain-layout',
      'contain-paint',
      'contain-style',
      'contain-[unset]',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-contain-size: initial;
          --tw-contain-layout: initial;
          --tw-contain-paint: initial;
          --tw-contain-style: initial;
        }
      }
    }

    .contain-inline-size {
      --tw-contain-size: inline-size;
      contain: var(--tw-contain-size, ) var(--tw-contain-layout, ) var(--tw-contain-paint, ) var(--tw-contain-style, );
    }

    .contain-layout {
      --tw-contain-layout: layout;
      contain: var(--tw-contain-size, ) var(--tw-contain-layout, ) var(--tw-contain-paint, ) var(--tw-contain-style, );
    }

    .contain-paint {
      --tw-contain-paint: paint;
      contain: var(--tw-contain-size, ) var(--tw-contain-layout, ) var(--tw-contain-paint, ) var(--tw-contain-style, );
    }

    .contain-size {
      --tw-contain-size: size;
      contain: var(--tw-contain-size, ) var(--tw-contain-layout, ) var(--tw-contain-paint, ) var(--tw-contain-style, );
    }

    .contain-style {
      --tw-contain-style: style;
      contain: var(--tw-contain-size, ) var(--tw-contain-layout, ) var(--tw-contain-paint, ) var(--tw-contain-style, );
    }

    .contain-\\[unset\\] {
      contain: unset;
    }

    .contain-content {
      contain: content;
    }

    .contain-none {
      contain: none;
    }

    .contain-strict {
      contain: strict;
    }

    @property --tw-contain-size {
      syntax: "*";
      inherits: false
    }

    @property --tw-contain-layout {
      syntax: "*";
      inherits: false
    }

    @property --tw-contain-paint {
      syntax: "*";
      inherits: false
    }

    @property --tw-contain-style {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'contain-none/foo',
      'contain-content/foo',
      'contain-strict/foo',
      'contain-size/foo',
      'contain-inline-size/foo',
      'contain-layout/foo',
      'contain-paint/foo',
      'contain-style/foo',
      'contain-[unset]/foo',
    ]),
  ).toEqual('')
})

test('content', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --content-slash: '/';
        }
        @tailwind utilities;
      `,
      ['content-slash', 'content-["hello_world"]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-content: "";
        }
      }
    }

    :root, :host {
      --content-slash: "/";
    }

    .content-\\[\\"hello_world\\"\\] {
      --tw-content: "hello world";
      content: var(--tw-content);
    }

    .content-slash {
      --tw-content: var(--content-slash);
      content: var(--tw-content);
    }

    @property --tw-content {
      syntax: "*";
      inherits: false;
      initial-value: "";
    }"
  `)
  expect(await run(['content', '-content-["hello_world"]', 'content-["hello_world"]/foo'])).toEqual(
    '',
  )
})

test('underline-offset', async () => {
  expect(
    await compileCss(
      css`
        @theme {
        }
        @tailwind utilities;
      `,
      [
        'underline-offset-auto',
        'underline-offset-4',
        '-underline-offset-4',
        'underline-offset-123',
        '-underline-offset-123',
        'underline-offset-[var(--value)]',
        '-underline-offset-[var(--value)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ".-underline-offset-4 {
      text-underline-offset: calc(4px * -1);
    }

    .-underline-offset-123 {
      text-underline-offset: calc(123px * -1);
    }

    .-underline-offset-\\[var\\(--value\\)\\] {
      text-underline-offset: calc(var(--value) * -1);
    }

    .underline-offset-4 {
      text-underline-offset: 4px;
    }

    .underline-offset-123 {
      text-underline-offset: 123px;
    }

    .underline-offset-\\[var\\(--value\\)\\] {
      text-underline-offset: var(--value);
    }

    .underline-offset-auto {
      text-underline-offset: auto;
    }"
  `)
  expect(
    await run([
      'underline-offset',
      'underline-offset--4',
      '-underline-offset-auto',
      'underline-offset-unknown',
      'underline-offset-auto/foo',
      'underline-offset-4/foo',
      '-underline-offset-4/foo',
      'underline-offset-123/foo',
      '-underline-offset-123/foo',
      'underline-offset-[var(--value)]/foo',
      '-underline-offset-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --text-underline-offset-auto: 4px;
        }
        @tailwind utilities;
      `,
      ['underline-offset-auto'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --text-underline-offset-auto: 4px;
    }

    .underline-offset-auto {
      text-underline-offset: var(--text-underline-offset-auto);
    }"
  `)
})

test('@container', async () => {
  expect(
    await run([
      '@container',
      '@container-normal',
      '@container/sidebar',
      '@container-normal/sidebar',
      '@container-size',
      '@container-size/sidebar',
    ]),
  ).toMatchInlineSnapshot(`
    ".\\@container-normal\\/sidebar {
      container: sidebar;
    }

    .\\@container-size\\/sidebar {
      container: sidebar / size;
    }

    .\\@container\\/sidebar {
      container: sidebar / inline-size;
    }

    .\\@container {
      container-type: inline-size;
    }

    .\\@container-normal {
      container-type: normal;
    }

    .\\@container-size {
      container-type: size;
    }"
  `)
  expect(
    await run([
      '-@container',
      '-@container-normal',
      '-@container/sidebar',
      '-@container-normal/sidebar',
      '-@container-size',
      '-@container-size/sidebar',
    ]),
  ).toEqual('')
})

