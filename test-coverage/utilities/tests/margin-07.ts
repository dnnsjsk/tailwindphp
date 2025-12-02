/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('mask-linear-to', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-linear-to-0',
        'mask-linear-to-1.5',
        'mask-linear-to-2',
        'mask-linear-to-0%',
        'mask-linear-to-2%',
        'mask-linear-to-[0px]',
        'mask-linear-to-[0%]',

        'mask-linear-to-(--my-var)',
        'mask-linear-to-(color:--my-var)',
        'mask-linear-to-(length:--my-var)',
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

    .mask-linear-to-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-\\(--my-var\\), .mask-linear-to-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-linear-to-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear-stops: var(--tw-mask-linear-position), var(--tw-mask-linear-from-color) var(--tw-mask-linear-from-position), var(--tw-mask-linear-to-color) var(--tw-mask-linear-to-position);
      --tw-mask-linear: linear-gradient(var(--tw-mask-linear-stops));
      --tw-mask-linear-to-position: 0px;
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
        'mask-linear-to',
        'mask-linear-to-2.8175',
        'mask-linear-to--1.5',
        'mask-linear-to--2',

        'mask-linear-to-2.5%',
        'mask-linear-to--5%',
        'mask-linear-to-unknown',
        'mask-linear-to-unknown%',

        '-mask-linear-to-0',
        '-mask-linear-to-1.5',
        '-mask-linear-to-2',
        '-mask-linear-to-0%',
        '-mask-linear-to-2%',
        '-mask-linear-to-[0px]',
        '-mask-linear-to-[0%]',

        '-mask-linear-to-(--my-var)',
        '-mask-linear-to-(color:--my-var)',
        '-mask-linear-to-(length:--my-var)',

        'mask-linear-to-[-25%]',
        'mask-linear-to-[25%]/foo',
        'mask-linear-to-[-25%]/foo',
        '-mask-linear-to-[-25%]',
        '-mask-linear-to-[25%]/foo',
        '-mask-linear-to-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

test('mask-radial', async () => {
  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      [
        'mask-circle',
        'mask-ellipse',
        'mask-radial-closest-side',
        'mask-radial-farthest-side',
        'mask-radial-closest-corner',
        'mask-radial-farthest-corner',
        'mask-radial-[25%_25%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-radial-from-position: 0%;
          --tw-mask-radial-to-position: 100%;
          --tw-mask-radial-from-color: black;
          --tw-mask-radial-to-color: transparent;
          --tw-mask-radial-shape: ellipse;
          --tw-mask-radial-size: farthest-corner;
          --tw-mask-radial-position: center;
        }
      }
    }

    .mask-radial-\\[25\\%_25\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops, var(--tw-mask-radial-size)));
      --tw-mask-radial-size: 25% 25%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-circle {
      --tw-mask-radial-shape: circle;
    }

    .mask-ellipse {
      --tw-mask-radial-shape: ellipse;
    }

    .mask-radial-closest-corner {
      --tw-mask-radial-size: closest-corner;
    }

    .mask-radial-closest-side {
      --tw-mask-radial-size: closest-side;
    }

    .mask-radial-farthest-corner {
      --tw-mask-radial-size: farthest-corner;
    }

    .mask-radial-farthest-side {
      --tw-mask-radial-size: farthest-side;
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

    @property --tw-mask-radial-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-radial-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-radial-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-radial-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }

    @property --tw-mask-radial-shape {
      syntax: "*";
      inherits: false;
      initial-value: ellipse;
    }

    @property --tw-mask-radial-size {
      syntax: "*";
      inherits: false;
      initial-value: farthest-corner;
    }

    @property --tw-mask-radial-position {
      syntax: "*";
      inherits: false;
      initial-value: center;
    }"
  `)
  expect(
    await run([
      'mask-radial',
      'mask-radial-[25%_25%]/foo',
      'mask-radial/foo',
      '-mask-radial',
      '-mask-radial-[25%_25%]',
      '-mask-radial/foo',
      '-mask-radial-[25%_25%]/foo',

      'mask-radial-from-[-25%]',
      'mask-radial-from-[25%]/foo',
      'mask-radial-from-[-25%]/foo',
      '-mask-radial-from-[-25%]',
      '-mask-radial-from-[25%]/foo',
      '-mask-radial-from-[-25%]/foo',
    ]),
  ).toEqual('')
})

test('mask-radial-at', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-radial-at-top',
        'mask-radial-at-top-left',
        'mask-radial-at-top-right',
        'mask-radial-at-bottom',
        'mask-radial-at-bottom-left',
        'mask-radial-at-bottom-right',
        'mask-radial-at-left',
        'mask-radial-at-right',
        'mask-radial-at-[25%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ".mask-radial-at-\\[25\\%\\] {
      --tw-mask-radial-position: 25%;
    }

    .mask-radial-at-bottom {
      --tw-mask-radial-position: bottom;
    }

    .mask-radial-at-bottom-left {
      --tw-mask-radial-position: bottom left;
    }

    .mask-radial-at-bottom-right {
      --tw-mask-radial-position: bottom right;
    }

    .mask-radial-at-left {
      --tw-mask-radial-position: left;
    }

    .mask-radial-at-right {
      --tw-mask-radial-position: right;
    }

    .mask-radial-at-top {
      --tw-mask-radial-position: top;
    }

    .mask-radial-at-top-left {
      --tw-mask-radial-position: top left;
    }

    .mask-radial-at-top-right {
      --tw-mask-radial-position: top right;
    }"
  `)
  expect(
    await run([
      'mask-radial-at',
      'mask-radial-at/foo',
      'mask-radial-at-25',
      'mask-radial-at-unknown',
      'mask-radial-at-[25%]/foo',
      'mask-radial-at-top/foo',
      'mask-radial-at-top-left/foo',
      'mask-radial-at-top-right/foo',
      'mask-radial-at-bottom/foo',
      'mask-radial-at-bottom-left/foo',
      'mask-radial-at-bottom-right/foo',
      'mask-radial-at-left/foo',
      'mask-radial-at-right/foo',

      '-mask-radial-at',
      '-mask-radial-at/foo',
      '-mask-radial-at-25',
      '-mask-radial-at-unknown',
      '-mask-radial-at-[25%]',
      '-mask-radial-at-[25%]/foo',

      '-mask-radial-at-top',
      '-mask-radial-at-top-left',
      '-mask-radial-at-top-right',
      '-mask-radial-at-bottom',
      '-mask-radial-at-bottom-left',
      '-mask-radial-at-bottom-right',
      '-mask-radial-at-left',
      '-mask-radial-at-right',

      'mask-radial-at-[25%]/foo',
      'mask-radial-at-[-25%]/foo',
      '-mask-radial-at-[-25%]',
      '-mask-radial-at-[25%]/foo',
      '-mask-radial-at-[-25%]/foo',
    ]),
  ).toEqual('')
})

test('mask-radial-from', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-radial-from-0',
        'mask-radial-from-1.5',
        'mask-radial-from-2',
        'mask-radial-from-0%',
        'mask-radial-from-2%',
        'mask-radial-from-[0px]',
        'mask-radial-from-[0%]',

        'mask-radial-from-(--my-var)',
        'mask-radial-from-(color:--my-var)',
        'mask-radial-from-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-radial-from-position: 0%;
          --tw-mask-radial-to-position: 100%;
          --tw-mask-radial-from-color: black;
          --tw-mask-radial-to-color: transparent;
          --tw-mask-radial-shape: ellipse;
          --tw-mask-radial-size: farthest-corner;
          --tw-mask-radial-position: center;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-radial-from-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-\\(--my-var\\), .mask-radial-from-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-radial-from-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-radial-stops: var(--tw-mask-radial-shape) var(--tw-mask-radial-size) at var(--tw-mask-radial-position), var(--tw-mask-radial-from-color) var(--tw-mask-radial-from-position), var(--tw-mask-radial-to-color) var(--tw-mask-radial-to-position);
      --tw-mask-radial: radial-gradient(var(--tw-mask-radial-stops));
      --tw-mask-radial-from-position: 0px;
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

    @property --tw-mask-radial-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-radial-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-radial-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-radial-to-color {
      syntax: "*";
      inherits: false;
      initial-value: transparent;
    }

    @property --tw-mask-radial-shape {
      syntax: "*";
      inherits: false;
      initial-value: ellipse;
    }

    @property --tw-mask-radial-size {
      syntax: "*";
      inherits: false;
      initial-value: farthest-corner;
    }

    @property --tw-mask-radial-position {
      syntax: "*";
      inherits: false;
      initial-value: center;
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
        'mask-radial-from',
        'mask-radial-from-2.8175',
        'mask-radial-from--1.5',
        'mask-radial-from--2',

        'mask-radial-from-2.5%',
        'mask-radial-from--5%',
        'mask-radial-from-unknown',
        'mask-radial-from-unknown%',

        '-mask-radial-from-0',
        '-mask-radial-from-1.5',
        '-mask-radial-from-2',
        '-mask-radial-from-0%',
        '-mask-radial-from-2%',
        '-mask-radial-from-[0px]',
        '-mask-radial-from-[0%]',

        '-mask-radial-from-(--my-var)',
        '-mask-radial-from-(color:--my-var)',
        '-mask-radial-from-(length:--my-var)',

        'mask-radial-from-[-25%]',
        'mask-radial-from-[25%]/foo',
        'mask-radial-from-[-25%]/foo',
        '-mask-radial-from-[-25%]',
        '-mask-radial-from-[25%]/foo',
        '-mask-radial-from-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

