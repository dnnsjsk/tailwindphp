/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('aspect-ratio', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --aspect-video: 16 / 9;
        }
        @tailwind utilities;
      `,
      ['aspect-video', 'aspect-[10/9]', 'aspect-4/3'],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --aspect-video: 16 / 9;
    }

    .aspect-4\\/3 {
      aspect-ratio: 4 / 3;
    }

    .aspect-\\[10\\/9\\] {
      aspect-ratio: 10 / 9;
    }

    .aspect-video {
      aspect-ratio: var(--aspect-video);
    }"
  `)
  expect(
    await run([
      'aspect',
      'aspect-potato',
      '-aspect-video',
      '-aspect-[10/9]',
      'aspect-foo/bar',
      'aspect-video/foo',
      'aspect-[10/9]/foo',
      'aspect-4/3/foo',
      'aspect--4/3',
      'aspect--4/-3',
      'aspect-4/-3',
    ]),
  ).toEqual('')
})

