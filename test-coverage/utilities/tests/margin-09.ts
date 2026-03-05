/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('mask-conic-from', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-conic-from-0',
        'mask-conic-from-1.5',
        'mask-conic-from-2',
        'mask-conic-from-0%',
        'mask-conic-from-2%',
        'mask-conic-from-[0px]',
        'mask-conic-from-[0%]',

        'mask-conic-from-(--my-var)',
        'mask-conic-from-(color:--my-var)',
        'mask-conic-from-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-conic-position: 0deg;
          --tw-mask-conic-from-position: 0%;
          --tw-mask-conic-to-position: 100%;
          --tw-mask-conic-from-color: black;
          --tw-mask-conic-to-color: transparent;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-conic-from-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-\\(--my-var\\), .mask-conic-from-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-from-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-from-position: 0px;
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

    @property --tw-mask-conic-position {
      syntax: "*";
      inherits: false;
      initial-value: 0deg;
    }

    @property --tw-mask-conic-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-conic-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-conic-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-conic-to-color {
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
        'mask-conic-from',
        'mask-conic-from-2.8175',
        'mask-conic-from--1.5',
        'mask-conic-from--2',

        'mask-conic-from-2.5%',
        'mask-conic-from--5%',
        'mask-conic-from-unknown',
        'mask-conic-from-unknown%',

        '-mask-conic-from-0',
        '-mask-conic-from-1.5',
        '-mask-conic-from-2',
        '-mask-conic-from-0%',
        '-mask-conic-from-2%',
        '-mask-conic-from-[0px]',
        '-mask-conic-from-[0%]',

        '-mask-conic-from-(--my-var)',
        '-mask-conic-from-(color:--my-var)',
        '-mask-conic-from-(length:--my-var)',

        'mask-conic-from-[-25%]',
        'mask-conic-from-[25%]/foo',
        'mask-conic-from-[-25%]/foo',
        '-mask-conic-from-[-25%]',
        '-mask-conic-from-[25%]/foo',
        '-mask-conic-from-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

test('mask-conic-to', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-conic-to-0',
        'mask-conic-to-1.5',
        'mask-conic-to-2',
        'mask-conic-to-0%',
        'mask-conic-to-2%',
        'mask-conic-to-[0px]',
        'mask-conic-to-[0%]',

        'mask-conic-to-(--my-var)',
        'mask-conic-to-(color:--my-var)',
        'mask-conic-to-(length:--my-var)',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-mask-linear: linear-gradient(#fff, #fff);
          --tw-mask-radial: linear-gradient(#fff, #fff);
          --tw-mask-conic: linear-gradient(#fff, #fff);
          --tw-mask-conic-position: 0deg;
          --tw-mask-conic-from-position: 0%;
          --tw-mask-conic-to-position: 100%;
          --tw-mask-conic-from-color: black;
          --tw-mask-conic-to-color: transparent;
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-conic-to-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-\\(--my-var\\), .mask-conic-to-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-conic-to-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-conic-stops: from var(--tw-mask-conic-position), var(--tw-mask-conic-from-color) var(--tw-mask-conic-from-position), var(--tw-mask-conic-to-color) var(--tw-mask-conic-to-position);
      --tw-mask-conic: conic-gradient(var(--tw-mask-conic-stops));
      --tw-mask-conic-to-position: 0px;
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

    @property --tw-mask-conic-position {
      syntax: "*";
      inherits: false;
      initial-value: 0deg;
    }

    @property --tw-mask-conic-from-position {
      syntax: "*";
      inherits: false;
      initial-value: 0%;
    }

    @property --tw-mask-conic-to-position {
      syntax: "*";
      inherits: false;
      initial-value: 100%;
    }

    @property --tw-mask-conic-from-color {
      syntax: "*";
      inherits: false;
      initial-value: black;
    }

    @property --tw-mask-conic-to-color {
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
        'mask-conic-to',
        'mask-conic-to-2.8175',
        'mask-conic-to--1.5',
        'mask-conic-to--2',

        'mask-conic-to-2.5%',
        'mask-conic-to--5%',
        'mask-conic-to-unknown',
        'mask-conic-to-unknown%',

        '-mask-conic-to-0',
        '-mask-conic-to-1.5',
        '-mask-conic-to-2',
        '-mask-conic-to-0%',
        '-mask-conic-to-2%',
        '-mask-conic-to-[0px]',
        '-mask-conic-to-[0%]',

        '-mask-conic-to-(--my-var)',
        '-mask-conic-to-(color:--my-var)',
        '-mask-conic-to-(length:--my-var)',

        'mask-conic-to-[-25%]',
        'mask-conic-to-[25%]/foo',
        'mask-conic-to-[-25%]/foo',
        '-mask-conic-to-[-25%]',
        '-mask-conic-to-[25%]/foo',
        '-mask-conic-to-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

test('mask-clip', async () => {
  expect(
    await run([
      'mask-clip-border',
      'mask-clip-padding',
      'mask-clip-content',
      'mask-clip-fill',
      'mask-clip-stroke',
      'mask-clip-view',
      'mask-no-clip',
    ]),
  ).toMatchInlineSnapshot(`
    ".mask-clip-border {
      -webkit-mask-clip: border-box;
      mask-clip: border-box;
    }

    .mask-clip-content {
      -webkit-mask-clip: content-box;
      mask-clip: content-box;
    }

    .mask-clip-fill {
      -webkit-mask-clip: fill-box;
      mask-clip: fill-box;
    }

    .mask-clip-padding {
      -webkit-mask-clip: padding-box;
      mask-clip: padding-box;
    }

    .mask-clip-stroke {
      -webkit-mask-clip: stroke-box;
      mask-clip: stroke-box;
    }

    .mask-clip-view {
      -webkit-mask-clip: view-box;
      mask-clip: view-box;
    }

    .mask-no-clip {
      -webkit-mask-clip: no-clip;
      mask-clip: no-clip;
    }"
  `)
  expect(
    await run([
      'mask-clip',
      '-mask-clip-border',
      '-mask-clip-padding',
      '-mask-clip-content',
      '-mask-clip-fill',
      '-mask-clip-stroke',
      '-mask-clip-view',
      '-mask-no-clip',
      'mask-clip-border/foo',
      'mask-clip-padding/foo',
      'mask-clip-content/foo',
      'mask-clip-fill/foo',
      'mask-clip-stroke/foo',
      'mask-clip-view/foo',
      'mask-no-clip/foo',
    ]),
  ).toEqual('')
})

test('mask-origin', async () => {
  expect(
    await run([
      'mask-origin-border',
      'mask-origin-padding',
      'mask-origin-content',
      'mask-origin-fill',
      'mask-origin-stroke',
      'mask-origin-view',
    ]),
  ).toMatchInlineSnapshot(`
    ".mask-origin-border {
      -webkit-mask-origin: border-box;
      mask-origin: border-box;
    }

    .mask-origin-content {
      -webkit-mask-origin: content-box;
      mask-origin: content-box;
    }

    .mask-origin-fill {
      -webkit-mask-origin: fill-box;
      mask-origin: fill-box;
    }

    .mask-origin-padding {
      -webkit-mask-origin: padding-box;
      mask-origin: padding-box;
    }

    .mask-origin-stroke {
      -webkit-mask-origin: stroke-box;
      mask-origin: stroke-box;
    }

    .mask-origin-view {
      -webkit-mask-origin: view-box;
      mask-origin: view-box;
    }"
  `)
  expect(
    await run([
      'mask-origin',
      '-mask-origin-border',
      '-mask-origin-padding',
      '-mask-origin-content',
      '-mask-origin-fill',
      '-mask-origin-stroke',
      '-mask-origin-view',
      'mask-origin-border/foo',
      'mask-origin-padding/foo',
      'mask-origin-content/foo',
      'mask-origin-fill/foo',
      'mask-origin-stroke/foo',
      'mask-origin-view/foo',
    ]),
  ).toEqual('')
})

test('mix-blend', async () => {
  expect(
    await run([
      'mix-blend-normal',
      'mix-blend-multiply',
      'mix-blend-screen',
      'mix-blend-overlay',
      'mix-blend-darken',
      'mix-blend-lighten',
      'mix-blend-color-dodge',
      'mix-blend-color-burn',
      'mix-blend-hard-light',
      'mix-blend-soft-light',
      'mix-blend-difference',
      'mix-blend-exclusion',
      'mix-blend-hue',
      'mix-blend-saturation',
      'mix-blend-color',
      'mix-blend-luminosity',
      'mix-blend-plus-darker',
      'mix-blend-plus-lighter',
    ]),
  ).toMatchInlineSnapshot(`
    ".mix-blend-color {
      mix-blend-mode: color;
    }

    .mix-blend-color-burn {
      mix-blend-mode: color-burn;
    }

    .mix-blend-color-dodge {
      mix-blend-mode: color-dodge;
    }

    .mix-blend-darken {
      mix-blend-mode: darken;
    }

    .mix-blend-difference {
      mix-blend-mode: difference;
    }

    .mix-blend-exclusion {
      mix-blend-mode: exclusion;
    }

    .mix-blend-hard-light {
      mix-blend-mode: hard-light;
    }

    .mix-blend-hue {
      mix-blend-mode: hue;
    }

    .mix-blend-lighten {
      mix-blend-mode: lighten;
    }

    .mix-blend-luminosity {
      mix-blend-mode: luminosity;
    }

    .mix-blend-multiply {
      mix-blend-mode: multiply;
    }

    .mix-blend-normal {
      mix-blend-mode: normal;
    }

    .mix-blend-overlay {
      mix-blend-mode: overlay;
    }

    .mix-blend-plus-darker {
      mix-blend-mode: plus-darker;
    }

    .mix-blend-plus-lighter {
      mix-blend-mode: plus-lighter;
    }

    .mix-blend-saturation {
      mix-blend-mode: saturation;
    }

    .mix-blend-screen {
      mix-blend-mode: screen;
    }

    .mix-blend-soft-light {
      mix-blend-mode: soft-light;
    }"
  `)
  expect(
    await run([
      'mix-blend',
      '-mix-blend-normal',
      '-mix-blend-multiply',
      '-mix-blend-screen',
      '-mix-blend-overlay',
      '-mix-blend-darken',
      '-mix-blend-lighten',
      '-mix-blend-color-dodge',
      '-mix-blend-color-burn',
      '-mix-blend-hard-light',
      '-mix-blend-soft-light',
      '-mix-blend-difference',
      '-mix-blend-exclusion',
      '-mix-blend-hue',
      '-mix-blend-saturation',
      '-mix-blend-color',
      '-mix-blend-luminosity',
      '-mix-blend-plus-lighter',
      'mix-blend-normal/foo',
      'mix-blend-multiply/foo',
      'mix-blend-screen/foo',
      'mix-blend-overlay/foo',
      'mix-blend-darken/foo',
      'mix-blend-lighten/foo',
      'mix-blend-color-dodge/foo',
      'mix-blend-color-burn/foo',
      'mix-blend-hard-light/foo',
      'mix-blend-soft-light/foo',
      'mix-blend-difference/foo',
      'mix-blend-exclusion/foo',
      'mix-blend-hue/foo',
      'mix-blend-saturation/foo',
      'mix-blend-color/foo',
      'mix-blend-luminosity/foo',
      'mix-blend-plus-darker/foo',
      'mix-blend-plus-lighter/foo',
    ]),
  ).toEqual('')
})

