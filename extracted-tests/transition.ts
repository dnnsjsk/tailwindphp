/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('transition', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --default-transition-timing-function: ease;
          --default-transition-duration: 100ms;
          --transition-property:
            color, background-color, border-color, text-decoration-color, fill, stroke, opacity,
            box-shadow, transform, filter, backdrop-filter;
          --transition-property-opacity: opacity;
        }
        @tailwind utilities;
      `,
      [
        'transition',
        'transition-none',
        'transition-all',
        'transition-transform',
        'transition-shadow',
        'transition-colors',
        'transition-opacity',
        'transition-[var(--value)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --default-transition-timing-function: ease;
      --default-transition-duration: .1s;
      --transition-property-opacity: opacity;
    }

    .transition {
      transition-property: color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --tw-gradient-from, --tw-gradient-via, --tw-gradient-to, opacity, box-shadow, transform, translate, scale, rotate, filter, -webkit-backdrop-filter, backdrop-filter, display, content-visibility, overlay, pointer-events;
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-\\[var\\(--value\\)\\] {
      transition-property: var(--value);
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-all {
      transition-property: all;
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-colors {
      transition-property: color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --tw-gradient-from, --tw-gradient-via, --tw-gradient-to;
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-opacity {
      transition-property: var(--transition-property-opacity);
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-shadow {
      transition-property: box-shadow;
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-transform {
      transition-property: transform, translate, scale, rotate;
      transition-timing-function: var(--tw-ease, var(--default-transition-timing-function));
      transition-duration: var(--tw-duration, var(--default-transition-duration));
    }

    .transition-none {
      transition-property: none;
    }"
  `)

  expect(
    await compileCss(
      css`
        @theme inline {
          --default-transition-timing-function: ease;
          --default-transition-duration: 100ms;
        }
        @tailwind utilities;
      `,
      ['transition', 'transition-all', 'transition-colors'],
    ),
  ).toMatchInlineSnapshot(`
    ".transition {
      transition-property: color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --tw-gradient-from, --tw-gradient-via, --tw-gradient-to, opacity, box-shadow, transform, translate, scale, rotate, filter, -webkit-backdrop-filter, backdrop-filter, display, content-visibility, overlay, pointer-events;
      transition-timing-function: var(--tw-ease, ease);
      transition-duration: var(--tw-duration, .1s);
    }

    .transition-all {
      transition-property: all;
      transition-timing-function: var(--tw-ease, ease);
      transition-duration: var(--tw-duration, .1s);
    }

    .transition-colors {
      transition-property: color, background-color, border-color, outline-color, text-decoration-color, fill, stroke, --tw-gradient-from, --tw-gradient-via, --tw-gradient-to;
      transition-timing-function: var(--tw-ease, ease);
      transition-duration: var(--tw-duration, .1s);
    }"
  `)

  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      ['transition-all'],
    ),
  ).toMatchInlineSnapshot(`
    ".transition-all {
      transition-property: all;
      transition-timing-function: var(--tw-ease, ease);
      transition-duration: var(--tw-duration, 0s);
    }"
  `)

  expect(
    await run([
      '-transition',
      '-transition-none',
      '-transition-all',
      '-transition-opacity',
      '-transition-[var(--value)]',
      'transition/foo',
      'transition-none/foo',
      'transition-all/foo',
      'transition-transform/foo',
      'transition-shadow/foo',
      'transition-colors/foo',
      'transition-opacity/foo',
      'transition-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --transition-property-colors: transform;
        }
        @tailwind utilities;
      `,
      ['transition-colors'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --transition-property-colors: transform;
    }

    .transition-colors {
      transition-property: var(--transition-property-colors);
      transition-timing-function: var(--tw-ease, ease);
      transition-duration: var(--tw-duration, 0s);
    }"
  `)
})

test('transition-behavior', async () => {
  expect(await run(['transition-discrete', 'transition-normal'])).toMatchInlineSnapshot(`
    ".transition-discrete {
      transition-behavior: allow-discrete;
    }

    .transition-normal {
      transition-behavior: normal;
    }"
  `)

  expect(await run(['-transition-discrete', '-transition-normal'])).toEqual('')
})

test('delay', async () => {
  expect(await run(['delay-123', 'delay-200', 'delay-[300ms]'])).toMatchInlineSnapshot(`
    ".delay-123 {
      transition-delay: .123s;
    }

    .delay-200 {
      transition-delay: .2s;
    }

    .delay-\\[300ms\\] {
      transition-delay: .3s;
    }"
  `)
  expect(
    await run([
      'delay',
      'delay--200',
      '-delay-200',
      '-delay-[300ms]',
      'delay-unknown',
      'delay-123/foo',
      'delay-200/foo',
      'delay-[300ms]/foo',
    ]),
  ).toEqual('')
})

test('duration', async () => {
  expect(await run(['duration-123', 'duration-200', 'duration-[300ms]'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-duration: initial;
        }
      }
    }

    .duration-123 {
      --tw-duration: .123s;
      transition-duration: .123s;
    }

    .duration-200 {
      --tw-duration: .2s;
      transition-duration: .2s;
    }

    .duration-\\[300ms\\] {
      --tw-duration: .3s;
      transition-duration: .3s;
    }

    @property --tw-duration {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'duration',
      'duration--200',
      '-duration-200',
      '-duration-[300ms]',
      'duration-123/foo',
      'duration-200/foo',
      'duration-[300ms]/foo',
    ]),
  ).toEqual('')
})

test('ease', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --ease-in: cubic-bezier(0.4, 0, 1, 1);
          --ease-out: cubic-bezier(0, 0, 0.2, 1);
        }
        @tailwind utilities;
      `,
      ['ease-in', 'ease-out', 'ease-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-ease: initial;
        }
      }
    }

    :root, :host {
      --ease-in: cubic-bezier(.4, 0, 1, 1);
      --ease-out: cubic-bezier(0, 0, .2, 1);
    }

    .ease-\\[var\\(--value\\)\\] {
      --tw-ease: var(--value);
      transition-timing-function: var(--value);
    }

    .ease-in {
      --tw-ease: var(--ease-in);
      transition-timing-function: var(--ease-in);
    }

    .ease-out {
      --tw-ease: var(--ease-out);
      transition-timing-function: var(--ease-out);
    }

    @property --tw-ease {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      '-ease-in',
      '-ease-out',
      '-ease-[var(--value)]',
      'ease-in/foo',
      'ease-out/foo',
      'ease-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --ease-linear: steps(4);
        }
        @tailwind utilities;
      `,
      ['ease-linear'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-ease: initial;
        }
      }
    }

    :root, :host {
      --ease-linear: steps(4);
    }

    .ease-linear {
      --tw-ease: var(--ease-linear);
      transition-timing-function: var(--ease-linear);
    }

    @property --tw-ease {
      syntax: "*";
      inherits: false
    }"
  `)
})

