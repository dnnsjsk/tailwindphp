<?php

declare(strict_types=1);

namespace TailwindPHP\Tests\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;

use function TailwindPHP\Utils\expand;

class BraceExpansionTest extends TestCase
{
    public static function expansionProvider(): array
    {
        return [
            ['a/b/c', ['a/b/c']],

            // Groups
            ['a/{x,y,z}/b', ['a/x/b', 'a/y/b', 'a/z/b']],
            ['{a,b}/{x,y}', ['a/x', 'a/y', 'b/x', 'b/y']],
            ['{{xs,sm,md,lg}:,}hidden', ['xs:hidden', 'sm:hidden', 'md:hidden', 'lg:hidden', 'hidden']],

            // Numeric ranges
            ['a/{0..5}/b', ['a/0/b', 'a/1/b', 'a/2/b', 'a/3/b', 'a/4/b', 'a/5/b']],
            ['a/{-5..0}/b', ['a/-5/b', 'a/-4/b', 'a/-3/b', 'a/-2/b', 'a/-1/b', 'a/0/b']],
            ['a/{0..-5}/b', ['a/0/b', 'a/-1/b', 'a/-2/b', 'a/-3/b', 'a/-4/b', 'a/-5/b']],
            ['a/{0..10..5}/b', ['a/0/b', 'a/5/b', 'a/10/b']],

            // Numeric range with step
            ['a/{0..5..2}/b', ['a/0/b', 'a/2/b', 'a/4/b']],

            // Nested braces
            ['a{b,c,/{x,y}}/e', ['ab/e', 'ac/e', 'a/x/e', 'a/y/e']],

            // Should not try to expand ranges with decimals
            ['{1.1..2.2}', ['1.1..2.2']],
        ];
    }

    #[Test]
    #[DataProvider('expansionProvider')]
    public function expands_patterns_correctly(string $input, array $expected): void
    {
        $result = expand($input);
        sort($result);
        sort($expected);
        $this->assertEquals($expected, $result);
    }

    #[Test]
    public function throws_on_unbalanced_braces(): void
    {
        $this->expectException(\Exception::class);
        expand('a{b,c{d,e},{f,g}h}x{y,z');
    }

    #[Test]
    public function throws_when_step_is_zero(): void
    {
        $this->expectException(\Exception::class);
        expand('a{0..5..0}/b');
    }
}
