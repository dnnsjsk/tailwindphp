<?php

declare(strict_types=1);

namespace TailwindPHP\Cli;

use TailwindPHP\Cli\Commands\BuildCommand;
use TailwindPHP\Cli\Commands\CacheClearCommand;
use TailwindPHP\Cli\Commands\InitCommand;
use TailwindPHP\Cli\Commands\WatchCommand;
use TailwindPHP\Cli\Console\Input;
use TailwindPHP\Cli\Console\Output;

/**
 * Main CLI application.
 *
 * Handles command routing, help display, and overall CLI execution.
 */
class Application
{
    public const VERSION = '1.0.0';

    public const NAME = 'TailwindPHP';

    private Input $input;

    private Output $output;

    /** @var array<string, Command> */
    private array $commands = [];

    public function __construct(?Input $input = null, ?Output $output = null)
    {
        $this->input = $input ?? new Input();
        $this->output = $output ?? new Output();

        // Configure output based on input flags
        $this->output->setQuiet($this->input->isQuiet());
        $this->output->setVerbose($this->input->isVerbose());

        // Register built-in commands
        $this->registerCommand(new BuildCommand());
        $this->registerCommand(new WatchCommand());
        $this->registerCommand(new InitCommand());
        $this->registerCommand(new CacheClearCommand());
    }

    /**
     * Register a command.
     */
    public function registerCommand(Command $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Run the CLI application.
     *
     * @return int Exit code
     */
    public function run(): int
    {
        try {
            // Handle --version
            if ($this->input->wantsVersion()) {
                $this->showVersion();

                return 0;
            }

            $commandName = $this->input->getCommand();

            // Handle --help with no command or 'help' command
            if ($this->input->wantsHelp() && $commandName === '') {
                $this->showHelp();

                return 0;
            }

            // No command specified
            if ($commandName === '') {
                $this->showHelp();

                return 0;
            }

            // Handle help for specific command
            if ($commandName === 'help') {
                $helpFor = $this->input->getArgument(0);
                if ($helpFor !== null && isset($this->commands[$helpFor])) {
                    $this->showCommandHelp($this->commands[$helpFor]);

                    return 0;
                }
                $this->showHelp();

                return 0;
            }

            // Handle --help for specific command
            if ($this->input->wantsHelp() && isset($this->commands[$commandName])) {
                $this->showCommandHelp($this->commands[$commandName]);

                return 0;
            }

            // Find and run command
            if (!isset($this->commands[$commandName])) {
                $this->output->error("Unknown command: {$commandName}");
                $this->output->writeln();
                $this->showAvailableCommands();

                return 1;
            }

            $command = $this->commands[$commandName];
            $command->setInput($this->input);
            $command->setOutput($this->output);

            return $command->execute();
        } catch (\Throwable $e) {
            $this->output->error($e->getMessage());

            if ($this->output->isVerbose()) {
                $this->output->writeln();
                $this->output->writeln($this->output->color('gray', $e->getTraceAsString()));
            }

            return 1;
        }
    }

    /**
     * Show version information.
     */
    private function showVersion(): void
    {
        $this->output->writeln(self::NAME . ' ' . $this->output->color('green', self::VERSION));
    }

    /**
     * Show main help screen.
     */
    private function showHelp(): void
    {
        $this->output->writeln();
        $this->output->writeln($this->output->color('bold', self::NAME) . ' ' . $this->output->color('green', self::VERSION));
        $this->output->writeln($this->output->color('gray', 'Generate Tailwind CSS with PHP - no Node.js required.'));
        $this->output->writeln();

        $this->output->writeln($this->output->color('yellow', 'USAGE:'));
        $this->output->writeln('  tailwindphp <command> [options]');
        $this->output->writeln();

        $this->showAvailableCommands();

        $this->output->writeln($this->output->color('yellow', 'GLOBAL OPTIONS:'));
        $this->output->writeln('  ' . $this->output->color('green', '-h, --help') . '       Display help');
        $this->output->writeln('  ' . $this->output->color('green', '-V, --version') . '    Display version');
        $this->output->writeln('  ' . $this->output->color('green', '-v, --verbose') . '    Verbose output');
        $this->output->writeln('  ' . $this->output->color('green', '-q, --quiet') . '      Suppress output');
        $this->output->writeln();

        $this->output->writeln($this->output->color('yellow', 'EXAMPLES:'));
        $this->output->writeln('  ' . $this->output->color('gray', '# Build CSS from PHP templates'));
        $this->output->writeln('  tailwindphp build -c "./templates/**/*.php" -o "./dist/styles.css"');
        $this->output->writeln();
        $this->output->writeln('  ' . $this->output->color('gray', '# Watch for changes'));
        $this->output->writeln('  tailwindphp watch -c "./templates" -o "./dist/styles.css"');
        $this->output->writeln();
        $this->output->writeln('  ' . $this->output->color('gray', '# Initialize config file'));
        $this->output->writeln('  tailwindphp init');
        $this->output->writeln();
    }

    /**
     * Show available commands.
     */
    private function showAvailableCommands(): void
    {
        $this->output->writeln($this->output->color('yellow', 'COMMANDS:'));

        $maxLen = 0;
        foreach ($this->commands as $name => $command) {
            $maxLen = max($maxLen, strlen($name));
        }

        foreach ($this->commands as $name => $command) {
            $padding = str_repeat(' ', $maxLen - strlen($name) + 2);
            $this->output->writeln('  ' . $this->output->color('green', $name) . $padding . $command->getDescription());
        }

        $this->output->writeln();
    }

    /**
     * Show help for a specific command.
     */
    private function showCommandHelp(Command $command): void
    {
        $this->output->writeln();
        $this->output->writeln($this->output->color('yellow', 'COMMAND:'));
        $this->output->writeln('  ' . $this->output->color('green', $command->getName()) . ' - ' . $command->getDescription());
        $this->output->writeln();

        $help = $command->getHelp();
        if ($help !== $command->getDescription()) {
            $this->output->writeln($help);
        }
    }

    /**
     * Get input handler.
     */
    public function getInput(): Input
    {
        return $this->input;
    }

    /**
     * Get output handler.
     */
    public function getOutput(): Output
    {
        return $this->output;
    }
}
