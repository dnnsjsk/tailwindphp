<?php

declare(strict_types=1);

namespace TailwindPHP\DesignSystem;

use function TailwindPHP\Ast\toCss;

use TailwindPHP\Candidate\DesignSystemInterface as CandidateDesignSystemInterface;

use function TailwindPHP\Candidate\parseCandidate;
use function TailwindPHP\Candidate\parseVariant;
use function TailwindPHP\Compile\compileAstNodes;
use function TailwindPHP\Compile\compileCandidates;

use TailwindPHP\Theme;
use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Utils\DefaultMap;
use TailwindPHP\Variants\Variants;

// Load utility registration files
require_once __DIR__ . '/utilities/accessibility.php';
require_once __DIR__ . '/utilities/layout.php';
require_once __DIR__ . '/utilities/flexbox.php';
require_once __DIR__ . '/utilities/spacing.php';
require_once __DIR__ . '/utilities/sizing.php';
require_once __DIR__ . '/utilities/typography.php';
require_once __DIR__ . '/utilities/borders.php';

/**
 * Design System - Combines theme, utilities, and variants.
 *
 * Port of: packages/tailwindcss/src/design-system.ts
 *
 * @port-deviation:structure TypeScript uses object literal with methods.
 * PHP uses class-based implementation with interfaces for type safety.
 *
 * @port-deviation:invalidCandidates TypeScript uses Set<string>.
 * PHP uses array as property since PHP doesn't have native Set.
 *
 * @port-deviation:intellisense TypeScript includes getClassList(), getVariants(), getClassOrder()
 * for IDE integration. PHP stubs these methods since IDE tooling is not primary use case.
 *
 * @port-deviation:substitution TypeScript calls substituteFunctions() and substituteAtVariant()
 * in compiledAstNodes cache callback. PHP handles these in the main compilation pipeline.
 */

const COMPILE_AST_FLAGS_NONE = 0;
const COMPILE_AST_FLAGS_RESPECT_IMPORTANT = 1 << 0;

/**
 * DesignSystem interface representing the complete design system.
 */
interface DesignSystemInterface
{
    public function getTheme(): Theme;
    public function getUtilities(): Utilities;
    public function getVariants(): Variants;
    public function getInvalidCandidates(): array;
    public function isImportant(): bool;
    public function setImportant(bool $important): void;
    public function parseCandidate(string $candidate): array;
    public function parseVariant(string $variant): ?array;
    public function compileAstNodes(array $candidate, int $flags = COMPILE_AST_FLAGS_RESPECT_IMPORTANT): array;
    public function printCandidate(array $candidate): string;
    public function printVariant(array $variant): string;
    public function resolveThemeValue(string $path, bool $forceInline = true): ?string;
    public function trackUsedVariables(string $raw): void;
    public function candidatesToCss(array $classes): array;
}

/**
 * Design System implementation.
 */
class DesignSystem implements DesignSystemInterface, CandidateDesignSystemInterface
{
    private Theme $theme;
    private Utilities $utilities;
    private Variants $variants;
    private array $invalidCandidates = [];
    private bool $important = false;
    private DefaultMap $parsedVariants;
    private DefaultMap $parsedCandidates;
    private DefaultMap $compiledAstNodes;
    private DefaultMap $trackUsedVariables;
    private array $storage = [];

    public function __construct(Theme $theme, Utilities $utilities, Variants $variants)
    {
        $this->theme = $theme;
        $this->utilities = $utilities;
        $this->variants = $variants;

        // Initialize lazy caches
        $designSystem = $this;

        $this->parsedVariants = new DefaultMap(function ($variant) use ($designSystem) {
            return parseVariant($variant, $designSystem);
        });

        $this->parsedCandidates = new DefaultMap(function ($candidate) use ($designSystem) {
            return iterator_to_array(parseCandidate($candidate, $designSystem));
        });

        $this->compiledAstNodes = new DefaultMap(function ($flags) use ($designSystem) {
            return new DefaultMap(function ($candidate) use ($designSystem, $flags) {
                return compileAstNodes($candidate, $designSystem, $flags);
            });
        });

        $this->trackUsedVariables = new DefaultMap(function ($raw) use ($designSystem) {
            foreach (extractUsedVariables($raw) as $variable) {
                $designSystem->theme->markUsedVariable($variable);
            }

            return true;
        });
    }

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function getUtilities(): Utilities
    {
        return $this->utilities;
    }

    public function getVariants(): Variants
    {
        return $this->variants;
    }

    public function getInvalidCandidates(): array
    {
        return $this->invalidCandidates;
    }

    public function addInvalidCandidate(string $candidate): void
    {
        $this->invalidCandidates[$candidate] = true;
    }

    public function hasInvalidCandidate(string $candidate): bool
    {
        return isset($this->invalidCandidates[$candidate]);
    }

    public function isImportant(): bool
    {
        return $this->important;
    }

    public function setImportant(bool $important): void
    {
        $this->important = $important;
    }

    public function parseCandidate(string $candidate): array
    {
        return $this->parsedCandidates->get($candidate);
    }

    public function parseVariant(string $variant): ?array
    {
        return $this->parsedVariants->get($variant);
    }

    public function compileAstNodes(array $candidate, int $flags = COMPILE_AST_FLAGS_RESPECT_IMPORTANT): array
    {
        return $this->compiledAstNodes->get($flags)->get($candidate);
    }

    public function printCandidate(array $candidate): string
    {
        return \TailwindPHP\printCandidate($this, $candidate);
    }

    public function printVariant(array $variant): string
    {
        return \TailwindPHP\printVariant($variant);
    }

    public function resolveThemeValue(string $path, bool $forceInline = true): ?string
    {
        // Extract an eventual modifier from the path. e.g.:
        // - "--color-red-500 / 50%" -> "50%"
        $lastSlash = strrpos($path, '/');
        $modifier = null;

        if ($lastSlash !== false) {
            $modifier = trim(substr($path, $lastSlash + 1));
            $path = trim(substr($path, 0, $lastSlash));
        }

        $themeValue = $this->theme->resolve(
            null,
            [$path],
            $forceInline ? Theme::OPTIONS_INLINE : Theme::OPTIONS_NONE,
        );

        // Apply the opacity modifier if present
        if ($modifier !== null && $themeValue !== null) {
            return withAlpha($themeValue, $modifier);
        }

        return $themeValue;
    }

    public function trackUsedVariables(string $raw): void
    {
        $this->trackUsedVariables->get($raw);
    }

    public function candidatesToCss(array $classes): array
    {
        $result = [];

        foreach ($classes as $className) {
            $wasInvalid = false;

            $compiled = compileCandidates([$className], $this, [
                'onInvalidCandidate' => function () use (&$wasInvalid) {
                    $wasInvalid = true;
                },
            ]);

            $astNodes = $compiled['astNodes'];

            if (empty($astNodes) || $wasInvalid) {
                $result[] = null;
            } else {
                $result[] = toCss($astNodes);
            }
        }

        return $result;
    }

    public function getStorage(): array
    {
        return $this->storage;
    }

    public function setStorage(string $key, $value): void
    {
        $this->storage[$key] = $value;
    }

    /**
     * Get variant order map for sorting.
     *
     * @return array<string, int>
     */
    public function getVariantOrder(): array
    {
        $order = [];
        foreach ($this->variants->variants as $name => $info) {
            $order[$name] = $info['order'] ?? 0;
        }

        return $order;
    }
}

/**
 * Build a complete design system from a theme.
 *
 * @param Theme $theme
 * @return DesignSystem
 */
function buildDesignSystem(Theme $theme): DesignSystem
{
    $utilities = createUtilities($theme);
    $variants = createVariants($theme);

    return new DesignSystem($theme, $utilities, $variants);
}

/**
 * Apply opacity to a color using `color-mix`.
 *
 * @param string $value
 * @param string $alpha
 * @return string
 */
function withAlpha(string $value, string $alpha): string
{
    // Convert numeric values (like `0.5`) to percentages (like `50%`) so they
    // work properly with `color-mix`. Assume anything that isn't a number is
    // safe to pass through as-is, like `var(--my-opacity)`.
    if (is_numeric($alpha)) {
        $alphaAsNumber = (float) $alpha;
        $alpha = ($alphaAsNumber * 100) . '%';
    }

    // No need for `color-mix` if the alpha is `100%`
    if ($alpha === '100%') {
        return $value;
    }

    return "color-mix(in oklab, {$value} {$alpha}, transparent)";
}

/**
 * Replace alpha in a color using `color-mix`.
 *
 * @param string $value
 * @param string $alpha
 * @return string
 */
function replaceAlpha(string $value, string $alpha): string
{
    // Convert numeric values (like `0.5`) to percentages (like `50%`) so they
    // work properly with `color-mix`. Assume anything that isn't a number is
    // safe to pass through as-is, like `var(--my-opacity)`.
    if (is_numeric($alpha)) {
        $alphaAsNumber = (float) $alpha;
        $alpha = ($alphaAsNumber * 100) . '%';
    }

    // Remove the opacity from the value if it exists
    // e.g., `rgb(255 0 0 / 50%)` -> `rgb(255 0 0)`
    $value = preg_replace('/\s*\/\s*[\d.]+%?\s*\)$/', ')', $value);

    return "color-mix(in oklab, {$value} {$alpha}, transparent)";
}

/**
 * Extract used variables from a raw string.
 *
 * @param string $raw
 * @return \Generator<string>
 */
function extractUsedVariables(string $raw): \Generator
{
    // Match CSS custom properties like --color-red-500
    preg_match_all('/var\(\s*(--[a-zA-Z0-9_-]+)/', $raw, $matches);

    foreach ($matches[1] ?? [] as $match) {
        yield $match;
    }
}

/**
 * Create utilities with all built-in utility definitions.
 * This is a placeholder that will be populated with all utilities.
 *
 * @param Theme $theme
 * @return Utilities
 */
function createUtilities(Theme $theme): Utilities
{
    $utilities = new Utilities();

    // Register all built-in utilities
    // This will be expanded to include all utilities from utilities.ts
    $builder = new \TailwindPHP\Utilities\UtilityBuilder($utilities, $theme);

    // Register accessibility utilities
    \TailwindPHP\Utilities\registerAccessibilityUtilities($builder);

    // Register layout utilities
    \TailwindPHP\Utilities\registerLayoutUtilities($builder);

    // Register flexbox & grid utilities
    \TailwindPHP\Utilities\registerFlexboxUtilities($builder);

    // Register spacing utilities
    \TailwindPHP\Utilities\registerSpacingUtilities($builder);

    // Register sizing utilities
    \TailwindPHP\Utilities\registerSizingUtilities($builder);

    // Register typography utilities
    \TailwindPHP\Utilities\registerTypographyUtilities($builder);

    // Register border utilities
    \TailwindPHP\Utilities\registerBorderUtilities($builder);

    // Register effects utilities
    \TailwindPHP\Utilities\registerEffectsUtilities($builder);

    // Register tables utilities
    \TailwindPHP\Utilities\registerTablesUtilities($builder);

    // Register transitions utilities
    \TailwindPHP\Utilities\registerTransitionsUtilities($builder);

    // Register transforms utilities
    \TailwindPHP\Utilities\registerTransformsUtilities($builder);

    // Register filters utilities
    \TailwindPHP\Utilities\registerFiltersUtilities($builder);

    // Register interactivity utilities
    \TailwindPHP\Utilities\registerInteractivityUtilities($builder);

    // Register SVG utilities
    \TailwindPHP\Utilities\registerSvgUtilities($builder);

    // Register background utilities
    \TailwindPHP\Utilities\registerBackgroundUtilities($builder);

    // Register mask utilities
    \TailwindPHP\Utilities\registerMaskUtilities($builder);

    return $utilities;
}

/**
 * Create variants with all built-in variant definitions.
 *
 * @param Theme $theme
 * @return Variants
 */
function createVariants(Theme $theme): Variants
{
    return \TailwindPHP\Variants\createVariants($theme);
}
