/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('bg', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --color-red-500: #ef4444;
          --background-color-blue-500: #3b82f6;
        }
        @tailwind utilities;
      `,
      [
        // background-color
        'bg-red-500',
        'bg-red-500/50',
        'bg-red-500/2.25',
        'bg-red-500/2.5',
        'bg-red-500/2.75',
        'bg-red-500/[0.5]',
        'bg-red-500/[50%]',
        'bg-red-500/100',
        'bg-red-500/[100%]',
        'bg-blue-500',
        'bg-current',
        'bg-current/50',
        'bg-current/[0.5]',
        'bg-current/[50%]',
        'bg-current/[var(--bg-opacity)]',
        'bg-inherit',
        'bg-transparent',
        'bg-[#0088cc]',
        'bg-[#0088cc]/50',
        'bg-[#0088cc]/[0.5]',
        'bg-[#0088cc]/[50%]',
        'bg-[var(--some-var)]',
        'bg-[var(--some-var)]/50',
        'bg-[var(--some-var)]/[0.5]',
        'bg-[var(--some-var)]/[50%]',
        'bg-[color:var(--some-var)]',
        'bg-[color:var(--some-var)]/50',
        'bg-[color:var(--some-var)]/[0.5]',
        'bg-[color:var(--some-var)]/[50%]',

        // background-image
        'bg-none',

        // Modern linear-gradient API
        'bg-linear-to-t',
        'bg-linear-to-tr',
        'bg-linear-to-r',
        'bg-linear-to-br',
        'bg-linear-to-b',
        'bg-linear-to-bl',
        'bg-linear-to-l',
        'bg-linear-to-tl',
        'bg-linear-45',
        '-bg-linear-45',

        // With interpolation mode modifier
        'bg-linear-to-r/oklch',
        'bg-linear-to-r/oklab',
        'bg-linear-to-r/hsl',
        'bg-linear-to-r/srgb',
        'bg-linear-to-r/longer',
        'bg-linear-to-r/shorter',
        'bg-linear-to-r/increasing',
        'bg-linear-to-r/decreasing',
        'bg-linear-to-r/[in_hsl_longer_hue]',
        'bg-linear-45/oklab',
        '-bg-linear-45/oklab',
        'bg-linear-45/shorter',
        'bg-linear-45/[in_hsl_longer_hue]',
        'bg-conic/oklch',
        'bg-conic/oklab',
        'bg-conic/hsl',
        'bg-conic/srgb',
        'bg-conic/longer',
        'bg-conic/shorter',
        'bg-conic/increasing',
        'bg-conic/decreasing',
        'bg-conic/[in_hsl_longer_hue]',
        'bg-conic-45/oklab',
        '-bg-conic-45/oklab',
        'bg-conic-45/shorter',
        'bg-conic-45/[in_hsl_longer_hue]',
        'bg-radial/oklch',
        'bg-radial/oklab',
        'bg-radial/hsl',
        'bg-radial/srgb',
        'bg-radial/longer',
        'bg-radial/shorter',
        'bg-radial/increasing',
        'bg-radial/decreasing',
        'bg-radial/[in_hsl_longer_hue]',
        'bg-radial-[circle_at_center]',
        // Invalid but proves not converted to `in oklch longer hue` when used
        // as an arbitrary value
        'bg-linear-to-r/[longer]',

        'bg-[url(/image.png)]',
        'bg-[url:var(--my-url)]',
        'bg-[linear-gradient(to_bottom,red,blue)]',
        'bg-[image:var(--my-gradient)]',
        'bg-linear-[125deg]',
        'bg-linear-[1.3rad]',
        'bg-linear-[to_bottom]',
        '-bg-linear-[125deg]',
        '-bg-linear-[1.3rad]',

        // background-size
        'bg-auto',
        'bg-cover',
        'bg-contain',
        'bg-[cover]',
        'bg-[contain]',
        'bg-[size:120px_120px]',

        // background-attachment
        'bg-fixed',
        'bg-local',
        'bg-scroll',

        // background-position
        'bg-top',
        'bg-top-left',
        'bg-top-right',
        'bg-bottom',
        'bg-bottom-left',
        'bg-bottom-right',
        'bg-left',
        'bg-right',
        'bg-center',
        'bg-[50%]',
        'bg-[120px]',
        'bg-[120px_120px]',
        'bg-[length:120px_120px]',
        'bg-[position:120px_120px]',
        'bg-[size:120px_120px]',
        // Legacy versions in v4.0 and earlier
        'bg-right-top',
        'bg-right-bottom',
        'bg-left-bottom',
        'bg-left-top',

        // background-repeat
        'bg-repeat',
        'bg-no-repeat',
        'bg-repeat-x',
        'bg-repeat-y',
        'bg-repeat-round',
        'bg-repeat-space',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --color-red-500: #ef4444;
      --background-color-blue-500: #3b82f6;
    }

    .bg-\\[\\#0088cc\\] {
      background-color: #08c;
    }

    .bg-\\[\\#0088cc\\]\\/50, .bg-\\[\\#0088cc\\]\\/\\[0\\.5\\], .bg-\\[\\#0088cc\\]\\/\\[50\\%\\] {
      background-color: oklab(59.9824% -.067 -.124 / .5);
    }

    .bg-\\[color\\:var\\(--some-var\\)\\], .bg-\\[color\\:var\\(--some-var\\)\\]\\/50 {
      background-color: var(--some-var);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-\\[color\\:var\\(--some-var\\)\\]\\/50 {
        background-color: color-mix(in oklab, var(--some-var) 50%, transparent);
      }
    }

    .bg-\\[color\\:var\\(--some-var\\)\\]\\/\\[0\\.5\\] {
      background-color: var(--some-var);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-\\[color\\:var\\(--some-var\\)\\]\\/\\[0\\.5\\] {
        background-color: color-mix(in oklab, var(--some-var) 50%, transparent);
      }
    }

    .bg-\\[color\\:var\\(--some-var\\)\\]\\/\\[50\\%\\] {
      background-color: var(--some-var);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-\\[color\\:var\\(--some-var\\)\\]\\/\\[50\\%\\] {
        background-color: color-mix(in oklab, var(--some-var) 50%, transparent);
      }
    }

    .bg-\\[var\\(--some-var\\)\\], .bg-\\[var\\(--some-var\\)\\]\\/50 {
      background-color: var(--some-var);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-\\[var\\(--some-var\\)\\]\\/50 {
        background-color: color-mix(in oklab, var(--some-var) 50%, transparent);
      }
    }

    .bg-\\[var\\(--some-var\\)\\]\\/\\[0\\.5\\] {
      background-color: var(--some-var);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-\\[var\\(--some-var\\)\\]\\/\\[0\\.5\\] {
        background-color: color-mix(in oklab, var(--some-var) 50%, transparent);
      }
    }

    .bg-\\[var\\(--some-var\\)\\]\\/\\[50\\%\\] {
      background-color: var(--some-var);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-\\[var\\(--some-var\\)\\]\\/\\[50\\%\\] {
        background-color: color-mix(in oklab, var(--some-var) 50%, transparent);
      }
    }

    .bg-blue-500 {
      background-color: var(--background-color-blue-500);
    }

    .bg-current, .bg-current\\/50 {
      background-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-current\\/50 {
        background-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .bg-current\\/\\[0\\.5\\] {
      background-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-current\\/\\[0\\.5\\] {
        background-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .bg-current\\/\\[50\\%\\] {
      background-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-current\\/\\[50\\%\\] {
        background-color: color-mix(in oklab, currentcolor 50%, transparent);
      }
    }

    .bg-current\\/\\[var\\(--bg-opacity\\)\\] {
      background-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-current\\/\\[var\\(--bg-opacity\\)\\] {
        background-color: color-mix(in oklab, currentcolor var(--bg-opacity), transparent);
      }
    }

    .bg-inherit {
      background-color: inherit;
    }

    .bg-red-500 {
      background-color: var(--color-red-500);
    }

    .bg-red-500\\/2\\.5 {
      background-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-red-500\\/2\\.5 {
        background-color: color-mix(in oklab, var(--color-red-500) 2.5%, transparent);
      }
    }

    .bg-red-500\\/2\\.25 {
      background-color: #ef444406;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-red-500\\/2\\.25 {
        background-color: color-mix(in oklab, var(--color-red-500) 2.25%, transparent);
      }
    }

    .bg-red-500\\/2\\.75 {
      background-color: #ef444407;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-red-500\\/2\\.75 {
        background-color: color-mix(in oklab, var(--color-red-500) 2.75%, transparent);
      }
    }

    .bg-red-500\\/50 {
      background-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-red-500\\/50 {
        background-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .bg-red-500\\/100 {
      background-color: var(--color-red-500);
    }

    .bg-red-500\\/\\[0\\.5\\] {
      background-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-red-500\\/\\[0\\.5\\] {
        background-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .bg-red-500\\/\\[50\\%\\] {
      background-color: #ef444480;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-red-500\\/\\[50\\%\\] {
        background-color: color-mix(in oklab, var(--color-red-500) 50%, transparent);
      }
    }

    .bg-red-500\\/\\[100\\%\\] {
      background-color: var(--color-red-500);
    }

    .bg-transparent {
      background-color: #0000;
    }

    .-bg-linear-45 {
      --tw-gradient-position: calc(45deg * -1);
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .-bg-linear-45 {
        --tw-gradient-position: calc(45deg * -1) in oklab;
      }
    }

    .-bg-linear-45 {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .-bg-linear-45\\/oklab {
      --tw-gradient-position: calc(45deg * -1);
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .-bg-linear-45\\/oklab {
        --tw-gradient-position: calc(45deg * -1) in oklab;
      }
    }

    .-bg-linear-45\\/oklab {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-45 {
      --tw-gradient-position: 45deg;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-45 {
        --tw-gradient-position: 45deg in oklab;
      }
    }

    .bg-linear-45 {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-45\\/\\[in_hsl_longer_hue\\] {
      --tw-gradient-position: 45deg;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-45\\/\\[in_hsl_longer_hue\\] {
        --tw-gradient-position: 45deg in hsl longer hue;
      }
    }

    .bg-linear-45\\/\\[in_hsl_longer_hue\\] {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-45\\/oklab {
      --tw-gradient-position: 45deg;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-45\\/oklab {
        --tw-gradient-position: 45deg in oklab;
      }
    }

    .bg-linear-45\\/oklab {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-45\\/shorter {
      --tw-gradient-position: 45deg;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-45\\/shorter {
        --tw-gradient-position: 45deg in oklch shorter hue;
      }
    }

    .bg-linear-45\\/shorter {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-b {
      --tw-gradient-position: to bottom;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-b {
        --tw-gradient-position: to bottom in oklab;
      }
    }

    .bg-linear-to-b {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-bl {
      --tw-gradient-position: to bottom left;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-bl {
        --tw-gradient-position: to bottom left in oklab;
      }
    }

    .bg-linear-to-bl {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-br {
      --tw-gradient-position: to bottom right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-br {
        --tw-gradient-position: to bottom right in oklab;
      }
    }

    .bg-linear-to-br {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-l {
      --tw-gradient-position: to left;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-l {
        --tw-gradient-position: to left in oklab;
      }
    }

    .bg-linear-to-l {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r {
        --tw-gradient-position: to right in oklab;
      }
    }

    .bg-linear-to-r {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/\\[in_hsl_longer_hue\\] {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/\\[in_hsl_longer_hue\\] {
        --tw-gradient-position: to right in hsl longer hue;
      }
    }

    .bg-linear-to-r\\/\\[in_hsl_longer_hue\\] {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/\\[longer\\] {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/\\[longer\\] {
        --tw-gradient-position: to right longer;
      }
    }

    .bg-linear-to-r\\/\\[longer\\] {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/decreasing {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/decreasing {
        --tw-gradient-position: to right in oklch decreasing hue;
      }
    }

    .bg-linear-to-r\\/decreasing {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/hsl {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/hsl {
        --tw-gradient-position: to right in hsl;
      }
    }

    .bg-linear-to-r\\/hsl {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/increasing {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/increasing {
        --tw-gradient-position: to right in oklch increasing hue;
      }
    }

    .bg-linear-to-r\\/increasing {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/longer {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/longer {
        --tw-gradient-position: to right in oklch longer hue;
      }
    }

    .bg-linear-to-r\\/longer {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/oklab {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/oklab {
        --tw-gradient-position: to right in oklab;
      }
    }

    .bg-linear-to-r\\/oklab {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/oklch {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/oklch {
        --tw-gradient-position: to right in oklch;
      }
    }

    .bg-linear-to-r\\/oklch {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/shorter {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/shorter {
        --tw-gradient-position: to right in oklch shorter hue;
      }
    }

    .bg-linear-to-r\\/shorter {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-r\\/srgb {
      --tw-gradient-position: to right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-r\\/srgb {
        --tw-gradient-position: to right in srgb;
      }
    }

    .bg-linear-to-r\\/srgb {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-t {
      --tw-gradient-position: to top;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-t {
        --tw-gradient-position: to top in oklab;
      }
    }

    .bg-linear-to-t {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-tl {
      --tw-gradient-position: to top left;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-tl {
        --tw-gradient-position: to top left in oklab;
      }
    }

    .bg-linear-to-tl {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-to-tr {
      --tw-gradient-position: to top right;
    }

    @supports (background-image: linear-gradient(in lab, red, red)) {
      .bg-linear-to-tr {
        --tw-gradient-position: to top right in oklab;
      }
    }

    .bg-linear-to-tr {
      background-image: linear-gradient(var(--tw-gradient-stops));
    }

    .-bg-conic-45\\/oklab {
      --tw-gradient-position: from calc(45deg * -1) in oklab;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .-bg-linear-\\[1\\.3rad\\] {
      --tw-gradient-position: calc(74.4845deg * -1);
      background-image: linear-gradient(var(--tw-gradient-stops, calc(74.4845deg * -1)));
    }

    .-bg-linear-\\[125deg\\] {
      --tw-gradient-position: calc(125deg * -1);
      background-image: linear-gradient(var(--tw-gradient-stops, calc(125deg * -1)));
    }

    .bg-conic-45\\/\\[in_hsl_longer_hue\\] {
      --tw-gradient-position: from 45deg in hsl longer hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic-45\\/oklab {
      --tw-gradient-position: from 45deg in oklab;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic-45\\/shorter {
      --tw-gradient-position: from 45deg in oklch shorter hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/\\[in_hsl_longer_hue\\] {
      --tw-gradient-position: in hsl longer hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/decreasing {
      --tw-gradient-position: in oklch decreasing hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/hsl {
      --tw-gradient-position: in hsl;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/increasing {
      --tw-gradient-position: in oklch increasing hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/longer {
      --tw-gradient-position: in oklch longer hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/oklab {
      --tw-gradient-position: in oklab;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/oklch {
      --tw-gradient-position: in oklch;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/shorter {
      --tw-gradient-position: in oklch shorter hue;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-conic\\/srgb {
      --tw-gradient-position: in srgb;
      background-image: conic-gradient(var(--tw-gradient-stops));
    }

    .bg-linear-\\[1\\.3rad\\] {
      --tw-gradient-position: 74.4845deg;
      background-image: linear-gradient(var(--tw-gradient-stops, 74.4845deg));
    }

    .bg-linear-\\[125deg\\] {
      --tw-gradient-position: 125deg;
      background-image: linear-gradient(var(--tw-gradient-stops, 125deg));
    }

    .bg-linear-\\[to_bottom\\] {
      --tw-gradient-position: to bottom;
      background-image: linear-gradient(var(--tw-gradient-stops, to bottom));
    }

    .bg-radial-\\[circle_at_center\\] {
      --tw-gradient-position: circle at center;
      background-image: radial-gradient(var(--tw-gradient-stops, circle at center));
    }

    .bg-radial\\/\\[in_hsl_longer_hue\\] {
      --tw-gradient-position: in hsl longer hue;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/decreasing {
      --tw-gradient-position: in oklch decreasing hue;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/hsl {
      --tw-gradient-position: in hsl;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/increasing {
      --tw-gradient-position: in oklch increasing hue;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/longer {
      --tw-gradient-position: in oklch longer hue;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/oklab {
      --tw-gradient-position: in oklab;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/oklch {
      --tw-gradient-position: in oklch;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/shorter {
      --tw-gradient-position: in oklch shorter hue;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-radial\\/srgb {
      --tw-gradient-position: in srgb;
      background-image: radial-gradient(var(--tw-gradient-stops));
    }

    .bg-\\[image\\:var\\(--my-gradient\\)\\] {
      background-image: var(--my-gradient);
    }

    .bg-\\[linear-gradient\\(to_bottom\\,red\\,blue\\)\\] {
      background-image: linear-gradient(red, #00f);
    }

    .bg-\\[url\\(\\/image\\.png\\)\\] {
      background-image: url("/image.png");
    }

    .bg-\\[url\\:var\\(--my-url\\)\\] {
      background-image: var(--my-url);
    }

    .bg-none {
      background-image: none;
    }

    .bg-\\[contain\\] {
      background-size: contain;
    }

    .bg-\\[cover\\] {
      background-size: cover;
    }

    .bg-\\[length\\:120px_120px\\], .bg-\\[size\\:120px_120px\\] {
      background-size: 120px 120px;
    }

    .bg-auto {
      background-size: auto;
    }

    .bg-contain {
      background-size: contain;
    }

    .bg-cover {
      background-size: cover;
    }

    .bg-fixed {
      background-attachment: fixed;
    }

    .bg-local {
      background-attachment: local;
    }

    .bg-scroll {
      background-attachment: scroll;
    }

    .bg-\\[50\\%\\] {
      background-position: 50%;
    }

    .bg-\\[120px\\] {
      background-position: 120px;
    }

    .bg-\\[120px_120px\\], .bg-\\[position\\:120px_120px\\] {
      background-position: 120px 120px;
    }

    .bg-bottom {
      background-position: bottom;
    }

    .bg-bottom-left {
      background-position: 0 100%;
    }

    .bg-bottom-right {
      background-position: 100% 100%;
    }

    .bg-center {
      background-position: center;
    }

    .bg-left {
      background-position: 0;
    }

    .bg-left-bottom {
      background-position: 0 100%;
    }

    .bg-left-top {
      background-position: 0 0;
    }

    .bg-right {
      background-position: 100%;
    }

    .bg-right-bottom {
      background-position: 100% 100%;
    }

    .bg-right-top {
      background-position: 100% 0;
    }

    .bg-top {
      background-position: top;
    }

    .bg-top-left {
      background-position: 0 0;
    }

    .bg-top-right {
      background-position: 100% 0;
    }

    .bg-no-repeat {
      background-repeat: no-repeat;
    }

    .bg-repeat {
      background-repeat: repeat;
    }

    .bg-repeat-round {
      background-repeat: round;
    }

    .bg-repeat-space {
      background-repeat: space;
    }

    .bg-repeat-x {
      background-repeat: repeat-x;
    }

    .bg-repeat-y {
      background-repeat: repeat-y;
    }"
  `)
  expect(
    await run([
      'bg',
      'bg-unknown',

      // background-color
      '-bg-red-500',
      '-bg-red-500/50',
      '-bg-red-500/[0.5]',
      '-bg-red-500/[50%]',
      '-bg-current',
      '-bg-current/50',
      '-bg-current/[0.5]',
      '-bg-current/[50%]',
      '-bg-inherit',
      '-bg-transparent',
      '-bg-[#0088cc]',
      '-bg-[#0088cc]/50',
      '-bg-[#0088cc]/[0.5]',
      '-bg-[#0088cc]/[50%]',

      // background-image
      '-bg-none',
      '-bg-gradient-to-br',
      '-bg-linear-to-br',
      '-bg-linear-[to_bottom]',

      // background-size
      '-bg-auto',
      '-bg-cover',
      '-bg-contain',

      // background-attachment
      '-bg-fixed',
      '-bg-local',
      '-bg-scroll',

      // background-position
      '-bg-center',
      '-bg-top',
      '-bg-right-top',
      '-bg-right-bottom',
      '-bg-bottom',
      '-bg-left-bottom',
      '-bg-left',
      '-bg-left-top',

      // background-repeat
      '-bg-repeat',
      '-bg-no-repeat',
      '-bg-repeat-x',
      '-bg-repeat-y',
      '-bg-round',
      '-bg-space',

      'bg-none/foo',
      'bg-[url(/image.png)]/foo',
      'bg-[url:var(--my-url)]/foo',
      'bg-[linear-gradient(to_bottom,red,blue)]/foo',
      'bg-[image:var(--my-gradient)]/foo',
      'bg-linear-[to_bottom]/hsl',
      'bg-conic-[45deg]/hsl',
      'bg-conic-[circle_at_center]/hsl',
      'bg-auto/foo',
      'bg-cover/foo',
      'bg-contain/foo',
      'bg-[cover]/foo',
      'bg-[contain]/foo',
      'bg-[size:120px_120px]/foo',
      'bg-fixed/foo',
      'bg-local/foo',
      'bg-scroll/foo',
      'bg-center/foo',
      'bg-top/foo',
      'bg-right-top/foo',
      'bg-right-bottom/foo',
      'bg-bottom/foo',
      'bg-left-bottom/foo',
      'bg-left/foo',
      'bg-left-top/foo',
      'bg-[50%]/foo',
      'bg-[120px]/foo',
      'bg-[120px_120px]/foo',
      'bg-[length:120px_120px]/foo',
      'bg-[position:120px_120px]/foo',
      'bg-[size:120px_120px]/foo',
      'bg-repeat/foo',
      'bg-no-repeat/foo',
      'bg-repeat-x/foo',
      'bg-repeat-y/foo',
      'bg-round/foo',
      'bg-space/foo',
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
      ['bg-current/half', 'bg-current/custom', '[color:red]/half'],
    ),
  ).toMatchInlineSnapshot(`
    ".bg-current\\/custom {
      background-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-current\\/custom {
        background-color: color-mix(in oklab, currentcolor var(--opacity-custom, var(--custom-opacity)), transparent);
      }
    }

    .bg-current\\/half {
      background-color: currentColor;
    }

    @supports (color: color-mix(in lab, red, red)) {
      .bg-current\\/half {
        background-color: color-mix(in oklab, currentcolor var(--opacity-half, .5), transparent);
      }
    }

    .\\[color\\:red\\]\\/half {
      color: color-mix(in srgb, red .5, transparent);
    }

    @supports (color: color-mix(in lab, red, red)) {
      .\\[color\\:red\\]\\/half {
        color: color-mix(in oklab, red var(--opacity-half, .5), transparent);
      }
    }"
  `)
})

