/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('mask-x-from', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-x-from-0',
        'mask-x-from-1.5',
        'mask-x-from-2',
        'mask-x-from-0%',
        'mask-x-from-2%',
        'mask-x-from-[0px]',
        'mask-x-from-[0%]',

        'mask-x-from-(--my-var)',
        'mask-x-from-(color:--my-var)',
        'mask-x-from-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-left: linear-gradient(#fff, #fff);
          --tw-mask-right: linear-gradient(#fff, #fff);
          --tw-mask-bottom: linear-gradient(#fff, #fff);
          --tw-mask-top: linear-gradient(#fff, #fff);
          --tw-mask-right-from-position: 0%;
          --tw-mask-right-to-position: 100%;
          --tw-mask-right-from-color: black;
          --tw-mask-right-to-color: transparent;
          --tw-mask-left-from-position: 0%;
          --tw-mask-left-to-position: 100%;
          --tw-mask-left-from-color: black;
          --tw-mask-left-to-color: transparent;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-x-from-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-color: var(--my-var);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-\\(--my-var\\), .mask-x-from-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: var(--my-var);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: calc(var(--spacing) * 0);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: 0%;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: calc(var(--spacing) * 1.5);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: calc(var(--spacing) * 2);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: 2%;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: 0%;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-from-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-from-position: 0px;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-from-position: 0px;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    @property --tw-mask-linear {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-radial {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-conic {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-left {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-right {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-bottom {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-top {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-right-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-right-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-right-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-right-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }

    @property --tw-mask-left-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-left-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-left-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-left-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-x-from',
        'mask-x-from-2.8175',
        'mask-x-from--1.5',
        'mask-x-from--2',

        'mask-x-from-2.5%',
        'mask-x-from--5%',
        'mask-x-from-unknown',
        'mask-x-from-unknown%',

        '-mask-x-from-0',
        '-mask-x-from-1.5',
        '-mask-x-from-2',
        '-mask-x-from-0%',
        '-mask-x-from-2%',
        '-mask-x-from-[0px]',
        '-mask-x-from-[0%]',

        '-mask-x-from-(--my-var)',
        '-mask-x-from-(color:--my-var)',
        '-mask-x-from-(length:--my-var)',

        'mask-x-from-[-25%]',
        'mask-x-from-[25%]/foo',
        'mask-x-from-[-25%]/foo',
        '-mask-x-from-[-25%]',
        '-mask-x-from-[25%]/foo',
        '-mask-x-from-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

test('mask-x-to', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-x-to-0',
        'mask-x-to-1.5',
        'mask-x-to-2',
        'mask-x-to-0%',
        'mask-x-to-2%',
        'mask-x-to-[0px]',
        'mask-x-to-[0%]',

        'mask-x-to-(--my-var)',
        'mask-x-to-(color:--my-var)',
        'mask-x-to-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-left: linear-gradient(#fff, #fff);
          --tw-mask-right: linear-gradient(#fff, #fff);
          --tw-mask-bottom: linear-gradient(#fff, #fff);
          --tw-mask-top: linear-gradient(#fff, #fff);
          --tw-mask-right-from-position: 0%;
          --tw-mask-right-to-position: 100%;
          --tw-mask-right-from-color: black;
          --tw-mask-right-to-color: transparent;
          --tw-mask-left-from-position: 0%;
          --tw-mask-left-to-position: 100%;
          --tw-mask-left-from-color: black;
          --tw-mask-left-to-color: transparent;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-x-to-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-color: var(--my-var);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-\\(--my-var\\), .mask-x-to-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: var(--my-var);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: calc(var(--spacing) * 0);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: 0%;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: calc(var(--spacing) * 1.5);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: calc(var(--spacing) * 2);
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: 2%;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: 0%;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-x-to-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-right: linear-gradient(to right, var(--tw-mask-right-from-color) var(--tw-mask-right-from-position), var(--tw-mask-right-to-color) var(--tw-mask-right-to-position));
      --tw-mask-right-to-position: 0px;
      --tw-mask-left: linear-gradient(to left, var(--tw-mask-left-from-color) var(--tw-mask-left-from-position), var(--tw-mask-left-to-color) var(--tw-mask-left-to-position));
      --tw-mask-left-to-position: 0px;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    @property --tw-mask-linear {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-radial {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-conic {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-left {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-right {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-bottom {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-top {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-right-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-right-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-right-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-right-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }

    @property --tw-mask-left-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-left-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-left-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-left-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-x-to',
        'mask-x-to-2.8175',
        'mask-x-to--1.5',
        'mask-x-to--2',

        'mask-x-to-2.5%',
        'mask-x-to--5%',
        'mask-x-to-unknown',
        'mask-x-to-unknown%',

        '-mask-x-to-0',
        '-mask-x-to-1.5',
        '-mask-x-to-2',
        '-mask-x-to-0%',
        '-mask-x-to-2%',
        '-mask-x-to-[0px]',
        '-mask-x-to-[0%]',

        '-mask-x-to-(--my-var)',
        '-mask-x-to-(color:--my-var)',
        '-mask-x-to-(length:--my-var)',

        'mask-x-to-[-25%]',
        'mask-x-to-[25%]/foo',
        'mask-x-to-[-25%]/foo',
        '-mask-x-to-[-25%]',
        '-mask-x-to-[25%]/foo',
        '-mask-x-to-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

test('mask-y-from', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-y-from-0',
        'mask-y-from-1.5',
        'mask-y-from-2',
        'mask-y-from-0%',
        'mask-y-from-2%',
        'mask-y-from-[0px]',
        'mask-y-from-[0%]',

        'mask-y-from-(--my-var)',
        'mask-y-from-(color:--my-var)',
        'mask-y-from-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-left: linear-gradient(#fff, #fff);
          --tw-mask-right: linear-gradient(#fff, #fff);
          --tw-mask-bottom: linear-gradient(#fff, #fff);
          --tw-mask-top: linear-gradient(#fff, #fff);
          --tw-mask-top-from-position: 0%;
          --tw-mask-top-to-position: 100%;
          --tw-mask-top-from-color: black;
          --tw-mask-top-to-color: transparent;
          --tw-mask-bottom-from-position: 0%;
          --tw-mask-bottom-to-position: 100%;
          --tw-mask-bottom-from-color: black;
          --tw-mask-bottom-to-color: transparent;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-y-from-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-color: var(--my-var);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-\\(--my-var\\), .mask-y-from-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: var(--my-var);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: calc(var(--spacing) * 0);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 0%;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: calc(var(--spacing) * 1.5);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: calc(var(--spacing) * 2);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 2%;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 0%;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-from-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 0px;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-from-position: 0px;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    @property --tw-mask-linear {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-radial {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-conic {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-left {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-right {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-bottom {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-top {
      syntax: "*";
      inherits: false;
      initial-value: linear-gradient(#fff, #fff);
    }

    @property --tw-mask-top-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-top-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-top-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-top-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }

    @property --tw-mask-bottom-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-bottom-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-bottom-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-bottom-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-y-from',
        'mask-y-from-2.8175',
        'mask-y-from--1.5',
        'mask-y-from--2',

        'mask-y-from-2.5%',
        'mask-y-from--5%',
        'mask-y-from-unknown',
        'mask-y-from-unknown%',

        '-mask-y-from-0',
        '-mask-y-from-1.5',
        '-mask-y-from-2',
        '-mask-y-from-0%',
        '-mask-y-from-2%',
        '-mask-y-from-[0px]',
        '-mask-y-from-[0%]',

        '-mask-y-from-(--my-var)',
        '-mask-y-from-(color:--my-var)',
        '-mask-y-from-(length:--my-var)',

        'mask-y-from-[-25%]',
        'mask-y-from-[25%]/foo',
        'mask-y-from-[-25%]/foo',
        '-mask-y-from-[-25%]',
        '-mask-y-from-[25%]/foo',
        '-mask-y-from-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

