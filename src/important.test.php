<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function TailwindPHP\compile;

/**
 * Tests for !important handling.
 *
 * Port of: packages/tailwindcss/src/important.test.ts
 */
class important extends TestCase
{
    #[Test]
    public function utilities_can_be_wrapped_in_a_selector(): void
    {
        // This is the v4 equivalent of `important: "#app"` from v3
        $input = <<<CSS
#app {
  @tailwind utilities;
}
CSS;

        $compiler = compile($input);
        $result = $compiler['build'](['underline', 'hover:line-through']);

        $this->assertStringContainsString('#app', $result);
        $this->assertStringContainsString('.underline', $result);
        $this->assertStringContainsString('text-decoration-line: underline', $result);
        $this->assertStringContainsString('.hover\\:line-through', $result);
        $this->assertStringContainsString('text-decoration-line: line-through', $result);
    }

    #[Test]
    public function utilities_can_be_wrapped_with_selector_and_marked_as_important(): void
    {
        // @media important makes all utilities !important
        $input = <<<CSS
@media important {
  #app {
    @tailwind utilities;
  }
}
CSS;

        $compiler = compile($input);
        $result = $compiler['build'](['underline', 'hover:line-through']);

        $this->assertStringContainsString('#app', $result);
        $this->assertStringContainsString('.underline', $result);
        $this->assertStringContainsString('text-decoration-line: underline !important', $result);
        $this->assertStringContainsString('.hover\\:line-through', $result);
        $this->assertStringContainsString('text-decoration-line: line-through !important', $result);
    }

    #[Test]
    public function important_suffix_on_utility_classes(): void
    {
        // The ! suffix makes a utility important
        $input = <<<CSS
@theme {
  --spacing-10: 2.5rem;
}
@tailwind utilities;
CSS;

        $compiler = compile($input);
        $result = $compiler['build'](['z-10!', 'mt-10!']);

        $this->assertStringContainsString('z-index: 10 !important', $result);
        $this->assertStringContainsString('margin-top: var(--spacing-10) !important', $result);
    }

    #[Test]
    public function important_suffix_with_variants(): void
    {
        $input = <<<CSS
@tailwind utilities;
CSS;

        $compiler = compile($input);
        $result = $compiler['build'](['hover:underline!']);

        $this->assertStringContainsString('text-decoration-line: underline !important', $result);
    }
}
