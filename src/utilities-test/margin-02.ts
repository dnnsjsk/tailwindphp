/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('min-height', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'min-h-full',
        'min-h-auto',
        'min-h-screen',
        'min-h-svh',
        'min-h-lvh',
        'min-h-dvh',
        'min-h-min',
        'min-h-lh',
        'min-h-max',
        'min-h-fit',
        'min-h-4',
        'min-h-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .min-h-4 {
      min-height: var(--spacing-4);
    }

    .min-h-\\[4px\\] {
      min-height: 4px;
    }

    .min-h-auto {
      min-height: auto;
    }

    .min-h-dvh {
      min-height: 100dvh;
    }

    .min-h-fit {
      min-height: fit-content;
    }

    .min-h-full {
      min-height: 100%;
    }

    .min-h-lh {
      min-height: 1lh;
    }

    .min-h-lvh {
      min-height: 100lvh;
    }

    .min-h-max {
      min-height: max-content;
    }

    .min-h-min {
      min-height: min-content;
    }

    .min-h-screen {
      min-height: 100vh;
    }

    .min-h-svh {
      min-height: 100svh;
    }"
  `)
  expect(
    await run([
      'min-h',
      '-min-h-4',
      '-min-h-[4px]',
      'min-h-auto/foo',
      'min-h-full/foo',
      'min-h-screen/foo',
      'min-h-svh/foo',
      'min-h-lvh/foo',
      'min-h-dvh/foo',
      'min-h-lh/foo',
      'min-h-min/foo',
      'min-h-max/foo',
      'min-h-fit/foo',
      'min-h-4/foo',
      'min-h-[4px]/foo',
    ]),
  ).toEqual('')
})

test('max-height', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'max-h-none',
        'max-h-full',
        'max-h-screen',
        'max-h-svh',
        'max-h-lvh',
        'max-h-dvh',
        'max-h-lh',
        'max-h-min',
        'max-h-max',
        'max-h-fit',
        'max-h-4',
        'max-h-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .max-h-4 {
      max-height: var(--spacing-4);
    }

    .max-h-\\[4px\\] {
      max-height: 4px;
    }

    .max-h-dvh {
      max-height: 100dvh;
    }

    .max-h-fit {
      max-height: fit-content;
    }

    .max-h-full {
      max-height: 100%;
    }

    .max-h-lh {
      max-height: 1lh;
    }

    .max-h-lvh {
      max-height: 100lvh;
    }

    .max-h-max {
      max-height: max-content;
    }

    .max-h-min {
      max-height: min-content;
    }

    .max-h-none {
      max-height: none;
    }

    .max-h-screen {
      max-height: 100vh;
    }

    .max-h-svh {
      max-height: 100svh;
    }"
  `)
  expect(
    await run([
      'max-h',
      'max-h-auto',
      '-max-h-4',
      '-max-h-[4px]',
      'max-h-none/foo',
      'max-h-full/foo',
      'max-h-screen/foo',
      'max-h-svh/foo',
      'max-h-lvh/foo',
      'max-h-dvh/foo',
      'max-h-lh/foo',
      'max-h-min/foo',
      'max-h-max/foo',
      'max-h-fit/foo',
      'max-h-4/foo',
      'max-h-[4px]/foo',
    ]),
  ).toEqual('')
})

test('mask', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      [
        // mask-image
        'mask-none',
        'mask-[linear-gradient(#ffff,#0000)]',
        'mask-[url(http://example.com)]',
        'mask-[var(--some-var)]',
        'mask-[image:var(--some-var)]',
        'mask-[url:var(--some-var)]',

        // mask-composite
        'mask-add',
        'mask-subtract',
        'mask-intersect',
        'mask-exclude',

        // mask-mode
        'mask-alpha',
        'mask-luminance',
        'mask-match',

        // mask-type
        'mask-type-alpha',
        'mask-type-luminance',

        // mask-size
        'mask-auto',
        'mask-cover',
        'mask-contain',
        'mask-[cover]',
        'mask-[contain]',
        'mask-[size:120px_120px]',

        // mask-position
        'mask-center',
        'mask-top',
        'mask-top-right',
        'mask-top-left',
        'mask-bottom',
        'mask-bottom-right',
        'mask-bottom-left',
        'mask-left',
        'mask-right',
        'mask-center',
        'mask-[50%]',
        'mask-[120px]',
        'mask-[120px_120px]',
        'mask-[length:120px_120px]',
        'mask-[position:120px_120px]',

        // mask-repeat
        'mask-repeat',
        'mask-no-repeat',
        'mask-repeat-x',
        'mask-repeat-y',
        'mask-repeat-round',
        'mask-repeat-space',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ".mask-\\[image\\:var\\(--some-var\\)\\] {
      -webkit-mask-image: var(--some-var);
      -webkit-mask-image: var(--some-var);
      mask-image: var(--some-var);
    }

    .mask-\\[linear-gradient\\(\\#ffff\\,\\#0000\\)\\] {
      -webkit-mask-image: linear-gradient(#fff, #0000);
      mask-image: linear-gradient(#fff, #0000);
    }

    .mask-\\[url\\(http\\:\\/\\/example\\.com\\)\\] {
      -webkit-mask-image: url("http://example.com");
      mask-image: url("http://example.com");
    }

    .mask-\\[url\\:var\\(--some-var\\)\\], .mask-\\[var\\(--some-var\\)\\] {
      -webkit-mask-image: var(--some-var);
      -webkit-mask-image: var(--some-var);
      mask-image: var(--some-var);
    }

    .mask-none {
      -webkit-mask-image: none;
      mask-image: none;
    }

    .mask-add {
      -webkit-mask-composite: source-over;
      -webkit-mask-composite: source-over;
      mask-composite: add;
    }

    .mask-exclude {
      -webkit-mask-composite: xor;
      -webkit-mask-composite: xor;
      mask-composite: exclude;
    }

    .mask-intersect {
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-subtract {
      -webkit-mask-composite: source-out;
      -webkit-mask-composite: source-out;
      mask-composite: subtract;
    }

    .mask-alpha {
      -webkit-mask-source-type: alpha;
      -webkit-mask-source-type: alpha;
      mask-mode: alpha;
    }

    .mask-luminance {
      -webkit-mask-source-type: luminance;
      -webkit-mask-source-type: luminance;
      mask-mode: luminance;
    }

    .mask-match {
      -webkit-mask-source-type: auto;
      -webkit-mask-source-type: auto;
      mask-mode: match-source;
    }

    .mask-type-alpha {
      mask-type: alpha;
    }

    .mask-type-luminance {
      mask-type: luminance;
    }

    .mask-\\[contain\\] {
      -webkit-mask-size: contain;
      mask-size: contain;
    }

    .mask-\\[cover\\] {
      -webkit-mask-size: cover;
      mask-size: cover;
    }

    .mask-\\[length\\:120px_120px\\], .mask-\\[size\\:120px_120px\\] {
      -webkit-mask-size: 120px 120px;
      mask-size: 120px 120px;
    }

    .mask-auto {
      -webkit-mask-size: auto;
      mask-size: auto;
    }

    .mask-contain {
      -webkit-mask-size: contain;
      mask-size: contain;
    }

    .mask-cover {
      -webkit-mask-size: cover;
      mask-size: cover;
    }

    .mask-\\[50\\%\\] {
      -webkit-mask-position: 50%;
      mask-position: 50%;
    }

    .mask-\\[120px\\] {
      -webkit-mask-position: 120px;
      mask-position: 120px;
    }

    .mask-\\[120px_120px\\], .mask-\\[position\\:120px_120px\\] {
      -webkit-mask-position: 120px 120px;
      mask-position: 120px 120px;
    }

    .mask-bottom {
      -webkit-mask-position: bottom;
      mask-position: bottom;
    }

    .mask-bottom-left {
      -webkit-mask-position: 0 100%;
      mask-position: 0 100%;
    }

    .mask-bottom-right {
      -webkit-mask-position: 100% 100%;
      mask-position: 100% 100%;
    }

    .mask-center {
      -webkit-mask-position: center;
      mask-position: center;
    }

    .mask-left {
      -webkit-mask-position: 0;
      mask-position: 0;
    }

    .mask-right {
      -webkit-mask-position: 100%;
      mask-position: 100%;
    }

    .mask-top {
      -webkit-mask-position: top;
      mask-position: top;
    }

    .mask-top-left {
      -webkit-mask-position: 0 0;
      mask-position: 0 0;
    }

    .mask-top-right {
      -webkit-mask-position: 100% 0;
      mask-position: 100% 0;
    }

    .mask-no-repeat {
      -webkit-mask-repeat: no-repeat;
      mask-repeat: no-repeat;
    }

    .mask-repeat {
      -webkit-mask-repeat: repeat;
      mask-repeat: repeat;
    }

    .mask-repeat-round {
      -webkit-mask-repeat: round;
      mask-repeat: round;
    }

    .mask-repeat-space {
      -webkit-mask-repeat: space;
      mask-repeat: space;
    }

    .mask-repeat-x {
      -webkit-mask-repeat: repeat-x;
      mask-repeat: repeat-x;
    }

    .mask-repeat-y {
      -webkit-mask-repeat: repeat-y;
      mask-repeat: repeat-y;
    }"
  `)
  expect(
    await run([
      'mask',
      'mask-unknown',

      // mask-image
      '-mask-none',
      'mask-none/foo',
      '-mask-[var(--some-var)]',
      'mask-[var(--some-var)]/foo',
      '-mask-[image:var(--some-var)]',
      'mask-[image:var(--some-var)]/foo',
      '-mask-[url:var(--some-var)]',
      'mask-[url:var(--some-var)]/foo',

      // mask-composite
      '-mask-add',
      '-mask-subtract',
      '-mask-intersect',
      '-mask-exclude',
      'mask-add/foo',
      'mask-subtract/foo',
      'mask-intersect/foo',
      'mask-exclude/foo',

      // mask-mode
      '-mask-alpha',
      '-mask-luminance',
      '-mask-match',
      'mask-alpha/foo',
      'mask-luminance/foo',
      'mask-match/foo',

      // mask-type
      '-mask-type-alpha',
      '-mask-type-luminance',
      'mask-type-alpha/foo',
      'mask-type-luminance/foo',

      // mask-size
      '-mask-auto',
      '-mask-cover',
      '-mask-contain',
      '-mask-auto/foo',
      '-mask-cover/foo',
      '-mask-contain/foo',

      // mask-position
      '-mask-center',
      '-mask-top',
      '-mask-right-top',
      '-mask-right-bottom',
      '-mask-bottom',
      '-mask-left-bottom',
      '-mask-left',
      '-mask-left-top',
      '-mask-center/foo',
      '-mask-top/foo',
      '-mask-right-top/foo',
      '-mask-right-bottom/foo',
      '-mask-bottom/foo',
      '-mask-left-bottom/foo',
      '-mask-left/foo',
      '-mask-left-top/foo',

      // mask-repeat
      'mask-repeat/foo',
      'mask-no-repeat/foo',
      'mask-repeat-x/foo',
      'mask-repeat-y/foo',
      'mask-round/foo',
      'mask-space/foo',
      '-mask-repeat',
      '-mask-no-repeat',
      '-mask-repeat-x',
      '-mask-repeat-y',
      '-mask-round',
      '-mask-space',
      '-mask-repeat/foo',
      '-mask-no-repeat/foo',
      '-mask-repeat-x/foo',
      '-mask-repeat-y/foo',
      '-mask-round/foo',
      '-mask-space/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme reference {
          --opacity-half: 0.5;
          --opacity-custom: var(--custom-opacity);
        }
        @tailwind utilities;
      `,
      ['mask-current/half', 'mask-current/custom', '[color:red]/half'],
    ),
  ).toMatchInlineSnapshot(`
    ".\\[color\\:red\\]\\/half {
      color: color-mix(in srgb, red .5, transparent);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .\\[color\\:red\\]\\/half {
        color: color-mix(in oklab, red var(--opacity-half, .5), transparent);
      }
    }"
  `)
})

test('mask-position', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      ['mask-position-[120px]', 'mask-position-[120px_120px]', 'mask-position-[var(--some-var)]'],
    ),
  ).toMatchInlineSnapshot(`
    ".mask-position-\\[120px\\] {
      -webkit-mask-position: 120px;
      mask-position: 120px;
    }

    .mask-position-\\[120px_120px\\] {
      -webkit-mask-position: 120px 120px;
      mask-position: 120px 120px;
    }

    .mask-position-\\[var\\(--some-var\\)\\] {
      -webkit-mask-position: var(--some-var);
      -webkit-mask-position: var(--some-var);
      mask-position: var(--some-var);
    }"
  `)
  expect(
    await run([
      'mask-position',
      'mask-position/foo',
      '-mask-position',
      '-mask-position/foo',

      'mask-position-[120px_120px]/foo',

      '-mask-position-[120px_120px]',
      '-mask-position-[120px_120px]/foo',
    ]),
  ).toEqual('')
})

test('mask-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
        }
        @tailwind utilities;
      `,
      ['mask-size-[120px]', 'mask-size-[120px_120px]', 'mask-size-[var(--some-var)]'],
    ),
  ).toMatchInlineSnapshot(`
    ".mask-size-\\[120px\\] {
      -webkit-mask-size: 120px;
      mask-size: 120px;
    }

    .mask-size-\\[120px_120px\\] {
      -webkit-mask-size: 120px 120px;
      mask-size: 120px 120px;
    }

    .mask-size-\\[var\\(--some-var\\)\\] {
      -webkit-mask-size: var(--some-var);
      -webkit-mask-size: var(--some-var);
      mask-size: var(--some-var);
    }"
  `)
  expect(
    await run([
      'mask-size',
      'mask-size/foo',
      '-mask-size',
      '-mask-size/foo',

      'mask-size-[120px_120px]/foo',

      '-mask-size-[120px_120px]',
      '-mask-size-[120px_120px]/foo',
    ]),
  ).toEqual('')
})

test('mask-t-from', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing: 0.25rem;
        }
        @tailwind utilities;
      `,
      [
        'mask-t-from-0',
        'mask-t-from-1.5',
        'mask-t-from-2',
        'mask-t-from-0%',
        'mask-t-from-2%',
        'mask-t-from-[0px]',
        'mask-t-from-[0%]',
        'mask-t-from-(--my-var)',
        'mask-t-from-(color:--my-var)',
        'mask-t-from-(length:--my-var)',
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
        }
      }
    }

    :root, :host {
      --spacing: .25rem;
    }

    .mask-t-from-\\(color\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-color: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-\\(--my-var\\), .mask-t-from-\\(length\\:--my-var\\) {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: var(--my-var);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-0 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: calc(var(--spacing) * 0);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-0\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-1\\.5 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: calc(var(--spacing) * 1.5);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-2 {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: calc(var(--spacing) * 2);
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-2\\% {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 2%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-\\[0\\%\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 0%;
      -webkit-mask-composite: source-in;
      -webkit-mask-composite: source-in;
      mask-composite: intersect;
    }

    .mask-t-from-\\[0px\\] {
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      -webkit-mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      mask-image: var(--tw-mask-linear), var(--tw-mask-radial), var(--tw-mask-conic);
      --tw-mask-linear: var(--tw-mask-left), var(--tw-mask-right), var(--tw-mask-bottom), var(--tw-mask-top);
      --tw-mask-top: linear-gradient(to top, var(--tw-mask-top-from-color) var(--tw-mask-top-from-position), var(--tw-mask-top-to-color) var(--tw-mask-top-to-position));
      --tw-mask-top-from-position: 0px;
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
        'mask-t-from',
        'mask-t-from-2.8175',
        'mask-t-from--1.5',
        'mask-t-from--2',

        'mask-t-from-2.5%',
        'mask-t-from--5%',
        'mask-t-from-unknown',
        'mask-t-from-unknown%',

        '-mask-t-from-0',
        '-mask-t-from-1.5',
        '-mask-t-from-2',
        '-mask-t-from-0%',
        '-mask-t-from-2%',
        '-mask-t-from-[0px]',
        '-mask-t-from-[0%]',

        '-mask-t-from-(--my-var)',
        '-mask-t-from-(color:--my-var)',
        '-mask-t-from-(length:--my-var)',

        'mask-l-from-[-25%]',
        'mask-l-from-[25%]/foo',
        'mask-l-from-[-25%]/foo',
        '-mask-l-from-[-25%]',
        '-mask-l-from-[25%]/foo',
        '-mask-l-from-[-25%]/foo',
      ],
    ),
  ).toEqual('')
})

