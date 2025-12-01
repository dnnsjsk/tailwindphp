/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

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

test('mask-y-to', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-y-to-0',
        'mask-y-to-1.5',
        'mask-y-to-2',
        'mask-y-to-0%',
        'mask-y-to-2%',
        'mask-y-to-[0px]',
        'mask-y-to-[0%]',

        'mask-y-to-(--my-var)',
        'mask-y-to-(color:--my-var)',
        'mask-y-to-(length:--my-var)',
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

    .mask-y-to-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-color: var(--my-var);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-\\(--my-var\\), .mask-y-to-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: var(--my-var);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: calc(var(--spacing) * 0);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: 0%;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: calc(var(--spacing) * 1.5);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: calc(var(--spacing) * 2);
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: 2%;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: 0%;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-y-to-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-to-position: 0px;
      --tw-mask-bottom: linear-gradient(to bottom, var(--tw-mask-bottom-from-color) var(--tw-mask-bottom-from-position), var(--tw-mask-bottom-to-color) var(--tw-mask-bottom-to-position));
      --tw-mask-bottom-to-position: 0px;
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
        'mask-y-to',
        'mask-y-to-2.8175',
        'mask-y-to--1.5',
        'mask-y-to--2',

        'mask-y-to-2.5%',
        'mask-y-to--5%',
        'mask-y-to-unknown',
        'mask-y-to-unknown%',

        '-mask-y-to-0',
        '-mask-y-to-1.5',
        '-mask-y-to-2',
        '-mask-y-to-0%',
        '-mask-y-to-2%',
        '-mask-y-to-[0px]',
        '-mask-y-to-[0%]',

        '-mask-y-to-(--my-var)',
        '-mask-y-to-(color:--my-var)',
        '-mask-y-to-(length:--my-var)',

        'mask-y-to-[-25%]',
        'mask-y-to-[25%]/foo',
        'mask-y-to-[-25%]/foo',
        '-mask-y-to-[-25%]',
        '-mask-y-to-[25%]/foo',
        '-mask-y-to-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

test('mask-linear', async () => {
  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      ['mask-linear-45', 'mask-linear-[3rad]', '-mask-linear-45'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-linear-position: 0deg;
          --tw-mask-linear-from-position: 0%;
          --tw-mask-linear-to-position: 100%;
          --tw-mask-linear-from-color: black;
          --tw-mask-linear-to-color: transparent;
        }
      }
    }

    .-mask-linear-45 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops, var(--tw-mask-linear-position)));
      --tw-mask-linear-position: calc(1deg * -45);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-45 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops, var(--tw-mask-linear-position)));
      --tw-mask-linear-position: calc(1deg * 45);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-\\[3rad\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops, var(--tw-mask-linear-position)));
      --tw-mask-linear-position: 171.887deg;
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

    @property --tw-mask-linear-position {
      syntax: "*";
      inherits: false;
      initial-value: 0deg;
    }

    @property --tw-mask-linear-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-linear-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-linear-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-linear-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }"
  `)
  expect(
    await run([
      'mask-linear',
      '-mask-linear',

      'mask-linear--75',
      'mask-linear-unknown',
      'mask-linear--75/foo',
      'mask-linear-unknown/foo',

      'mask-linear-45/foo',
      '-mask-linear-45/foo',

      'mask-linear-[3rad]/foo',
      '-mask-linear-[3rad]/foo',
    ]),
  ).toEqual('')
})

test('mask-linear-from', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-linear-from-0',
        'mask-linear-from-1.5',
        'mask-linear-from-2',
        'mask-linear-from-0%',
        'mask-linear-from-2%',
        'mask-linear-from-[0px]',
        'mask-linear-from-[0%]',

        'mask-linear-from-(--my-var)',
        'mask-linear-from-(color:--my-var)',
        'mask-linear-from-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-linear-position: 0deg;
          --tw-mask-linear-from-position: 0%;
          --tw-mask-linear-to-position: 100%;
          --tw-mask-linear-from-color: black;
          --tw-mask-linear-to-color: transparent;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-linear-from-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-\\(--my-var\\), .mask-linear-from-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-from-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-from-position: 0px;
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

    @property --tw-mask-linear-position {
      syntax: "*";
      inherits: false;
      initial-value: 0deg;
    }

    @property --tw-mask-linear-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-linear-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-linear-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-linear-to-color {
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
        'mask-linear-from',
        'mask-linear-from-2.8175',
        'mask-linear-from--1.5',
        'mask-linear-from--2',

        'mask-linear-from-2.5%',
        'mask-linear-from--5%',
        'mask-linear-from-unknown',
        'mask-linear-from-unknown%',

        '-mask-linear-from-0',
        '-mask-linear-from-1.5',
        '-mask-linear-from-2',
        '-mask-linear-from-0%',
        '-mask-linear-from-2%',
        '-mask-linear-from-[0px]',
        '-mask-linear-from-[0%]',

        '-mask-linear-from-(--my-var)',
        '-mask-linear-from-(color:--my-var)',
        '-mask-linear-from-(length:--my-var)',

        'mask-linear-from-[-25%]',
        'mask-linear-from-[25%]/foo',
        'mask-linear-from-[-25%]/foo',
        '-mask-linear-from-[-25%]',
        '-mask-linear-from-[25%]/foo',
        '-mask-linear-from-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

