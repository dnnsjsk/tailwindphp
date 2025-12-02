#!/usr/bin/env php
<?php
/**
 * TailwindCSS Reference Update Script
 *
 * Updates the TailwindCSS reference to a specific version or latest,
 * re-extracts tests, and runs the test suite.
 *
 * Usage:
 *   php scripts/update-tailwind.php              # Update to latest
 *   php scripts/update-tailwind.php v4.1.18      # Update to specific tag
 *   php scripts/update-tailwind.php abc123       # Update to specific commit
 *   php scripts/update-tailwind.php --check      # Show current version only
 */

$rootDir = dirname(__DIR__);
$referenceDir = $rootDir . '/reference/tailwindcss';

// Colors for terminal output
function color(string $text, string $color): string {
    $colors = [
        'green' => "\033[32m",
        'red' => "\033[31m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'reset' => "\033[0m",
        'bold' => "\033[1m",
    ];
    return ($colors[$color] ?? '') . $text . $colors['reset'];
}

function run(string $cmd, ?string $cwd = null): array {
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

function getCurrentVersion(string $referenceDir): array {
    $commit = run('git rev-parse HEAD', $referenceDir)['output'];
    $tag = run('git describe --tags --exact-match 2>/dev/null || git describe --tags --abbrev=0 2>/dev/null', $referenceDir)['output'];
    $branch = run('git rev-parse --abbrev-ref HEAD', $referenceDir)['output'];

    return [
        'commit' => $commit,
        'tag' => $tag ?: 'unknown',
        'branch' => $branch,
        'short' => substr($commit, 0, 7),
    ];
}

function getLatestTag(string $referenceDir): string {
    // Get latest v4.x tag
    $result = run('git fetch --tags && git tag -l "v4.*" | sort -V | tail -1', $referenceDir);
    return $result['output'] ?: '';
}

// Parse arguments
$arg = $argv[1] ?? null;

// Check current version
$current = getCurrentVersion($referenceDir);

if ($arg === '--check' || $arg === '-c') {
    echo color("Current TailwindCSS Reference\n", 'bold');
    echo "  Tag:    " . color($current['tag'], 'green') . "\n";
    echo "  Commit: " . color($current['commit'], 'blue') . "\n";
    echo "  Branch: {$current['branch']}\n";
    exit(0);
}

echo color("TailwindCSS Reference Updater\n", 'bold');
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n\n";

echo color("Current version:\n", 'yellow');
echo "  Tag:    {$current['tag']}\n";
echo "  Commit: {$current['short']}\n\n";

// Determine target
$target = $arg;
if (!$target) {
    echo "Fetching latest version...\n";
    $target = getLatestTag($referenceDir);
    if (!$target) {
        echo color("Error: Could not determine latest tag\n", 'red');
        exit(1);
    }
}

echo color("Target version: ", 'yellow') . color($target, 'green') . "\n\n";

// Confirm
echo "This will:\n";
echo "  1. Checkout TailwindCSS reference to {$target}\n";
echo "  2. Re-extract all tests from TypeScript source\n";
echo "  3. Run the full test suite\n\n";

echo "Continue? [y/N] ";
$handle = fopen("php://stdin", "r");
$line = fgets($handle);
fclose($handle);

if (trim(strtolower($line)) !== 'y') {
    echo "Aborted.\n";
    exit(0);
}

echo "\n";

// Step 1: Fetch and checkout
echo color("Step 1: Updating reference...\n", 'bold');
$result = run("git fetch --all --tags", $referenceDir);
if ($result['code'] !== 0) {
    echo color("  Error fetching: {$result['error']}\n", 'red');
    exit(1);
}

$result = run("git checkout {$target}", $referenceDir);
if ($result['code'] !== 0) {
    echo color("  Error checking out {$target}: {$result['error']}\n", 'red');
    exit(1);
}

$newVersion = getCurrentVersion($referenceDir);
echo color("  ✓ ", 'green') . "Checked out {$newVersion['tag']} ({$newVersion['short']})\n\n";

// Step 2: Update README badge
echo color("Step 2: Updating README badge...\n", 'bold');
$readmePath = $rootDir . '/README.md';
$readme = file_get_contents($readmePath);
$readme = preg_replace(
    '/TailwindCSS-v[\d.]+-/',
    "TailwindCSS-{$newVersion['tag']}-",
    $readme
);
file_put_contents($readmePath, $readme);
echo color("  ✓ ", 'green') . "README badge updated to {$newVersion['tag']}\n\n";

// Step 3: Extract tests
echo color("Step 3: Extracting tests...\n", 'bold');
$result = run("composer extract", $rootDir);
if ($result['code'] !== 0) {
    echo color("  Error extracting tests:\n", 'red');
    echo "  {$result['error']}\n";
    echo "  {$result['output']}\n";
    exit(1);
}
echo color("  ✓ ", 'green') . "Tests extracted\n\n";

// Step 4: Run tests
echo color("Step 4: Running tests...\n", 'bold');
passthru("cd " . escapeshellarg($rootDir) . " && composer test", $testResult);

echo "\n";

if ($testResult === 0) {
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'green');
    echo color("✓ Update successful!\n", 'green');
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'green');
    echo "\nUpdated from {$current['tag']} to {$newVersion['tag']}\n";
    echo "\nNext steps:\n";
    echo "  1. Review changes: git diff\n";
    echo "  2. Commit: git add -A && git commit -m \"Update to TailwindCSS {$newVersion['tag']}\"\n";
    echo "  3. Tag: git tag tailwind-{$newVersion['tag']}\n";
    echo "  4. Push: git push && git push --tags\n";
} else {
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'red');
    echo color("✗ Tests failed!\n", 'red');
    echo color("━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n", 'red');
    echo "\nThe TailwindCSS reference was updated but tests are failing.\n";
    echo "This likely means TailwindCSS made breaking changes.\n";
    echo "\nNext steps:\n";
    echo "  1. Review failing tests\n";
    echo "  2. Update PHP implementation to match\n";
    echo "  3. Re-run: composer test\n";
    exit(1);
}
