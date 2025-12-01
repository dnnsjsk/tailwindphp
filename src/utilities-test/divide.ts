/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('divide-x', async () => {
  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      ['divide-x', 'divide-x-4', 'divide-x-123', 'divide-x-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-divide-x-reverse: 0;
          --tw-border-style: solid;
        }
      }
    }

    :where(.divide-x > :not(:last-child)) {
      --tw-divide-x-reverse: 0;
      border-inline-style: var(--tw-border-style);
      border-inline-start-width: calc(1px * var(--tw-divide-x-reverse));
      border-inline-end-width: calc(1px * calc(1 - var(--tw-divide-x-reverse)));
    }

    :where(.divide-x-4 > :not(:last-child)) {
      --tw-divide-x-reverse: 0;
      border-inline-style: var(--tw-border-style);
      border-inline-start-width: calc(4px * var(--tw-divide-x-reverse));
      border-inline-end-width: calc(4px * calc(1 - var(--tw-divide-x-reverse)));
    }

    :where(.divide-x-123 > :not(:last-child)) {
      --tw-divide-x-reverse: 0;
      border-inline-style: var(--tw-border-style);
      border-inline-start-width: calc(123px * var(--tw-divide-x-reverse));
      border-inline-end-width: calc(123px * calc(1 - var(--tw-divide-x-reverse)));
    }

    :where(.divide-x-\\[4px\\] > :not(:last-child)) {
      --tw-divide-x-reverse: 0;
      border-inline-style: var(--tw-border-style);
      border-inline-start-width: calc(4px * var(--tw-divide-x-reverse));
      border-inline-end-width: calc(4px * calc(1 - var(--tw-divide-x-reverse)));
    }

    @property --tw-divide-x-reverse {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(
    await run([
      '-divide-x',
      'divide-x--4',
      '-divide-x-4',
      '-divide-x-123',
      'divide-x-unknown',
      'divide-x/foo',
      'divide-x-4/foo',
      'divide-x-123/foo',
      'divide-x-[4px]/foo',
    ]),
  ).toEqual('')
})

test('divide-x with custom default border width', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --default-border-width: 2px;
        }
        @tailwind utilities;
      `,
      ['divide-x'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-divide-x-reverse: 0;
          --tw-border-style: solid;
        }
      }
    }

    :where(.divide-x > :not(:last-child)) {
      --tw-divide-x-reverse: 0;
      border-inline-style: var(--tw-border-style);
      border-inline-start-width: calc(2px * var(--tw-divide-x-reverse));
      border-inline-end-width: calc(2px * calc(1 - var(--tw-divide-x-reverse)));
    }

    @property --tw-divide-x-reverse {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(await run(['divide-x/foo'])).toEqual('')
})

test('divide-y', async () => {
  expect(
    await compileCss(
      css`
        @tailwind utilities;
      `,
      ['divide-y', 'divide-y-4', 'divide-y-123', 'divide-y-[4px]'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-divide-y-reverse: 0;
          --tw-border-style: solid;
        }
      }
    }

    :where(.divide-y > :not(:last-child)) {
      --tw-divide-y-reverse: 0;
      border-bottom-style: var(--tw-border-style);
      border-top-style: var(--tw-border-style);
      border-top-width: calc(1px * var(--tw-divide-y-reverse));
      border-bottom-width: calc(1px * calc(1 - var(--tw-divide-y-reverse)));
    }

    :where(.divide-y-4 > :not(:last-child)) {
      --tw-divide-y-reverse: 0;
      border-bottom-style: var(--tw-border-style);
      border-top-style: var(--tw-border-style);
      border-top-width: calc(4px * var(--tw-divide-y-reverse));
      border-bottom-width: calc(4px * calc(1 - var(--tw-divide-y-reverse)));
    }

    :where(.divide-y-123 > :not(:last-child)) {
      --tw-divide-y-reverse: 0;
      border-bottom-style: var(--tw-border-style);
      border-top-style: var(--tw-border-style);
      border-top-width: calc(123px * var(--tw-divide-y-reverse));
      border-bottom-width: calc(123px * calc(1 - var(--tw-divide-y-reverse)));
    }

    :where(.divide-y-\\[4px\\] > :not(:last-child)) {
      --tw-divide-y-reverse: 0;
      border-bottom-style: var(--tw-border-style);
      border-top-style: var(--tw-border-style);
      border-top-width: calc(4px * var(--tw-divide-y-reverse));
      border-bottom-width: calc(4px * calc(1 - var(--tw-divide-y-reverse)));
    }

    @property --tw-divide-y-reverse {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(
    await run([
      '-divide-y',
      'divide-y--4',
      '-divide-y-4',
      '-divide-y-123',
      'divide-y-unknown',
      'divide-y/foo',
      'divide-y-4/foo',
      'divide-y-123/foo',
      'divide-y-[4px]/foo',
    ]),
  ).toEqual('')
})

test('divide-y with custom default border width', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --default-border-width: 2px;
        }
        @tailwind utilities;
      `,
      ['divide-y'],
    ),
  ).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-divide-y-reverse: 0;
          --tw-border-style: solid;
        }
      }
    }

    :where(.divide-y > :not(:last-child)) {
      --tw-divide-y-reverse: 0;
      border-bottom-style: var(--tw-border-style);
      border-top-style: var(--tw-border-style);
      border-top-width: calc(2px * var(--tw-divide-y-reverse));
      border-bottom-width: calc(2px * calc(1 - var(--tw-divide-y-reverse)));
    }

    @property --tw-divide-y-reverse {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }

    @property --tw-border-style {
      syntax: "*";
      inherits: false;
      initial-value: solid;
    }"
  `)
  expect(await run(['divide-y/foo'])).toEqual('')
})

test('divide-x-reverse', async () => {
  expect(await run(['divide-x-reverse'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-divide-x-reverse: 0;
        }
      }
    }

    :where(.divide-x-reverse > :not(:last-child)) {
      --tw-divide-x-reverse: 1;
    }

    @property --tw-divide-x-reverse {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(await run(['-divide-x-reverse', 'divide-x-reverse/foo'])).toEqual('')
})

test('divide-y-reverse', async () => {
  expect(await run(['divide-y-reverse'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-divide-y-reverse: 0;
        }
      }
    }

    :where(.divide-y-reverse > :not(:last-child)) {
      --tw-divide-y-reverse: 1;
    }

    @property --tw-divide-y-reverse {
      syntax: "*";
      inherits: false;
      initial-value: 0;
    }"
  `)
  expect(await run(['-divide-y-reverse', 'divide-y-reverse/foo'])).toEqual('')
})

test('divide-style', async () => {
  expect(
    await run(['divide-solid', 'divide-dashed', 'divide-dotted', 'divide-double', 'divide-none']),
  ).toMatchInlineSnapshot(`
    ":where(.divide-dashed > :not(:last-child)) {
      --tw-border-style: dashed;
      border-style: dashed;
    }

    :where(.divide-dotted > :not(:last-child)) {
      --tw-border-style: dotted;
      border-style: dotted;
    }

    :where(.divide-double > :not(:last-child)) {
      --tw-border-style: double;
      border-style: double;
    }

    :where(.divide-none > :not(:last-child)) {
      --tw-border-style: none;
      border-style: none;
    }

    :where(.divide-solid > :not(:last-child)) {
      --tw-border-style: solid;
      border-style: solid;
    }"
  `)
  expect(
    await run([
      'divide',
      '-divide-solid',
      '-divide-dashed',
      '-divide-dotted',
      '-divide-double',
      '-divide-none',
      'divide-solid/foo',
      'divide-dashed/foo',
      'divide-dotted/foo',
      'divide-double/foo',
      'divide-none/foo',
    ]),
  ).toEqual('')
})

test('divide-color', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --border-color-best-blue: #6495ed;
        }
        @tailwind utilities;
      `,
      [
        'divide-red-500',
        'divide-red-500/50',
        'divide-red-500/2.25',
        'divide-red-500/2.5',
        'divide-red-500/2.75',
        'divide-red-500/[0.5]',
        'divide-red-500/[50%]',
        'divide-best-blue',
        'divide-current',
        'divide-current/50',
        'divide-current/[0.5]',
        'divide-current/[50%]',
        'divide-inherit',
        'divide-transparent',
        'divide-[#0088cc]',
        'divide-[#0088cc]/50',
        'divide-[#0088cc]/[0.5]',
        'divide-[#0088cc]/[50%]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --border-color-best-blue: #6495ed;
    }

    :where(.divide-\\[\\#0088cc\\] > :not(:last-child)) {
      border-color: #08c;
    }

    :where(.divide-\\[\\#0088cc\\]\\/50 > :not(:last-child)), :where(.divide-\\[\\#0088cc\\]\\/\\[0\\.5\\] > :not(:last-child)), :where(.divide-\\[\\#0088cc\\]\\/\\[50\\%\\] > :not(:last-child)) {
      border-color: oklab(59.9824% -.067 -.124 / .5);
    }

    :where(.divide-best-blue > :not(:last-child)) {
      border-color: var(--border-color-best-blue);
    }

    :where(.divide-current > :not(:last-child)), :where(.divide-current\\/50 > :not(:last-child)) {
      border-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-current\\/50 > :not(:last-child)) {
        border-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    :where(.divide-current\\/\\[0\\.5\\] > :not(:last-child)) {
      border-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-current\\/\\[0\\.5\\] > :not(:last-child)) {
        border-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    :where(.divide-current\\/\\[50\\%\\] > :not(:last-child)) {
      border-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-current\\/\\[50\\%\\] > :not(:last-child)) {
        border-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    :where(.divide-inherit > :not(:last-child)) {
      border-color: inherit;
    }

    :where(.divide-red-500 > :not(:last-child)) {
      border-color: var(--color-red-500);
    }

    :where(.divide-red-500\\/2\\.5 > :not(:last-child)) {
      border-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-red-500\\/2\\.5 > :not(:last-child)) {
        border-color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    :where(.divide-red-500\\/2\\.25 > :not(:last-child)) {
      border-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-red-500\\/2\\.25 > :not(:last-child)) {
        border-color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    :where(.divide-red-500\\/2\\.75 > :not(:last-child)) {
      border-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-red-500\\/2\\.75 > :not(:last-child)) {
        border-color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    :where(.divide-red-500\\/50 > :not(:last-child)) {
      border-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-red-500\\/50 > :not(:last-child)) {
        border-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    :where(.divide-red-500\\/\\[0\\.5\\] > :not(:last-child)) {
      border-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-red-500\\/\\[0\\.5\\] > :not(:last-child)) {
        border-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    :where(.divide-red-500\\/\\[50\\%\\] > :not(:last-child)) {
      border-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      :where(.divide-red-500\\/\\[50\\%\\] > :not(:last-child)) {
        border-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    :where(.divide-transparent > :not(:last-child)) {
      border-color: #0000;
    }"
  `)
  expect(
    await run([
      'divide',
      '-divide-red-500',
      '-divide-red-500/50',
      '-divide-red-500/[0.5]',
      '-divide-red-500/[50%]',
      '-divide-current',
      '-divide-current/50',
      '-divide-current/[0.5]',
      '-divide-current/[50%]',
      '-divide-inherit',
      '-divide-transparent',
      '-divide-[#0088cc]',
      '-divide-[#0088cc]/50',
      '-divide-[#0088cc]/[0.5]',
      '-divide-[#0088cc]/[50%]',
      'divide-red-500/foo',
      'divide-red-500/50/foo',
      'divide-red-500/[0.5]/foo',
      'divide-red-500/[50%]/foo',
      'divide-current/foo',
      'divide-current/50/foo',
      'divide-current/[0.5]/foo',
      'divide-current/[50%]/foo',
      'divide-inherit/foo',
      'divide-transparent/foo',
      'divide-[#0088cc]/foo',
      'divide-[#0088cc]/50/foo',
      'divide-[#0088cc]/[0.5]/foo',
      'divide-[#0088cc]/[50%]/foo',
    ]),
  ).toEqual('')
})

