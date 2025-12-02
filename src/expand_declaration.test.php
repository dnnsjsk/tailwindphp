<?php

declare(strict_types=1);

namespace TailwindPHP;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function TailwindPHP\CssParser\parse;
use function TailwindPHP\Ast\toCss;
use function TailwindPHP\Walk\walk;
use function TailwindPHP\ExpandDeclaration\expandDeclaration;
use TailwindPHP\Walk\WalkAction;

use const TailwindPHP\ExpandDeclaration\SIGNATURE_EXPAND_PROPERTIES;
use const TailwindPHP\ExpandDeclaration\SIGNATURE_LOGICAL_TO_PHYSICAL;

/**
 * Tests for expand-declaration.php
 *
 * Port of: packages/tailwindcss/src/expand-declaration.test.ts
 */
class expand_declaration extends TestCase
{
    /**
     * Helper function to expand declarations in CSS.
     */
    private function expand(string $input, int $options): string
    {
        $ast = parse($input);

        walk($ast, function (&$node) use ($options) {
            if ($node['kind'] === 'declaration') {
                $result = expandDeclaration($node, $options);
                if ($result !== null) {
                    return WalkAction::ReplaceSkip($result);
                }
            }
        });

        return toCss($ast);
    }

    #[Test]
    public function expand_to_4_properties(): void
    {
        $options = SIGNATURE_EXPAND_PROPERTIES;

        $input = <<<CSS
.one {
  inset: 10px;
}

.two {
  inset: 10px 20px;
}

.three {
  inset: 10px 20px 30px;
}

.four {
  inset: 10px 20px 30px 40px;
}
CSS;

        $expected = <<<CSS
.one {
  top: 10px;
  right: 10px;
  bottom: 10px;
  left: 10px;
}
.two {
  top: 10px;
  right: 20px;
  bottom: 10px;
  left: 20px;
}
.three {
  top: 10px;
  right: 20px;
  bottom: 30px;
  left: 20px;
}
.four {
  top: 10px;
  right: 20px;
  bottom: 30px;
  left: 40px;
}
CSS;

        $this->assertEquals(trim($expected), trim($this->expand($input, $options)));
    }

    #[Test]
    public function expand_to_2_properties(): void
    {
        $options = SIGNATURE_EXPAND_PROPERTIES;

        $input = <<<CSS
.one {
  gap: 10px;
}

.two {
  gap: 10px 20px;
}
CSS;

        $expected = <<<CSS
.one {
  row-gap: 10px;
  column-gap: 10px;
}
.two {
  row-gap: 10px;
  column-gap: 20px;
}
CSS;

        $this->assertEquals(trim($expected), trim($this->expand($input, $options)));
    }

    #[Test]
    public function expansion_with_important(): void
    {
        $options = SIGNATURE_EXPAND_PROPERTIES;

        $input = <<<CSS
.one {
  inset: 10px;
}

.two {
  inset: 10px 20px;
}

.three {
  inset: 10px 20px 30px !important;
}

.four {
  inset: 10px 20px 30px 40px;
}
CSS;

        $expected = <<<CSS
.one {
  top: 10px;
  right: 10px;
  bottom: 10px;
  left: 10px;
}
.two {
  top: 10px;
  right: 20px;
  bottom: 10px;
  left: 20px;
}
.three {
  top: 10px !important;
  right: 20px !important;
  bottom: 30px !important;
  left: 20px !important;
}
.four {
  top: 10px;
  right: 20px;
  bottom: 30px;
  left: 40px;
}
CSS;

        $this->assertEquals(trim($expected), trim($this->expand($input, $options)));
    }

    #[Test]
    public function expand_logical_properties_margin_block(): void
    {
        $options = SIGNATURE_EXPAND_PROPERTIES | SIGNATURE_LOGICAL_TO_PHYSICAL;

        $input = <<<CSS
.example {
  margin-block: 10px 20px;
}
CSS;

        $expected = <<<CSS
.example {
  margin-top: 10px;
  margin-bottom: 20px;
}
CSS;

        $this->assertEquals(trim($expected), trim($this->expand($input, $options)));
    }
}
