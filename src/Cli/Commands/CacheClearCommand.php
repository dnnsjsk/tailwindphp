<?php

declare(strict_types=1);

namespace TailwindPHP\Cli\Commands;

use TailwindPHP\Cli\Command;
use TailwindPHP\Tailwind;

/**
 * Clear the TailwindPHP cache.
 */
class CacheClearCommand extends Command
{
    public function getName(): string
    {
        return 'cache:clear';
    }

    public function getDescription(): string
    {
        return 'Clear the CSS cache';
    }

    public function getHelp(): string
    {
        return <<<'HELP'
<yellow>USAGE:</yellow>
  tailwindphp cache:clear [options]

<yellow>OPTIONS:</yellow>
  <green>--path</green>=DIR        Custom cache directory (default: system temp)

<yellow>EXAMPLES:</yellow>
  <gray># Clear default cache</gray>
  tailwindphp cache:clear

  <gray># Clear custom cache directory</gray>
  tailwindphp cache:clear --path=/path/to/cache

HELP;
    }

    public function execute(): int
    {
        $cachePath = $this->getStringOpt('path', '', '');

        // Also check config file for cache path
        if ($cachePath === '') {
            $config = $this->loadConfig();
            if (isset($config['cache']) && is_string($config['cache'])) {
                $cachePath = $config['cache'];
            }
        }

        $this->output->info('Clearing cache...');

        // Clear cache
        if ($cachePath !== '') {
            $deleted = Tailwind::clearCache($cachePath);
        } else {
            $deleted = Tailwind::clearCache();
        }

        if ($deleted > 0) {
            $this->output->success("Cleared {$deleted} cached file(s).");
        } else {
            $this->output->info('Cache is empty.');
        }

        return 0;
    }

    /**
     * Load config file.
     *
     * @return array<string, mixed>
     */
    private function loadConfig(): array
    {
        $configPath = getcwd() . '/tailwind.config.php';

        if (!file_exists($configPath)) {
            return [];
        }

        $config = require $configPath;

        return is_array($config) ? $config : [];
    }
}
