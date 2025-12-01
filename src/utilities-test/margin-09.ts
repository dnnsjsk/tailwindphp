/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

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

