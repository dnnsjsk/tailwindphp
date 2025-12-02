<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for all Tailwind utility classes.
 * Organized by category matching src/utilities/*.php files.
 */
class UtilitiesTest extends TestCase
{
    private function assertGenerates(string $class, string $expectedProperty, ?string $expectedValue = null): void
    {
        $css = Tailwind::generate("<div class=\"{$class}\">");
        $this->assertStringContainsString($expectedProperty, $css, "Class '{$class}' should generate '{$expectedProperty}'");
        if ($expectedValue !== null) {
            $this->assertStringContainsString($expectedValue, $css, "Class '{$class}' should contain value '{$expectedValue}'");
        }
    }

    // =========================================================================
    // LAYOUT (layout.php)
    // =========================================================================

    // Display
    public function test_display_block(): void { $this->assertGenerates('block', 'display: block'); }
    public function test_display_inline_block(): void { $this->assertGenerates('inline-block', 'display: inline-block'); }
    public function test_display_inline(): void { $this->assertGenerates('inline', 'display: inline'); }
    public function test_display_flex(): void { $this->assertGenerates('flex', 'display: flex'); }
    public function test_display_inline_flex(): void { $this->assertGenerates('inline-flex', 'display: inline-flex'); }
    public function test_display_grid(): void { $this->assertGenerates('grid', 'display: grid'); }
    public function test_display_inline_grid(): void { $this->assertGenerates('inline-grid', 'display: inline-grid'); }
    public function test_display_table(): void { $this->assertGenerates('table', 'display: table'); }
    public function test_display_table_row(): void { $this->assertGenerates('table-row', 'display: table-row'); }
    public function test_display_table_cell(): void { $this->assertGenerates('table-cell', 'display: table-cell'); }
    public function test_display_contents(): void { $this->assertGenerates('contents', 'display: contents'); }
    public function test_display_hidden(): void { $this->assertGenerates('hidden', 'display: none'); }

    // Position
    public function test_position_static(): void { $this->assertGenerates('static', 'position: static'); }
    public function test_position_relative(): void { $this->assertGenerates('relative', 'position: relative'); }
    public function test_position_absolute(): void { $this->assertGenerates('absolute', 'position: absolute'); }
    public function test_position_fixed(): void { $this->assertGenerates('fixed', 'position: fixed'); }
    public function test_position_sticky(): void { $this->assertGenerates('sticky', 'position: sticky'); }

    // Inset
    public function test_inset_0(): void { $this->assertGenerates('inset-0', 'inset:'); }
    public function test_inset_auto(): void { $this->assertGenerates('inset-auto', 'inset: auto'); }
    public function test_inset_x_0(): void { $this->assertGenerates('inset-x-0', 'inset-inline:'); }
    public function test_inset_y_0(): void { $this->assertGenerates('inset-y-0', 'inset-block:'); }
    public function test_top_0(): void { $this->assertGenerates('top-0', 'top:'); }
    public function test_right_0(): void { $this->assertGenerates('right-0', 'right:'); }
    public function test_bottom_0(): void { $this->assertGenerates('bottom-0', 'bottom:'); }
    public function test_left_0(): void { $this->assertGenerates('left-0', 'left:'); }
    public function test_start_0(): void { $this->assertGenerates('start-0', 'inset-inline-start:'); }
    public function test_end_0(): void { $this->assertGenerates('end-0', 'inset-inline-end:'); }

    // Z-index
    public function test_z_0(): void { $this->assertGenerates('z-0', 'z-index: 0'); }
    public function test_z_10(): void { $this->assertGenerates('z-10', 'z-index: 10'); }
    public function test_z_50(): void { $this->assertGenerates('z-50', 'z-index: 50'); }
    public function test_z_auto(): void { $this->assertGenerates('z-auto', 'z-index: auto'); }
    public function test_z_negative(): void { $this->assertGenerates('-z-10', 'z-index:'); }

    // Float / Clear
    public function test_float_left(): void { $this->assertGenerates('float-left', 'float: left'); }
    public function test_float_right(): void { $this->assertGenerates('float-right', 'float: right'); }
    public function test_float_none(): void { $this->assertGenerates('float-none', 'float: none'); }
    public function test_clear_left(): void { $this->assertGenerates('clear-left', 'clear: left'); }
    public function test_clear_right(): void { $this->assertGenerates('clear-right', 'clear: right'); }
    public function test_clear_both(): void { $this->assertGenerates('clear-both', 'clear: both'); }

    // Overflow
    public function test_overflow_auto(): void { $this->assertGenerates('overflow-auto', 'overflow: auto'); }
    public function test_overflow_hidden(): void { $this->assertGenerates('overflow-hidden', 'overflow: hidden'); }
    public function test_overflow_visible(): void { $this->assertGenerates('overflow-visible', 'overflow: visible'); }
    public function test_overflow_scroll(): void { $this->assertGenerates('overflow-scroll', 'overflow: scroll'); }
    public function test_overflow_clip(): void { $this->assertGenerates('overflow-clip', 'overflow: clip'); }
    public function test_overflow_x_auto(): void { $this->assertGenerates('overflow-x-auto', 'overflow-x: auto'); }
    public function test_overflow_y_hidden(): void { $this->assertGenerates('overflow-y-hidden', 'overflow-y: hidden'); }

    // Visibility
    public function test_visible(): void { $this->assertGenerates('visible', 'visibility: visible'); }
    public function test_invisible(): void { $this->assertGenerates('invisible', 'visibility: hidden'); }
    public function test_collapse(): void { $this->assertGenerates('collapse', 'visibility: collapse'); }

    // Isolation
    public function test_isolate(): void { $this->assertGenerates('isolate', 'isolation: isolate'); }
    public function test_isolation_auto(): void { $this->assertGenerates('isolation-auto', 'isolation: auto'); }

    // Object Fit
    public function test_object_contain(): void { $this->assertGenerates('object-contain', 'object-fit: contain'); }
    public function test_object_cover(): void { $this->assertGenerates('object-cover', 'object-fit: cover'); }
    public function test_object_fill(): void { $this->assertGenerates('object-fill', 'object-fit: fill'); }
    public function test_object_none(): void { $this->assertGenerates('object-none', 'object-fit: none'); }
    public function test_object_scale_down(): void { $this->assertGenerates('object-scale-down', 'object-fit: scale-down'); }

    // Object Position
    public function test_object_center(): void { $this->assertGenerates('object-center', 'object-position: center'); }
    public function test_object_top(): void { $this->assertGenerates('object-top', 'object-position: top'); }
    public function test_object_bottom(): void { $this->assertGenerates('object-bottom', 'object-position: bottom'); }

    // Box Sizing
    public function test_box_border(): void { $this->assertGenerates('box-border', 'box-sizing: border-box'); }
    public function test_box_content(): void { $this->assertGenerates('box-content', 'box-sizing: content-box'); }

    // Aspect Ratio
    public function test_aspect_auto(): void { $this->assertGenerates('aspect-auto', 'aspect-ratio: auto'); }
    public function test_aspect_square(): void { $this->assertGenerates('aspect-square', 'aspect-ratio: 1 / 1'); }
    public function test_aspect_video(): void { $this->assertGenerates('aspect-video', 'aspect-ratio: 16 / 9'); }

    // Columns
    public function test_columns_1(): void { $this->assertGenerates('columns-1', 'columns: 1'); }
    public function test_columns_2(): void { $this->assertGenerates('columns-2', 'columns: 2'); }
    public function test_columns_auto(): void { $this->assertGenerates('columns-auto', 'columns: auto'); }

    // Break
    public function test_break_before_auto(): void { $this->assertGenerates('break-before-auto', 'break-before: auto'); }
    public function test_break_after_page(): void { $this->assertGenerates('break-after-page', 'break-after: page'); }
    public function test_break_inside_avoid(): void { $this->assertGenerates('break-inside-avoid', 'break-inside: avoid'); }

    // Box Decoration
    public function test_box_decoration_clone(): void { $this->assertGenerates('box-decoration-clone', 'box-decoration-break: clone'); }
    public function test_box_decoration_slice(): void { $this->assertGenerates('box-decoration-slice', 'box-decoration-break: slice'); }

    // =========================================================================
    // FLEXBOX & GRID (flexbox.php)
    // =========================================================================

    // Flex Direction
    public function test_flex_row(): void { $this->assertGenerates('flex-row', 'flex-direction: row'); }
    public function test_flex_row_reverse(): void { $this->assertGenerates('flex-row-reverse', 'flex-direction: row-reverse'); }
    public function test_flex_col(): void { $this->assertGenerates('flex-col', 'flex-direction: column'); }
    public function test_flex_col_reverse(): void { $this->assertGenerates('flex-col-reverse', 'flex-direction: column-reverse'); }

    // Flex Wrap
    public function test_flex_wrap(): void { $this->assertGenerates('flex-wrap', 'flex-wrap: wrap'); }
    public function test_flex_wrap_reverse(): void { $this->assertGenerates('flex-wrap-reverse', 'flex-wrap: wrap-reverse'); }
    public function test_flex_nowrap(): void { $this->assertGenerates('flex-nowrap', 'flex-wrap: nowrap'); }

    // Flex
    public function test_flex_1(): void { $this->assertGenerates('flex-1', 'flex: 1'); }
    public function test_flex_auto(): void { $this->assertGenerates('flex-auto', 'flex: auto'); }
    public function test_flex_initial(): void { $this->assertGenerates('flex-initial', 'flex:'); }
    public function test_flex_none(): void { $this->assertGenerates('flex-none', 'flex: none'); }

    // Flex Grow
    public function test_grow(): void { $this->assertGenerates('grow', 'flex-grow:'); }
    public function test_grow_0(): void { $this->assertGenerates('grow-0', 'flex-grow: 0'); }

    // Flex Shrink
    public function test_shrink(): void { $this->assertGenerates('shrink', 'flex-shrink:'); }
    public function test_shrink_0(): void { $this->assertGenerates('shrink-0', 'flex-shrink: 0'); }

    // Order
    public function test_order_1(): void { $this->assertGenerates('order-1', 'order: 1'); }
    public function test_order_first(): void { $this->assertGenerates('order-first', 'order:'); }
    public function test_order_last(): void { $this->assertGenerates('order-last', 'order:'); }
    public function test_order_none(): void { $this->assertGenerates('order-none', 'order: 0'); }

    // Grid Template Columns
    public function test_grid_cols_1(): void { $this->assertGenerates('grid-cols-1', 'grid-template-columns:'); }
    public function test_grid_cols_12(): void { $this->assertGenerates('grid-cols-12', 'grid-template-columns:'); }
    public function test_grid_cols_none(): void { $this->assertGenerates('grid-cols-none', 'grid-template-columns: none'); }
    public function test_grid_cols_subgrid(): void { $this->assertGenerates('grid-cols-subgrid', 'grid-template-columns: subgrid'); }

    // Grid Template Rows
    public function test_grid_rows_1(): void { $this->assertGenerates('grid-rows-1', 'grid-template-rows:'); }
    public function test_grid_rows_6(): void { $this->assertGenerates('grid-rows-6', 'grid-template-rows:'); }
    public function test_grid_rows_none(): void { $this->assertGenerates('grid-rows-none', 'grid-template-rows: none'); }

    // Grid Column Span
    public function test_col_span_1(): void { $this->assertGenerates('col-span-1', 'grid-column:'); }
    public function test_col_span_full(): void { $this->assertGenerates('col-span-full', 'grid-column:'); }
    public function test_col_auto(): void { $this->assertGenerates('col-auto', 'grid-column: auto'); }
    public function test_col_start_1(): void { $this->assertGenerates('col-start-1', 'grid-column-start:'); }
    public function test_col_end_1(): void { $this->assertGenerates('col-end-1', 'grid-column-end:'); }

    // Grid Row Span
    public function test_row_span_1(): void { $this->assertGenerates('row-span-1', 'grid-row:'); }
    public function test_row_span_full(): void { $this->assertGenerates('row-span-full', 'grid-row:'); }
    public function test_row_auto(): void { $this->assertGenerates('row-auto', 'grid-row: auto'); }
    public function test_row_start_1(): void { $this->assertGenerates('row-start-1', 'grid-row-start:'); }
    public function test_row_end_1(): void { $this->assertGenerates('row-end-1', 'grid-row-end:'); }

    // Grid Auto Flow
    public function test_grid_flow_row(): void { $this->assertGenerates('grid-flow-row', 'grid-auto-flow: row'); }
    public function test_grid_flow_col(): void { $this->assertGenerates('grid-flow-col', 'grid-auto-flow: column'); }
    public function test_grid_flow_dense(): void { $this->assertGenerates('grid-flow-dense', 'grid-auto-flow: dense'); }
    public function test_grid_flow_row_dense(): void { $this->assertGenerates('grid-flow-row-dense', 'grid-auto-flow: row dense'); }

    // Grid Auto Columns/Rows
    public function test_auto_cols_auto(): void { $this->assertGenerates('auto-cols-auto', 'grid-auto-columns: auto'); }
    public function test_auto_cols_min(): void { $this->assertGenerates('auto-cols-min', 'grid-auto-columns: min-content'); }
    public function test_auto_cols_max(): void { $this->assertGenerates('auto-cols-max', 'grid-auto-columns: max-content'); }
    public function test_auto_cols_fr(): void { $this->assertGenerates('auto-cols-fr', 'grid-auto-columns: minmax(0, 1fr)'); }
    public function test_auto_rows_auto(): void { $this->assertGenerates('auto-rows-auto', 'grid-auto-rows: auto'); }

    // Gap
    public function test_gap_0(): void { $this->assertGenerates('gap-0', 'gap:'); }
    public function test_gap_4(): void { $this->assertGenerates('gap-4', 'gap:'); }
    public function test_gap_x_4(): void { $this->assertGenerates('gap-x-4', 'column-gap:'); }
    public function test_gap_y_4(): void { $this->assertGenerates('gap-y-4', 'row-gap:'); }

    // Justify Content
    public function test_justify_start(): void { $this->assertGenerates('justify-start', 'justify-content: flex-start'); }
    public function test_justify_end(): void { $this->assertGenerates('justify-end', 'justify-content: flex-end'); }
    public function test_justify_center(): void { $this->assertGenerates('justify-center', 'justify-content: center'); }
    public function test_justify_between(): void { $this->assertGenerates('justify-between', 'justify-content: space-between'); }
    public function test_justify_around(): void { $this->assertGenerates('justify-around', 'justify-content: space-around'); }
    public function test_justify_evenly(): void { $this->assertGenerates('justify-evenly', 'justify-content: space-evenly'); }
    public function test_justify_stretch(): void { $this->assertGenerates('justify-stretch', 'justify-content: stretch'); }

    // Justify Items
    public function test_justify_items_start(): void { $this->assertGenerates('justify-items-start', 'justify-items: start'); }
    public function test_justify_items_center(): void { $this->assertGenerates('justify-items-center', 'justify-items: center'); }
    public function test_justify_items_stretch(): void { $this->assertGenerates('justify-items-stretch', 'justify-items: stretch'); }

    // Justify Self
    public function test_justify_self_auto(): void { $this->assertGenerates('justify-self-auto', 'justify-self: auto'); }
    public function test_justify_self_start(): void { $this->assertGenerates('justify-self-start', 'justify-self:'); }
    public function test_justify_self_center(): void { $this->assertGenerates('justify-self-center', 'justify-self: center'); }

    // Align Content
    public function test_content_start(): void { $this->assertGenerates('content-start', 'align-content: flex-start'); }
    public function test_content_center(): void { $this->assertGenerates('content-center', 'align-content: center'); }
    public function test_content_end(): void { $this->assertGenerates('content-end', 'align-content: flex-end'); }
    public function test_content_between(): void { $this->assertGenerates('content-between', 'align-content: space-between'); }

    // Align Items
    public function test_items_start(): void { $this->assertGenerates('items-start', 'align-items: flex-start'); }
    public function test_items_center(): void { $this->assertGenerates('items-center', 'align-items: center'); }
    public function test_items_end(): void { $this->assertGenerates('items-end', 'align-items: flex-end'); }
    public function test_items_baseline(): void { $this->assertGenerates('items-baseline', 'align-items: baseline'); }
    public function test_items_stretch(): void { $this->assertGenerates('items-stretch', 'align-items: stretch'); }

    // Align Self
    public function test_self_auto(): void { $this->assertGenerates('self-auto', 'align-self: auto'); }
    public function test_self_start(): void { $this->assertGenerates('self-start', 'align-self: flex-start'); }
    public function test_self_center(): void { $this->assertGenerates('self-center', 'align-self: center'); }
    public function test_self_stretch(): void { $this->assertGenerates('self-stretch', 'align-self: stretch'); }

    // Place Content
    public function test_place_content_center(): void { $this->assertGenerates('place-content-center', 'place-content: center'); }
    public function test_place_content_start(): void { $this->assertGenerates('place-content-start', 'place-content: start'); }
    public function test_place_content_between(): void { $this->assertGenerates('place-content-between', 'place-content: space-between'); }

    // Place Items
    public function test_place_items_center(): void { $this->assertGenerates('place-items-center', 'place-items: center'); }
    public function test_place_items_start(): void { $this->assertGenerates('place-items-start', 'place-items: start'); }

    // Place Self
    public function test_place_self_auto(): void { $this->assertGenerates('place-self-auto', 'place-self: auto'); }
    public function test_place_self_center(): void { $this->assertGenerates('place-self-center', 'place-self: center'); }

    // =========================================================================
    // SPACING (spacing.php)
    // =========================================================================

    // Padding
    public function test_p_0(): void { $this->assertGenerates('p-0', 'padding:'); }
    public function test_p_4(): void { $this->assertGenerates('p-4', 'padding:'); }
    public function test_p_px(): void { $this->assertGenerates('p-px', 'padding:'); }
    public function test_px_4(): void { $this->assertGenerates('px-4', 'padding-inline:'); }
    public function test_py_4(): void { $this->assertGenerates('py-4', 'padding-block:'); }
    public function test_pt_4(): void { $this->assertGenerates('pt-4', 'padding-top:'); }
    public function test_pr_4(): void { $this->assertGenerates('pr-4', 'padding-right:'); }
    public function test_pb_4(): void { $this->assertGenerates('pb-4', 'padding-bottom:'); }
    public function test_pl_4(): void { $this->assertGenerates('pl-4', 'padding-left:'); }
    public function test_ps_4(): void { $this->assertGenerates('ps-4', 'padding-inline-start:'); }
    public function test_pe_4(): void { $this->assertGenerates('pe-4', 'padding-inline-end:'); }

    // Margin
    public function test_m_0(): void { $this->assertGenerates('m-0', 'margin:'); }
    public function test_m_4(): void { $this->assertGenerates('m-4', 'margin:'); }
    public function test_m_auto(): void { $this->assertGenerates('m-auto', 'margin: auto'); }
    public function test_mx_auto(): void { $this->assertGenerates('mx-auto', 'margin-inline: auto'); }
    public function test_my_4(): void { $this->assertGenerates('my-4', 'margin-block:'); }
    public function test_mt_4(): void { $this->assertGenerates('mt-4', 'margin-top:'); }
    public function test_mr_4(): void { $this->assertGenerates('mr-4', 'margin-right:'); }
    public function test_mb_4(): void { $this->assertGenerates('mb-4', 'margin-bottom:'); }
    public function test_ml_4(): void { $this->assertGenerates('ml-4', 'margin-left:'); }
    public function test_ms_4(): void { $this->assertGenerates('ms-4', 'margin-inline-start:'); }
    public function test_me_4(): void { $this->assertGenerates('me-4', 'margin-inline-end:'); }
    public function test_negative_mt_4(): void { $this->assertGenerates('-mt-4', 'margin-top:'); }

    // Space Between
    public function test_space_x_4(): void { $this->assertGenerates('space-x-4', '--tw-space-x-reverse'); }
    public function test_space_y_4(): void { $this->assertGenerates('space-y-4', '--tw-space-y-reverse'); }
    public function test_space_x_reverse(): void { $this->assertGenerates('space-x-reverse', '--tw-space-x-reverse'); }
    public function test_space_y_reverse(): void { $this->assertGenerates('space-y-reverse', '--tw-space-y-reverse'); }

    // =========================================================================
    // SIZING (sizing.php)
    // =========================================================================

    // Width
    public function test_w_0(): void { $this->assertGenerates('w-0', 'width:'); }
    public function test_w_4(): void { $this->assertGenerates('w-4', 'width:'); }
    public function test_w_full(): void { $this->assertGenerates('w-full', 'width: 100%'); }
    public function test_w_screen(): void { $this->assertGenerates('w-screen', 'width: 100vw'); }
    public function test_w_auto(): void { $this->assertGenerates('w-auto', 'width: auto'); }
    public function test_w_min(): void { $this->assertGenerates('w-min', 'width: min-content'); }
    public function test_w_max(): void { $this->assertGenerates('w-max', 'width: max-content'); }
    public function test_w_fit(): void { $this->assertGenerates('w-fit', 'width: fit-content'); }
    public function test_w_1_2(): void { $this->assertGenerates('w-1/2', 'width:'); }

    // Min Width
    public function test_min_w_0(): void { $this->assertGenerates('min-w-0', 'min-width:'); }
    public function test_min_w_full(): void { $this->assertGenerates('min-w-full', 'min-width: 100%'); }
    public function test_min_w_min(): void { $this->assertGenerates('min-w-min', 'min-width: min-content'); }

    // Max Width
    public function test_max_w_0(): void { $this->assertGenerates('max-w-0', 'max-width:'); }
    public function test_max_w_full(): void { $this->assertGenerates('max-w-full', 'max-width: 100%'); }
    public function test_max_w_none(): void { $this->assertGenerates('max-w-none', 'max-width: none'); }
    public function test_max_w_xs(): void { $this->assertGenerates('max-w-xs', 'max-width:'); }
    public function test_max_w_sm(): void { $this->assertGenerates('max-w-sm', 'max-width:'); }
    public function test_max_w_md(): void { $this->assertGenerates('max-w-md', 'max-width:'); }
    public function test_max_w_lg(): void { $this->assertGenerates('max-w-lg', 'max-width:'); }
    public function test_max_w_xl(): void { $this->assertGenerates('max-w-xl', 'max-width:'); }
    public function test_max_w_prose(): void { $this->assertGenerates('max-w-prose', 'max-width:'); }

    // Height
    public function test_h_0(): void { $this->assertGenerates('h-0', 'height:'); }
    public function test_h_4(): void { $this->assertGenerates('h-4', 'height:'); }
    public function test_h_full(): void { $this->assertGenerates('h-full', 'height: 100%'); }
    public function test_h_screen(): void { $this->assertGenerates('h-screen', 'height: 100vh'); }
    public function test_h_auto(): void { $this->assertGenerates('h-auto', 'height: auto'); }
    public function test_h_min(): void { $this->assertGenerates('h-min', 'height: min-content'); }
    public function test_h_max(): void { $this->assertGenerates('h-max', 'height: max-content'); }
    public function test_h_fit(): void { $this->assertGenerates('h-fit', 'height: fit-content'); }
    public function test_h_svh(): void { $this->assertGenerates('h-svh', 'height: 100svh'); }
    public function test_h_dvh(): void { $this->assertGenerates('h-dvh', 'height: 100dvh'); }
    public function test_h_lvh(): void { $this->assertGenerates('h-lvh', 'height: 100lvh'); }

    // Min Height
    public function test_min_h_0(): void { $this->assertGenerates('min-h-0', 'min-height:'); }
    public function test_min_h_full(): void { $this->assertGenerates('min-h-full', 'min-height: 100%'); }
    public function test_min_h_screen(): void { $this->assertGenerates('min-h-screen', 'min-height: 100vh'); }

    // Max Height
    public function test_max_h_0(): void { $this->assertGenerates('max-h-0', 'max-height:'); }
    public function test_max_h_full(): void { $this->assertGenerates('max-h-full', 'max-height: 100%'); }
    public function test_max_h_screen(): void { $this->assertGenerates('max-h-screen', 'max-height: 100vh'); }
    public function test_max_h_none(): void { $this->assertGenerates('max-h-none', 'max-height: none'); }

    // Size (width + height)
    public function test_size_0(): void { $this->assertGenerates('size-0', 'width:'); }
    public function test_size_4(): void { $this->assertGenerates('size-4', 'width:'); }
    public function test_size_full(): void { $this->assertGenerates('size-full', 'width: 100%'); }

    // =========================================================================
    // TYPOGRAPHY (typography.php)
    // =========================================================================

    // Font Family
    public function test_font_sans(): void { $this->assertGenerates('font-sans', 'font-family:'); }
    public function test_font_serif(): void { $this->assertGenerates('font-serif', 'font-family:'); }
    public function test_font_mono(): void { $this->assertGenerates('font-mono', 'font-family:'); }

    // Font Size
    public function test_text_xs(): void { $this->assertGenerates('text-xs', 'font-size:'); }
    public function test_text_sm(): void { $this->assertGenerates('text-sm', 'font-size:'); }
    public function test_text_base(): void { $this->assertGenerates('text-base', 'font-size:'); }
    public function test_text_lg(): void { $this->assertGenerates('text-lg', 'font-size:'); }
    public function test_text_xl(): void { $this->assertGenerates('text-xl', 'font-size:'); }
    public function test_text_2xl(): void { $this->assertGenerates('text-2xl', 'font-size:'); }

    // Font Weight (Tailwind 4 uses CSS variables)
    public function test_font_thin(): void { $this->assertGenerates('font-thin', 'font-weight: var(--font-weight-thin)'); }
    public function test_font_light(): void { $this->assertGenerates('font-light', 'font-weight: var(--font-weight-light)'); }
    public function test_font_normal(): void { $this->assertGenerates('font-normal', 'font-weight: var(--font-weight-normal)'); }
    public function test_font_medium(): void { $this->assertGenerates('font-medium', 'font-weight: var(--font-weight-medium)'); }
    public function test_font_semibold(): void { $this->assertGenerates('font-semibold', 'font-weight: var(--font-weight-semibold)'); }
    public function test_font_bold(): void { $this->assertGenerates('font-bold', 'font-weight: var(--font-weight-bold)'); }
    public function test_font_extrabold(): void { $this->assertGenerates('font-extrabold', 'font-weight: var(--font-weight-extrabold)'); }
    public function test_font_black(): void { $this->assertGenerates('font-black', 'font-weight: var(--font-weight-black)'); }

    // Font Style
    public function test_italic(): void { $this->assertGenerates('italic', 'font-style: italic'); }
    public function test_not_italic(): void { $this->assertGenerates('not-italic', 'font-style: normal'); }

    // Line Height (Tailwind 4 uses CSS variables for named values)
    public function test_leading_none(): void { $this->assertGenerates('leading-none', 'line-height: 1'); }
    public function test_leading_tight(): void { $this->assertGenerates('leading-tight', 'line-height: var(--leading-tight)'); }
    public function test_leading_normal(): void { $this->assertGenerates('leading-normal', 'line-height: var(--leading-normal)'); }
    public function test_leading_loose(): void { $this->assertGenerates('leading-loose', 'line-height: var(--leading-loose)'); }
    public function test_leading_3(): void { $this->assertGenerates('leading-3', 'line-height: calc(var(--spacing) * 3)'); }

    // Letter Spacing
    public function test_tracking_tighter(): void { $this->assertGenerates('tracking-tighter', 'letter-spacing: var(--tracking-tighter)'); }
    public function test_tracking_tight(): void { $this->assertGenerates('tracking-tight', 'letter-spacing: var(--tracking-tight)'); }
    public function test_tracking_normal(): void { $this->assertGenerates('tracking-normal', 'letter-spacing: var(--tracking-normal)'); }
    public function test_tracking_wide(): void { $this->assertGenerates('tracking-wide', 'letter-spacing: var(--tracking-wide)'); }
    public function test_tracking_wider(): void { $this->assertGenerates('tracking-wider', 'letter-spacing: var(--tracking-wider)'); }
    public function test_tracking_widest(): void { $this->assertGenerates('tracking-widest', 'letter-spacing: var(--tracking-widest)'); }

    // Text Color
    public function test_text_inherit(): void { $this->assertGenerates('text-inherit', 'color: inherit'); }
    public function test_text_current(): void { $this->assertGenerates('text-current', 'color: currentcolor'); }
    public function test_text_transparent(): void { $this->assertGenerates('text-transparent', 'color: transparent'); }
    public function test_text_black(): void { $this->assertGenerates('text-black', 'color:'); }
    public function test_text_white(): void { $this->assertGenerates('text-white', 'color:'); }
    public function test_text_red_500(): void { $this->assertGenerates('text-red-500', 'color:'); }
    public function test_text_blue_500(): void { $this->assertGenerates('text-blue-500', 'color:'); }

    // Text Align
    public function test_text_left(): void { $this->assertGenerates('text-left', 'text-align: left'); }
    public function test_text_center(): void { $this->assertGenerates('text-center', 'text-align: center'); }
    public function test_text_right(): void { $this->assertGenerates('text-right', 'text-align: right'); }
    public function test_text_justify(): void { $this->assertGenerates('text-justify', 'text-align: justify'); }
    public function test_text_start(): void { $this->assertGenerates('text-start', 'text-align: start'); }
    public function test_text_end(): void { $this->assertGenerates('text-end', 'text-align: end'); }

    // Vertical Align
    public function test_align_baseline(): void { $this->assertGenerates('align-baseline', 'vertical-align: baseline'); }
    public function test_align_top(): void { $this->assertGenerates('align-top', 'vertical-align: top'); }
    public function test_align_middle(): void { $this->assertGenerates('align-middle', 'vertical-align: middle'); }
    public function test_align_bottom(): void { $this->assertGenerates('align-bottom', 'vertical-align: bottom'); }

    // Text Decoration
    public function test_underline(): void { $this->assertGenerates('underline', 'text-decoration-line: underline'); }
    public function test_overline(): void { $this->assertGenerates('overline', 'text-decoration-line: overline'); }
    public function test_line_through(): void { $this->assertGenerates('line-through', 'text-decoration-line: line-through'); }
    public function test_no_underline(): void { $this->assertGenerates('no-underline', 'text-decoration-line: none'); }

    // Text Decoration Style
    public function test_decoration_solid(): void { $this->assertGenerates('decoration-solid', 'text-decoration-style: solid'); }
    public function test_decoration_double(): void { $this->assertGenerates('decoration-double', 'text-decoration-style: double'); }
    public function test_decoration_dotted(): void { $this->assertGenerates('decoration-dotted', 'text-decoration-style: dotted'); }
    public function test_decoration_dashed(): void { $this->assertGenerates('decoration-dashed', 'text-decoration-style: dashed'); }
    public function test_decoration_wavy(): void { $this->assertGenerates('decoration-wavy', 'text-decoration-style: wavy'); }

    // Text Decoration Thickness
    public function test_decoration_auto(): void { $this->assertGenerates('decoration-auto', 'text-decoration-thickness: auto'); }
    public function test_decoration_from_font(): void { $this->assertGenerates('decoration-from-font', 'text-decoration-thickness: from-font'); }
    public function test_decoration_0(): void { $this->assertGenerates('decoration-0', 'text-decoration-thickness: 0'); }
    public function test_decoration_1(): void { $this->assertGenerates('decoration-1', 'text-decoration-thickness: 1px'); }
    public function test_decoration_2(): void { $this->assertGenerates('decoration-2', 'text-decoration-thickness: 2px'); }

    // Text Underline Offset
    public function test_underline_offset_auto(): void { $this->assertGenerates('underline-offset-auto', 'text-underline-offset: auto'); }
    public function test_underline_offset_0(): void { $this->assertGenerates('underline-offset-0', 'text-underline-offset: 0'); }
    public function test_underline_offset_1(): void { $this->assertGenerates('underline-offset-1', 'text-underline-offset: 1px'); }
    public function test_underline_offset_2(): void { $this->assertGenerates('underline-offset-2', 'text-underline-offset: 2px'); }

    // Text Transform
    public function test_uppercase(): void { $this->assertGenerates('uppercase', 'text-transform: uppercase'); }
    public function test_lowercase(): void { $this->assertGenerates('lowercase', 'text-transform: lowercase'); }
    public function test_capitalize(): void { $this->assertGenerates('capitalize', 'text-transform: capitalize'); }
    public function test_normal_case(): void { $this->assertGenerates('normal-case', 'text-transform: none'); }

    // Text Overflow
    public function test_truncate(): void { $this->assertGenerates('truncate', 'text-overflow: ellipsis'); }
    public function test_text_ellipsis(): void { $this->assertGenerates('text-ellipsis', 'text-overflow: ellipsis'); }
    public function test_text_clip(): void { $this->assertGenerates('text-clip', 'text-overflow: clip'); }

    // Whitespace
    public function test_whitespace_normal(): void { $this->assertGenerates('whitespace-normal', 'white-space: normal'); }
    public function test_whitespace_nowrap(): void { $this->assertGenerates('whitespace-nowrap', 'white-space: nowrap'); }
    public function test_whitespace_pre(): void { $this->assertGenerates('whitespace-pre', 'white-space: pre'); }
    public function test_whitespace_pre_line(): void { $this->assertGenerates('whitespace-pre-line', 'white-space: pre-line'); }
    public function test_whitespace_pre_wrap(): void { $this->assertGenerates('whitespace-pre-wrap', 'white-space: pre-wrap'); }
    public function test_whitespace_break_spaces(): void { $this->assertGenerates('whitespace-break-spaces', 'white-space: break-spaces'); }

    // Word Break
    public function test_break_normal(): void { $this->assertGenerates('break-normal', 'word-break: normal'); }
    public function test_break_words(): void { $this->assertGenerates('break-words', 'overflow-wrap: break-word'); }
    public function test_break_all(): void { $this->assertGenerates('break-all', 'word-break: break-all'); }
    public function test_break_keep(): void { $this->assertGenerates('break-keep', 'word-break: keep-all'); }

    // Hyphens
    public function test_hyphens_none(): void { $this->assertGenerates('hyphens-none', 'hyphens: none'); }
    public function test_hyphens_manual(): void { $this->assertGenerates('hyphens-manual', 'hyphens: manual'); }
    public function test_hyphens_auto(): void { $this->assertGenerates('hyphens-auto', 'hyphens: auto'); }

    // List Style Type
    public function test_list_none(): void { $this->assertGenerates('list-none', 'list-style-type: none'); }
    public function test_list_disc(): void { $this->assertGenerates('list-disc', 'list-style-type: disc'); }
    public function test_list_decimal(): void { $this->assertGenerates('list-decimal', 'list-style-type: decimal'); }

    // List Style Position
    public function test_list_inside(): void { $this->assertGenerates('list-inside', 'list-style-position: inside'); }
    public function test_list_outside(): void { $this->assertGenerates('list-outside', 'list-style-position: outside'); }

    // =========================================================================
    // BACKGROUNDS (backgrounds.php)
    // =========================================================================

    // Background Attachment
    public function test_bg_fixed(): void { $this->assertGenerates('bg-fixed', 'background-attachment: fixed'); }
    public function test_bg_local(): void { $this->assertGenerates('bg-local', 'background-attachment: local'); }
    public function test_bg_scroll(): void { $this->assertGenerates('bg-scroll', 'background-attachment: scroll'); }

    // Background Clip
    public function test_bg_clip_border(): void { $this->assertGenerates('bg-clip-border', 'background-clip: border-box'); }
    public function test_bg_clip_padding(): void { $this->assertGenerates('bg-clip-padding', 'background-clip: padding-box'); }
    public function test_bg_clip_content(): void { $this->assertGenerates('bg-clip-content', 'background-clip: content-box'); }
    public function test_bg_clip_text(): void { $this->assertGenerates('bg-clip-text', 'background-clip: text'); }

    // Background Color
    public function test_bg_inherit(): void { $this->assertGenerates('bg-inherit', 'background-color: inherit'); }
    public function test_bg_current(): void { $this->assertGenerates('bg-current', 'background-color: currentcolor'); }
    public function test_bg_transparent(): void { $this->assertGenerates('bg-transparent', 'background-color: transparent'); }
    public function test_bg_black(): void { $this->assertGenerates('bg-black', 'background-color:'); }
    public function test_bg_white(): void { $this->assertGenerates('bg-white', 'background-color:'); }
    public function test_bg_red_500(): void { $this->assertGenerates('bg-red-500', 'background-color:'); }
    public function test_bg_blue_500(): void { $this->assertGenerates('bg-blue-500', 'background-color:'); }

    // Background Origin
    public function test_bg_origin_border(): void { $this->assertGenerates('bg-origin-border', 'background-origin: border-box'); }
    public function test_bg_origin_padding(): void { $this->assertGenerates('bg-origin-padding', 'background-origin: padding-box'); }
    public function test_bg_origin_content(): void { $this->assertGenerates('bg-origin-content', 'background-origin: content-box'); }

    // Background Position
    public function test_bg_center(): void { $this->assertGenerates('bg-center', 'background-position: center'); }
    public function test_bg_top(): void { $this->assertGenerates('bg-top', 'background-position: top'); }
    public function test_bg_bottom(): void { $this->assertGenerates('bg-bottom', 'background-position: bottom'); }
    public function test_bg_left(): void { $this->assertGenerates('bg-left', 'background-position:'); }
    public function test_bg_right(): void { $this->assertGenerates('bg-right', 'background-position:'); }
    public function test_bg_left_top(): void { $this->assertGenerates('bg-left-top', 'background-position:'); }

    // Background Repeat
    public function test_bg_repeat(): void { $this->assertGenerates('bg-repeat', 'background-repeat: repeat'); }
    public function test_bg_no_repeat(): void { $this->assertGenerates('bg-no-repeat', 'background-repeat: no-repeat'); }
    public function test_bg_repeat_x(): void { $this->assertGenerates('bg-repeat-x', 'background-repeat: repeat-x'); }
    public function test_bg_repeat_y(): void { $this->assertGenerates('bg-repeat-y', 'background-repeat: repeat-y'); }
    public function test_bg_repeat_round(): void { $this->assertGenerates('bg-repeat-round', 'background-repeat: round'); }
    public function test_bg_repeat_space(): void { $this->assertGenerates('bg-repeat-space', 'background-repeat: space'); }

    // Background Size
    public function test_bg_auto(): void { $this->assertGenerates('bg-auto', 'background-size: auto'); }
    public function test_bg_cover(): void { $this->assertGenerates('bg-cover', 'background-size: cover'); }
    public function test_bg_contain(): void { $this->assertGenerates('bg-contain', 'background-size: contain'); }

    // Gradients
    public function test_bg_linear_to_t(): void { $this->assertGenerates('bg-linear-to-t', 'background-image:'); }
    public function test_bg_linear_to_r(): void { $this->assertGenerates('bg-linear-to-r', 'background-image:'); }
    public function test_bg_linear_to_b(): void { $this->assertGenerates('bg-linear-to-b', 'background-image:'); }
    public function test_bg_linear_to_l(): void { $this->assertGenerates('bg-linear-to-l', 'background-image:'); }
    public function test_bg_linear_to_tr(): void { $this->assertGenerates('bg-linear-to-tr', 'background-image:'); }
    public function test_bg_linear_to_br(): void { $this->assertGenerates('bg-linear-to-br', 'background-image:'); }
    public function test_bg_radial(): void { $this->assertGenerates('bg-radial', 'background-image:'); }
    public function test_bg_conic(): void { $this->assertGenerates('bg-conic', 'background-image:'); }

    // Gradient Stops
    public function test_from_red_500(): void { $this->assertGenerates('from-red-500', '--tw-gradient-from:'); }
    public function test_via_blue_500(): void { $this->assertGenerates('via-blue-500', '--tw-gradient-via:'); }
    public function test_to_green_500(): void { $this->assertGenerates('to-green-500', '--tw-gradient-to:'); }
    public function test_from_0(): void { $this->assertGenerates('from-0%', '--tw-gradient-from-position: 0%'); }
    public function test_from_50(): void { $this->assertGenerates('from-50%', '--tw-gradient-from-position: 50%'); }
    public function test_via_50(): void { $this->assertGenerates('via-50%', '--tw-gradient-via-position: 50%'); }
    public function test_to_100(): void { $this->assertGenerates('to-100%', '--tw-gradient-to-position: 100%'); }

    // =========================================================================
    // BORDERS (borders.php)
    // =========================================================================

    // Border Radius
    public function test_rounded(): void { $this->assertGenerates('rounded', 'border-radius:'); }
    public function test_rounded_none(): void { $this->assertGenerates('rounded-none', 'border-radius: 0'); }
    public function test_rounded_sm(): void { $this->assertGenerates('rounded-sm', 'border-radius:'); }
    public function test_rounded_md(): void { $this->assertGenerates('rounded-md', 'border-radius:'); }
    public function test_rounded_lg(): void { $this->assertGenerates('rounded-lg', 'border-radius:'); }
    public function test_rounded_xl(): void { $this->assertGenerates('rounded-xl', 'border-radius:'); }
    public function test_rounded_full(): void { $this->assertGenerates('rounded-full', 'border-radius:'); }
    public function test_rounded_t(): void { $this->assertGenerates('rounded-t', 'border-top-left-radius:'); }
    public function test_rounded_r(): void { $this->assertGenerates('rounded-r', 'border-top-right-radius:'); }
    public function test_rounded_b(): void { $this->assertGenerates('rounded-b', 'border-bottom-'); }
    public function test_rounded_l(): void { $this->assertGenerates('rounded-l', 'border-top-left-radius:'); }
    public function test_rounded_tl(): void { $this->assertGenerates('rounded-tl', 'border-top-left-radius:'); }
    public function test_rounded_tr(): void { $this->assertGenerates('rounded-tr', 'border-top-right-radius:'); }
    public function test_rounded_br(): void { $this->assertGenerates('rounded-br', 'border-bottom-right-radius:'); }
    public function test_rounded_bl(): void { $this->assertGenerates('rounded-bl', 'border-bottom-left-radius:'); }

    // Border Width
    public function test_border(): void { $this->assertGenerates('border', 'border-width:'); }
    public function test_border_0(): void { $this->assertGenerates('border-0', 'border-width: 0'); }
    public function test_border_2(): void { $this->assertGenerates('border-2', 'border-width: 2px'); }
    public function test_border_4(): void { $this->assertGenerates('border-4', 'border-width: 4px'); }
    public function test_border_8(): void { $this->assertGenerates('border-8', 'border-width: 8px'); }
    public function test_border_x(): void { $this->assertGenerates('border-x', 'border-inline-width:'); }
    public function test_border_y(): void { $this->assertGenerates('border-y', 'border-block-width:'); }
    public function test_border_t(): void { $this->assertGenerates('border-t', 'border-top-width:'); }
    public function test_border_r(): void { $this->assertGenerates('border-r', 'border-right-width:'); }
    public function test_border_b(): void { $this->assertGenerates('border-b', 'border-bottom-width:'); }
    public function test_border_l(): void { $this->assertGenerates('border-l', 'border-left-width:'); }
    public function test_border_s(): void { $this->assertGenerates('border-s', 'border-inline-start-width:'); }
    public function test_border_e(): void { $this->assertGenerates('border-e', 'border-inline-end-width:'); }

    // Border Color (currentcolor is lowercase per CSS spec)
    public function test_border_inherit(): void { $this->assertGenerates('border-inherit', 'border-color: inherit'); }
    public function test_border_current(): void { $this->assertGenerates('border-current', 'border-color: currentcolor'); }
    public function test_border_transparent(): void { $this->assertGenerates('border-transparent', 'border-color: transparent'); }
    public function test_border_black(): void { $this->assertGenerates('border-black', 'border-color: var(--color-black)'); }
    public function test_border_red_500(): void { $this->assertGenerates('border-red-500', 'border-color: var(--color-red-500)'); }

    // Border Style
    public function test_border_solid(): void { $this->assertGenerates('border-solid', 'border-style: solid'); }
    public function test_border_dashed(): void { $this->assertGenerates('border-dashed', 'border-style: dashed'); }
    public function test_border_dotted(): void { $this->assertGenerates('border-dotted', 'border-style: dotted'); }
    public function test_border_double(): void { $this->assertGenerates('border-double', 'border-style: double'); }
    public function test_border_hidden(): void { $this->assertGenerates('border-hidden', 'border-style: hidden'); }
    public function test_border_none(): void { $this->assertGenerates('border-none', 'border-style: none'); }

    // Divide
    public function test_divide_x(): void { $this->assertGenerates('divide-x', 'border-inline-start-width:'); }
    public function test_divide_y(): void { $this->assertGenerates('divide-y', 'border-top-width:'); }
    public function test_divide_x_reverse(): void { $this->assertGenerates('divide-x-reverse', '--tw-divide-x-reverse'); }
    public function test_divide_y_reverse(): void { $this->assertGenerates('divide-y-reverse', '--tw-divide-y-reverse'); }
    public function test_divide_solid(): void { $this->assertGenerates('divide-solid', 'border-style: solid'); }
    public function test_divide_dashed(): void { $this->assertGenerates('divide-dashed', 'border-style: dashed'); }
    public function test_divide_red_500(): void { $this->assertGenerates('divide-red-500', 'border-color:'); }

    // Outline
    public function test_outline(): void { $this->assertGenerates('outline', 'outline-width:'); }
    public function test_outline_none(): void { $this->assertGenerates('outline-none', 'outline-style: none'); }
    public function test_outline_0(): void { $this->assertGenerates('outline-0', 'outline-width:'); }
    public function test_outline_1(): void { $this->assertGenerates('outline-1', 'outline-width:'); }
    public function test_outline_2(): void { $this->assertGenerates('outline-2', 'outline-width:'); }
    public function test_outline_4(): void { $this->assertGenerates('outline-4', 'outline-width:'); }
    public function test_outline_red_500(): void { $this->assertGenerates('outline-red-500', 'outline-color:'); }
    public function test_outline_solid(): void { $this->assertGenerates('outline-solid', 'outline-style: solid'); }
    public function test_outline_dashed(): void { $this->assertGenerates('outline-dashed', 'outline-style: dashed'); }
    public function test_outline_dotted(): void { $this->assertGenerates('outline-dotted', 'outline-style: dotted'); }
    public function test_outline_double(): void { $this->assertGenerates('outline-double', 'outline-style: double'); }
    public function test_outline_offset_0(): void { $this->assertGenerates('outline-offset-0', 'outline-offset:'); }
    public function test_outline_offset_2(): void { $this->assertGenerates('outline-offset-2', 'outline-offset:'); }

    // Ring
    public function test_ring(): void { $this->assertGenerates('ring', 'box-shadow:'); }
    public function test_ring_0(): void { $this->assertGenerates('ring-0', 'box-shadow:'); }
    public function test_ring_1(): void { $this->assertGenerates('ring-1', 'box-shadow:'); }
    public function test_ring_2(): void { $this->assertGenerates('ring-2', 'box-shadow:'); }
    public function test_ring_inset(): void { $this->assertGenerates('ring-inset', '--tw-ring-inset'); }
    public function test_ring_red_500(): void { $this->assertGenerates('ring-red-500', '--tw-ring-color:'); }
    public function test_ring_offset_2(): void { $this->assertGenerates('ring-offset-2', '--tw-ring-offset-width:'); }
    public function test_ring_offset_red_500(): void { $this->assertGenerates('ring-offset-red-500', '--tw-ring-offset-color:'); }

    // =========================================================================
    // EFFECTS (effects.php)
    // =========================================================================

    // Box Shadow
    public function test_shadow(): void { $this->assertGenerates('shadow', 'box-shadow:'); }
    public function test_shadow_sm(): void { $this->assertGenerates('shadow-sm', 'box-shadow:'); }
    public function test_shadow_md(): void { $this->assertGenerates('shadow-md', 'box-shadow:'); }
    public function test_shadow_lg(): void { $this->assertGenerates('shadow-lg', 'box-shadow:'); }
    public function test_shadow_xl(): void { $this->assertGenerates('shadow-xl', 'box-shadow:'); }
    public function test_shadow_2xl(): void { $this->assertGenerates('shadow-2xl', 'box-shadow:'); }
    public function test_shadow_inner(): void { $this->assertGenerates('shadow-inner', 'box-shadow:'); }
    public function test_shadow_none(): void { $this->assertGenerates('shadow-none', 'box-shadow:'); }
    public function test_shadow_red_500(): void { $this->assertGenerates('shadow-red-500', '--tw-shadow-color:'); }

    // Inset Shadow
    public function test_inset_shadow(): void { $this->assertGenerates('inset-shadow', 'box-shadow:'); }
    public function test_inset_shadow_sm(): void { $this->assertGenerates('inset-shadow-sm', 'box-shadow:'); }
    public function test_inset_shadow_none(): void { $this->assertGenerates('inset-shadow-none', 'box-shadow:'); }

    // Opacity
    public function test_opacity_0(): void { $this->assertGenerates('opacity-0', 'opacity: 0'); }
    public function test_opacity_50(): void { $this->assertGenerates('opacity-50', 'opacity:'); }
    public function test_opacity_100(): void { $this->assertGenerates('opacity-100', 'opacity: 1'); }

    // Mix Blend Mode
    public function test_mix_blend_normal(): void { $this->assertGenerates('mix-blend-normal', 'mix-blend-mode: normal'); }
    public function test_mix_blend_multiply(): void { $this->assertGenerates('mix-blend-multiply', 'mix-blend-mode: multiply'); }
    public function test_mix_blend_screen(): void { $this->assertGenerates('mix-blend-screen', 'mix-blend-mode: screen'); }
    public function test_mix_blend_overlay(): void { $this->assertGenerates('mix-blend-overlay', 'mix-blend-mode: overlay'); }

    // Background Blend Mode
    public function test_bg_blend_normal(): void { $this->assertGenerates('bg-blend-normal', 'background-blend-mode: normal'); }
    public function test_bg_blend_multiply(): void { $this->assertGenerates('bg-blend-multiply', 'background-blend-mode: multiply'); }

    // =========================================================================
    // FILTERS (filters.php)
    // =========================================================================

    // Blur
    public function test_blur(): void { $this->assertGenerates('blur', 'filter:'); }
    public function test_blur_none(): void { $this->assertGenerates('blur-none', 'filter:'); }
    public function test_blur_sm(): void { $this->assertGenerates('blur-sm', 'filter:'); }
    public function test_blur_md(): void { $this->assertGenerates('blur-md', 'filter:'); }
    public function test_blur_lg(): void { $this->assertGenerates('blur-lg', 'filter:'); }
    public function test_blur_xl(): void { $this->assertGenerates('blur-xl', 'filter:'); }

    // Brightness
    public function test_brightness_0(): void { $this->assertGenerates('brightness-0', 'filter:'); }
    public function test_brightness_50(): void { $this->assertGenerates('brightness-50', 'filter:'); }
    public function test_brightness_100(): void { $this->assertGenerates('brightness-100', 'filter:'); }
    public function test_brightness_150(): void { $this->assertGenerates('brightness-150', 'filter:'); }

    // Contrast
    public function test_contrast_0(): void { $this->assertGenerates('contrast-0', 'filter:'); }
    public function test_contrast_50(): void { $this->assertGenerates('contrast-50', 'filter:'); }
    public function test_contrast_100(): void { $this->assertGenerates('contrast-100', 'filter:'); }

    // Drop Shadow
    public function test_drop_shadow(): void { $this->assertGenerates('drop-shadow', 'filter:'); }
    public function test_drop_shadow_sm(): void { $this->assertGenerates('drop-shadow-sm', 'filter:'); }
    public function test_drop_shadow_md(): void { $this->assertGenerates('drop-shadow-md', 'filter:'); }
    public function test_drop_shadow_lg(): void { $this->assertGenerates('drop-shadow-lg', 'filter:'); }
    public function test_drop_shadow_xl(): void { $this->assertGenerates('drop-shadow-xl', 'filter:'); }
    public function test_drop_shadow_none(): void { $this->assertGenerates('drop-shadow-none', 'filter:'); }

    // Grayscale
    public function test_grayscale(): void { $this->assertGenerates('grayscale', 'filter:'); }
    public function test_grayscale_0(): void { $this->assertGenerates('grayscale-0', 'filter:'); }

    // Hue Rotate
    public function test_hue_rotate_0(): void { $this->assertGenerates('hue-rotate-0', 'filter:'); }
    public function test_hue_rotate_15(): void { $this->assertGenerates('hue-rotate-15', 'filter:'); }
    public function test_hue_rotate_90(): void { $this->assertGenerates('hue-rotate-90', 'filter:'); }
    public function test_hue_rotate_180(): void { $this->assertGenerates('hue-rotate-180', 'filter:'); }

    // Invert
    public function test_invert(): void { $this->assertGenerates('invert', 'filter:'); }
    public function test_invert_0(): void { $this->assertGenerates('invert-0', 'filter:'); }

    // Saturate
    public function test_saturate_0(): void { $this->assertGenerates('saturate-0', 'filter:'); }
    public function test_saturate_50(): void { $this->assertGenerates('saturate-50', 'filter:'); }
    public function test_saturate_100(): void { $this->assertGenerates('saturate-100', 'filter:'); }

    // Sepia
    public function test_sepia(): void { $this->assertGenerates('sepia', 'filter:'); }
    public function test_sepia_0(): void { $this->assertGenerates('sepia-0', 'filter:'); }

    // Backdrop Filter
    public function test_backdrop_blur(): void { $this->assertGenerates('backdrop-blur', 'backdrop-filter:'); }
    public function test_backdrop_blur_sm(): void { $this->assertGenerates('backdrop-blur-sm', 'backdrop-filter:'); }
    public function test_backdrop_brightness_50(): void { $this->assertGenerates('backdrop-brightness-50', 'backdrop-filter:'); }
    public function test_backdrop_contrast_50(): void { $this->assertGenerates('backdrop-contrast-50', 'backdrop-filter:'); }
    public function test_backdrop_grayscale(): void { $this->assertGenerates('backdrop-grayscale', 'backdrop-filter:'); }
    public function test_backdrop_hue_rotate_90(): void { $this->assertGenerates('backdrop-hue-rotate-90', 'backdrop-filter:'); }
    public function test_backdrop_invert(): void { $this->assertGenerates('backdrop-invert', 'backdrop-filter:'); }
    public function test_backdrop_opacity_50(): void { $this->assertGenerates('backdrop-opacity-50', 'backdrop-filter:'); }
    public function test_backdrop_saturate_50(): void { $this->assertGenerates('backdrop-saturate-50', 'backdrop-filter:'); }
    public function test_backdrop_sepia(): void { $this->assertGenerates('backdrop-sepia', 'backdrop-filter:'); }

    // =========================================================================
    // TRANSFORMS (transforms.php)
    // =========================================================================

    // Scale
    public function test_scale_0(): void { $this->assertGenerates('scale-0', 'scale:'); }
    public function test_scale_50(): void { $this->assertGenerates('scale-50', 'scale:'); }
    public function test_scale_100(): void { $this->assertGenerates('scale-100', 'scale:'); }
    public function test_scale_150(): void { $this->assertGenerates('scale-150', 'scale:'); }
    public function test_scale_x_50(): void { $this->assertGenerates('scale-x-50', 'scale:'); }
    public function test_scale_y_50(): void { $this->assertGenerates('scale-y-50', 'scale:'); }

    // Rotate
    public function test_rotate_0(): void { $this->assertGenerates('rotate-0', 'rotate:'); }
    public function test_rotate_45(): void { $this->assertGenerates('rotate-45', 'rotate:'); }
    public function test_rotate_90(): void { $this->assertGenerates('rotate-90', 'rotate:'); }
    public function test_rotate_180(): void { $this->assertGenerates('rotate-180', 'rotate:'); }
    public function test_negative_rotate_45(): void { $this->assertGenerates('-rotate-45', 'rotate:'); }

    // Translate
    public function test_translate_x_0(): void { $this->assertGenerates('translate-x-0', 'translate:'); }
    public function test_translate_x_4(): void { $this->assertGenerates('translate-x-4', 'translate:'); }
    public function test_translate_y_4(): void { $this->assertGenerates('translate-y-4', 'translate:'); }
    public function test_translate_x_full(): void { $this->assertGenerates('translate-x-full', 'translate:'); }
    public function test_translate_x_1_2(): void { $this->assertGenerates('translate-x-1/2', 'translate:'); }
    public function test_negative_translate_x_4(): void { $this->assertGenerates('-translate-x-4', 'translate:'); }
    public function test_negative_translate_x_1_2(): void { $this->assertGenerates('-translate-x-1/2', 'translate:'); }

    // Skew
    public function test_skew_x_0(): void { $this->assertGenerates('skew-x-0', 'transform:'); }
    public function test_skew_x_3(): void { $this->assertGenerates('skew-x-3', 'transform:'); }
    public function test_skew_y_6(): void { $this->assertGenerates('skew-y-6', 'transform:'); }
    public function test_negative_skew_x_3(): void { $this->assertGenerates('-skew-x-3', 'transform:'); }

    // Transform Origin (some values use percentage coordinates)
    public function test_origin_center(): void { $this->assertGenerates('origin-center', 'transform-origin: center'); }
    public function test_origin_top(): void { $this->assertGenerates('origin-top', 'transform-origin: top'); }
    public function test_origin_top_right(): void { $this->assertGenerates('origin-top-right', 'transform-origin: 100% 0'); }
    public function test_origin_bottom_left(): void { $this->assertGenerates('origin-bottom-left', 'transform-origin: 0 100%'); }

    // =========================================================================
    // TRANSITIONS & ANIMATION (transitions.php)
    // =========================================================================

    // Transition Property
    public function test_transition(): void { $this->assertGenerates('transition', 'transition-property:'); }
    public function test_transition_none(): void { $this->assertGenerates('transition-none', 'transition-property: none'); }
    public function test_transition_all(): void { $this->assertGenerates('transition-all', 'transition-property: all'); }
    public function test_transition_colors(): void { $this->assertGenerates('transition-colors', 'transition-property:'); }
    public function test_transition_opacity(): void { $this->assertGenerates('transition-opacity', 'transition-property: opacity'); }
    public function test_transition_shadow(): void { $this->assertGenerates('transition-shadow', 'transition-property:'); }
    public function test_transition_transform(): void { $this->assertGenerates('transition-transform', 'transition-property:'); }

    // Duration (values optimized to seconds by LightningCSS)
    public function test_duration_75(): void { $this->assertGenerates('duration-75', 'transition-duration: .075s'); }
    public function test_duration_100(): void { $this->assertGenerates('duration-100', 'transition-duration: .1s'); }
    public function test_duration_150(): void { $this->assertGenerates('duration-150', 'transition-duration: .15s'); }
    public function test_duration_200(): void { $this->assertGenerates('duration-200', 'transition-duration: .2s'); }
    public function test_duration_300(): void { $this->assertGenerates('duration-300', 'transition-duration: .3s'); }
    public function test_duration_500(): void { $this->assertGenerates('duration-500', 'transition-duration: .5s'); }
    public function test_duration_1000(): void { $this->assertGenerates('duration-1000', 'transition-duration: 1s'); }

    // Timing Function
    public function test_ease_linear(): void { $this->assertGenerates('ease-linear', 'transition-timing-function: linear'); }
    public function test_ease_in(): void { $this->assertGenerates('ease-in', 'transition-timing-function:'); }
    public function test_ease_out(): void { $this->assertGenerates('ease-out', 'transition-timing-function:'); }
    public function test_ease_in_out(): void { $this->assertGenerates('ease-in-out', 'transition-timing-function:'); }

    // Delay (values optimized to seconds by LightningCSS)
    public function test_delay_75(): void { $this->assertGenerates('delay-75', 'transition-delay: .075s'); }
    public function test_delay_100(): void { $this->assertGenerates('delay-100', 'transition-delay: .1s'); }
    public function test_delay_150(): void { $this->assertGenerates('delay-150', 'transition-delay: .15s'); }
    public function test_delay_200(): void { $this->assertGenerates('delay-200', 'transition-delay: .2s'); }

    // Animation
    public function test_animate_none(): void { $this->assertGenerates('animate-none', 'animation: none'); }
    public function test_animate_spin(): void { $this->assertGenerates('animate-spin', 'animation:'); }
    public function test_animate_ping(): void { $this->assertGenerates('animate-ping', 'animation:'); }
    public function test_animate_pulse(): void { $this->assertGenerates('animate-pulse', 'animation:'); }
    public function test_animate_bounce(): void { $this->assertGenerates('animate-bounce', 'animation:'); }

    // =========================================================================
    // INTERACTIVITY (interactivity.php)
    // =========================================================================

    // Accent Color (currentcolor is lowercase per CSS spec)
    public function test_accent_auto(): void { $this->assertGenerates('accent-auto', 'accent-color: auto'); }
    public function test_accent_inherit(): void { $this->assertGenerates('accent-inherit', 'accent-color: inherit'); }
    public function test_accent_current(): void { $this->assertGenerates('accent-current', 'accent-color: currentcolor'); }
    public function test_accent_red_500(): void { $this->assertGenerates('accent-red-500', 'accent-color:'); }

    // Appearance
    public function test_appearance_none(): void { $this->assertGenerates('appearance-none', 'appearance: none'); }
    public function test_appearance_auto(): void { $this->assertGenerates('appearance-auto', 'appearance: auto'); }

    // Cursor
    public function test_cursor_auto(): void { $this->assertGenerates('cursor-auto', 'cursor: auto'); }
    public function test_cursor_default(): void { $this->assertGenerates('cursor-default', 'cursor: default'); }
    public function test_cursor_pointer(): void { $this->assertGenerates('cursor-pointer', 'cursor: pointer'); }
    public function test_cursor_wait(): void { $this->assertGenerates('cursor-wait', 'cursor: wait'); }
    public function test_cursor_text(): void { $this->assertGenerates('cursor-text', 'cursor: text'); }
    public function test_cursor_move(): void { $this->assertGenerates('cursor-move', 'cursor: move'); }
    public function test_cursor_not_allowed(): void { $this->assertGenerates('cursor-not-allowed', 'cursor: not-allowed'); }
    public function test_cursor_grab(): void { $this->assertGenerates('cursor-grab', 'cursor: grab'); }
    public function test_cursor_grabbing(): void { $this->assertGenerates('cursor-grabbing', 'cursor: grabbing'); }

    // Caret Color (currentcolor is lowercase per CSS spec)
    public function test_caret_inherit(): void { $this->assertGenerates('caret-inherit', 'caret-color: inherit'); }
    public function test_caret_current(): void { $this->assertGenerates('caret-current', 'caret-color: currentcolor'); }
    public function test_caret_transparent(): void { $this->assertGenerates('caret-transparent', 'caret-color: transparent'); }
    public function test_caret_red_500(): void { $this->assertGenerates('caret-red-500', 'caret-color:'); }

    // Pointer Events
    public function test_pointer_events_none(): void { $this->assertGenerates('pointer-events-none', 'pointer-events: none'); }
    public function test_pointer_events_auto(): void { $this->assertGenerates('pointer-events-auto', 'pointer-events: auto'); }

    // Resize
    public function test_resize_none(): void { $this->assertGenerates('resize-none', 'resize: none'); }
    public function test_resize(): void { $this->assertGenerates('resize', 'resize: both'); }
    public function test_resize_x(): void { $this->assertGenerates('resize-x', 'resize: horizontal'); }
    public function test_resize_y(): void { $this->assertGenerates('resize-y', 'resize: vertical'); }

    // Scroll Behavior
    public function test_scroll_auto(): void { $this->assertGenerates('scroll-auto', 'scroll-behavior: auto'); }
    public function test_scroll_smooth(): void { $this->assertGenerates('scroll-smooth', 'scroll-behavior: smooth'); }

    // Scroll Margin (uses logical properties)
    public function test_scroll_m_0(): void { $this->assertGenerates('scroll-m-0', 'scroll-margin:'); }
    public function test_scroll_m_4(): void { $this->assertGenerates('scroll-m-4', 'scroll-margin:'); }
    public function test_scroll_mx_4(): void { $this->assertGenerates('scroll-mx-4', 'scroll-margin-inline:'); }
    public function test_scroll_my_4(): void { $this->assertGenerates('scroll-my-4', 'scroll-margin-block:'); }
    public function test_scroll_mt_4(): void { $this->assertGenerates('scroll-mt-4', 'scroll-margin-top:'); }

    // Scroll Padding (uses logical properties)
    public function test_scroll_p_0(): void { $this->assertGenerates('scroll-p-0', 'scroll-padding:'); }
    public function test_scroll_p_4(): void { $this->assertGenerates('scroll-p-4', 'scroll-padding:'); }
    public function test_scroll_px_4(): void { $this->assertGenerates('scroll-px-4', 'scroll-padding-inline:'); }
    public function test_scroll_py_4(): void { $this->assertGenerates('scroll-py-4', 'scroll-padding-block:'); }
    public function test_scroll_pt_4(): void { $this->assertGenerates('scroll-pt-4', 'scroll-padding-top:'); }

    // Scroll Snap Align
    public function test_snap_start(): void { $this->assertGenerates('snap-start', 'scroll-snap-align: start'); }
    public function test_snap_end(): void { $this->assertGenerates('snap-end', 'scroll-snap-align: end'); }
    public function test_snap_center(): void { $this->assertGenerates('snap-center', 'scroll-snap-align: center'); }
    public function test_snap_align_none(): void { $this->assertGenerates('snap-align-none', 'scroll-snap-align: none'); }

    // Scroll Snap Stop
    public function test_snap_normal(): void { $this->assertGenerates('snap-normal', 'scroll-snap-stop: normal'); }
    public function test_snap_always(): void { $this->assertGenerates('snap-always', 'scroll-snap-stop: always'); }

    // Scroll Snap Type
    public function test_snap_none(): void { $this->assertGenerates('snap-none', 'scroll-snap-type: none'); }
    public function test_snap_x(): void { $this->assertGenerates('snap-x', 'scroll-snap-type:'); }
    public function test_snap_y(): void { $this->assertGenerates('snap-y', 'scroll-snap-type:'); }
    public function test_snap_both(): void { $this->assertGenerates('snap-both', 'scroll-snap-type:'); }
    public function test_snap_mandatory(): void { $this->assertGenerates('snap-mandatory', '--tw-scroll-snap-strictness: mandatory'); }
    public function test_snap_proximity(): void { $this->assertGenerates('snap-proximity', '--tw-scroll-snap-strictness: proximity'); }

    // Touch Action
    public function test_touch_auto(): void { $this->assertGenerates('touch-auto', 'touch-action: auto'); }
    public function test_touch_none(): void { $this->assertGenerates('touch-none', 'touch-action: none'); }
    public function test_touch_pan_x(): void { $this->assertGenerates('touch-pan-x', 'touch-action:'); }
    public function test_touch_pan_y(): void { $this->assertGenerates('touch-pan-y', 'touch-action:'); }
    public function test_touch_pinch_zoom(): void { $this->assertGenerates('touch-pinch-zoom', 'touch-action:'); }
    public function test_touch_manipulation(): void { $this->assertGenerates('touch-manipulation', 'touch-action: manipulation'); }

    // User Select
    public function test_select_none(): void { $this->assertGenerates('select-none', 'user-select: none'); }
    public function test_select_text(): void { $this->assertGenerates('select-text', 'user-select: text'); }
    public function test_select_all(): void { $this->assertGenerates('select-all', 'user-select: all'); }
    public function test_select_auto(): void { $this->assertGenerates('select-auto', 'user-select: auto'); }

    // Will Change
    public function test_will_change_auto(): void { $this->assertGenerates('will-change-auto', 'will-change: auto'); }
    public function test_will_change_scroll(): void { $this->assertGenerates('will-change-scroll', 'will-change: scroll-position'); }
    public function test_will_change_contents(): void { $this->assertGenerates('will-change-contents', 'will-change: contents'); }
    public function test_will_change_transform(): void { $this->assertGenerates('will-change-transform', 'will-change: transform'); }

    // =========================================================================
    // SVG (svg.php)
    // =========================================================================

    // Fill (currentcolor is lowercase per CSS spec)
    public function test_fill_none(): void { $this->assertGenerates('fill-none', 'fill: none'); }
    public function test_fill_inherit(): void { $this->assertGenerates('fill-inherit', 'fill: inherit'); }
    public function test_fill_current(): void { $this->assertGenerates('fill-current', 'fill: currentcolor'); }
    public function test_fill_transparent(): void { $this->assertGenerates('fill-transparent', 'fill: transparent'); }
    public function test_fill_red_500(): void { $this->assertGenerates('fill-red-500', 'fill:'); }

    // Stroke (currentcolor is lowercase per CSS spec)
    public function test_stroke_none(): void { $this->assertGenerates('stroke-none', 'stroke: none'); }
    public function test_stroke_inherit(): void { $this->assertGenerates('stroke-inherit', 'stroke: inherit'); }
    public function test_stroke_current(): void { $this->assertGenerates('stroke-current', 'stroke: currentcolor'); }
    public function test_stroke_transparent(): void { $this->assertGenerates('stroke-transparent', 'stroke: transparent'); }
    public function test_stroke_red_500(): void { $this->assertGenerates('stroke-red-500', 'stroke:'); }

    // Stroke Width
    public function test_stroke_0(): void { $this->assertGenerates('stroke-0', 'stroke-width: 0'); }
    public function test_stroke_1(): void { $this->assertGenerates('stroke-1', 'stroke-width: 1'); }
    public function test_stroke_2(): void { $this->assertGenerates('stroke-2', 'stroke-width: 2'); }

    // =========================================================================
    // ACCESSIBILITY (accessibility.php)
    // =========================================================================

    public function test_sr_only(): void { $this->assertGenerates('sr-only', 'position: absolute'); }
    public function test_not_sr_only(): void { $this->assertGenerates('not-sr-only', 'position: static'); }
    public function test_forced_color_adjust_auto(): void { $this->assertGenerates('forced-color-adjust-auto', 'forced-color-adjust: auto'); }
    public function test_forced_color_adjust_none(): void { $this->assertGenerates('forced-color-adjust-none', 'forced-color-adjust: none'); }

    // =========================================================================
    // TABLES (tables.php)
    // =========================================================================

    // Border Collapse
    public function test_border_collapse(): void { $this->assertGenerates('border-collapse', 'border-collapse: collapse'); }
    public function test_border_separate(): void { $this->assertGenerates('border-separate', 'border-collapse: separate'); }

    // Border Spacing
    public function test_border_spacing_0(): void { $this->assertGenerates('border-spacing-0', 'border-spacing:'); }
    public function test_border_spacing_4(): void { $this->assertGenerates('border-spacing-4', 'border-spacing:'); }
    public function test_border_spacing_x_4(): void { $this->assertGenerates('border-spacing-x-4', '--tw-border-spacing-x:'); }
    public function test_border_spacing_y_4(): void { $this->assertGenerates('border-spacing-y-4', '--tw-border-spacing-y:'); }

    // Table Layout
    public function test_table_auto(): void { $this->assertGenerates('table-auto', 'table-layout: auto'); }
    public function test_table_fixed(): void { $this->assertGenerates('table-fixed', 'table-layout: fixed'); }

    // Caption Side
    public function test_caption_top(): void { $this->assertGenerates('caption-top', 'caption-side: top'); }
    public function test_caption_bottom(): void { $this->assertGenerates('caption-bottom', 'caption-side: bottom'); }

    // =========================================================================
    // ARBITRARY VALUES
    // =========================================================================

    public function test_arbitrary_width(): void { $this->assertGenerates('w-[300px]', 'width: 300px'); }
    public function test_arbitrary_height(): void { $this->assertGenerates('h-[200px]', 'height: 200px'); }
    public function test_arbitrary_padding(): void { $this->assertGenerates('p-[20px]', 'padding: 20px'); }
    public function test_arbitrary_margin(): void { $this->assertGenerates('m-[10px]', 'margin: 10px'); }
    public function test_arbitrary_color(): void { $this->assertGenerates('text-[#ff0000]', 'color:'); }
    public function test_arbitrary_bg_color(): void { $this->assertGenerates('bg-[#00ff00]', 'background-color:'); }
    public function test_arbitrary_font_size(): void { $this->assertGenerates('text-[18px]', 'font-size: 18px'); }
    public function test_arbitrary_grid_cols(): void { $this->assertGenerates('grid-cols-[1fr_2fr]', 'grid-template-columns: 1fr 2fr'); }
    public function test_arbitrary_z_index(): void { $this->assertGenerates('z-[999]', 'z-index: 999'); }
    public function test_arbitrary_top(): void { $this->assertGenerates('top-[50%]', 'top: 50%'); }
    public function test_arbitrary_calc(): void { $this->assertGenerates('w-[calc(100%-20px)]', 'width: calc(100% - 20px)'); }

    // Arbitrary property
    public function test_arbitrary_property(): void { $this->assertGenerates('[clip-path:circle(50%)]', 'clip-path: circle(50%)'); }
    public function test_arbitrary_property_with_spaces(): void { $this->assertGenerates('[margin:10px_20px]', 'margin: 10px 20px'); }
}
