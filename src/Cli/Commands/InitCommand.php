<?php

declare(strict_types=1);

namespace TailwindPHP\Cli\Commands;

use TailwindPHP\Cli\Command;

/**
 * Initialize a new TailwindPHP configuration file.
 */
class InitCommand extends Command
{
    public function getName(): string
    {
        return 'init';
    }

    public function getDescription(): string
    {
        return 'Create a tailwind.config.php file';
    }

    public function getHelp(): string
    {
        return <<<'HELP'
<yellow>USAGE:</yellow>
  tailwindphp init [options]

<yellow>OPTIONS:</yellow>
  <green>--full</green>               Include all configuration options with comments
  <green>--css</green>                Also create a starter CSS file (app.css)
  <green>-f, --force</green>          Overwrite existing files

<yellow>EXAMPLES:</yellow>
  <gray># Create minimal config</gray>
  tailwindphp init

  <gray># Create full config with all options</gray>
  tailwindphp init --full

  <gray># Create config and starter CSS</gray>
  tailwindphp init --css

HELP;
    }

    public function execute(): int
    {
        $force = $this->getBoolOpt('force', 'f', false);
        $full = $this->getBoolOpt('full', '', false);
        $createCss = $this->getBoolOpt('css', '', false);

        $configFile = getcwd() . '/tailwind.config.php';
        $cssFile = getcwd() . '/app.css';

        // Check if files exist
        if (file_exists($configFile) && !$force) {
            $this->output->error('tailwind.config.php already exists. Use --force to overwrite.');

            return 1;
        }

        if ($createCss && file_exists($cssFile) && !$force) {
            $this->output->error('app.css already exists. Use --force to overwrite.');

            return 1;
        }

        // Generate config content
        $configContent = $full ? $this->getFullConfig() : $this->getMinimalConfig();

        // Write config file
        file_put_contents($configFile, $configContent);
        $this->output->success('Created tailwind.config.php');

        // Create CSS file if requested
        if ($createCss) {
            file_put_contents($cssFile, $this->getStarterCss());
            $this->output->success('Created app.css');
        }

        $this->output->writeln();
        $this->output->info('Next steps:');
        $this->output->writeln('  1. Edit tailwind.config.php to configure your content paths');
        if ($createCss) {
            $this->output->writeln('  2. Edit app.css to customize your theme');
            $this->output->writeln('  3. Run: tailwindphp build');
        } else {
            $this->output->writeln('  2. Run: tailwindphp build');
        }
        $this->output->writeln();

        return 0;
    }

    /**
     * Get minimal config content.
     */
    private function getMinimalConfig(): string
    {
        return <<<'PHP'
<?php

return [
    // Content paths - files to scan for Tailwind classes
    // Supports: glob patterns, directories, or arrays
    'content' => [
        './templates/**/*.php',
        './views/**/*.blade.php',
    ],

    // Input CSS file (optional) - contains @import and @theme directives
    // 'css' => './src/app.css',

    // Output CSS file
    'output' => './dist/styles.css',

    // Minify output CSS (recommended for production)
    'minify' => false,

    // Enable caching (true = default location, or specify path)
    'cache' => true,
];

PHP;
    }

    /**
     * Get full config content with all options.
     */
    private function getFullConfig(): string
    {
        return <<<'PHP'
<?php

/**
 * TailwindPHP Configuration
 *
 * Documentation: https://github.com/dnnsjsk/tailwindphp
 */

return [
    // -------------------------------------------------------------------------
    // Content Paths
    // -------------------------------------------------------------------------
    // Configure paths to all your template files. TailwindPHP will scan these
    // files for class names and generate only the CSS you actually use.
    //
    // Supported formats:
    // - Glob patterns: "./templates/**/*.php"
    // - Directories: "./templates" (scans recursively)
    // - Individual files: "./templates/header.php"
    // - Arrays of the above
    //
    // Supported extensions: .php, .html, .htm, .twig, .blade.php, .vue, .jsx, .tsx, .svelte

    'content' => [
        './templates/**/*.php',
        './views/**/*.blade.php',
        './components/**/*.php',
    ],

    // -------------------------------------------------------------------------
    // Input CSS File
    // -------------------------------------------------------------------------
    // Path to your input CSS file containing @import and @theme directives.
    // This is where you customize Tailwind with your own theme values.
    // If not specified, defaults to: @import "tailwindcss";

    'css' => './src/app.css',

    // -------------------------------------------------------------------------
    // Output CSS File
    // -------------------------------------------------------------------------
    // Where to write the generated CSS. The directory will be created if needed.

    'output' => './dist/styles.css',

    // -------------------------------------------------------------------------
    // Minification
    // -------------------------------------------------------------------------
    // Whether to minify the output CSS. Removes comments, collapses whitespace,
    // shortens colors, etc. Use --minify flag to override.

    'minify' => false,

    // -------------------------------------------------------------------------
    // Caching
    // -------------------------------------------------------------------------
    // Enable caching to speed up repeated builds.
    // - true: Cache in system temp directory
    // - false: Disable caching
    // - '/path/to/cache': Custom cache directory
    //
    // Use --no-cache flag to disable for a single build.
    // Use `tailwindphp cache:clear` to clear the cache.

    'cache' => true,

    // -------------------------------------------------------------------------
    // Cache TTL (Time-to-Live)
    // -------------------------------------------------------------------------
    // How long cached CSS is valid, in seconds. Set to null for no expiration.

    'cacheTtl' => null,
];

PHP;
    }

    /**
     * Get starter CSS content.
     */
    private function getStarterCss(): string
    {
        return <<<'CSS'
/*
|--------------------------------------------------------------------------
| TailwindPHP Starter CSS
|--------------------------------------------------------------------------
|
| This file imports Tailwind CSS and provides a place to customize your
| theme and add custom utilities.
|
*/

/* Import Tailwind CSS (includes theme, preflight, and utilities) */
@import "tailwindcss";

/*
|--------------------------------------------------------------------------
| Theme Customization
|--------------------------------------------------------------------------
|
| Customize Tailwind's default theme by defining CSS custom properties.
| These override or extend the default values.
|
| See: https://tailwindcss.com/docs/theme
|
*/

@theme {
    /* Colors */
    --color-brand: #3b82f6;
    --color-brand-dark: #2563eb;

    /* Typography */
    /* --font-sans: "Inter", sans-serif; */
    /* --font-mono: "Fira Code", monospace; */

    /* Spacing */
    /* --spacing-128: 32rem; */

    /* Border Radius */
    /* --radius-xl: 1rem; */
}

/*
|--------------------------------------------------------------------------
| Base Styles
|--------------------------------------------------------------------------
|
| Add custom base styles on top of Preflight.
|
*/

@layer base {
    /* Example: Style headings */
    /* h1 { font-size: var(--text-4xl); font-weight: bold; } */
    /* h2 { font-size: var(--text-3xl); font-weight: semibold; } */
}

/*
|--------------------------------------------------------------------------
| Component Classes
|--------------------------------------------------------------------------
|
| Add reusable component classes.
|
*/

@layer components {
    /* Example: Button component */
    /* .btn {
        @apply px-4 py-2 rounded-lg font-medium transition-colors;
    }
    .btn-primary {
        @apply bg-brand text-white hover:bg-brand-dark;
    } */
}

/*
|--------------------------------------------------------------------------
| Custom Utilities
|--------------------------------------------------------------------------
|
| Add custom utility classes.
|
*/

@layer utilities {
    /* Example: Text balance utility */
    /* .text-balance {
        text-wrap: balance;
    } */
}

CSS;
    }
}
