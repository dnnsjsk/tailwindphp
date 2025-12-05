<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\CandidateParser\CandidateParser as Parser;
use TailwindPHP\Utilities\Utilities;

/**
 * Tests for CandidateParser.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class CandidateParser extends TestCase
{
    private Utilities $utilities;

    private Parser $parser;

    protected function setUp(): void
    {
        $this->utilities = new Utilities();

        // Register some test utilities
        $this->utilities->static('flex', fn () => [['property' => 'display', 'value' => 'flex']]);
        $this->utilities->static('hidden', fn () => [['property' => 'display', 'value' => 'none']]);
        $this->utilities->static('-translate-full', fn () => [['property' => 'transform', 'value' => 'translate(-100%)']]);

        $this->utilities->functional('p', fn ($value) => [['property' => 'padding', 'value' => $value]]);
        $this->utilities->functional('m', fn ($value) => [['property' => 'margin', 'value' => $value]]);
        $this->utilities->functional('bg', fn ($value) => [['property' => 'background', 'value' => $value]]);
        $this->utilities->functional('text', fn ($value) => [['property' => 'color', 'value' => $value]]);
        $this->utilities->functional('w', fn ($value) => [['property' => 'width', 'value' => $value]]);
        $this->utilities->functional('rounded', fn ($value) => [['property' => 'border-radius', 'value' => $value]]);
        $this->utilities->functional('rounded-t', fn ($value) => [['property' => 'border-top-radius', 'value' => $value]]);
        $this->utilities->functional('rotate', fn ($value) => [['property' => 'transform', 'value' => "rotate({$value})"]]);

        $this->parser = new Parser($this->utilities);
    }

    // ==================================================
    // Static utility tests
    // ==================================================

    #[Test]
    public function parse_static_utility(): void
    {
        $result = $this->parser->parse('flex');

        $this->assertNotNull($result);
        $this->assertSame('static', $result['kind']);
        $this->assertSame('flex', $result['root']);
        $this->assertSame('flex', $result['raw']);
        $this->assertFalse($result['important']);
    }

    #[Test]
    public function parse_static_utility_with_important_suffix(): void
    {
        $result = $this->parser->parse('flex!');

        $this->assertNotNull($result);
        $this->assertSame('static', $result['kind']);
        $this->assertSame('flex', $result['root']);
        $this->assertTrue($result['important']);
    }

    #[Test]
    public function parse_static_utility_with_important_prefix(): void
    {
        $result = $this->parser->parse('!flex');

        $this->assertNotNull($result);
        $this->assertSame('static', $result['kind']);
        $this->assertSame('flex', $result['root']);
        $this->assertTrue($result['important']);
    }

    #[Test]
    public function parse_static_utility_starting_with_dash(): void
    {
        $result = $this->parser->parse('-translate-full');

        $this->assertNotNull($result);
        $this->assertSame('static', $result['kind']);
        $this->assertSame('-translate-full', $result['root']);
    }

    #[Test]
    public function parse_returns_null_for_unknown_utility(): void
    {
        $result = $this->parser->parse('unknown');

        $this->assertNull($result);
    }

    // ==================================================
    // Functional utility tests
    // ==================================================

    #[Test]
    public function parse_functional_utility_with_named_value(): void
    {
        $result = $this->parser->parse('p-4');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('p', $result['root']);
        $this->assertSame('named', $result['value']['kind']);
        $this->assertSame('4', $result['value']['value']);
        $this->assertFalse($result['important']);
    }

    #[Test]
    public function parse_functional_utility_with_arbitrary_value(): void
    {
        $result = $this->parser->parse('p-[1rem]');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('p', $result['root']);
        $this->assertSame('arbitrary', $result['value']['kind']);
        $this->assertSame('1rem', $result['value']['value']);
    }

    #[Test]
    public function parse_functional_utility_arbitrary_decodes_underscores(): void
    {
        $result = $this->parser->parse('p-[10px_20px]');

        $this->assertNotNull($result);
        $this->assertSame('10px 20px', $result['value']['value']);
    }

    #[Test]
    public function parse_functional_utility_with_fraction(): void
    {
        $result = $this->parser->parse('w-1/2');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('w', $result['root']);
        $this->assertSame('named', $result['value']['kind']);
        $this->assertSame('1/2', $result['value']['value']);
        $this->assertSame('1/2', $result['value']['fraction']);
    }

    #[Test]
    public function parse_functional_utility_with_css_variable_syntax(): void
    {
        $result = $this->parser->parse('rotate-(--my-rotation)');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('rotate', $result['root']);
        $this->assertSame('arbitrary', $result['value']['kind']);
        $this->assertSame('var(--my-rotation)', $result['value']['value']);
    }

    #[Test]
    public function parse_functional_utility_with_numeric_modifier_as_fraction(): void
    {
        // Numeric modifiers (like opacity /50) are treated as part of the value
        // because they can't be distinguished from fractions (like w-1/2)
        $result = $this->parser->parse('bg-red-500/50');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('bg', $result['root']);
        $this->assertSame('named', $result['value']['kind']);
        $this->assertSame('red-500/50', $result['value']['value']);
        $this->assertNull($result['modifier']);
    }

    #[Test]
    public function parse_functional_utility_with_named_modifier(): void
    {
        // Non-numeric modifiers (like /myname) are properly extracted
        $result = $this->parser->parse('bg-red-500/myopacity');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('bg', $result['root']);
        $this->assertSame('named', $result['value']['kind']);
        $this->assertSame('red-500', $result['value']['value']);
        $this->assertNotNull($result['modifier']);
        $this->assertSame('named', $result['modifier']['kind']);
        $this->assertSame('myopacity', $result['modifier']['value']);
    }

    #[Test]
    public function parse_functional_utility_with_important_suffix(): void
    {
        $result = $this->parser->parse('p-4!');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertTrue($result['important']);
    }

    #[Test]
    public function parse_functional_utility_with_important_prefix(): void
    {
        $result = $this->parser->parse('!p-4');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertTrue($result['important']);
    }

    #[Test]
    public function parse_functional_utility_default_value(): void
    {
        // rounded-t exists as a functional utility with default value
        $result = $this->parser->parse('rounded-t');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('rounded-t', $result['root']);
        $this->assertNull($result['value']);
    }

    #[Test]
    public function parse_functional_utility_hyphenated_value(): void
    {
        $result = $this->parser->parse('text-red-500');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('text', $result['root']);
        $this->assertSame('red-500', $result['value']['value']);
    }

    #[Test]
    public function parse_returns_null_for_partial_functional(): void
    {
        // p- without value should not match
        $result = $this->parser->parse('p-');

        $this->assertNull($result);
    }

    // ==================================================
    // Edge cases
    // ==================================================

    #[Test]
    public function parse_handles_multi_part_root(): void
    {
        // rounded-t-lg should find rounded-t as root with lg as value
        $result = $this->parser->parse('rounded-t-lg');

        $this->assertNotNull($result);
        $this->assertSame('functional', $result['kind']);
        $this->assertSame('rounded-t', $result['root']);
        $this->assertSame('lg', $result['value']['value']);
    }

    #[Test]
    public function parse_preserves_raw_candidate(): void
    {
        $result = $this->parser->parse('p-4!');

        $this->assertSame('p-4!', $result['raw']);
    }
}
