<?php

declare(strict_types=1);

namespace TailwindPHP\Utils;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class compare extends TestCase
{
    private const LESS = -1;
    private const EQUAL = 0;
    private const GREATER = 1;

    public static function comparisonProvider(): array
    {
        return [
            // Same strings
            ['abc', 'abc', self::EQUAL],

            // Shorter string comes first
            ['abc', 'abcd', self::LESS],

            // Longer string comes first
            ['abcd', 'abc', self::GREATER],

            // Numbers
            ['1', '1', self::EQUAL],
            ['1', '2', self::LESS],
            ['2', '1', self::GREATER],
            ['1', '10', self::LESS],
            ['10', '1', self::GREATER],

            // Numbers of different lengths
            ['75', '700', self::LESS],
            ['700', '75', self::GREATER],
            ['75', '770', self::LESS],
            ['770', '75', self::GREATER],
        ];
    }

    #[Test]
    #[DataProvider('comparisonProvider')]
    public function compares_strings_correctly(string $a, string $b, int $expected): void
    {
        $result = compare($a, $b);
        $this->assertEquals($expected, $result <=> 0);
    }

    #[Test]
    public function sorts_strings_with_numbers_consistently(): void
    {
        $input = ['p-0', 'p-0.5', 'p-1', 'p-1.5', 'p-10', 'p-12', 'p-2', 'p-20', 'p-21'];
        shuffle($input);
        usort($input, 'TailwindPHP\\Utils\\compare');

        $this->assertEquals([
            'p-0',
            'p-0.5',
            'p-1',
            'p-1.5',
            'p-2',
            'p-10',
            'p-12',
            'p-20',
            'p-21',
        ], $input);
    }

    #[Test]
    public function sorts_strings_with_modifiers_consistently(): void
    {
        $input = [
            'text-5xl',
            'text-6xl',
            'text-6xl/loose',
            'text-6xl/wide',
            'bg-red-500',
            'bg-red-500/50',
            'bg-red-500/70',
            'bg-red-500/60',
            'bg-red-50',
            'bg-red-50/50',
            'bg-red-50/70',
            'bg-red-50/60',
        ];
        shuffle($input);
        usort($input, 'TailwindPHP\\Utils\\compare');

        $this->assertEquals([
            'bg-red-50',
            'bg-red-50/50',
            'bg-red-50/60',
            'bg-red-50/70',
            'bg-red-500',
            'bg-red-500/50',
            'bg-red-500/60',
            'bg-red-500/70',
            'text-5xl',
            'text-6xl',
            'text-6xl/loose',
            'text-6xl/wide',
        ], $input);
    }
}
