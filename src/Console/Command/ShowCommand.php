<?php

declare (strict_types=1);
namespace Rector\Core\Console\Command;

use Rector\Core\Contract\Console\OutputStyleInterface;
use Rector\Core\Contract\Rector\RectorInterface;
use Rector\PostRector\Contract\Rector\ComplementaryRectorInterface;
use Rector\PostRector\Contract\Rector\PostRectorInterface;
use RectorPrefix20220202\Symfony\Component\Console\Command\Command;
use RectorPrefix20220202\Symfony\Component\Console\Input\InputInterface;
use RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface;
use RectorPrefix20220202\Symplify\PackageBuilder\Console\Command\CommandNaming;
final class ShowCommand extends \RectorPrefix20220202\Symfony\Component\Console\Command\Command
{
    /**
     * @readonly
     * @var \Rector\Core\Contract\Console\OutputStyleInterface
     */
    private $outputStyle;
    /**
     * @var RectorInterface[]
     * @readonly
     */
    private $rectors;
    /**
     * @param RectorInterface[] $rectors
     */
    public function __construct(\Rector\Core\Contract\Console\OutputStyleInterface $outputStyle, array $rectors)
    {
        $this->outputStyle = $outputStyle;
        $this->rectors = $rectors;
        parent::__construct();
    }
    protected function configure() : void
    {
        $this->setName(\RectorPrefix20220202\Symplify\PackageBuilder\Console\Command\CommandNaming::classToName(self::class));
        $this->setDescription('Show loaded Rectors with their configuration');
    }
    protected function execute(\RectorPrefix20220202\Symfony\Component\Console\Input\InputInterface $input, \RectorPrefix20220202\Symfony\Component\Console\Output\OutputInterface $output) : int
    {
        $this->outputStyle->title('Loaded Rector rules');
        $rectors = \array_filter($this->rectors, function (\Rector\Core\Contract\Rector\RectorInterface $rector) : bool {
            if ($rector instanceof \Rector\PostRector\Contract\Rector\PostRectorInterface) {
                return \false;
            }
            return !$rector instanceof \Rector\PostRector\Contract\Rector\ComplementaryRectorInterface;
        });
        $rectorCount = \count($rectors);
        if ($rectorCount === 0) {
            $warningMessage = \sprintf('No Rectors were loaded.%sAre sure your "rector.php" config is in the root?%sTry "--config <path>" option to include it.', \PHP_EOL . \PHP_EOL, \PHP_EOL);
            $this->outputStyle->warning($warningMessage);
            return self::FAILURE;
        }
        $rectorCount = \count($rectors);
        foreach ($rectors as $rector) {
            $this->outputStyle->writeln(' * ' . \get_class($rector));
        }
        $message = \sprintf('%d loaded Rectors', $rectorCount);
        $this->outputStyle->success($message);
        $this->outputStyle->error('The "show" command is deprecated and will be removed, as it was used only for more output on Rector run. Use the "--debug" option and process command for debugging output instead.');
        // to spot the error message
        \sleep(3);
        return \RectorPrefix20220202\Symfony\Component\Console\Command\Command::FAILURE;
    }
}
