<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Comprehensive tests for all Tailwind variants.
 */
class VariantsTest extends TestCase
{
    private function assertVariantGenerates(string $class, string $expectedSelector, ?string $expectedProperty = null): void
    {
        $css = Tailwind::generate("<div class=\"{$class}\">");
        $this->assertStringContainsString($expectedSelector, $css, "Class '{$class}' should generate selector containing '{$expectedSelector}'");
        if ($expectedProperty !== null) {
            $this->assertStringContainsString($expectedProperty, $css, "Class '{$class}' should contain '{$expectedProperty}'");
        }
    }

    // =========================================================================
    // PSEUDO-CLASS VARIANTS
    // =========================================================================

    // Interactive states
    public function test_hover(): void { $this->assertVariantGenerates('hover:bg-blue-500', ':hover'); }
    public function test_focus(): void { $this->assertVariantGenerates('focus:bg-blue-500', ':focus'); }
    public function test_focus_within(): void { $this->assertVariantGenerates('focus-within:bg-blue-500', ':focus-within'); }
    public function test_focus_visible(): void { $this->assertVariantGenerates('focus-visible:bg-blue-500', ':focus-visible'); }
    public function test_active(): void { $this->assertVariantGenerates('active:bg-blue-500', ':active'); }
    public function test_visited(): void { $this->assertVariantGenerates('visited:text-purple-500', ':visited'); }
    public function test_target(): void { $this->assertVariantGenerates('target:bg-blue-500', ':target'); }

    // Form states
    public function test_disabled(): void { $this->assertVariantGenerates('disabled:opacity-50', ':disabled'); }
    public function test_enabled(): void { $this->assertVariantGenerates('enabled:bg-blue-500', ':enabled'); }
    public function test_checked(): void { $this->assertVariantGenerates('checked:bg-blue-500', ':checked'); }
    public function test_indeterminate(): void { $this->assertVariantGenerates('indeterminate:bg-blue-500', ':indeterminate'); }
    public function test_default(): void { $this->assertVariantGenerates('default:ring-2', ':default'); }
    public function test_required(): void { $this->assertVariantGenerates('required:border-red-500', ':required'); }
    public function test_valid(): void { $this->assertVariantGenerates('valid:border-green-500', ':valid'); }
    public function test_invalid(): void { $this->assertVariantGenerates('invalid:border-red-500', ':invalid'); }
    public function test_in_range(): void { $this->assertVariantGenerates('in-range:border-green-500', ':in-range'); }
    public function test_out_of_range(): void { $this->assertVariantGenerates('out-of-range:border-red-500', ':out-of-range'); }
    public function test_placeholder_shown(): void { $this->assertVariantGenerates('placeholder-shown:border-gray-500', ':placeholder-shown'); }
    public function test_autofill(): void { $this->assertVariantGenerates('autofill:bg-yellow-200', ':autofill'); }
    public function test_read_only(): void { $this->assertVariantGenerates('read-only:bg-gray-100', ':read-only'); }

    // Structural
    public function test_first(): void { $this->assertVariantGenerates('first:mt-0', ':first-child'); }
    public function test_last(): void { $this->assertVariantGenerates('last:mb-0', ':last-child'); }
    public function test_only(): void { $this->assertVariantGenerates('only:mx-auto', ':only-child'); }
    public function test_odd(): void { $this->assertVariantGenerates('odd:bg-gray-100', ':nth-child(odd)'); }
    public function test_even(): void { $this->assertVariantGenerates('even:bg-gray-50', ':nth-child(2n)'); }
    public function test_first_of_type(): void { $this->assertVariantGenerates('first-of-type:mt-0', ':first-of-type'); }
    public function test_last_of_type(): void { $this->assertVariantGenerates('last-of-type:mb-0', ':last-of-type'); }
    public function test_only_of_type(): void { $this->assertVariantGenerates('only-of-type:mx-auto', ':only-of-type'); }
    public function test_empty(): void { $this->assertVariantGenerates('empty:hidden', ':empty'); }

    // =========================================================================
    // PSEUDO-ELEMENT VARIANTS
    // =========================================================================

    public function test_before(): void { $this->assertVariantGenerates('before:block', ':before'); }
    public function test_after(): void { $this->assertVariantGenerates('after:block', ':after'); }
    public function test_placeholder(): void { $this->assertVariantGenerates('placeholder:text-gray-400', '::placeholder'); }
    public function test_file(): void { $this->assertVariantGenerates('file:mr-4', '::file-selector-button'); }
    public function test_marker(): void { $this->assertVariantGenerates('marker:text-red-500', '::marker'); }
    public function test_selection(): void { $this->assertVariantGenerates('selection:bg-blue-500', '::selection'); }
    public function test_first_line(): void { $this->assertVariantGenerates('first-line:uppercase', ':first-line'); }
    public function test_first_letter(): void { $this->assertVariantGenerates('first-letter:text-2xl', ':first-letter'); }
    public function test_backdrop(): void { $this->assertVariantGenerates('backdrop:bg-black/50', '::backdrop'); }

    // =========================================================================
    // RESPONSIVE VARIANTS
    // =========================================================================

    public function test_sm(): void
    {
        $css = Tailwind::generate('<div class="sm:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('40rem', $css); // 640px = 40rem
    }

    public function test_md(): void
    {
        $css = Tailwind::generate('<div class="md:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('48rem', $css); // 768px = 48rem
    }

    public function test_lg(): void
    {
        $css = Tailwind::generate('<div class="lg:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('64rem', $css); // 1024px = 64rem
    }

    public function test_xl(): void
    {
        $css = Tailwind::generate('<div class="xl:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('80rem', $css); // 1280px = 80rem
    }

    public function test_2xl(): void
    {
        $css = Tailwind::generate('<div class="2xl:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('96rem', $css); // 1536px = 96rem
    }

    public function test_max_sm(): void
    {
        $css = Tailwind::generate('<div class="max-sm:flex">');
        $this->assertStringContainsString('@media', $css);
        // Tailwind 4 uses "not (min-width: ...)" instead of "max-width"
        $this->assertStringContainsString('not', $css);
    }

    public function test_max_md(): void
    {
        $css = Tailwind::generate('<div class="max-md:flex">');
        $this->assertStringContainsString('@media', $css);
        // Tailwind 4 uses "not (min-width: ...)" instead of "max-width"
        $this->assertStringContainsString('not', $css);
    }

    // =========================================================================
    // DARK MODE
    // =========================================================================

    public function test_dark(): void
    {
        $css = Tailwind::generate('<div class="dark:bg-gray-800">');
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
    }

    // =========================================================================
    // MOTION VARIANTS
    // =========================================================================

    public function test_motion_safe(): void
    {
        $css = Tailwind::generate('<div class="motion-safe:animate-spin">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('prefers-reduced-motion: no-preference', $css);
    }

    public function test_motion_reduce(): void
    {
        $css = Tailwind::generate('<div class="motion-reduce:animate-none">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
    }

    // =========================================================================
    // CONTRAST VARIANTS
    // =========================================================================

    public function test_contrast_more(): void
    {
        $css = Tailwind::generate('<div class="contrast-more:border-2">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('prefers-contrast: more', $css);
    }

    public function test_contrast_less(): void
    {
        $css = Tailwind::generate('<div class="contrast-less:opacity-80">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('prefers-contrast: less', $css);
    }

    // =========================================================================
    // PRINT VARIANT
    // =========================================================================

    public function test_print(): void
    {
        $css = Tailwind::generate('<div class="print:hidden">');
        $this->assertStringContainsString('@media print', $css);
    }

    // =========================================================================
    // ORIENTATION VARIANTS
    // =========================================================================

    public function test_portrait(): void
    {
        $css = Tailwind::generate('<div class="portrait:hidden">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('orientation: portrait', $css);
    }

    public function test_landscape(): void
    {
        $css = Tailwind::generate('<div class="landscape:hidden">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('orientation: landscape', $css);
    }

    // =========================================================================
    // LTR/RTL VARIANTS
    // =========================================================================

    public function test_ltr(): void { $this->assertVariantGenerates('ltr:ml-4', ':where(:dir(ltr)'); }
    public function test_rtl(): void { $this->assertVariantGenerates('rtl:mr-4', ':where(:dir(rtl)'); }

    // =========================================================================
    // OPEN VARIANT
    // =========================================================================

    public function test_open(): void { $this->assertVariantGenerates('open:bg-gray-100', ':is([open], :popover-open, :open)'); }

    // =========================================================================
    // FORCED COLORS VARIANT
    // =========================================================================

    public function test_forced_colors(): void
    {
        $css = Tailwind::generate('<div class="forced-colors:hidden">');
        $this->assertStringContainsString('@media (forced-colors: active)', $css);
    }

    // =========================================================================
    // GROUP VARIANTS
    // =========================================================================

    public function test_group_hover(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-hover:text-blue-500">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_group_focus(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-focus:text-blue-500">');
        $this->assertStringContainsString(':focus', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_group_active(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-active:text-blue-500">');
        $this->assertStringContainsString(':active', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_group_first(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-first:mt-0">');
        $this->assertStringContainsString(':first-child', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_named_group(): void
    {
        $css = Tailwind::generate('<div class="group/sidebar"><span class="group-hover/sidebar:text-blue-500">');
        $this->assertStringContainsString(':hover', $css);
    }

    // =========================================================================
    // PEER VARIANTS
    // =========================================================================

    public function test_peer_hover(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-hover:text-blue-500">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_peer_focus(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-focus:text-blue-500">');
        $this->assertStringContainsString(':focus', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_peer_checked(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-checked:bg-blue-500">');
        $this->assertStringContainsString(':checked', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_peer_invalid(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-invalid:border-red-500">');
        $this->assertStringContainsString(':invalid', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_named_peer(): void
    {
        $css = Tailwind::generate('<input class="peer/email"><span class="peer-invalid/email:border-red-500">');
        $this->assertStringContainsString(':invalid', $css);
    }

    // =========================================================================
    // HAS VARIANTS
    // Note: Tailwind 4 has-* variants (has-hover, has-checked, etc.) apply the
    // pseudo-class directly, NOT using CSS :has() selector
    // For :has() selector, use arbitrary variants like has-[:checked]
    // =========================================================================

    public function test_has_hover(): void
    {
        // has-hover generates a hover media query, not :has(:hover)
        $css = Tailwind::generate('<div class="has-hover:bg-blue-500">');
        $this->assertStringContainsString('@media (hover: hover)', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_has_checked(): void
    {
        // has-checked applies :checked directly, not :has(:checked)
        $css = Tailwind::generate('<div class="has-checked:bg-blue-500">');
        $this->assertStringContainsString(':checked', $css);
    }

    public function test_has_focus(): void
    {
        // has-focus applies :focus directly, not :has(:focus)
        $css = Tailwind::generate('<div class="has-focus:ring-2">');
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_group_has_hover(): void
    {
        // Use arbitrary :has() for actual CSS :has() selector
        $css = Tailwind::generate('<div class="group"><span class="group-has-[:hover]:text-blue-500">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_peer_has_checked(): void
    {
        // Use arbitrary :has() for actual CSS :has() selector
        $css = Tailwind::generate('<input class="peer"><span class="peer-has-[:checked]:bg-blue-500">');
        $this->assertStringContainsString(':checked', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    // =========================================================================
    // NOT VARIANTS
    // =========================================================================

    public function test_not_first(): void
    {
        $css = Tailwind::generate('<div class="not-first:mt-4">');
        $this->assertStringContainsString(':not', $css);
        $this->assertStringContainsString(':first-child', $css);
    }

    public function test_not_last(): void
    {
        $css = Tailwind::generate('<div class="not-last:mb-4">');
        $this->assertStringContainsString(':not', $css);
        $this->assertStringContainsString(':last-child', $css);
    }

    public function test_not_disabled(): void
    {
        $css = Tailwind::generate('<div class="not-disabled:opacity-100">');
        $this->assertStringContainsString(':not', $css);
        $this->assertStringContainsString(':disabled', $css);
    }

    // =========================================================================
    // ARIA VARIANTS
    // =========================================================================

    public function test_aria_checked(): void { $this->assertVariantGenerates('aria-checked:bg-blue-500', '[aria-checked="true"]'); }
    public function test_aria_disabled(): void { $this->assertVariantGenerates('aria-disabled:opacity-50', '[aria-disabled="true"]'); }
    public function test_aria_expanded(): void { $this->assertVariantGenerates('aria-expanded:rotate-180', '[aria-expanded="true"]'); }
    public function test_aria_hidden(): void { $this->assertVariantGenerates('aria-hidden:hidden', '[aria-hidden="true"]'); }
    public function test_aria_pressed(): void { $this->assertVariantGenerates('aria-pressed:bg-blue-500', '[aria-pressed="true"]'); }
    public function test_aria_selected(): void { $this->assertVariantGenerates('aria-selected:bg-blue-500', '[aria-selected="true"]'); }

    public function test_aria_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="aria-[current=page]:font-bold">');
        $this->assertStringContainsString('[aria-current="page"]', $css);
    }

    // =========================================================================
    // DATA VARIANTS
    // =========================================================================

    public function test_data_checked(): void { $this->assertVariantGenerates('data-checked:bg-blue-500', '[data-checked]'); }
    public function test_data_disabled(): void { $this->assertVariantGenerates('data-disabled:opacity-50', '[data-disabled]'); }

    public function test_data_arbitrary(): void
    {
        $css = Tailwind::generate('<div class="data-[state=open]:block">');
        $this->assertStringContainsString('[data-state="open"]', $css);
    }

    // =========================================================================
    // SUPPORTS VARIANTS
    // =========================================================================

    public function test_supports_grid(): void
    {
        $css = Tailwind::generate('<div class="supports-[display:grid]:grid">');
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('display: grid', $css);
    }

    public function test_supports_backdrop(): void
    {
        $css = Tailwind::generate('<div class="supports-[backdrop-filter]:backdrop-blur">');
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('backdrop-filter:', $css);
    }

    // =========================================================================
    // ARBITRARY VARIANTS
    // =========================================================================

    public function test_arbitrary_variant(): void
    {
        $css = Tailwind::generate('<div class="[&>p]:mt-4">');
        // Output selector uses >p without space
        $this->assertStringContainsString('>p', $css);
    }

    public function test_arbitrary_variant_hover(): void
    {
        $css = Tailwind::generate('<div class="[&:nth-child(3)]:bg-blue-500">');
        $this->assertStringContainsString(':nth-child(3)', $css);
    }

    public function test_arbitrary_variant_at_rule(): void
    {
        $css = Tailwind::generate('<div class="[@media(min-width:800px)]:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('800px', $css);
    }

    public function test_arbitrary_variant_attribute(): void
    {
        $css = Tailwind::generate('<div class="[&[data-active]]:bg-blue-500">');
        $this->assertStringContainsString('[data-active]', $css);
    }

    // =========================================================================
    // STACKED VARIANTS
    // =========================================================================

    public function test_stacked_hover_focus(): void
    {
        $css = Tailwind::generate('<div class="hover:focus:bg-blue-500">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_stacked_sm_hover(): void
    {
        $css = Tailwind::generate('<div class="sm:hover:bg-red-500">');
        $this->assertStringContainsString('@media (min-width: 40rem)', $css);
        $this->assertStringContainsString('@media (hover: hover)', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_stacked_dark_hover(): void
    {
        $css = Tailwind::generate('<div class="dark:hover:bg-blue-500">');
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
        $this->assertStringContainsString('@media (hover: hover)', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    public function test_stacked_group_hover_first(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-hover:first:mt-0">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':first-child', $css);
    }

    public function test_triple_stacked_variants(): void
    {
        $css = Tailwind::generate('<div class="sm:dark:hover:text-green-500">');
        $this->assertStringContainsString('@media (min-width: 40rem)', $css);
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
        $this->assertStringContainsString('@media (hover: hover)', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    // =========================================================================
    // IMPORTANT MODIFIER
    // =========================================================================

    public function test_important_modifier(): void
    {
        $css = Tailwind::generate('<div class="!flex">');
        $this->assertStringContainsString('!important', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_important_with_variant(): void
    {
        $css = Tailwind::generate('<div class="hover:!bg-blue-500">');
        $this->assertStringContainsString('!important', $css);
        $this->assertStringContainsString(':hover', $css);
    }

    // =========================================================================
    // CHILD/DESCENDANT SELECTORS (*)
    // =========================================================================

    public function test_direct_children(): void
    {
        $css = Tailwind::generate('<div class="*:mt-4">');
        $this->assertStringContainsString('> *', $css);
    }

    public function test_direct_children_with_variant(): void
    {
        $css = Tailwind::generate('<div class="hover:*:mt-4">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('> *', $css);
    }

    // =========================================================================
    // EXHAUSTIVE DEEP STACKED VARIANTS (3+ deep)
    // =========================================================================

    public function test_4_deep_stacked_sm_dark_hover_focus(): void
    {
        $css = Tailwind::generate('<div class="sm:dark:hover:focus:bg-blue-500">');
        $this->assertStringContainsString('@media (min-width: 40rem)', $css);
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':focus', $css);
    }

    public function test_4_deep_stacked_lg_dark_hover_active(): void
    {
        $css = Tailwind::generate('<div class="lg:dark:hover:active:text-white">');
        $this->assertStringContainsString('@media (min-width: 64rem)', $css);
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':active', $css);
    }

    public function test_triple_stacked_md_group_hover_first(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="md:group-hover:first:mt-0">');
        $this->assertStringContainsString('@media (min-width: 48rem)', $css);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString(':first-child', $css);
    }

    public function test_triple_stacked_dark_peer_focus_disabled(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="dark:peer-focus:disabled:opacity-50">');
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
        $this->assertStringContainsString(':focus', $css);
        $this->assertStringContainsString(':disabled', $css);
    }

    // =========================================================================
    // EXHAUSTIVE RESPONSIVE VARIANTS
    // =========================================================================

    public function test_min_lg(): void
    {
        $css = Tailwind::generate('<div class="min-lg:flex">');
        $this->assertStringContainsString('@media (min-width: 64rem)', $css);
    }

    public function test_min_xl(): void
    {
        $css = Tailwind::generate('<div class="min-xl:flex">');
        $this->assertStringContainsString('@media (min-width: 80rem)', $css);
    }

    public function test_max_lg(): void
    {
        $css = Tailwind::generate('<div class="max-lg:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('not', $css);
    }

    public function test_max_xl(): void
    {
        $css = Tailwind::generate('<div class="max-xl:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('not', $css);
    }

    public function test_max_2xl(): void
    {
        $css = Tailwind::generate('<div class="max-2xl:flex">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('not', $css);
    }

    // =========================================================================
    // EXHAUSTIVE NTH-CHILD VARIANTS
    // =========================================================================

    public function test_nth_3(): void
    {
        $css = Tailwind::generate('<div class="nth-3:bg-blue-500">');
        $this->assertStringContainsString(':nth-child(3)', $css);
    }

    public function test_even_variant(): void
    {
        $css = Tailwind::generate('<div class="even:bg-blue-500">');
        $this->assertStringContainsString(':nth-child(2n)', $css);
    }

    public function test_odd_variant(): void
    {
        $css = Tailwind::generate('<div class="odd:bg-blue-500">');
        $this->assertStringContainsString(':nth-child(odd)', $css);
    }

    public function test_nth_last_3(): void
    {
        $css = Tailwind::generate('<div class="nth-last-3:bg-blue-500">');
        $this->assertStringContainsString(':nth-last-child(3)', $css);
    }

    public function test_nth_of_type_3(): void
    {
        $css = Tailwind::generate('<div class="nth-of-type-3:bg-blue-500">');
        $this->assertStringContainsString(':nth-of-type(3)', $css);
    }

    public function test_nth_last_of_type_3(): void
    {
        $css = Tailwind::generate('<div class="nth-last-of-type-3:bg-blue-500">');
        $this->assertStringContainsString(':nth-last-of-type(3)', $css);
    }

    // =========================================================================
    // EXHAUSTIVE GROUP/PEER VARIANTS
    // =========================================================================

    public function test_group_disabled(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-disabled:opacity-50">');
        $this->assertStringContainsString(':disabled', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_group_checked(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-checked:bg-blue-500">');
        $this->assertStringContainsString(':checked', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_group_focus_within(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-focus-within:ring-2">');
        $this->assertStringContainsString(':focus-within', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_group_focus_visible(): void
    {
        $css = Tailwind::generate('<div class="group"><span class="group-focus-visible:outline-2">');
        $this->assertStringContainsString(':focus-visible', $css);
        $this->assertStringContainsString('.group', $css);
    }

    public function test_peer_disabled(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-disabled:opacity-50">');
        $this->assertStringContainsString(':disabled', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_peer_placeholder_shown(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-placeholder-shown:text-gray-500">');
        $this->assertStringContainsString(':placeholder-shown', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_peer_required(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-required:text-red-500">');
        $this->assertStringContainsString(':required', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    public function test_peer_valid(): void
    {
        $css = Tailwind::generate('<input class="peer"><span class="peer-valid:text-green-500">');
        $this->assertStringContainsString(':valid', $css);
        $this->assertStringContainsString('.peer', $css);
    }

    // =========================================================================
    // EXHAUSTIVE ARIA VARIANTS
    // =========================================================================

    public function test_aria_readonly(): void { $this->assertVariantGenerates('aria-readonly:bg-gray-100', '[aria-readonly="true"]'); }
    public function test_aria_required(): void { $this->assertVariantGenerates('aria-required:text-red-500', '[aria-required="true"]'); }
    public function test_aria_current_page(): void
    {
        $css = Tailwind::generate('<div class="aria-[current=page]:font-bold">');
        $this->assertStringContainsString('[aria-current="page"]', $css);
    }
    public function test_aria_busy(): void { $this->assertVariantGenerates('aria-busy:cursor-wait', '[aria-busy="true"]'); }

    // =========================================================================
    // EXHAUSTIVE DATA VARIANTS
    // =========================================================================

    public function test_data_open(): void { $this->assertVariantGenerates('data-open:block', '[data-open]'); }
    public function test_data_closed(): void { $this->assertVariantGenerates('data-closed:hidden', '[data-closed]'); }
    public function test_data_state_active(): void
    {
        $css = Tailwind::generate('<div class="data-[state=active]:bg-blue-500">');
        $this->assertStringContainsString('[data-state="active"]', $css);
    }
    public function test_data_orientation_vertical(): void
    {
        $css = Tailwind::generate('<div class="data-[orientation=vertical]:flex-col">');
        $this->assertStringContainsString('[data-orientation="vertical"]', $css);
    }

    // =========================================================================
    // EXHAUSTIVE SUPPORTS VARIANTS
    // =========================================================================

    public function test_supports_flex(): void
    {
        $css = Tailwind::generate('<div class="supports-[display:flex]:flex">');
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    public function test_supports_gap(): void
    {
        $css = Tailwind::generate('<div class="supports-[gap:1rem]:gap-4">');
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('gap: 1rem', $css);
    }

    public function test_supports_sticky(): void
    {
        $css = Tailwind::generate('<div class="supports-[position:sticky]:sticky">');
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('position: sticky', $css);
    }

    // =========================================================================
    // EXHAUSTIVE ARBITRARY VARIANTS
    // =========================================================================

    public function test_arbitrary_first_child(): void
    {
        $css = Tailwind::generate('<div class="[&:first-child]:mt-0">');
        $this->assertStringContainsString(':first-child', $css);
    }

    public function test_arbitrary_last_child(): void
    {
        $css = Tailwind::generate('<div class="[&:last-child]:mb-0">');
        $this->assertStringContainsString(':last-child', $css);
    }

    public function test_arbitrary_not_first(): void
    {
        $css = Tailwind::generate('<div class="[&:not(:first-child)]:mt-4">');
        $this->assertStringContainsString(':not(:first-child)', $css);
    }

    public function test_arbitrary_not_last(): void
    {
        $css = Tailwind::generate('<div class="[&:not(:last-child)]:mb-4">');
        $this->assertStringContainsString(':not(:last-child)', $css);
    }

    public function test_arbitrary_descendant(): void
    {
        $css = Tailwind::generate('<div class="[&_p]:text-gray-700">');
        $this->assertStringContainsString(' p', $css);
    }

    public function test_arbitrary_child_selector(): void
    {
        $css = Tailwind::generate('<div class="[&>div]:border">');
        $this->assertStringContainsString('>div', $css);
    }

    public function test_arbitrary_sibling_selector(): void
    {
        $css = Tailwind::generate('<div class="[&+div]:mt-4">');
        $this->assertStringContainsString('+div', $css);
    }

    public function test_arbitrary_general_sibling(): void
    {
        $css = Tailwind::generate('<div class="[&~div]:text-gray-500">');
        $this->assertStringContainsString('~div', $css);
    }

    public function test_arbitrary_data_attribute(): void
    {
        $css = Tailwind::generate('<div class="[&[data-loading]]:opacity-50">');
        $this->assertStringContainsString('[data-loading]', $css);
    }

    public function test_arbitrary_class_selector(): void
    {
        $css = Tailwind::generate('<div class="[&.active]:bg-blue-500">');
        $this->assertStringContainsString('.active', $css);
    }

    // =========================================================================
    // EXHAUSTIVE CONTAINER QUERIES
    // =========================================================================

    public function test_container_sm(): void
    {
        $css = Tailwind::generate('<div class="@container"><span class="@sm:flex">');
        $this->assertStringContainsString('@container', $css);
    }

    public function test_container_md(): void
    {
        $css = Tailwind::generate('<div class="@container"><span class="@md:grid">');
        $this->assertStringContainsString('@container', $css);
    }

    public function test_container_lg(): void
    {
        $css = Tailwind::generate('<div class="@container"><span class="@lg:hidden">');
        $this->assertStringContainsString('@container', $css);
    }

    public function test_container_xl(): void
    {
        $css = Tailwind::generate('<div class="@container"><span class="@xl:block">');
        $this->assertStringContainsString('@container', $css);
    }

    // =========================================================================
    // EXHAUSTIVE MOTION/PREFERENCE VARIANTS
    // =========================================================================

    public function test_prefers_reduced_motion(): void
    {
        $css = Tailwind::generate('<div class="motion-reduce:transition-none">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('prefers-reduced-motion: reduce', $css);
    }

    public function test_prefers_no_reduced_motion(): void
    {
        $css = Tailwind::generate('<div class="motion-safe:transition-all">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('prefers-reduced-motion: no-preference', $css);
    }

    // =========================================================================
    // EXHAUSTIVE INVERTED COLORS VARIANT
    // =========================================================================

    public function test_inverted_colors(): void
    {
        $css = Tailwind::generate('<div class="inverted-colors:invert">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('inverted-colors: inverted', $css);
    }

    // =========================================================================
    // EXHAUSTIVE SCRIPTING VARIANT
    // =========================================================================

    public function test_noscript(): void
    {
        $css = Tailwind::generate('<div class="noscript:hidden">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('scripting: none', $css);
    }

    // =========================================================================
    // EXHAUSTIVE POINTER VARIANTS
    // =========================================================================

    public function test_pointer_coarse(): void
    {
        $css = Tailwind::generate('<div class="pointer-coarse:p-4">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('pointer: coarse', $css);
    }

    public function test_pointer_fine(): void
    {
        $css = Tailwind::generate('<div class="pointer-fine:p-2">');
        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('pointer: fine', $css);
    }

    // =========================================================================
    // EXHAUSTIVE STARTING STYLE VARIANT
    // =========================================================================

    public function test_starting(): void
    {
        $css = Tailwind::generate('<div class="starting:opacity-0">');
        $this->assertStringContainsString('@starting-style', $css);
    }

    // =========================================================================
    // EXHAUSTIVE IN-* VARIANTS (inside composition)
    // =========================================================================

    public function test_in_hover(): void
    {
        $css = Tailwind::generate('<div class="in-hover:text-blue-500">');
        // in-hover applies to elements inside a hovered container
        $this->assertStringContainsString(':hover', $css);
    }

    // =========================================================================
    // EXHAUSTIVE STANDALONE CHILDREN VARIANT
    // =========================================================================

    public function test_children_variant(): void
    {
        $css = Tailwind::generate('<div class="*:mt-2">');
        $this->assertStringContainsString('> *', $css);
    }

    public function test_children_first_variant(): void
    {
        $css = Tailwind::generate('<div class="first:*:mt-0">');
        $this->assertStringContainsString(':first-child', $css);
        $this->assertStringContainsString('> *', $css);
    }

    // =========================================================================
    // EXHAUSTIVE MULTIPLE CLASSES WITH VARIANTS
    // =========================================================================

    public function test_multiple_hover_classes(): void
    {
        $css = Tailwind::generate('<div class="hover:bg-blue-500 hover:text-white hover:shadow-lg">');
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('background-color:', $css);
        $this->assertStringContainsString('color:', $css);
        $this->assertStringContainsString('box-shadow:', $css);
    }

    public function test_multiple_responsive_classes(): void
    {
        $css = Tailwind::generate('<div class="sm:flex sm:gap-4 sm:p-6">');
        $this->assertStringContainsString('@media (min-width: 40rem)', $css);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('gap:', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    public function test_mixed_variants(): void
    {
        $css = Tailwind::generate('<div class="flex hover:flex-col md:flex-row dark:bg-gray-900">');
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('flex-direction: column', $css);
        $this->assertStringContainsString('@media (min-width: 48rem)', $css);
        $this->assertStringContainsString('flex-direction: row', $css);
        $this->assertStringContainsString('@media (prefers-color-scheme: dark)', $css);
    }
}
