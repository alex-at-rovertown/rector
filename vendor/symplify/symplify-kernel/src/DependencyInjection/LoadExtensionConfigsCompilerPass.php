<?php

declare (strict_types=1);
namespace RectorPrefix20220202\Symplify\SymplifyKernel\DependencyInjection;

use RectorPrefix20220202\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass;
use RectorPrefix20220202\Symfony\Component\DependencyInjection\ContainerBuilder;
/**
 * Mimics @see \Symfony\Component\HttpKernel\DependencyInjection\MergeExtensionConfigurationPass without dependency on
 * symfony/http-kernel
 */
final class LoadExtensionConfigsCompilerPass extends \RectorPrefix20220202\Symfony\Component\DependencyInjection\Compiler\MergeExtensionConfigurationPass
{
    public function process(\RectorPrefix20220202\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder) : void
    {
        $extensionNames = \array_keys($containerBuilder->getExtensions());
        foreach ($extensionNames as $extensionName) {
            $containerBuilder->loadFromExtension($extensionName, []);
        }
        parent::process($containerBuilder);
    }
}
