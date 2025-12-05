<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\Utils\addWhitespaceAroundMathOperators;
use function TailwindPHP\Utils\hasMathFn;

/**
 * Tests for math-operators.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class math_operators extends TestCase
{
    // ==================================================
    // hasMathFn tests
    // ==================================================

    #[Test]
    public function has_math_fn_calc(): void
    {
        $this->assertTrue(hasMathFn('calc(100% - 10px)'));
    }

    #[Test]
    public function has_math_fn_min(): void
    {
        $this->assertTrue(hasMathFn('min(100px, 50%)'));
    }

    #[Test]
    public function has_math_fn_max(): void
    {
        $this->assertTrue(hasMathFn('max(100px, 50%)'));
    }

    #[Test]
    public function has_math_fn_clamp(): void
    {
        $this->assertTrue(hasMathFn('clamp(10px, 5vw, 50px)'));
    }

    #[Test]
    public function has_math_fn_sin(): void
    {
        $this->assertTrue(hasMathFn('sin(45deg)'));
    }

    #[Test]
    public function has_math_fn_cos(): void
    {
        $this->assertTrue(hasMathFn('cos(45deg)'));
    }

    #[Test]
    public function has_math_fn_sqrt(): void
    {
        $this->assertTrue(hasMathFn('sqrt(4)'));
    }

    #[Test]
    public function has_math_fn_round(): void
    {
        $this->assertTrue(hasMathFn('round(1.5)'));
    }

    #[Test]
    public function has_math_fn_false_for_no_paren(): void
    {
        $this->assertFalse(hasMathFn('calc'));
        $this->assertFalse(hasMathFn('100px'));
    }

    #[Test]
    public function has_math_fn_false_for_non_math_fn(): void
    {
        $this->assertFalse(hasMathFn('url(image.png)'));
        $this->assertFalse(hasMathFn('var(--color)'));
    }

    // ==================================================
    // addWhitespaceAroundMathOperators tests
    // ==================================================

    #[Test]
    public function adds_whitespace_in_calc(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(100%-10px)');
        $this->assertSame('calc(100% - 10px)', $result);
    }

    #[Test]
    public function adds_whitespace_multiplication(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(2*3)');
        $this->assertSame('calc(2 * 3)', $result);
    }

    #[Test]
    public function adds_whitespace_division(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(6/2)');
        $this->assertSame('calc(6 / 2)', $result);
    }

    #[Test]
    public function adds_whitespace_addition(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(1+2)');
        $this->assertSame('calc(1 + 2)', $result);
    }

    #[Test]
    public function preserves_negative_numbers(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(-10px)');
        $this->assertStringContainsString('-10px', $result);
    }

    #[Test]
    public function handles_nested_calc(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(100%-calc(10px+5px))');
        $this->assertStringContainsString(' - ', $result);
        $this->assertStringContainsString(' + ', $result);
    }

    #[Test]
    public function normalizes_comma_spacing(): void
    {
        $result = addWhitespaceAroundMathOperators('clamp(10px,5vw,50px)');
        $this->assertSame('clamp(10px, 5vw, 50px)', $result);
    }

    #[Test]
    public function no_change_for_non_math(): void
    {
        $input = '100px';
        $this->assertSame($input, addWhitespaceAroundMathOperators($input));
    }

    #[Test]
    public function no_change_for_url(): void
    {
        $input = 'url(image.png)';
        $this->assertSame($input, addWhitespaceAroundMathOperators($input));
    }

    #[Test]
    public function preserves_scientific_notation(): void
    {
        $result = addWhitespaceAroundMathOperators('calc(1e+5)');
        // Should not add spaces around the + in scientific notation
        $this->assertStringContainsString('1e+5', $result);
    }
}
