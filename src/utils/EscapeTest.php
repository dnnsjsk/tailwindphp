<?php

declare(strict_types=1);

namespace TailwindPHP\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function TailwindPHP\Utils\escape;
use function TailwindPHP\Utils\unescape;

class EscapeTest extends TestCase
{
    #[Test]
    public function escape_adds_backslashes(): void
    {
        $this->assertEquals('red-1\\/2', escape('red-1/2'));
    }

    #[Test]
    public function unescape_removes_backslashes(): void
    {
        $this->assertEquals('red-1/2', unescape('red-1\\/2'));
    }

    #[Test]
    public function escape_handles_single_dash(): void
    {
        $this->assertEquals('\\-', escape('-'));
    }

    #[Test]
    public function escape_handles_leading_digit(): void
    {
        $this->assertEquals('\\31 23', escape('123'));
    }

    #[Test]
    public function escape_handles_dash_followed_by_digit(): void
    {
        $this->assertEquals('-\\31 23', escape('-123'));
    }

    #[Test]
    public function escape_handles_null_character(): void
    {
        $this->assertEquals("a\u{FFFD}b", escape("a\0b"));
    }

    #[Test]
    public function escape_handles_control_characters(): void
    {
        $this->assertEquals('\\1 ', escape("\x01"));
    }

    #[Test]
    public function escape_preserves_alphanumeric(): void
    {
        $this->assertEquals('abc123', escape('abc123'));
    }

    #[Test]
    public function escape_preserves_dashes_and_underscores(): void
    {
        $this->assertEquals('foo-bar_baz', escape('foo-bar_baz'));
    }

    #[Test]
    public function unescape_handles_hex_escapes(): void
    {
        $this->assertEquals('A', unescape('\\41 '));
        $this->assertEquals('A', unescape('\\41'));
    }
}
