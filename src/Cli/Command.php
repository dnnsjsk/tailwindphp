<?php

declare(strict_types=1);

namespace TailwindPHP\Cli;

use TailwindPHP\Cli\Console\Input;
use TailwindPHP\Cli\Console\Output;

/**
 * Base class for CLI commands.
 */
abstract class Command
{
    protected Input $input;

    protected Output $output;

    /**
     * Get the command name.
     */
    abstract public function getName(): string;

    /**
     * Get short description for command list.
     */
    abstract public function getDescription(): string;

    /**
     * Execute the command.
     *
     * @return int Exit code (0 for success)
     */
    abstract public function execute(): int;

    /**
     * Get detailed help text.
     */
    public function getHelp(): string
    {
        return $this->getDescription();
    }

    /**
     * Set the input handler.
     */
    public function setInput(Input $input): void
    {
        $this->input = $input;
    }

    /**
     * Set the output handler.
     */
    public function setOutput(Output $output): void
    {
        $this->output = $output;
    }

    /**
     * Get option aliases (short => long).
     *
     * @return array<string, string>
     */
    public function getOptionAliases(): array
    {
        return [];
    }

    /**
     * Resolve option aliases.
     */
    protected function getOpt(string $long, string $short = '', mixed $default = null): mixed
    {
        if ($short !== '' && $this->input->hasOption($short)) {
            return $this->input->getOption($short);
        }

        return $this->input->getOption($long, $default);
    }

    /**
     * Get a string option with alias support.
     */
    protected function getStringOpt(string $long, string $short = '', string $default = ''): string
    {
        if ($short !== '' && $this->input->hasOption($short)) {
            $value = $this->input->getOption($short);

            return is_string($value) ? $value : $default;
        }

        return $this->input->getStringOption($long, $default);
    }

    /**
     * Get a boolean option with alias support.
     */
    protected function getBoolOpt(string $long, string $short = '', bool $default = false): bool
    {
        if ($short !== '' && $this->input->hasOption($short)) {
            return true;
        }

        return $this->input->getBoolOption($long, $default);
    }
}
