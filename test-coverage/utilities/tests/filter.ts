/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('filter', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --blur-xl: 24px;
          --color-red-500: #ef4444;
          --drop-shadow: 0 1px 1px rgb(0 0 0 / 0.05);
          --drop-shadow-xl: 0 9px 7px rgb(0 0 0 / 0.1);
        }
        @theme inline {
          --drop-shadow-multi: 0 1px 1px rgb(0 0 0 / 0.05), 0 9px 7px rgb(0 0 0 / 0.1);
        }
        @tailwind utilities;
      `,
      [
        'filter',
        'filter-none',
        'filter-[var(--value)]',
        'blur-xl',
        'blur-none',
        'blur-[4px]',
        'brightness-50',
        'brightness-[1.23]',
        'contrast-50',
        'contrast-[1.23]',
        'grayscale',
        'grayscale-0',
        'grayscale-[var(--value)]',
        'hue-rotate-15',
        'hue-rotate-[45deg]',
        '-hue-rotate-15',
        '-hue-rotate-[45deg]',
        'invert',
        'invert-0',
        'invert-[var(--value)]',
        'drop-shadow',
        'drop-shadow/25',
        'drop-shadow-xl',
        'drop-shadow-multi',
        'drop-shadow-[0_0_red]',
        'drop-shadow-red-500',
        'drop-shadow-red-500/50',
        'drop-shadow-none',
        'drop-shadow-inherit',
        'saturate-0',
        'saturate-[1.75]',
        'saturate-[var(--value)]',
        'sepia',
        'sepia-0',
        'sepia-[50%]',
        'sepia-[var(--value)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-blur: initial;
          --tw-brightness: initial;
          --tw-contrast: initial;
          --tw-grayscale: initial;
          --tw-hue-rotate: initial;
          --tw-invert: initial;
          --tw-opacity: initial;
          --tw-saturate: initial;
          --tw-sepia: initial;
          --tw-drop-shadow: initial;
          --tw-drop-shadow-color: initial;
          --tw-drop-shadow-alpha: 100%;
          --tw-drop-shadow-size: initial;
        }
      }
    }

    :root, :host {
      --blur-xl: 24px;
      --color-red-500: #ef4444;
      --drop-shadow: 0 1px 1px #0000000d;
      --drop-shadow-xl: 0 9px 7px #0000001a;
    }

    .blur-\\[4px\\] {
      --tw-blur: blur(4px);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .blur-none {
      --tw-blur: ;
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .blur-xl {
      --tw-blur: blur(var(--blur-xl));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .brightness-50 {
      --tw-brightness: brightness(50%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .brightness-\\[1\\.23\\] {
      --tw-brightness: brightness(1.23);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .contrast-50 {
      --tw-contrast: contrast(50%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .contrast-\\[1\\.23\\] {
      --tw-contrast: contrast(1.23);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow\\/25 {
      --tw-drop-shadow-alpha: 25%;
      --tw-drop-shadow-size: drop-shadow(0 1px 1px var(--tw-drop-shadow-color, oklab(0% 0 0 / .25)));
      --tw-drop-shadow: drop-shadow(var(--drop-shadow));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow {
      --tw-drop-shadow-size: drop-shadow(0 1px 1px var(--tw-drop-shadow-color, #0000000d));
      --tw-drop-shadow: drop-shadow(var(--drop-shadow));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow-\\[0_0_red\\] {
      --tw-drop-shadow-size: drop-shadow(0 0 var(--tw-drop-shadow-color, red));
      --tw-drop-shadow: var(--tw-drop-shadow-size);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow-multi {
      --tw-drop-shadow-size: drop-shadow(0 1px 1px var(--tw-drop-shadow-color, #0000000d)) drop-shadow(0 9px 7px var(--tw-drop-shadow-color, #0000001a));
      --tw-drop-shadow: drop-shadow(0 1px 1px #0000000d) drop-shadow(0 9px 7px #0000001a);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow-xl {
      --tw-drop-shadow-size: drop-shadow(0 9px 7px var(--tw-drop-shadow-color, #0000001a));
      --tw-drop-shadow: drop-shadow(var(--drop-shadow-xl));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow-none {
      --tw-drop-shadow: ;
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .drop-shadow-inherit {
      --tw-drop-shadow-color: inherit;
      --tw-drop-shadow: var(--tw-drop-shadow-size);
    }

    .drop-shadow-red-500 {
      --tw-drop-shadow-color: #ef4444;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .drop-shadow-red-500 {
        --tw-drop-shadow-color: color-mix(in oklab, var(--color-red-500) var(--tw-drop-shadow-alpha), transparent);
      }
    }

    .drop-shadow-red-500 {
      --tw-drop-shadow: var(--tw-drop-shadow-size);
    }

    .drop-shadow-red-500\\/50 {
      --tw-drop-shadow-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .drop-shadow-red-500\\/50 {
        --tw-drop-shadow-color: color-mix(in oklab, color-mix(in oklab, var(--color-red-500) 50%, transparent) var(--tw-drop-shadow-alpha), transparent);
      }
    }

    .drop-shadow-red-500\\/50 {
      --tw-drop-shadow: var(--tw-drop-shadow-size);
    }

    .grayscale {
      --tw-grayscale: grayscale(100%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .grayscale-0 {
      --tw-grayscale: grayscale(0%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .grayscale-\\[var\\(--value\\)\\] {
      --tw-grayscale: grayscale(var(--value));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .-hue-rotate-15 {
      --tw-hue-rotate: hue-rotate(calc(15deg * -1));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .-hue-rotate-\\[45deg\\] {
      --tw-hue-rotate: hue-rotate(calc(45deg * -1));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .hue-rotate-15 {
      --tw-hue-rotate: hue-rotate(15deg);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .hue-rotate-\\[45deg\\] {
      --tw-hue-rotate: hue-rotate(45deg);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .invert {
      --tw-invert: invert(100%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .invert-0 {
      --tw-invert: invert(0%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .invert-\\[var\\(--value\\)\\] {
      --tw-invert: invert(var(--value));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .saturate-0 {
      --tw-saturate: saturate(0%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .saturate-\\[1\\.75\\] {
      --tw-saturate: saturate(1.75);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .saturate-\\[var\\(--value\\)\\] {
      --tw-saturate: saturate(var(--value));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .sepia {
      --tw-sepia: sepia(100%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .sepia-0 {
      --tw-sepia: sepia(0%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .sepia-\\[50\\%\\] {
      --tw-sepia: sepia(50%);
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .sepia-\\[var\\(--value\\)\\] {
      --tw-sepia: sepia(var(--value));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .filter {
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    .filter-\\[var\\(--value\\)\\] {
      filter: var(--value);
    }

    .filter-none {
      filter: none;
    }

    @property --tw-blur {
      syntax: "*";
      inherits: false
    }

    @property --tw-brightness {
      syntax: "*";
      inherits: false
    }

    @property --tw-contrast {
      syntax: "*";
      inherits: false
    }

    @property --tw-grayscale {
      syntax: "*";
      inherits: false
    }

    @property --tw-hue-rotate {
      syntax: "*";
      inherits: false
    }

    @property --tw-invert {
      syntax: "*";
      inherits: false
    }

    @property --tw-opacity {
      syntax: "*";
      inherits: false
    }

    @property --tw-saturate {
      syntax: "*";
      inherits: false
    }

    @property --tw-sepia {
      syntax: "*";
      inherits: false
    }

    @property --tw-drop-shadow {
      syntax: "*";
      inherits: false
    }

    @property --tw-drop-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-drop-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-drop-shadow-size {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      '-filter',
      '-filter-none',
      '-filter-[var(--value)]',
      '-blur-xl',
      '-blur-[4px]',
      'brightness--50',
      '-brightness-50',
      '-brightness-[1.23]',
      'brightness-unknown',
      'contrast--50',
      '-contrast-50',
      '-contrast-[1.23]',
      'contrast-unknown',
      '-grayscale',
      '-grayscale-0',
      'grayscale--1',
      '-grayscale-[var(--value)]',
      'grayscale-unknown',
      'hue-rotate--5',
      'hue-rotate-unknown',
      '-invert',
      'invert--5',
      '-invert-0',
      '-invert-[var(--value)]',
      'invert-unknown',
      '-drop-shadow-xl',
      '-drop-shadow-[0_0_red]',

      'drop-shadow/foo',
      '-drop-shadow/foo',
      '-drop-shadow/25',
      '-drop-shadow-red-500',
      'drop-shadow-red-500/foo',
      '-drop-shadow-red-500/foo',
      '-drop-shadow-red-500/50',

      '-saturate-0',
      'saturate--5',
      '-saturate-[1.75]',
      '-saturate-[var(--value)]',
      'saturate-saturate',
      '-sepia',
      'sepia--50',
      '-sepia-0',
      '-sepia-[50%]',
      '-sepia-[var(--value)]',
      'sepia-unknown',
      'filter/foo',
      'filter-none/foo',
      'filter-[var(--value)]/foo',
      'blur-xl/foo',
      'blur-none/foo',
      'blur-[4px]/foo',
      'brightness-50/foo',
      'brightness-[1.23]/foo',
      'contrast-50/foo',
      'contrast-[1.23]/foo',
      'grayscale/foo',
      'grayscale-0/foo',
      'grayscale-[var(--value)]/foo',
      'hue-rotate-15/foo',
      'hue-rotate-[45deg]/foo',
      'invert/foo',
      'invert-0/foo',
      'invert-[var(--value)]/foo',
      'drop-shadow-xl/foo',
      'drop-shadow-[0_0_red]/foo',
      'saturate-0/foo',
      'saturate-[1.75]/foo',
      'saturate-[var(--value)]/foo',
      'sepia/foo',
      'sepia-0/foo',
      'sepia-[50%]/foo',
      'sepia-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --blur-none: 2px;
        }
        @tailwind utilities;
      `,
      ['blur-none'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-blur: initial;
          --tw-brightness: initial;
          --tw-contrast: initial;
          --tw-grayscale: initial;
          --tw-hue-rotate: initial;
          --tw-invert: initial;
          --tw-opacity: initial;
          --tw-saturate: initial;
          --tw-sepia: initial;
          --tw-drop-shadow: initial;
          --tw-drop-shadow-color: initial;
          --tw-drop-shadow-alpha: 100%;
          --tw-drop-shadow-size: initial;
        }
      }
    }

    :root, :host {
      --blur-none: 2px;
    }

    .blur-none {
      --tw-blur: blur(var(--blur-none));
      filter: var(--tw-blur, ) var(--tw-brightness, ) var(--tw-contrast, ) var(--tw-grayscale, ) var(--tw-hue-rotate, ) var(--tw-invert, ) var(--tw-saturate, ) var(--tw-sepia, ) var(--tw-drop-shadow, );
    }

    @property --tw-blur {
      syntax: "*";
      inherits: false
    }

    @property --tw-brightness {
      syntax: "*";
      inherits: false
    }

    @property --tw-contrast {
      syntax: "*";
      inherits: false
    }

    @property --tw-grayscale {
      syntax: "*";
      inherits: false
    }

    @property --tw-hue-rotate {
      syntax: "*";
      inherits: false
    }

    @property --tw-invert {
      syntax: "*";
      inherits: false
    }

    @property --tw-opacity {
      syntax: "*";
      inherits: false
    }

    @property --tw-saturate {
      syntax: "*";
      inherits: false
    }

    @property --tw-sepia {
      syntax: "*";
      inherits: false
    }

    @property --tw-drop-shadow {
      syntax: "*";
      inherits: false
    }

    @property --tw-drop-shadow-color {
      syntax: "*";
      inherits: false
    }

    @property --tw-drop-shadow-alpha {
      syntax: "<percentage>";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-drop-shadow-size {
      syntax: "*";
      inherits: false
    }"
  `)
})

