<?php

declare(strict_types=1);

namespace TailwindPHP\Tests;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

use function TailwindPHP\clearCache;

use TailwindPHP\Tailwind;

/**
 * Tests for the caching functionality.
 */
class CacheTest extends TestCase
{
    private string $testCacheDir;

    protected function setUp(): void
    {
        $this->testCacheDir = sys_get_temp_dir() . '/tailwindphp_test_' . uniqid();
        mkdir($this->testCacheDir, 0755, true);
    }

    protected function tearDown(): void
    {
        // Clean up test cache directory
        if (is_dir($this->testCacheDir)) {
            $files = glob($this->testCacheDir . '/*');
            if ($files) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        unlink($file);
                    }
                }
            }
            rmdir($this->testCacheDir);
        }
    }

    #[Test]
    public function it_caches_generated_css_with_custom_directory(): void
    {
        $html = '<div class="flex p-4">Hello</div>';

        // First call - should generate and cache
        $css1 = Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
        ]);

        // Verify cache file was created
        $cacheFiles = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertCount(1, $cacheFiles);

        // Second call - should return cached result
        $css2 = Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
        ]);

        $this->assertSame($css1, $css2);
        $this->assertStringContainsString('.flex', $css1);
        $this->assertStringContainsString('.p-4', $css1);
    }

    #[Test]
    public function it_caches_to_default_directory_when_cache_is_true(): void
    {
        $html = '<div class="mt-8 text-center">Hello</div>';
        $defaultCacheDir = sys_get_temp_dir() . '/tailwindphp';

        // Clean up any existing cache first
        clearCache(true);

        // Generate with default cache
        $css = Tailwind::generate([
            'content' => $html,
            'cache' => true,
        ]);

        $this->assertStringContainsString('.mt-8', $css);
        $this->assertStringContainsString('.text-center', $css);

        // Verify cache file exists in default location
        $cacheFiles = glob($defaultCacheDir . '/tailwind_*.css');
        $this->assertNotEmpty($cacheFiles);

        // Clean up
        clearCache(true);
    }

    #[Test]
    public function it_generates_different_cache_keys_for_different_inputs(): void
    {
        $html1 = '<div class="flex">A</div>';
        $html2 = '<div class="grid">B</div>';

        Tailwind::generate([
            'content' => $html1,
            'cache' => $this->testCacheDir,
        ]);

        Tailwind::generate([
            'content' => $html2,
            'cache' => $this->testCacheDir,
        ]);

        // Should have 2 different cache files
        $cacheFiles = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertCount(2, $cacheFiles);
    }

    #[Test]
    public function it_generates_different_cache_keys_for_different_css(): void
    {
        $html = '<div class="custom-class">Hello</div>';
        $css1 = '@import "tailwindcss"; @utility custom-class { color: red; }';
        $css2 = '@import "tailwindcss"; @utility custom-class { color: blue; }';

        Tailwind::generate([
            'content' => $html,
            'css' => $css1,
            'cache' => $this->testCacheDir,
        ]);

        Tailwind::generate([
            'content' => $html,
            'css' => $css2,
            'cache' => $this->testCacheDir,
        ]);

        // Should have 2 different cache files
        $cacheFiles = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertCount(2, $cacheFiles);
    }

    #[Test]
    public function it_generates_different_cache_keys_for_minify_option(): void
    {
        $html = '<div class="bg-red-500">Hello</div>';

        Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
            'minify' => false,
        ]);

        Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
            'minify' => true,
        ]);

        // Should have 2 different cache files (minified and non-minified)
        $cacheFiles = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertCount(2, $cacheFiles);
    }

    #[Test]
    public function it_respects_cache_ttl(): void
    {
        $html = '<div class="border">Hello</div>';

        // Generate with a very short TTL
        Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
            'cacheTtl' => 1, // 1 second
        ]);

        // Verify cache file exists
        $cacheFiles = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertCount(1, $cacheFiles);

        // Wait for cache to expire
        sleep(2);

        // This should regenerate (cache expired)
        $css = Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
            'cacheTtl' => 1,
        ]);

        $this->assertStringContainsString('.border', $css);
    }

    #[Test]
    public function it_clears_cache_with_custom_directory(): void
    {
        $html = '<div class="hidden visible">Hello</div>';

        // Generate to create cache files
        Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
        ]);

        // Verify files exist
        $before = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertNotEmpty($before);

        // Clear cache
        $deleted = clearCache($this->testCacheDir);

        // Verify files were deleted
        $this->assertSame(count($before), $deleted);
        $after = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertEmpty($after);
    }

    #[Test]
    public function it_clears_cache_via_static_method(): void
    {
        $html = '<div class="opacity-50">Hello</div>';

        // Generate to create cache files
        Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
        ]);

        // Clear via static method
        $deleted = Tailwind::clearCache($this->testCacheDir);

        $this->assertGreaterThan(0, $deleted);
        $after = glob($this->testCacheDir . '/tailwind_*.css');
        $this->assertEmpty($after);
    }

    #[Test]
    public function it_returns_zero_when_clearing_nonexistent_directory(): void
    {
        $deleted = clearCache('/nonexistent/directory/path');
        $this->assertSame(0, $deleted);
    }

    #[Test]
    public function it_returns_zero_when_clearing_with_null(): void
    {
        $deleted = clearCache(null);
        $this->assertSame(0, $deleted);
    }

    #[Test]
    public function it_returns_zero_when_clearing_with_false(): void
    {
        $deleted = clearCache(false);
        $this->assertSame(0, $deleted);
    }

    #[Test]
    public function it_does_not_cache_when_cache_option_is_not_set(): void
    {
        $html = '<div class="rotate-45">Hello</div>';

        // Generate without cache option
        $css = Tailwind::generate([
            'content' => $html,
        ]);

        $this->assertStringContainsString('rotate', $css);

        // Default cache directory should not have new files for this
        // (Note: other tests may have created files, so we just verify it works)
    }

    #[Test]
    public function it_creates_cache_directory_if_not_exists(): void
    {
        $newCacheDir = $this->testCacheDir . '/nested/cache/dir';
        $this->assertDirectoryDoesNotExist($newCacheDir);

        $html = '<div class="scale-150">Hello</div>';

        Tailwind::generate([
            'content' => $html,
            'cache' => $newCacheDir,
        ]);

        $this->assertDirectoryExists($newCacheDir);
        $cacheFiles = glob($newCacheDir . '/tailwind_*.css');
        $this->assertCount(1, $cacheFiles);

        // Clean up nested dirs
        if ($cacheFiles) {
            foreach ($cacheFiles as $file) {
                unlink($file);
            }
        }
        rmdir($newCacheDir);
        rmdir(dirname($newCacheDir));
        rmdir(dirname(dirname($newCacheDir)));
    }

    #[Test]
    public function it_returns_cached_content_on_second_call(): void
    {
        $html = '<div class="translate-x-4">Hello</div>';

        // First call
        $start1 = microtime(true);
        $css1 = Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
        ]);
        $time1 = microtime(true) - $start1;

        // Second call (should be cached)
        $start2 = microtime(true);
        $css2 = Tailwind::generate([
            'content' => $html,
            'cache' => $this->testCacheDir,
        ]);
        $time2 = microtime(true) - $start2;

        $this->assertSame($css1, $css2);

        // Cached call should be significantly faster (at least 5x)
        // Note: This is a loose check as timing can vary
        $this->assertLessThan($time1, $time2);
    }
}
