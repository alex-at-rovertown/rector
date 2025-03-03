<?php

declare (strict_types=1);
namespace RectorPrefix20220202\Symplify\SymplifyKernel\Contract\Config;

use RectorPrefix20220202\Symfony\Component\Config\Loader\LoaderInterface;
use RectorPrefix20220202\Symfony\Component\DependencyInjection\ContainerBuilder;
interface LoaderFactoryInterface
{
    public function create(\RectorPrefix20220202\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder, string $currentWorkingDirectory) : \RectorPrefix20220202\Symfony\Component\Config\Loader\LoaderInterface;
}
