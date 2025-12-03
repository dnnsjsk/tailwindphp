<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

/**
 * Tests for constant-fold-declaration.php
 *
 * Port of: packages/tailwindcss/src/constant-fold-declaration.test.ts
 */
class constant_fold_declaration extends TestCase
{
    /**
     * Test cases for constant folding.
     */
    public static function foldableExpressionsProvider(): array
    {
        return [
            // Simple expression
            ['calc(1 + 1)', '2'],
            ['calc(3 - 2)', '1'],
            ['calc(2 * 3)', '6'],
            ['calc(8 / 2)', '4'],

            // Nested
            ['calc(1 + calc(1 + 1))', '3'],
            ['calc(3 - calc(1 + 2))', '0'],
            ['calc(2 * calc(1 + 3))', '8'],
            ['calc(8 / calc(2 + 2))', '2'],
            ['calc(1 + (1 + 1))', '3'],
            ['calc(3 - (1 + 2))', '0'],
            ['calc(2 * (1 + 3))', '8'],
            ['calc(8 / (2 + 2))', '2'],

            // With units
            ['calc(1rem * 2)', '2rem'],
            ['calc(2rem - 0.5rem)', '1.5rem'],
            ['calc(3rem * 6)', '18rem'],
            ['calc(5rem / 2)', '2.5rem'],

            // Nested partial evaluation
            ['calc(calc(1 + 2) + 2rem)', 'calc(3 + 2rem)'],

            // Evaluation only handles two operands right now
            ['calc(1 + 2 + 3)', 'calc(1 + 2 + 3)'],
        ];
    }

    #[Test]
    #[DataProvider('foldableExpressionsProvider')]
    public function should_constant_fold(string $input, string $expected): void
    {
        $this->assertEquals($expected, constantFoldDeclaration($input));
    }

    /**
     * Test cases for expressions that should not fold due to different units.
     */
    public static function differentUnitsProvider(): array
    {
        return [
            ['calc(1rem * 2%)'],
            ['calc(1rem * 2px)'],
            ['calc(2rem - 6)'],
            ['calc(3rem * 3dvw)'],
            ['calc(3rem * 2dvh)'],
            ['calc(5rem / 17px)'],
        ];
    }

    #[Test]
    #[DataProvider('differentUnitsProvider')]
    public function should_not_constant_fold_different_units(string $input): void
    {
        $this->assertEquals($input, constantFoldDeclaration($input));
    }

    /**
     * Test cases for expressions that should fold to zero.
     */
    public static function foldToZeroProvider(): array
    {
        return [
            ['calc(0 * 100vw)'],
            ['calc(0 * calc(1 * 2))'],
            ['calc(0 * var(--foo))'],
            ['calc(0 * calc(var(--spacing) * 32))'],

            ['calc(100vw * 0)'],
            ['calc(calc(1 * 2) * 0)'],
            ['calc(var(--foo) * 0)'],
            ['calc(calc(var(--spacing, 0.25rem) * 32) * 0)'],
            ['calc(var(--spacing, 0.25rem) * -0)'],
            ['calc(-0px * -1)'],

            // Zeroes
            ['0px'],
            ['0rem'],
            ['0em'],
            ['0dvh'],
            ['-0'],
            ['+0'],
            ['-0.0rem'],
            ['+0.00rem'],
        ];
    }

    #[Test]
    #[DataProvider('foldToZeroProvider')]
    public function should_constant_fold_to_zero(string $input): void
    {
        $this->assertEquals('0', constantFoldDeclaration($input));
    }

    /**
     * Test cases for non-foldable units that should convert to their canonical form.
     */
    public static function nonFoldableUnitsProvider(): array
    {
        return [
            ['0deg', '0deg'],
            ['0rad', '0deg'],
            ['0%', '0%'],
            ['0turn', '0deg'],
            ['0fr', '0fr'],
            ['0ms', '0s'],
            ['0s', '0s'],
            ['-0.0deg', '0deg'],
            ['-0.0rad', '0deg'],
            ['-0.0%', '0%'],
            ['-0.0turn', '0deg'],
            ['-0.0fr', '0fr'],
            ['-0.0ms', '0s'],
            ['-0.0s', '0s'],
        ];
    }

    #[Test]
    #[DataProvider('nonFoldableUnitsProvider')]
    public function should_not_fold_non_foldable_units_to_zero(string $input, string $expected): void
    {
        $this->assertEquals($expected, constantFoldDeclaration($input));
    }

    #[Test]
    public function should_not_constant_fold_when_dividing_by_zero(): void
    {
        $this->assertEquals('calc(123rem / 0)', constantFoldDeclaration('calc(123rem / 0)'));
    }
}
