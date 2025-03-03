<?php

declare (strict_types=1);
namespace RectorPrefix20220202;

use Ssch\TYPO3Rector\Set\Typo3LevelSetList;
use Ssch\TYPO3Rector\Set\Typo3SetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(\Ssch\TYPO3Rector\Set\Typo3LevelSetList::UP_TO_TYPO3_10);
    $containerConfigurator->import(\Ssch\TYPO3Rector\Set\Typo3SetList::TYPO3_11);
};
