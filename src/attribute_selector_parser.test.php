<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use function TailwindPHP\AttributeSelectorParser\parse;

/**
 * Tests for attribute-selector-parser.php
 *
 * Port of: packages/tailwindcss/src/attribute-selector-parser.test.ts
 */
class attribute_selector_parser extends TestCase
{
    /**
     * Invalid attribute selectors that should return null.
     */
    public static function invalidSelectorProvider(): array
    {
        return [
            [''],
            [']'],
            ['[]'],
            ['['],
            ['="value"'],
            ['data-foo]'],
            ['[data-foo'],
            ['[data-foo="foo]'],
            ['[data-foo * = foo]'],
            ['[data-foo*=]'],
            ['[data-foo=value x]'],
            ['[data-foo=value ix]'],
        ];
    }

    #[Test]
    #[DataProvider('invalidSelectorProvider')]
    public function should_parse_an_invalid_attribute_selector_as_null(string $input): void
    {
        $this->assertNull(parse($input));
    }

    /**
     * Valid attribute selectors with expected results.
     */
    public static function validSelectorProvider(): array
    {
        return [
            [
                '[data-foo]',
                ['attribute' => 'data-foo', 'operator' => null, 'quote' => null, 'value' => null, 'sensitivity' => null],
            ],
            [
                '[ data-foo ]',
                ['attribute' => 'data-foo', 'operator' => null, 'quote' => null, 'value' => null, 'sensitivity' => null],
            ],
            [
                '[data-state=expanded]',
                ['attribute' => 'data-state', 'operator' => '=', 'quote' => null, 'value' => 'expanded', 'sensitivity' => null],
            ],
            [
                '[data-state = expanded ]',
                ['attribute' => 'data-state', 'operator' => '=', 'quote' => null, 'value' => 'expanded', 'sensitivity' => null],
            ],
            [
                '[data-state*="expanded"]',
                ['attribute' => 'data-state', 'operator' => '*=', 'quote' => '"', 'value' => 'expanded', 'sensitivity' => null],
            ],
            [
                '[data-state*="expanded"i]',
                ['attribute' => 'data-state', 'operator' => '*=', 'quote' => '"', 'value' => 'expanded', 'sensitivity' => 'i'],
            ],
            [
                '[data-state*=expanded i]',
                ['attribute' => 'data-state', 'operator' => '*=', 'quote' => null, 'value' => 'expanded', 'sensitivity' => 'i'],
            ],
        ];
    }

    #[Test]
    #[DataProvider('validSelectorProvider')]
    public function should_parse_correctly(string $selector, array $expected): void
    {
        $this->assertEquals($expected, parse($selector));
    }

    #[Test]
    public function should_work_with_a_real_world_example(): void
    {
        $this->assertEquals(
            [
                'attribute' => 'data-url',
                'operator' => '$=',
                'quote' => '"',
                'value' => '.com',
                'sensitivity' => 'i',
            ],
            parse('[data-url$=".com"i]')
        );
    }
}
