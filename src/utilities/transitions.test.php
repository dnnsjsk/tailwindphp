<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\Tests\TestHelper;

/**
 * Transitions Utilities Tests
 *
 * Port of transition tests from: packages/tailwindcss/src/utilities.test.ts
 */
class transitions extends TestCase
{
    // =========================================================================
    // Transition
    // =========================================================================

    #[Test]
    public function transition_default(): void
    {
        $css = TestHelper::run(['transition']);
        $this->assertStringContainsString('.transition {', $css);
        $this->assertStringContainsString('transition-property:', $css);
        $this->assertStringContainsString('transition-timing-function:', $css);
        $this->assertStringContainsString('transition-duration:', $css);
    }

    #[Test]
    public function transition_none(): void
    {
        $css = TestHelper::run(['transition-none']);
        $this->assertStringContainsString('.transition-none {', $css);
        $this->assertStringContainsString('transition-property: none;', $css);
    }

    #[Test]
    public function transition_all(): void
    {
        $css = TestHelper::run(['transition-all']);
        $this->assertStringContainsString('.transition-all {', $css);
        $this->assertStringContainsString('transition-property: all;', $css);
        $this->assertStringContainsString('transition-timing-function:', $css);
        $this->assertStringContainsString('transition-duration:', $css);
    }

    #[Test]
    public function transition_colors(): void
    {
        $css = TestHelper::run(['transition-colors']);
        $this->assertStringContainsString('.transition-colors {', $css);
        $this->assertStringContainsString('transition-property:', $css);
        $this->assertStringContainsString('color', $css);
        $this->assertStringContainsString('background-color', $css);
    }

    #[Test]
    public function transition_opacity(): void
    {
        $css = TestHelper::run(['transition-opacity']);
        $this->assertStringContainsString('.transition-opacity {', $css);
        $this->assertStringContainsString('transition-property: opacity;', $css);
    }

    #[Test]
    public function transition_shadow(): void
    {
        $css = TestHelper::run(['transition-shadow']);
        $this->assertStringContainsString('.transition-shadow {', $css);
        $this->assertStringContainsString('transition-property: box-shadow;', $css);
    }

    #[Test]
    public function transition_transform(): void
    {
        $css = TestHelper::run(['transition-transform']);
        $this->assertStringContainsString('.transition-transform {', $css);
        $this->assertStringContainsString('transition-property: transform', $css);
    }

    #[Test]
    public function transition_arbitrary(): void
    {
        $css = TestHelper::run(['transition-[var(--value)]']);
        $this->assertStringContainsString('transition-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('transition-property: var(--value);', $css);
    }

    #[Test]
    public function transition_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-transition']));
        $this->assertEquals('', TestHelper::run(['-transition-none']));
        $this->assertEquals('', TestHelper::run(['-transition-all']));
        $this->assertEquals('', TestHelper::run(['transition/foo']));
        $this->assertEquals('', TestHelper::run(['transition-none/foo']));
    }

    #[Test]
    public function transition_behavior(): void
    {
        $css = TestHelper::run(['transition-discrete']);
        $this->assertStringContainsString('.transition-discrete {', $css);
        $this->assertStringContainsString('transition-behavior: allow-discrete;', $css);

        $css = TestHelper::run(['transition-normal']);
        $this->assertStringContainsString('.transition-normal {', $css);
        $this->assertStringContainsString('transition-behavior: normal;', $css);
    }

    // =========================================================================
    // Delay
    // =========================================================================

    #[Test]
    public function delay_values(): void
    {
        $css = TestHelper::run(['delay-200']);
        $this->assertStringContainsString('.delay-200 {', $css);
        $this->assertStringContainsString('transition-delay: 200ms;', $css);

        $css = TestHelper::run(['delay-123']);
        $this->assertStringContainsString('.delay-123 {', $css);
        $this->assertStringContainsString('transition-delay: 123ms;', $css);
    }

    #[Test]
    public function delay_arbitrary(): void
    {
        $css = TestHelper::run(['delay-[300ms]']);
        $this->assertStringContainsString('delay-\\[300ms\\]', $css);
        $this->assertStringContainsString('transition-delay: 300ms;', $css);
    }

    #[Test]
    public function delay_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['delay']));
        $this->assertEquals('', TestHelper::run(['delay--200']));
        $this->assertEquals('', TestHelper::run(['-delay-200']));
        $this->assertEquals('', TestHelper::run(['delay-200/foo']));
    }

    // =========================================================================
    // Duration
    // =========================================================================

    #[Test]
    public function duration_values(): void
    {
        // When theme value exists (--transition-duration-200), use CSS variable
        $css = TestHelper::run(['duration-200']);
        $this->assertStringContainsString('.duration-200 {', $css);
        $this->assertStringContainsString('--tw-duration: var(--transition-duration-200);', $css);
        $this->assertStringContainsString('transition-duration: var(--transition-duration-200);', $css);

        // When theme value doesn't exist, fall back to bare value conversion
        $css = TestHelper::run(['duration-123']);
        $this->assertStringContainsString('.duration-123 {', $css);
        $this->assertStringContainsString('--tw-duration: 123ms;', $css);
        $this->assertStringContainsString('transition-duration: 123ms;', $css);
    }

    #[Test]
    public function duration_arbitrary(): void
    {
        $css = TestHelper::run(['duration-[300ms]']);
        $this->assertStringContainsString('duration-\\[300ms\\]', $css);
        $this->assertStringContainsString('--tw-duration: 300ms;', $css);
        $this->assertStringContainsString('transition-duration: 300ms;', $css);
    }

    #[Test]
    public function duration_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['duration']));
        $this->assertEquals('', TestHelper::run(['duration--200']));
        $this->assertEquals('', TestHelper::run(['-duration-200']));
        $this->assertEquals('', TestHelper::run(['duration-200/foo']));
    }

    // =========================================================================
    // Ease
    // =========================================================================

    #[Test]
    public function ease_values(): void
    {
        // When theme values exist, use them
        $css = TestHelper::run(['ease-in']);
        $this->assertStringContainsString('.ease-in {', $css);
        $this->assertStringContainsString('--tw-ease:', $css);
        $this->assertStringContainsString('transition-timing-function:', $css);

        $css = TestHelper::run(['ease-out']);
        $this->assertStringContainsString('.ease-out {', $css);
    }

    #[Test]
    public function ease_linear(): void
    {
        // When theme value exists (--ease-linear), use CSS variable
        $css = TestHelper::run(['ease-linear']);
        $this->assertStringContainsString('.ease-linear {', $css);
        $this->assertStringContainsString('--tw-ease: var(--ease-linear);', $css);
        $this->assertStringContainsString('transition-timing-function: var(--ease-linear);', $css);
    }

    #[Test]
    public function ease_arbitrary(): void
    {
        $css = TestHelper::run(['ease-[var(--value)]']);
        $this->assertStringContainsString('ease-\\[var\\(--value\\)\\]', $css);
        $this->assertStringContainsString('--tw-ease: var(--value);', $css);
        $this->assertStringContainsString('transition-timing-function: var(--value);', $css);
    }

    #[Test]
    public function ease_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', TestHelper::run(['-ease-in']));
        $this->assertEquals('', TestHelper::run(['-ease-out']));
        $this->assertEquals('', TestHelper::run(['ease-in/foo']));
    }

    // =========================================================================
    // Will Change
    // =========================================================================

    #[Test]
    public function will_change(): void
    {
        $css = TestHelper::run(['will-change-auto']);
        $this->assertStringContainsString('.will-change-auto {', $css);
        $this->assertStringContainsString('will-change: auto;', $css);

        $css = TestHelper::run(['will-change-scroll']);
        $this->assertStringContainsString('.will-change-scroll {', $css);
        $this->assertStringContainsString('will-change: scroll-position;', $css);

        $css = TestHelper::run(['will-change-contents']);
        $this->assertStringContainsString('.will-change-contents {', $css);
        $this->assertStringContainsString('will-change: contents;', $css);

        $css = TestHelper::run(['will-change-transform']);
        $this->assertStringContainsString('.will-change-transform {', $css);
        $this->assertStringContainsString('will-change: transform;', $css);
    }

    #[Test]
    public function will_change_arbitrary(): void
    {
        $css = TestHelper::run(['will-change-[opacity]']);
        $this->assertStringContainsString('will-change-\\[opacity\\]', $css);
        $this->assertStringContainsString('will-change: opacity;', $css);
    }
}
