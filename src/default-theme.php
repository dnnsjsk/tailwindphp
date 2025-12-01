<?php

declare(strict_types=1);

namespace TailwindPHP;

/**
 * Default Theme - Standard Tailwind CSS theme values.
 *
 * Port of: packages/tailwindcss/theme.css
 *
 * This provides the default theme values that Tailwind ships with.
 * The original is defined in CSS, we provide it as PHP for programmatic use.
 */
class DefaultTheme
{
    /**
     * Create a Theme instance with all default Tailwind values.
     */
    public static function create(): Theme
    {
        $theme = new Theme();

        self::addSpacing($theme);
        self::addBreakpoints($theme);
        self::addBorderRadius($theme);
        self::addFontSizes($theme);
        self::addFontWeights($theme);
        self::addLineHeights($theme);
        self::addLetterSpacing($theme);
        self::addColors($theme);
        self::addShadows($theme);
        self::addBlur($theme);
        self::addWidths($theme);
        self::addZIndex($theme);
        self::addOpacity($theme);
        self::addTransitions($theme);

        return $theme;
    }

    private static function addSpacing(Theme $theme): void
    {
        $theme->add('--spacing', '0.25rem');
        $theme->add('--spacing-0', '0px');
        $theme->add('--spacing-px', '1px');
        $theme->add('--spacing-0_5', '0.125rem');
        $theme->add('--spacing-1', '0.25rem');
        $theme->add('--spacing-1_5', '0.375rem');
        $theme->add('--spacing-2', '0.5rem');
        $theme->add('--spacing-2_5', '0.625rem');
        $theme->add('--spacing-3', '0.75rem');
        $theme->add('--spacing-3_5', '0.875rem');
        $theme->add('--spacing-4', '1rem');
        $theme->add('--spacing-5', '1.25rem');
        $theme->add('--spacing-6', '1.5rem');
        $theme->add('--spacing-7', '1.75rem');
        $theme->add('--spacing-8', '2rem');
        $theme->add('--spacing-9', '2.25rem');
        $theme->add('--spacing-10', '2.5rem');
        $theme->add('--spacing-11', '2.75rem');
        $theme->add('--spacing-12', '3rem');
        $theme->add('--spacing-14', '3.5rem');
        $theme->add('--spacing-16', '4rem');
        $theme->add('--spacing-20', '5rem');
        $theme->add('--spacing-24', '6rem');
        $theme->add('--spacing-28', '7rem');
        $theme->add('--spacing-32', '8rem');
        $theme->add('--spacing-36', '9rem');
        $theme->add('--spacing-40', '10rem');
        $theme->add('--spacing-44', '11rem');
        $theme->add('--spacing-48', '12rem');
        $theme->add('--spacing-52', '13rem');
        $theme->add('--spacing-56', '14rem');
        $theme->add('--spacing-60', '15rem');
        $theme->add('--spacing-64', '16rem');
        $theme->add('--spacing-72', '18rem');
        $theme->add('--spacing-80', '20rem');
        $theme->add('--spacing-96', '24rem');
    }

    private static function addBreakpoints(Theme $theme): void
    {
        $theme->add('--breakpoint-sm', '40rem');
        $theme->add('--breakpoint-md', '48rem');
        $theme->add('--breakpoint-lg', '64rem');
        $theme->add('--breakpoint-xl', '80rem');
        $theme->add('--breakpoint-2xl', '96rem');
    }

    private static function addBorderRadius(Theme $theme): void
    {
        $theme->add('--radius', '0.25rem');
        $theme->add('--radius-none', '0px');
        $theme->add('--radius-sm', '0.125rem');
        $theme->add('--radius-md', '0.375rem');
        $theme->add('--radius-lg', '0.5rem');
        $theme->add('--radius-xl', '0.75rem');
        $theme->add('--radius-2xl', '1rem');
        $theme->add('--radius-3xl', '1.5rem');
        $theme->add('--radius-full', '9999px');
    }

    private static function addFontSizes(Theme $theme): void
    {
        $theme->add('--font-size-xs', '0.75rem');
        $theme->add('--font-size-xs--line-height', '1rem');
        $theme->add('--font-size-sm', '0.875rem');
        $theme->add('--font-size-sm--line-height', '1.25rem');
        $theme->add('--font-size-base', '1rem');
        $theme->add('--font-size-base--line-height', '1.5rem');
        $theme->add('--font-size-lg', '1.125rem');
        $theme->add('--font-size-lg--line-height', '1.75rem');
        $theme->add('--font-size-xl', '1.25rem');
        $theme->add('--font-size-xl--line-height', '1.75rem');
        $theme->add('--font-size-2xl', '1.5rem');
        $theme->add('--font-size-2xl--line-height', '2rem');
        $theme->add('--font-size-3xl', '1.875rem');
        $theme->add('--font-size-3xl--line-height', '2.25rem');
        $theme->add('--font-size-4xl', '2.25rem');
        $theme->add('--font-size-4xl--line-height', '2.5rem');
        $theme->add('--font-size-5xl', '3rem');
        $theme->add('--font-size-5xl--line-height', '1');
        $theme->add('--font-size-6xl', '3.75rem');
        $theme->add('--font-size-6xl--line-height', '1');
        $theme->add('--font-size-7xl', '4.5rem');
        $theme->add('--font-size-7xl--line-height', '1');
        $theme->add('--font-size-8xl', '6rem');
        $theme->add('--font-size-8xl--line-height', '1');
        $theme->add('--font-size-9xl', '8rem');
        $theme->add('--font-size-9xl--line-height', '1');
    }

    private static function addFontWeights(Theme $theme): void
    {
        $theme->add('--font-weight-thin', '100');
        $theme->add('--font-weight-extralight', '200');
        $theme->add('--font-weight-light', '300');
        $theme->add('--font-weight-normal', '400');
        $theme->add('--font-weight-medium', '500');
        $theme->add('--font-weight-semibold', '600');
        $theme->add('--font-weight-bold', '700');
        $theme->add('--font-weight-extrabold', '800');
        $theme->add('--font-weight-black', '900');
    }

    private static function addLineHeights(Theme $theme): void
    {
        $theme->add('--line-height-none', '1');
        $theme->add('--line-height-tight', '1.25');
        $theme->add('--line-height-snug', '1.375');
        $theme->add('--line-height-normal', '1.5');
        $theme->add('--line-height-relaxed', '1.625');
        $theme->add('--line-height-loose', '2');
    }

    private static function addLetterSpacing(Theme $theme): void
    {
        $theme->add('--letter-spacing-tighter', '-0.05em');
        $theme->add('--letter-spacing-tight', '-0.025em');
        $theme->add('--letter-spacing-normal', '0em');
        $theme->add('--letter-spacing-wide', '0.025em');
        $theme->add('--letter-spacing-wider', '0.05em');
        $theme->add('--letter-spacing-widest', '0.1em');
    }

    private static function addColors(Theme $theme): void
    {
        $theme->add('--color-black', '#000');
        $theme->add('--color-white', '#fff');
        $theme->add('--color-transparent', 'transparent');
        $theme->add('--color-current', 'currentColor');
        $theme->add('--color-inherit', 'inherit');
    }

    private static function addShadows(Theme $theme): void
    {
        // Box shadows
        $theme->add('--shadow', '0 1px 3px 0 rgb(0 0 0 / 0.1), 0 1px 2px -1px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-sm', '0 1px 2px 0 rgb(0 0 0 / 0.05)');
        $theme->add('--shadow-md', '0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-lg', '0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-xl', '0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1)');
        $theme->add('--shadow-2xl', '0 25px 50px -12px rgb(0 0 0 / 0.25)');
        $theme->add('--shadow-inner', 'inset 0 2px 4px 0 rgb(0 0 0 / 0.05)');

        // Inset shadows
        $theme->add('--inset-shadow-sm', 'inset 0 1px 1px rgb(0 0 0 / 0.05)');
        $theme->add('--inset-shadow', 'inset 0 2px 4px rgb(0 0 0 / 0.05)');

        // Drop shadows
        $theme->add('--drop-shadow', '0 1px 2px rgb(0 0 0 / 0.1), 0 1px 1px rgb(0 0 0 / 0.06)');
        $theme->add('--drop-shadow-sm', '0 1px 1px rgb(0 0 0 / 0.05)');
        $theme->add('--drop-shadow-md', '0 4px 3px rgb(0 0 0 / 0.07), 0 2px 2px rgb(0 0 0 / 0.06)');
        $theme->add('--drop-shadow-lg', '0 10px 8px rgb(0 0 0 / 0.04), 0 4px 3px rgb(0 0 0 / 0.1)');
        $theme->add('--drop-shadow-xl', '0 20px 13px rgb(0 0 0 / 0.03), 0 8px 5px rgb(0 0 0 / 0.08)');
        $theme->add('--drop-shadow-2xl', '0 25px 25px rgb(0 0 0 / 0.15)');
    }

    private static function addBlur(Theme $theme): void
    {
        $theme->add('--blur', '8px');
        $theme->add('--blur-none', '0');
        $theme->add('--blur-sm', '4px');
        $theme->add('--blur-md', '12px');
        $theme->add('--blur-lg', '16px');
        $theme->add('--blur-xl', '24px');
        $theme->add('--blur-2xl', '40px');
        $theme->add('--blur-3xl', '64px');
    }

    private static function addWidths(Theme $theme): void
    {
        $theme->add('--width-xs', '20rem');
        $theme->add('--width-sm', '24rem');
        $theme->add('--width-md', '28rem');
        $theme->add('--width-lg', '32rem');
        $theme->add('--width-xl', '36rem');
        $theme->add('--width-2xl', '42rem');
        $theme->add('--width-3xl', '48rem');
        $theme->add('--width-4xl', '56rem');
        $theme->add('--width-5xl', '64rem');
        $theme->add('--width-6xl', '72rem');
        $theme->add('--width-7xl', '80rem');
    }

    private static function addZIndex(Theme $theme): void
    {
        $theme->add('--z-index-0', '0');
        $theme->add('--z-index-10', '10');
        $theme->add('--z-index-20', '20');
        $theme->add('--z-index-30', '30');
        $theme->add('--z-index-40', '40');
        $theme->add('--z-index-50', '50');
    }

    private static function addOpacity(Theme $theme): void
    {
        $theme->add('--opacity-0', '0');
        $theme->add('--opacity-5', '0.05');
        $theme->add('--opacity-10', '0.1');
        $theme->add('--opacity-15', '0.15');
        $theme->add('--opacity-20', '0.2');
        $theme->add('--opacity-25', '0.25');
        $theme->add('--opacity-30', '0.3');
        $theme->add('--opacity-35', '0.35');
        $theme->add('--opacity-40', '0.4');
        $theme->add('--opacity-45', '0.45');
        $theme->add('--opacity-50', '0.5');
        $theme->add('--opacity-55', '0.55');
        $theme->add('--opacity-60', '0.6');
        $theme->add('--opacity-65', '0.65');
        $theme->add('--opacity-70', '0.7');
        $theme->add('--opacity-75', '0.75');
        $theme->add('--opacity-80', '0.8');
        $theme->add('--opacity-85', '0.85');
        $theme->add('--opacity-90', '0.9');
        $theme->add('--opacity-95', '0.95');
        $theme->add('--opacity-100', '1');
    }

    private static function addTransitions(Theme $theme): void
    {
        // Duration
        $theme->add('--transition-duration-0', '0s');
        $theme->add('--transition-duration-75', '75ms');
        $theme->add('--transition-duration-100', '100ms');
        $theme->add('--transition-duration-150', '150ms');
        $theme->add('--transition-duration-200', '200ms');
        $theme->add('--transition-duration-300', '300ms');
        $theme->add('--transition-duration-500', '500ms');
        $theme->add('--transition-duration-700', '700ms');
        $theme->add('--transition-duration-1000', '1000ms');

        // Easing
        $theme->add('--ease-linear', 'linear');
        $theme->add('--ease-in', 'cubic-bezier(0.4, 0, 1, 1)');
        $theme->add('--ease-out', 'cubic-bezier(0, 0, 0.2, 1)');
        $theme->add('--ease-in-out', 'cubic-bezier(0.4, 0, 0.2, 1)');
    }
}
