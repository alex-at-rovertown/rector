<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace RectorPrefix20220202\Symfony\Component\Console\Command;

use RectorPrefix20220202\Symfony\Component\Console\Completion\CompletionInput;
use RectorPrefix20220202\Symfony\Component\Console\Completion\CompletionSuggestions;
use RectorPrefix20220202\Symfony\Component\Console\Input\InputArgument;
use RectorPrefix20220202\Symfony\Component\Console\Input\InputInterface;
use RectorPrefix20220202\Symfony\Component\Console\Input\InputOption;
use RectorPrefix20220202\Symfony\Component\Console\Output\ConsoleOutputInterface;
use RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface;
use RectorPrefix20220202\Symfony\Component\Process\Process;
/**
 * Dumps the completion script for the current shell.
 *
 * @author Wouter de Jong <wouter@wouterj.nl>
 */
final class DumpCompletionCommand extends \RectorPrefix20220202\Symfony\Component\Console\Command\Command
{
    protected static $defaultName = 'completion';
    protected static $defaultDescription = 'Dump the shell completion script';
    public function complete(\RectorPrefix20220202\Symfony\Component\Console\Completion\CompletionInput $input, \RectorPrefix20220202\Symfony\Component\Console\Completion\CompletionSuggestions $suggestions) : void
    {
        if ($input->mustSuggestArgumentValuesFor('shell')) {
            $suggestions->suggestValues($this->getSupportedShells());
        }
    }
    protected function configure()
    {
        $fullCommand = $_SERVER['PHP_SELF'];
        $commandName = \basename($fullCommand);
        $fullCommand = \realpath($fullCommand) ?: $fullCommand;
        $this->setHelp(<<<EOH
The <info>%command.name%</> command dumps the shell completion script required
to use shell autocompletion (currently only bash completion is supported).

<comment>Static installation
-------------------</>

Dump the script to a global completion file and restart your shell:

    <info>%command.full_name% bash | sudo tee /etc/bash_completion.d/{$commandName}</>

Or dump the script to a local file and source it:

    <info>%command.full_name% bash > completion.sh</>

    <comment># source the file whenever you use the project</>
    <info>source completion.sh</>

    <comment># or add this line at the end of your "~/.bashrc" file:</>
    <info>source /path/to/completion.sh</>

<comment>Dynamic installation
--------------------</>

Add this add the end of your shell configuration file (e.g. <info>"~/.bashrc"</>):

    <info>eval "\$({$fullCommand} completion bash)"</>
EOH
)->addArgument('shell', \RectorPrefix20220202\Symfony\Component\Console\Input\InputArgument::OPTIONAL, 'The shell type (e.g. "bash"), the value of the "$SHELL" env var will be used if this is not given')->addOption('debug', null, \RectorPrefix20220202\Symfony\Component\Console\Input\InputOption::VALUE_NONE, 'Tail the completion debug log');
    }
    protected function execute(\RectorPrefix20220202\Symfony\Component\Console\Input\InputInterface $input, \RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $commandName = \basename($_SERVER['argv'][0]);
        if ($input->getOption('debug')) {
            $this->tailDebugLog($commandName, $output);
            return self::SUCCESS;
        }
        $shell = $input->getArgument('shell') ?? self::guessShell();
        $completionFile = __DIR__ . '/../Resources/completion.' . $shell;
        if (!\file_exists($completionFile)) {
            $supportedShells = $this->getSupportedShells();
            ($output instanceof \RectorPrefix20220202\Symfony\Component\Console\Output\ConsoleOutputInterface ? $output->getErrorOutput() : $output)->writeln(\sprintf('<error>Detected shell "%s", which is not supported by Symfony shell completion (supported shells: "%s").</>', $shell, \implode('", "', $supportedShells)));
            return self::INVALID;
        }
        $output->write(\str_replace(['{{ COMMAND_NAME }}', '{{ VERSION }}'], [$commandName, $this->getApplication()->getVersion()], \file_get_contents($completionFile)));
        return self::SUCCESS;
    }
    private static function guessShell() : string
    {
        return \basename($_SERVER['SHELL'] ?? '');
    }
    private function tailDebugLog(string $commandName, \RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface $output) : void
    {
        $debugFile = \sys_get_temp_dir() . '/sf_' . $commandName . '.log';
        if (!\file_exists($debugFile)) {
            \touch($debugFile);
        }
        $process = new \RectorPrefix20220202\Symfony\Component\Process\Process(['tail', '-f', $debugFile], null, null, null, 0);
        $process->run(function (string $type, string $line) use($output) : void {
            $output->write($line);
        });
    }
    /**
     * @return string[]
     */
    private function getSupportedShells() : array
    {
        return \array_map(function ($f) {
            return \pathinfo($f, \PATHINFO_EXTENSION);
        }, \glob(__DIR__ . '/../Resources/completion.*'));
    }
}
