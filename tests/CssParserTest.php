<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use TailwindPHP\CssSyntaxError;

use function TailwindPHP\parse;
use function TailwindPHP\rule;
use function TailwindPHP\decl;
use function TailwindPHP\comment;

class CssParserTest extends TestCase
{
    // COMMENTS

    #[Test]
    public function parses_a_comment_and_ignores_it(): void
    {
        $result = parse('/*Hello, world!*/');
        $this->assertEquals([], $result);
    }

    #[Test]
    public function parses_a_comment_with_an_escaped_ending_and_ignores_it(): void
    {
        $result = parse('/*Hello, \*\/ world!*/');
        $this->assertEquals([], $result);
    }

    #[Test]
    public function parses_a_comment_inside_of_a_selector_and_ignores_it(): void
    {
        $result = parse('.foo { /*Example comment*/ }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function removes_comments_in_between_selectors_while_maintaining_correct_whitespace(): void
    {
        $result = parse('.foo/*.bar*/.baz { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo.baz', 'nodes' => []],
        ], $result);

        $result = parse('.foo/*.bar*//*.baz*/.qux { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo.qux', 'nodes' => []],
        ], $result);

        $result = parse('.foo/*.bar*/ /*.baz*/.qux { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo .qux', 'nodes' => []],
        ], $result);

        $result = parse('.foo /*.bar*/.baz { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo .baz', 'nodes' => []],
        ], $result);

        $result = parse('.foo/*.bar*/ .baz { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo .baz', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function collects_license_comments(): void
    {
        $result = parse('/*! License #1 */');
        $this->assertEquals([
            ['kind' => 'comment', 'value' => '! License #1 '],
        ], $result);
    }

    #[Test]
    public function handles_comments_before_element_selectors(): void
    {
        $result = parse('.dark /* comment */p { color: black; }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.dark p',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'black', 'important' => false],
                ],
            ],
        ], $result);
    }

    // DECLARATIONS

    #[Test]
    public function parses_a_simple_declaration(): void
    {
        $result = parse('color: red;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_declarations_with_strings(): void
    {
        $result = parse("content: 'Hello, world!';");
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'content', 'value' => "'Hello, world!'", 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_declarations_with_nested_strings(): void
    {
        $result = parse('content: \'Good, "monday", morning!\';');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'content', 'value' => '\'Good, "monday", morning!\'', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_declarations_with_nested_strings_that_are_not_balanced(): void
    {
        $result = parse('content: "It\'s a beautiful day!";');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'content', 'value' => '"It\'s a beautiful day!"', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_declarations_with_strings_and_escaped_string_endings(): void
    {
        $result = parse("content: 'These are not the end \"\\' of the string';");
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'content', 'value' => "'These are not the end \"\\' of the string'", 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_declarations_with_important(): void
    {
        $result = parse('width: 123px !important;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'width', 'value' => '123px', 'important' => true],
        ], $result);
    }

    #[Test]
    public function parses_declarations_with_important_and_trailing_comment(): void
    {
        $result = parse('width: 123px !important /* Very important */;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'width', 'value' => '123px', 'important' => true],
        ], $result);
    }

    // CUSTOM PROPERTIES

    #[Test]
    public function parses_a_custom_property(): void
    {
        $result = parse('--foo: bar;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => 'bar', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_a_minified_custom_property(): void
    {
        $result = parse(':root{--foo:bar;}');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => ':root',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => '--foo', 'value' => 'bar', 'important' => false],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_minified_custom_property_with_no_semicolon(): void
    {
        $result = parse(':root{--foo:bar}');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => ':root',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => '--foo', 'value' => 'bar', 'important' => false],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_missing_ending_semicolon(): void
    {
        $result = parse('--foo: bar');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => 'bar', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_missing_ending_semicolon_and_important(): void
    {
        $result = parse('--foo: bar !important');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => 'bar', 'important' => true],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_embedded_programming_language(): void
    {
        $result = parse('--foo: if(x > 5) this.width = 10;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => 'if(x > 5) this.width = 10', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_empty_block_as_value(): void
    {
        $result = parse('--foo: {};');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => '{}', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_empty_value(): void
    {
        $result = parse('--foo:;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => '', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_space_value(): void
    {
        $result = parse('--foo: ;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => '', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_a_custom_property_with_escaped_characters(): void
    {
        $result = parse('--foo: This is not the end \\;, but this is;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => 'This is not the end \\;, but this is', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_empty_custom_properties(): void
    {
        $result = parse('--foo: ;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => '', 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_custom_properties_with_important(): void
    {
        $result = parse('--foo: bar !important;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => 'bar', 'important' => true],
        ], $result);
    }

    #[Test]
    public function parses_custom_properties_with_data_url_value(): void
    {
        $result = parse("--foo: 'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ==';");
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => '--foo', 'value' => "'data:text/plain;base64,SGVsbG8sIFdvcmxkIQ=='", 'important' => false],
        ], $result);
    }

    #[Test]
    public function parses_multiple_declarations(): void
    {
        $result = parse('color: red; background-color: blue;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
            ['kind' => 'declaration', 'property' => 'background-color', 'value' => 'blue', 'important' => false],
        ], $result);
    }

    #[Test]
    public function correctly_parses_comments_with_colon_inside_them(): void
    {
        $result = parse('color/* color: #f00; */: red;');
        $this->assertEquals([
            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
        ], $result);
    }

    // SELECTORS

    #[Test]
    public function parses_a_simple_selector(): void
    {
        $result = parse('.foo { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function parses_selectors_with_escaped_characters(): void
    {
        $result = parse('.hover\\:foo:hover { }');
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.hover\\:foo:hover', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function parses_multiple_simple_selectors(): void
    {
        $result = parse(".foo,\n.bar { }");
        $this->assertEquals([
            ['kind' => 'rule', 'selector' => '.foo, .bar', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function parses_multiple_declarations_inside_of_a_selector(): void
    {
        $result = parse('.foo { color: red; font-size: 16px; }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    ['kind' => 'declaration', 'property' => 'font-size', 'value' => '16px', 'important' => false],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_rules_with_declarations_that_end_with_missing_semicolon(): void
    {
        $result = parse(".foo { color: red; font-size: 16px }");
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    ['kind' => 'declaration', 'property' => 'font-size', 'value' => '16px', 'important' => false],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_rules_with_declarations_that_end_with_missing_semicolon_and_important(): void
    {
        $result = parse(".foo { color: red; font-size: 16px !important }");
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    ['kind' => 'declaration', 'property' => 'font-size', 'value' => '16px', 'important' => true],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_multiline_selector(): void
    {
        $result = parse(".foo,\n.bar,\n.baz\n{\ncolor:red;\n}");
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo, .bar, .baz',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                ],
            ],
        ], $result);
    }

    // AT-RULES

    #[Test]
    public function parses_an_at_rule_without_a_block(): void
    {
        $result = parse('@charset "UTF-8";');
        $this->assertEquals([
            ['kind' => 'at-rule', 'name' => '@charset', 'params' => '"UTF-8"', 'nodes' => []],
        ], $result);
    }

    public static function whitespaceProvider(): array
    {
        return [
            [' '],
            ['  '],
            ["\t"],
            [" \t"],
            ["\t "],
            ["\t\t"],
            ["\n"],
            [" \n"],
            ["\n "],
            ["\n\n"],
            ["\r\n"],
            [" \r\n"],
            ["\r\n "],
        ];
    }

    #[Test]
    #[DataProvider('whitespaceProvider')]
    public function parses_at_rule_with_whitespace_in_params(string $whitespace): void
    {
        $result = parse("@apply{$whitespace}bg-red-500;");
        $this->assertEquals([
            ['kind' => 'at-rule', 'name' => '@apply', 'params' => 'bg-red-500', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function parses_an_at_rule_without_a_block_or_semicolon(): void
    {
        $result = parse('@tailwind utilities');
        $this->assertEquals([
            ['kind' => 'at-rule', 'name' => '@tailwind', 'params' => 'utilities', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function parses_an_at_rule_without_a_block_or_semicolon_when_last_rule_in_block(): void
    {
        $result = parse("@layer utilities {\n@tailwind utilities\n}");
        $this->assertEquals([
            [
                'kind' => 'at-rule',
                'name' => '@layer',
                'params' => 'utilities',
                'nodes' => [
                    ['kind' => 'at-rule', 'name' => '@tailwind', 'params' => 'utilities', 'nodes' => []],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_a_nested_at_rule_without_a_block(): void
    {
        $result = parse('@layer utilities { @charset "UTF-8"; }');
        $this->assertEquals([
            [
                'kind' => 'at-rule',
                'name' => '@layer',
                'params' => 'utilities',
                'nodes' => [
                    ['kind' => 'at-rule', 'name' => '@charset', 'params' => '"UTF-8"', 'nodes' => []],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_custom_at_rules_without_a_block(): void
    {
        $result = parse('@tailwind; @tailwind base;');
        $this->assertEquals([
            ['kind' => 'at-rule', 'name' => '@tailwind', 'params' => '', 'nodes' => []],
            ['kind' => 'at-rule', 'name' => '@tailwind', 'params' => 'base', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function parses_nested_media_queries(): void
    {
        $css = '@media (width >= 600px) { .foo { color: red; @media (width >= 800px) { color: blue; } } }';
        $result = parse($css);
        $this->assertEquals([
            [
                'kind' => 'at-rule',
                'name' => '@media',
                'params' => '(width >= 600px)',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '.foo',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                            [
                                'kind' => 'at-rule',
                                'name' => '@media',
                                'params' => '(width >= 800px)',
                                'nodes' => [
                                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue', 'important' => false],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    // NESTING

    #[Test]
    public function parses_nested_rules(): void
    {
        $result = parse('.foo { .bar { .baz { color: red; } } }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '.bar',
                        'nodes' => [
                            [
                                'kind' => 'rule',
                                'selector' => '.baz',
                                'nodes' => [
                                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_nested_selector_with_ampersand(): void
    {
        $result = parse('.foo { color: red; &:hover { color: blue; } }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    [
                        'kind' => 'rule',
                        'selector' => '&:hover',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue', 'important' => false],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_nested_sibling_selectors(): void
    {
        $result = parse('.foo { .bar { color: red; } .baz { color: blue; } }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '.bar',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                        ],
                    ],
                    [
                        'kind' => 'rule',
                        'selector' => '.baz',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue', 'important' => false],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    // COMPLEX

    #[Test]
    public function parses_complex_examples(): void
    {
        $result = parse('@custom \\{ { foo: bar; }');
        $this->assertEquals([
            [
                'kind' => 'at-rule',
                'name' => '@custom',
                'params' => '\\{',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'foo', 'value' => 'bar', 'important' => false],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function parses_minified_nested_css(): void
    {
        $result = parse('.foo{color:red;@media(width>=600px){.bar{color:blue;font-weight:bold}}}');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    [
                        'kind' => 'at-rule',
                        'name' => '@media',
                        'params' => '(width>=600px)',
                        'nodes' => [
                            [
                                'kind' => 'rule',
                                'selector' => '.bar',
                                'nodes' => [
                                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue', 'important' => false],
                                    ['kind' => 'declaration', 'property' => 'font-weight', 'value' => 'bold', 'important' => false],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function ignores_everything_inside_comments(): void
    {
        $result = parse('.foo:has(.bar /* instead \\*\\/ of .baz { */) { color: red; }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo:has(.bar )',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function ignores_consecutive_semicolons(): void
    {
        $result = parse(';;;');
        $this->assertEquals([], $result);
    }

    #[Test]
    public function ignores_semicolons_after_at_rule_with_body(): void
    {
        $result = parse('@plugin "foo" {} ;');
        $this->assertEquals([
            ['kind' => 'at-rule', 'name' => '@plugin', 'params' => '"foo"', 'nodes' => []],
        ], $result);
    }

    #[Test]
    public function ignores_consecutive_semicolons_after_declaration(): void
    {
        $result = parse('.foo { color: red;;; }');
        $this->assertEquals([
            [
                'kind' => 'rule',
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                ],
            ],
        ], $result);
    }

    // ERRORS

    #[Test]
    public function errors_when_curly_brackets_are_unbalanced_opening(): void
    {
        $this->expectException(CssSyntaxError::class);
        $this->expectExceptionMessage('Missing opening {');
        parse('.foo { color: red; } .bar color: blue; }');
    }

    #[Test]
    public function errors_when_curly_brackets_are_unbalanced_closing(): void
    {
        $this->expectException(CssSyntaxError::class);
        $this->expectExceptionMessage('Missing closing }');
        parse('.foo { color: red; } .bar { color: blue;');
    }

    #[Test]
    public function errors_when_unterminated_string_is_used(): void
    {
        $this->expectException(CssSyntaxError::class);
        $this->expectExceptionMessage('Unterminated string');
        parse(".foo { content: \"Hello world!\n }");
    }

    #[Test]
    public function errors_when_incomplete_custom_properties_are_used(): void
    {
        $this->expectException(CssSyntaxError::class);
        $this->expectExceptionMessage('Invalid custom property, expected a value');
        parse('--foo');
    }

    #[Test]
    public function errors_when_incomplete_custom_properties_are_used_inside_rules(): void
    {
        $this->expectException(CssSyntaxError::class);
        $this->expectExceptionMessage('Invalid custom property, expected a value');
        parse('.foo { --bar }');
    }

    #[Test]
    public function errors_when_declaration_is_incomplete(): void
    {
        $this->expectException(CssSyntaxError::class);
        $this->expectExceptionMessage('Invalid declaration');
        parse('.foo { bar }');
    }

    #[Test]
    public function ignores_bom_at_beginning_of_file(): void
    {
        $result = parse("\u{FEFF}@reference 'tailwindcss';");
        $this->assertEquals([
            ['kind' => 'at-rule', 'name' => '@reference', 'params' => "'tailwindcss'", 'nodes' => []],
        ], $result);
    }
}
