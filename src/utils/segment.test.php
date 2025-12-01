<?php

declare(strict_types=1);

namespace TailwindPHP\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function TailwindPHP\Utils\segment;

class segment extends TestCase
{
    #[Test]
    public function should_result_in_a_single_segment_when_the_separator_is_not_present(): void
    {
        $this->assertEquals(['foo'], segment('foo', ':'));
    }

    #[Test]
    public function should_split_by_the_separator(): void
    {
        $this->assertEquals(['foo', 'bar', 'baz'], segment('foo:bar:baz', ':'));
    }

    #[Test]
    public function should_not_split_inside_of_parens(): void
    {
        $this->assertEquals(['a', '(b:c)', 'd'], segment('a:(b:c):d', ':'));
    }

    #[Test]
    public function should_not_split_inside_of_brackets(): void
    {
        $this->assertEquals(['a', '[b:c]', 'd'], segment('a:[b:c]:d', ':'));
    }

    #[Test]
    public function should_not_split_inside_of_curlies(): void
    {
        $this->assertEquals(['a', '{b:c}', 'd'], segment('a:{b:c}:d', ':'));
    }

    #[Test]
    public function should_not_split_inside_of_double_quotes(): void
    {
        $this->assertEquals(['a', '"b:c"', 'd'], segment('a:"b:c":d', ':'));
    }

    #[Test]
    public function should_not_split_inside_of_single_quotes(): void
    {
        $this->assertEquals(['a', "'b:c'", 'd'], segment("a:'b:c':d", ':'));
    }

    #[Test]
    public function should_not_crash_when_double_quotes_are_unbalanced(): void
    {
        $this->assertEquals(['a', '"b:c:d'], segment('a:"b:c:d', ':'));
    }

    #[Test]
    public function should_not_crash_when_single_quotes_are_unbalanced(): void
    {
        $this->assertEquals(['a', "'b:c:d"], segment("a:'b:c:d", ':'));
    }

    #[Test]
    public function should_skip_escaped_double_quotes(): void
    {
        $this->assertEquals(['a', '"b:c\":d"', 'e'], segment('a:"b:c\":d":e', ':'));
    }

    #[Test]
    public function should_skip_escaped_single_quotes(): void
    {
        $this->assertEquals(['a', "'b:c\':d'", 'e'], segment("a:'b:c\':d':e", ':'));
    }

    #[Test]
    public function should_split_by_the_escape_sequence_which_is_escape_as_well(): void
    {
        $this->assertEquals(['a', 'b', 'c', 'd'], segment('a\\b\\c\\d', '\\'));
        $this->assertEquals(['a', '(b\\c)', 'd'], segment('a\\(b\\c)\\d', '\\'));
        $this->assertEquals(['a', '[b\\c]', 'd'], segment('a\\[b\\c]\\d', '\\'));
        $this->assertEquals(['a', '{b\\c}', 'd'], segment('a\\{b\\c}\\d', '\\'));
    }
}
