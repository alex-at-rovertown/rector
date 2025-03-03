<?php

declare (strict_types=1);
namespace RectorPrefix20220202;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $services = $containerConfigurator->services();
    $services->defaults()->public()->autowire()->autoconfigure();
    $services->load('Rector\\PHPOffice\\', __DIR__ . '/../src')->exclude([__DIR__ . '/../src/Set', __DIR__ . '/../src/Rector', __DIR__ . '/../src/ValueObject']);
};
