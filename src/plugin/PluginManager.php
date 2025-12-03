<?php

declare(strict_types=1);

namespace TailwindPHP\Plugin;

use TailwindPHP\Theme;
use TailwindPHP\Utilities\Utilities;
use TailwindPHP\Variants\Variants;

/**
 * Plugin Manager - Handles plugin registration and execution.
 *
 * This class manages the lifecycle of plugins:
 * 1. Register built-in and custom plugins
 * 2. Look up plugins by name (for @plugin directive)
 * 3. Execute plugins with the PluginAPI
 */
class PluginManager
{
    /**
     * @var array<string, PluginInterface>
     */
    private array $plugins = [];

    /**
     * @var array<string, class-string<PluginInterface>>
     */
    private static array $builtInPlugins = [
        '@tailwindcss/typography' => \TailwindPHP\Plugin\Plugins\TypographyPlugin::class,
        '@tailwindcss/forms' => \TailwindPHP\Plugin\Plugins\FormsPlugin::class,
    ];

    /**
     * Register a plugin instance.
     *
     * @param PluginInterface $plugin
     */
    public function register(PluginInterface $plugin): void
    {
        $this->plugins[$plugin->getName()] = $plugin;
    }

    /**
     * Register a built-in plugin class.
     *
     * @param string $name Plugin name
     * @param class-string<PluginInterface> $class Plugin class name
     */
    public static function registerBuiltIn(string $name, string $class): void
    {
        self::$builtInPlugins[$name] = $class;
    }

    /**
     * Check if a plugin is registered.
     *
     * @param string $name Plugin name
     * @return bool
     */
    public function has(string $name): bool
    {
        return isset($this->plugins[$name]) || isset(self::$builtInPlugins[$name]);
    }

    /**
     * Get a plugin by name.
     *
     * @param string $name Plugin name
     * @return PluginInterface|null
     */
    public function get(string $name): ?PluginInterface
    {
        // Check registered instances first
        if (isset($this->plugins[$name])) {
            return $this->plugins[$name];
        }

        // Check built-in plugins
        if (isset(self::$builtInPlugins[$name])) {
            $class = self::$builtInPlugins[$name];
            $this->plugins[$name] = new $class();

            return $this->plugins[$name];
        }

        return null;
    }

    /**
     * Execute a plugin by name.
     *
     * @param string $name Plugin name
     * @param PluginAPI $api Plugin API instance
     * @param array $options Options from @plugin directive
     * @return bool True if plugin was found and executed
     */
    public function execute(string $name, PluginAPI $api, array $options = []): bool
    {
        $plugin = $this->get($name);

        if ($plugin === null) {
            return false;
        }

        // Execute the plugin
        $plugin($api, $options);

        return true;
    }

    /**
     * Get theme extensions for a plugin.
     *
     * @param string $name Plugin name
     * @param array $options Options from @plugin directive
     * @return array Theme extensions
     */
    public function getThemeExtensions(string $name, array $options = []): array
    {
        $plugin = $this->get($name);

        if ($plugin === null) {
            return [];
        }

        return $plugin->getThemeExtensions($options);
    }

    /**
     * Get all registered plugin names.
     *
     * @return array<string>
     */
    public function getRegisteredPlugins(): array
    {
        return array_unique(array_merge(
            array_keys($this->plugins),
            array_keys(self::$builtInPlugins),
        ));
    }

    /**
     * Create a PluginAPI instance for plugin execution.
     *
     * @param Theme $theme
     * @param Utilities $utilities
     * @param Variants $variants
     * @param array $config
     * @return PluginAPI
     */
    public function createAPI(
        Theme $theme,
        Utilities $utilities,
        Variants $variants,
        array $config = [],
    ): PluginAPI {
        return new PluginAPI($theme, $utilities, $variants, $config);
    }

    /**
     * Apply all plugins from a list of names.
     *
     * @param array<string|array{name: string, options: array}> $pluginRefs Plugin references
     * @param Theme $theme
     * @param Utilities $utilities
     * @param Variants $variants
     * @param array $config
     * @return PluginAPI The API instance with all plugins applied
     */
    public function applyPlugins(
        array $pluginRefs,
        Theme $theme,
        Utilities $utilities,
        Variants $variants,
        array $config = [],
    ): PluginAPI {
        $api = $this->createAPI($theme, $utilities, $variants, $config);

        foreach ($pluginRefs as $ref) {
            if (is_string($ref)) {
                $name = $ref;
                $options = [];
            } else {
                $name = $ref['name'];
                $options = $ref['options'] ?? [];
            }

            // First apply theme extensions
            $themeExtensions = $this->getThemeExtensions($name, $options);
            $this->applyThemeExtensions($theme, $themeExtensions);

            // Then execute the plugin
            $this->execute($name, $api, $options);
        }

        return $api;
    }

    /**
     * Apply theme extensions from a plugin.
     *
     * @param Theme $theme
     * @param array $extensions
     */
    private function applyThemeExtensions(Theme $theme, array $extensions): void
    {
        if (empty($extensions)) {
            return;
        }

        // Process theme extensions
        foreach ($extensions as $namespace => $values) {
            if (!is_array($values)) {
                continue;
            }

            $themeNamespace = $this->toThemeNamespace($namespace);

            foreach ($values as $key => $value) {
                if ($key === 'DEFAULT') {
                    $theme->add($themeNamespace, $value);
                } else {
                    $theme->add("{$themeNamespace}-{$key}", $value);
                }
            }
        }
    }

    /**
     * Convert a config namespace to theme namespace.
     */
    private function toThemeNamespace(string $namespace): string
    {
        // Convert camelCase to kebab-case and add -- prefix
        $kebab = strtolower(preg_replace('/([A-Z])/', '-$1', $namespace));

        return "--{$kebab}";
    }
}
