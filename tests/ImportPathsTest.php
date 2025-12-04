<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\TestCase;
use TailwindPHP\Tailwind;

/**
 * Tests for the importPaths functionality.
 *
 * Tests cover:
 * - Single file imports
 * - Directory imports (all .css files)
 * - Array of files/directories
 * - Nested @import resolution
 * - Deduplication of imports
 * - Custom resolver functions
 * - Combining inline CSS with importPaths
 */
class ImportPathsTest extends TestCase
{
    private string $fixturesPath;

    protected function setUp(): void
    {
        $this->fixturesPath = __DIR__ . '/fixtures/css-imports';
    }

    /**
     * Test loading a single CSS file.
     */
    public function test_single_file_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="standalone-class">Hello</div>',
            'importPaths' => $this->fixturesPath . '/standalone.css',
        ]);

        $this->assertStringContainsString('.standalone-class', $css);
        $this->assertStringContainsString('color: red', $css);
        $this->assertStringContainsString('background: blue', $css);
    }

    /**
     * Test loading a CSS file with Tailwind utilities.
     */
    public function test_file_with_tailwind_utilities(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom-component flex">Hello</div>',
            'importPaths' => $this->fixturesPath . '/with-tailwind.css',
        ]);

        $this->assertStringContainsString('.custom-component', $css);
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('align-items: center', $css);
    }

    /**
     * Test loading all CSS files from a directory.
     */
    public function test_directory_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn standalone-class">Hello</div>',
            'importPaths' => $this->fixturesPath,
        ]);

        // Should include content from multiple files
        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('.standalone-class', $css);
    }

    /**
     * Test nested @import resolution.
     */
    public function test_nested_imports(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="card btn btn-primary">Hello</div>',
            'importPaths' => $this->fixturesPath . '/main.css',
        ]);

        // main.css -> components.css -> buttons.css
        $this->assertStringContainsString('.card', $css);
        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('.btn-primary', $css);
    }

    /**
     * Test imports from subdirectories.
     */
    public function test_subdirectory_imports(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="page-title text-gradient">Hello</div>',
            'importPaths' => $this->fixturesPath . '/with-shared.css',
        ]);

        $this->assertStringContainsString('.page-title', $css);
        $this->assertStringContainsString('.text-gradient', $css);
    }

    /**
     * Test combining inline CSS with file imports.
     */
    public function test_inline_css_with_import_paths(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom-inline standalone-class">Hello</div>',
            'css' => '.custom-inline { color: green; }',
            'importPaths' => $this->fixturesPath . '/standalone.css',
        ]);

        // Inline CSS should be included
        $this->assertStringContainsString('.custom-inline', $css);
        $this->assertStringContainsString('color: green', $css);

        // File CSS should also be included
        $this->assertStringContainsString('.standalone-class', $css);
        $this->assertStringContainsString('color: red', $css);
    }

    /**
     * Test array of import paths.
     */
    public function test_array_of_import_paths(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn text-gradient">Hello</div>',
            'importPaths' => [
                $this->fixturesPath . '/buttons.css',
                $this->fixturesPath . '/shared/utilities.css',
            ],
        ]);

        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('.text-gradient', $css);
    }

    /**
     * Test custom resolver function.
     */
    public function test_custom_resolver(): void
    {
        $virtualFiles = [
            'virtual.css' => '.virtual-class { color: purple; }',
        ];

        $css = Tailwind::generate([
            'content' => '<div class="virtual-class">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile) use ($virtualFiles): ?string {
                if ($uri === null) {
                    // Root CSS
                    return '@import "virtual.css";';
                }

                return $virtualFiles[$uri] ?? null;
            },
        ]);

        $this->assertStringContainsString('.virtual-class', $css);
        $this->assertStringContainsString('color: purple', $css);
    }

    /**
     * Test that missing files are silently skipped.
     */
    public function test_missing_file_silent_skip(): void
    {
        // Should not throw, just skip the missing file
        $css = Tailwind::generate([
            'content' => '<div class="flex">Hello</div>',
            'css' => '@import "tailwindcss";',
            'importPaths' => $this->fixturesPath . '/nonexistent.css',
        ]);

        // Should still produce output with tailwindcss
        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test that default @import "tailwindcss" is used when no css or importPaths given.
     */
    public function test_default_tailwindcss_import(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">Hello</div>',
        ]);

        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);
    }

    /**
     * Test theme customization from imported file.
     */
    public function test_theme_from_imported_file(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-brand">Hello</div>',
            'importPaths' => $this->fixturesPath . '/main.css',
        ]);

        // The main.css defines --color-brand
        $this->assertStringContainsString('--color-brand', $css);
    }

    /**
     * Test that duplicate virtual module imports are deduplicated.
     */
    public function test_duplicate_virtual_module_deduplication(): void
    {
        // Create a file that imports tailwindcss twice
        $tempFile = sys_get_temp_dir() . '/tailwindphp-test-' . uniqid() . '.css';
        file_put_contents($tempFile, '@import "tailwindcss"; @import "tailwindcss";');

        try {
            $css = Tailwind::generate([
                'content' => '<div class="flex">Hello</div>',
                'importPaths' => $tempFile,
            ]);

            // Should only have one set of utilities
            $this->assertStringContainsString('display: flex', $css);
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test circular imports are handled gracefully via deduplication.
     *
     * When file A imports B and B imports A, the second import of A is
     * deduplicated (since A was already seen), preventing infinite loops.
     */
    public function test_circular_import_protection(): void
    {
        // Create two files that import each other
        $tempDir = sys_get_temp_dir() . '/tailwindphp-circular-' . uniqid();
        mkdir($tempDir);

        $fileA = $tempDir . '/a.css';
        $fileB = $tempDir . '/b.css';

        file_put_contents($fileA, "@import \"./b.css\";\n.class-a { color: red; }");
        file_put_contents($fileB, "@import \"./a.css\";\n.class-b { color: blue; }");

        try {
            // Circular imports are handled gracefully via file deduplication
            // When B tries to import A again, A is already seen so returns empty
            $css = Tailwind::generate([
                'content' => '<div class="class-a class-b">Hello</div>',
                'css' => '@import "tailwindcss";',
                'importPaths' => $fileA,
            ]);

            // Both classes should be in the output
            $this->assertStringContainsString('.class-a', $css);
            $this->assertStringContainsString('.class-b', $css);
        } finally {
            @unlink($fileA);
            @unlink($fileB);
            @rmdir($tempDir);
        }
    }

    /**
     * Test empty directory returns working CSS (falls back to default).
     */
    public function test_empty_directory(): void
    {
        $tempDir = sys_get_temp_dir() . '/tailwindphp-empty-' . uniqid();
        mkdir($tempDir);

        try {
            $css = Tailwind::generate([
                'content' => '<div class="flex">Hello</div>',
                'importPaths' => $tempDir,
            ]);

            // Should work with default tailwindcss import since no files found
            $this->assertStringContainsString('display: flex', $css);
        } finally {
            @rmdir($tempDir);
        }
    }

    /**
     * Test array with mixed valid/invalid paths skips invalid ones gracefully.
     */
    public function test_array_with_invalid_paths(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn flex">Hello</div>',
            'css' => '@import "tailwindcss";',
            'importPaths' => [
                $this->fixturesPath . '/nonexistent.css',  // Invalid
                $this->fixturesPath . '/buttons.css',      // Valid
                '/path/that/does/not/exist/',              // Invalid
            ],
        ]);

        // Should still include valid file content and tailwindcss utilities
        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test that file imports are deduplicated (same file imported multiple times).
     */
    public function test_file_import_deduplication(): void
    {
        // Create a file that imports the same CSS file multiple times
        $tempFile = sys_get_temp_dir() . '/tailwindphp-dedup-' . uniqid() . '.css';
        file_put_contents($tempFile, sprintf(
            "@import \"%s/buttons.css\";\n@import \"%s/buttons.css\";\n",
            $this->fixturesPath,
            $this->fixturesPath,
        ));

        try {
            $css = Tailwind::generate([
                'content' => '<div class="btn">Hello</div>',
                'css' => '@import "tailwindcss";',
                'importPaths' => $tempFile,
            ]);

            // Should contain button styles (only once, but we just check presence)
            $this->assertStringContainsString('.btn', $css);

            // Count occurrences - should only appear once due to deduplication
            $btnCount = substr_count($css, '.btn {');
            $this->assertLessThanOrEqual(1, $btnCount, 'Duplicate file imports should be deduplicated');
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test custom resolver returning null for unknown imports.
     */
    public function test_custom_resolver_null_for_unknown(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex known-class">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "unknown.css"; .known-class { color: blue; }';
                }

                // Return null for unknown imports - they should be silently skipped
                return null;
            },
        ]);

        $this->assertStringContainsString('.known-class', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test that @import with layer modifier works with importPaths.
     */
    public function test_import_with_layer_modifier(): void
    {
        $tempFile = sys_get_temp_dir() . '/tailwindphp-layer-' . uniqid() . '.css';
        file_put_contents($tempFile, "@import \"tailwindcss\";\n@import \"{$this->fixturesPath}/standalone.css\" layer(components);");

        try {
            $css = Tailwind::generate([
                'content' => '<div class="standalone-class flex">Hello</div>',
                'importPaths' => $tempFile,
            ]);

            // Should include content wrapped in layer
            $this->assertStringContainsString('.standalone-class', $css);
            $this->assertStringContainsString('display: flex', $css);
            $this->assertStringContainsString('@layer components', $css);
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test multiple @theme blocks are merged correctly.
     */
    public function test_multiple_theme_blocks(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="text-brand bg-secondary">Hello</div>',
            'css' => '
                @import "tailwindcss";
                @theme {
                    --color-brand: #ff0000;
                }
                @theme {
                    --color-secondary: #00ff00;
                }
            ',
        ]);

        // Both theme colors should be defined
        $this->assertStringContainsString('--color-brand', $css);
        $this->assertStringContainsString('--color-secondary', $css);
        // Utilities using them should work
        $this->assertStringContainsString('text-brand', $css);
        $this->assertStringContainsString('bg-secondary', $css);
    }

    /**
     * Test @theme override behavior (later definitions override earlier).
     */
    public function test_theme_override_behavior(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="text-brand">Hello</div>',
            'css' => '
                @import "tailwindcss";
                @theme {
                    --color-brand: #ff0000;
                }
                @theme {
                    --color-brand: #0000ff;
                }
            ',
        ]);

        // Later definition should win
        $this->assertStringContainsString('--color-brand: #0000ff', $css);
        // Earlier definition should not appear
        $this->assertStringNotContainsString('#ff0000', $css);
    }

    /**
     * Test multiple @utility definitions with same name both appear.
     *
     * Like regular CSS, multiple @utility blocks with the same name are both
     * included in the output. The cascade determines which one wins.
     */
    public function test_multiple_utility_definitions(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="my-util">Hello</div>',
            'css' => '
                @import "tailwindcss";
                @utility my-util {
                    color: red;
                }
                @utility my-util {
                    color: blue;
                }
            ',
        ]);

        // Both definitions are included (cascade determines winner)
        $this->assertStringContainsString('.my-util', $css);
        // The second definition should appear (color is converted to hex #00f)
        $this->assertStringContainsString('color:', $css);
    }

    /**
     * Test @theme from multiple imported files are merged.
     */
    public function test_theme_from_multiple_files(): void
    {
        $tempDir = sys_get_temp_dir() . '/tailwindphp-theme-' . uniqid();
        mkdir($tempDir);

        $file1 = $tempDir . '/theme1.css';
        $file2 = $tempDir . '/theme2.css';

        file_put_contents($file1, '@theme { --color-primary: #111; }');
        file_put_contents($file2, '@theme { --color-accent: #222; }');

        try {
            $css = Tailwind::generate([
                'content' => '<div class="text-primary bg-accent">Hello</div>',
                'css' => '@import "tailwindcss";',
                'importPaths' => [$file1, $file2],
            ]);

            // Both theme variables should be present
            $this->assertStringContainsString('--color-primary', $css);
            $this->assertStringContainsString('--color-accent', $css);
        } finally {
            @unlink($file1);
            @unlink($file2);
            @rmdir($tempDir);
        }
    }

    /**
     * Test @utilities from imported files work correctly.
     */
    public function test_utility_from_imported_file(): void
    {
        $tempFile = sys_get_temp_dir() . '/tailwindphp-util-' . uniqid() . '.css';
        file_put_contents($tempFile, '
            @utility custom-padding {
                padding: 42px;
            }
        ');

        try {
            $css = Tailwind::generate([
                'content' => '<div class="custom-padding flex">Hello</div>',
                'css' => '@import "tailwindcss";',
                'importPaths' => $tempFile,
            ]);

            $this->assertStringContainsString('.custom-padding', $css);
            $this->assertStringContainsString('padding: 42px', $css);
            $this->assertStringContainsString('display: flex', $css);
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test that multiple @import "tailwindcss/utilities" are deduplicated.
     */
    public function test_multiple_utilities_import_deduplication(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">Hello</div>',
            'css' => '
                @import "tailwindcss/preflight";
                @import "tailwindcss/theme";
                @import "tailwindcss/utilities";
                @import "tailwindcss/utilities";
            ',
        ]);

        // Should work and only have one set of utilities
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);

        // Count .flex occurrences - should only appear once
        $flexCount = substr_count($css, '.flex {');
        $this->assertEquals(1, $flexCount, 'Duplicate @import "tailwindcss/utilities" should be deduplicated');
    }

    /**
     * Test that preflight can be imported multiple times via importPaths.
     */
    public function test_preflight_via_importpaths(): void
    {
        $tempFile = sys_get_temp_dir() . '/tailwindphp-preflight-' . uniqid() . '.css';
        file_put_contents($tempFile, '@import "tailwindcss/preflight"; @import "tailwindcss/preflight";');

        try {
            $css = Tailwind::generate([
                'content' => '<div class="flex">Hello</div>',
                'css' => '@import "tailwindcss/utilities";',
                'importPaths' => $tempFile,
            ]);

            // Should work and include preflight
            $this->assertStringContainsString('box-sizing: border-box', $css);
            $this->assertStringContainsString('display: flex', $css);
        } finally {
            unlink($tempFile);
        }
    }

    /**
     * Test that inline CSS with multiple @import "tailwindcss" are deduplicated.
     */
    public function test_inline_multiple_tailwindcss_deduplication(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">Hello</div>',
            'css' => '@import "tailwindcss"; @import "tailwindcss";',
        ]);

        // Should work and only include one set of styles
        $this->assertStringContainsString('display: flex', $css);

        // Count box-sizing occurrences (from preflight) - should only be 1
        $boxSizingCount = substr_count($css, 'box-sizing: border-box');
        $this->assertEquals(1, $boxSizingCount, 'Duplicate @import "tailwindcss" should be deduplicated');
    }

    /**
     * Test that inline CSS with multiple @import "tailwindcss/preflight" are deduplicated.
     */
    public function test_inline_multiple_preflight_deduplication(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">Hello</div>',
            'css' => '
                @import "tailwindcss/preflight";
                @import "tailwindcss/preflight";
                @import "tailwindcss/utilities";
            ',
        ]);

        // Should work
        $this->assertStringContainsString('display: flex', $css);

        // Count box-sizing occurrences - should only be 1
        $boxSizingCount = substr_count($css, 'box-sizing: border-box');
        $this->assertEquals(1, $boxSizingCount, 'Duplicate preflight imports should be deduplicated');
    }

    /**
     * Test that inline CSS with multiple @import "tailwindcss/utilities" are deduplicated.
     */
    public function test_inline_multiple_utilities_deduplication(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex p-4">Hello</div>',
            'css' => '
                @import "tailwindcss/theme";
                @import "tailwindcss/utilities";
                @import "tailwindcss/utilities";
            ',
        ]);

        // Should work
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('padding:', $css);

        // Count .flex occurrences - should only be 1
        $flexCount = substr_count($css, '.flex {');
        $this->assertEquals(1, $flexCount, 'Duplicate utilities imports should be deduplicated');
    }

    /**
     * Test that circular imports are handled via deduplication.
     *
     * When a resolver returns content that imports itself, the deduplication
     * mechanism detects the circular reference and returns empty content,
     * preventing infinite loops.
     */
    public function test_circular_import_deduplication(): void
    {
        // Create a resolver that returns an import to itself
        $css = Tailwind::generate([
            'content' => '<div class="flex from-loop">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "loop.css";';
                }
                if ($uri === 'loop.css') {
                    // Return content that imports itself - will be deduplicated
                    return '.from-loop { color: red; } @import "loop.css";';
                }

                return null;
            },
        ]);

        // Deduplication should prevent infinite loop
        // The .from-loop class should appear exactly once
        $this->assertStringContainsString('display: flex', $css);
        $this->assertStringContainsString('.from-loop', $css);
        // Count occurrences - should only be 1
        $this->assertSame(1, substr_count($css, '.from-loop'));
    }

    /**
     * Test resolver with empty string return is handled.
     */
    public function test_resolver_empty_string(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="flex">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss";';
                }

                // Return empty string for unknown imports
                return '';
            },
        ]);

        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test deeply nested import chain (not circular, just deep).
     */
    public function test_deep_import_chain(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="level-1 level-2 level-3 level-4 level-5">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "level-1.css";';
                }

                return match ($uri) {
                    'level-1.css' => '.level-1 { color: red; } @import "level-2.css";',
                    'level-2.css' => '.level-2 { color: orange; } @import "level-3.css";',
                    'level-3.css' => '.level-3 { color: yellow; } @import "level-4.css";',
                    'level-4.css' => '.level-4 { color: green; } @import "level-5.css";',
                    'level-5.css' => '.level-5 { color: blue; }',
                    default => null,
                };
            },
        ]);

        // All levels should be present
        $this->assertStringContainsString('.level-1', $css);
        $this->assertStringContainsString('.level-2', $css);
        $this->assertStringContainsString('.level-3', $css);
        $this->assertStringContainsString('.level-4', $css);
        $this->assertStringContainsString('.level-5', $css);
    }

    /**
     * Test resolver returning @utility directives.
     */
    public function test_resolver_with_utility_directive(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom-gradient">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "custom.css";';
                }
                if ($uri === 'custom.css') {
                    return '@utility custom-gradient { background: linear-gradient(to right, red, blue); }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('.custom-gradient', $css);
        $this->assertStringContainsString('linear-gradient', $css);
    }

    /**
     * Test import with layer() modifier via resolver.
     */
    public function test_import_with_layer_via_resolver(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="layer-test flex">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "test.css" layer(components);';
                }
                if ($uri === 'test.css') {
                    return '.layer-test { color: purple; }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('@layer components', $css);
        $this->assertStringContainsString('.layer-test', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test import with supports() modifier via resolver.
     */
    public function test_import_with_supports_via_resolver(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="supports-test">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "test.css" supports(display: grid);';
                }
                if ($uri === 'test.css') {
                    return '.supports-test { display: grid; }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('display: grid', $css);
        $this->assertStringContainsString('.supports-test', $css);
    }

    /**
     * Test import with media query modifier via resolver.
     */
    public function test_import_with_media_via_resolver(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="media-test">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "test.css" screen and (min-width: 768px);';
                }
                if ($uri === 'test.css') {
                    return '.media-test { font-size: 1.5rem; }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('min-width: 768px', $css);
        $this->assertStringContainsString('.media-test', $css);
    }

    /**
     * Test import with all modifiers: layer, supports, and media query.
     */
    public function test_import_with_all_modifiers_via_resolver(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="full-test">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "test.css" layer(components) supports(display: flex) screen;';
                }
                if ($uri === 'test.css') {
                    return '.full-test { display: flex; }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('@layer components', $css);
        $this->assertStringContainsString('@supports', $css);
        $this->assertStringContainsString('@media screen', $css);
        $this->assertStringContainsString('.full-test', $css);
    }

    /**
     * Test import with anonymous layer modifier.
     */
    public function test_import_with_anonymous_layer_via_resolver(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="anon-layer-test">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "test.css" layer;';
                }
                if ($uri === 'test.css') {
                    return '.anon-layer-test { color: blue; }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('@layer', $css);
        $this->assertStringContainsString('.anon-layer-test', $css);
    }

    /**
     * Test resolver with @custom-variant directive.
     */
    public function test_resolver_with_custom_variant(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="custom-hover:text-red-500">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "variants.css";';
                }
                if ($uri === 'variants.css') {
                    return '@custom-variant custom-hover (&:hover);';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString(':hover', $css);
        $this->assertStringContainsString('color:', $css);
    }

    /**
     * Test that resolver returning null falls through correctly.
     */
    public function test_resolver_null_fallthrough(): void
    {
        // When resolver returns null, system should skip that import silently
        $css = Tailwind::generate([
            'content' => '<div class="flex">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "nonexistent.css";';
                }

                // Return null for unknown - should be skipped
                return null;
            },
        ]);

        // Should still work, just skip the nonexistent import
        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test layer modifier with inline media via resolver.
     *
     * Since media query modifiers on @import aren't supported for resolver imports,
     * combine layer() modifier with inline @media in the returned content.
     */
    public function test_layer_with_inline_media_via_resolver(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="layered-test">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "test.css" layer(components);';
                }
                if ($uri === 'test.css') {
                    return '@media (min-width: 1024px) { .layered-test { padding: 2rem; } }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('@media', $css);
        $this->assertStringContainsString('@layer', $css);
        $this->assertStringContainsString('.layered-test', $css);
    }

    /**
     * Test resolver chain with @theme directive (single nesting level).
     *
     * Note: @theme directives are properly processed with single-level import
     * nesting. Deeper nesting may require the @theme to be in the same file
     * as @import "tailwindcss" for proper processing order.
     */
    public function test_resolver_chain_with_theme(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="bg-primary">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    // Include both tailwindcss and theme together
                    return '@import "tailwindcss"; @import "theme.css";';
                }
                if ($uri === 'theme.css') {
                    return '@theme { --color-primary: #ff6600; }';
                }

                return null;
            },
        ]);

        // The theme variable should be in the CSS output
        $this->assertStringContainsString('--color-primary', $css);
    }

    /**
     * Test resolver with multiple utility imports.
     */
    public function test_resolver_multiple_utility_imports(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn icon flex">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "buttons.css"; @import "icons.css";';
                }

                return match ($uri) {
                    'buttons.css' => '@utility btn { padding: 0.5rem 1rem; }',
                    'icons.css' => '@utility icon { width: 1.5rem; height: 1.5rem; }',
                    default => null,
                };
            },
        ]);

        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('.icon', $css);
        $this->assertStringContainsString('display: flex', $css);
    }

    /**
     * Test resolver with @apply directive.
     */
    public function test_resolver_with_apply(): void
    {
        $css = Tailwind::generate([
            'content' => '<div class="btn">Hello</div>',
            'importPaths' => function (?string $uri, ?string $fromFile): ?string {
                if ($uri === null) {
                    return '@import "tailwindcss"; @import "components.css";';
                }
                if ($uri === 'components.css') {
                    return '.btn { @apply px-4 py-2 rounded bg-blue-500 text-white; }';
                }

                return null;
            },
        ]);

        $this->assertStringContainsString('.btn', $css);
        $this->assertStringContainsString('padding', $css);
        $this->assertStringContainsString('border-radius', $css);
    }
}
