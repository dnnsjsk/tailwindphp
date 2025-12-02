/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('line-clamp', async () => {
  expect(await run(['line-clamp-4', 'line-clamp-99', 'line-clamp-[123]', 'line-clamp-none']))
    .toMatchInlineSnapshot(`
      ".line-clamp-4 {
        -webkit-line-clamp: 4;
        -webkit-box-orient: vertical;
        display: -webkit-box;
        overflow: hidden;
      }

      .line-clamp-99 {
        -webkit-line-clamp: 99;
        -webkit-box-orient: vertical;
        display: -webkit-box;
        overflow: hidden;
      }

      .line-clamp-\\[123\\] {
        -webkit-line-clamp: 123;
        -webkit-box-orient: vertical;
        display: -webkit-box;
        overflow: hidden;
      }

      .line-clamp-none {
        -webkit-line-clamp: unset;
        -webkit-box-orient: horizontal;
        display: block;
        overflow: visible;
      }"
    `)
  expect(
    await run([
      'line-clamp',
      'line-clamp--4',
      '-line-clamp-4',
      '-line-clamp-[123]',
      '-line-clamp-none',
      'line-clamp-unknown',
      'line-clamp-123.5',
      'line-clamp-4/foo',
      'line-clamp-99/foo',
      'line-clamp-[123]/foo',
      'line-clamp-none/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --line-clamp-none: 0;
        }
        @tailwind utilities;
      `,
      ['line-clamp-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --line-clamp-none: 0;
    }

    .line-clamp-none {
      -webkit-line-clamp: var(--line-clamp-none);
      -webkit-box-orient: vertical;
      display: -webkit-box;
      overflow: hidden;
    }"
  `)
})

test('list-style-position', async () => {
  expect(await run(['list-inside', 'list-outside'])).toMatchInlineSnapshot(`
    ".list-inside {
      list-style-position: inside;
    }

    .list-outside {
      list-style-position: outside;
    }"
  `)
  expect(
    await run(['-list-inside', '-list-outside', 'list-inside/foo', 'list-outside/foo']),
  ).toEqual('')
})

test('list', async () => {
  expect(await run(['list-none', 'list-disc', 'list-decimal', 'list-[var(--value)]']))
    .toMatchInlineSnapshot(`
      ".list-\\[var\\(--value\\)\\] {
        list-style-type: var(--value);
      }

      .list-decimal {
        list-style-type: decimal;
      }

      .list-disc {
        list-style-type: disc;
      }

      .list-none {
        list-style-type: none;
      }"
    `)
  expect(
    await run([
      '-list-none',
      '-list-disc',
      '-list-decimal',
      '-list-[var(--value)]',
      'list-none/foo',
      'list-disc/foo',
      'list-decimal/foo',
      'list-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --list-style-type-none: disc;
        }
        @tailwind utilities;
      `,
      ['list-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --list-style-type-none: disc;
    }

    .list-none {
      list-style-type: var(--list-style-type-none);
    }"
  `)
})

test('list-image', async () => {
  expect(await run(['list-image-none', 'list-image-[var(--value)]'])).toMatchInlineSnapshot(`
    ".list-image-\\[var\\(--value\\)\\] {
      list-style-image: var(--value);
    }

    .list-image-none {
      list-style-image: none;
    }"
  `)
  expect(
    await run([
      'list-image',
      '-list-image-none',
      '-list-image-[var(--value)]',
      'list-image-none/foo',
      'list-image-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --list-style-image-none: url(../foo.png);
        }
        @tailwind utilities;
      `,
      ['list-image-none'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --list-style-image-none: url("../foo.png");
    }

    .list-image-none {
      list-style-image: var(--list-style-image-none);
    }"
  `)
})

test('text-overflow', async () => {
  expect(await run(['text-ellipsis', 'text-clip'])).toMatchInlineSnapshot(`
    ".text-clip {
      text-overflow: clip;
    }

    .text-ellipsis {
      text-overflow: ellipsis;
    }"
  `)
  expect(await run(['-text-ellipsis', '-text-clip', 'text-ellipsis/foo', 'text-clip/foo'])).toEqual(
    '',
  )
})

test('text-align', async () => {
  expect(
    await run(['text-left', 'text-center', 'text-right', 'text-justify', 'text-start', 'text-end']),
  ).toMatchInlineSnapshot(`
    ".text-center {
      text-align: center;
    }

    .text-end {
      text-align: end;
    }

    .text-justify {
      text-align: justify;
    }

    .text-left {
      text-align: left;
    }

    .text-right {
      text-align: right;
    }

    .text-start {
      text-align: start;
    }"
  `)
  expect(
    await run([
      '-text-left',
      '-text-center',
      '-text-right',
      '-text-justify',
      '-text-start',
      '-text-end',
      'text-left/foo',
      'text-center/foo',
      'text-right/foo',
      'text-justify/foo',
      'text-start/foo',
      'text-end/foo',
    ]),
  ).toEqual('')
})

test('font', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --font-sans:
            ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
            'Segoe UI Symbol', 'Noto Color Emoji';
          --font-weight-bold: 650;
        }
        @tailwind utilities;
      `,
      [
        // font-family
        'font-sans',
        'font-["arial_rounded"]',
        'font-[ui-sans-serif]',
        'font-[var(--my-family)]',
        'font-[family-name:var(--my-family)]',
        'font-[generic-name:var(--my-family)]',

        // font-weight
        'font-bold',
        'font-[100]',
        'font-[number:var(--my-weight)]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-font-weight: initial;
        }
      }
    }

    :root, :host {
      --font-sans: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
      --font-weight-bold: 650;
    }

    .font-\\[\\"arial_rounded\\"\\] {
      font-family: arial rounded;
    }

    .font-\\[family-name\\:var\\(--my-family\\)\\], .font-\\[generic-name\\:var\\(--my-family\\)\\] {
      font-family: var(--my-family);
    }

    .font-\\[ui-sans-serif\\] {
      font-family: ui-sans-serif;
    }

    .font-sans {
      font-family: var(--font-sans);
    }

    .font-\\[100\\] {
      --tw-font-weight: 100;
      font-weight: 100;
    }

    .font-\\[number\\:var\\(--my-weight\\)\\] {
      --tw-font-weight: var(--my-weight);
      font-weight: var(--my-weight);
    }

    .font-\\[var\\(--my-family\\)\\] {
      --tw-font-weight: var(--my-family);
      font-weight: var(--my-family);
    }

    .font-bold {
      --tw-font-weight: var(--font-weight-bold);
      font-weight: var(--font-weight-bold);
    }

    @property --tw-font-weight {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await compileCss(
      css`
        @theme reference {
          --font-sans:
            ui-sans-serif, system-ui, sans-serif, 'Apple Color Emoji', 'Segoe UI Emoji',
            'Segoe UI Symbol', 'Noto Color Emoji';
          --font-weight-bold: 650;
        }
        @tailwind utilities;
      `,
      [
        'font',
        // font-family
        '-font-sans',

        // font-weight
        '-font-bold',

        'font-weight-bold',
        'font-sans/foo',
        'font-["arial_rounded"]/foo',
        'font-[ui-sans-serif]/foo',
        'font-[var(--my-family)]/foo',
        'font-[family-name:var(--my-family)]/foo',
        'font-[generic-name:var(--my-family)]/foo',
        'font-bold/foo',
        'font-[100]/foo',
        'font-[number:var(--my-weight)]/foo',
      ],
    ),
  ).toEqual('')
})

test('text-transform', async () => {
  expect(await run(['uppercase', 'lowercase', 'capitalize', 'normal-case'])).toMatchInlineSnapshot(`
    ".capitalize {
      text-transform: capitalize;
    }

    .lowercase {
      text-transform: lowercase;
    }

    .normal-case {
      text-transform: none;
    }

    .uppercase {
      text-transform: uppercase;
    }"
  `)
  expect(
    await run([
      '-uppercase',
      '-lowercase',
      '-capitalize',
      '-normal-case',
      'uppercase/foo',
      'lowercase/foo',
      'capitalize/foo',
      'normal-case/foo',
    ]),
  ).toEqual('')
})

test('font-style', async () => {
  expect(await run(['italic', 'not-italic'])).toMatchInlineSnapshot(`
    ".italic {
      font-style: italic;
    }

    .not-italic {
      font-style: normal;
    }"
  `)
  expect(await run(['-italic', '-not-italic', 'italic/foo', 'not-italic/foo'])).toEqual('')
})

test('font-stretch', async () => {
  expect(await run(['font-stretch-ultra-expanded', 'font-stretch-50%', 'font-stretch-200%']))
    .toMatchInlineSnapshot(`
      ".font-stretch-50\\% {
        font-stretch: 50%;
      }

      .font-stretch-200\\% {
        font-stretch: 200%;
      }

      .font-stretch-ultra-expanded {
        font-stretch: ultra-expanded;
      }"
    `)
  expect(
    await run([
      'font-stretch',
      'font-stretch-20%',
      'font-stretch-50',
      'font-stretch-400%',
      'font-stretch-50.5%',
      'font-stretch-potato',
      'font-stretch-ultra-expanded/foo',
      'font-stretch-50%/foo',
      'font-stretch-200%/foo',
    ]),
  ).toEqual('')
})

test('text-decoration-line', async () => {
  expect(await run(['underline', 'overline', 'line-through', 'no-underline']))
    .toMatchInlineSnapshot(`
      ".line-through {
        text-decoration-line: line-through;
      }

      .no-underline {
        text-decoration-line: none;
      }

      .overline {
        text-decoration-line: overline;
      }

      .underline {
        text-decoration-line: underline;
      }"
    `)
  expect(
    await run([
      '-underline',
      '-overline',
      '-line-through',
      '-no-underline',
      'underline/foo',
      'overline/foo',
      'line-through/foo',
      'no-underline/foo',
    ]),
  ).toEqual('')
})

test('leading', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --leading-tight: 1.25;
          --leading-6: 1.5rem;
        }
        @tailwind utilities;
      `,
      ['leading-tight', 'leading-6', 'leading-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-leading: initial;
        }
      }
    }

    :root, :host {
      --leading-tight: 1.25;
      --leading-6: 1.5rem;
    }

    .leading-6 {
      --tw-leading: var(--leading-6);
      line-height: var(--leading-6);
    }

    .leading-\\[var\\(--value\\)\\] {
      --tw-leading: var(--value);
      line-height: var(--value);
    }

    .leading-tight {
      --tw-leading: var(--leading-tight);
      line-height: var(--leading-tight);
    }

    @property --tw-leading {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'leading',
      '-leading-tight',
      '-leading-6',
      '-leading-[var(--value)]',
      'leading-tight/foo',
      'leading-6/foo',
      'leading-[var(--value)]/foo',
    ]),
  ).toEqual('')

  expect(
    await compileCss(
      css`
        @theme {
          --leading-none: 2;
        }
        @tailwind utilities;
      `,
      ['leading-none'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-leading: initial;
        }
      }
    }

    :root, :host {
      --leading-none: 2;
    }

    .leading-none {
      --tw-leading: var(--leading-none);
      line-height: var(--leading-none);
    }

    @property --tw-leading {
      syntax: "*";
      inherits: false
    }"
  `)
})

test('tracking', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --tracking-normal: 0em;
          --tracking-wide: 0.025em;
        }
        @tailwind utilities;
      `,
      ['tracking-normal', 'tracking-wide', 'tracking-[var(--value)]', '-tracking-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-tracking: initial;
        }
      }
    }

    :root, :host {
      --tracking-normal: 0em;
      --tracking-wide: .025em;
    }

    .-tracking-\\[var\\(--value\\)\\] {
      --tw-tracking: calc(var(--value) * -1);
      letter-spacing: calc(var(--value) * -1);
    }

    .tracking-\\[var\\(--value\\)\\] {
      --tw-tracking: var(--value);
      letter-spacing: var(--value);
    }

    .tracking-normal {
      --tw-tracking: var(--tracking-normal);
      letter-spacing: var(--tracking-normal);
    }

    .tracking-wide {
      --tw-tracking: var(--tracking-wide);
      letter-spacing: var(--tracking-wide);
    }

    @property --tw-tracking {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      'tracking',
      'tracking-normal/foo',
      'tracking-wide/foo',
      'tracking-[var(--value)]/foo',
      '-tracking-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('font-smoothing', async () => {
  expect(await run(['antialiased', 'subpixel-antialiased'])).toMatchInlineSnapshot(`
    ".antialiased {
      -webkit-font-smoothing: antialiased;
      -moz-osx-font-smoothing: grayscale;
    }

    .subpixel-antialiased {
      -webkit-font-smoothing: auto;
      -moz-osx-font-smoothing: auto;
    }"
  `)
  expect(
    await run([
      '-antialiased',
      '-subpixel-antialiased',
      'antialiased/foo',
      'subpixel-antialiased/foo',
    ]),
  ).toEqual('')
})

test('font-variant-numeric', async () => {
  expect(
    await run([
      'normal-nums',
      'ordinal',
      'slashed-zero',
      'lining-nums',
      'oldstyle-nums',
      'proportional-nums',
      'tabular-nums',
      'diagonal-fractions',
      'stacked-fractions',
    ]),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-ordinal: initial;
          --tw-slashed-zero: initial;
          --tw-numeric-figure: initial;
          --tw-numeric-spacing: initial;
          --tw-numeric-fraction: initial;
        }
      }
    }

    .diagonal-fractions {
      --tw-numeric-fraction: diagonal-fractions;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .lining-nums {
      --tw-numeric-figure: lining-nums;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .oldstyle-nums {
      --tw-numeric-figure: oldstyle-nums;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .ordinal {
      --tw-ordinal: ordinal;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .proportional-nums {
      --tw-numeric-spacing: proportional-nums;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .slashed-zero {
      --tw-slashed-zero: slashed-zero;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .stacked-fractions {
      --tw-numeric-fraction: stacked-fractions;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .tabular-nums {
      --tw-numeric-spacing: tabular-nums;
      font-variant-numeric: var(--tw-ordinal, ) var(--tw-slashed-zero, ) var(--tw-numeric-figure, ) var(--tw-numeric-spacing, ) var(--tw-numeric-fraction, );
    }

    .normal-nums {
      font-variant-numeric: normal;
    }

    @property --tw-ordinal {
      syntax: "*";
      inherits: false
    }

    @property --tw-slashed-zero {
      syntax: "*";
      inherits: false
    }

    @property --tw-numeric-figure {
      syntax: "*";
      inherits: false
    }

    @property --tw-numeric-spacing {
      syntax: "*";
      inherits: false
    }

    @property --tw-numeric-fraction {
      syntax: "*";
      inherits: false
    }"
  `)
  expect(
    await run([
      '-normal-nums',
      '-ordinal',
      '-slashed-zero',
      '-lining-nums',
      '-oldstyle-nums',
      '-proportional-nums',
      '-tabular-nums',
      '-diagonal-fractions',
      '-stacked-fractions',
      'normal-nums/foo',
      'ordinal/foo',
      'slashed-zero/foo',
      'lining-nums/foo',
      'oldstyle-nums/foo',
      'proportional-nums/foo',
      'tabular-nums/foo',
      'diagonal-fractions/foo',
      'stacked-fractions/foo',
    ]),
  ).toEqual('')
})

