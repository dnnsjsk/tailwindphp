<?php

declare(strict_types=1);

namespace TailwindPHP\Utils;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\Test;

use function TailwindPHP\Utils\toKeyPath;

class ToKeyPathTest extends TestCase
{
    #[Test]
    public function can_convert_key_paths_to_arrays(): void
    {
        $this->assertEquals(['fontSize', 'xs'], toKeyPath('fontSize.xs'));
        $this->assertEquals(['fontSize', 'xs', '1', 'lineHeight'], toKeyPath('fontSize.xs[1].lineHeight'));
        $this->assertEquals(['colors', 'red', '500'], toKeyPath('colors.red.500'));
        $this->assertEquals(['colors', 'red', '500'], toKeyPath('colors[red].500'));
        $this->assertEquals(['colors', 'red', '500'], toKeyPath('colors[red].[500]'));
        $this->assertEquals(['colors', 'red', '500'], toKeyPath('colors[red]500'));
        $this->assertEquals(['colors', 'red', '500'], toKeyPath('colors[red][500]'));
        $this->assertEquals(['colors', 'red', '500', '50', '5'], toKeyPath('colors[red]500[50]5'));
    }
}
