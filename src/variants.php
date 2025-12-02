<?php

declare(strict_types=1);

namespace TailwindPHP\Variants;

use TailwindPHP\Candidate\VariantsInterface;
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
class Variants implements VariantsInterface
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
     * @param array $variant
     * @return bool
     */
    public function compoundsWith(string $parent, array $variant): bool
    {
        $parentInfo = $this->variants[$parent] ?? null;

        if ($variant['kind'] === 'arbitrary') {
            $childInfo = ['compounds' => compoundsForSelectors([$variant['selector']])];
        } else {
            $childInfo = $this->variants[$variant['root']] ?? null;
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
            return \TailwindPHP\Walk\WalkAction::Skip;
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
            return \TailwindPHP\Walk\WalkAction::Skip;
        }
    });
}

/**
 * Quote attribute values in selectors.
 *
 * @param string $input
 * @return string
 */
function quoteAttributeValue(string $input): string
{
    if (strpos($input, '=') !== false) {
        $parts = explode('=', $input, 2);
        $attribute = $parts[0];
        $value = trim($parts[1] ?? '');

        // If the value is already quoted, skip
        if (strlen($value) > 0 && ($value[0] === "'" || $value[0] === '"')) {
            return $input;
        }

        // Handle case sensitivity flags on unescaped values
        if (strlen($value) > 1) {
            $trailingChar = $value[strlen($value) - 1];
            if (
                $value[strlen($value) - 2] === ' ' &&
                ($trailingChar === 'i' || $trailingChar === 'I' ||
                 $trailingChar === 's' || $trailingChar === 'S')
            ) {
                return "{$attribute}=\"" . substr($value, 0, -2) . "\" {$trailingChar}";
            }
        }

        return "{$attribute}=\"{$value}\"";
    }

    return $input;
}

/**
 * Negate conditions for @media, @supports, @container.
 *
 * @param string $ruleName
 * @param array $conditions
 * @return array
 */
function negateConditions(string $ruleName, array $conditions): array
{
    return array_map(function ($condition) use ($ruleName) {
        $condition = trim($condition);
        $parts = segment($condition, ' ');

        // @media not {query}
        // @supports not {query}
        // @container not {query}
        if (count($parts) > 0 && $parts[0] === 'not') {
            return implode(' ', array_slice($parts, 1));
        }

        if ($ruleName === '@container') {
            // @container {query}
            if (strlen($parts[0]) > 0 && $parts[0][0] === '(') {
                return "not {$condition}";
            }
            // @container {name} not {query}
            elseif (count($parts) > 1 && $parts[1] === 'not') {
                return "{$parts[0]} " . implode(' ', array_slice($parts, 2));
            }
            // @container {name} {query}
            else {
                return "{$parts[0]} not " . implode(' ', array_slice($parts, 1));
            }
        }

        return "not {$condition}";
    }, $conditions);
}

/**
 * Negate an at-rule.
 *
 * @param array $rule
 * @return array|null
 */
function negateAtRule(array $rule): ?array
{
    $conditionalRules = ['@media', '@supports', '@container'];

    foreach ($conditionalRules as $ruleName) {
        if ($ruleName !== $rule['name']) continue;

        $conditions = segment($rule['params'], ',');

        // We don't support things like `@media screen, print`
        if (count($conditions) > 1) return false;

        $conditions = negateConditions($rule['name'], $conditions);
        return \TailwindPHP\Ast\atRule($rule['name'], implode(', ', $conditions));
    }

    return false;
}

/**
 * Negate a selector.
 *
 * @param string $selector
 * @return string|null
 */
function negateSelector(string $selector): ?string
{
    if (strpos($selector, '::') !== false) return false;

    $selectors = array_map(function ($sel) {
        // Replace `&` in target variant with `*`
        return str_replace('&', '*', $sel);
    }, segment($selector, ','));

    return '&:not(' . implode(', ', $selectors) . ')';
}

/**
 * Create variants with all built-in variant definitions.
 *
 * Port of: packages/tailwindcss/src/variants.ts (createVariants function)
 *
 * @param \TailwindPHP\Theme $theme
 * @return Variants
 */
function createVariants(\TailwindPHP\Theme $theme): Variants
{
    $variants = new Variants();

    /**
     * Register a static variant like `hover`.
     */
    $staticVariant = function (
        string $name,
        array $selectors,
        array $options = []
    ) use ($variants) {
        $compounds = $options['compounds'] ?? compoundsForSelectors($selectors);

        $variants->static(
            $name,
            function (&$r) use ($selectors) {
                $r['nodes'] = array_map(
                    fn($selector) => \TailwindPHP\Ast\rule($selector, $r['nodes']),
                    $selectors
                );
            },
            ['compounds' => $compounds]
        );
    };

    // Universal selectors
    $staticVariant('*', [':is(& > *)'], ['compounds' => COMPOUNDS_NEVER]);
    $staticVariant('**', [':is(& *)'], ['compounds' => COMPOUNDS_NEVER]);

    // not-* compound variant
    $variants->compound('not', COMPOUNDS_STYLE_RULES | COMPOUNDS_AT_RULES, function (&$ruleNode, $variant) {
        if (isset($variant['variant']['kind']) && $variant['variant']['kind'] === 'arbitrary' &&
            isset($variant['variant']['relative']) && $variant['variant']['relative']) {
            return false;
        }

        if (isset($variant['modifier'])) return false;

        $didApply = false;

        $nodes = [$ruleNode];
        walk($nodes, function (&$node, $ctx) use (&$didApply, &$ruleNode) {
            if ($node['kind'] !== 'rule' && $node['kind'] !== 'at-rule') {
                return \TailwindPHP\Walk\WalkAction::Continue;
            }
            if (!empty($node['nodes'])) {
                return \TailwindPHP\Walk\WalkAction::Continue;
            }

            // Collect at-rules and style rules from path
            $atRules = [];
            $styleRules = [];

            $path = $ctx->path();
            $path[] = $node;

            foreach ($path as $pathNode) {
                if ($pathNode['kind'] === 'at-rule') {
                    $atRules[] = $pathNode;
                } elseif ($pathNode['kind'] === 'rule') {
                    $styleRules[] = $pathNode;
                }
            }

            if (count($atRules) > 1) return \TailwindPHP\Walk\WalkAction::Stop;
            if (count($styleRules) > 1) return \TailwindPHP\Walk\WalkAction::Stop;

            $rules = [];

            foreach ($styleRules as $styleNode) {
                $selector = negateSelector($styleNode['selector']);
                if (!$selector) {
                    $didApply = false;
                    return \TailwindPHP\Walk\WalkAction::Stop;
                }
                $rules[] = \TailwindPHP\Ast\styleRule($selector, []);
            }

            foreach ($atRules as $atNode) {
                $negatedAtRule = negateAtRule($atNode);
                if (!$negatedAtRule) {
                    $didApply = false;
                    return \TailwindPHP\Walk\WalkAction::Stop;
                }
                $rules[] = $negatedAtRule;
            }

            $ruleNode = \TailwindPHP\Ast\styleRule('&', $rules);
            $didApply = true;

            return \TailwindPHP\Walk\WalkAction::Skip;
        });

        // Simplify if possible
        if ($ruleNode['kind'] === 'rule' && $ruleNode['selector'] === '&' && count($ruleNode['nodes']) === 1) {
            $ruleNode = array_merge($ruleNode, $ruleNode['nodes'][0]);
        }

        if (!$didApply) return false;
    });

    $variants->suggest('not', fn() => array_filter(
        $variants->keys(),
        fn($name) => $variants->compoundsWith('not', $name)
    ));

    // group-* compound variant
    $variants->compound('group', COMPOUNDS_STYLE_RULES, function (&$ruleNode, $variant) use ($theme) {
        if (isset($variant['variant']['kind']) && $variant['variant']['kind'] === 'arbitrary' &&
            isset($variant['variant']['relative']) && $variant['variant']['relative']) {
            return false;
        }

        $prefix = $theme->getPrefix();
        $prefixStr = $prefix ? "{$prefix}\\:" : '';

        $variantSelector = isset($variant['modifier'])
            ? ":where(.{$prefixStr}group\\/{$variant['modifier']['value']})"
            : ":where(.{$prefixStr}group)";

        $didApply = false;

        // Note: We need to modify ruleNode by reference. Since walk() works on array copies,
        // we wrap in an array and copy back the result.
        $nodes = [$ruleNode];
        walk($nodes, function (&$node, $ctx) use (&$didApply, $variantSelector) {
            if ($node['kind'] !== 'rule') return \TailwindPHP\Walk\WalkAction::Continue;

            // Throw out any candidates with variants using nested style rules
            foreach ($ctx->path() as $parent) {
                if ($parent['kind'] === 'rule') {
                    $didApply = false;
                    return \TailwindPHP\Walk\WalkAction::Stop;
                }
            }

            $selector = str_replace('&', $variantSelector, $node['selector']);

            // Wrap selector list in :is()
            if (count(segment($selector, ',')) > 1) {
                $selector = ":is({$selector})";
            }

            $node['selector'] = "&:is({$selector} *)";
            $didApply = true;
        });

        // Copy back the modified node
        $ruleNode['selector'] = $nodes[0]['selector'];
        $ruleNode['nodes'] = $nodes[0]['nodes'];

        if (!$didApply) return false;
    });

    $variants->suggest('group', fn() => array_filter(
        $variants->keys(),
        fn($name) => $variants->compoundsWith('group', $name)
    ));

    // peer-* compound variant
    $variants->compound('peer', COMPOUNDS_STYLE_RULES, function (&$ruleNode, $variant) use ($theme) {
        if (isset($variant['variant']['kind']) && $variant['variant']['kind'] === 'arbitrary' &&
            isset($variant['variant']['relative']) && $variant['variant']['relative']) {
            return false;
        }

        $prefix = $theme->getPrefix();
        $prefixStr = $prefix ? "{$prefix}\\:" : '';

        $variantSelector = isset($variant['modifier'])
            ? ":where(.{$prefixStr}peer\\/{$variant['modifier']['value']})"
            : ":where(.{$prefixStr}peer)";

        $didApply = false;

        // Note: We need to modify ruleNode by reference. Since walk() works on array copies,
        // we wrap in an array and copy back the result.
        $nodes = [$ruleNode];
        walk($nodes, function (&$node, $ctx) use (&$didApply, $variantSelector) {
            if ($node['kind'] !== 'rule') return \TailwindPHP\Walk\WalkAction::Continue;

            foreach ($ctx->path() as $parent) {
                if ($parent['kind'] === 'rule') {
                    $didApply = false;
                    return \TailwindPHP\Walk\WalkAction::Stop;
                }
            }

            $selector = str_replace('&', $variantSelector, $node['selector']);

            if (count(segment($selector, ',')) > 1) {
                $selector = ":is({$selector})";
            }

            $node['selector'] = "&:is({$selector} ~ *)";
            $didApply = true;
        });

        // Copy back the modified node
        $ruleNode['selector'] = $nodes[0]['selector'];
        $ruleNode['nodes'] = $nodes[0]['nodes'];

        if (!$didApply) return false;
    });

    $variants->suggest('peer', fn() => array_filter(
        $variants->keys(),
        fn($name) => $variants->compoundsWith('peer', $name)
    ));

    // Pseudo-element variants
    $staticVariant('first-letter', ['&::first-letter']);
    $staticVariant('first-line', ['&::first-line']);
    $staticVariant('marker', [
        '& *::marker',
        '&::marker',
        '& *::-webkit-details-marker',
        '&::-webkit-details-marker',
    ]);
    $staticVariant('selection', ['& *::selection', '&::selection']);
    $staticVariant('file', ['&::file-selector-button']);
    $staticVariant('placeholder', ['&::placeholder']);
    $staticVariant('backdrop', ['&::backdrop']);
    $staticVariant('details-content', ['&::details-content']);

    // before/after with content property
    $contentProperties = function () {
        return \TailwindPHP\Ast\atRoot([
            \TailwindPHP\Ast\atRule('@property', '--tw-content', [
                \TailwindPHP\Ast\decl('syntax', '"*"'),
                \TailwindPHP\Ast\decl('initial-value', '""'),
                \TailwindPHP\Ast\decl('inherits', 'false'),
            ]),
        ]);
    };

    $variants->static(
        'before',
        function (&$v) use ($contentProperties) {
            $v['nodes'] = [
                \TailwindPHP\Ast\styleRule('&::before', [
                    $contentProperties(),
                    \TailwindPHP\Ast\decl('content', 'var(--tw-content)'),
                    ...$v['nodes'],
                ]),
            ];
        },
        ['compounds' => COMPOUNDS_NEVER]
    );

    $variants->static(
        'after',
        function (&$v) use ($contentProperties) {
            $v['nodes'] = [
                \TailwindPHP\Ast\styleRule('&::after', [
                    $contentProperties(),
                    \TailwindPHP\Ast\decl('content', 'var(--tw-content)'),
                    ...$v['nodes'],
                ]),
            ];
        },
        ['compounds' => COMPOUNDS_NEVER]
    );

    // Positional
    $staticVariant('first', ['&:first-child']);
    $staticVariant('last', ['&:last-child']);
    $staticVariant('only', ['&:only-child']);
    $staticVariant('odd', ['&:nth-child(odd)']);
    $staticVariant('even', ['&:nth-child(2n)']);
    $staticVariant('first-of-type', ['&:first-of-type']);
    $staticVariant('last-of-type', ['&:last-of-type']);
    $staticVariant('only-of-type', ['&:only-of-type']);

    // State
    $staticVariant('visited', ['&:visited']);
    $staticVariant('target', ['&:target']);
    $staticVariant('open', ['&:is([open], :popover-open, :open)']);

    // Forms
    $staticVariant('default', ['&:default']);
    $staticVariant('checked', ['&:checked']);
    $staticVariant('indeterminate', ['&:indeterminate']);
    $staticVariant('placeholder-shown', ['&:placeholder-shown']);
    $staticVariant('autofill', ['&:autofill']);
    $staticVariant('optional', ['&:optional']);
    $staticVariant('required', ['&:required']);
    $staticVariant('valid', ['&:valid']);
    $staticVariant('invalid', ['&:invalid']);
    $staticVariant('user-valid', ['&:user-valid']);
    $staticVariant('user-invalid', ['&:user-invalid']);
    $staticVariant('in-range', ['&:in-range']);
    $staticVariant('out-of-range', ['&:out-of-range']);
    $staticVariant('read-only', ['&:read-only']);

    // Content
    $staticVariant('empty', ['&:empty']);

    // Interactive
    $staticVariant('focus-within', ['&:focus-within']);

    // hover with media query
    $variants->static('hover', function (&$r) {
        $r['nodes'] = [
            \TailwindPHP\Ast\styleRule('&:hover', [
                \TailwindPHP\Ast\atRule('@media', '(hover: hover)', $r['nodes']),
            ]),
        ];
    });

    $staticVariant('focus', ['&:focus']);
    $staticVariant('focus-visible', ['&:focus-visible']);
    $staticVariant('active', ['&:active']);
    $staticVariant('enabled', ['&:enabled']);
    $staticVariant('disabled', ['&:disabled']);

    $staticVariant('inert', ['&:is([inert], [inert] *)']);

    // in-* compound variant
    $variants->compound('in', COMPOUNDS_STYLE_RULES, function (&$ruleNode, $variant) {
        if (isset($variant['modifier'])) return false;

        $didApply = false;

        $nodes = [$ruleNode];
        walk($nodes, function (&$node, $ctx) use (&$didApply) {
            if ($node['kind'] !== 'rule') return \TailwindPHP\Walk\WalkAction::Continue;

            foreach ($ctx->path() as $parent) {
                if ($parent['kind'] === 'rule') {
                    $didApply = false;
                    return \TailwindPHP\Walk\WalkAction::Stop;
                }
            }

            $node['selector'] = ':where(' . str_replace('&', '*', $node['selector']) . ') &';
            $didApply = true;
        });

        if (!$didApply) return false;
    });

    $variants->suggest('in', fn() => array_filter(
        $variants->keys(),
        fn($name) => $variants->compoundsWith('in', $name)
    ));

    // has-* compound variant
    $variants->compound('has', COMPOUNDS_STYLE_RULES, function (&$ruleNode, $variant) {
        if (isset($variant['modifier'])) return false;

        $didApply = false;

        $nodes = [$ruleNode];
        walk($nodes, function (&$node, $ctx) use (&$didApply) {
            if ($node['kind'] !== 'rule') return \TailwindPHP\Walk\WalkAction::Continue;

            foreach ($ctx->path() as $parent) {
                if ($parent['kind'] === 'rule') {
                    $didApply = false;
                    return \TailwindPHP\Walk\WalkAction::Stop;
                }
            }

            $node['selector'] = '&:has(' . str_replace('&', '*', $node['selector']) . ')';
            $didApply = true;
        });

        if (!$didApply) return false;
    });

    $variants->suggest('has', fn() => array_filter(
        $variants->keys(),
        fn($name) => $variants->compoundsWith('has', $name)
    ));

    // Functional variants
    $variants->functional('aria', function (&$ruleNode, $variant) {
        if (!isset($variant['value']) || isset($variant['modifier'])) return false;

        $value = $variant['value'];
        if ($value['kind'] === 'arbitrary') {
            $ruleNode['nodes'] = [
                \TailwindPHP\Ast\styleRule("&[aria-" . quoteAttributeValue($value['value']) . "]", $ruleNode['nodes']),
            ];
        } else {
            $ruleNode['nodes'] = [
                \TailwindPHP\Ast\styleRule("&[aria-{$value['value']}=\"true\"]", $ruleNode['nodes']),
            ];
        }
    });

    $variants->suggest('aria', fn() => [
        'busy', 'checked', 'disabled', 'expanded', 'hidden',
        'pressed', 'readonly', 'required', 'selected',
    ]);

    $variants->functional('data', function (&$ruleNode, $variant) {
        if (!isset($variant['value']) || isset($variant['modifier'])) return false;

        $ruleNode['nodes'] = [
            \TailwindPHP\Ast\styleRule("&[data-" . quoteAttributeValue($variant['value']['value']) . "]", $ruleNode['nodes']),
        ];
    });

    $variants->functional('nth', function (&$ruleNode, $variant) {
        if (!isset($variant['value']) || isset($variant['modifier'])) return false;

        $value = $variant['value'];
        if ($value['kind'] === 'named' && !ctype_digit($value['value'])) return false;

        $ruleNode['nodes'] = [
            \TailwindPHP\Ast\styleRule("&:nth-child({$value['value']})", $ruleNode['nodes']),
        ];
    });

    $variants->functional('nth-last', function (&$ruleNode, $variant) {
        if (!isset($variant['value']) || isset($variant['modifier'])) return false;

        $value = $variant['value'];
        if ($value['kind'] === 'named' && !ctype_digit($value['value'])) return false;

        $ruleNode['nodes'] = [
            \TailwindPHP\Ast\styleRule("&:nth-last-child({$value['value']})", $ruleNode['nodes']),
        ];
    });

    $variants->functional('nth-of-type', function (&$ruleNode, $variant) {
        if (!isset($variant['value']) || isset($variant['modifier'])) return false;

        $value = $variant['value'];
        if ($value['kind'] === 'named' && !ctype_digit($value['value'])) return false;

        $ruleNode['nodes'] = [
            \TailwindPHP\Ast\styleRule("&:nth-of-type({$value['value']})", $ruleNode['nodes']),
        ];
    });

    $variants->functional('nth-last-of-type', function (&$ruleNode, $variant) {
        if (!isset($variant['value']) || isset($variant['modifier'])) return false;

        $value = $variant['value'];
        if ($value['kind'] === 'named' && !ctype_digit($value['value'])) return false;

        $ruleNode['nodes'] = [
            \TailwindPHP\Ast\styleRule("&:nth-last-of-type({$value['value']})", $ruleNode['nodes']),
        ];
    });

    $variants->functional(
        'supports',
        function (&$ruleNode, $variant) {
            if (!isset($variant['value']) || isset($variant['modifier'])) return false;

            $value = $variant['value']['value'];
            if ($value === null) return false;

            // When the value starts with function-like syntax, use as-is
            if (preg_match('/^[\w-]*\s*\(/', $value)) {
                $query = preg_replace('/\b(and|or|not)\b/', ' $1 ', $value);
                $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@supports', $query, $ruleNode['nodes'])];
                return;
            }

            // Shorthand: supports-[display] -> @supports (display: var(--tw))
            if (strpos($value, ':') === false) {
                $value = "{$value}: var(--tw)";
            }

            // Wrap in parens if needed
            if ($value[0] !== '(' || $value[strlen($value) - 1] !== ')') {
                $value = "({$value})";
            }

            $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@supports', $value, $ruleNode['nodes'])];
        },
        ['compounds' => COMPOUNDS_AT_RULES]
    );

    // Media preference variants
    $staticVariant('motion-safe', ['@media (prefers-reduced-motion: no-preference)']);
    $staticVariant('motion-reduce', ['@media (prefers-reduced-motion: reduce)']);

    $staticVariant('contrast-more', ['@media (prefers-contrast: more)']);
    $staticVariant('contrast-less', ['@media (prefers-contrast: less)']);

    // Breakpoints
    $breakpoints = $theme->namespace('--breakpoint');
    if (empty($breakpoints)) {
        $breakpoints = [
            'sm' => '40rem',
            'md' => '48rem',
            'lg' => '64rem',
            'xl' => '80rem',
            '2xl' => '96rem',
        ];
    }

    // max-* breakpoint variant
    $variants->group(function () use ($variants, $theme, $breakpoints) {
        $variants->functional(
            'max',
            function (&$ruleNode, $variant) use ($theme, $breakpoints) {
                if (isset($variant['modifier'])) return false;
                if (!isset($variant['value'])) return false;

                $value = $variant['value'];
                $breakpointValue = null;

                if ($value['kind'] === 'arbitrary') {
                    $breakpointValue = $value['value'];
                } elseif ($value['kind'] === 'named') {
                    $breakpointValue = $breakpoints[$value['value']] ??
                        $theme->resolve(null, ["--breakpoint-{$value['value']}"]);
                }

                if (!$breakpointValue) return false;

                $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@media', "(width < {$breakpointValue})", $ruleNode['nodes'])];
            },
            ['compounds' => COMPOUNDS_AT_RULES]
        );
    });

    $variants->suggest('max', fn() => array_keys($breakpoints));

    // min-* breakpoint and static breakpoint variants (sm, md, lg, etc.)
    $variants->group(function () use ($variants, $theme, $breakpoints) {
        // Register static breakpoint variants
        foreach ($breakpoints as $name => $value) {
            $variants->static(
                $name,
                function (&$ruleNode) use ($value) {
                    $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@media', "(width >= {$value})", $ruleNode['nodes'])];
                },
                ['compounds' => COMPOUNDS_AT_RULES]
            );
        }

        // Register min-* functional variant
        $variants->functional(
            'min',
            function (&$ruleNode, $variant) use ($theme, $breakpoints) {
                if (isset($variant['modifier'])) return false;
                if (!isset($variant['value'])) return false;

                $value = $variant['value'];
                $breakpointValue = null;

                if ($value['kind'] === 'arbitrary') {
                    $breakpointValue = $value['value'];
                } elseif ($value['kind'] === 'named') {
                    $breakpointValue = $breakpoints[$value['value']] ??
                        $theme->resolve(null, ["--breakpoint-{$value['value']}"]);
                }

                if (!$breakpointValue) return false;

                $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@media', "(width >= {$breakpointValue})", $ruleNode['nodes'])];
            },
            ['compounds' => COMPOUNDS_AT_RULES]
        );
    });

    $variants->suggest('min', fn() => array_keys($breakpoints));

    // Container query variants
    $containerWidths = $theme->namespace('--container');

    // @max-* container variant
    $variants->group(function () use ($variants, $theme, $containerWidths) {
        $variants->functional(
            '@max',
            function (&$ruleNode, $variant) use ($theme, $containerWidths) {
                if (!isset($variant['value'])) return false;

                $value = $variant['value'];
                $width = null;

                if ($value['kind'] === 'arbitrary') {
                    $width = $value['value'];
                } elseif ($value['kind'] === 'named') {
                    $width = $containerWidths[$value['value']] ??
                        $theme->resolve(null, ["--container-{$value['value']}"]);
                }

                if (!$width) return false;

                $containerName = isset($variant['modifier']) ? $variant['modifier']['value'] : null;
                $params = $containerName ? "{$containerName} (width < {$width})" : "(width < {$width})";

                $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@container', $params, $ruleNode['nodes'])];
            },
            ['compounds' => COMPOUNDS_AT_RULES]
        );
    });

    $variants->suggest('@max', fn() => array_keys($containerWidths ?? []));

    // @* and @min-* container variants
    $variants->group(function () use ($variants, $theme, $containerWidths) {
        $variants->functional(
            '@',
            function (&$ruleNode, $variant) use ($theme, $containerWidths) {
                if (!isset($variant['value'])) return false;

                $value = $variant['value'];
                $width = null;

                if ($value['kind'] === 'arbitrary') {
                    $width = $value['value'];
                } elseif ($value['kind'] === 'named') {
                    $width = $containerWidths[$value['value']] ??
                        $theme->resolve(null, ["--container-{$value['value']}"]);
                }

                if (!$width) return false;

                $containerName = isset($variant['modifier']) ? $variant['modifier']['value'] : null;
                $params = $containerName ? "{$containerName} (width >= {$width})" : "(width >= {$width})";

                $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@container', $params, $ruleNode['nodes'])];
            },
            ['compounds' => COMPOUNDS_AT_RULES]
        );

        $variants->functional(
            '@min',
            function (&$ruleNode, $variant) use ($theme, $containerWidths) {
                if (!isset($variant['value'])) return false;

                $value = $variant['value'];
                $width = null;

                if ($value['kind'] === 'arbitrary') {
                    $width = $value['value'];
                } elseif ($value['kind'] === 'named') {
                    $width = $containerWidths[$value['value']] ??
                        $theme->resolve(null, ["--container-{$value['value']}"]);
                }

                if (!$width) return false;

                $containerName = isset($variant['modifier']) ? $variant['modifier']['value'] : null;
                $params = $containerName ? "{$containerName} (width >= {$width})" : "(width >= {$width})";

                $ruleNode['nodes'] = [\TailwindPHP\Ast\atRule('@container', $params, $ruleNode['nodes'])];
            },
            ['compounds' => COMPOUNDS_AT_RULES]
        );
    });

    $variants->suggest('@min', fn() => array_keys($containerWidths ?? []));
    $variants->suggest('@', fn() => array_keys($containerWidths ?? []));

    // Orientation
    $staticVariant('portrait', ['@media (orientation: portrait)']);
    $staticVariant('landscape', ['@media (orientation: landscape)']);

    // Direction
    $staticVariant('ltr', ['&:where(:dir(ltr), [dir="ltr"], [dir="ltr"] *)']);
    $staticVariant('rtl', ['&:where(:dir(rtl), [dir="rtl"], [dir="rtl"] *)']);

    // Color scheme
    $staticVariant('dark', ['@media (prefers-color-scheme: dark)']);

    // Starting style
    $staticVariant('starting', ['@starting-style']);

    // Print
    $staticVariant('print', ['@media print']);

    // Forced colors
    $staticVariant('forced-colors', ['@media (forced-colors: active)']);

    // Inverted colors
    $staticVariant('inverted-colors', ['@media (inverted-colors: inverted)']);

    // Pointer variants
    $staticVariant('pointer-none', ['@media (pointer: none)']);
    $staticVariant('pointer-coarse', ['@media (pointer: coarse)']);
    $staticVariant('pointer-fine', ['@media (pointer: fine)']);
    $staticVariant('any-pointer-none', ['@media (any-pointer: none)']);
    $staticVariant('any-pointer-coarse', ['@media (any-pointer: coarse)']);
    $staticVariant('any-pointer-fine', ['@media (any-pointer: fine)']);

    // Scripting
    $staticVariant('noscript', ['@media (scripting: none)']);

    return $variants;
}
