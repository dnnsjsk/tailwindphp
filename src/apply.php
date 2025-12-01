<?php

declare(strict_types=1);

namespace TailwindPHP;

use TailwindPHP\Utils\DefaultMap;
use TailwindPHP\DesignSystem\DesignSystem;

/**
 * @apply directive handling
 *
 * Port of: packages/tailwindcss/src/apply.ts
 */

const FEATURES_NONE = 0;
const FEATURES_AT_APPLY = 1 << 0;

/**
 * Substitute @apply at-rules with actual utility classes.
 *
 * @param array $ast The AST to process
 * @param DesignSystem $designSystem The design system
 * @return int Features flags
 */
function substituteAtApply(array &$ast, DesignSystem $designSystem): int
{
    $features = FEATURES_NONE;

    // Wrap the whole AST in a root rule to make sure there is always a parent
    // available for `@apply` at-rules.
    $root = styleRule('&', $ast);

    // Track all nodes containing @apply
    $parents = new \SplObjectStorage();

    // Track all the dependencies of an AstNode
    $dependencies = new DefaultMap(fn() => []);

    // Track all @utility definitions by its root (name)
    $definitions = new DefaultMap(fn() => []);

    // Collect all new @utility definitions and all @apply rules first
    walk([$root], function ($node, $ctx) use (&$features, $parents, $dependencies, $definitions, $designSystem) {
        if ($node['kind'] !== 'at-rule') {
            return;
        }

        // Do not allow @apply rules inside @keyframes rules.
        if ($node['name'] === '@keyframes') {
            walk($node['nodes'] ?? [], function ($child) {
                if ($child['kind'] === 'at-rule' && $child['name'] === '@apply') {
                    throw new \RuntimeException('You cannot use `@apply` inside `@keyframes`.');
                }
            });
            return WalkAction::Skip;
        }

        // @utility defines a utility
        if ($node['name'] === '@utility') {
            $name = preg_replace('/-\*$/', '', $node['params']);
            $defs = $definitions->get($name);
            $defs[] = $node;
            $definitions->set($name, $defs);

            // In case @apply rules are used inside @utility rules.
            walk($node['nodes'] ?? [], function ($child) use ($parents, $dependencies, $designSystem, $node) {
                if ($child['kind'] !== 'at-rule' || $child['name'] !== '@apply') {
                    return;
                }

                $parents->attach((object)$node);

                $deps = $dependencies->get($node);
                foreach (resolveApplyDependencies($child, $designSystem) as $dependency) {
                    $deps[] = $dependency;
                }
                $dependencies->set($node, $deps);
            });
            return;
        }

        // Any other @apply node.
        if ($node['name'] === '@apply') {
            if ($ctx['parent'] === null) {
                return;
            }

            $features |= FEATURES_AT_APPLY;

            $parents->attach((object)$ctx['parent']);

            foreach (resolveApplyDependencies($node, $designSystem) as $dependency) {
                // Mark every parent in the path as having a dependency to that utility.
                foreach ($ctx['path']() as $parent) {
                    if (!$parents->contains((object)$parent)) {
                        continue;
                    }
                    $deps = $dependencies->get($parent);
                    $deps[] = $dependency;
                    $dependencies->set($parent, $deps);
                }
            }
        }
    });

    // Topological sort before substituting @apply
    $seen = new \SplObjectStorage();
    $sorted = [];
    $wip = new \SplObjectStorage();

    $visit = function ($node, $path = []) use (&$visit, $seen, &$sorted, $wip, $dependencies, $definitions, $designSystem) {
        $nodeObj = (object)$node;

        if ($seen->contains($nodeObj)) {
            return;
        }

        // Circular dependency detected
        if ($wip->contains($nodeObj)) {
            $pathIndex = array_search($node, $path, true);
            $next = $path[($pathIndex + 1) % count($path)] ?? null;

            if (
                $node['kind'] === 'at-rule' &&
                $node['name'] === '@utility' &&
                $next !== null &&
                $next['kind'] === 'at-rule' &&
                $next['name'] === '@utility'
            ) {
                walk($node['nodes'] ?? [], function ($child) use ($designSystem, $next) {
                    if ($child['kind'] !== 'at-rule' || $child['name'] !== '@apply') {
                        return;
                    }

                    $candidates = preg_split('/\s+/', $child['params']);
                    foreach ($candidates as $candidate) {
                        foreach ($designSystem->parseCandidate($candidate) as $candidateAstNode) {
                            if ($candidateAstNode['kind'] === 'arbitrary') {
                                continue;
                            }
                            if (in_array($candidateAstNode['kind'], ['static', 'functional'])) {
                                $nextName = preg_replace('/-\*$/', '', $next['params']);
                                if ($nextName === $candidateAstNode['root']) {
                                    throw new \RuntimeException(
                                        "You cannot `@apply` the `{$candidate}` utility here because it creates a circular dependency."
                                    );
                                }
                            }
                        }
                    }
                });
            }

            // Generic fallback error
            throw new \RuntimeException(
                "Circular dependency detected:\n\n" . toCss([$node]) . "\nRelies on:\n\n" . toCss([$next])
            );
        }

        $wip->attach($nodeObj);

        foreach ($dependencies->get($node) as $dependencyId) {
            foreach ($definitions->get($dependencyId) as $dependency) {
                $path[] = $node;
                $visit($dependency, $path);
                array_pop($path);
            }
        }

        $seen->attach($nodeObj);
        $wip->detach($nodeObj);

        $sorted[] = $node;
    };

    foreach ($parents as $parent) {
        $visit($parent);
    }

    // Substitute the @apply at-rules in order
    foreach ($sorted as $parent) {
        if (!isset($parent['nodes'])) {
            continue;
        }

        walk($parent['nodes'], function ($child) use ($designSystem) {
            if ($child['kind'] !== 'at-rule' || $child['name'] !== '@apply') {
                return;
            }

            $parts = preg_split('/(\s+)/', $child['params'], -1, PREG_SPLIT_DELIM_CAPTURE);
            $candidateOffsets = [];

            $offset = 0;
            foreach ($parts as $idx => $part) {
                if ($idx % 2 === 0) {
                    $candidateOffsets[$part] = $offset;
                }
                $offset += strlen($part);
            }

            // Parse the candidates to an AST that we can replace the @apply rule with.
            $candidates = array_keys($candidateOffsets);
            $compiled = compileCandidates($candidates, $designSystem, [
                'respectImportant' => false,
                'onInvalidCandidate' => function ($candidate) use ($designSystem) {
                    // When using prefix, make sure prefix is used in candidate
                    $prefix = $designSystem->getTheme()->getPrefix();
                    if ($prefix && !str_starts_with($candidate, $prefix)) {
                        throw new \RuntimeException(
                            "Cannot apply unprefixed utility class `{$candidate}`. Did you mean `{$prefix}:{$candidate}`?"
                        );
                    }

                    // When the utility is blocklisted
                    if ($designSystem->hasInvalidCandidate($candidate)) {
                        throw new \RuntimeException(
                            "Cannot apply utility class `{$candidate}` because it has been explicitly disabled."
                        );
                    }

                    // Verify if variants exist
                    $parts = \TailwindPHP\Utils\segment($candidate, ':');
                    if (count($parts) > 1) {
                        $utility = array_pop($parts);

                        // Ensure utility on its own compiles
                        if ($designSystem->candidatesToCss([$utility])[0] ?? null) {
                            $compiledVariants = $designSystem->candidatesToCss(
                                array_map(fn($variant) => "{$variant}:[--tw-variant-check:1]", $parts)
                            );
                            $unknownVariants = array_filter(
                                $parts,
                                fn($_, $idx) => ($compiledVariants[$idx] ?? null) === null,
                                ARRAY_FILTER_USE_BOTH
                            );
                            if (count($unknownVariants) > 0) {
                                $variantList = implode(', ', array_map(fn($v) => "`{$v}`", $unknownVariants));
                                $plural = count($unknownVariants) === 1 ? 'variant does' : 'variants do';
                                throw new \RuntimeException(
                                    "Cannot apply utility class `{$candidate}` because the {$variantList} {$plural} not exist."
                                );
                            }
                        }
                    }

                    // When the theme is empty
                    if ($designSystem->getTheme()->size() === 0) {
                        throw new \RuntimeException(
                            "Cannot apply unknown utility class `{$candidate}`. Are you missing a theme import?"
                        );
                    }

                    // Fallback to most generic error message
                    throw new \RuntimeException("Cannot apply unknown utility class `{$candidate}`");
                },
            ]);

            $candidateAst = array_map(function ($node) {
                return cloneAstNode($node);
            }, $compiled['astNodes']);

            // Collect the nodes to insert in place of the @apply rule
            $newNodes = [];
            foreach ($candidateAst as $candidateNode) {
                if ($candidateNode['kind'] === 'rule') {
                    foreach ($candidateNode['nodes'] ?? [] as $childNode) {
                        $newNodes[] = $childNode;
                    }
                } else {
                    $newNodes[] = $candidateNode;
                }
            }

            return WalkAction::Replace($newNodes);
        });
    }

    return $features;
}

/**
 * Resolve dependencies for @apply directive.
 *
 * @param array $node The @apply at-rule node
 * @param DesignSystem $designSystem The design system
 * @return \Generator<string>
 */
function resolveApplyDependencies(array $node, DesignSystem $designSystem): \Generator
{
    foreach (preg_split('/\s+/', $node['params']) as $candidate) {
        foreach ($designSystem->parseCandidate($candidate) as $parsedNode) {
            switch ($parsedNode['kind']) {
                case 'arbitrary':
                    // Doesn't matter, because there is no lookup needed
                    break;

                case 'static':
                case 'functional':
                    // Lookup by "root"
                    yield $parsedNode['root'];
                    break;
            }
        }
    }
}
