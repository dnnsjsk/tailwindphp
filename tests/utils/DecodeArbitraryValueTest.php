<?php

declare(strict_types=1);

namespace TailwindPHP\Tests\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

use function TailwindPHP\DecodeArbitraryValue\decodeArbitraryValue;

class DecodeArbitraryValueTest extends TestCase
{
    // Decoding arbitrary values tests

    #[Test]
    public function replaces_an_underscore_with_a_space(): void
    {
        $this->assertEquals('foo bar', decodeArbitraryValue('foo_bar'));
    }

    #[Test]
    public function replaces_multiple_underscores_with_spaces(): void
    {
        $this->assertEquals('  foo  bar  ', decodeArbitraryValue('__foo__bar__'));
    }

    #[Test]
    public function replaces_escaped_underscores_with_a_normal_underscore(): void
    {
        $this->assertEquals('foo_bar', decodeArbitraryValue('foo\\_bar'));
    }

    #[Test]
    public function does_not_replace_underscores_in_url(): void
    {
        $this->assertEquals('url(./my_file.jpg)', decodeArbitraryValue('url(./my_file.jpg)'));
        $this->assertEquals(
            'no-repeat url(./my_file.jpg)',
            decodeArbitraryValue('no-repeat_url(./my_file.jpg)')
        );
    }

    #[Test]
    public function does_not_replace_underscores_in_first_argument_of_var(): void
    {
        $this->assertEquals('var(--spacing-1_5)', decodeArbitraryValue('var(--spacing-1_5)'));
        $this->assertEquals('var(--spacing-1_5, 1rem)', decodeArbitraryValue('var(--spacing-1_5,_1rem)'));
        $this->assertEquals(
            'var(--spacing-1_5, var(--spacing-2_5, 1rem))',
            decodeArbitraryValue('var(--spacing-1_5,_var(--spacing-2_5,_1rem))')
        );
    }

    #[Test]
    public function does_not_replace_underscores_in_first_argument_of_theme(): void
    {
        $this->assertEquals('theme(--spacing-1_5)', decodeArbitraryValue('theme(--spacing-1_5)'));
        $this->assertEquals('theme(--spacing-1_5, 1rem)', decodeArbitraryValue('theme(--spacing-1_5,_1rem)'));
        $this->assertEquals(
            'theme(--spacing-1_5, theme(--spacing-2_5, 1rem))',
            decodeArbitraryValue('theme(--spacing-1_5,_theme(--spacing-2_5,_1rem))')
        );
    }

    #[Test]
    public function leaves_var_as_is(): void
    {
        $this->assertEquals('var(--foo)', decodeArbitraryValue('var(--foo)'));
        $this->assertEquals('var(--headings-h1-size)', decodeArbitraryValue('var(--headings-h1-size)'));
    }

    // Math operators tests

    public static function mathOperatorsProvider(): array
    {
        return [
            // math functions like calc(…) get spaces around operators
            ['calc(1+2)', 'calc(1 + 2)'],
            ['calc(100%+1rem)', 'calc(100% + 1rem)'],
            ['calc(1+calc(100%-20px))', 'calc(1 + calc(100% - 20px))'],
            ['calc(var(--headings-h1-size)*100)', 'calc(var(--headings-h1-size) * 100)'],
            [
                'calc(var(--headings-h1-size)*calc(100%+50%))',
                'calc(var(--headings-h1-size) * calc(100% + 50%))',
            ],
            ['min(1+2)', 'min(1 + 2)'],
            ['max(1+2)', 'max(1 + 2)'],
            ['clamp(1+2,1+3,1+4)', 'clamp(1 + 2, 1 + 3, 1 + 4)'],
            ['var(--width, calc(100%+1rem))', 'var(--width, calc(100% + 1rem))'],
            ['calc(1px*(7--12/24))', 'calc(1px * (7 - -12 / 24))'],
            ['calc((7-32)/(1400-782))', 'calc((7 - 32) / (1400 - 782))'],
            ['calc((7-3)/(1400-782))', 'calc((7 - 3) / (1400 - 782))'],
            ['calc((7-32)/(1400-782))', 'calc((7 - 32) / (1400 - 782))'],
            ['calc((70-3)/(1400-782))', 'calc((70 - 3) / (1400 - 782))'],
            ['calc((70-32)/(1400-782))', 'calc((70 - 32) / (1400 - 782))'],
            ['calc((704-3)/(1400-782))', 'calc((704 - 3) / (1400 - 782))'],
            ['calc((704-320))', 'calc((704 - 320))'],
            ['calc((704-320)/1)', 'calc((704 - 320) / 1)'],
            ['calc((704-320)/(1400-782))', 'calc((704 - 320) / (1400 - 782))'],
            ['calc(24px+-1rem)', 'calc(24px + -1rem)'],
            ['calc(24px+(-1rem))', 'calc(24px + (-1rem))'],
            ['calc(24px_+_-1rem)', 'calc(24px + -1rem)'],
            ['calc(24px+(-1rem))', 'calc(24px + (-1rem))'],
            ['calc(24px_+_(-1rem))', 'calc(24px + (-1rem))'],
            [
                'calc(var(--10-10px,calc(-20px-(-30px--40px)-50px)))',
                'calc(var(--10-10px,calc(-20px - (-30px - -40px) - 50px)))',
            ],
            ['calc(theme(spacing.1-bar))', 'calc(theme(spacing.1-bar))'],
            ['theme(spacing.1-bar)', 'theme(spacing.1-bar)'],
            ['calc(theme(spacing.1-bar))', 'calc(theme(spacing.1-bar))'],
            ['calc(1rem-theme(spacing.1-bar))', 'calc(1rem - theme(spacing.1-bar))'],
            ['calc(theme(spacing.foo-2))', 'calc(theme(spacing.foo-2))'],
            ['calc(theme(spacing.foo-bar))', 'calc(theme(spacing.foo-bar))'],

            // With percentages
            ['calc(100%-var(--foo))', 'calc(100% - var(--foo))'],

            // With uppercase units
            ['calc(100PX-theme(spacing.1))', 'calc(100PX - theme(spacing.1))'],

            // Preserving CSS keyword tokens like fit-content without splitting around hyphens in complex expressions
            ['min(fit-content,calc(100dvh-4rem))', 'min(fit-content, calc(100dvh - 4rem))'],
            [
                'min(theme(spacing.foo-bar),fit-content,calc(20*calc(40-30)))',
                'min(theme(spacing.foo-bar), fit-content, calc(20 * calc(40 - 30)))',
            ],
            [
                'min(fit-content,calc(100dvh-4rem)-calc(50dvh--2px))',
                'min(fit-content, calc(100dvh - 4rem) - calc(50dvh - -2px))',
            ],
            ['min(-3.4e-2-var(--foo),calc-size(auto))', 'min(-3.4e-2 - var(--foo), calc-size(auto))'],
            [
                'clamp(-10e3-var(--foo),calc-size(max-content),var(--foo)+-10e3)',
                'clamp(-10e3 - var(--foo), calc-size(max-content), var(--foo) + -10e3)',
            ],

            // A negative number immediately after a `,` should not have spaces inserted
            ['clamp(-3px+4px,-3px+4px,-3px+4px)', 'clamp(-3px + 4px, -3px + 4px, -3px + 4px)'],

            // Prevent formatting inside `var()` functions
            ['calc(var(--foo-bar-bar)*2)', 'calc(var(--foo-bar-bar) * 2)'],

            // Prevent formatting inside `env()` functions
            ['calc(env(safe-area-inset-bottom)*2)', 'calc(env(safe-area-inset-bottom) * 2)'],

            // Handle dashed functions that look like known dashed idents
            [
                'fit-content(min(max-content,max(min-content,calc(20px+1em))))',
                'fit-content(min(max-content, max(min-content, calc(20px + 1em))))',
            ],

            // Should format inside `calc()` nested in `env()`
            [
                'env(safe-area-inset-bottom,calc(10px+20px))',
                'env(safe-area-inset-bottom,calc(10px + 20px))',
            ],

            [
                'calc(env(safe-area-inset-bottom,calc(10px+20px))+5px)',
                'calc(env(safe-area-inset-bottom,calc(10px + 20px)) + 5px)',
            ],

            // Prevent formatting keywords
            ['minmax(min-content,25%)', 'minmax(min-content,25%)'],

            // Prevent formatting keywords
            [
                'radial-gradient(calc(1+2)),radial-gradient(calc(1+2))',
                'radial-gradient(calc(1 + 2)),radial-gradient(calc(1 + 2))',
            ],
            ['w-[calc(anchor-size(width)+8px)]', 'w-[calc(anchor-size(width) + 8px)]'],
            ['w-[calc(anchor-size(foo(bar))+8px)]', 'w-[calc(anchor-size(foo(bar)) + 8px)]'],

            [
                '[content-start]_calc(100%-1px)_[content-end]_minmax(1rem,1fr)',
                '[content-start] calc(100% - 1px) [content-end] minmax(1rem,1fr)',
            ],

            // round(…) function
            ['round(1+2,1+3)', 'round(1 + 2, 1 + 3)'],
            ['round(to-zero,1+2,1+3)', 'round(to-zero, 1 + 2, 1 + 3)'],

            // Nested parens in non-math functions don't format their contents
            ['env((safe-area-inset-bottom))', 'env((safe-area-inset-bottom))'],

            // `-infinity` is a keyword and should not have spaces around the `-`
            ['atan(1 + -infinity)', 'atan(1 + -infinity)'],
        ];
    }

    #[Test]
    #[DataProvider('mathOperatorsProvider')]
    public function adds_spaces_around_math_operators(string $input, string $expected): void
    {
        $this->assertEquals($expected, decodeArbitraryValue($input));
    }
}
