<?php

declare(strict_types=1);

namespace TailwindPHP\Variants;

use function TailwindPHP\Ast\rule;
use function TailwindPHP\Ast\atRule;
use function TailwindPHP\Ast\styleRule;
use function TailwindPHP\Ast\cloneAstNode;
use function TailwindPHP\Utils\segment;
use function TailwindPHP\Walk\walk;

/**
 * Variants - Variant registry and core variant functions.
 *
 * Port of: packages/tailwindcss/src/variants.ts
 */

const IS_VALID_VARIANT_NAME = '/^@?[a-z0-9][a-zA-Z0-9_-]*(?<![_-])$/';

// Compound flags - what types of rules a variant can compound with
const COMPOUNDS_NEVER = 0;
const COMPOUNDS_AT_RULES = 1 << 0;      // 1
const COMPOUNDS_STYLE_RULES = 1 << 1;   // 2

/**
 * Variants class to manage variant registrations.
 */
class Variants
{
    /**
     * @var array<int, callable>
     */
    public array $compareFns = [];

    /**
     * @var array<string, array{kind: string, order: int, applyFn: callable, compoundsWith: int, compounds: int}>
     */
    public array $variants = [];

    /**
     * @var array<string, callable>
     */
    private array $completions = [];

    /**
     * Group order for variant ordering.
     * @var int|null
     */
    private ?int $groupOrder = null;

    /**
     * Last assigned order.
     * @var int
     */
    private int $lastOrder = 0;

    /**
     * Register a static variant.
     *
     * @param string $name
     * @param callable $applyFn
     * @param array $options
     * @return void
     */
    public function static(string $name, callable $applyFn, array $options = []): void
    {
        $this->set($name, [
            'kind' => 'static',
            'applyFn' => $applyFn,
            'compoundsWith' => COMPOUNDS_NEVER,
            'compounds' => $options['compounds'] ?? COMPOUNDS_STYLE_RULES,
            'order' => $options['order'] ?? null,
        ]);
    }

    /**
     * Register a variant from an AST definition.
     *
     * @param string $name
     * @param array $ast
     * @param object $designSystem
     * @return void
     */
    public function fromAst(string $name, array $ast, object $designSystem): void
    {
        $selectors = [];
        $usesAtVariant = false;

        walk($ast, function ($node) use (&$selectors, &$usesAtVariant) {
            if ($node['kind'] === 'rule') {
                $selectors[] = $node['selector'];
            } elseif ($node['kind'] === 'at-rule' && $node['name'] === '@variant') {
                $usesAtVariant = true;
            } elseif ($node['kind'] === 'at-rule' && $node['name'] !== '@slot') {
                $selectors[] = "{$node['name']} {$node['params']}";
            }
        });

        $this->static(
            $name,
            function (&$r) use ($ast, $usesAtVariant, $designSystem) {
                $body = array_map('TailwindPHP\\Ast\\cloneAstNode', $ast);
                if ($usesAtVariant) {
                    substituteAtVariant($body, $designSystem);
                }
                substituteAtSlot($body, $r['nodes']);
                $r['nodes'] = $body;
            },
            ['compounds' => compoundsForSelectors($selectors)]
        );
    }

    /**
     * Register a functional variant.
     *
     * @param string $name
     * @param callable $applyFn
     * @param array $options
     * @return void
     */
    public function functional(string $name, callable $applyFn, array $options = []): void
    {
        $this->set($name, [
            'kind' => 'functional',
            'applyFn' => $applyFn,
            'compoundsWith' => COMPOUNDS_NEVER,
            'compounds' => $options['compounds'] ?? COMPOUNDS_STYLE_RULES,
            'order' => $options['order'] ?? null,
        ]);
    }

    /**
     * Register a compound variant.
     *
     * @param string $name
     * @param int $compoundsWith
     * @param callable $applyFn
     * @param array $options
     * @return void
     */
    public function compound(string $name, int $compoundsWith, callable $applyFn, array $options = []): void
    {
        $this->set($name, [
            'kind' => 'compound',
            'applyFn' => $applyFn,
            'compoundsWith' => $compoundsWith,
            'compounds' => $options['compounds'] ?? COMPOUNDS_STYLE_RULES,
            'order' => $options['order'] ?? null,
        ]);
    }

    /**
     * Group variants together for ordering.
     *
     * @param callable $fn
     * @param callable|null $compareFn
     * @return void
     */
    public function group(callable $fn, ?callable $compareFn = null): void
    {
        $this->groupOrder = $this->nextOrder();
        if ($compareFn) {
            $this->compareFns[$this->groupOrder] = $compareFn;
        }
        $fn();
        $this->groupOrder = null;
    }

    /**
     * Check if a variant exists.
     *
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->variants[$name]);
    }

    /**
     * Get a variant by name.
     *
     * @param string $name
     * @return array|null
     */
    public function get(string $name): ?array
    {
        return $this->variants[$name] ?? null;
    }

    /**
     * Get the kind of a variant.
     *
     * @param string $name
     * @return string
     */
    public function kind(string $name): string
    {
        return $this->variants[$name]['kind'] ?? '';
    }

    /**
     * Check if a parent variant can compound with a child variant.
     *
     * @param string $parent
     * @param string|array $child
     * @return bool
     */
    public function compoundsWith(string $parent, $child): bool
    {
        $parentInfo = $this->variants[$parent] ?? null;

        if (is_string($child)) {
            $childInfo = $this->variants[$child] ?? null;
        } else {
            if ($child['kind'] === 'arbitrary') {
                $childInfo = ['compounds' => compoundsForSelectors([$child['selector']])];
            } else {
                $childInfo = $this->variants[$child['root']] ?? null;
            }
        }

        // One of the variants doesn't exist
        if (!$parentInfo || !$childInfo) return false;

        // The parent variant is not a compound variant
        if ($parentInfo['kind'] !== 'compound') return false;

        // The child variant cannot compound
        if ($childInfo['compounds'] === COMPOUNDS_NEVER) return false;

        // The parent variant cannot compound
        if ($parentInfo['compoundsWith'] === COMPOUNDS_NEVER) return false;

        // Any rule that child may generate must be supported by parent
        if (($parentInfo['compoundsWith'] & $childInfo['compounds']) === 0) return false;

        return true;
    }

    /**
     * Register suggestions for a variant.
     *
     * @param string $name
     * @param callable $suggestions
     * @return void
     */
    public function suggest(string $name, callable $suggestions): void
    {
        $this->completions[$name] = $suggestions;
    }

    /**
     * Get completions for a variant.
     *
     * @param string $name
     * @return array
     */
    public function getCompletions(string $name): array
    {
        if (isset($this->completions[$name])) {
            return ($this->completions[$name])();
        }
        return [];
    }

    /**
     * Compare two variants for sorting.
     *
     * @param array|null $a
     * @param array|null $z
     * @return int
     */
    public function compare(?array $a, ?array $z): int
    {
        if ($a === $z) return 0;
        if ($a === null) return -1;
        if ($z === null) return 1;

        if ($a['kind'] === 'arbitrary' && $z['kind'] === 'arbitrary') {
            return $a['selector'] < $z['selector'] ? -1 : 1;
        } elseif ($a['kind'] === 'arbitrary') {
            return 1;
        } elseif ($z['kind'] === 'arbitrary') {
            return -1;
        }

        $aOrder = $this->variants[$a['root']]['order'] ?? 0;
        $zOrder = $this->variants[$z['root']]['order'] ?? 0;

        $orderedByVariant = $aOrder - $zOrder;
        if ($orderedByVariant !== 0) return $orderedByVariant;

        if ($a['kind'] === 'compound' && $z['kind'] === 'compound') {
            $order = $this->compare($a['variant'], $z['variant']);
            if ($order !== 0) return $order;

            if (isset($a['modifier']) && isset($z['modifier'])) {
                return $a['modifier']['value'] < $z['modifier']['value'] ? -1 : 1;
            } elseif (isset($a['modifier'])) {
                return 1;
            } elseif (isset($z['modifier'])) {
                return -1;
            } else {
                return 0;
            }
        }

        if (isset($this->compareFns[$aOrder])) {
            return ($this->compareFns[$aOrder])($a, $z);
        }

        if ($a['root'] !== $z['root']) {
            return $a['root'] < $z['root'] ? -1 : 1;
        }

        // Both are functional at this point
        $aValue = $a['value'] ?? null;
        $zValue = $z['value'] ?? null;

        if ($aValue === null) return -1;
        if ($zValue === null) return 1;

        // Variants with arbitrary values should appear after named values
        if ($aValue['kind'] === 'arbitrary' && $zValue['kind'] !== 'arbitrary') return 1;
        if ($aValue['kind'] !== 'arbitrary' && $zValue['kind'] === 'arbitrary') return -1;

        return $aValue['value'] < $zValue['value'] ? -1 : 1;
    }

    /**
     * Get all variant keys.
     *
     * @return array<string>
     */
    public function keys(): array
    {
        return array_keys($this->variants);
    }

    /**
     * Get all variant entries.
     *
     * @return iterable<array{0: string, 1: array}>
     */
    public function entries(): iterable
    {
        foreach ($this->variants as $key => $value) {
            yield [$key, $value];
        }
    }

    /**
     * Set a variant.
     *
     * @param string $name
     * @param array $data
     * @return void
     */
    private function set(string $name, array $data): void
    {
        $existing = $this->variants[$name] ?? null;

        if ($existing) {
            $this->variants[$name] = array_merge($existing, [
                'kind' => $data['kind'],
                'applyFn' => $data['applyFn'],
                'compounds' => $data['compounds'],
            ]);
        } else {
            $order = $data['order'];
            if ($order === null) {
                $this->lastOrder = $this->nextOrder();
                $order = $this->lastOrder;
            }

            $this->variants[$name] = [
                'kind' => $data['kind'],
                'applyFn' => $data['applyFn'],
                'order' => $order,
                'compoundsWith' => $data['compoundsWith'],
                'compounds' => $data['compounds'],
            ];
        }
    }

    /**
     * Get the next order number.
     *
     * @return int
     */
    private function nextOrder(): int
    {
        return $this->groupOrder ?? $this->lastOrder + 1;
    }
}

/**
 * Determine what compound types are supported by a set of selectors.
 *
 * @param array<string> $selectors
 * @return int
 */
function compoundsForSelectors(array $selectors): int
{
    $compounds = COMPOUNDS_NEVER;

    foreach ($selectors as $sel) {
        if (strlen($sel) > 0 && $sel[0] === '@') {
            // Non-conditional at-rules are present so we can't compound
            if (
                !str_starts_with($sel, '@media') &&
                !str_starts_with($sel, '@supports') &&
                !str_starts_with($sel, '@container')
            ) {
                return COMPOUNDS_NEVER;
            }

            $compounds |= COMPOUNDS_AT_RULES;
            continue;
        }

        // Pseudo-elements are present so we can't compound
        if (strpos($sel, '::') !== false) {
            return COMPOUNDS_NEVER;
        }

        $compounds |= COMPOUNDS_STYLE_RULES;
    }

    return $compounds;
}

/**
 * Substitute @slot placeholders with actual nodes.
 *
 * @param array &$ast
 * @param array $nodes
 * @return void
 */
function substituteAtSlot(array &$ast, array $nodes): void
{
    walk($ast, function (&$node) use ($nodes) {
        if ($node['kind'] === 'at-rule' && $node['name'] === '@slot') {
            $node['kind'] = 'rule';
            $node['selector'] = '&';
            $node['nodes'] = $nodes;
            unset($node['name'], $node['params']);
            return \TailwindPHP\Walk\WALK_ACTION_SKIP;
        }
    });
}

/**
 * Substitute @variant references.
 *
 * @param array &$ast
 * @param object $designSystem
 * @return void
 */
function substituteAtVariant(array &$ast, object $designSystem): void
{
    walk($ast, function (&$node) use ($designSystem) {
        if ($node['kind'] === 'at-rule' && $node['name'] === '@variant') {
            $variantName = trim($node['params']);
            $variant = $designSystem->variants->get($variantName);
            if ($variant) {
                // Apply the variant transformation
                // This is simplified - full implementation would be more complex
                $node['kind'] = 'rule';
                $node['selector'] = '&';
                unset($node['name'], $node['params']);
            }
            return \TailwindPHP\Walk\WALK_ACTION_SKIP;
        }
    });
}
