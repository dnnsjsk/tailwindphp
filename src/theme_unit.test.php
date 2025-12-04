<?php

declare(strict_types=1);

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\isIgnoredThemeKey;

use TailwindPHP\Theme;

use const TailwindPHP\THEME_OPTION_DEFAULT;
use const TailwindPHP\THEME_OPTION_INLINE;
use const TailwindPHP\THEME_OPTION_NONE;
use const TailwindPHP\THEME_OPTION_REFERENCE;
use const TailwindPHP\THEME_OPTION_STATIC;

/**
 * Tests for Theme class.
 *
 * @port-deviation:tests These tests are PHP-specific additions for complete coverage.
 */
class theme_unit extends TestCase
{
    // =========================================================================
    // isIgnoredThemeKey tests
    // =========================================================================

    #[Test]
    public function ignored_theme_key_font_weight(): void
    {
        $this->assertTrue(isIgnoredThemeKey('--font-weight', '--font'));
        $this->assertTrue(isIgnoredThemeKey('--font-weight-bold', '--font'));
        $this->assertTrue(isIgnoredThemeKey('--font-size', '--font'));
    }

    #[Test]
    public function ignored_theme_key_font_family_not_ignored(): void
    {
        $this->assertFalse(isIgnoredThemeKey('--font-sans', '--font'));
        $this->assertFalse(isIgnoredThemeKey('--font-mono', '--font'));
    }

    #[Test]
    public function ignored_theme_key_inset(): void
    {
        $this->assertTrue(isIgnoredThemeKey('--inset-shadow', '--inset'));
        $this->assertTrue(isIgnoredThemeKey('--inset-ring', '--inset'));
        $this->assertFalse(isIgnoredThemeKey('--inset-0', '--inset'));
    }

    #[Test]
    public function ignored_theme_key_text(): void
    {
        $this->assertTrue(isIgnoredThemeKey('--text-color', '--text'));
        $this->assertTrue(isIgnoredThemeKey('--text-shadow', '--text'));
        $this->assertFalse(isIgnoredThemeKey('--text-sm', '--text'));
    }

    #[Test]
    public function ignored_theme_key_grid_column(): void
    {
        $this->assertTrue(isIgnoredThemeKey('--grid-column-start', '--grid-column'));
        $this->assertTrue(isIgnoredThemeKey('--grid-column-end', '--grid-column'));
        $this->assertFalse(isIgnoredThemeKey('--grid-column-1', '--grid-column'));
    }

    // =========================================================================
    // Theme constructor and basic methods
    // =========================================================================

    #[Test]
    public function theme_constructor_empty(): void
    {
        $theme = new Theme();
        $this->assertSame(0, $theme->size());
    }

    #[Test]
    public function theme_constructor_with_values(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-blue' => ['value' => '#0000ff', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $this->assertSame(2, $theme->size());
    }

    #[Test]
    public function theme_get_prefix_default(): void
    {
        $theme = new Theme();
        $this->assertNull($theme->getPrefix());
    }

    #[Test]
    public function theme_get_prefix_set(): void
    {
        $theme = new Theme();
        $theme->prefix = 'tw';
        $this->assertSame('tw', $theme->getPrefix());
    }

    // =========================================================================
    // add() tests
    // =========================================================================

    #[Test]
    public function theme_add_value(): void
    {
        $theme = new Theme();
        $theme->add('--color-red', '#ff0000');
        $this->assertTrue($theme->has('--color-red'));
    }

    #[Test]
    public function theme_add_value_initial_removes(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $theme->add('--color-red', 'initial');
        $this->assertFalse($theme->has('--color-red'));
    }

    #[Test]
    public function theme_add_namespace_wildcard_clears(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-blue' => ['value' => '#0000ff', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--spacing-4' => ['value' => '1rem', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $theme->add('--color-*', 'initial');
        $this->assertFalse($theme->has('--color-red'));
        $this->assertFalse($theme->has('--color-blue'));
        $this->assertTrue($theme->has('--spacing-4'));
    }

    #[Test]
    public function theme_add_all_wildcard_clears_all(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--spacing-4' => ['value' => '1rem', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $theme->add('--*', 'initial');
        $this->assertSame(0, $theme->size());
    }

    #[Test]
    public function theme_add_default_doesnt_override_non_default(): void
    {
        $theme = new Theme();
        $theme->add('--color-red', '#ff0000', THEME_OPTION_NONE);
        $theme->add('--color-red', '#ff5555', THEME_OPTION_DEFAULT);
        $this->assertSame('#ff0000', $theme->get(['--color-red']));
    }

    #[Test]
    public function theme_add_default_overrides_default(): void
    {
        $theme = new Theme();
        $theme->add('--color-red', '#ff0000', THEME_OPTION_DEFAULT);
        $theme->add('--color-red', '#ff5555', THEME_OPTION_DEFAULT);
        $this->assertSame('#ff5555', $theme->get(['--color-red']));
    }

    #[Test]
    public function theme_add_non_default_overrides_default(): void
    {
        $theme = new Theme();
        $theme->add('--color-red', '#ff0000', THEME_OPTION_DEFAULT);
        $theme->add('--color-red', '#ff5555', THEME_OPTION_NONE);
        $this->assertSame('#ff5555', $theme->get(['--color-red']));
    }

    #[Test]
    public function theme_add_invalid_wildcard_value_throws(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $theme = new Theme();
        $theme->add('--color-*', 'red');
    }

    // =========================================================================
    // get() tests
    // =========================================================================

    #[Test]
    public function theme_get_returns_value(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $this->assertSame('#ff0000', $theme->get(['--color-red']));
    }

    #[Test]
    public function theme_get_returns_null_for_missing(): void
    {
        $theme = new Theme();
        $this->assertNull($theme->get(['--color-red']));
    }

    #[Test]
    public function theme_get_returns_first_matching(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--bg-red' => ['value' => '#ff5555', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $this->assertSame('#ff0000', $theme->get(['--color-red', '--bg-red']));
    }

    // =========================================================================
    // has() and hasDefault() tests
    // =========================================================================

    #[Test]
    public function theme_has_returns_true(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $this->assertTrue($theme->has('--color-red'));
    }

    #[Test]
    public function theme_has_returns_false(): void
    {
        $theme = new Theme();
        $this->assertFalse($theme->has('--color-red'));
    }

    #[Test]
    public function theme_has_default_true(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_DEFAULT, 'src' => null],
        ]);
        $this->assertTrue($theme->hasDefault('--color-red'));
    }

    #[Test]
    public function theme_has_default_false(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $this->assertFalse($theme->hasDefault('--color-red'));
    }

    // =========================================================================
    // keysInNamespaces() tests
    // =========================================================================

    #[Test]
    public function theme_keys_in_namespaces(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-blue' => ['value' => '#0000ff', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--spacing-4' => ['value' => '1rem', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $keys = $theme->keysInNamespaces(['--color']);
        $this->assertCount(2, $keys);
        $this->assertContains('red', $keys);
        $this->assertContains('blue', $keys);
    }

    #[Test]
    public function theme_keys_in_namespaces_skips_nested(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-red--alpha' => ['value' => '0.5', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $keys = $theme->keysInNamespaces(['--color']);
        $this->assertCount(1, $keys);
        $this->assertSame('red', $keys[0]);
    }

    #[Test]
    public function theme_keys_in_namespaces_skips_ignored(): void
    {
        $theme = new Theme([
            '--font-sans' => ['value' => 'sans-serif', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--font-weight' => ['value' => '400', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $keys = $theme->keysInNamespaces(['--font']);
        $this->assertCount(1, $keys);
        $this->assertSame('sans', $keys[0]);
    }

    // =========================================================================
    // resolve() tests
    // =========================================================================

    #[Test]
    public function theme_resolve_returns_var(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $result = $theme->resolve('red', ['--color']);
        $this->assertSame('var(--color-red)', $result);
    }

    #[Test]
    public function theme_resolve_inline_returns_value(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_INLINE, 'src' => null],
        ]);
        $result = $theme->resolve('red', ['--color']);
        $this->assertSame('#ff0000', $result);
    }

    #[Test]
    public function theme_resolve_reference_includes_fallback(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_REFERENCE, 'src' => null],
        ]);
        $result = $theme->resolve('red', ['--color']);
        $this->assertSame('var(--color-red, #ff0000)', $result);
    }

    #[Test]
    public function theme_resolve_returns_null_for_missing(): void
    {
        $theme = new Theme();
        $this->assertNull($theme->resolve('red', ['--color']));
    }

    #[Test]
    public function theme_resolve_with_dot_uses_underscore(): void
    {
        $theme = new Theme([
            '--spacing-0_5' => ['value' => '0.125rem', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $result = $theme->resolve('0.5', ['--spacing']);
        $this->assertSame('var(--spacing-0_5)', $result);
    }

    // =========================================================================
    // resolveValue() tests
    // =========================================================================

    #[Test]
    public function theme_resolve_value_returns_raw(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $result = $theme->resolveValue('red', ['--color']);
        $this->assertSame('#ff0000', $result);
    }

    // =========================================================================
    // resolveWith() tests
    // =========================================================================

    #[Test]
    public function theme_resolve_with_returns_value_and_extra(): void
    {
        $theme = new Theme([
            '--text-sm' => ['value' => '0.875rem', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--text-sm--line-height' => ['value' => '1.25rem', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $result = $theme->resolveWith('sm', ['--text'], ['--line-height']);
        $this->assertSame('var(--text-sm)', $result[0]);
        $this->assertArrayHasKey('--line-height', $result[1]);
    }

    // =========================================================================
    // namespace() tests
    // =========================================================================

    #[Test]
    public function theme_namespace_returns_values(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-blue' => ['value' => '#0000ff', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $values = $theme->namespace('--color');
        $this->assertSame('#ff0000', $values['red']);
        $this->assertSame('#0000ff', $values['blue']);
    }

    // =========================================================================
    // prefix tests
    // =========================================================================

    #[Test]
    public function theme_prefix_key(): void
    {
        $theme = new Theme();
        $theme->prefix = 'tw';
        $this->assertSame('--tw-color-red', $theme->prefixKey('--color-red'));
    }

    #[Test]
    public function theme_prefix_key_no_prefix(): void
    {
        $theme = new Theme();
        $this->assertSame('--color-red', $theme->prefixKey('--color-red'));
    }

    #[Test]
    public function theme_entries_with_prefix(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $theme->prefix = 'tw';

        foreach ($theme->entries() as [$key, $value]) {
            $this->assertSame('--tw-color-red', $key);
            $this->assertSame('#ff0000', $value['value']);
        }
    }

    // =========================================================================
    // clearNamespace() tests
    // =========================================================================

    #[Test]
    public function theme_clear_namespace(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
            '--color-blue' => ['value' => '#0000ff', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $theme->clearNamespace('--color', THEME_OPTION_NONE);
        $this->assertSame(0, $theme->size());
    }

    #[Test]
    public function theme_clear_namespace_with_options(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_DEFAULT, 'src' => null],
            '--color-blue' => ['value' => '#0000ff', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $theme->clearNamespace('--color', THEME_OPTION_DEFAULT);
        $this->assertFalse($theme->has('--color-red'));
        $this->assertTrue($theme->has('--color-blue'));
    }

    // =========================================================================
    // markUsedVariable() tests
    // =========================================================================

    #[Test]
    public function theme_mark_used_variable(): void
    {
        $theme = new Theme([
            '--color-red' => ['value' => '#ff0000', 'options' => THEME_OPTION_NONE, 'src' => null],
        ]);
        $firstTime = $theme->markUsedVariable('--color-red');
        $this->assertTrue($firstTime);

        $secondTime = $theme->markUsedVariable('--color-red');
        $this->assertFalse($secondTime);
    }

    #[Test]
    public function theme_mark_used_variable_missing(): void
    {
        $theme = new Theme();
        $this->assertFalse($theme->markUsedVariable('--color-red'));
    }

    // =========================================================================
    // keyframes tests
    // =========================================================================

    #[Test]
    public function theme_add_keyframes(): void
    {
        $theme = new Theme();
        $node = ['name' => '@keyframes', 'params' => 'spin', 'nodes' => []];
        $theme->addKeyframes($node);
        $this->assertTrue($theme->hasKeyframe('spin'));
    }

    #[Test]
    public function theme_get_keyframes(): void
    {
        $theme = new Theme();
        $node = ['name' => '@keyframes', 'params' => 'spin', 'nodes' => []];
        $theme->addKeyframes($node);
        $keyframes = $theme->getKeyframes();
        $this->assertCount(1, $keyframes);
    }

    #[Test]
    public function theme_get_keyframe_options(): void
    {
        $theme = new Theme();
        $node = ['name' => '@keyframes', 'params' => 'spin', 'nodes' => []];
        $theme->addKeyframes($node, THEME_OPTION_STATIC);
        $this->assertSame(THEME_OPTION_STATIC, $theme->getKeyframeOptions('spin'));
    }

    #[Test]
    public function theme_get_keyframe_options_missing(): void
    {
        $theme = new Theme();
        $this->assertSame(THEME_OPTION_NONE, $theme->getKeyframeOptions('missing'));
    }
}
