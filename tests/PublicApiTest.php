<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\cn;
use function TailwindPHP\compose;
use function TailwindPHP\join;
use function TailwindPHP\merge;
use function TailwindPHP\variants;

/**
 * Tests for public API functions exported from TailwindPHP namespace.
 *
 * These are thin wrappers around the library implementations, but we test
 * them to ensure they're properly exported and working.
 */
class PublicApiTest extends TestCase
{
    // =========================================================================
    // cn() tests
    // =========================================================================

    #[Test]
    public function cn_merges_classes(): void
    {
        $this->assertSame('foo bar', cn('foo', 'bar'));
    }

    #[Test]
    public function cn_handles_conditionals(): void
    {
        $this->assertSame('foo bar', cn('foo', ['bar' => true, 'baz' => false]));
    }

    #[Test]
    public function cn_resolves_conflicts(): void
    {
        $this->assertSame('py-1 px-4', cn('px-2 py-1', 'px-4'));
    }

    #[Test]
    public function cn_handles_null_and_false(): void
    {
        $this->assertSame('foo bar', cn('foo', null, false, 'bar'));
    }

    // =========================================================================
    // merge() tests
    // =========================================================================

    #[Test]
    public function merge_resolves_conflicts(): void
    {
        $this->assertSame('py-1 px-4', merge('px-2 py-1', 'px-4'));
    }

    #[Test]
    public function merge_handles_variants(): void
    {
        $this->assertSame('hover:bg-blue-500', merge('hover:bg-red-500', 'hover:bg-blue-500'));
    }

    // =========================================================================
    // join() tests
    // =========================================================================

    #[Test]
    public function join_concatenates_classes(): void
    {
        $this->assertSame('foo bar baz', join('foo', 'bar', 'baz'));
    }

    #[Test]
    public function join_filters_falsy_values(): void
    {
        $this->assertSame('foo bar', join('foo', null, false, '', 'bar'));
    }

    // =========================================================================
    // variants() tests
    // =========================================================================

    #[Test]
    public function variants_returns_callable(): void
    {
        $button = variants([
            'base' => 'btn',
            'variants' => [
                'size' => [
                    'sm' => 'text-sm',
                    'md' => 'text-base',
                ],
            ],
        ]);

        $this->assertIsCallable($button);
    }

    #[Test]
    public function variants_applies_base_classes(): void
    {
        $button = variants([
            'base' => 'btn font-semibold',
        ]);

        $this->assertSame('btn font-semibold', $button());
    }

    #[Test]
    public function variants_applies_variant_classes(): void
    {
        $button = variants([
            'base' => 'btn',
            'variants' => [
                'size' => [
                    'sm' => 'text-sm px-2',
                    'md' => 'text-base px-4',
                ],
            ],
        ]);

        $this->assertSame('btn text-sm px-2', $button(['size' => 'sm']));
        $this->assertSame('btn text-base px-4', $button(['size' => 'md']));
    }

    #[Test]
    public function variants_applies_default_variants(): void
    {
        $button = variants([
            'base' => 'btn',
            'variants' => [
                'size' => [
                    'sm' => 'text-sm',
                    'md' => 'text-base',
                ],
            ],
            'defaultVariants' => [
                'size' => 'md',
            ],
        ]);

        $this->assertSame('btn text-base', $button());
    }

    #[Test]
    public function variants_allows_custom_class_override(): void
    {
        $button = variants([
            'base' => 'btn',
        ]);

        $this->assertSame('btn mt-4', $button(['class' => 'mt-4']));
    }

    // =========================================================================
    // compose() tests
    // =========================================================================

    #[Test]
    public function compose_merges_multiple_variants(): void
    {
        $box = variants([
            'variants' => [
                'shadow' => [
                    'sm' => 'shadow-sm',
                    'md' => 'shadow-md',
                ],
            ],
            'defaultVariants' => [
                'shadow' => 'sm',
            ],
        ]);

        $stack = variants([
            'variants' => [
                'gap' => [
                    '1' => 'gap-1',
                    '2' => 'gap-2',
                ],
            ],
        ]);

        $card = compose($box, $stack);

        $this->assertSame('shadow-sm', $card());
        $this->assertSame('shadow-md gap-2', $card(['shadow' => 'md', 'gap' => '2']));
    }

    // =========================================================================
    // Integration: cn() + variants()
    // =========================================================================

    #[Test]
    public function cn_works_with_variants_output(): void
    {
        $button = variants([
            'base' => 'btn px-4',
            'variants' => [
                'size' => [
                    'sm' => 'text-sm',
                ],
            ],
        ]);

        // cn() should merge variant output with custom classes, resolving conflicts
        $class = cn($button(['size' => 'sm']), 'px-8 mt-4');

        $this->assertSame('btn text-sm px-8 mt-4', $class);
    }
}
