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
}
