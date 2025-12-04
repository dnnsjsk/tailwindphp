<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\Utils\isValidArbitrary;

/**
 * Tests for is-valid-arbitrary.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class is_valid_arbitrary extends TestCase
{
    #[Test]
    public function valid_simple_value(): void
    {
        $this->assertTrue(isValidArbitrary('red'));
        $this->assertTrue(isValidArbitrary('10px'));
        $this->assertTrue(isValidArbitrary('1rem'));
    }

    #[Test]
    public function valid_with_parens(): void
    {
        $this->assertTrue(isValidArbitrary('calc(100% - 10px)'));
        $this->assertTrue(isValidArbitrary('var(--color)'));
        $this->assertTrue(isValidArbitrary('rgb(255, 0, 0)'));
    }

    #[Test]
    public function valid_with_brackets(): void
    {
        $this->assertTrue(isValidArbitrary('[data-active]'));
        $this->assertTrue(isValidArbitrary('url(image.png)'));
    }

    #[Test]
    public function valid_nested_parens(): void
    {
        $this->assertTrue(isValidArbitrary('calc(100% - calc(10px + 5px))'));
    }

    #[Test]
    public function valid_with_quotes(): void
    {
        $this->assertTrue(isValidArbitrary('"hello world"'));
        $this->assertTrue(isValidArbitrary("'hello world'"));
    }

    #[Test]
    public function valid_escaped_characters(): void
    {
        $this->assertTrue(isValidArbitrary('\\(escaped\\)'));
        $this->assertTrue(isValidArbitrary('\\[escaped\\]'));
    }

    #[Test]
    public function unbalanced_open_parens_still_valid(): void
    {
        // The algorithm checks for unbalanced closing, not opening
        $this->assertTrue(isValidArbitrary('calc(100%'));
    }

    #[Test]
    public function invalid_unbalanced_close_paren(): void
    {
        $this->assertFalse(isValidArbitrary('100%)'));
    }

    #[Test]
    public function unbalanced_open_brackets_still_valid(): void
    {
        // The algorithm checks for unbalanced closing, not opening
        $this->assertTrue(isValidArbitrary('[data'));
    }

    #[Test]
    public function invalid_unbalanced_close_bracket(): void
    {
        $this->assertFalse(isValidArbitrary('data]'));
    }

    #[Test]
    public function invalid_top_level_semicolon(): void
    {
        $this->assertFalse(isValidArbitrary('color: red;'));
    }

    #[Test]
    public function valid_semicolon_inside_parens(): void
    {
        $this->assertTrue(isValidArbitrary('url(data:text/css;base64,abc)'));
    }

    #[Test]
    public function valid_empty_string(): void
    {
        $this->assertTrue(isValidArbitrary(''));
    }

    #[Test]
    public function valid_complex_css_function(): void
    {
        $this->assertTrue(isValidArbitrary('linear-gradient(to right, red, blue)'));
    }

    #[Test]
    public function invalid_unbalanced_curly_braces(): void
    {
        // Curly braces inside arbitrary values are not balanced-checked the same way
        $this->assertFalse(isValidArbitrary('{color:red}'));
    }
}
