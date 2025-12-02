/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('scroll-snap-type', async () => {
  expect(await run(['snap-none', 'snap-x', 'snap-y', 'snap-both'])).toMatchInlineSnapshot(`
    "@layer properties {
      @supports (((-webkit-hyphens: none)) and (not (margin-trim: inline))) or ((-moz-orient: inline) and (not (color: rgb(from red r g b)))) {
        *, :before, :after, ::backdrop {
          --tw-scroll-snap-strictness: proximity;
        }
      }
    }

    .snap-both {
      scroll-snap-type: both var(--tw-scroll-snap-strictness);
    }

    .snap-none {
      scroll-snap-type: none;
    }

    .snap-x {
      scroll-snap-type: x var(--tw-scroll-snap-strictness);
    }

    .snap-y {
      scroll-snap-type: y var(--tw-scroll-snap-strictness);
    }

    @property --tw-scroll-snap-strictness {
      syntax: "*";
      inherits: false;
      initial-value: proximity;
    }"
  `)
  expect(
    await run([
      '-snap-none',
      '-snap-x',
      '-snap-y',
      '-snap-both',
      'snap-none/foo',
      'snap-x/foo',
      'snap-y/foo',
      'snap-both/foo',
    ]),
  ).toEqual('')
})

test('scroll-snap-align', async () => {
  expect(await run(['snap-align-none', 'snap-start', 'snap-end', 'snap-center']))
    .toMatchInlineSnapshot(`
      ".snap-align-none {
        scroll-snap-align: none;
      }

      .snap-center {
        scroll-snap-align: center;
      }

      .snap-end {
        scroll-snap-align: end;
      }

      .snap-start {
        scroll-snap-align: start;
      }"
    `)
  expect(
    await run([
      '-snap-align-none',
      '-snap-start',
      '-snap-end',
      '-snap-center',
      'snap-align-none/foo',
      'snap-start/foo',
      'snap-end/foo',
      'snap-center/foo',
    ]),
  ).toEqual('')
})

test('scroll-snap-stop', async () => {
  expect(await run(['snap-normal', 'snap-always'])).toMatchInlineSnapshot(`
    ".snap-always {
      scroll-snap-stop: always;
    }

    .snap-normal {
      scroll-snap-stop: normal;
    }"
  `)
  expect(await run(['-snap-normal', '-snap-always', 'snap-normal/foo', 'snap-always/foo'])).toEqual(
    '',
  )
})

test('scroll-m', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-m-4', 'scroll-m-[4px]', '-scroll-m-4', '-scroll-m-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-m-4 {
      scroll-margin: calc(var(--spacing-4) * -1);
    }

    .-scroll-m-\\[var\\(--value\\)\\] {
      scroll-margin: calc(var(--value) * -1);
    }

    .scroll-m-4 {
      scroll-margin: var(--spacing-4);
    }

    .scroll-m-\\[4px\\] {
      scroll-margin: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-m',
      'scroll-m-4/foo',
      'scroll-m-[4px]/foo',
      '-scroll-m-4/foo',
      '-scroll-m-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-mx', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-mx-4', 'scroll-mx-[4px]', '-scroll-mx-4', '-scroll-mx-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-mx-4 {
      scroll-margin-inline: calc(var(--spacing-4) * -1);
    }

    .-scroll-mx-\\[var\\(--value\\)\\] {
      scroll-margin-inline: calc(var(--value) * -1);
    }

    .scroll-mx-4 {
      scroll-margin-inline: var(--spacing-4);
    }

    .scroll-mx-\\[4px\\] {
      scroll-margin-inline: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-mx',
      'scroll-mx-4/foo',
      'scroll-mx-[4px]/foo',
      '-scroll-mx-4/foo',
      '-scroll-mx-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-my', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-my-4', 'scroll-my-[4px]', '-scroll-my-4', '-scroll-my-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-my-4 {
      scroll-margin-block: calc(var(--spacing-4) * -1);
    }

    .-scroll-my-\\[var\\(--value\\)\\] {
      scroll-margin-block: calc(var(--value) * -1);
    }

    .scroll-my-4 {
      scroll-margin-block: var(--spacing-4);
    }

    .scroll-my-\\[4px\\] {
      scroll-margin-block: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-my',
      'scroll-my-4/foo',
      'scroll-my-[4px]/foo',
      '-scroll-my-4/foo',
      '-scroll-my-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-ms', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-ms-4', 'scroll-ms-[4px]', '-scroll-ms-4', '-scroll-ms-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-ms-4 {
      scroll-margin-inline-start: calc(var(--spacing-4) * -1);
    }

    .-scroll-ms-\\[var\\(--value\\)\\] {
      scroll-margin-inline-start: calc(var(--value) * -1);
    }

    .scroll-ms-4 {
      scroll-margin-inline-start: var(--spacing-4);
    }

    .scroll-ms-\\[4px\\] {
      scroll-margin-inline-start: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-ms',
      'scroll-ms-4/foo',
      'scroll-ms-[4px]/foo',
      '-scroll-ms-4/foo',
      '-scroll-ms-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-me', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-me-4', 'scroll-me-[4px]', '-scroll-me-4', '-scroll-me-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-me-4 {
      scroll-margin-inline-end: calc(var(--spacing-4) * -1);
    }

    .-scroll-me-\\[var\\(--value\\)\\] {
      scroll-margin-inline-end: calc(var(--value) * -1);
    }

    .scroll-me-4 {
      scroll-margin-inline-end: var(--spacing-4);
    }

    .scroll-me-\\[4px\\] {
      scroll-margin-inline-end: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-me',
      'scroll-me-4/foo',
      'scroll-me-[4px]/foo',
      '-scroll-me-4/foo',
      '-scroll-me-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-mt', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-mt-4', 'scroll-mt-[4px]', '-scroll-mt-4', '-scroll-mt-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-mt-4 {
      scroll-margin-top: calc(var(--spacing-4) * -1);
    }

    .-scroll-mt-\\[var\\(--value\\)\\] {
      scroll-margin-top: calc(var(--value) * -1);
    }

    .scroll-mt-4 {
      scroll-margin-top: var(--spacing-4);
    }

    .scroll-mt-\\[4px\\] {
      scroll-margin-top: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-mt',
      'scroll-mt-4/foo',
      'scroll-mt-[4px]/foo',
      '-scroll-mt-4/foo',
      '-scroll-mt-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-mr', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-mr-4', 'scroll-mr-[4px]', '-scroll-mr-4', '-scroll-mr-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-mr-4 {
      scroll-margin-right: calc(var(--spacing-4) * -1);
    }

    .-scroll-mr-\\[var\\(--value\\)\\] {
      scroll-margin-right: calc(var(--value) * -1);
    }

    .scroll-mr-4 {
      scroll-margin-right: var(--spacing-4);
    }

    .scroll-mr-\\[4px\\] {
      scroll-margin-right: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-mr',
      'scroll-mr-4/foo',
      'scroll-mr-[4px]/foo',
      '-scroll-mr-4/foo',
      '-scroll-mr-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-mb', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-mb-4', 'scroll-mb-[4px]', '-scroll-mb-4', '-scroll-mb-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-mb-4 {
      scroll-margin-bottom: calc(var(--spacing-4) * -1);
    }

    .-scroll-mb-\\[var\\(--value\\)\\] {
      scroll-margin-bottom: calc(var(--value) * -1);
    }

    .scroll-mb-4 {
      scroll-margin-bottom: var(--spacing-4);
    }

    .scroll-mb-\\[4px\\] {
      scroll-margin-bottom: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-mb',
      'scroll-mb-4/foo',
      'scroll-mb-[4px]/foo',
      '-scroll-mb-4/foo',
      '-scroll-mb-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-ml', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-ml-4', 'scroll-ml-[4px]', '-scroll-ml-4', '-scroll-ml-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .-scroll-ml-4 {
      scroll-margin-left: calc(var(--spacing-4) * -1);
    }

    .-scroll-ml-\\[var\\(--value\\)\\] {
      scroll-margin-left: calc(var(--value) * -1);
    }

    .scroll-ml-4 {
      scroll-margin-left: var(--spacing-4);
    }

    .scroll-ml-\\[4px\\] {
      scroll-margin-left: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-ml',
      'scroll-ml-4/foo',
      'scroll-ml-[4px]/foo',
      '-scroll-ml-4/foo',
      '-scroll-ml-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-p', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-p-4', 'scroll-p-[4px]', '-scroll-p-4', '-scroll-p-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-p-4 {
      scroll-padding: var(--spacing-4);
    }

    .scroll-p-\\[4px\\] {
      scroll-padding: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-p',
      'scroll-p-4/foo',
      'scroll-p-[4px]/foo',
      '-scroll-p-4/foo',
      '-scroll-p-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-px', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-px-4', 'scroll-px-[4px]', '-scroll-px-4', '-scroll-px-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-px-4 {
      scroll-padding-inline: var(--spacing-4);
    }

    .scroll-px-\\[4px\\] {
      scroll-padding-inline: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-px',
      'scroll-px-4/foo',
      'scroll-px-[4px]/foo',
      '-scroll-px-4/foo',
      '-scroll-px-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-py', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-py-4', 'scroll-py-[4px]', '-scroll-py-4', '-scroll-py-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-py-4 {
      scroll-padding-block: var(--spacing-4);
    }

    .scroll-py-\\[4px\\] {
      scroll-padding-block: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-py',
      'scroll-py-4/foo',
      'scroll-py-[4px]/foo',
      '-scroll-py-4/foo',
      '-scroll-py-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-ps', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-ps-4', 'scroll-ps-[4px]', '-scroll-ps-4', '-scroll-ps-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-ps-4 {
      scroll-padding-inline-start: var(--spacing-4);
    }

    .scroll-ps-\\[4px\\] {
      scroll-padding-inline-start: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-ps',
      'scroll-ps-4/foo',
      'scroll-ps-[4px]/foo',
      '-scroll-ps-4/foo',
      '-scroll-ps-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-pe', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-pe-4', 'scroll-pe-[4px]', '-scroll-pe-4', '-scroll-pe-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-pe-4 {
      scroll-padding-inline-end: var(--spacing-4);
    }

    .scroll-pe-\\[4px\\] {
      scroll-padding-inline-end: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-pe',
      'scroll-pe-4/foo',
      'scroll-pe-[4px]/foo',
      '-scroll-pe-4/foo',
      '-scroll-pe-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-pt', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-pt-4', 'scroll-pt-[4px]', '-scroll-pt-4', '-scroll-pt-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-pt-4 {
      scroll-padding-top: var(--spacing-4);
    }

    .scroll-pt-\\[4px\\] {
      scroll-padding-top: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-pt',
      'scroll-pt-4/foo',
      'scroll-pt-[4px]/foo',
      '-scroll-pt-4/foo',
      '-scroll-pt-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-pr', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-pr-4', 'scroll-pr-[4px]', '-scroll-pr-4', '-scroll-pr-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-pr-4 {
      scroll-padding-right: var(--spacing-4);
    }

    .scroll-pr-\\[4px\\] {
      scroll-padding-right: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-pr',
      'scroll-pr-4/foo',
      'scroll-pr-[4px]/foo',
      '-scroll-pr-4/foo',
      '-scroll-pr-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-pb', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-pb-4', 'scroll-pb-[4px]', '-scroll-pb-4', '-scroll-pb-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-pb-4 {
      scroll-padding-bottom: var(--spacing-4);
    }

    .scroll-pb-\\[4px\\] {
      scroll-padding-bottom: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-pb',
      'scroll-pb-4/foo',
      'scroll-pb-[4px]/foo',
      '-scroll-pb-4/foo',
      '-scroll-pb-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-pl', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      ['scroll-pl-4', 'scroll-pl-[4px]', '-scroll-pl-4', '-scroll-pl-[var(--value)]'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .scroll-pl-4 {
      scroll-padding-left: var(--spacing-4);
    }

    .scroll-pl-\\[4px\\] {
      scroll-padding-left: 4px;
    }"
  `)
  expect(
    await run([
      'scroll-pl',
      'scroll-pl-4/foo',
      'scroll-pl-[4px]/foo',
      '-scroll-pl-4/foo',
      '-scroll-pl-[var(--value)]/foo',
    ]),
  ).toEqual('')
})

test('scroll-behavior', async () => {
  expect(await run(['scroll-auto', 'scroll-smooth'])).toMatchInlineSnapshot(`
    ".scroll-auto {
      scroll-behavior: auto;
    }

    .scroll-smooth {
      scroll-behavior: smooth;
    }"
  `)
  expect(
    await run(['scroll', '-scroll-auto', '-scroll-smooth', 'scroll-auto/foo', 'scroll-smooth/foo']),
  ).toEqual('')
})

