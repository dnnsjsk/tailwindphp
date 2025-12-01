/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('cursor', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --cursor-custom: url(/my-cursor.png);
        }
        @tailwind utilities;
      `,
      [
        'cursor-auto',
        'cursor-default',
        'cursor-pointer',
        'cursor-wait',
        'cursor-text',
        'cursor-move',
        'cursor-help',
        'cursor-not-allowed',
        'cursor-none',
        'cursor-context-menu',
        'cursor-progress',
        'cursor-cell',
        'cursor-crosshair',
        'cursor-vertical-text',
        'cursor-alias',
        'cursor-copy',
        'cursor-no-drop',
        'cursor-grab',
        'cursor-grabbing',
        'cursor-all-scroll',
        'cursor-col-resize',
        'cursor-row-resize',
        'cursor-n-resize',
        'cursor-e-resize',
        'cursor-s-resize',
        'cursor-w-resize',
        'cursor-ne-resize',
        'cursor-nw-resize',
        'cursor-se-resize',
        'cursor-sw-resize',
        'cursor-ew-resize',
        'cursor-ns-resize',
        'cursor-nesw-resize',
        'cursor-nwse-resize',
        'cursor-zoom-in',
        'cursor-zoom-out',
        'cursor-[var(--value)]',
        'cursor-custom',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --cursor-custom: url("/my-cursor.png");
    }

    .cursor-\\[var\\(--value\\)\\] {
      cursor: var(--value);
    }

    .cursor-alias {
      cursor: alias;
    }

    .cursor-all-scroll {
      cursor: all-scroll;
    }

    .cursor-auto {
      cursor: auto;
    }

    .cursor-cell {
      cursor: cell;
    }

    .cursor-col-resize {
      cursor: col-resize;
    }

    .cursor-context-menu {
      cursor: context-menu;
    }

    .cursor-copy {
      cursor: copy;
    }

    .cursor-crosshair {
      cursor: crosshair;
    }

    .cursor-custom {
      cursor: var(--cursor-custom);
    }

    .cursor-default {
      cursor: default;
    }

    .cursor-e-resize {
      cursor: e-resize;
    }

    .cursor-ew-resize {
      cursor: ew-resize;
    }

    .cursor-grab {
      cursor: grab;
    }

    .cursor-grabbing {
      cursor: grabbing;
    }

    .cursor-help {
      cursor: help;
    }

    .cursor-move {
      cursor: move;
    }

    .cursor-n-resize {
      cursor: n-resize;
    }

    .cursor-ne-resize {
      cursor: ne-resize;
    }

    .cursor-nesw-resize {
      cursor: nesw-resize;
    }

    .cursor-no-drop {
      cursor: no-drop;
    }

    .cursor-none {
      cursor: none;
    }

    .cursor-not-allowed {
      cursor: not-allowed;
    }

    .cursor-ns-resize {
      cursor: ns-resize;
    }

    .cursor-nw-resize {
      cursor: nw-resize;
    }

    .cursor-nwse-resize {
      cursor: nwse-resize;
    }

    .cursor-pointer {
      cursor: pointer;
    }

    .cursor-progress {
      cursor: progress;
    }

    .cursor-row-resize {
      cursor: row-resize;
    }

    .cursor-s-resize {
      cursor: s-resize;
    }

    .cursor-se-resize {
      cursor: se-resize;
    }

    .cursor-sw-resize {
      cursor: sw-resize;
    }

    .cursor-text {
      cursor: text;
    }

    .cursor-vertical-text {
      cursor: vertical-text;
    }

    .cursor-w-resize {
      cursor: w-resize;
    }

    .cursor-wait {
      cursor: wait;
    }

    .cursor-zoom-in {
      cursor: zoom-in;
    }

    .cursor-zoom-out {
      cursor: zoom-out;
    }"
  `)
  expect(
    await run([
      'cursor',
      '-cursor-auto',
      '-cursor-default',
      '-cursor-pointer',
      '-cursor-wait',
      '-cursor-text',
      '-cursor-move',
      '-cursor-help',
      '-cursor-not-allowed',
      '-cursor-none',
      '-cursor-context-menu',
      '-cursor-progress',
      '-cursor-cell',
      '-cursor-crosshair',
      '-cursor-vertical-text',
      '-cursor-alias',
      '-cursor-copy',
      '-cursor-no-drop',
      '-cursor-grab',
      '-cursor-grabbing',
      '-cursor-all-scroll',
      '-cursor-col-resize',
      '-cursor-row-resize',
      '-cursor-n-resize',
      '-cursor-e-resize',
      '-cursor-s-resize',
      '-cursor-w-resize',
      '-cursor-ne-resize',
      '-cursor-nw-resize',
      '-cursor-se-resize',
      '-cursor-sw-resize',
      '-cursor-ew-resize',
      '-cursor-ns-resize',
      '-cursor-nesw-resize',
      '-cursor-nwse-resize',
      '-cursor-zoom-in',
      '-cursor-zoom-out',
      '-cursor-[var(--value)]',
      '-cursor-custom',
      'cursor-auto/foo',
      'cursor-default/foo',
      'cursor-pointer/foo',
      'cursor-wait/foo',
      'cursor-text/foo',
      'cursor-move/foo',
      'cursor-help/foo',
      'cursor-not-allowed/foo',
      'cursor-none/foo',
      'cursor-context-menu/foo',
      'cursor-progress/foo',
      'cursor-cell/foo',
      'cursor-crosshair/foo',
      'cursor-vertical-text/foo',
      'cursor-alias/foo',
      'cursor-copy/foo',
      'cursor-no-drop/foo',
      'cursor-grab/foo',
      'cursor-grabbing/foo',
      'cursor-all-scroll/foo',
      'cursor-col-resize/foo',
      'cursor-row-resize/foo',
      'cursor-n-resize/foo',
      'cursor-e-resize/foo',
      'cursor-s-resize/foo',
      'cursor-w-resize/foo',
      'cursor-ne-resize/foo',
      'cursor-nw-resize/foo',
      'cursor-se-resize/foo',
      'cursor-sw-resize/foo',
      'cursor-ew-resize/foo',
      'cursor-ns-resize/foo',
      'cursor-nesw-resize/foo',
      'cursor-nwse-resize/foo',
      'cursor-zoom-in/foo',
      'cursor-zoom-out/foo',
      'cursor-[var(--value)]/foo',
      'cursor-custom/foo',
    ]),
  ).toEqual('')
})

