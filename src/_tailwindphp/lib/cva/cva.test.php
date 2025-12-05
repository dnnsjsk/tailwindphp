<?php

declare(strict_types=1);

/**
 * Tests for CVA (Class Variance Authority) PHP port.
 *
 * Tests are extracted from reference/cva/packages/cva/src/index.test.ts
 */

namespace TailwindPHP\Lib\Cva;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/cva.php';

class cva extends TestCase
{
    // ==================================================
    // cx tests
    // ==================================================

    public static function cxTestProvider(): array
    {
        $testFile = __DIR__ . '/../../../../test-coverage/lib/cva/cx.json';
        if (!file_exists($testFile)) {
            return [];
        }

        $tests = json_decode(file_get_contents($testFile), true);
        $data = [];

        foreach ($tests as $test) {
            $data[$test['name']] = [$test['input'], $test['expected']];
        }

        return $data;
    }

    #[DataProvider('cxTestProvider')]
    #[Test]
    public function test_cx(mixed $input, string $expected): void
    {
        if (is_array($input)) {
            $result = cx(...$input);
        } else {
            $result = cx($input);
        }

        $this->assertSame($expected, $result);
    }

    // ==================================================
    // compose tests
    // ==================================================

    #[Test]
    public function test_compose_merges_components(): void
    {
        $box = cva([
            'variants' => [
                'shadow' => [
                    'sm' => 'shadow-sm',
                    'md' => 'shadow-md',
                ],
            ],
            'defaultVariants' => [
                'shadow' => 'sm',
            ],
        ]);

        $stack = cva([
            'variants' => [
                'gap' => [
                    'unset' => null,
                    '1' => 'gap-1',
                    '2' => 'gap-2',
                    '3' => 'gap-3',
                ],
            ],
            'defaultVariants' => [
                'gap' => 'unset',
            ],
        ]);

        $card = compose($box, $stack);

        $this->assertSame('shadow-sm', $card());
        $this->assertSame('shadow-sm adhoc-class', $card(['class' => 'adhoc-class']));
        $this->assertSame('shadow-sm adhoc-class', $card(['className' => 'adhoc-class']));
        $this->assertSame('shadow-md', $card(['shadow' => 'md']));
        $this->assertSame('shadow-sm gap-2', $card(['gap' => '2']));
        $this->assertSame('shadow-md gap-3 adhoc-class', $card(['shadow' => 'md', 'gap' => '3', 'class' => 'adhoc-class']));
        $this->assertSame('shadow-md gap-3 adhoc-class', $card(['shadow' => 'md', 'gap' => '3', 'className' => 'adhoc-class']));
    }

    // ==================================================
    // cva tests - without anything (empty)
    // ==================================================

    #[Test]
    public function test_cva_empty(): void
    {
        $example = cva();

        $this->assertSame('', $example());
        $this->assertSame('', $example([]));
        $this->assertSame('adhoc-class', $example(['class' => 'adhoc-class']));
        $this->assertSame('adhoc-className', $example(['className' => 'adhoc-className']));
        $this->assertSame('adhoc-class adhoc-className', $example(['class' => 'adhoc-class', 'className' => 'adhoc-className']));
    }

    // ==================================================
    // cva tests - without base, without defaults
    // ==================================================

    public static function cvaWithoutBaseWithoutDefaultsProvider(): array
    {
        return [
            'undefined' => [null, ''],
            'empty' => [[], ''],
            'intent secondary' => [
                ['intent' => 'secondary'],
                'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
            ],
            'size small' => [
                ['size' => 'small'],
                'button--small text-sm py-1 px-2',
            ],
            'disabled true' => [
                ['disabled' => 'true'],
                'button--disabled opacity-050 cursor-not-allowed',
            ],
            'intent secondary size unset' => [
                ['intent' => 'secondary', 'size' => 'unset'],
                'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
            ],
            'intent danger size medium' => [
                ['intent' => 'danger', 'size' => 'medium'],
                'button--danger bg-red-500 text-white border-transparent hover:bg-red-600 button--medium text-base py-2 px-4',
            ],
            'intent warning size large' => [
                ['intent' => 'warning', 'size' => 'large'],
                'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--large text-lg py-2.5 px-4',
            ],
            'intent warning size large disabled true (compound)' => [
                ['intent' => 'warning', 'size' => 'large', 'disabled' => 'true'],
                'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--disabled opacity-050 cursor-not-allowed button--large text-lg py-2.5 px-4 button--warning-disabled text-black',
            ],
            'intent primary m 0' => [
                ['intent' => 'primary', 'm' => '0'],
                'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-0',
            ],
            'intent primary m 1' => [
                ['intent' => 'primary', 'm' => '1'],
                'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-1',
            ],
            'with class' => [
                ['intent' => 'primary', 'm' => '1', 'class' => 'adhoc-class'],
                'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-1 adhoc-class',
            ],
            'with className' => [
                ['intent' => 'primary', 'm' => '1', 'className' => 'adhoc-classname'],
                'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-1 adhoc-classname',
            ],
        ];
    }

    #[DataProvider('cvaWithoutBaseWithoutDefaultsProvider')]
    #[Test]
    public function test_cva_without_base_without_defaults(?array $props, string $expected): void
    {
        $button = cva([
            'variants' => [
                'intent' => [
                    'unset' => null,
                    'primary' => 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600',
                    'secondary' => 'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
                    'warning' => 'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600',
                    'danger' => 'button--danger bg-red-500 text-white border-transparent hover:bg-red-600',
                ],
                'disabled' => [
                    'unset' => null,
                    'true' => 'button--disabled opacity-050 cursor-not-allowed',
                    'false' => 'button--enabled cursor-pointer',
                ],
                'size' => [
                    'unset' => null,
                    'small' => 'button--small text-sm py-1 px-2',
                    'medium' => 'button--medium text-base py-2 px-4',
                    'large' => 'button--large text-lg py-2.5 px-4',
                ],
                'm' => [
                    'unset' => null,
                    '0' => 'm-0',
                    '1' => 'm-1',
                ],
            ],
            'compoundVariants' => [
                ['intent' => 'primary', 'size' => 'medium', 'class' => 'button--primary-medium uppercase'],
                ['intent' => 'warning', 'disabled' => 'false', 'class' => 'button--warning-enabled text-gray-800'],
                ['intent' => 'warning', 'disabled' => 'true', 'class' => 'button--warning-disabled text-black'],
            ],
        ]);

        $this->assertSame($expected, $button($props));
    }

    // ==================================================
    // cva tests - with base, with defaults
    // ==================================================

    public static function cvaWithBaseWithDefaultsProvider(): array
    {
        return [
            'undefined' => [
                null,
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase',
            ],
            'empty' => [
                [],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase',
            ],
            'intent secondary' => [
                ['intent' => 'secondary'],
                'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100 button--enabled cursor-pointer button--medium text-base py-2 px-4',
            ],
            'size small' => [
                ['size' => 'small'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--small text-sm py-1 px-2',
            ],
            'disabled unset' => [
                ['disabled' => 'unset'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--medium text-base py-2 px-4 button--primary-medium uppercase',
            ],
            'disabled true' => [
                ['disabled' => 'true'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--disabled opacity-050 cursor-not-allowed button--medium text-base py-2 px-4 button--primary-medium uppercase',
            ],
            'intent secondary size unset' => [
                ['intent' => 'secondary', 'size' => 'unset'],
                'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100 button--enabled cursor-pointer',
            ],
            'intent danger size medium (compound with array)' => [
                ['intent' => 'danger', 'size' => 'medium'],
                'button font-semibold border rounded button--danger bg-red-500 text-white border-transparent hover:bg-red-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--warning-danger !border-red-500 button--warning-danger-medium',
            ],
            'intent warning size large (compound with array)' => [
                ['intent' => 'warning', 'size' => 'large'],
                'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--enabled cursor-pointer button--large text-lg py-2.5 px-4 button--warning-enabled text-gray-800 button--warning-danger !border-red-500',
            ],
            'intent warning size large disabled true' => [
                ['intent' => 'warning', 'size' => 'large', 'disabled' => 'true'],
                'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--disabled opacity-050 cursor-not-allowed button--large text-lg py-2.5 px-4 button--warning-disabled text-black button--warning-danger !border-red-500',
            ],
            'with class' => [
                ['intent' => 'primary', 'class' => 'adhoc-class'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase adhoc-class',
            ],
            'with className' => [
                ['intent' => 'primary', 'className' => 'adhoc-classname'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase adhoc-classname',
            ],
        ];
    }

    #[DataProvider('cvaWithBaseWithDefaultsProvider')]
    #[Test]
    public function test_cva_with_base_with_defaults(?array $props, string $expected): void
    {
        $button = cva([
            'base' => 'button font-semibold border rounded',
            'variants' => [
                'intent' => [
                    'unset' => null,
                    'primary' => 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600',
                    'secondary' => 'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
                    'warning' => 'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600',
                    'danger' => 'button--danger bg-red-500 text-white border-transparent hover:bg-red-600',
                ],
                'disabled' => [
                    'unset' => null,
                    'true' => 'button--disabled opacity-050 cursor-not-allowed',
                    'false' => 'button--enabled cursor-pointer',
                ],
                'size' => [
                    'unset' => null,
                    'small' => 'button--small text-sm py-1 px-2',
                    'medium' => 'button--medium text-base py-2 px-4',
                    'large' => 'button--large text-lg py-2.5 px-4',
                ],
            ],
            'compoundVariants' => [
                ['intent' => 'primary', 'size' => 'medium', 'class' => 'button--primary-medium uppercase'],
                ['intent' => 'warning', 'disabled' => 'false', 'class' => 'button--warning-enabled text-gray-800'],
                ['intent' => 'warning', 'disabled' => 'true', 'class' => 'button--warning-disabled text-black'],
                ['intent' => ['warning', 'danger'], 'class' => 'button--warning-danger !border-red-500'],
                ['intent' => ['warning', 'danger'], 'size' => 'medium', 'class' => 'button--warning-danger-medium'],
            ],
            'defaultVariants' => [
                'disabled' => 'false',
                'intent' => 'primary',
                'size' => 'medium',
            ],
        ]);

        $this->assertSame($expected, $button($props));
    }

    // ==================================================
    // cva tests - with base, without defaults
    // ==================================================

    public static function cvaWithBaseWithoutDefaultsProvider(): array
    {
        return [
            'undefined' => [null, 'button font-semibold border rounded'],
            'empty' => [[], 'button font-semibold border rounded'],
            'intent secondary' => [
                ['intent' => 'secondary'],
                'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
            ],
            'size small' => [
                ['size' => 'small'],
                'button font-semibold border rounded button--small text-sm py-1 px-2',
            ],
            'disabled false' => [
                ['disabled' => 'false'],
                'button font-semibold border rounded button--enabled cursor-pointer',
            ],
            'disabled true' => [
                ['disabled' => 'true'],
                'button font-semibold border rounded button--disabled opacity-050 cursor-not-allowed',
            ],
            'intent secondary size unset' => [
                ['intent' => 'secondary', 'size' => 'unset'],
                'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
            ],
            'intent danger size medium' => [
                ['intent' => 'danger', 'size' => 'medium'],
                'button font-semibold border rounded button--danger bg-red-500 text-white border-transparent hover:bg-red-600 button--medium text-base py-2 px-4 button--warning-danger !border-red-500 button--warning-danger-medium',
            ],
            'intent warning size large' => [
                ['intent' => 'warning', 'size' => 'large'],
                'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--large text-lg py-2.5 px-4 button--warning-danger !border-red-500',
            ],
            'intent warning size large disabled unset' => [
                ['intent' => 'warning', 'size' => 'large', 'disabled' => 'unset'],
                'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--large text-lg py-2.5 px-4 button--warning-danger !border-red-500',
            ],
            'intent warning size large disabled true' => [
                ['intent' => 'warning', 'size' => 'large', 'disabled' => 'true'],
                'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--disabled opacity-050 cursor-not-allowed button--large text-lg py-2.5 px-4 button--warning-disabled text-black button--warning-danger !border-red-500',
            ],
            'intent warning size large disabled false' => [
                ['intent' => 'warning', 'size' => 'large', 'disabled' => 'false'],
                'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--enabled cursor-pointer button--large text-lg py-2.5 px-4 button--warning-enabled text-gray-800 button--warning-danger !border-red-500',
            ],
            'with class' => [
                ['intent' => 'primary', 'class' => 'adhoc-class'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 adhoc-class',
            ],
            'with className' => [
                ['intent' => 'primary', 'className' => 'adhoc-className'],
                'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 adhoc-className',
            ],
        ];
    }

    #[DataProvider('cvaWithBaseWithoutDefaultsProvider')]
    #[Test]
    public function test_cva_with_base_without_defaults(?array $props, string $expected): void
    {
        $button = cva([
            'base' => 'button font-semibold border rounded',
            'variants' => [
                'intent' => [
                    'unset' => null,
                    'primary' => 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600',
                    'secondary' => 'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100',
                    'warning' => 'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600',
                    'danger' => 'button--danger bg-red-500 text-white border-transparent hover:bg-red-600',
                ],
                'disabled' => [
                    'unset' => null,
                    'true' => 'button--disabled opacity-050 cursor-not-allowed',
                    'false' => 'button--enabled cursor-pointer',
                ],
                'size' => [
                    'unset' => null,
                    'small' => 'button--small text-sm py-1 px-2',
                    'medium' => 'button--medium text-base py-2 px-4',
                    'large' => 'button--large text-lg py-2.5 px-4',
                ],
            ],
            'compoundVariants' => [
                ['intent' => 'primary', 'size' => 'medium', 'class' => 'button--primary-medium uppercase'],
                ['intent' => 'warning', 'disabled' => 'false', 'class' => 'button--warning-enabled text-gray-800'],
                ['intent' => 'warning', 'disabled' => 'true', 'class' => 'button--warning-disabled text-black'],
                ['intent' => ['warning', 'danger'], 'class' => 'button--warning-danger !border-red-500'],
                ['intent' => ['warning', 'danger'], 'size' => 'medium', 'class' => 'button--warning-danger-medium'],
            ],
        ]);

        $this->assertSame($expected, $button($props));
    }

    // ==================================================
    // defineConfig tests
    // ==================================================

    #[Test]
    public function test_defineConfig_onComplete_extends_cx(): void
    {
        $prefix = 'never-gonna-give-you-up';
        $suffix = 'never-gonna-let-you-down';

        $config = defineConfig([
            'hooks' => [
                'onComplete' => fn ($className) => "{$prefix} {$className} {$suffix}",
            ],
        ]);

        $result = $config['cx']('foo', 'bar');
        $parts = explode(' ', $result);

        $this->assertSame($prefix, $parts[0]);
        $this->assertSame($suffix, $parts[count($parts) - 1]);
    }

    #[Test]
    public function test_defineConfig_onComplete_extends_cva(): void
    {
        $prefix = 'never-gonna-give-you-up';
        $suffix = 'never-gonna-let-you-down';

        $config = defineConfig([
            'hooks' => [
                'onComplete' => fn ($className) => "{$prefix} {$className} {$suffix}",
            ],
        ]);

        $component = $config['cva']([
            'base' => 'foo',
            'variants' => ['intent' => ['primary' => 'bar']],
        ]);

        $result = $component(['intent' => 'primary']);
        $parts = explode(' ', $result);

        $this->assertSame($prefix, $parts[0]);
        $this->assertSame($suffix, $parts[count($parts) - 1]);
    }

    #[Test]
    public function test_defineConfig_onComplete_extends_compose(): void
    {
        $prefix = 'never-gonna-give-you-up';
        $suffix = 'never-gonna-let-you-down';

        $config = defineConfig([
            'hooks' => [
                'onComplete' => fn ($className) => "{$prefix} {$className} {$suffix}",
            ],
        ]);

        $box = cva([
            'variants' => [
                'shadow' => ['sm' => 'shadow-sm', 'md' => 'shadow-md'],
            ],
            'defaultVariants' => ['shadow' => 'sm'],
        ]);

        $stack = cva([
            'variants' => [
                'gap' => ['unset' => null, '1' => 'gap-1', '2' => 'gap-2'],
            ],
            'defaultVariants' => ['gap' => 'unset'],
        ]);

        $card = $config['compose']($box, $stack);
        $result = $card();
        $parts = explode(' ', $result);

        $this->assertSame($prefix, $parts[0]);
        $this->assertSame($suffix, $parts[count($parts) - 1]);
    }
}
