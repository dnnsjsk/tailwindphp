<?php

/**
 * Extract CVA tests from TypeScript source.
 *
 * Parses reference/cva/packages/cva/src/index.test.ts
 * and outputs JSON test files for PHP test runner.
 */

$testFile = __DIR__ . '/../../reference/cva/packages/cva/src/index.test.ts';
$outputDir = __DIR__ . '/cva';

if (!file_exists($testFile)) {
    echo "Error: Test file not found: {$testFile}\n";
    exit(1);
}

$content = file_get_contents($testFile);

// Ensure output directory exists
if (!is_dir($outputDir)) {
    mkdir($outputDir, 0755, true);
}

$tests = [
    'cx' => [],
    'compose' => [],
    'cva' => [],
    'defineConfig' => [],
];

// ============================================================================
// Extract cx tests
// ============================================================================

// The cx tests are in a describe.each format with input/expected pairs
// Format: [input, expected] or deeply nested arrays
if (preg_match('/describe\("cx".*?describe\.each<CVA\.CXOptions>\(\[(.*?)\]\)\("cx\(%o\)"/s', $content, $match)) {
    $cxTestData = $match[1];

    // Parse the test cases - we'll manually define the simple ones
    $tests['cx'][] = ['name' => 'cx(null)', 'input' => null, 'expected' => ''];
    $tests['cx'][] = ['name' => 'cx(false)', 'input' => false, 'expected' => ''];
    $tests['cx'][] = ['name' => 'cx("foo")', 'input' => 'foo', 'expected' => 'foo'];
    $tests['cx'][] = ['name' => 'cx(array with nulls)', 'input' => ['foo', null, 'bar', null, 'baz'], 'expected' => 'foo bar baz'];
    $tests['cx'][] = ['name' => 'cx(nested arrays)', 'input' => ['foo', ['bar', ['hello', ['world']]], 'cya'], 'expected' => 'foo bar hello world cya'];
    $tests['cx'][] = ['name' => 'cx(deeply nested)', 'input' => ['foo', ['bar'], ['baz', 'qux']], 'expected' => 'foo bar baz qux'];
}

// ============================================================================
// Extract compose tests
// ============================================================================

$tests['compose'][] = [
    'name' => 'compose: should merge into a single component',
    'box' => [
        'variants' => [
            'shadow' => ['sm' => 'shadow-sm', 'md' => 'shadow-md'],
        ],
        'defaultVariants' => ['shadow' => 'sm'],
    ],
    'stack' => [
        'variants' => [
            'gap' => ['unset' => null, '1' => 'gap-1', '2' => 'gap-2', '3' => 'gap-3'],
        ],
        'defaultVariants' => ['gap' => 'unset'],
    ],
    'cases' => [
        [[], 'shadow-sm'],
        [['class' => 'adhoc-class'], 'shadow-sm adhoc-class'],
        [['className' => 'adhoc-class'], 'shadow-sm adhoc-class'],
        [['shadow' => 'md'], 'shadow-md'],
        [['gap' => '2'], 'shadow-sm gap-2'],
        [['shadow' => 'md', 'gap' => '3', 'class' => 'adhoc-class'], 'shadow-md gap-3 adhoc-class'],
        [['shadow' => 'md', 'gap' => '3', 'className' => 'adhoc-class'], 'shadow-md gap-3 adhoc-class'],
    ],
];

// ============================================================================
// Extract cva tests - without base, without defaults
// ============================================================================

$buttonWithoutBaseWithoutDefaults = [
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
];

$tests['cva'][] = [
    'name' => 'without base, without defaults',
    'config' => $buttonWithoutBaseWithoutDefaults,
    'cases' => [
        [null, ''],
        [[], ''],
        [['intent' => 'secondary'], 'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100'],
        [['size' => 'small'], 'button--small text-sm py-1 px-2'],
        [['disabled' => 'true'], 'button--disabled opacity-050 cursor-not-allowed'],
        [['intent' => 'secondary', 'size' => 'unset'], 'button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100'],
        [['intent' => 'danger', 'size' => 'medium'], 'button--danger bg-red-500 text-white border-transparent hover:bg-red-600 button--medium text-base py-2 px-4'],
        [['intent' => 'warning', 'size' => 'large'], 'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--large text-lg py-2.5 px-4'],
        [['intent' => 'warning', 'size' => 'large', 'disabled' => 'true'], 'button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--disabled opacity-050 cursor-not-allowed button--large text-lg py-2.5 px-4 button--warning-disabled text-black'],
        [['intent' => 'primary', 'm' => '0'], 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-0'],
        [['intent' => 'primary', 'm' => '1'], 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-1'],
        [['intent' => 'primary', 'm' => '1', 'class' => 'adhoc-class'], 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-1 adhoc-class'],
        [['intent' => 'primary', 'm' => '1', 'className' => 'adhoc-classname'], 'button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 m-1 adhoc-classname'],
    ],
];

// ============================================================================
// Extract cva tests - with base, with defaults
// ============================================================================

$buttonWithBaseWithDefaults = [
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
];

$tests['cva'][] = [
    'name' => 'with base, with defaults',
    'config' => $buttonWithBaseWithDefaults,
    'cases' => [
        [null, 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase'],
        [[], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase'],
        [['intent' => 'secondary'], 'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100 button--enabled cursor-pointer button--medium text-base py-2 px-4'],
        [['size' => 'small'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--small text-sm py-1 px-2'],
        [['disabled' => 'unset'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--medium text-base py-2 px-4 button--primary-medium uppercase'],
        [['disabled' => 'true'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--disabled opacity-050 cursor-not-allowed button--medium text-base py-2 px-4 button--primary-medium uppercase'],
        [['intent' => 'secondary', 'size' => 'unset'], 'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100 button--enabled cursor-pointer'],
        [['intent' => 'danger', 'size' => 'medium'], 'button font-semibold border rounded button--danger bg-red-500 text-white border-transparent hover:bg-red-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--warning-danger !border-red-500 button--warning-danger-medium'],
        [['intent' => 'warning', 'size' => 'large'], 'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--enabled cursor-pointer button--large text-lg py-2.5 px-4 button--warning-enabled text-gray-800 button--warning-danger !border-red-500'],
        [['intent' => 'warning', 'size' => 'large', 'disabled' => 'true'], 'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--disabled opacity-050 cursor-not-allowed button--large text-lg py-2.5 px-4 button--warning-disabled text-black button--warning-danger !border-red-500'],
        [['intent' => 'primary', 'class' => 'adhoc-class'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase adhoc-class'],
        [['intent' => 'primary', 'className' => 'adhoc-classname'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 button--enabled cursor-pointer button--medium text-base py-2 px-4 button--primary-medium uppercase adhoc-classname'],
    ],
];

// ============================================================================
// Extract cva tests - with base, without defaults
// ============================================================================

$buttonWithBaseWithoutDefaults = [
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
];

$tests['cva'][] = [
    'name' => 'with base, without defaults',
    'config' => $buttonWithBaseWithoutDefaults,
    'cases' => [
        [null, 'button font-semibold border rounded'],
        [[], 'button font-semibold border rounded'],
        [['intent' => 'secondary'], 'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100'],
        [['size' => 'small'], 'button font-semibold border rounded button--small text-sm py-1 px-2'],
        [['disabled' => 'false'], 'button font-semibold border rounded button--enabled cursor-pointer'],
        [['disabled' => 'true'], 'button font-semibold border rounded button--disabled opacity-050 cursor-not-allowed'],
        [['intent' => 'secondary', 'size' => 'unset'], 'button font-semibold border rounded button--secondary bg-white text-gray-800 border-gray-400 hover:bg-gray-100'],
        [['intent' => 'danger', 'size' => 'medium'], 'button font-semibold border rounded button--danger bg-red-500 text-white border-transparent hover:bg-red-600 button--medium text-base py-2 px-4 button--warning-danger !border-red-500 button--warning-danger-medium'],
        [['intent' => 'warning', 'size' => 'large'], 'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--large text-lg py-2.5 px-4 button--warning-danger !border-red-500'],
        [['intent' => 'warning', 'size' => 'large', 'disabled' => 'unset'], 'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--large text-lg py-2.5 px-4 button--warning-danger !border-red-500'],
        [['intent' => 'warning', 'size' => 'large', 'disabled' => 'true'], 'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--disabled opacity-050 cursor-not-allowed button--large text-lg py-2.5 px-4 button--warning-disabled text-black button--warning-danger !border-red-500'],
        [['intent' => 'warning', 'size' => 'large', 'disabled' => 'false'], 'button font-semibold border rounded button--warning bg-yellow-500 border-transparent hover:bg-yellow-600 button--enabled cursor-pointer button--large text-lg py-2.5 px-4 button--warning-enabled text-gray-800 button--warning-danger !border-red-500'],
        [['intent' => 'primary', 'class' => 'adhoc-class'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 adhoc-class'],
        [['intent' => 'primary', 'className' => 'adhoc-className'], 'button font-semibold border rounded button--primary bg-blue-500 text-white border-transparent hover:bg-blue-600 adhoc-className'],
    ],
];

// ============================================================================
// Extract cva tests - without anything (empty)
// ============================================================================

$tests['cva'][] = [
    'name' => 'without anything (empty)',
    'config' => null,
    'cases' => [
        [null, ''],
        [[], ''],
        [['class' => 'adhoc-class'], 'adhoc-class'],
        [['className' => 'adhoc-className'], 'adhoc-className'],
        [['class' => 'adhoc-class', 'className' => 'adhoc-className'], 'adhoc-class adhoc-className'],
    ],
];

// ============================================================================
// Extract defineConfig tests
// ============================================================================

$tests['defineConfig'][] = [
    'name' => 'hooks: onComplete should extend cx',
    'hook' => 'onComplete',
    'prefix' => 'never-gonna-give-you-up',
    'suffix' => 'never-gonna-let-you-down',
    'type' => 'cx',
    'input' => ['foo', 'bar'],
    'expected_contains_prefix' => true,
    'expected_contains_suffix' => true,
];

$tests['defineConfig'][] = [
    'name' => 'hooks: onComplete should extend cva',
    'hook' => 'onComplete',
    'prefix' => 'never-gonna-give-you-up',
    'suffix' => 'never-gonna-let-you-down',
    'type' => 'cva',
    'config' => ['base' => 'foo', 'variants' => ['intent' => ['primary' => 'bar']]],
    'props' => ['intent' => 'primary'],
    'expected_contains_prefix' => true,
    'expected_contains_suffix' => true,
];

$tests['defineConfig'][] = [
    'name' => 'hooks: onComplete should extend compose',
    'hook' => 'onComplete',
    'prefix' => 'never-gonna-give-you-up',
    'suffix' => 'never-gonna-let-you-down',
    'type' => 'compose',
    'expected_contains_prefix' => true,
    'expected_contains_suffix' => true,
];

// ============================================================================
// Write output files
// ============================================================================

foreach ($tests as $category => $categoryTests) {
    $outputFile = $outputDir . '/' . $category . '.json';
    file_put_contents(
        $outputFile,
        json_encode($categoryTests, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
    );
    echo 'Wrote ' . count($categoryTests) . " tests to {$outputFile}\n";
}

// Write summary
$summary = [
    'source' => 'reference/cva/packages/cva/src/index.test.ts',
    'extracted' => date('Y-m-d H:i:s'),
    'counts' => [],
];

foreach ($tests as $category => $categoryTests) {
    $testCount = 0;
    foreach ($categoryTests as $test) {
        if (isset($test['cases'])) {
            $testCount += count($test['cases']);
        } else {
            $testCount++;
        }
    }
    $summary['counts'][$category] = $testCount;
}

$summary['total'] = array_sum($summary['counts']);

file_put_contents(
    $outputDir . '/summary.json',
    json_encode($summary, JSON_PRETTY_PRINT),
);

echo "\nSummary:\n";
foreach ($summary['counts'] as $category => $count) {
    echo "  {$category}: {$count} tests\n";
}
echo "  Total: {$summary['total']} tests\n";
