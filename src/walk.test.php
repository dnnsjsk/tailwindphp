<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\Ast\decl;
use function TailwindPHP\Ast\styleRule;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\Walk\walk;

use TailwindPHP\Walk\WalkAction;

class walk extends TestCase
{
    private function createAst(): array
    {
        return [
            [
                'kind' => 'a',
                'nodes' => [
                    ['kind' => 'b', 'nodes' => [['kind' => 'c']]],
                    ['kind' => 'd', 'nodes' => [['kind' => 'e', 'nodes' => [['kind' => 'f']]]]],
                    ['kind' => 'g', 'nodes' => [['kind' => 'h']]],
                ],
            ],
            ['kind' => 'i'],
        ];
    }

    // ENTER (function) tests

    #[Test]
    public function visits_all_nodes_in_an_ast(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
        });

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'], $visited);
    }

    #[Test]
    public function visits_all_nodes_and_calculates_path(): void
    {
        $ast = $this->createAst();

        $paths = [];
        walk($ast, function ($node, $ctx) use (&$paths) {
            $path = array_map(fn ($n) => $n['kind'], $ctx->path());
            if (count($path) === 0) {
                array_unshift($path, 'ø');
            }
            $path[] = $node['kind'];
            $paths[] = implode(' → ', $path);
        });

        $expected = [
            'ø → a',
            'a → b',
            'a → b → c',
            'a → d',
            'a → d → e',
            'a → d → e → f',
            'a → g',
            'a → g → h',
            'ø → i',
        ];
        $this->assertEquals($expected, $paths);
    }

    #[Test]
    public function skips_a_node_children_first_node(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'b') {
                return WalkAction::Skip;
            }
        });

        $this->assertEquals(['a', 'b', 'd', 'e', 'f', 'g', 'h', 'i'], $visited);
    }

    #[Test]
    public function skips_a_node_children_middle_node(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'd') {
                return WalkAction::Skip;
            }
        });

        $this->assertEquals(['a', 'b', 'c', 'd', 'g', 'h', 'i'], $visited);
    }

    #[Test]
    public function skips_a_node_children_last_node(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'g') {
                return WalkAction::Skip;
            }
        });

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'i'], $visited);
    }

    #[Test]
    public function stops_entirely(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'd') {
                return WalkAction::Stop;
            }
        });

        $this->assertEquals(['a', 'b', 'c', 'd'], $visited);
    }

    #[Test]
    public function replaces_a_node_and_visits_replacements(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'd') {
                return WalkAction::Replace([
                    ['kind' => 'e1', 'nodes' => [['kind' => 'f1']]],
                    ['kind' => 'e2', 'nodes' => [['kind' => 'f2']]],
                ]);
            }
        });

        $this->assertEquals(['a', 'b', 'c', 'd', 'e1', 'f1', 'e2', 'f2', 'g', 'h', 'i'], $visited);
    }

    #[Test]
    public function replaces_a_node_and_skips_replacements(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'd') {
                return WalkAction::ReplaceSkip([
                    ['kind' => 'e1', 'nodes' => [['kind' => 'f1']]],
                    ['kind' => 'e2', 'nodes' => [['kind' => 'f2']]],
                ]);
            }
        });

        $this->assertEquals(['a', 'b', 'c', 'd', 'g', 'h', 'i'], $visited);

        // Walk the mutated AST to verify the replacement happened
        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
        });

        $this->assertEquals(['a', 'b', 'c', 'e1', 'f1', 'e2', 'f2', 'g', 'h', 'i'], $visited);
    }

    #[Test]
    public function replaces_a_node_and_stops(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
            if ($node['kind'] === 'd') {
                return WalkAction::ReplaceStop([
                    ['kind' => 'e1', 'nodes' => [['kind' => 'f1']]],
                    ['kind' => 'e2', 'nodes' => [['kind' => 'f2']]],
                ]);
            }
        });

        $this->assertEquals(['a', 'b', 'c', 'd'], $visited);

        // Walk the mutated AST to verify the replacement happened
        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
        });

        $this->assertEquals(['a', 'b', 'c', 'e1', 'f1', 'e2', 'f2', 'g', 'h', 'i'], $visited);
    }

    // ENTER (object) tests

    #[Test]
    public function visits_all_nodes_with_enter_hook(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, [
            'enter' => function ($node) use (&$visited) {
                $visited[] = $node['kind'];
            },
        ]);

        $this->assertEquals(['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i'], $visited);
    }

    // EXIT tests

    #[Test]
    public function visits_all_nodes_on_exit(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, [
            'exit' => function ($node) use (&$visited) {
                $visited[] = $node['kind'];
            },
        ]);

        $this->assertEquals(['c', 'b', 'f', 'e', 'd', 'h', 'g', 'a', 'i'], $visited);
    }

    #[Test]
    public function stops_on_exit(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, [
            'exit' => function ($node) use (&$visited) {
                $visited[] = $node['kind'];
                if ($node['kind'] === 'd') {
                    return WalkAction::Stop;
                }
            },
        ]);

        $this->assertEquals(['c', 'b', 'f', 'e', 'd'], $visited);
    }

    #[Test]
    public function replaces_on_exit(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, [
            'exit' => function ($node) use (&$visited) {
                $visited[] = $node['kind'];
                if ($node['kind'] === 'd') {
                    return WalkAction::Replace([
                        ['kind' => 'e1', 'nodes' => [['kind' => 'f1']]],
                        ['kind' => 'e2', 'nodes' => [['kind' => 'f2']]],
                    ]);
                }
            },
        ]);

        $this->assertEquals(['c', 'b', 'f', 'e', 'd', 'h', 'g', 'a', 'i'], $visited);

        // Walk the mutated AST to verify the replacement happened
        $visited = [];
        walk($ast, function ($node) use (&$visited) {
            $visited[] = $node['kind'];
        });

        $this->assertEquals(['a', 'b', 'c', 'e1', 'f1', 'e2', 'f2', 'g', 'h', 'i'], $visited);
    }

    // ENTER & EXIT tests

    #[Test]
    public function visits_all_nodes_with_enter_and_exit(): void
    {
        $ast = $this->createAst();

        $visited = [];
        walk($ast, [
            'enter' => function ($node, $ctx) use (&$visited) {
                $visited[] = str_repeat('  ', $ctx->depth) . " Enter({$node['kind']})";
            },
            'exit' => function ($node, $ctx) use (&$visited) {
                $visited[] = str_repeat('  ', $ctx->depth) . " Exit({$node['kind']})";
            },
        ]);

        $expected = [
            ' Enter(a)',
            '   Enter(b)',
            '     Enter(c)',
            '     Exit(c)',
            '   Exit(b)',
            '   Enter(d)',
            '     Enter(e)',
            '       Enter(f)',
            '       Exit(f)',
            '     Exit(e)',
            '   Exit(d)',
            '   Enter(g)',
            '     Enter(h)',
            '     Exit(h)',
            '   Exit(g)',
            ' Exit(a)',
            ' Enter(i)',
            ' Exit(i)',
        ];
        $this->assertEquals($expected, $visited);
    }

    // Real world use case test

    #[Test]
    public function real_world_use_case(): void
    {
        $ast = [
            styleRule('.example', [
                decl('margin-top', '12px'),
                decl('padding', '8px'),
                decl('margin', '16px 18px'),
                decl('colors', 'red'),
            ]),
        ];

        walk($ast, [
            'enter' => function (&$node) {
                // Expand `margin` shorthand into multiple properties
                if ($node['kind'] === 'declaration' && $node['property'] === 'margin' && $node['value']) {
                    $parts = explode(' ', $node['value']);
                    $y = $parts[0];
                    $x = $parts[1] ?? $parts[0];

                    return WalkAction::Replace([
                        decl('margin-top', $y),
                        decl('margin-bottom', $y),
                        decl('margin-left', $x),
                        decl('margin-right', $x),
                    ]);
                }

                // These properties should not be uppercased, so skip them
                elseif ($node['kind'] === 'declaration' && $node['property'] === 'colors' && $node['value']) {
                    return WalkAction::ReplaceSkip([
                        decl('color', $node['value']),
                        decl('background-color', $node['value']),
                        decl('border-color', $node['value']),
                    ]);
                }

                // Make all properties uppercase
                elseif ($node['kind'] === 'declaration') {
                    $node['property'] = strtoupper($node['property']);
                }
            },
            'exit' => function (&$node) {
                // Sort declarations alphabetically within a rule (case-insensitive like JS localeCompare)
                if ($node['kind'] === 'rule') {
                    usort($node['nodes'], function ($a, $z) {
                        if ($a['kind'] === 'declaration' && $z['kind'] === 'declaration') {
                            return strcasecmp($a['property'], $z['property']);
                        }

                        return 0;
                    });
                }
            },
        ]);

        $css = toCss($ast);

        $expected = ".example {\n  background-color: red;\n  border-color: red;\n  color: red;\n  MARGIN-BOTTOM: 16px;\n  MARGIN-LEFT: 18px;\n  MARGIN-RIGHT: 18px;\n  MARGIN-TOP: 12px;\n  MARGIN-TOP: 16px;\n  PADDING: 8px;\n}\n";

        $this->assertEquals($expected, $css);
    }
}
