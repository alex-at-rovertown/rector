<?php

declare (strict_types=1);
namespace RectorPrefix20220202;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/../config.php');
    $services = $containerConfigurator->services();
    $services->set(\Ssch\TYPO3Rector\Rector\v11\v3\SubstituteMethodRmFromListOfGeneralUtilityRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v11\v3\SwitchBehaviorOfArrayUtilityMethodsRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v11\v3\UseLanguageTypeForLanguageFieldColumnRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v11\v3\SubstituteExtbaseRequestGetBaseUriRector::class);
};
