<?php

declare(strict_types=1);

namespace TailwindPHP\Plugin;

/**
 * Plugin Interface - Contract for TailwindPHP plugins.
 *
 * Plugins implement this interface to register utilities, variants,
 * and components with TailwindPHP.
 */
interface PluginInterface
{
    /**
     * Get the plugin name/identifier.
     *
     * This is used to reference the plugin in @plugin directives.
     * e.g., 'typography' for @plugin "typography"
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Execute the plugin, registering utilities/variants/components.
     *
     * @param PluginAPI $api The plugin API instance
     * @param array $options Options passed from @plugin directive
     */
    public function __invoke(PluginAPI $api, array $options = []): void;

    /**
     * Get the plugin's theme extensions.
     *
     * Returns an array that will be merged into the theme config.
     * This is equivalent to the second argument of plugin.withOptions().
     *
     * @param array $options Options passed from @plugin directive
     * @return array Theme configuration to merge
     */
    public function getThemeExtensions(array $options = []): array;
}
