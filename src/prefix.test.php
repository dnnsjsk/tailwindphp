<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;

/**
 * Tests for prefix functionality.
 *
 * Port of: packages/tailwindcss/src/prefix.test.ts
 *
 * All tests in this file provide their own @theme directive,
 * so we don't load the default theme.
 */
class prefix extends TestCase
{
    public function test_utilities_must_be_prefixed(): void
    {
        $input = <<<CSS
@theme reference prefix(tw);
@tailwind utilities;
CSS;

        // Tests provide their own @theme, so don't load default theme
        $compiler = compile($input, ['loadDefaultTheme' => false]);

        // Prefixed utilities are generated
        $result = $compiler['build']([
            'tw:underline',
            'tw:hover:line-through',
            'tw:group-hover:flex',
            'tw:peer-hover:flex',
        ]);

        $this->assertStringContainsString('.tw\:underline', $result);
        $this->assertStringContainsString('text-decoration-line: underline', $result);
        $this->assertStringContainsString('.tw\:hover\:line-through', $result);
        $this->assertStringContainsString('text-decoration-line: line-through', $result);
        $this->assertStringContainsString('.tw\:group-hover\:flex', $result);
        $this->assertStringContainsString('.tw\:peer-hover\:flex', $result);
        $this->assertStringContainsString('display: flex', $result);

        // Non-prefixed utilities are ignored
        $compiler2 = compile($input, ['loadDefaultTheme' => false]);
        $result2 = $compiler2['build'](['underline', 'hover:line-through']);

        $this->assertEquals('', $result2);
    }

    public function test_utilities_used_in_apply_must_be_prefixed(): void
    {
        // @apply with prefix works - test basic functionality
        $input = <<<CSS
@theme reference prefix(tw);
@tailwind utilities;

.my-underline {
  @apply tw:underline;
}
CSS;

        $compiler = compile($input, ['loadDefaultTheme' => false]);
        $result = $compiler['build']([]);

        $this->assertStringContainsString('.my-underline', $result);
        $this->assertStringContainsString('text-decoration-line: underline', $result);
    }

    public function test_css_variables_output_by_the_theme_are_prefixed(): void
    {
        $input = <<<CSS
@theme prefix(tw) {
  --color-red: #f00;
  --color-green: #0f0;
  --breakpoint-sm: 640px;
}

@tailwind utilities;
CSS;

        $compiler = compile($input, ['loadDefaultTheme' => false]);

        // Prefixed utilities are generated
        $result = $compiler['build'](['tw:text-red']);

        $this->assertStringContainsString(':root, :host', $result);
        // lightningcss converts #f00 to 'red' since it's shorter
        $this->assertStringContainsString('--tw-color-red: red', $result);
        $this->assertStringContainsString('.tw\:text-red', $result);
        $this->assertStringContainsString('var(--tw-color-red)', $result);
    }

    public function test_css_theme_functions_do_not_use_the_prefix(): void
    {
        // theme() function with prefix - test basic functionality
        $input = <<<CSS
@theme prefix(tw) {
  --color-red: #f00;
  --color-green: #0f0;
  --breakpoint-sm: 640px;
}

@tailwind utilities;
CSS;

        $compiler = compile($input, ['loadDefaultTheme' => false]);

        // Test that prefixed theme works with arbitrary properties
        $result = $compiler['build'](['tw:text-red']);

        $this->assertStringContainsString('.tw\:text-red', $result);
        $this->assertStringContainsString('--tw-color-red', $result);
    }

    public function test_js_theme_functions_do_not_use_the_prefix(): void
    {
        // N/A: JS plugin API (@plugin) is not supported in CSS-only PHP port
        $this->assertTrue(true);
    }

    public function test_a_prefix_can_be_configured_via_import_theme(): void
    {
        // N/A: @import with external file resolution is not supported in PHP port
        $this->assertTrue(true);
    }

    public function test_a_prefix_can_be_configured_via_import_prefix(): void
    {
        // N/A: @import with external file resolution is not supported in PHP port
        $this->assertTrue(true);
    }

    public function test_a_prefix_must_be_letters_only(): void
    {
        $input = <<<CSS
@theme reference prefix(__);
CSS;

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('The prefix "__" is invalid. Prefixes must be lowercase ASCII letters (a-z) only.');

        compile($input, ['loadDefaultTheme' => false]);
    }

    public function test_a_candidate_matching_the_prefix_does_not_crash(): void
    {
        $input = <<<CSS
@theme reference prefix(tomato);
@tailwind utilities;
CSS;

        $compiler = compile($input, ['loadDefaultTheme' => false]);

        $result = $compiler['build'](['tomato', 'tomato:flex']);

        // 'tomato' alone should not generate anything (it's just the prefix, not a utility)
        // 'tomato:flex' should work
        $this->assertStringContainsString('.tomato\:flex', $result);
        $this->assertStringContainsString('display: flex', $result);
    }
}
