<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\WalkAction;

use function TailwindPHP\ValueParser\parse;
use function TailwindPHP\ValueParser\toCss;
use function TailwindPHP\walk;

class ValueParserTest extends TestCase
{
    // parse tests

    #[Test]
    public function parses_a_value(): void
    {
        $result = parse('123px');
        $this->assertEquals([['kind' => 'word', 'value' => '123px']], $result);
    }

    #[Test]
    public function parses_a_string_value(): void
    {
        $result = parse("'hello world'");
        $this->assertEquals([['kind' => 'word', 'value' => "'hello world'"]], $result);
    }

    #[Test]
    public function parses_a_list(): void
    {
        $result = parse('hello world');
        $this->assertEquals([
            ['kind' => 'word', 'value' => 'hello'],
            ['kind' => 'separator', 'value' => ' '],
            ['kind' => 'word', 'value' => 'world'],
        ], $result);
    }

    #[Test]
    public function parses_a_string_containing_parentheses(): void
    {
        $result = parse("'hello ( world )'");
        $this->assertEquals([['kind' => 'word', 'value' => "'hello ( world )'"]], $result);
    }

    #[Test]
    public function parses_a_function_with_no_arguments(): void
    {
        $result = parse('theme()');
        $this->assertEquals([['kind' => 'function', 'value' => 'theme', 'nodes' => []]], $result);
    }

    #[Test]
    public function parses_a_function_with_a_single_argument(): void
    {
        $result = parse('theme(foo)');
        $this->assertEquals([
            ['kind' => 'function', 'value' => 'theme', 'nodes' => [
                ['kind' => 'word', 'value' => 'foo'],
            ]],
        ], $result);
    }

    #[Test]
    public function parses_a_function_with_a_single_string_argument(): void
    {
        $result = parse("theme('foo')");
        $this->assertEquals([
            ['kind' => 'function', 'value' => 'theme', 'nodes' => [
                ['kind' => 'word', 'value' => "'foo'"],
            ]],
        ], $result);
    }

    #[Test]
    public function parses_a_function_with_multiple_arguments(): void
    {
        $result = parse('theme(foo, bar)');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => 'theme',
                'nodes' => [
                    ['kind' => 'word', 'value' => 'foo'],
                    ['kind' => 'separator', 'value' => ', '],
                    ['kind' => 'word', 'value' => 'bar'],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_function_with_multiple_arguments_across_lines(): void
    {
        $result = parse("theme(\n\tfoo,\n\tbar\n)");
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => 'theme',
                'nodes' => [
                    ['kind' => 'separator', 'value' => "\n\t"],
                    ['kind' => 'word', 'value' => 'foo'],
                    ['kind' => 'separator', 'value' => ",\n\t"],
                    ['kind' => 'word', 'value' => 'bar'],
                    ['kind' => 'separator', 'value' => "\n"],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_function_with_nested_arguments(): void
    {
        $result = parse('theme(foo, theme(bar))');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => 'theme',
                'nodes' => [
                    ['kind' => 'word', 'value' => 'foo'],
                    ['kind' => 'separator', 'value' => ', '],
                    ['kind' => 'function', 'value' => 'theme', 'nodes' => [
                        ['kind' => 'word', 'value' => 'bar'],
                    ]],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_function_with_nested_arguments_separated_by_slash(): void
    {
        $result = parse('theme(colors.red.500/var(--opacity))');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => 'theme',
                'nodes' => [
                    ['kind' => 'word', 'value' => 'colors.red.500'],
                    ['kind' => 'word', 'value' => '/'],
                    ['kind' => 'function', 'value' => 'var', 'nodes' => [
                        ['kind' => 'word', 'value' => '--opacity'],
                    ]],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function handles_calculations(): void
    {
        $result = parse('calc((1 + 2) * 3)');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => 'calc',
                'nodes' => [
                    [
                        'kind' => 'function',
                        'value' => '',
                        'nodes' => [
                            ['kind' => 'word', 'value' => '1'],
                            ['kind' => 'separator', 'value' => ' '],
                            ['kind' => 'word', 'value' => '+'],
                            ['kind' => 'separator', 'value' => ' '],
                            ['kind' => 'word', 'value' => '2'],
                        ],
                    ],
                    ['kind' => 'separator', 'value' => ' '],
                    ['kind' => 'word', 'value' => '*'],
                    ['kind' => 'separator', 'value' => ' '],
                    ['kind' => 'word', 'value' => '3'],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function handles_media_query_params_with_functions(): void
    {
        $result = parse('(min-width: 600px) and (max-width:theme(colors.red.500)) and (theme(--breakpoint-sm)<width<=theme(--breakpoint-md))');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => '',
                'nodes' => [
                    ['kind' => 'word', 'value' => 'min-width'],
                    ['kind' => 'separator', 'value' => ': '],
                    ['kind' => 'word', 'value' => '600px'],
                ],
            ],
            ['kind' => 'separator', 'value' => ' '],
            ['kind' => 'word', 'value' => 'and'],
            ['kind' => 'separator', 'value' => ' '],
            [
                'kind' => 'function',
                'value' => '',
                'nodes' => [
                    ['kind' => 'word', 'value' => 'max-width'],
                    ['kind' => 'separator', 'value' => ':'],
                    ['kind' => 'function', 'value' => 'theme', 'nodes' => [
                        ['kind' => 'word', 'value' => 'colors.red.500'],
                    ]],
                ],
            ],
            ['kind' => 'separator', 'value' => ' '],
            ['kind' => 'word', 'value' => 'and'],
            ['kind' => 'separator', 'value' => ' '],
            [
                'kind' => 'function',
                'value' => '',
                'nodes' => [
                    ['kind' => 'function', 'value' => 'theme', 'nodes' => [
                        ['kind' => 'word', 'value' => '--breakpoint-sm'],
                    ]],
                    ['kind' => 'separator', 'value' => '<'],
                    ['kind' => 'word', 'value' => 'width'],
                    ['kind' => 'separator', 'value' => '<='],
                    ['kind' => 'function', 'value' => 'theme', 'nodes' => [
                        ['kind' => 'word', 'value' => '--breakpoint-md'],
                    ]],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function does_not_error_when_extra_close_paren_passed(): void
    {
        $result = parse('calc(1 + 2))');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => 'calc',
                'nodes' => [
                    ['kind' => 'word', 'value' => '1'],
                    ['kind' => 'separator', 'value' => ' '],
                    ['kind' => 'word', 'value' => '+'],
                    ['kind' => 'separator', 'value' => ' '],
                    ['kind' => 'word', 'value' => '2'],
                ],
            ],
        ], $result);
    }

    // toCss tests

    #[Test]
    public function pretty_prints_calculations(): void
    {
        $result = toCss(parse('calc((1 + 2) * 3)'));
        $this->assertEquals('calc((1 + 2) * 3)', $result);
    }

    #[Test]
    public function pretty_prints_nested_function_calls(): void
    {
        $result = toCss(parse('theme(foo, theme(bar))'));
        $this->assertEquals('theme(foo, theme(bar))', $result);
    }

    #[Test]
    public function pretty_prints_media_query_params_with_functions(): void
    {
        $result = toCss(parse('(min-width: 600px) and (max-width:theme(colors.red.500))'));
        $this->assertEquals('(min-width: 600px) and (max-width:theme(colors.red.500))', $result);
    }

    #[Test]
    public function preserves_multiple_spaces(): void
    {
        $result = toCss(parse('foo(   bar  )'));
        $this->assertEquals('foo(   bar  )', $result);
    }

    // walk integration test

    #[Test]
    public function can_be_used_to_replace_a_function_call(): void
    {
        $ast = parse('(min-width: 600px) and (max-width: theme(lg))');

        walk($ast, function ($node) {
            if ($node['kind'] === 'function' && $node['value'] === 'theme') {
                return WalkAction::Replace(['kind' => 'word', 'value' => '64rem']);
            }
        });

        $this->assertEquals('(min-width: 600px) and (max-width: 64rem)', toCss($ast));
    }
}
