<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use TailwindPHP\DesignSystem\DesignSystem;
use function TailwindPHP\DesignSystem\buildDesignSystem;
use function TailwindPHP\getClassOrder;

/**
 * Tests for sort.php
 *
 * Port of: packages/tailwindcss/src/sort.test.ts
 */
class sort extends TestCase
{
    private static ?DesignSystem $design = null;

    /**
     * Create a design system with test theme values.
     */
    private static function loadDesign(): DesignSystem
    {
        if (self::$design === null) {
            $theme = new Theme();
            $theme->add('--spacing-1', '0.25rem');
            $theme->add('--spacing-3', '0.75rem');
            $theme->add('--spacing-4', '1rem');
            $theme->add('--color-red-500', 'red');
            $theme->add('--color-blue-500', 'blue');
            self::$design = buildDesignSystem($theme);
        }
        return self::$design;
    }

    /**
     * Sort classes using the design system.
     */
    private function sortClasses(string $input, DesignSystem $design): string
    {
        $classes = explode(' ', $input);
        $ordered = getClassOrder($design, $classes);
        return $this->defaultSort($ordered);
    }

    /**
     * Default sort implementation (like prettier-plugin-tailwindcss would use).
     *
     * @param array<array{0: string, 1: int|null}> $arrayOfTuples
     */
    private function defaultSort(array $arrayOfTuples): string
    {
        usort($arrayOfTuples, function ($a, $z) {
            if ($a[1] === $z[1]) return 0;
            if ($a[1] === null) return -1;
            if ($z[1] === null) return 1;
            return $a[1] <=> $z[1];
        });

        return implode(' ', array_map(fn($tuple) => $tuple[0], $arrayOfTuples));
    }

    public static function sortingProvider(): array
    {
        return [
            // Utilities
            ['py-3 p-1 px-3', 'p-1 px-3 py-3'],

            // Utilities with variants
            ['px-3 focus:hover:p-3 hover:p-1 py-3', 'px-3 py-3 hover:p-1 focus:hover:p-3'],

            // Utilities with important
            ['px-3 py-4! p-1', 'p-1 px-3 py-4!'],
            ['py-4! px-3 p-1', 'p-1 px-3 py-4!'],

            // User CSS order is the same and moved to the front
            ['b p-1 a', 'b a p-1'],
            ['hover:b focus:p-1 a', 'hover:b a focus:p-1'],
        ];
    }

    #[Test]
    #[DataProvider('sortingProvider')]
    public function sorts_classes(string $input, string $expected): void
    {
        $result = $this->sortClasses($input, self::loadDesign());
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function can_sort_classes_deterministically_across_multiple_class_lists(): void
    {
        $classes = [
            [
                'a-class px-3 p-1 b-class py-3 bg-red-500 bg-blue-500',
                'a-class b-class bg-blue-500 bg-red-500 p-1 px-3 py-3',
            ],
            [
                'px-3 b-class p-1 py-3 bg-blue-500 a-class bg-red-500',
                'b-class a-class bg-blue-500 bg-red-500 p-1 px-3 py-3',
            ],
        ];

        // Shared design
        $design = self::loadDesign();
        foreach ($classes as [$input, $output]) {
            $this->assertEquals($output, $this->sortClasses($input, $design));
        }

        // Fresh design (reset cache)
        self::$design = null;
        foreach ($classes as [$input, $output]) {
            $this->assertEquals($output, $this->sortClasses($input, self::loadDesign()));
        }
    }

    #[Test]
    public function sorts_arbitrary_values_across_one_or_more_class_lists_consistently(): void
    {
        $classes = [
            ['[--fg:#fff]', '[--fg:#fff]'],
            ['[--bg:#111] [--bg_hover:#000] [--fg:#fff]', '[--bg:#111] [--bg_hover:#000] [--fg:#fff]'],
        ];

        // Shared design
        $design = self::loadDesign();
        foreach ($classes as [$input, $output]) {
            $this->assertEquals($output, $this->sortClasses($input, $design));
        }

        // Fresh design (reset cache)
        self::$design = null;
        foreach ($classes as [$input, $output]) {
            $this->assertEquals($output, $this->sortClasses($input, self::loadDesign()));
        }
    }
}
