#!/usr/bin/env php
<?php

/**
 * Plugin Reference Update Script
 *
 * Updates @tailwindcss/typography and/or @tailwindcss/forms references to latest versions,
 * re-extracts tests, and runs the test suite.
 *
 * Usage:
 *   php scripts/update-plugins.php              # Update all plugins to latest
 *   php scripts/update-plugins.php typography   # Update only typography
 *   php scripts/update-plugins.php forms        # Update only forms
 *   php scripts/update-plugins.php --check      # Show current versions only
 */

$rootDir = dirname(__DIR__);

// Plugin configurations
$plugins = [
    'typography' => [
        'dir' => $rootDir . '/reference/tailwindcss-typography',
        'tagPattern' => 'v*',
        'extractScript' => $rootDir . '/test-coverage/plugins/typography/extract-tests.php',
        'package' => '@tailwindcss/typography',
    ],
    'forms' => [
        'dir' => $rootDir . '/reference/tailwindcss-forms',
        'tagPattern' => 'v*',
        'extractScript' => $rootDir . '/test-coverage/plugins/forms/extract-tests.php',
        'package' => '@tailwindcss/forms',
    ],
];

// Colors for terminal output
function color(string $text, string $color): string
{
    $colors = [
        'green' => "\033[32m",
        'red' => "\033[31m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'cyan' => "\033[36m",
        'reset' => "\033[0m",
        'bold' => "\033[1m",
    ];

    return ($colors[$color] ?? '') . $text . $colors['reset'];
}

function run(string $cmd, ?string $cwd = null): array
{
    $descriptors = [
        0 => ['pipe', 'r'],
        1 => ['pipe', 'w'],
        2 => ['pipe', 'w'],
    ];

    $process = proc_open($cmd, $descriptors, $pipes, $cwd);

    if (!is_resource($process)) {
        return ['output' => '', 'error' => 'Failed to run command', 'code' => 1];
    }

    fclose($pipes[0]);
    $output = stream_get_contents($pipes[1]);
    $error = stream_get_contents($pipes[2]);
    fclose($pipes[1]);
    fclose($pipes[2]);

    $code = proc_close($process);

    return ['output' => trim($output), 'error' => trim($error), 'code' => $code];
}

function getCurrentVersion(string $dir): array
{
    $commit = run('git rev-parse HEAD', $dir)['output'];
    $tag = run('git describe --tags --exact-match 2>/dev/null || git describe --tags --abbrev=0 2>/dev/null', $dir)['output'];

    return [
        'commit' => $commit,
        'tag' => $tag ?: 'unknown',
        'short' => substr($commit, 0, 7),
    ];
}

function getLatestTag(string $dir, string $pattern): string
{
    $result = run("git fetch --tags && git tag -l \"{$pattern}\" | sort -V | tail -1", $dir);

    return $result['output'] ?: '';
}

// Parse arguments
$arg = $argv[1] ?? null;

// Check mode
if ($arg === '--check' || $arg === '-c') {
    echo color("Plugin Versions\n", 'bold');
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

    foreach ($plugins as $name => $config) {
        if (!is_dir($config['dir'])) {
            echo color($config['package'], 'yellow') . ': ' . color('not installed', 'red') . "\n";
            continue;
        }
        $version = getCurrentVersion($config['dir']);
        echo color($config['package'], 'cyan') . "\n";
        echo '  Tag:    ' . color($version['tag'], 'green') . "\n";
        echo '  Commit: ' . color($version['short'], 'blue') . "\n\n";
    }
    exit(0);
}

// Determine which plugins to update
$pluginsToUpdate = [];
if ($arg && isset($plugins[$arg])) {
    $pluginsToUpdate[$arg] = $plugins[$arg];
} elseif ($arg && !isset($plugins[$arg])) {
    echo color("Error: Unknown plugin '{$arg}'\n", 'red');
    echo 'Available: ' . implode(', ', array_keys($plugins)) . "\n";
    exit(1);
} else {
    $pluginsToUpdate = $plugins;
}

echo color("Plugin Reference Updater\n", 'bold');
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

// Show current versions
echo color("Current versions:\n", 'yellow');
foreach ($pluginsToUpdate as $name => $config) {
    if (!is_dir($config['dir'])) {
        echo "  {$config['package']}: " . color('not installed', 'red') . "\n";
        continue;
    }
    $version = getCurrentVersion($config['dir']);
    echo "  {$config['package']}: {$version['tag']} ({$version['short']})\n";
}
echo "\n";

// Fetch latest versions
echo "Fetching latest versions...\n";
$updates = [];
foreach ($pluginsToUpdate as $name => $config) {
    if (!is_dir($config['dir'])) {
        echo color("  Skipping {$config['package']} (not installed)\n", 'yellow');
        continue;
    }
    $latest = getLatestTag($config['dir'], $config['tagPattern']);
    if (!$latest) {
        echo color("  Could not determine latest tag for {$config['package']}\n", 'red');
        continue;
    }
    $current = getCurrentVersion($config['dir']);
    if ($current['tag'] === $latest) {
        echo "  {$config['package']}: " . color("already at {$latest}", 'green') . "\n";
    } else {
        echo "  {$config['package']}: {$current['tag']} → " . color($latest, 'green') . "\n";
        $updates[$name] = ['config' => $config, 'from' => $current['tag'], 'to' => $latest];
    }
}
echo "\n";

if (empty($updates)) {
    echo color("All plugins are up to date!\n", 'green');
    exit(0);
}

// Confirm
echo "This will:\n";
foreach ($updates as $name => $info) {
    echo "  - Update {$info['config']['package']} from {$info['from']} to {$info['to']}\n";
}
echo "  - Re-extract tests from references\n";
echo "  - Run the test suite\n\n";

echo 'Continue? [y/N] ';
$handle = fopen('php://stdin', 'r');
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'y') {
    echo "Aborted.\n";
    exit(0);
}

echo "\n";

// Update each plugin
foreach ($updates as $name => $info) {
    echo color("Updating {$info['config']['package']}...\n", 'bold');

    $result = run('git fetch --all --tags', $info['config']['dir']);
    if ($result['code'] !== 0) {
        echo color("  Error fetching: {$result['error']}\n", 'red');
        exit(1);
    }

    $result = run("git checkout {$info['to']}", $info['config']['dir']);
    if ($result['code'] !== 0) {
        echo color("  Error checking out {$info['to']}: {$result['error']}\n", 'red');
        exit(1);
    }

    echo color('  ✓ ', 'green') . "Checked out {$info['to']}\n";

    // Run extraction script
    if (file_exists($info['config']['extractScript'])) {
        echo "  Extracting tests...\n";
        $result = run('php ' . escapeshellarg($info['config']['extractScript']), $rootDir);
        if ($result['code'] !== 0) {
            echo color("  Error extracting: {$result['error']}\n", 'red');
        } else {
            echo color('  ✓ ', 'green') . "Tests extracted\n";
            if ($result['output']) {
                echo "  {$result['output']}\n";
            }
        }
    } else {
        echo color("  No extraction script found\n", 'yellow');
    }
    echo "\n";
}

// Run tests
echo color("Running plugin tests...\n", 'bold');
passthru('cd ' . escapeshellarg($rootDir) . ' && ./vendor/bin/phpunit --filter="Plugin"', $testResult);

echo "\n";

if ($testResult === 0) {
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'green');
    echo color("✓ Update successful!\n", 'green');
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'green');
    echo "\nUpdated:\n";
    foreach ($updates as $name => $info) {
        echo "  {$info['config']['package']}: {$info['from']} → {$info['to']}\n";
    }
    echo "\nNext steps:\n";
    echo "  1. Review changes: git diff\n";
    echo "  2. Run full test suite: composer test\n";
    echo '  3. Commit: git add -A && git commit -m "Update plugins: ' . implode(', ', array_map(fn ($n, $i) => "{$i['config']['package']} {$i['to']}", array_keys($updates), $updates)) . "\"\n";
} else {
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'red');
    echo color("✗ Tests failed!\n", 'red');
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'red');
    echo "\nThe plugin references were updated but tests are failing.\n";
    echo "This likely means the plugin made breaking changes.\n";
    echo "\nNext steps:\n";
    echo "  1. Review failing tests\n";
    echo "  2. Update PHP implementation to match\n";
    echo "  3. Re-run: composer test\n";
    exit(1);
}
