<?php

/**
 * Benchmark: AST operations
 *
 * Equivalent to: packages/tailwindcss/src/ast.bench.ts
 */

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../benchmarks/Benchmark.php';

use TailwindPHP\Benchmarks\Benchmark;
use function TailwindPHP\CssParser\parse;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\Ast\cloneAstNode;

$bench = new Benchmark();

$input = <<<'CSS'
@theme {
  --color-primary: #333;
}
@tailwind utilities;
.foo {
  color: red;
  /* comment */
  &:hover {
    color: blue;
    @apply font-bold;
  }
}
CSS;

$ast = parse($input);

$bench->describe('AST to CSS', function ($bench) use ($ast) {
    $bench->bench('toCss', function () use ($ast) {
        toCss($ast);
    });
});

$bench->describe('Cloning AST nodes', function ($bench) use ($ast) {
    $bench->bench('cloneAstNode()', function () use ($ast) {
        array_map('TailwindPHP\\Ast\\cloneAstNode', $ast);
    });
});

$bench->printResults();
