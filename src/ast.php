<?php

declare(strict_types=1);

namespace TailwindPHP;

/**
 * AST node types and builder functions for TailwindPHP.
 *
 * Port of: packages/tailwindcss/src/ast.ts
 */

const AT_SIGN = 0x40;

/**
 * @typedef array{kind: 'rule', selector: string, nodes: array<AstNode>} StyleRule
 * @typedef array{kind: 'at-rule', name: string, params: string, nodes: array<AstNode>} AtRule
 * @typedef array{kind: 'declaration', property: string, value: string|null, important: bool} Declaration
 * @typedef array{kind: 'comment', value: string} Comment
 * @typedef array{kind: 'context', context: array<string, string|bool>, nodes: array<AstNode>} Context
 * @typedef array{kind: 'at-root', nodes: array<AstNode>} AtRoot
 * @typedef StyleRule|AtRule|Declaration|Comment|Context|AtRoot AstNode
 */

/**
 * Create a style rule node.
 *
 * @param string $selector
 * @param array<AstNode> $nodes
 * @return array{kind: 'rule', selector: string, nodes: array}
 */
function styleRule(string $selector, array $nodes = []): array
{
    return [
        'kind' => 'rule',
        'selector' => $selector,
        'nodes' => $nodes,
    ];
}

/**
 * Create an at-rule node.
 *
 * @param string $name
 * @param string $params
 * @param array<AstNode> $nodes
 * @return array{kind: 'at-rule', name: string, params: string, nodes: array}
 */
function atRule(string $name, string $params = '', array $nodes = []): array
{
    return [
        'kind' => 'at-rule',
        'name' => $name,
        'params' => $params,
        'nodes' => $nodes,
    ];
}

/**
 * Create a rule node (either style rule or at-rule based on selector).
 *
 * @param string $selector
 * @param array<AstNode> $nodes
 * @return array
 */
function rule(string $selector, array $nodes = []): array
{
    if (strlen($selector) > 0 && ord($selector[0]) === AT_SIGN) {
        return parseAtRule($selector, $nodes);
    }

    return styleRule($selector, $nodes);
}

/**
 * Create a declaration node.
 *
 * @param string $property
 * @param string|null $value
 * @param bool $important
 * @return array{kind: 'declaration', property: string, value: string|null, important: bool}
 */
function decl(string $property, ?string $value, bool $important = false): array
{
    return [
        'kind' => 'declaration',
        'property' => $property,
        'value' => $value,
        'important' => $important,
    ];
}

/**
 * Create a comment node.
 *
 * @param string $value
 * @return array{kind: 'comment', value: string}
 */
function comment(string $value): array
{
    return [
        'kind' => 'comment',
        'value' => $value,
    ];
}

/**
 * Create a context node.
 *
 * @param array<string, string|bool> $context
 * @param array<AstNode> $nodes
 * @return array{kind: 'context', context: array, nodes: array}
 */
function context(array $context, array $nodes): array
{
    return [
        'kind' => 'context',
        'context' => $context,
        'nodes' => $nodes,
    ];
}

/**
 * Create an at-root node.
 *
 * @param array<AstNode> $nodes
 * @return array{kind: 'at-root', nodes: array}
 */
function atRoot(array $nodes): array
{
    return [
        'kind' => 'at-root',
        'nodes' => $nodes,
    ];
}

/**
 * Deep clone an AST node.
 *
 * @param array $node
 * @return array
 */
function cloneAstNode(array $node): array
{
    switch ($node['kind']) {
        case 'rule':
            return [
                'kind' => $node['kind'],
                'selector' => $node['selector'],
                'nodes' => array_map('TailwindPHP\\cloneAstNode', $node['nodes']),
            ];

        case 'at-rule':
            return [
                'kind' => $node['kind'],
                'name' => $node['name'],
                'params' => $node['params'],
                'nodes' => array_map('TailwindPHP\\cloneAstNode', $node['nodes']),
            ];

        case 'at-root':
            return [
                'kind' => $node['kind'],
                'nodes' => array_map('TailwindPHP\\cloneAstNode', $node['nodes']),
            ];

        case 'context':
            return [
                'kind' => $node['kind'],
                'context' => $node['context'],
                'nodes' => array_map('TailwindPHP\\cloneAstNode', $node['nodes']),
            ];

        case 'declaration':
            return [
                'kind' => $node['kind'],
                'property' => $node['property'],
                'value' => $node['value'],
                'important' => $node['important'],
            ];

        case 'comment':
            return [
                'kind' => $node['kind'],
                'value' => $node['value'],
            ];

        default:
            throw new \Exception("Unknown node kind: {$node['kind']}");
    }
}

/**
 * Convert AST to CSS string.
 *
 * @param array<AstNode> $ast
 * @return string
 */
function toCss(array $ast): string
{
    $stringify = function (array $node, int $depth = 0) use (&$stringify): string {
        $css = '';
        $indent = str_repeat('  ', $depth);

        switch ($node['kind']) {
            case 'declaration':
                $important = $node['important'] ? ' !important' : '';
                $css .= "{$indent}{$node['property']}: {$node['value']}{$important};\n";
                break;

            case 'rule':
                $css .= "{$indent}{$node['selector']} {\n";
                foreach ($node['nodes'] as $child) {
                    $css .= $stringify($child, $depth + 1);
                }
                $css .= "{$indent}}\n";
                break;

            case 'at-rule':
                // Print at-rules without nodes with a `;` instead of an empty block.
                if (count($node['nodes']) === 0) {
                    $css .= "{$indent}{$node['name']} {$node['params']};\n";
                } else {
                    $params = $node['params'] ? " {$node['params']} " : ' ';
                    $css .= "{$indent}{$node['name']}{$params}{\n";
                    foreach ($node['nodes'] as $child) {
                        $css .= $stringify($child, $depth + 1);
                    }
                    $css .= "{$indent}}\n";
                }
                break;

            case 'comment':
                $css .= "{$indent}/*{$node['value']}*/\n";
                break;

            case 'context':
            case 'at-root':
                // These should've been handled by optimizeAst
                break;
        }

        return $css;
    };

    $css = '';
    foreach ($ast as $node) {
        $css .= $stringify($node, 0);
    }

    return $css;
}

/**
 * Parse an at-rule from a buffer string.
 *
 * @param string $buffer
 * @param array<AstNode> $nodes
 * @return array{kind: 'at-rule', name: string, params: string, nodes: array}
 */
function parseAtRule(string $buffer, array $nodes = []): array
{
    $name = $buffer;
    $params = '';

    // Find where the name ends and params begin
    $len = strlen($buffer);
    for ($i = 5; $i < $len; $i++) {
        $char = ord($buffer[$i]);
        // SPACE = 0x20, TAB = 0x09, OPEN_PAREN = 0x28
        if ($char === 0x20 || $char === 0x09 || $char === 0x28) {
            $name = substr($buffer, 0, $i);
            $params = substr($buffer, $i);
            break;
        }
    }

    return atRule(trim($name), trim($params), $nodes);
}
