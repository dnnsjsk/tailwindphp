<?php

declare(strict_types=1);

namespace TailwindPHP;

use function TailwindPHP\Ast\rule;
use function TailwindPHP\Walk\walk;
use TailwindPHP\Walk\WalkAction;
use TailwindPHP\DesignSystem\DesignSystem;
use function TailwindPHP\Utils\segment;
use function TailwindPHP\Compile\compileCandidates;

/**
 * Substitute @apply at-rules with actual utility declarations.
 *
 * Port of: packages/tailwindcss/src/apply.ts
 *
 * @param array $ast
 * @param DesignSystem $designSystem
 * @return int Features flags
 */
function substituteAtApply(array &$ast, DesignSystem $designSystem): int
{
    $features = FEATURE_NONE;

    // Wrap the whole AST in a root rule to make sure there is always a parent
    // available for `@apply` at-rules.
    $root = rule('&', $ast);

    // Track all nodes containing @apply
    $parentNodes = [];

    // Track all the dependencies of an AstNode (by string key)
    $dependencies = [];

    // Track all @utility definitions by its root (name)
    $definitions = [];

    // Collect all new @utility definitions and all @apply rules first
    $rootArray = [$root];
    walk($rootArray, function (&$node, $ctx) use (&$features, &$parentNodes, &$dependencies, &$definitions, $designSystem) {
        if ($node['kind'] !== 'at-rule') {
            return WalkAction::Continue;
        }

        // Do not allow @apply rules inside @keyframes rules
        if ($node['name'] === '@keyframes') {
            $keyframeNodes = $node['nodes'] ?? [];
            walk($keyframeNodes, function (&$child) {
                if ($child['kind'] === 'at-rule' && $child['name'] === '@apply') {
                    throw new \Exception('You cannot use `@apply` inside `@keyframes`.');
                }
                return WalkAction::Continue;
            });
            return WalkAction::Skip;
        }

        // @utility defines a utility, which is important for topological sort
        if ($node['name'] === '@utility') {
            $name = preg_replace('/-\*$/', '', $node['params']);
            if (!isset($definitions[$name])) {
                $definitions[$name] = [];
            }
            $definitions[$name][] = $node;

            // In case @apply rules are used inside @utility rules
            $utilityNodes = $node['nodes'] ?? [];
            walk($utilityNodes, function (&$child) use (&$node, &$parentNodes, &$dependencies, $designSystem, $name) {
                if ($child['kind'] !== 'at-rule' || $child['name'] !== '@apply') {
                    return WalkAction::Continue;
                }

                $parentNodes[$name] = $node;

                foreach (resolveApplyDependencies($child, $designSystem) as $dependency) {
                    if (!isset($dependencies[$name])) {
                        $dependencies[$name] = [];
                    }
                    $dependencies[$name][$dependency] = true;
                }

                return WalkAction::Continue;
            });
            return WalkAction::Continue;
        }

        // Any other @apply node
        if ($node['name'] === '@apply') {
            $features |= FEATURE_AT_APPLY;
        }

        return WalkAction::Continue;
    });

    // Substitute the @apply at-rules
    $root = $rootArray[0];
    $rootArray2 = [$root];
    walk($rootArray2, function (&$node, $ctx) use ($designSystem) {
        if ($node['kind'] !== 'at-rule' || $node['name'] !== '@apply') {
            return WalkAction::Continue;
        }

        // Parse the candidates from @apply params
        $candidates = preg_split('/\s+/', trim($node['params']));
        $candidates = array_filter($candidates);

        if (empty($candidates)) {
            return WalkAction::Replace([]);
        }

        // Compile the candidates to CSS
        $compiled = compileCandidates($candidates, $designSystem, [
            'respectImportant' => false,
        ]);

        // Collect the nodes to insert in place of the @apply rule
        $newNodes = [];
        foreach ($compiled['astNodes'] as $candidateNode) {
            if ($candidateNode['kind'] === 'rule') {
                // Insert the rule's children instead of the rule itself
                foreach ($candidateNode['nodes'] ?? [] as $child) {
                    $newNodes[] = $child;
                }
            } else {
                $newNodes[] = $candidateNode;
            }
        }

        return WalkAction::Replace($newNodes);
    });

    // Extract the processed nodes from the root wrapper
    $root = $rootArray2[0];
    $ast = $root['nodes'] ?? [];

    return $features;
}

/**
 * Resolve dependencies from an @apply at-rule.
 *
 * @param array $node The @apply at-rule node
 * @param DesignSystem $designSystem
 * @return iterable<string> Dependency names
 */
function resolveApplyDependencies(array $node, DesignSystem $designSystem): iterable
{
    $candidates = preg_split('/\s+/', trim($node['params']));

    foreach ($candidates as $candidate) {
        if (empty($candidate)) {
            continue;
        }

        // Parse the candidate
        foreach ($designSystem->parseCandidate($candidate) as $parsed) {
            if (!isset($parsed['kind'])) {
                continue;
            }

            switch ($parsed['kind']) {
                case 'arbitrary':
                    // Doesn't matter, no lookup needed
                    break;

                case 'static':
                case 'functional':
                    // Lookup by "root"
                    yield $parsed['root'];
                    break;
            }
        }
    }
}
