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

