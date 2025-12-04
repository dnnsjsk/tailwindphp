<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\Utils\inferDataType;
use function TailwindPHP\Utils\isAbsoluteSize;
use function TailwindPHP\Utils\isAngle;
use function TailwindPHP\Utils\isFamilyName;
use function TailwindPHP\Utils\isFraction;
use function TailwindPHP\Utils\isGenericName;
use function TailwindPHP\Utils\isImage;
use function TailwindPHP\Utils\isLengthValue;
use function TailwindPHP\Utils\isLineWidth;
use function TailwindPHP\Utils\isMultipleOf;
use function TailwindPHP\Utils\isNumberValue;
use function TailwindPHP\Utils\isPercentage;
use function TailwindPHP\Utils\isPositiveInteger;
use function TailwindPHP\Utils\isRelativeSize;
use function TailwindPHP\Utils\isStrictPositiveInteger;
use function TailwindPHP\Utils\isUrl;
use function TailwindPHP\Utils\isValidOpacityValue;
use function TailwindPHP\Utils\isValidSpacingMultiplier;
use function TailwindPHP\Utils\isVector;

/**
 * Tests for infer-data-type.php.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class infer_data_type extends TestCase
{
    // =========================================================================
    // inferDataType tests
    // =========================================================================

    #[Test]
    public function infer_color(): void
    {
        $this->assertSame('color', inferDataType('#ff0000', ['color', 'length']));
        $this->assertSame('color', inferDataType('red', ['color', 'length']));
    }

    #[Test]
    public function infer_length(): void
    {
        $this->assertSame('length', inferDataType('10px', ['length', 'color']));
        $this->assertSame('length', inferDataType('1.5rem', ['length']));
    }

    #[Test]
    public function infer_percentage(): void
    {
        $this->assertSame('percentage', inferDataType('50%', ['percentage', 'length']));
    }

    #[Test]
    public function infer_number(): void
    {
        $this->assertSame('number', inferDataType('1.5', ['number']));
        $this->assertSame('number', inferDataType('42', ['number']));
    }

    #[Test]
    public function infer_returns_null_for_var(): void
    {
        $this->assertNull(inferDataType('var(--color)', ['color']));
    }

    #[Test]
    public function infer_returns_null_for_no_match(): void
    {
        $this->assertNull(inferDataType('hello', ['color', 'length']));
    }

    // =========================================================================
    // isUrl tests
    // =========================================================================

    #[Test]
    public function is_url_valid(): void
    {
        $this->assertTrue(isUrl('url(image.png)'));
        $this->assertTrue(isUrl('url("image.png")'));
    }

    #[Test]
    public function is_url_invalid(): void
    {
        $this->assertFalse(isUrl('image.png'));
        // url() with empty contents is still considered a url pattern match
    }

    // =========================================================================
    // isLineWidth tests
    // =========================================================================

    #[Test]
    public function is_line_width_keywords(): void
    {
        $this->assertTrue(isLineWidth('thin'));
        $this->assertTrue(isLineWidth('medium'));
        $this->assertTrue(isLineWidth('thick'));
    }

    #[Test]
    public function is_line_width_length(): void
    {
        $this->assertTrue(isLineWidth('1px'));
        $this->assertTrue(isLineWidth('2px'));
    }

    // =========================================================================
    // isImage tests
    // =========================================================================

    #[Test]
    public function is_image_url(): void
    {
        $this->assertTrue(isImage('url(image.png)'));
    }

    #[Test]
    public function is_image_gradient(): void
    {
        $this->assertTrue(isImage('linear-gradient(to right, red, blue)'));
        $this->assertTrue(isImage('radial-gradient(circle, red, blue)'));
        $this->assertTrue(isImage('conic-gradient(red, blue)'));
    }

    // =========================================================================
    // isGenericName tests
    // =========================================================================

    #[Test]
    public function is_generic_name(): void
    {
        $this->assertTrue(isGenericName('serif'));
        $this->assertTrue(isGenericName('sans-serif'));
        $this->assertTrue(isGenericName('monospace'));
        $this->assertTrue(isGenericName('system-ui'));
    }

    #[Test]
    public function is_generic_name_invalid(): void
    {
        $this->assertFalse(isGenericName('Arial'));
        $this->assertFalse(isGenericName('custom'));
    }

    // =========================================================================
    // isFamilyName tests
    // =========================================================================

    #[Test]
    public function is_family_name_valid(): void
    {
        $this->assertTrue(isFamilyName('Arial'));
        $this->assertTrue(isFamilyName('Times New Roman'));
        $this->assertTrue(isFamilyName('Arial, sans-serif'));
    }

    #[Test]
    public function is_family_name_invalid_starts_with_digit(): void
    {
        $this->assertFalse(isFamilyName('123Font'));
    }

    // =========================================================================
    // isAbsoluteSize tests
    // =========================================================================

    #[Test]
    public function is_absolute_size(): void
    {
        $this->assertTrue(isAbsoluteSize('xx-small'));
        $this->assertTrue(isAbsoluteSize('medium'));
        $this->assertTrue(isAbsoluteSize('xxx-large'));
        $this->assertFalse(isAbsoluteSize('huge'));
    }

    // =========================================================================
    // isRelativeSize tests
    // =========================================================================

    #[Test]
    public function is_relative_size(): void
    {
        $this->assertTrue(isRelativeSize('larger'));
        $this->assertTrue(isRelativeSize('smaller'));
        $this->assertFalse(isRelativeSize('big'));
    }

    // =========================================================================
    // isNumberValue tests
    // =========================================================================

    #[Test]
    public function is_number_value(): void
    {
        $this->assertTrue(isNumberValue('1.5'));
        $this->assertTrue(isNumberValue('42'));
        $this->assertTrue(isNumberValue('-10'));
        $this->assertTrue(isNumberValue('1e5'));
    }

    // =========================================================================
    // isPercentage tests
    // =========================================================================

    #[Test]
    public function is_percentage(): void
    {
        $this->assertTrue(isPercentage('50%'));
        $this->assertTrue(isPercentage('100%'));
        $this->assertTrue(isPercentage('-10%'));
        $this->assertFalse(isPercentage('50'));
    }

    // =========================================================================
    // isFraction tests
    // =========================================================================

    #[Test]
    public function is_fraction(): void
    {
        $this->assertTrue(isFraction('1/2'));
        $this->assertTrue(isFraction('16 / 9'));
        $this->assertFalse(isFraction('1.5'));
    }

    // =========================================================================
    // isLengthValue tests
    // =========================================================================

    #[Test]
    public function is_length_value(): void
    {
        $this->assertTrue(isLengthValue('10px'));
        $this->assertTrue(isLengthValue('1.5rem'));
        $this->assertTrue(isLengthValue('100vh'));
        $this->assertFalse(isLengthValue('10'));
    }

    // =========================================================================
    // isAngle tests
    // =========================================================================

    #[Test]
    public function is_angle(): void
    {
        $this->assertTrue(isAngle('45deg'));
        $this->assertTrue(isAngle('1rad'));
        $this->assertTrue(isAngle('0.5turn'));
        $this->assertFalse(isAngle('45'));
    }

    // =========================================================================
    // isVector tests
    // =========================================================================

    #[Test]
    public function is_vector(): void
    {
        $this->assertTrue(isVector('1 0 0'));
        $this->assertTrue(isVector('0 1 0'));
        $this->assertFalse(isVector('1 0'));
        $this->assertFalse(isVector('1'));
    }

    // =========================================================================
    // isPositiveInteger tests
    // =========================================================================

    #[Test]
    public function is_positive_integer(): void
    {
        $this->assertTrue(isPositiveInteger('0'));
        $this->assertTrue(isPositiveInteger('42'));
        $this->assertFalse(isPositiveInteger('-1'));
        $this->assertFalse(isPositiveInteger('1.5'));
    }

    // =========================================================================
    // isStrictPositiveInteger tests
    // =========================================================================

    #[Test]
    public function is_strict_positive_integer(): void
    {
        $this->assertTrue(isStrictPositiveInteger('1'));
        $this->assertTrue(isStrictPositiveInteger('42'));
        $this->assertFalse(isStrictPositiveInteger('0'));
        $this->assertFalse(isStrictPositiveInteger('-1'));
    }

    // =========================================================================
    // isValidSpacingMultiplier tests
    // =========================================================================

    #[Test]
    public function is_valid_spacing_multiplier(): void
    {
        $this->assertTrue(isValidSpacingMultiplier('0'));
        $this->assertTrue(isValidSpacingMultiplier('0.25'));
        $this->assertTrue(isValidSpacingMultiplier('0.5'));
        $this->assertTrue(isValidSpacingMultiplier('1'));
        $this->assertFalse(isValidSpacingMultiplier('0.3'));
    }

    // =========================================================================
    // isValidOpacityValue tests
    // =========================================================================

    #[Test]
    public function is_valid_opacity_value(): void
    {
        $this->assertTrue(isValidOpacityValue('0'));
        $this->assertTrue(isValidOpacityValue('50'));
        $this->assertTrue(isValidOpacityValue('100'));
        $this->assertFalse(isValidOpacityValue('101'));
        $this->assertFalse(isValidOpacityValue('-1'));
    }

    // =========================================================================
    // isMultipleOf tests
    // =========================================================================

    #[Test]
    public function is_multiple_of(): void
    {
        $this->assertTrue(isMultipleOf('0', 0.5));
        $this->assertTrue(isMultipleOf('0.5', 0.5));
        $this->assertTrue(isMultipleOf('1', 0.5));
        $this->assertFalse(isMultipleOf('0.3', 0.5));
    }
}
