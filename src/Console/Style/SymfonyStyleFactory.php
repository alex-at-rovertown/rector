<?php

declare (strict_types=1);
namespace Rector\Core\Console\Style;

use RectorPrefix20220202\Symfony\Component\Console\Application;
use RectorPrefix20220202\Symfony\Component\Console\Input\ArgvInput;
use RectorPrefix20220202\Symfony\Component\Console\Output\ConsoleOutput;
use RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface;
use RectorPrefix20220202\Symfony\Component\Console\Style\SymfonyStyle;
use RectorPrefix20220202\Symplify\PackageBuilder\Reflection\PrivatesCaller;
final class SymfonyStyleFactory
{
    /**
     * @readonly
     * @var \Symplify\PackageBuilder\Reflection\PrivatesCaller
     */
    private $privatesCaller;
    public function __construct(\RectorPrefix20220202\Symplify\PackageBuilder\Reflection\PrivatesCaller $privatesCaller)
    {
        $this->privatesCaller = $privatesCaller;
    }
    public function create() : \RectorPrefix20220202\Symfony\Component\Console\Style\SymfonyStyle
    {
        $argvInput = new \RectorPrefix20220202\Symfony\Component\Console\Input\ArgvInput();
        $consoleOutput = new \RectorPrefix20220202\Symfony\Component\Console\Output\ConsoleOutput();
        // to configure all -v, -vv, -vvv options without memory-lock to Application run() arguments
        $this->privatesCaller->callPrivateMethod(new \RectorPrefix20220202\Symfony\Component\Console\Application(), 'configureIO', [$argvInput, $consoleOutput]);
        $debugArgvInputParameterOption = $argvInput->getParameterOption('--debug');
        // --debug is called
        if ($debugArgvInputParameterOption === null) {
            $consoleOutput->setVerbosity(\RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface::VERBOSITY_DEBUG);
        }
        return new \RectorPrefix20220202\Symfony\Component\Console\Style\SymfonyStyle($argvInput, $consoleOutput);
    }
}
