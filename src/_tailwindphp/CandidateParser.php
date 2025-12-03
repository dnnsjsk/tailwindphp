<?php

declare(strict_types=1);

namespace TailwindPHP\CandidateParser;

use TailwindPHP\Utilities\Utilities;

/**
 * Candidate Parser - Parse Tailwind class names into structured data.
 *
 * @port-deviation:helper This is NOT part of the TailwindCSS port.
 * It's a simplified candidate parser that works with the Utilities registry.
 *
 * The full candidate.php port handles more complex cases (variants, etc.).
 * This parser handles the core utility parsing needed for compilation.
 */
class CandidateParser
{
    private Utilities $utilities;

    public function __construct(Utilities $utilities)
    {
        $this->utilities = $utilities;
    }

    /**
     * Parse a candidate string into its components.
     *
     * @param string $candidate The class name to parse
     * @return array|null Parsed candidate or null if not valid
     */
    public function parse(string $candidate): ?array
    {
        // Check for important modifier
        $important = false;
        $base = $candidate;

        if (str_ends_with($base, '!')) {
            $important = true;
            $base = substr($base, 0, -1);
        } elseif (str_starts_with($base, '!')) {
            $important = true;
            $base = substr($base, 1);
        }

        // Check for static utility (including those starting with -, like -translate-full)
        if ($this->utilities->has($base, 'static')) {
            return [
                'kind' => 'static',
                'root' => $base,
                'important' => $important,
                'raw' => $candidate,
            ];
        }

        // Check for functional utility
        $parts = $this->parseFunctionalCandidate($base);
        if ($parts !== null) {
            [$root, $value, $modifier] = $parts;

            if ($this->utilities->has($root, 'functional')) {
                return [
                    'kind' => 'functional',
                    'root' => $root,
                    'value' => $value,
                    'modifier' => $modifier !== null
                        ? ['kind' => 'named', 'value' => $modifier]
                        : null,
                    'important' => $important,
                    'raw' => $candidate,
                ];
            }
        }

        return null;
    }

    /**
     * Parse a functional candidate into root, value, and modifier.
     *
     * @param string $candidate
     * @return array|null [$root, $value, $modifier] or null
     */
    private function parseFunctionalCandidate(string $candidate): ?array
    {
        // Check if this might have a modifier (/ not inside arbitrary values)
        $hasSlash = !str_contains($candidate, '[') && str_contains($candidate, '/');

        // If there's a / that could be a modifier, try modifier approach first
        if ($hasSlash) {
            $slashPos = strrpos($candidate, '/');
            $potentialModifier = substr($candidate, $slashPos + 1);
            $candidateWithoutModifier = substr($candidate, 0, $slashPos);

            // Only treat as modifier if the part after / is NOT numeric (not a fraction)
            if (!is_numeric($potentialModifier) && !preg_match('/^\d+$/', $potentialModifier)) {
                $result = $this->parseFunctionalCandidateInternal($candidateWithoutModifier);
                if ($result !== null) {
                    return [$result[0], $result[1], $potentialModifier];
                }
            }
        }

        // Try to parse without modifier extraction
        $result = $this->parseFunctionalCandidateInternal($candidate);
        if ($result !== null) {
            return [$result[0], $result[1], null];
        }

        return null;
    }

    /**
     * Internal helper to parse candidate without modifier handling.
     *
     * @param string $candidate
     * @return array|null [$root, $valueObj] or null
     */
    private function parseFunctionalCandidateInternal(string $candidate): ?array
    {
        // First check for exact match (utility with default value like rounded-t, border-x)
        if ($this->utilities->has($candidate, 'functional')) {
            return [$candidate, null];
        }

        // Check for parenthesized CSS variable syntax like rotate-(--var)
        if (str_ends_with($candidate, ')')) {
            $idx = strpos($candidate, '-(');
            if ($idx !== false) {
                $maybeRoot = substr($candidate, 0, $idx);
                if ($this->utilities->has($maybeRoot, 'functional')) {
                    $value = substr($candidate, $idx + 2, -1);
                    // Parenthesized values must start with -- (CSS variable)
                    if (strlen($value) >= 2 && $value[0] === '-' && $value[1] === '-') {
                        $valueObj = [
                            'kind' => 'arbitrary',
                            'value' => "var({$value})",
                            'dataType' => null,
                        ];

                        return [$maybeRoot, $valueObj];
                    }
                }
            }
        }

        // Try to find the root by testing progressively shorter prefixes
        $idx = strrpos($candidate, '-');

        while ($idx !== false && $idx > 0) {
            $maybeRoot = substr($candidate, 0, $idx);

            if ($this->utilities->has($maybeRoot, 'functional')) {
                $value = substr($candidate, $idx + 1);
                if ($value === '') {
                    return null;
                }

                // Determine value kind
                $valueObj = null;
                if (str_starts_with($value, '[') && str_ends_with($value, ']')) {
                    // Arbitrary value - decode underscores to spaces
                    $innerValue = substr($value, 1, -1);
                    $innerValue = str_replace('_', ' ', $innerValue);
                    $valueObj = [
                        'kind' => 'arbitrary',
                        'value' => $innerValue,
                        'dataType' => null,
                    ];
                } else {
                    // Check if it's a fraction like 1/2
                    $fraction = null;
                    if (strpos($value, '/') !== false) {
                        $fraction = $value;
                    }
                    $valueObj = [
                        'kind' => 'named',
                        'value' => $value,
                        'fraction' => $fraction,
                    ];
                }

                return [$maybeRoot, $valueObj];
            }

            $idx = strrpos(substr($candidate, 0, $idx), '-');
        }

        return null;
    }
}
