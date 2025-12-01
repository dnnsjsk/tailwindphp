<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Layout Utilities Tests
 *
 * Port of layout tests from: packages/tailwindcss/src/utilities.test.ts
 * Lines: 40-122 (pointer-events, visibility, position)
 */
class layout extends TestCase
{
    #[Test]
    public function pointer_events(): void
    {
        $css = TestHelper::run(['pointer-events-none', 'pointer-events-auto']);

        // Check both utilities are generated
        $this->assertStringContainsString('.pointer-events-auto {', $css);
        $this->assertStringContainsString('pointer-events: auto;', $css);
        $this->assertStringContainsString('.pointer-events-none {', $css);
        $this->assertStringContainsString('pointer-events: none;', $css);
    }

    #[Test]
    public function pointer_events_invalid_variants_return_empty(): void
    {
        // These should all return empty - pointer-events doesn't support these forms
        $this->assertEquals('', TestHelper::run(['-pointer-events-none']));
        $this->assertEquals('', TestHelper::run(['-pointer-events-auto']));
        $this->assertEquals('', TestHelper::run(['pointer-events-[var(--value)]']));
        $this->assertEquals('', TestHelper::run(['pointer-events-none/foo']));
    }

    #[Test]
    public function visibility(): void
    {
        $css = TestHelper::run(['visible', 'invisible', 'collapse']);

        $this->assertStringContainsString('.collapse {', $css);
        $this->assertStringContainsString('visibility: collapse;', $css);

        $this->assertStringContainsString('.invisible {', $css);
        $this->assertStringContainsString('visibility: hidden;', $css);

        $this->assertStringContainsString('.visible {', $css);
        $this->assertStringContainsString('visibility: visible;', $css);
    }

    #[Test]
    public function visibility_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-visible']));
        $this->assertEquals('', TestHelper::run(['-invisible']));
        $this->assertEquals('', TestHelper::run(['-collapse']));
        $this->assertEquals('', TestHelper::run(['visible/foo']));
        $this->assertEquals('', TestHelper::run(['invisible/foo']));
        $this->assertEquals('', TestHelper::run(['collapse/foo']));
    }

    #[Test]
    public function position(): void
    {
        $css = TestHelper::run(['static', 'fixed', 'absolute', 'relative', 'sticky']);

        $this->assertStringContainsString('.absolute {', $css);
        $this->assertStringContainsString('position: absolute;', $css);

        $this->assertStringContainsString('.fixed {', $css);
        $this->assertStringContainsString('position: fixed;', $css);

        $this->assertStringContainsString('.relative {', $css);
        $this->assertStringContainsString('position: relative;', $css);

        $this->assertStringContainsString('.static {', $css);
        $this->assertStringContainsString('position: static;', $css);

        $this->assertStringContainsString('.sticky {', $css);
        $this->assertStringContainsString('position: sticky;', $css);
    }

    #[Test]
    public function position_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-static']));
        $this->assertEquals('', TestHelper::run(['-fixed']));
        $this->assertEquals('', TestHelper::run(['-absolute']));
        $this->assertEquals('', TestHelper::run(['-relative']));
        $this->assertEquals('', TestHelper::run(['-sticky']));
        $this->assertEquals('', TestHelper::run(['static/foo']));
        $this->assertEquals('', TestHelper::run(['fixed/foo']));
        $this->assertEquals('', TestHelper::run(['absolute/foo']));
        $this->assertEquals('', TestHelper::run(['relative/foo']));
        $this->assertEquals('', TestHelper::run(['sticky/foo']));
    }

    #[Test]
    public function isolation(): void
    {
        $css = TestHelper::run(['isolate', 'isolation-auto']);

        $this->assertStringContainsString('.isolate {', $css);
        $this->assertStringContainsString('isolation: isolate;', $css);

        $this->assertStringContainsString('.isolation-auto {', $css);
        $this->assertStringContainsString('isolation: auto;', $css);
    }

    #[Test]
    public function float(): void
    {
        $css = TestHelper::run(['float-start', 'float-end', 'float-right', 'float-left', 'float-none']);

        $this->assertStringContainsString('.float-start {', $css);
        $this->assertStringContainsString('float: inline-start;', $css);

        $this->assertStringContainsString('.float-end {', $css);
        $this->assertStringContainsString('float: inline-end;', $css);

        $this->assertStringContainsString('.float-right {', $css);
        $this->assertStringContainsString('float: right;', $css);

        $this->assertStringContainsString('.float-left {', $css);
        $this->assertStringContainsString('float: left;', $css);

        $this->assertStringContainsString('.float-none {', $css);
        $this->assertStringContainsString('float: none;', $css);
    }

    #[Test]
    public function clear(): void
    {
        $css = TestHelper::run(['clear-start', 'clear-end', 'clear-right', 'clear-left', 'clear-both', 'clear-none']);

        $this->assertStringContainsString('.clear-start {', $css);
        $this->assertStringContainsString('clear: inline-start;', $css);

        $this->assertStringContainsString('.clear-end {', $css);
        $this->assertStringContainsString('clear: inline-end;', $css);

        $this->assertStringContainsString('.clear-right {', $css);
        $this->assertStringContainsString('clear: right;', $css);

        $this->assertStringContainsString('.clear-left {', $css);
        $this->assertStringContainsString('clear: left;', $css);

        $this->assertStringContainsString('.clear-both {', $css);
        $this->assertStringContainsString('clear: both;', $css);

        $this->assertStringContainsString('.clear-none {', $css);
        $this->assertStringContainsString('clear: none;', $css);
    }

    #[Test]
    public function box_sizing(): void
    {
        $css = TestHelper::run(['box-border', 'box-content']);

        $this->assertStringContainsString('.box-border {', $css);
        $this->assertStringContainsString('box-sizing: border-box;', $css);

        $this->assertStringContainsString('.box-content {', $css);
        $this->assertStringContainsString('box-sizing: content-box;', $css);
    }

    #[Test]
    public function display(): void
    {
        $css = TestHelper::run([
            'block', 'inline-block', 'inline', 'flex', 'inline-flex',
            'table', 'inline-table', 'table-caption', 'table-cell',
            'table-column', 'table-column-group', 'table-footer-group',
            'table-header-group', 'table-row-group', 'table-row',
            'flow-root', 'grid', 'inline-grid', 'contents', 'list-item', 'hidden'
        ]);

        $this->assertStringContainsString('.block {', $css);
        $this->assertStringContainsString('display: block;', $css);

        $this->assertStringContainsString('.inline-block {', $css);
        $this->assertStringContainsString('display: inline-block;', $css);

        $this->assertStringContainsString('.inline {', $css);
        $this->assertStringContainsString('display: inline;', $css);

        $this->assertStringContainsString('.flex {', $css);
        $this->assertStringContainsString('display: flex;', $css);

        $this->assertStringContainsString('.hidden {', $css);
        $this->assertStringContainsString('display: none;', $css);

        $this->assertStringContainsString('.grid {', $css);
        $this->assertStringContainsString('display: grid;', $css);
    }

    #[Test]
    public function overflow(): void
    {
        $css = TestHelper::run([
            'overflow-auto', 'overflow-hidden', 'overflow-clip',
            'overflow-visible', 'overflow-scroll',
            'overflow-x-auto', 'overflow-y-auto'
        ]);

        $this->assertStringContainsString('.overflow-auto {', $css);
        $this->assertStringContainsString('overflow: auto;', $css);

        $this->assertStringContainsString('.overflow-hidden {', $css);
        $this->assertStringContainsString('overflow: hidden;', $css);

        $this->assertStringContainsString('.overflow-x-auto {', $css);
        $this->assertStringContainsString('overflow-x: auto;', $css);

        $this->assertStringContainsString('.overflow-y-auto {', $css);
        $this->assertStringContainsString('overflow-y: auto;', $css);
    }

    #[Test]
    public function overscroll(): void
    {
        $css = TestHelper::run([
            'overscroll-auto', 'overscroll-contain', 'overscroll-none',
            'overscroll-x-auto', 'overscroll-y-auto'
        ]);

        $this->assertStringContainsString('.overscroll-auto {', $css);
        $this->assertStringContainsString('overscroll-behavior: auto;', $css);

        $this->assertStringContainsString('.overscroll-contain {', $css);
        $this->assertStringContainsString('overscroll-behavior: contain;', $css);

        $this->assertStringContainsString('.overscroll-x-auto {', $css);
        $this->assertStringContainsString('overscroll-behavior-x: auto;', $css);
    }

    #[Test]
    public function scroll_behavior(): void
    {
        $css = TestHelper::run(['scroll-auto', 'scroll-smooth']);

        $this->assertStringContainsString('.scroll-auto {', $css);
        $this->assertStringContainsString('scroll-behavior: auto;', $css);

        $this->assertStringContainsString('.scroll-smooth {', $css);
        $this->assertStringContainsString('scroll-behavior: smooth;', $css);
    }

    #[Test]
    public function object_fit(): void
    {
        $css = TestHelper::run([
            'object-contain', 'object-cover', 'object-fill',
            'object-none', 'object-scale-down'
        ]);

        $this->assertStringContainsString('.object-contain {', $css);
        $this->assertStringContainsString('object-fit: contain;', $css);

        $this->assertStringContainsString('.object-cover {', $css);
        $this->assertStringContainsString('object-fit: cover;', $css);

        $this->assertStringContainsString('.object-fill {', $css);
        $this->assertStringContainsString('object-fit: fill;', $css);

        $this->assertStringContainsString('.object-none {', $css);
        $this->assertStringContainsString('object-fit: none;', $css);

        $this->assertStringContainsString('.object-scale-down {', $css);
        $this->assertStringContainsString('object-fit: scale-down;', $css);
    }

    #[Test]
    public function object_position(): void
    {
        $css = TestHelper::run([
            'object-bottom', 'object-center', 'object-left',
            'object-left-bottom', 'object-left-top', 'object-right',
            'object-right-bottom', 'object-right-top', 'object-top'
        ]);

        $this->assertStringContainsString('.object-bottom {', $css);
        $this->assertStringContainsString('object-position: bottom;', $css);

        $this->assertStringContainsString('.object-center {', $css);
        $this->assertStringContainsString('object-position: center;', $css);

        $this->assertStringContainsString('.object-left-bottom {', $css);
        $this->assertStringContainsString('object-position: left bottom;', $css);
    }

    #[Test]
    public function break_before(): void
    {
        $css = TestHelper::run([
            'break-before-auto', 'break-before-avoid', 'break-before-all',
            'break-before-avoid-page', 'break-before-page', 'break-before-left',
            'break-before-right', 'break-before-column'
        ]);

        $this->assertStringContainsString('.break-before-auto {', $css);
        $this->assertStringContainsString('break-before: auto;', $css);

        $this->assertStringContainsString('.break-before-page {', $css);
        $this->assertStringContainsString('break-before: page;', $css);
    }

    #[Test]
    public function break_inside(): void
    {
        $css = TestHelper::run([
            'break-inside-auto', 'break-inside-avoid',
            'break-inside-avoid-page', 'break-inside-avoid-column'
        ]);

        $this->assertStringContainsString('.break-inside-auto {', $css);
        $this->assertStringContainsString('break-inside: auto;', $css);

        $this->assertStringContainsString('.break-inside-avoid {', $css);
        $this->assertStringContainsString('break-inside: avoid;', $css);
    }

    #[Test]
    public function break_after(): void
    {
        $css = TestHelper::run([
            'break-after-auto', 'break-after-avoid', 'break-after-all',
            'break-after-avoid-page', 'break-after-page', 'break-after-left',
            'break-after-right', 'break-after-column'
        ]);

        $this->assertStringContainsString('.break-after-auto {', $css);
        $this->assertStringContainsString('break-after: auto;', $css);

        $this->assertStringContainsString('.break-after-column {', $css);
        $this->assertStringContainsString('break-after: column;', $css);
    }

    #[Test]
    public function box_decoration_break(): void
    {
        $css = TestHelper::run(['box-decoration-clone', 'box-decoration-slice']);

        $this->assertStringContainsString('.box-decoration-clone {', $css);
        $this->assertStringContainsString('box-decoration-break: clone;', $css);

        $this->assertStringContainsString('.box-decoration-slice {', $css);
        $this->assertStringContainsString('box-decoration-break: slice;', $css);
    }
}
