/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('bg-blend', async () => {
  expect(
    await run([
      'bg-blend-normal',
      'bg-blend-multiply',
      'bg-blend-screen',
      'bg-blend-overlay',
      'bg-blend-darken',
      'bg-blend-lighten',
      'bg-blend-color-dodge',
      'bg-blend-color-burn',
      'bg-blend-hard-light',
      'bg-blend-soft-light',
      'bg-blend-difference',
      'bg-blend-exclusion',
      'bg-blend-hue',
      'bg-blend-saturation',
      'bg-blend-color',
      'bg-blend-luminosity',
    ]),
  ).toMatchInlineSnapshot(`
    ".bg-blend-color {
      background-blend-mode: color;
    }

    .bg-blend-color-burn {
      background-blend-mode: color-burn;
    }

    .bg-blend-color-dodge {
      background-blend-mode: color-dodge;
    }

    .bg-blend-darken {
      background-blend-mode: darken;
    }

    .bg-blend-difference {
      background-blend-mode: difference;
    }

    .bg-blend-exclusion {
      background-blend-mode: exclusion;
    }

    .bg-blend-hard-light {
      background-blend-mode: hard-light;
    }

    .bg-blend-hue {
      background-blend-mode: hue;
    }

    .bg-blend-lighten {
      background-blend-mode: lighten;
    }

    .bg-blend-luminosity {
      background-blend-mode: luminosity;
    }

    .bg-blend-multiply {
      background-blend-mode: multiply;
    }

    .bg-blend-normal {
      background-blend-mode: normal;
    }

    .bg-blend-overlay {
      background-blend-mode: overlay;
    }

    .bg-blend-saturation {
      background-blend-mode: saturation;
    }

    .bg-blend-screen {
      background-blend-mode: screen;
    }

    .bg-blend-soft-light {
      background-blend-mode: soft-light;
    }"
  `)
  expect(
    await run([
      'bg-blend',
      '-bg-blend-normal',
      '-bg-blend-multiply',
      '-bg-blend-screen',
      '-bg-blend-overlay',
      '-bg-blend-darken',
      '-bg-blend-lighten',
      '-bg-blend-color-dodge',
      '-bg-blend-color-burn',
      '-bg-blend-hard-light',
      '-bg-blend-soft-light',
      '-bg-blend-difference',
      '-bg-blend-exclusion',
      '-bg-blend-hue',
      '-bg-blend-saturation',
      '-bg-blend-color',
      '-bg-blend-luminosity',
      'bg-blend-normal/foo',
      'bg-blend-multiply/foo',
      'bg-blend-screen/foo',
      'bg-blend-overlay/foo',
      'bg-blend-darken/foo',
      'bg-blend-lighten/foo',
      'bg-blend-color-dodge/foo',
      'bg-blend-color-burn/foo',
      'bg-blend-hard-light/foo',
      'bg-blend-soft-light/foo',
      'bg-blend-difference/foo',
      'bg-blend-exclusion/foo',
      'bg-blend-hue/foo',
      'bg-blend-saturation/foo',
      'bg-blend-color/foo',
      'bg-blend-luminosity/foo',
    ]),
  ).toEqual('')
})

