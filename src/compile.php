<?php

declare(strict_types=1);

namespace TailwindPHP\Compile;

use function TailwindPHP\Ast\rule;
use function TailwindPHP\Ast\decl;
use function TailwindPHP\Ast\atRule;
use function TailwindPHP\Escape\escape;
use function TailwindPHP\Utils\compare;
use function TailwindPHP\Walk\walk;
use function TailwindPHP\PropertyOrder\PROPERTY_ORDER;

use const TailwindPHP\PropertyOrder\PROPERTY_ORDER;

/**
 * Compile - Candidate compilation to CSS AST.
 *
 * Port of: packages/tailwindcss/src/compile.ts
 */

// CompileAstFlags
const COMPILE_FLAG_NONE = 0;
const COMPILE_FLAG_RESPECT_IMPORTANT = 1 << 0;

/**
 * Compile multiple candidates into AST nodes.
 *
 * @param iterable<string> $rawCandidates
 * @param object $designSystem
 * @param array $options
 * @return array{astNodes: array, nodeSorting: array}
 */
function compileCandidates(
    iterable $rawCandidates,
    object $designSystem,
    array $options = []
): array {
    $onInvalidCandidate = $options['onInvalidCandidate'] ?? null;
    $respectImportant = $options['respectImportant'] ?? true;

    $nodeSorting = [];
    $astNodes = [];
    $matches = [];

    // Parse candidates and variants
    foreach ($rawCandidates as $rawCandidate) {
        if (isset($designSystem->invalidCandidates[$rawCandidate])) {
            if ($onInvalidCandidate) {
                $onInvalidCandidate($rawCandidate);
            }
            continue;
        }

        $candidates = $designSystem->parseCandidate($rawCandidate);
        if (empty($candidates)) {
            if ($onInvalidCandidate) {
                $onInvalidCandidate($rawCandidate);
            }
            continue;
        }

        $matches[$rawCandidate] = $candidates;
    }

    $flags = COMPILE_FLAG_NONE;
    if ($respectImportant) {
        $flags |= COMPILE_FLAG_RESPECT_IMPORTANT;
    }

    $variantOrderMap = $designSystem->getVariantOrder();

    // Create the AST
    foreach ($matches as $rawCandidate => $candidates) {
        $found = false;

        foreach ($candidates as $candidate) {
            $rules = $designSystem->compileAstNodes($candidate, $flags);
            if (empty($rules)) continue;

            $found = true;

            foreach ($rules as $ruleInfo) {
                $node = $ruleInfo['node'];
                $propertySort = $ruleInfo['propertySort'];

                // Track the variant order
                $variantOrder = 0;
                foreach ($candidate['variants'] as $variant) {
                    $variantOrder |= 1 << ($variantOrderMap[$variant] ?? 0);
                }

                $nodeSorting[spl_object_hash((object)$node)] = [
                    'properties' => $propertySort,
                    'variants' => $variantOrder,
                    'candidate' => $rawCandidate,
                ];
                $astNodes[] = $node;
            }
        }

        if (!$found && $onInvalidCandidate) {
            $onInvalidCandidate($rawCandidate);
        }
    }

    // Sort AST nodes
    usort($astNodes, function ($a, $z) use (&$nodeSorting) {
        $aSorting = $nodeSorting[spl_object_hash((object)$a)];
        $zSorting = $nodeSorting[spl_object_hash((object)$z)];

        // Sort by variant order first
        if ($aSorting['variants'] !== $zSorting['variants']) {
            return $aSorting['variants'] - $zSorting['variants'];
        }

        // Find the first property that is different between the two rules
        $offset = 0;
        while (
            $offset < count($aSorting['properties']['order']) &&
            $offset < count($zSorting['properties']['order']) &&
            $aSorting['properties']['order'][$offset] === $zSorting['properties']['order'][$offset]
        ) {
            $offset++;
        }

        // Sort by lowest property index first
        $aOrder = $aSorting['properties']['order'][$offset] ?? PHP_INT_MAX;
        $zOrder = $zSorting['properties']['order'][$offset] ?? PHP_INT_MAX;

        if ($aOrder !== $zOrder) {
            return $aOrder - $zOrder;
        }

        // Sort by most properties first, then by least properties
        if ($zSorting['properties']['count'] !== $aSorting['properties']['count']) {
            return $zSorting['properties']['count'] - $aSorting['properties']['count'];
        }

        // Sort alphabetically
        return compare($aSorting['candidate'], $zSorting['candidate']);
    });

    return [
        'astNodes' => $astNodes,
        'nodeSorting' => $nodeSorting,
    ];
}

/**
 * Compile AST nodes for a single candidate.
 *
 * @param array $candidate
 * @param object $designSystem
 * @param int $flags
 * @return array
 */
function compileAstNodes(array $candidate, object $designSystem, int $flags): array
{
    $asts = compileBaseUtility($candidate, $designSystem);
    if (empty($asts)) return [];

    $respectImportant = $designSystem->important && ($flags & COMPILE_FLAG_RESPECT_IMPORTANT);

    $rules = [];
    $selector = '.' . escape($candidate['raw']);

    foreach ($asts as $nodes) {
        $propertySort = getPropertySort($nodes);

        // Apply important if needed
        if ($candidate['important'] || $respectImportant) {
            applyImportant($nodes);
        }

        $node = [
            'kind' => 'rule',
            'selector' => $selector,
            'nodes' => $nodes,
        ];

        // Apply variants
        foreach ($candidate['variants'] as $variant) {
            $result = applyVariant($node, $variant, $designSystem->variants);

            // When the variant results in null, the variant cannot be applied
            if ($result === null) return [];
        }

        $rules[] = [
            'node' => $node,
            'propertySort' => $propertySort,
        ];
    }

    return $rules;
}

/**
 * Apply a variant to a rule node.
 *
 * @param array &$node
 * @param array $variant
 * @param object $variants
 * @param int $depth
 * @return null|void
 */
function applyVariant(array &$node, array $variant, object $variants, int $depth = 0)
{
    if ($variant['kind'] === 'arbitrary') {
        // Relative selectors are not valid at the top level
        if ($variant['relative'] && $depth === 0) return null;

        $node['nodes'] = [rule($variant['selector'], $node['nodes'])];
        return;
    }

    // Get the variant's apply function
    $variantData = $variants->get($variant['root']);
    if (!$variantData) return null;

    $applyFn = $variantData['applyFn'];

    if ($variant['kind'] === 'compound') {
        // Create an isolated placeholder node
        $isolatedNode = atRule('@slot');

        $result = applyVariant($isolatedNode, $variant['variant'], $variants, $depth + 1);
        if ($result === null) return null;

        if ($variant['root'] === 'not' && count($isolatedNode['nodes']) > 1) {
            return null;
        }

        foreach ($isolatedNode['nodes'] as &$child) {
            if ($child['kind'] !== 'rule' && $child['kind'] !== 'at-rule') return null;

            $result = $applyFn($child, $variant);
            if ($result === null) return null;
        }

        // Replace placeholder with actual node
        walk($isolatedNode['nodes'], function (&$child) use (&$node) {
            if (($child['kind'] === 'rule' || $child['kind'] === 'at-rule') && empty($child['nodes'])) {
                $child['nodes'] = $node['nodes'];
                return \TailwindPHP\Walk\WALK_ACTION_SKIP;
            }
        });

        $node['nodes'] = $isolatedNode['nodes'];
        return;
    }

    // All other variants
    $result = $applyFn($node, $variant);
    if ($result === null) return null;
}

/**
 * Check if a utility is a fallback utility.
 *
 * @param array $utility
 * @return bool
 */
function isFallbackUtility(array $utility): bool
{
    $types = $utility['options']['types'] ?? [];
    return count($types) > 1 && in_array('any', $types);
}

/**
 * Compile the base utility for a candidate.
 *
 * @param array $candidate
 * @param object $designSystem
 * @return array
 */
function compileBaseUtility(array $candidate, object $designSystem): array
{
    if ($candidate['kind'] === 'arbitrary') {
        $value = $candidate['value'];

        // Handle opacity modifier for arbitrary properties
        if ($candidate['modifier']) {
            $value = asColor($value, $candidate['modifier'], $designSystem->theme);
        }

        if ($value === null) return [];

        return [[decl($candidate['property'], $value)]];
    }

    $utilities = $designSystem->utilities->get($candidate['root']) ?? [];

    $asts = [];

    // Try normal utilities first
    $normalUtilities = array_filter($utilities, fn($u) => !isFallbackUtility($u));
    foreach ($normalUtilities as $utility) {
        if ($utility['kind'] !== $candidate['kind']) continue;

        $compiledNodes = $utility['compileFn']($candidate);
        if ($compiledNodes === null) return $asts;
        if ($compiledNodes === false) continue;
        $asts[] = $compiledNodes;
    }

    if (!empty($asts)) return $asts;

    // Try fallback utilities
    $fallbackUtilities = array_filter($utilities, fn($u) => isFallbackUtility($u));
    foreach ($fallbackUtilities as $utility) {
        if ($utility['kind'] !== $candidate['kind']) continue;

        $compiledNodes = $utility['compileFn']($candidate);
        if ($compiledNodes === null) return $asts;
        if ($compiledNodes === false) continue;
        $asts[] = $compiledNodes;
    }

    return $asts;
}

/**
 * Apply color modification with opacity.
 *
 * @param string $value
 * @param array $modifier
 * @param object $theme
 * @return string|null
 */
function asColor(string $value, array $modifier, object $theme): ?string
{
    // This will be implemented when utilities.php is ported
    // For now, return the value as-is
    return $value;
}

/**
 * Apply !important to all declarations in an AST.
 *
 * @param array &$ast
 * @return void
 */
function applyImportant(array &$ast): void
{
    for ($i = 0; $i < count($ast); $i++) {
        $node = &$ast[$i];

        // Skip AtRoot nodes
        if ($node['kind'] === 'at-root') {
            continue;
        }

        if ($node['kind'] === 'declaration') {
            $node['important'] = true;
        } elseif ($node['kind'] === 'rule' || $node['kind'] === 'at-rule') {
            applyImportant($node['nodes']);
        }
    }
}

/**
 * Get property sort order for AST nodes.
 *
 * @param array $nodes
 * @return array{order: array<int>, count: int}
 */
function getPropertySort(array $nodes): array
{
    $order = [];
    $count = 0;
    $queue = $nodes;
    $seenTwSort = false;

    while (!empty($queue)) {
        $node = array_shift($queue);

        if ($node['kind'] === 'declaration') {
            if (!isset($node['value'])) continue;

            $count++;

            if ($seenTwSort) continue;

            // Check for --tw-sort property
            if ($node['property'] === '--tw-sort') {
                $idx = array_search($node['value'] ?? '', PROPERTY_ORDER);
                if ($idx !== false) {
                    $order[$idx] = true;
                    $seenTwSort = true;
                    continue;
                }
            }

            $idx = array_search($node['property'], PROPERTY_ORDER);
            if ($idx !== false) {
                $order[$idx] = true;
            }
        } elseif ($node['kind'] === 'rule' || $node['kind'] === 'at-rule') {
            foreach ($node['nodes'] as $child) {
                $queue[] = $child;
            }
        }
    }

    return [
        'order' => array_keys($order),
        'count' => $count,
    ];
}
