<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\CssFormatter\CssFormatter as Formatter;

/**
 * Tests for CssFormatter.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class CssFormatter extends TestCase
{
    #[Test]
    public function format_returns_empty_string_for_empty_rules(): void
    {
        $this->assertSame('', Formatter::format([]));
    }

    #[Test]
    public function format_formats_single_rule_with_declaration(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                ],
            ],
        ];

        $expected = ".foo {\n  color: red;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_formats_multiple_declarations(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                    ['kind' => 'declaration', 'property' => 'padding', 'value' => '1rem'],
                ],
            ],
        ];

        $expected = ".foo {\n  color: red;\n  padding: 1rem;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_adds_important_to_declarations(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                ],
                'important' => true,
            ],
        ];

        $expected = ".foo {\n  color: red !important;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_handles_declaration_level_important(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => true],
                ],
            ],
        ];

        $expected = ".foo {\n  color: red !important;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_formats_multiple_rules(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                ],
            ],
            [
                'selector' => '.bar',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue'],
                ],
            ],
        ];

        $expected = ".foo {\n  color: red;\n}\n\n.bar {\n  color: blue;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_expands_nested_rules(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                    [
                        'kind' => 'rule',
                        'selector' => '&:hover',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue'],
                        ],
                    ],
                ],
            ],
        ];

        $expected = ".foo {\n  color: red;\n}\n\n.foo:hover {\n  color: blue;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_expands_nested_descendant_selector(): void
    {
        $rules = [
            [
                'selector' => '.parent',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '& .child',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                        ],
                    ],
                ],
            ],
        ];

        $expected = ".parent .child {\n  color: red;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_handles_deeply_nested_rules(): void
    {
        $rules = [
            [
                'selector' => '.a',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '& .b',
                        'nodes' => [
                            [
                                'kind' => 'rule',
                                'selector' => '& .c',
                                'nodes' => [
                                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ];

        $expected = ".a .b .c {\n  color: red;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_skips_empty_nested_rules(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'color', 'value' => 'red'],
                    [
                        'kind' => 'rule',
                        'selector' => '&:hover',
                        'nodes' => [],
                    ],
                ],
            ],
        ];

        $expected = ".foo {\n  color: red;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_propagates_important_to_nested_rules(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    [
                        'kind' => 'rule',
                        'selector' => '&:hover',
                        'nodes' => [
                            ['kind' => 'declaration', 'property' => 'color', 'value' => 'blue'],
                        ],
                    ],
                ],
                'important' => true,
            ],
        ];

        $expected = ".foo:hover {\n  color: blue !important;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_handles_complex_selectors(): void
    {
        $rules = [
            [
                'selector' => '.prose :where(h1):not(:where([class~="not-prose"], [class~="not-prose"] *))',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => 'font-size', 'value' => '2rem'],
                ],
            ],
        ];

        $expected = ".prose :where(h1):not(:where([class~=\"not-prose\"], [class~=\"not-prose\"] *)) {\n  font-size: 2rem;\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }

    #[Test]
    public function format_handles_css_variables(): void
    {
        $rules = [
            [
                'selector' => '.foo',
                'nodes' => [
                    ['kind' => 'declaration', 'property' => '--tw-bg-opacity', 'value' => '1'],
                    ['kind' => 'declaration', 'property' => 'background-color', 'value' => 'rgb(239 68 68 / var(--tw-bg-opacity))'],
                ],
            ],
        ];

        $expected = ".foo {\n  --tw-bg-opacity: 1;\n  background-color: rgb(239 68 68 / var(--tw-bg-opacity));\n}";
        $this->assertSame($expected, Formatter::format($rules));
    }
}
