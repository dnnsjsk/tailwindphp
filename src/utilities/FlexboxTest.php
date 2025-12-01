<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\TestHelper;

use function TailwindPHP\Utilities\registerFlexboxUtilities;

/**
 * Flexbox & Grid Utilities Tests
 *
 * Port of flexbox/grid tests from: packages/tailwindcss/src/utilities.test.ts
 */
class FlexboxTest extends TestCase
{
    private TestHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestHelper();
        $this->helper->registerUtilities(function ($builder) {
            registerFlexboxUtilities($builder);
        });
    }

    #[Test]
    public function flex_direction(): void
    {
        $css = $this->helper->run(['flex-row', 'flex-row-reverse', 'flex-col', 'flex-col-reverse']);

        $this->assertStringContainsString('.flex-col {', $css);
        $this->assertStringContainsString('flex-direction: column;', $css);

        $this->assertStringContainsString('.flex-col-reverse {', $css);
        $this->assertStringContainsString('flex-direction: column-reverse;', $css);

        $this->assertStringContainsString('.flex-row {', $css);
        $this->assertStringContainsString('flex-direction: row;', $css);

        $this->assertStringContainsString('.flex-row-reverse {', $css);
        $this->assertStringContainsString('flex-direction: row-reverse;', $css);
    }

    #[Test]
    public function flex_direction_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-flex-row']));
        $this->assertEquals('', $this->helper->run(['-flex-row-reverse']));
        $this->assertEquals('', $this->helper->run(['-flex-col']));
        $this->assertEquals('', $this->helper->run(['-flex-col-reverse']));
        $this->assertEquals('', $this->helper->run(['flex-row/foo']));
        $this->assertEquals('', $this->helper->run(['flex-col/foo']));
    }

    #[Test]
    public function flex_wrap(): void
    {
        $css = $this->helper->run(['flex-wrap', 'flex-wrap-reverse', 'flex-nowrap']);

        $this->assertStringContainsString('.flex-nowrap {', $css);
        $this->assertStringContainsString('flex-wrap: nowrap;', $css);

        $this->assertStringContainsString('.flex-wrap {', $css);
        $this->assertStringContainsString('flex-wrap: wrap;', $css);

        $this->assertStringContainsString('.flex-wrap-reverse {', $css);
        $this->assertStringContainsString('flex-wrap: wrap-reverse;', $css);
    }

    #[Test]
    public function flex_wrap_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-flex-wrap']));
        $this->assertEquals('', $this->helper->run(['-flex-wrap-reverse']));
        $this->assertEquals('', $this->helper->run(['-flex-nowrap']));
        $this->assertEquals('', $this->helper->run(['flex-wrap/foo']));
    }

    #[Test]
    public function flex_shorthand(): void
    {
        $css = $this->helper->run(['flex-1', 'flex-auto', 'flex-initial', 'flex-none']);

        $this->assertStringContainsString('.flex-1 {', $css);
        $this->assertStringContainsString('flex: 1;', $css);

        $this->assertStringContainsString('.flex-auto {', $css);
        $this->assertStringContainsString('flex: auto;', $css);

        $this->assertStringContainsString('.flex-initial {', $css);
        $this->assertStringContainsString('flex: 0 auto;', $css);

        $this->assertStringContainsString('.flex-none {', $css);
        $this->assertStringContainsString('flex: none;', $css);
    }

    #[Test]
    public function flex_with_arbitrary_values(): void
    {
        $css = $this->helper->run(['flex-[123]']);

        $this->assertStringContainsString('.flex-\\[123\\] {', $css);
        $this->assertStringContainsString('flex: 123;', $css);
    }

    #[Test]
    public function flex_grow(): void
    {
        $css = $this->helper->run(['grow', 'grow-0']);

        $this->assertStringContainsString('.grow {', $css);
        $this->assertStringContainsString('flex-grow: 1;', $css);

        $this->assertStringContainsString('.grow-0 {', $css);
        $this->assertStringContainsString('flex-grow: 0;', $css);
    }

    #[Test]
    public function flex_grow_with_arbitrary_values(): void
    {
        $css = $this->helper->run(['grow-[123]']);

        $this->assertStringContainsString('.grow-\\[123\\] {', $css);
        $this->assertStringContainsString('flex-grow: 123;', $css);
    }

    #[Test]
    public function flex_grow_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-grow']));
        $this->assertEquals('', $this->helper->run(['grow--1']));
        $this->assertEquals('', $this->helper->run(['grow-1.5']));
        $this->assertEquals('', $this->helper->run(['-grow-0']));
        $this->assertEquals('', $this->helper->run(['grow/foo']));
        $this->assertEquals('', $this->helper->run(['grow-0/foo']));
    }

    #[Test]
    public function flex_shrink(): void
    {
        $css = $this->helper->run(['shrink', 'shrink-0']);

        $this->assertStringContainsString('.shrink {', $css);
        $this->assertStringContainsString('flex-shrink: 1;', $css);

        $this->assertStringContainsString('.shrink-0 {', $css);
        $this->assertStringContainsString('flex-shrink: 0;', $css);
    }

    #[Test]
    public function flex_shrink_with_arbitrary_values(): void
    {
        $css = $this->helper->run(['shrink-[123]']);

        $this->assertStringContainsString('.shrink-\\[123\\] {', $css);
        $this->assertStringContainsString('flex-shrink: 123;', $css);
    }

    #[Test]
    public function flex_shrink_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-shrink']));
        $this->assertEquals('', $this->helper->run(['shrink--1']));
        $this->assertEquals('', $this->helper->run(['shrink-1.5']));
        $this->assertEquals('', $this->helper->run(['-shrink-0']));
        $this->assertEquals('', $this->helper->run(['shrink/foo']));
    }

    #[Test]
    public function flex_basis(): void
    {
        $css = $this->helper->run(['basis-auto', 'basis-full']);

        $this->assertStringContainsString('.basis-auto {', $css);
        $this->assertStringContainsString('flex-basis: auto;', $css);

        $this->assertStringContainsString('.basis-full {', $css);
        $this->assertStringContainsString('flex-basis: 100%;', $css);
    }

    #[Test]
    public function flex_basis_with_arbitrary_values(): void
    {
        $css = $this->helper->run(['basis-[123px]']);

        $this->assertStringContainsString('.basis-\\[123px\\] {', $css);
        $this->assertStringContainsString('flex-basis: 123px;', $css);
    }

    #[Test]
    public function grid_flow(): void
    {
        $css = $this->helper->run([
            'grid-flow-row',
            'grid-flow-col',
            'grid-flow-dense',
            'grid-flow-row-dense',
            'grid-flow-col-dense',
        ]);

        $this->assertStringContainsString('.grid-flow-col {', $css);
        $this->assertStringContainsString('grid-auto-flow: column;', $css);

        $this->assertStringContainsString('.grid-flow-col-dense {', $css);
        $this->assertStringContainsString('grid-auto-flow: column dense;', $css);

        $this->assertStringContainsString('.grid-flow-dense {', $css);
        $this->assertStringContainsString('grid-auto-flow: dense;', $css);

        $this->assertStringContainsString('.grid-flow-row {', $css);
        $this->assertStringContainsString('grid-auto-flow: row;', $css);

        $this->assertStringContainsString('.grid-flow-row-dense {', $css);
        $this->assertStringContainsString('grid-auto-flow: row dense;', $css);
    }

    #[Test]
    public function grid_flow_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['grid-flow']));
        $this->assertEquals('', $this->helper->run(['-grid-flow-row']));
        $this->assertEquals('', $this->helper->run(['grid-flow-row/foo']));
    }

    #[Test]
    public function grid_cols(): void
    {
        $css = $this->helper->run(['grid-cols-none', 'grid-cols-subgrid', 'grid-cols-12', 'grid-cols-99']);

        $this->assertStringContainsString('.grid-cols-12 {', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(12, minmax(0, 1fr));', $css);

        $this->assertStringContainsString('.grid-cols-99 {', $css);
        $this->assertStringContainsString('grid-template-columns: repeat(99, minmax(0, 1fr));', $css);

        $this->assertStringContainsString('.grid-cols-none {', $css);
        $this->assertStringContainsString('grid-template-columns: none;', $css);

        $this->assertStringContainsString('.grid-cols-subgrid {', $css);
        $this->assertStringContainsString('grid-template-columns: subgrid;', $css);
    }

    #[Test]
    public function grid_cols_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['grid-cols']));
        $this->assertEquals('', $this->helper->run(['grid-cols-0']));
        $this->assertEquals('', $this->helper->run(['-grid-cols-none']));
        $this->assertEquals('', $this->helper->run(['grid-cols-none/foo']));
    }

    #[Test]
    public function grid_rows(): void
    {
        $css = $this->helper->run(['grid-rows-none', 'grid-rows-subgrid', 'grid-rows-12', 'grid-rows-99']);

        $this->assertStringContainsString('.grid-rows-12 {', $css);
        $this->assertStringContainsString('grid-template-rows: repeat(12, minmax(0, 1fr));', $css);

        $this->assertStringContainsString('.grid-rows-99 {', $css);
        $this->assertStringContainsString('grid-template-rows: repeat(99, minmax(0, 1fr));', $css);

        $this->assertStringContainsString('.grid-rows-none {', $css);
        $this->assertStringContainsString('grid-template-rows: none;', $css);

        $this->assertStringContainsString('.grid-rows-subgrid {', $css);
        $this->assertStringContainsString('grid-template-rows: subgrid;', $css);
    }

    #[Test]
    public function justify_content(): void
    {
        $css = $this->helper->run([
            'justify-normal', 'justify-start', 'justify-end', 'justify-center',
            'justify-between', 'justify-around', 'justify-evenly', 'justify-stretch'
        ]);

        $this->assertStringContainsString('.justify-start {', $css);
        $this->assertStringContainsString('justify-content: flex-start;', $css);

        $this->assertStringContainsString('.justify-center {', $css);
        $this->assertStringContainsString('justify-content: center;', $css);

        $this->assertStringContainsString('.justify-between {', $css);
        $this->assertStringContainsString('justify-content: space-between;', $css);
    }

    #[Test]
    public function justify_items(): void
    {
        $css = $this->helper->run([
            'justify-items-start', 'justify-items-end', 'justify-items-center',
            'justify-items-stretch', 'justify-items-normal'
        ]);

        $this->assertStringContainsString('.justify-items-start {', $css);
        $this->assertStringContainsString('justify-items: start;', $css);

        $this->assertStringContainsString('.justify-items-center {', $css);
        $this->assertStringContainsString('justify-items: center;', $css);
    }

    #[Test]
    public function justify_self(): void
    {
        $css = $this->helper->run([
            'justify-self-auto', 'justify-self-start', 'justify-self-end',
            'justify-self-center', 'justify-self-stretch'
        ]);

        $this->assertStringContainsString('.justify-self-auto {', $css);
        $this->assertStringContainsString('justify-self: auto;', $css);

        $this->assertStringContainsString('.justify-self-center {', $css);
        $this->assertStringContainsString('justify-self: center;', $css);
    }

    #[Test]
    public function align_content(): void
    {
        $css = $this->helper->run([
            'content-normal', 'content-start', 'content-end', 'content-center',
            'content-between', 'content-around', 'content-evenly', 'content-baseline', 'content-stretch'
        ]);

        $this->assertStringContainsString('.content-start {', $css);
        $this->assertStringContainsString('align-content: flex-start;', $css);

        $this->assertStringContainsString('.content-center {', $css);
        $this->assertStringContainsString('align-content: center;', $css);
    }

    #[Test]
    public function align_items(): void
    {
        $css = $this->helper->run([
            'items-start', 'items-end', 'items-center', 'items-baseline', 'items-stretch'
        ]);

        $this->assertStringContainsString('.items-start {', $css);
        $this->assertStringContainsString('align-items: flex-start;', $css);

        $this->assertStringContainsString('.items-center {', $css);
        $this->assertStringContainsString('align-items: center;', $css);

        $this->assertStringContainsString('.items-baseline {', $css);
        $this->assertStringContainsString('align-items: baseline;', $css);
    }

    #[Test]
    public function align_self(): void
    {
        $css = $this->helper->run([
            'self-auto', 'self-start', 'self-end', 'self-center', 'self-stretch', 'self-baseline'
        ]);

        $this->assertStringContainsString('.self-auto {', $css);
        $this->assertStringContainsString('align-self: auto;', $css);

        $this->assertStringContainsString('.self-center {', $css);
        $this->assertStringContainsString('align-self: center;', $css);
    }

    #[Test]
    public function place_content(): void
    {
        $css = $this->helper->run([
            'place-content-center', 'place-content-start', 'place-content-end',
            'place-content-between', 'place-content-around', 'place-content-evenly',
            'place-content-baseline', 'place-content-stretch'
        ]);

        $this->assertStringContainsString('.place-content-center {', $css);
        $this->assertStringContainsString('place-content: center;', $css);

        $this->assertStringContainsString('.place-content-between {', $css);
        $this->assertStringContainsString('place-content: space-between;', $css);
    }

    #[Test]
    public function place_items(): void
    {
        $css = $this->helper->run([
            'place-items-start', 'place-items-end', 'place-items-center',
            'place-items-baseline', 'place-items-stretch'
        ]);

        $this->assertStringContainsString('.place-items-start {', $css);
        $this->assertStringContainsString('place-items: start;', $css);

        $this->assertStringContainsString('.place-items-center {', $css);
        $this->assertStringContainsString('place-items: center;', $css);
    }

    #[Test]
    public function place_self(): void
    {
        $css = $this->helper->run([
            'place-self-auto', 'place-self-start', 'place-self-end',
            'place-self-center', 'place-self-stretch'
        ]);

        $this->assertStringContainsString('.place-self-auto {', $css);
        $this->assertStringContainsString('place-self: auto;', $css);

        $this->assertStringContainsString('.place-self-center {', $css);
        $this->assertStringContainsString('place-self: center;', $css);
    }

    #[Test]
    public function auto_cols(): void
    {
        $css = $this->helper->run(['auto-cols-auto', 'auto-cols-min', 'auto-cols-max', 'auto-cols-fr']);

        $this->assertStringContainsString('.auto-cols-auto {', $css);
        $this->assertStringContainsString('grid-auto-columns: auto;', $css);

        $this->assertStringContainsString('.auto-cols-min {', $css);
        $this->assertStringContainsString('grid-auto-columns: min-content;', $css);

        $this->assertStringContainsString('.auto-cols-max {', $css);
        $this->assertStringContainsString('grid-auto-columns: max-content;', $css);

        $this->assertStringContainsString('.auto-cols-fr {', $css);
        $this->assertStringContainsString('grid-auto-columns: minmax(0, 1fr);', $css);
    }

    #[Test]
    public function auto_rows(): void
    {
        $css = $this->helper->run(['auto-rows-auto', 'auto-rows-min', 'auto-rows-max', 'auto-rows-fr']);

        $this->assertStringContainsString('.auto-rows-auto {', $css);
        $this->assertStringContainsString('grid-auto-rows: auto;', $css);

        $this->assertStringContainsString('.auto-rows-min {', $css);
        $this->assertStringContainsString('grid-auto-rows: min-content;', $css);

        $this->assertStringContainsString('.auto-rows-max {', $css);
        $this->assertStringContainsString('grid-auto-rows: max-content;', $css);

        $this->assertStringContainsString('.auto-rows-fr {', $css);
        $this->assertStringContainsString('grid-auto-rows: minmax(0, 1fr);', $css);
    }
}
