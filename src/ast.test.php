<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function TailwindPHP\CssParser\parse;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\Ast\styleRule;
use function TailwindPHP\Ast\atRule;
use function TailwindPHP\Ast\decl;
use function TailwindPHP\Ast\comment;
use function TailwindPHP\Ast\context;
use function TailwindPHP\Ast\atRoot;
use function TailwindPHP\Ast\cloneAstNode;

class ast extends TestCase
{
    #[Test]
    public function creates_style_rule_node(): void
    {
        $result = styleRule('.foo', [
            decl('color', 'red'),
        ]);

        $this->assertEquals([
            'kind' => 'rule',
            'selector' => '.foo',
            'nodes' => [
                ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
            ],
        ], $result);
    }

    #[Test]
    public function creates_at_rule_node(): void
    {
        $result = atRule('@media', '(min-width: 768px)', [
            styleRule('.foo', [decl('color', 'red')]),
        ]);

        $this->assertEquals([
            'kind' => 'at-rule',
            'name' => '@media',
            'params' => '(min-width: 768px)',
            'nodes' => [
                [
                    'kind' => 'rule',
                    'selector' => '.foo',
                    'nodes' => [
                        ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function creates_declaration_node(): void
    {
        $result = decl('color', 'red');
        $this->assertEquals([
            'kind' => 'declaration',
            'property' => 'color',
            'value' => 'red',
            'important' => false,
        ], $result);

        $resultImportant = decl('color', 'red', true);
        $this->assertEquals([
            'kind' => 'declaration',
            'property' => 'color',
            'value' => 'red',
            'important' => true,
        ], $resultImportant);
    }

    #[Test]
    public function creates_comment_node(): void
    {
        $result = comment('Hello, world!');
        $this->assertEquals([
            'kind' => 'comment',
            'value' => 'Hello, world!',
        ], $result);
    }

    #[Test]
    public function creates_context_node(): void
    {
        $result = context(['theme' => 'dark'], [
            styleRule('.foo', [decl('color', 'white')]),
        ]);

        $this->assertEquals([
            'kind' => 'context',
            'context' => ['theme' => 'dark'],
            'nodes' => [
                [
                    'kind' => 'rule',
                    'selector' => '.foo',
                    'nodes' => [
                        ['kind' => 'declaration', 'property' => 'color', 'value' => 'white', 'important' => false],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function creates_at_root_node(): void
    {
        $result = atRoot([
            styleRule('.foo', [decl('color', 'red')]),
        ]);

        $this->assertEquals([
            'kind' => 'at-root',
            'nodes' => [
                [
                    'kind' => 'rule',
                    'selector' => '.foo',
                    'nodes' => [
                        ['kind' => 'declaration', 'property' => 'color', 'value' => 'red', 'important' => false],
                    ],
                ],
            ],
        ], $result);
    }

    #[Test]
    public function clones_ast_nodes_deeply(): void
    {
        $original = styleRule('.foo', [
            decl('color', 'red'),
            styleRule('.bar', [
                decl('color', 'blue'),
            ]),
        ]);

        $cloned = cloneAstNode($original);

        // Should be equal
        $this->assertEquals($original, $cloned);

        // Verify deep clone works by modifying clone and checking original is unchanged
        $cloned['selector'] = '.modified';
        $cloned['nodes'][0]['value'] = 'green';

        $this->assertEquals('.foo', $original['selector']);
        $this->assertEquals('red', $original['nodes'][0]['value']);
    }

    #[Test]
    public function clones_at_rule_nodes(): void
    {
        $original = atRule('@media', '(min-width: 768px)', [
            styleRule('.foo', [decl('color', 'red')]),
        ]);

        $cloned = cloneAstNode($original);

        $this->assertEquals($original, $cloned);

        // Verify deep clone
        $cloned['params'] = 'modified';
        $this->assertEquals('(min-width: 768px)', $original['params']);
    }

    #[Test]
    public function clones_context_nodes(): void
    {
        $original = context(['theme' => 'dark'], [
            styleRule('.foo', [decl('color', 'white')]),
        ]);

        $cloned = cloneAstNode($original);

        $this->assertEquals($original, $cloned);

        // Verify deep clone
        $cloned['context']['theme'] = 'light';
        $this->assertEquals('dark', $original['context']['theme']);
    }

    #[Test]
    public function clones_at_root_nodes(): void
    {
        $original = atRoot([
            styleRule('.foo', [decl('color', 'red')]),
        ]);

        $cloned = cloneAstNode($original);

        $this->assertEquals($original, $cloned);

        // Verify deep clone
        $cloned['nodes'][0]['selector'] = '.modified';
        $this->assertEquals('.foo', $original['nodes'][0]['selector']);
    }

    #[Test]
    public function clones_comment_nodes(): void
    {
        $original = comment('Hello, world!');
        $cloned = cloneAstNode($original);

        $this->assertEquals($original, $cloned);

        // Verify deep clone
        $cloned['value'] = 'Modified';
        $this->assertEquals('Hello, world!', $original['value']);
    }

    #[Test]
    public function converts_ast_to_css_string(): void
    {
        $ast = [
            styleRule('.foo', [
                decl('color', 'red'),
            ]),
        ];

        $css = toCss($ast);

        $expected = ".foo {\n  color: red;\n}\n";
        $this->assertEquals($expected, $css);
    }

    #[Test]
    public function converts_nested_ast_to_css_string(): void
    {
        $ast = [
            styleRule('.foo', [
                decl('color', 'red'),
                styleRule('&:hover', [
                    decl('color', 'blue'),
                ]),
            ]),
        ];

        $css = toCss($ast);

        $expected = ".foo {\n  color: red;\n  &:hover {\n    color: blue;\n  }\n}\n";
        $this->assertEquals($expected, $css);
    }

    #[Test]
    public function converts_at_rules_to_css_string(): void
    {
        $ast = [
            atRule('@media', '(min-width: 768px)', [
                styleRule('.foo', [
                    decl('color', 'red'),
                ]),
            ]),
        ];

        $css = toCss($ast);

        $expected = "@media (min-width: 768px) {\n  .foo {\n    color: red;\n  }\n}\n";
        $this->assertEquals($expected, $css);
    }

    #[Test]
    public function converts_at_rules_without_nodes_to_css_with_semicolon(): void
    {
        $ast = [
            atRule('@charset', '"UTF-8"'),
            atRule('@import', "url('https://example.com/styles.css')"),
        ];

        $css = toCss($ast);

        $expected = "@charset \"UTF-8\";\n@import url('https://example.com/styles.css');\n";
        $this->assertEquals($expected, $css);
    }

    #[Test]
    public function converts_important_declarations_to_css(): void
    {
        $ast = [
            styleRule('.foo', [
                decl('color', 'red', true),
            ]),
        ];

        $css = toCss($ast);

        $expected = ".foo {\n  color: red !important;\n}\n";
        $this->assertEquals($expected, $css);
    }

    #[Test]
    public function converts_comments_to_css(): void
    {
        $ast = [
            comment('! License information'),
            styleRule('.foo', [
                decl('color', 'red'),
            ]),
        ];

        $css = toCss($ast);

        $expected = "/*! License information*/\n.foo {\n  color: red;\n}\n";
        $this->assertEquals($expected, $css);
    }

    #[Test]
    public function pretty_prints_parsed_ast(): void
    {
        $parsed = parse('.foo{color:red;&:hover{color:blue;}}');
        $css = toCss($parsed);

        $expected = ".foo {\n  color: red;\n  &:hover {\n    color: blue;\n  }\n}\n";
        $this->assertEquals($expected, $css);
    }
}
