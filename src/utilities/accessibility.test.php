<?php

declare(strict_types=1);

namespace TailwindPHP\Utilities;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;
use TailwindPHP\TestHelper;

use function TailwindPHP\Utilities\registerAccessibilityUtilities;

/**
 * Accessibility Utilities Tests
 *
 * Port of accessibility tests from: packages/tailwindcss/src/utilities.test.ts
 * Lines: 7-38
 */
class accessibility extends TestCase
{
    private TestHelper $helper;

    protected function setUp(): void
    {
        $this->helper = new TestHelper();
        $this->helper->registerUtilities(function ($builder) {
            registerAccessibilityUtilities($builder);
        });
    }

    #[Test]
    public function sr_only(): void
    {
        $css = $this->helper->run(['sr-only']);

        $this->assertStringContainsString('.sr-only {', $css);
        $this->assertStringContainsString('clip-path: inset(50%);', $css);
        $this->assertStringContainsString('white-space: nowrap;', $css);
        $this->assertStringContainsString('border-width: 0;', $css);
        $this->assertStringContainsString('width: 1px;', $css);
        $this->assertStringContainsString('height: 1px;', $css);
        $this->assertStringContainsString('margin: -1px;', $css);
        $this->assertStringContainsString('padding: 0;', $css);
        $this->assertStringContainsString('position: absolute;', $css);
        $this->assertStringContainsString('overflow: hidden;', $css);
    }

    #[Test]
    public function sr_only_invalid_variants_return_empty(): void
    {
        // These should all return empty - sr-only doesn't support these forms
        $this->assertEquals('', $this->helper->run(['-sr-only']));
        $this->assertEquals('', $this->helper->run(['sr-only-[var(--value)]']));
        $this->assertEquals('', $this->helper->run(['sr-only/foo']));
    }

    #[Test]
    public function not_sr_only(): void
    {
        $css = $this->helper->run(['not-sr-only']);

        $this->assertStringContainsString('.not-sr-only {', $css);
        $this->assertStringContainsString('clip-path: none;', $css);
        $this->assertStringContainsString('white-space: normal;', $css);
        $this->assertStringContainsString('width: auto;', $css);
        $this->assertStringContainsString('height: auto;', $css);
        $this->assertStringContainsString('margin: 0;', $css);
        $this->assertStringContainsString('padding: 0;', $css);
        $this->assertStringContainsString('position: static;', $css);
        $this->assertStringContainsString('overflow: visible;', $css);
    }

    #[Test]
    public function not_sr_only_invalid_variants_return_empty(): void
    {
        $this->assertEquals('', $this->helper->run(['-not-sr-only']));
        $this->assertEquals('', $this->helper->run(['not-sr-only-[var(--value)]']));
        $this->assertEquals('', $this->helper->run(['not-sr-only/foo']));
    }
}
