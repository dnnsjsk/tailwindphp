<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use TailwindPHP\Theme;
use TailwindPHP\Candidate\DesignSystemInterface;
use TailwindPHP\Candidate\UtilitiesInterface;
use TailwindPHP\Candidate\VariantsInterface;

use function TailwindPHP\Candidate\parseCandidate;
use function TailwindPHP\Candidate\parseVariant;
use function TailwindPHP\Candidate\printCandidate;
use function TailwindPHP\Candidate\printVariant;
use function TailwindPHP\Candidate\cloneCandidate;
use function TailwindPHP\Candidate\cloneVariant;
use function TailwindPHP\Candidate\findRoots;

/**
 * Stub Utilities class for testing.
 */
class StubUtilities implements UtilitiesInterface
{
    private array $staticUtilities = [];
    private array $functionalUtilities = [];

    public function static(string $name, callable $fn): void
    {
        $this->staticUtilities[$name] = $fn;
    }

    public function functional(string $name, callable $fn): void
    {
        $this->functionalUtilities[$name] = $fn;
    }

    public function has(string $name, string $kind): bool
    {
        if ($kind === 'static') {
            return isset($this->staticUtilities[$name]);
        }
        if ($kind === 'functional') {
            return isset($this->functionalUtilities[$name]);
        }
        return false;
    }
}

/**
 * Compound types for variants.
 */
class Compounds
{
    public const StyleRules = 'style_rules';
    public const AtRules = 'at_rules';
}

/**
 * Stub Variants class for testing.
 */
class StubVariants implements VariantsInterface
{
    private array $staticVariants = [];
    private array $functionalVariants = [];
    private array $compoundVariants = [];

    public function static(string $name, callable $fn): void
    {
        $this->staticVariants[$name] = $fn;
    }

    public function functional(string $name, callable $fn): void
    {
        $this->functionalVariants[$name] = $fn;
    }

    public function compound(string $name, string $compoundType, callable $fn): void
    {
        $this->compoundVariants[$name] = ['type' => $compoundType, 'fn' => $fn];
    }

    public function has(string $root): bool
    {
        return isset($this->staticVariants[$root])
            || isset($this->functionalVariants[$root])
            || isset($this->compoundVariants[$root]);
    }

    public function kind(string $root): string
    {
        if (isset($this->staticVariants[$root])) {
            return 'static';
        }
        if (isset($this->functionalVariants[$root])) {
            return 'functional';
        }
        if (isset($this->compoundVariants[$root])) {
            return 'compound';
        }
        return '';
    }

    public function compoundsWith(string $root, array $variant): bool
    {
        if (!isset($this->compoundVariants[$root])) {
            return false;
        }

        $compoundType = $this->compoundVariants[$root]['type'];

        // StyleRules compounds with arbitrary selectors, static variants, functional variants, and other compounds
        if ($compoundType === Compounds::StyleRules) {
            return in_array($variant['kind'], ['arbitrary', 'static', 'functional', 'compound']);
        }

        // AtRules compounds only with at-rule variants
        if ($compoundType === Compounds::AtRules) {
            return $variant['kind'] === 'arbitrary' && isset($variant['selector']) && str_starts_with($variant['selector'], '@');
        }

        return false;
    }
}

/**
 * Stub DesignSystem for testing.
 */
class StubDesignSystem implements DesignSystemInterface
{
    private Theme $theme;
    private UtilitiesInterface $utilities;
    private VariantsInterface $variants;

    public function __construct(
        ?UtilitiesInterface $utilities = null,
        ?VariantsInterface $variants = null,
        ?string $prefix = null
    ) {
        $this->theme = new Theme();
        $this->theme->prefix = $prefix;
        $this->utilities = $utilities ?? new StubUtilities();
        $this->variants = $variants ?? new StubVariants();
    }

    public function getTheme(): Theme
    {
        return $this->theme;
    }

    public function getUtilities(): UtilitiesInterface
    {
        return $this->utilities;
    }

    public function getVariants(): VariantsInterface
    {
        return $this->variants;
    }

    public function parseVariant(string $variant): ?array
    {
        return parseVariant($variant, $this);
    }
}

/**
 * Helper function to run candidate parsing.
 */
function run(
    string $candidate,
    ?StubUtilities $utilities = null,
    ?StubVariants $variants = null,
    ?string $prefix = null
): array {
    $utilities = $utilities ?? new StubUtilities();
    $variants = $variants ?? new StubVariants();

    $designSystem = new StubDesignSystem($utilities, $variants, $prefix);

    return iterator_to_array(parseCandidate($candidate, $designSystem));
}

class candidate extends TestCase
{
    #[Test]
    public function should_skip_unknown_utilities(): void
    {
        $this->assertEquals([], run('unknown-utility'));
    }

    #[Test]
    public function should_skip_unknown_variants(): void
    {
        $this->assertEquals([], run('unknown-variant:flex'));
    }

    #[Test]
    public function should_parse_a_simple_utility(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $result = run('flex', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('static', $result[0]['kind']);
        $this->assertEquals('flex', $result[0]['root']);
        $this->assertEquals('flex', $result[0]['raw']);
        $this->assertFalse($result[0]['important']);
        $this->assertEquals([], $result[0]['variants']);
    }

    #[Test]
    public function should_parse_a_simple_utility_that_should_be_important(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $result = run('flex!', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('static', $result[0]['kind']);
        $this->assertEquals('flex', $result[0]['root']);
        $this->assertTrue($result[0]['important']);
    }

    #[Test]
    public function should_parse_a_simple_utility_that_can_be_negative(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('-translate-x', fn() => []);

        $result = run('-translate-x-4', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('functional', $result[0]['kind']);
        $this->assertEquals('-translate-x', $result[0]['root']);
        $this->assertEquals('named', $result[0]['value']['kind']);
        $this->assertEquals('4', $result[0]['value']['value']);
        $this->assertNull($result[0]['value']['fraction']);
    }

    #[Test]
    public function should_parse_a_simple_utility_with_a_variant(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->static('hover', fn() => null);

        $result = run('hover:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertEquals('static', $result[0]['kind']);
        $this->assertEquals('flex', $result[0]['root']);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('static', $result[0]['variants'][0]['kind']);
        $this->assertEquals('hover', $result[0]['variants'][0]['root']);
    }

    #[Test]
    public function should_parse_a_simple_utility_with_stacked_variants(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->static('hover', fn() => null);
        $variants->static('focus', fn() => null);

        $result = run('focus:hover:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(2, $result[0]['variants']);
        $this->assertEquals('hover', $result[0]['variants'][0]['root']);
        $this->assertEquals('focus', $result[0]['variants'][1]['root']);
    }

    #[Test]
    public function should_parse_a_simple_utility_with_an_arbitrary_variant(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $result = run('[&_p]:flex', $utilities);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('arbitrary', $result[0]['variants'][0]['kind']);
        $this->assertEquals('& p', $result[0]['variants'][0]['selector']);
        $this->assertFalse($result[0]['variants'][0]['relative']);
    }

    #[Test]
    public function should_parse_an_arbitrary_variant_using_the_automatic_var_shorthand(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->functional('supports', fn() => null);

        $result = run('supports-(--test):flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('functional', $result[0]['variants'][0]['kind']);
        $this->assertEquals('supports', $result[0]['variants'][0]['root']);
        $this->assertEquals('arbitrary', $result[0]['variants'][0]['value']['kind']);
        $this->assertEquals('var(--test)', $result[0]['variants'][0]['value']['value']);
    }

    #[Test]
    public function should_parse_a_simple_utility_with_a_parameterized_variant(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->functional('data', fn() => null);

        $result = run('data-[disabled]:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('functional', $result[0]['variants'][0]['kind']);
        $this->assertEquals('data', $result[0]['variants'][0]['root']);
        $this->assertEquals('arbitrary', $result[0]['variants'][0]['value']['kind']);
        $this->assertEquals('disabled', $result[0]['variants'][0]['value']['value']);
    }

    #[Test]
    public function should_parse_compound_variants_with_an_arbitrary_value(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->compound('group', Compounds::StyleRules, fn() => null);

        $result = run('group-[&_p]/parent-name:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('compound', $result[0]['variants'][0]['kind']);
        $this->assertEquals('group', $result[0]['variants'][0]['root']);
        $this->assertEquals('named', $result[0]['variants'][0]['modifier']['kind']);
        $this->assertEquals('parent-name', $result[0]['variants'][0]['modifier']['value']);
        $this->assertEquals('arbitrary', $result[0]['variants'][0]['variant']['kind']);
        $this->assertEquals('& p', $result[0]['variants'][0]['variant']['selector']);
    }

    #[Test]
    public function should_parse_a_simple_utility_with_an_arbitrary_media_variant(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $result = run('[@media(width>=123px)]:flex', $utilities);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('arbitrary', $result[0]['variants'][0]['kind']);
        $this->assertEquals('@media(width>=123px)', $result[0]['variants'][0]['selector']);
    }

    #[Test]
    public function should_skip_arbitrary_variants_where_media_and_other_arbitrary_variants_are_combined(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $result = run('[@media(width>=123px){&:hover}]:flex', $utilities);

        $this->assertEquals([], $result);
    }

    #[Test]
    public function should_parse_a_utility_with_a_modifier(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-red-500/50', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('functional', $result[0]['kind']);
        $this->assertEquals('bg', $result[0]['root']);
        $this->assertEquals('named', $result[0]['modifier']['kind']);
        $this->assertEquals('50', $result[0]['modifier']['value']);
        $this->assertEquals('red-500', $result[0]['value']['value']);
        $this->assertEquals('red-500/50', $result[0]['value']['fraction']);
    }

    #[Test]
    public function should_parse_a_utility_with_an_arbitrary_modifier(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-red-500/[50%]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('arbitrary', $result[0]['modifier']['kind']);
        $this->assertEquals('50%', $result[0]['modifier']['value']);
        $this->assertNull($result[0]['value']['fraction']);
    }

    #[Test]
    public function should_not_parse_a_partial_utility(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);
        $utilities->functional('bg', fn() => []);

        $this->assertEquals([], run('flex-', $utilities));
        $this->assertEquals([], run('bg-', $utilities));
    }

    #[Test]
    public function should_not_parse_static_utilities_with_a_modifier(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $this->assertEquals([], run('flex/foo', $utilities));
    }

    #[Test]
    public function should_not_parse_functional_utilities_with_multiple_modifiers(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $this->assertEquals([], run('bg-red-1/2/3', $utilities));
    }

    #[Test]
    public function should_parse_a_utility_with_an_arbitrary_value(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-[#0088cc]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('functional', $result[0]['kind']);
        $this->assertEquals('bg', $result[0]['root']);
        $this->assertEquals('arbitrary', $result[0]['value']['kind']);
        $this->assertEquals('#0088cc', $result[0]['value']['value']);
        $this->assertNull($result[0]['value']['dataType']);
    }

    #[Test]
    public function should_not_parse_a_utility_with_an_incomplete_arbitrary_value(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $this->assertEquals([], run('bg-[#0088cc', $utilities));
    }

    #[Test]
    public function should_parse_a_utility_with_an_arbitrary_value_with_parens(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-(--my-color)', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('functional', $result[0]['kind']);
        $this->assertEquals('arbitrary', $result[0]['value']['kind']);
        $this->assertEquals('var(--my-color)', $result[0]['value']['value']);
    }

    #[Test]
    public function should_not_parse_a_utility_with_an_arbitrary_value_with_parens_not_starting_with_dashes(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $this->assertEquals([], run('bg-(my-color)', $utilities));
    }

    #[Test]
    public function should_parse_a_utility_with_an_arbitrary_value_including_a_typehint(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-[color:var(--value)]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('color', $result[0]['value']['dataType']);
        $this->assertEquals('var(--value)', $result[0]['value']['value']);
    }

    #[Test]
    public function should_parse_a_utility_with_an_arbitrary_value_with_parens_including_a_typehint(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-(color:--my-color)', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('color', $result[0]['value']['dataType']);
        $this->assertEquals('var(--my-color)', $result[0]['value']['value']);
    }

    #[Test]
    public function should_parse_arbitrary_properties(): void
    {
        $result = run('[color:red]');

        $this->assertCount(1, $result);
        $this->assertEquals('arbitrary', $result[0]['kind']);
        $this->assertEquals('color', $result[0]['property']);
        $this->assertEquals('red', $result[0]['value']);
    }

    #[Test]
    public function should_parse_arbitrary_properties_with_a_modifier(): void
    {
        $result = run('[color:red]/50');

        $this->assertCount(1, $result);
        $this->assertEquals('arbitrary', $result[0]['kind']);
        $this->assertEquals('named', $result[0]['modifier']['kind']);
        $this->assertEquals('50', $result[0]['modifier']['value']);
    }

    #[Test]
    public function should_skip_arbitrary_properties_that_start_with_uppercase(): void
    {
        $this->assertEquals([], run('[Color:red]'));
    }

    #[Test]
    public function should_skip_arbitrary_properties_without_value(): void
    {
        $this->assertEquals([], run('[color]'));
    }

    #[Test]
    public function should_parse_arbitrary_properties_that_are_important(): void
    {
        $result = run('[color:red]!');

        $this->assertCount(1, $result);
        $this->assertTrue($result[0]['important']);
    }

    #[Test]
    public function should_parse_arbitrary_properties_with_a_variant(): void
    {
        $variants = new StubVariants();
        $variants->static('hover', fn() => null);

        $result = run('hover:[color:red]', null, $variants);

        $this->assertCount(1, $result);
        $this->assertEquals('arbitrary', $result[0]['kind']);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('hover', $result[0]['variants'][0]['root']);
    }

    #[Test]
    public function should_replace_underscore_with_space(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('content', fn() => []);

        $result = run('content-["hello_world"]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('"hello world"', $result[0]['value']['value']);
    }

    #[Test]
    public function should_not_replace_escaped_underscore_with_space(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('content', fn() => []);

        $result = run('content-["hello\\_world"]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('"hello_world"', $result[0]['value']['value']);
    }

    #[Test]
    public function should_not_replace_underscore_in_url(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-[no-repeat_url(https://example.com/some_page)]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('no-repeat url(https://example.com/some_page)', $result[0]['value']['value']);
    }

    #[Test]
    public function should_not_replace_underscore_in_first_argument_of_var(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('ml', fn() => []);

        $result = run('ml-[var(--spacing-1_5,_var(--spacing-2_5,_1rem))]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('var(--spacing-1_5, var(--spacing-2_5, 1rem))', $result[0]['value']['value']);
    }

    #[Test]
    public function should_parse_candidates_with_a_prefix(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->static('hover', fn() => null);

        // A prefix is required
        $result = run('flex', $utilities, $variants, 'tw');
        $this->assertEquals([], $result);

        // The prefix always comes first — even before variants
        $result = run('tw:flex', $utilities, $variants, 'tw');
        $this->assertCount(1, $result);
        $this->assertEquals('flex', $result[0]['root']);

        $result = run('tw:hover:flex', $utilities, $variants, 'tw');
        $this->assertCount(1, $result);
        $this->assertEquals('flex', $result[0]['root']);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('hover', $result[0]['variants'][0]['root']);
    }

    #[Test]
    public function should_parse_a_static_variant_starting_with_at(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->static('@lg', fn() => null);

        $result = run('@lg:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('static', $result[0]['variants'][0]['kind']);
        $this->assertEquals('@lg', $result[0]['variants'][0]['root']);
    }

    #[Test]
    public function should_parse_a_functional_variant_starting_with_at(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->functional('@', fn() => null);

        $result = run('@lg:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('functional', $result[0]['variants'][0]['kind']);
        $this->assertEquals('@', $result[0]['variants'][0]['root']);
        $this->assertEquals('lg', $result[0]['variants'][0]['value']['value']);
    }

    #[Test]
    public function should_parse_a_functional_variant_with_a_modifier(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->functional('foo', fn() => null);

        $result = run('foo-bar/50:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertCount(1, $result[0]['variants']);
        $this->assertEquals('named', $result[0]['variants'][0]['modifier']['kind']);
        $this->assertEquals('50', $result[0]['variants'][0]['modifier']['value']);
    }

    public static function emptyArbitraryValuesProvider(): array
    {
        return [
            // Empty arbitrary value
            ['bg-[]'],
            ['bg-()'],
            ['bg-[_]'],
            ['bg-(_)'],

            // Empty arbitrary value, with typehint
            ['bg-[color:]'],
            ['bg-(color:)'],
            ['bg-[color:_]'],
            ['bg-(color:_)'],

            // Empty arbitrary modifier
            ['bg-red-500/[]'],
            ['bg-red-500/()'],
            ['bg-red-500/[_]'],
            ['bg-red-500/(_)'],

            // Empty arbitrary modifier for arbitrary properties
            ['[color:red]/[]'],
            ['[color:red]/()'],
            ['[color:red]/[_]'],
            ['[color:red]/(_)'],
        ];
    }

    #[Test]
    #[DataProvider('emptyArbitraryValuesProvider')]
    public function should_not_parse_invalid_empty_arbitrary_values(string $rawCandidate): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);
        $utilities->functional('bg', fn() => []);

        $variants = new StubVariants();
        $variants->functional('data', fn() => null);
        $variants->compound('group', Compounds::StyleRules, fn() => null);

        $this->assertEquals([], run($rawCandidate, $utilities, $variants));
    }

    public static function invalidArbitraryValuesProvider(): array
    {
        return [
            // Arbitrary properties with `;` or `}`
            ['[color:red;color:blue]'],
            ['[color:red}html{color:blue]'],

            // Arbitrary values that end the declaration
            ['bg-[red;color:blue]'],

            // Arbitrary values that end the block
            ['bg-[red}html{color:blue]'],
        ];
    }

    #[Test]
    #[DataProvider('invalidArbitraryValuesProvider')]
    public function should_not_parse_invalid_arbitrary_values(string $rawCandidate): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);
        $utilities->functional('bg', fn() => []);

        $variants = new StubVariants();
        $variants->functional('data', fn() => null);
        $variants->compound('group', Compounds::StyleRules, fn() => null);

        $this->assertEquals([], run($rawCandidate, $utilities, $variants));
    }

    #[Test]
    public function clone_candidate_preserves_structure(): void
    {
        $candidate = [
            'kind' => 'functional',
            'root' => 'bg',
            'value' => [
                'kind' => 'named',
                'value' => 'red-500',
                'fraction' => null,
            ],
            'modifier' => [
                'kind' => 'named',
                'value' => '50',
            ],
            'variants' => [
                [
                    'kind' => 'static',
                    'root' => 'hover',
                ],
            ],
            'important' => false,
            'raw' => 'hover:bg-red-500/50',
        ];

        $cloned = cloneCandidate($candidate);

        // Structure should be equal
        $this->assertEquals($candidate, $cloned);

        // Modifying the clone should not affect the original
        $cloned['root'] = 'modified';
        $this->assertEquals('bg', $candidate['root']);
    }

    #[Test]
    public function clone_variant_preserves_structure(): void
    {
        $variant = [
            'kind' => 'compound',
            'root' => 'group',
            'modifier' => [
                'kind' => 'named',
                'value' => 'parent',
            ],
            'variant' => [
                'kind' => 'static',
                'root' => 'hover',
            ],
        ];

        $cloned = cloneVariant($variant);

        // Structure should be equal
        $this->assertEquals($variant, $cloned);

        // Modifying the clone should not affect the original
        $cloned['root'] = 'modified';
        $this->assertEquals('group', $variant['root']);
    }

    #[Test]
    public function find_roots_returns_all_matching_permutations(): void
    {
        $known = ['bg', 'bg-red'];

        $roots = iterator_to_array(findRoots('bg-red-500', function ($root) use ($known) {
            return in_array($root, $known);
        }));

        $this->assertCount(2, $roots);
        $this->assertEquals(['bg-red', '500'], $roots[0]);
        $this->assertEquals(['bg', 'red-500'], $roots[1]);
    }

    #[Test]
    public function should_parse_utility_with_an_implicit_variable_as_the_modifier(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-red-500/[var(--value)]', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('arbitrary', $result[0]['modifier']['kind']);
        $this->assertEquals('var(--value)', $result[0]['modifier']['value']);
    }

    #[Test]
    public function should_parse_utility_with_an_implicit_variable_as_modifier_using_shorthand(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        $result = run('bg-red-500/(--value)', $utilities);

        $this->assertCount(1, $result);
        $this->assertEquals('arbitrary', $result[0]['modifier']['kind']);
        $this->assertEquals('var(--value)', $result[0]['modifier']['value']);
    }

    #[Test]
    public function should_not_parse_an_invalid_arbitrary_shorthand_modifier(): void
    {
        $utilities = new StubUtilities();
        $utilities->functional('bg', fn() => []);

        // Completely empty
        $this->assertEquals([], run('bg-red-500/()', $utilities));

        // Invalid due to not starting with --
        $this->assertEquals([], run('bg-red-500/(value)', $utilities));
    }

    #[Test]
    public function should_parse_compound_group_with_itself(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->static('hover', fn() => null);
        $variants->compound('group', Compounds::StyleRules, fn() => null);

        $result = run('group-group-group-hover/parent-name:flex', $utilities, $variants);

        $this->assertCount(1, $result);
        $this->assertEquals('compound', $result[0]['variants'][0]['kind']);
        $this->assertEquals('group', $result[0]['variants'][0]['root']);
        $this->assertEquals('compound', $result[0]['variants'][0]['variant']['kind']);
        $this->assertEquals('group', $result[0]['variants'][0]['variant']['root']);
        $this->assertEquals('compound', $result[0]['variants'][0]['variant']['variant']['kind']);
        $this->assertEquals('hover', $result[0]['variants'][0]['variant']['variant']['variant']['root']);
    }

    #[Test]
    public function should_not_parse_a_partial_variant(): void
    {
        $utilities = new StubUtilities();
        $utilities->static('flex', fn() => []);

        $variants = new StubVariants();
        $variants->static('open', fn() => null);
        $variants->functional('data', fn() => null);

        $this->assertEquals([], run('open-:flex', $utilities, $variants));
        $this->assertEquals([], run('data-:flex', $utilities, $variants));
    }
}
