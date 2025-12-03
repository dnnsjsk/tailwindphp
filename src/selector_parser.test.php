<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\SelectorParser\parse;
use function TailwindPHP\SelectorParser\toCss;
use function TailwindPHP\Walk\walk;

use TailwindPHP\Walk\WalkAction;

class selector_parser extends TestCase
{
    // parse tests

    #[Test]
    public function parses_a_simple_selector(): void
    {
        $result = parse('.foo');
        $this->assertEquals([['kind' => 'selector', 'value' => '.foo']], $result);
    }

    #[Test]
    public function parses_a_compound_selector(): void
    {
        $result = parse('.foo.bar:hover#id');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '.foo'],
            ['kind' => 'selector', 'value' => '.bar'],
            ['kind' => 'selector', 'value' => ':hover'],
            ['kind' => 'selector', 'value' => '#id'],
        ], $result);
    }

    #[Test]
    public function parses_a_selector_list(): void
    {
        $result = parse('.foo,.bar');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '.foo'],
            ['kind' => 'separator', 'value' => ','],
            ['kind' => 'selector', 'value' => '.bar'],
        ], $result);
    }

    #[Test]
    public function combines_everything_within_attribute_selectors(): void
    {
        $result = parse('.foo[bar="baz"]');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '.foo'],
            ['kind' => 'selector', 'value' => '[bar="baz"]'],
        ], $result);
    }

    #[Test]
    public function parses_functions(): void
    {
        $result = parse('.foo:hover:not(.bar:focus)');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '.foo'],
            ['kind' => 'selector', 'value' => ':hover'],
            [
                'kind' => 'function',
                'value' => ':not',
                'nodes' => [
                    ['kind' => 'selector', 'value' => '.bar'],
                    ['kind' => 'selector', 'value' => ':focus'],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function handles_next_children_combinator(): void
    {
        $result = parse('.foo + p');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '.foo'],
            ['kind' => 'combinator', 'value' => ' + '],
            ['kind' => 'selector', 'value' => 'p'],
        ], $result);
    }

    #[Test]
    public function handles_escaped_characters(): void
    {
        $result = parse('foo\\.bar');
        $this->assertEquals([['kind' => 'selector', 'value' => 'foo\\.bar']], $result);
    }

    #[Test]
    public function parses_nth_child(): void
    {
        $result = parse(':nth-child(n+1)');
        $this->assertEquals([
            [
                'kind' => 'function',
                'value' => ':nth-child',
                'nodes' => [
                    ['kind' => 'value', 'value' => 'n+1'],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_has_with_nested_nth_child(): void
    {
        $result = parse('&:has(.child:nth-child(2))');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '&'],
            [
                'kind' => 'function',
                'value' => ':has',
                'nodes' => [
                    ['kind' => 'selector', 'value' => '.child'],
                    [
                        'kind' => 'function',
                        'value' => ':nth-child',
                        'nodes' => [
                            ['kind' => 'value', 'value' => '2'],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_has_with_nested_nth_child_without_selector(): void
    {
        $result = parse('&:has(:nth-child(2))');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '&'],
            [
                'kind' => 'function',
                'value' => ':has',
                'nodes' => [
                    [
                        'kind' => 'function',
                        'value' => ':nth-child',
                        'nodes' => [
                            ['kind' => 'value', 'value' => '2'],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_nesting_selector_before_attribute_selector(): void
    {
        $result = parse('&[data-foo]');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '&'],
            ['kind' => 'selector', 'value' => '[data-foo]'],
        ], $result);
    }

    #[Test]
    public function parses_nesting_selector_after_attribute_selector(): void
    {
        $result = parse('[data-foo]&');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '[data-foo]'],
            ['kind' => 'selector', 'value' => '&'],
        ], $result);
    }

    #[Test]
    public function parses_universal_selector_before_attribute_selector(): void
    {
        $result = parse('*[data-foo]');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '*'],
            ['kind' => 'selector', 'value' => '[data-foo]'],
        ], $result);
    }

    #[Test]
    public function parses_universal_selector_after_attribute_selector(): void
    {
        $result = parse('[data-foo]*');
        $this->assertEquals([
            ['kind' => 'selector', 'value' => '[data-foo]'],
            ['kind' => 'selector', 'value' => '*'],
        ], $result);
    }

    // toCss tests

    #[Test]
    public function prints_a_simple_selector(): void
    {
        $result = toCss(parse('.foo'));
        $this->assertEquals('.foo', $result);
    }

    #[Test]
    public function prints_a_compound_selector(): void
    {
        $result = toCss(parse('.foo.bar:hover#id'));
        $this->assertEquals('.foo.bar:hover#id', $result);
    }

    #[Test]
    public function prints_a_selector_list(): void
    {
        $result = toCss(parse('.foo,.bar'));
        $this->assertEquals('.foo,.bar', $result);
    }

    #[Test]
    public function prints_an_attribute_selector(): void
    {
        $result = toCss(parse('.foo[bar="baz"]'));
        $this->assertEquals('.foo[bar="baz"]', $result);
    }

    #[Test]
    public function prints_a_function(): void
    {
        $result = toCss(parse('.foo:hover:not(.bar:focus)'));
        $this->assertEquals('.foo:hover:not(.bar:focus)', $result);
    }

    #[Test]
    public function prints_escaped_characters(): void
    {
        $result = toCss(parse('foo\\.bar'));
        $this->assertEquals('foo\\.bar', $result);
    }

    #[Test]
    public function prints_nth_child(): void
    {
        $result = toCss(parse(':nth-child(n+1)'));
        $this->assertEquals(':nth-child(n+1)', $result);
    }

    // walk integration test

    #[Test]
    public function can_be_used_to_replace_a_function_call(): void
    {
        $ast = parse('.foo:hover:not(.bar:focus)');

        walk($ast, function ($node) {
            if ($node['kind'] === 'function' && $node['value'] === ':not') {
                return WalkAction::Replace(['kind' => 'selector', 'value' => '.inverted-bar']);
            }
        });

        $this->assertEquals('.foo:hover.inverted-bar', toCss($ast));
    }
}
