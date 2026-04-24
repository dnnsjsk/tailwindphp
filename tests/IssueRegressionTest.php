<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

class IssueRegressionTest extends TestCase
{
    public function test_infer_data_type_dispatch_survives_vendor_namespace_prefixing(): void
    {
        $tempDir = sys_get_temp_dir() . '/tailwindphp-prefix-' . bin2hex(random_bytes(6));
        mkdir($tempDir);

        try {
            foreach (['segment.php', 'math-operators.php', 'is-color.php', 'infer-data-type.php'] as $file) {
                $source = file_get_contents(__DIR__ . '/../src/utils/' . $file);
                $source = str_replace(
                    'namespace TailwindPHP\\Utils;',
                    'namespace ScopedVendor\\TailwindPHP\\Utils;',
                    $source,
                );
                file_put_contents($tempDir . '/' . $file, $source);
            }

            file_put_contents(
                $tempDir . '/run.php',
                <<<'PHP'
<?php

declare(strict_types=1);

require __DIR__ . '/segment.php';
require __DIR__ . '/math-operators.php';
require __DIR__ . '/is-color.php';
require __DIR__ . '/infer-data-type.php';

use function ScopedVendor\TailwindPHP\Utils\inferDataType;

$cases = [
    ['15px', ['color', 'length'], 'length'],
    ['#ff0000', ['color', 'length'], 'color'],
    ['50%', ['percentage', 'length'], 'percentage'],
];

foreach ($cases as [$value, $types, $expected]) {
    $actual = inferDataType($value, $types);

    if ($actual !== $expected) {
        fwrite(STDERR, "{$value}: expected {$expected}, got " . var_export($actual, true) . PHP_EOL);
        exit(1);
    }
}
PHP,
            );

            $output = [];
            $exitCode = 0;
            exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($tempDir . '/run.php') . ' 2>&1', $output, $exitCode);

            $this->assertSame(0, $exitCode, implode("\n", $output));
        } finally {
            foreach (glob($tempDir . '/*') ?: [] as $file) {
                unlink($file);
            }
            rmdir($tempDir);
        }
    }

    public function test_minified_arbitrary_math_values_preserve_function_spacing(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="h-[clamp(9.375rem,7.635rem_+_8.699vw,15.8125rem)] w-[calc(100%_+_10px)] mb-[min(1rem_+_2vw,3rem)]"></div>',
            'css' => '@import "tailwindcss/utilities";',
            'minify' => true,
        ]);

        $this->assertStringContainsString(
            'height:clamp(9.375rem, 7.635rem + 8.699vw, 15.8125rem)',
            $css,
        );
        $this->assertStringContainsString('width:calc(100% + 10px)', $css);
        $this->assertStringContainsString('margin-bottom:min(1rem + 2vw, 3rem)', $css);
        $this->assertStringNotContainsString('7.635rem+8.699vw', $css);
        $this->assertStringNotContainsString('7.635rem + 8.699vw,15.8125rem', $css);
    }
}
