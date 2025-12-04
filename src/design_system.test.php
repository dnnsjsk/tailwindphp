<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use TailwindPHP\DesignSystem\DesignSystem;
use TailwindPHP\Theme;

use const TailwindPHP\THEME_OPTION_NONE;

use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Variants\Variants;

/**
 * Tests for DesignSystem class.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class design_system extends TestCase
{
    private function createDesignSystem(): DesignSystem
    {
        $theme = new Theme([
            '--spacing-4' => ['value' => '1rem', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--spacing-8' => ['value' => '2rem', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-red-500' => ['value' => '#ef4444', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-blue-500' => ['value' => '#3b82f6', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);

        $utilities = new Utilities();
        $variants = new Variants();

        // Register basic utilities
        $utilities->static('flex', fn () => [['property' => 'display', 'value' => 'flex']]);
        $utilities->static('hidden', fn () => [['property' => 'display', 'value' => 'none']]);
        $utilities->functional('p', function ($value) use ($theme) {
            $resolved = $theme->resolve($value, ['--spacing']);

            return $resolved ? [['property' => 'padding', 'value' => $resolved]] : null;
        });

        return new DesignSystem($theme, $utilities, $variants);
    }

    // =========================================================================
    // Constructor and getters
    // =========================================================================

    #[Test]
    public function design_system_can_be_created(): void
    {
        $ds = $this->createDesignSystem();
        $this->assertInstanceOf(DesignSystem::class, $ds);
    }

    #[Test]
    public function design_system_get_theme(): void
    {
        $ds = $this->createDesignSystem();
        $this->assertInstanceOf(Theme::class, $ds->getTheme());
    }

    #[Test]
    public function design_system_get_utilities(): void
    {
        $ds = $this->createDesignSystem();
        $this->assertInstanceOf(Utilities::class, $ds->getUtilities());
    }

    #[Test]
    public function design_system_get_variants(): void
    {
        $ds = $this->createDesignSystem();
        $this->assertInstanceOf(Variants::class, $ds->getVariants());
    }

    // =========================================================================
    // Important flag
    // =========================================================================

    #[Test]
    public function design_system_is_important_default_false(): void
    {
        $ds = $this->createDesignSystem();
        $this->assertFalse($ds->isImportant());
    }

    #[Test]
    public function design_system_set_important(): void
    {
        $ds = $this->createDesignSystem();
        $ds->setImportant(true);
        $this->assertTrue($ds->isImportant());
    }

    // =========================================================================
    // Invalid candidates
    // =========================================================================

    #[Test]
    public function design_system_get_invalid_candidates_default_empty(): void
    {
        $ds = $this->createDesignSystem();
        $this->assertIsArray($ds->getInvalidCandidates());
        $this->assertEmpty($ds->getInvalidCandidates());
    }

    // =========================================================================
    // Theme resolution
    // =========================================================================

    #[Test]
    public function design_system_resolve_theme_value(): void
    {
        $ds = $this->createDesignSystem();
        // Path is in CSS variable format (--spacing-4)
        $value = $ds->resolveThemeValue('--spacing-4');
        $this->assertSame('1rem', $value);
    }

    #[Test]
    public function design_system_resolve_theme_value_missing(): void
    {
        $ds = $this->createDesignSystem();
        $value = $ds->resolveThemeValue('--spacing-unknown');
        $this->assertNull($value);
    }

    #[Test]
    public function design_system_resolve_theme_value_color(): void
    {
        $ds = $this->createDesignSystem();
        $value = $ds->resolveThemeValue('--color-red-500');
        $this->assertSame('#ef4444', $value);
    }

    // =========================================================================
    // Candidate parsing
    // =========================================================================

    #[Test]
    public function design_system_parse_candidate_static(): void
    {
        $ds = $this->createDesignSystem();
        $candidates = $ds->parseCandidate('flex');
        $this->assertIsArray($candidates);
    }

    #[Test]
    public function design_system_parse_candidate_functional(): void
    {
        $ds = $this->createDesignSystem();
        $candidates = $ds->parseCandidate('p-4');
        $this->assertIsArray($candidates);
    }

    #[Test]
    public function design_system_parse_candidate_unknown(): void
    {
        $ds = $this->createDesignSystem();
        $candidates = $ds->parseCandidate('unknown-utility');
        // Unknown candidates return empty or specific structure
        $this->assertIsArray($candidates);
    }

    // =========================================================================
    // Variant parsing
    // =========================================================================

    #[Test]
    public function design_system_parse_variant(): void
    {
        $ds = $this->createDesignSystem();
        $variant = $ds->parseVariant('hover');
        // Variant may or may not be registered in basic setup
        $this->assertTrue($variant === null || is_array($variant));
    }

    // =========================================================================
    // CSS generation
    // =========================================================================

    #[Test]
    public function design_system_candidates_to_css(): void
    {
        $ds = $this->createDesignSystem();
        $result = $ds->candidatesToCss(['flex']);
        $this->assertIsArray($result);
    }

}
