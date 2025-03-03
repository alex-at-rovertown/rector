<?php

declare (strict_types=1);
namespace RectorPrefix20220202;

use Rector\Renaming\Rector\MethodCall\RenameMethodRector;
use Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector;
use Rector\Renaming\ValueObject\MethodCallRename;
use Rector\Renaming\ValueObject\RenameStaticMethod;
use Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\AdditionalHeadersToArrayTypoScriptRector;
use Ssch\TYPO3Rector\Rector\v8\v0\ChangeMethodCallsForStandaloneViewRector;
use Ssch\TYPO3Rector\Rector\v8\v0\GetFileAbsFileNameRemoveDeprecatedArgumentsRector;
use Ssch\TYPO3Rector\Rector\v8\v0\GetPreferredClientLanguageRector;
use Ssch\TYPO3Rector\Rector\v8\v0\PrependAbsolutePathToGetFileAbsFileNameRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RandomMethodsToRandomClassRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RefactorRemovedMarkerMethodsFromHtmlParserRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RefactorRemovedMethodsFromContentObjectRendererRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RefactorRemovedMethodsFromGeneralUtilityRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RemoveCharsetConverterParametersRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RemoveLangCsConvObjAndParserFactoryRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RemovePropertyUserAuthenticationRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RemoveRteHtmlParserEvalWriteFileRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RemoveWakeupCallFromEntityRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RenderCharsetDefaultsToUtf8Rector;
use Ssch\TYPO3Rector\Rector\v8\v0\RequireMethodsToNativeFunctionsRector;
use Ssch\TYPO3Rector\Rector\v8\v0\RteHtmlParserRector;
use Ssch\TYPO3Rector\Rector\v8\v0\TimeTrackerGlobalsToSingletonRector;
use Ssch\TYPO3Rector\Rector\v8\v0\TimeTrackerInsteadOfNullTimeTrackerRector;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
return static function (\Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator $containerConfigurator) : void {
    $containerConfigurator->import(__DIR__ . '/../config.php');
    $services = $containerConfigurator->services();
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\ChangeMethodCallsForStandaloneViewRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RefactorRemovedMethodsFromGeneralUtilityRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RefactorRemovedMethodsFromContentObjectRendererRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RemovePropertyUserAuthenticationRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\TimeTrackerGlobalsToSingletonRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RemoveWakeupCallFromEntityRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RteHtmlParserRector::class);
    $services->set(\Rector\Renaming\Rector\MethodCall\RenameMethodRector::class)->configure([new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Recordlist\\RecordList', 'printContent', 'mainAction'), new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Recordlist\\Controller\\ElementBrowserFramesetController', 'printContent', 'mainAction'), new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Rtehtmlarea\\Controller\\UserElementsController', 'main', 'main_user'), new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Rtehtmlarea\\Controller\\UserElementsController', 'printContent', 'mainAction'), new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Rtehtmlarea\\Controller\\ParseHtmlController', 'main', 'main_parse_html'), new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Rtehtmlarea\\Controller\\ParseHtmlController', 'printContent', 'mainAction')]);
    $services->set(\Rector\Renaming\Rector\StaticCall\RenameStaticMethodRector::class)->configure([new \Rector\Renaming\ValueObject\RenameStaticMethod('TYPO3\\CMS\\Extbase\\Utility\\ExtensionUtility', 'configureModule', 'TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility', 'configureModule'), new \Rector\Renaming\ValueObject\RenameStaticMethod('TYPO3\\CMS\\Core\\TypoScript\\TemplateService', 'sortedKeyList', 'TYPO3\\CMS\\Core\\Utility\\ArrayUtility', 'filterAndSortByNumericKeys'), new \Rector\Renaming\ValueObject\RenameStaticMethod('TYPO3\\CMS\\Core\\Utility\\GeneralUtility', 'imageMagickCommand', 'TYPO3\\CMS\\Core\\Utility\\CommandUtility', 'imageMagickCommand')]);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\PrependAbsolutePathToGetFileAbsFileNameRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RefactorRemovedMarkerMethodsFromHtmlParserRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RemoveRteHtmlParserEvalWriteFileRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RandomMethodsToRandomClassRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RequireMethodsToNativeFunctionsRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\GetPreferredClientLanguageRector::class);
    $services->set(\Rector\Renaming\Rector\MethodCall\RenameMethodRector::class)->configure([new \Rector\Renaming\ValueObject\MethodCallRename('TYPO3\\CMS\\Fluid\\Core\\Rendering\\RenderingContext', 'getTemplateVariableContainer', 'getVariableProvider')]);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\TimeTrackerInsteadOfNullTimeTrackerRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RemoveCharsetConverterParametersRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\GetFileAbsFileNameRemoveDeprecatedArgumentsRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RemoveLangCsConvObjAndParserFactoryRector::class);
    $services->set(\Ssch\TYPO3Rector\Rector\v8\v0\RenderCharsetDefaultsToUtf8Rector::class);
    $services->set(\Ssch\TYPO3Rector\FileProcessor\TypoScript\Rector\AdditionalHeadersToArrayTypoScriptRector::class);
};
