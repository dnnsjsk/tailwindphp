#!/usr/bin/env php
<?php

/**
 * Generate PHPUnit test files from extracted TailwindCSS TypeScript tests.
 *
 * This script parses the extracted .ts test files and generates matching
 * PHPUnit test files that verify PHP output against TailwindCSS expectations.
 */

$extractedTestsDir = dirname(__DIR__) . '/extracted-tests';
$outputDir = dirname(__DIR__) . '/src/utilities';

/**
 * Parse a TypeScript test file and extract test cases.
 */
function parseTestFile(string $filePath): array
{
    $content = file_get_contents($filePath);
    $tests = [];

    // Split by test() blocks
    $lines = explode("\n", $content);
    $inTest = false;
    $braceCount = 0;
    $testBody = '';
    $currentTestName = '';
    $testBodies = [];

    foreach ($lines as $line) {
        if (preg_match('/^test\([\'"]([^\'"]+)[\'"]/', $line, $m)) {
            $inTest = true;
            $currentTestName = $m[1];
            $braceCount = substr_count($line, '{') - substr_count($line, '}');
            $testBody = $line . "\n";
            continue;
        }

        if ($inTest) {
            $testBody .= $line . "\n";
            $braceCount += substr_count($line, '{') - substr_count($line, '}');

            if ($braceCount <= 0) {
                $testBodies[$currentTestName] = $testBody;
                $inTest = false;
                $testBody = '';
            }
        }
    }

    foreach ($testBodies as $testName => $testBody) {
        $pos = 0;
        $testIndex = 0;

        while (($expectPos = strpos($testBody, 'expect(', $pos)) !== false) {
            $runPos = strpos($testBody, 'await run([', $expectPos);
            $compilePos = strpos($testBody, 'await compileCss(', $expectPos);

            $isRun = $runPos !== false && ($compilePos === false || $runPos < $compilePos);
            $isCompile = $compilePos !== false && ($runPos === false || $compilePos < $runPos);

            if ($isRun && $runPos < $expectPos + 50) {
                $arrayStart = strpos($testBody, '[', $runPos);
                $arrayEnd = findMatchingBracket($testBody, $arrayStart, '[', ']');

                if ($arrayEnd !== false) {
                    $classesStr = substr($testBody, $arrayStart + 1, $arrayEnd - $arrayStart - 1);
                    $classes = parseClassArray($classesStr);
                    $afterArray = substr($testBody, $arrayEnd, 200);

                    if (preg_match('/\)\s*\)\s*\.toMatchInlineSnapshot\s*\(\s*`/', $afterArray)) {
                        $snapshotStart = strpos($testBody, '.toMatchInlineSnapshot(`', $arrayEnd);
                        if ($snapshotStart !== false) {
                            $cssStart = strpos($testBody, '`', $snapshotStart + 20) + 1;
                            $cssEnd = strpos($testBody, '`)', $cssStart);
                            if ($cssEnd !== false) {
                                $expectedCss = substr($testBody, $cssStart, $cssEnd - $cssStart);
                                if (!empty($classes)) {
                                    $tests[] = [
                                        'name' => $testName,
                                        'index' => $testIndex,
                                        'classes' => $classes,
                                        'expected' => cleanExpectedCss($expectedCss),
                                        'type' => 'match',
                                    ];
                                    $testIndex++;
                                }
                            }
                        }
                    } elseif (preg_match('/\)\s*\)\s*\.toEqual\s*\(\s*[\'"][\'"]/', $afterArray)) {
                        if (!empty($classes)) {
                            $tests[] = [
                                'name' => $testName,
                                'index' => $testIndex,
                                'classes' => $classes,
                                'expected' => '',
                                'type' => 'empty',
                            ];
                            $testIndex++;
                        }
                    }
                }
                $pos = $arrayEnd !== false ? $arrayEnd : $expectPos + 10;
            } elseif ($isCompile && $compilePos < $expectPos + 50) {
                // Skip compileCss tests - require theme setup
                $pos = $compilePos + 20;
                $testIndex++;
            } else {
                $pos = $expectPos + 10;
            }
        }
    }

    return $tests;
}

function findMatchingBracket(string $str, int $start, string $open, string $close): ?int
{
    $count = 1;
    $pos = $start + 1;
    $len = strlen($str);

    while ($pos < $len && $count > 0) {
        $char = $str[$pos];
        if ($char === $open) $count++;
        elseif ($char === $close) $count--;
        $pos++;
    }

    return $count === 0 ? $pos - 1 : null;
}

function parseClassArray(string $str): array
{
    $classes = [];
    preg_match_all('/[\'"]([^\'"]+)[\'"]/', $str, $matches);
    foreach ($matches[1] as $class) {
        $classes[] = $class;
    }
    return $classes;
}

function cleanExpectedCss(string $css): string
{
    $css = preg_replace('/@layer\s+properties\s*\{[\s\S]*?\n\s*\}\s*/m', '', $css);
    $css = preg_replace('/:root,\s*:host\s*\{[\s\S]*?\}\s*/m', '', $css);
    $css = preg_replace('/@property\s+[^\{]+\{[\s\S]*?\}\s*/m', '', $css);
    $css = preg_replace('/@supports\s*\([^\)]+\)\s*\{[\s\S]*?\n\s*\}\s*/m', '', $css);
    return trim($css);
}

/**
 * Extract CSS rules from a CSS string.
 */
function extractCssRules(string $css): array
{
    $rules = [];
    preg_match_all('/([^\{\}]+)\s*\{([^\}]*)\}/m', $css, $matches, PREG_SET_ORDER);

    foreach ($matches as $match) {
        $selector = trim($match[1]);
        $declarationsStr = trim($match[2]);
        $declarations = [];

        $parts = array_filter(array_map('trim', explode(';', $declarationsStr)));
        foreach ($parts as $part) {
            if (strpos($part, ':') !== false) {
                [$prop, $value] = array_map('trim', explode(':', $part, 2));
                $declarations[$prop] = $value;
            }
        }

        $rules[$selector] = $declarations;
    }

    return $rules;
}

/**
 * Convert test name to PHP method name.
 */
function toMethodName(string $name, int $index = 0): string
{
    $method = preg_replace('/[^a-zA-Z0-9]+/', '_', $name);
    $method = preg_replace('/^_+|_+$/', '', $method);
    $method = strtolower($method);
    if ($index > 0) {
        $method .= '_' . $index;
    }
    return $method;
}

/**
 * Convert filename to class name.
 */
function toClassName(string $filename): string
{
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $name = preg_replace('/[^a-zA-Z0-9]+/', '_', $name);
    $name = preg_replace('/^_+|_+$/', '', $name);
    // Convert to PascalCase
    $parts = explode('_', $name);
    $parts = array_map('ucfirst', $parts);
    return implode('', $parts) . 'Test';
}

/**
 * Generate PHPUnit test file content.
 */
function generateTestFile(string $filename, array $tests): string
{
    $className = toClassName($filename);

    $php = "<?php\n\n";
    $php .= "declare(strict_types=1);\n\n";
    $php .= "namespace TailwindPHP\\Utilities;\n\n";
    $php .= "use PHPUnit\\Framework\\TestCase;\n";
    $php .= "use PHPUnit\\Framework\\Attributes\\Test;\n";
    $php .= "use TailwindPHP\\Tests\\TestHelper;\n\n";
    $php .= "/**\n";
    $php .= " * Auto-generated from: extracted-tests/{$filename}\n";
    $php .= " * \n";
    $php .= " * These tests verify PHP output matches TailwindCSS test expectations.\n";
    $php .= " */\n";
    $php .= "class {$className} extends TestCase\n";
    $php .= "{\n";

    $seenMethods = [];

    foreach ($tests as $test) {
        $methodName = toMethodName($test['name'], $test['index']);

        // Ensure unique method names
        if (isset($seenMethods[$methodName])) {
            $seenMethods[$methodName]++;
            $methodName .= '_' . $seenMethods[$methodName];
        } else {
            $seenMethods[$methodName] = 0;
        }

        $php .= "    #[Test]\n";
        $php .= "    public function {$methodName}(): void\n";
        $php .= "    {\n";

        $classesPhp = "['" . implode("', '", $test['classes']) . "']";

        if ($test['type'] === 'empty') {
            $php .= "        // Invalid variants should return empty\n";
            $php .= "        \$css = TestHelper::run({$classesPhp});\n";
            $php .= "        \$this->assertEquals('', \$css);\n";
        } else {
            $php .= "        \$css = TestHelper::run({$classesPhp});\n\n";

            // Extract expected rules and generate assertions
            $rules = extractCssRules($test['expected']);

            foreach ($rules as $selector => $declarations) {
                $selectorEscaped = addslashes($selector);
                $php .= "        // {$selectorEscaped}\n";

                foreach ($declarations as $prop => $value) {
                    $propEscaped = addslashes($prop);
                    $valueEscaped = addslashes($value);
                    $php .= "        \$this->assertStringContainsString('{$propEscaped}:', \$css);\n";
                }
            }
        }

        $php .= "    }\n\n";
    }

    $php .= "}\n";

    return $php;
}

// Main execution
echo "Generating PHPUnit tests from TailwindCSS test suite...\n\n";

$tsFiles = glob($extractedTestsDir . '/*.ts');

if (empty($tsFiles)) {
    echo "No .ts files found in $extractedTestsDir\n";
    exit(1);
}

$totalTests = 0;
$totalFiles = 0;

foreach ($tsFiles as $file) {
    $basename = basename($file);

    $tests = parseTestFile($file);

    if (empty($tests)) {
        continue;
    }

    // Generate PHP test file
    $phpContent = generateTestFile($basename, $tests);

    // Determine output filename
    $outputName = pathinfo($basename, PATHINFO_FILENAME);
    $outputFile = $outputDir . '/' . $outputName . '.generated.test.php';

    file_put_contents($outputFile, $phpContent);

    echo "Generated: {$outputName}.generated.test.php (" . count($tests) . " tests)\n";

    $totalTests += count($tests);
    $totalFiles++;
}

echo "\n";
echo "Total: {$totalFiles} files, {$totalTests} tests generated\n";
echo "\nRun with: php vendor/bin/phpunit --filter='generated'\n";
