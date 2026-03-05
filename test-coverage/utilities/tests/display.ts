/**
 * Extracted from tailwindcss/packages/tailwindcss/src/utilities.test.ts
 *
 * These tests show the expected CSS output for each utility class.
 * Use as reference when implementing PHP utilities.
 */

import { expect, test } from 'vitest'
import { compileCss, run } from './test-utils/run'

test('display', async () => {
  expect(
    await run([
      'block',
      'inline-block',
      'inline',
      'flex',
      'inline-flex',
      'table',
      'inline-table',
      'table-caption',
      'table-cell',
      'table-column',
      'table-column-group',
      'table-footer-group',
      'table-header-group',
      'table-row-group',
      'table-row',
      'flow-root',
      'grid',
      'inline-grid',
      'contents',
      'list-item',
      'hidden',
    ]),
  ).toMatchInlineSnapshot(`
    ".block {
      display: block;
    }

    .contents {
      display: contents;
    }

    .flex {
      display: flex;
    }

    .flow-root {
      display: flow-root;
    }

    .grid {
      display: grid;
    }

    .hidden {
      display: none;
    }

    .inline {
      display: inline;
    }

    .inline-block {
      display: inline-block;
    }

    .inline-flex {
      display: inline-flex;
    }

    .inline-grid {
      display: inline-grid;
    }

    .inline-table {
      display: inline-table;
    }

    .list-item {
      display: list-item;
    }

    .table {
      display: table;
    }

    .table-caption {
      display: table-caption;
    }

    .table-cell {
      display: table-cell;
    }

    .table-column {
      display: table-column;
    }

    .table-column-group {
      display: table-column-group;
    }

    .table-footer-group {
      display: table-footer-group;
    }

    .table-header-group {
      display: table-header-group;
    }

    .table-row {
      display: table-row;
    }

    .table-row-group {
      display: table-row-group;
    }"
  `)
  expect(
    await run([
      '-block',
      '-inline-block',
      '-inline',
      '-flex',
      '-inline-flex',
      '-table',
      '-inline-table',
      '-table-caption',
      '-table-cell',
      '-table-column',
      '-table-column-group',
      '-table-footer-group',
      '-table-header-group',
      '-table-row-group',
      '-table-row',
      '-flow-root',
      '-grid',
      '-inline-grid',
      '-contents',
      '-list-item',
      '-hidden',
      'block/foo',
      'inline-block/foo',
      'inline/foo',
      'flex/foo',
      'inline-flex/foo',
      'table/foo',
      'inline-table/foo',
      'table-caption/foo',
      'table-cell/foo',
      'table-column/foo',
      'table-column-group/foo',
      'table-footer-group/foo',
      'table-header-group/foo',
      'table-row-group/foo',
      'table-row/foo',
      'flow-root/foo',
      'grid/foo',
      'inline-grid/foo',
      'contents/foo',
      'list-item/foo',
      'hidden/foo',
    ]),
  ).toEqual('')
})

test('inline-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
          --container-xl: 36rem;
        }
        @tailwind utilities;
      `,
      [
        'inline-full',
        'inline-auto',
        'inline-screen',
        'inline-svw',
        'inline-lvw',
        'inline-dvw',
        'inline-min',
        'inline-max',
        'inline-fit',
        'inline-4',
        'inline-xl',
        'inline-1/2',
        'inline-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
      --container-xl: 36rem;
    }

    .inline-1\\/2 {
      inline-size: 50%;
    }

    .inline-4 {
      inline-size: var(--spacing-4);
    }

    .inline-\\[4px\\] {
      inline-size: 4px;
    }

    .inline-auto {
      inline-size: auto;
    }

    .inline-dvw {
      inline-size: 100dvw;
    }

    .inline-fit {
      inline-size: fit-content;
    }

    .inline-full {
      inline-size: 100%;
    }

    .inline-lvw {
      inline-size: 100lvw;
    }

    .inline-max {
      inline-size: max-content;
    }

    .inline-min {
      inline-size: min-content;
    }

    .inline-screen {
      inline-size: 100vw;
    }

    .inline-svw {
      inline-size: 100svw;
    }

    .inline-xl {
      inline-size: var(--container-xl);
    }"
  `)
  expect(
    await run([
      'inline--1',
      'inline--1/2',
      'inline--1/-2',
      'inline-1/-2',
      '-inline-4',
      '-inline-1/2',
      '-inline-[4px]',
      'inline-full/foo',
      'inline-auto/foo',
      'inline-screen/foo',
      'inline-svw/foo',
      'inline-lvw/foo',
      'inline-dvw/foo',
      'inline-min/foo',
      'inline-max/foo',
      'inline-fit/foo',
      'inline-4/foo',
      'inline-xl/foo',
      'inline-1/2/foo',
      'inline-[4px]/foo',
    ]),
  ).toEqual('')
})

test('block-size', async () => {
  expect(
    await compileCss(
      css`
        @theme {
          --spacing-4: 1rem;
        }
        @tailwind utilities;
      `,
      [
        'block-full',
        'block-auto',
        'block-screen',
        'block-svh',
        'block-lvh',
        'block-dvh',
        'block-min',
        'block-lh',
        'block-max',
        'block-fit',
        'block-4',
        'block-1/2',
        'block-[4px]',
      ],
    ),
  ).toMatchInlineSnapshot(`
    ":root, :host {
      --spacing-4: 1rem;
    }

    .block-1\\/2 {
      block-size: 50%;
    }

    .block-4 {
      block-size: var(--spacing-4);
    }

    .block-\\[4px\\] {
      block-size: 4px;
    }

    .block-auto {
      block-size: auto;
    }

    .block-dvh {
      block-size: 100dvh;
    }

    .block-fit {
      block-size: fit-content;
    }

    .block-full {
      block-size: 100%;
    }

    .block-lh {
      block-size: 1lh;
    }

    .block-lvh {
      block-size: 100lvh;
    }

    .block-max {
      block-size: max-content;
    }

    .block-min {
      block-size: min-content;
    }

    .block-screen {
      block-size: 100vh;
    }

    .block-svh {
      block-size: 100svh;
    }"
  `)
  expect(
    await run([
      '-block-4',
      'block--1',
      'block--1/2',
      'block--1/-2',
      'block-1/-2',
      '-block-1/2',
      '-block-[4px]',
      'block-full/foo',
      'block-auto/foo',
      'block-screen/foo',
      'block-svh/foo',
      'block-lvh/foo',
      'block-dvh/foo',
      'block-lh/foo',
      'block-min/foo',
      'block-max/foo',
      'block-fit/foo',
      'block-4/foo',
      'block-1/2/foo',
      'block-[4px]/foo',
    ]),
  ).toEqual('')
})

