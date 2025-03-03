<?php

declare (strict_types=1);
namespace RectorPrefix20220202\Symplify\SmartFileSystem\Json;

use RectorPrefix20220202\Nette\Utils\Arrays;
use RectorPrefix20220202\Nette\Utils\Json;
use RectorPrefix20220202\Symplify\SmartFileSystem\FileSystemGuard;
use RectorPrefix20220202\Symplify\SmartFileSystem\SmartFileSystem;
/**
 * @api
 * @see \Symplify\SmartFileSystem\Tests\Json\JsonFileSystem\JsonFileSystemTest
 */
final class JsonFileSystem
{
    /**
     * @var \Symplify\SmartFileSystem\FileSystemGuard
     */
    private $fileSystemGuard;
    /**
     * @var \Symplify\SmartFileSystem\SmartFileSystem
     */
    private $smartFileSystem;
    public function __construct(\RectorPrefix20220202\Symplify\SmartFileSystem\FileSystemGuard $fileSystemGuard, \RectorPrefix20220202\Symplify\SmartFileSystem\SmartFileSystem $smartFileSystem)
    {
        $this->fileSystemGuard = $fileSystemGuard;
        $this->smartFileSystem = $smartFileSystem;
    }
    /**
     * @return mixed[]
     */
    public function loadFilePathToJson(string $filePath) : array
    {
        $this->fileSystemGuard->ensureFileExists($filePath, __METHOD__);
        $fileContent = $this->smartFileSystem->readFile($filePath);
        return \RectorPrefix20220202\Nette\Utils\Json::decode($fileContent, \RectorPrefix20220202\Nette\Utils\Json::FORCE_ARRAY);
    }
    /**
     * @param array<string, mixed> $jsonArray
     */
    public function writeJsonToFilePath(array $jsonArray, string $filePath) : void
    {
        $jsonContent = \RectorPrefix20220202\Nette\Utils\Json::encode($jsonArray, \RectorPrefix20220202\Nette\Utils\Json::PRETTY) . \PHP_EOL;
        $this->smartFileSystem->dumpFile($filePath, $jsonContent);
    }
    /**
     * @param array<string, mixed> $newJsonArray
     */
    public function mergeArrayToJsonFile(string $filePath, array $newJsonArray) : void
    {
        $jsonArray = $this->loadFilePathToJson($filePath);
        $newComposerJsonArray = \RectorPrefix20220202\Nette\Utils\Arrays::mergeTree($jsonArray, $newJsonArray);
        $this->writeJsonToFilePath($newComposerJsonArray, $filePath);
    }
}
