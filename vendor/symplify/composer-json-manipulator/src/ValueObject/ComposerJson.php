<?php

declare (strict_types=1);
namespace RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject;

use RectorPrefix20220202\Nette\Utils\Arrays;
use RectorPrefix20220202\Nette\Utils\Strings;
use RectorPrefix20220202\Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter;
use Symplify\SmartFileSystem\SmartFileInfo;
use RectorPrefix20220202\Symplify\SymplifyKernel\Exception\ShouldNotHappenException;
/**
 * @api
 * @see \Symplify\ComposerJsonManipulator\Tests\ValueObject\ComposerJsonTest
 */
final class ComposerJson
{
    /**
     * @var string
     */
    private const CLASSMAP_KEY = 'classmap';
    /**
     * @var string
     */
    private const PHP = 'php';
    /**
     * @var string|null
     */
    private $name;
    /**
     * @var string|null
     */
    private $description;
    /**
     * @var string[]
     */
    private $keywords = [];
    /**
     * @var string|null
     */
    private $homepage;
    /**
     * @var string|string[]|null
     */
    private $license;
    /**
     * @var string|null
     */
    private $minimumStability;
    /**
     * @var bool|null
     */
    private $preferStable;
    /**
     * @var mixed[]
     */
    private $repositories = [];
    /**
     * @var array<string, mixed>
     */
    private $require = [];
    /**
     * @var mixed[]
     */
    private $autoload = [];
    /**
     * @var mixed[]
     */
    private $extra = [];
    /**
     * @var array<string, mixed>
     */
    private $requireDev = [];
    /**
     * @var mixed[]
     */
    private $autoloadDev = [];
    /**
     * @var string[]
     */
    private $orderedKeys = [];
    /**
     * @var string[]
     */
    private $replace = [];
    /**
     * @var array<string, string|string[]>
     */
    private $scripts = [];
    /**
     * @var mixed[]
     */
    private $config = [];
    /**
     * @var \Symplify\SmartFileSystem\SmartFileInfo|null
     */
    private $fileInfo;
    /**
     * @var \Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter
     */
    private $composerPackageSorter;
    /**
     * @var array<string, string>
     */
    private $conflicts = [];
    /**
     * @var mixed[]
     */
    private $bin = [];
    /**
     * @var string|null
     */
    private $type;
    /**
     * @var mixed[]
     */
    private $authors = [];
    /**
     * @var array<string, string>
     */
    private $scriptsDescriptions = [];
    /**
     * @var array<string, string>
     */
    private $suggest = [];
    /**
     * @var string|null
     */
    private $version;
    public function __construct()
    {
        $this->composerPackageSorter = new \RectorPrefix20220202\Symplify\ComposerJsonManipulator\Sorter\ComposerPackageSorter();
    }
    public function setOriginalFileInfo(\Symplify\SmartFileSystem\SmartFileInfo $fileInfo) : void
    {
        $this->fileInfo = $fileInfo;
    }
    public function setName(string $name) : void
    {
        $this->name = $name;
    }
    public function setType(string $type) : void
    {
        $this->type = $type;
    }
    /**
     * @param mixed[] $require
     */
    public function setRequire(array $require) : void
    {
        $this->require = $this->sortPackagesIfNeeded($require);
    }
    public function getVersion() : ?string
    {
        return $this->version;
    }
    public function setVersion(string $version) : void
    {
        $this->version = $version;
    }
    /**
     * @return mixed[]
     */
    public function getRequire() : array
    {
        return $this->require;
    }
    public function getRequirePhpVersion() : ?string
    {
        return $this->require[self::PHP] ?? null;
    }
    /**
     * @return array<string, string>
     */
    public function getRequirePhp() : array
    {
        $requiredPhpVersion = $this->require[self::PHP] ?? null;
        if ($requiredPhpVersion === null) {
            return [];
        }
        return [self::PHP => $requiredPhpVersion];
    }
    /**
     * @return mixed[]
     */
    public function getRequireDev() : array
    {
        return $this->requireDev;
    }
    public function setRequireDev(array $requireDev) : void
    {
        $this->requireDev = $this->sortPackagesIfNeeded($requireDev);
    }
    /**
     * @param string[] $orderedKeys
     */
    public function setOrderedKeys(array $orderedKeys) : void
    {
        $this->orderedKeys = $orderedKeys;
    }
    /**
     * @return string[]
     */
    public function getOrderedKeys() : array
    {
        return $this->orderedKeys;
    }
    /**
     * @return mixed[]
     */
    public function getAutoload() : array
    {
        return $this->autoload;
    }
    /**
     * @return string[]
     */
    public function getAbsoluteAutoloadDirectories() : array
    {
        if ($this->fileInfo === null) {
            throw new \RectorPrefix20220202\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        $autoloadDirectories = $this->getAutoloadDirectories();
        $absoluteAutoloadDirectories = [];
        foreach ($autoloadDirectories as $autoloadDirectory) {
            if (\is_file($autoloadDirectory)) {
                // skip files
                continue;
            }
            $absoluteAutoloadDirectories[] = $this->resolveExistingAutoloadDirectory($autoloadDirectory);
        }
        return $absoluteAutoloadDirectories;
    }
    /**
     * @param mixed[] $autoload
     */
    public function setAutoload(array $autoload) : void
    {
        $this->autoload = $autoload;
    }
    /**
     * @return mixed[]
     */
    public function getAutoloadDev() : array
    {
        return $this->autoloadDev;
    }
    /**
     * @param mixed[] $autoloadDev
     */
    public function setAutoloadDev(array $autoloadDev) : void
    {
        $this->autoloadDev = $autoloadDev;
    }
    /**
     * @return mixed[]
     */
    public function getRepositories() : array
    {
        return $this->repositories;
    }
    /**
     * @param mixed[] $repositories
     */
    public function setRepositories(array $repositories) : void
    {
        $this->repositories = $repositories;
    }
    public function setMinimumStability(string $minimumStability) : void
    {
        $this->minimumStability = $minimumStability;
    }
    public function removeMinimumStability() : void
    {
        $this->minimumStability = null;
    }
    public function getMinimumStability() : ?string
    {
        return $this->minimumStability;
    }
    public function getPreferStable() : ?bool
    {
        return $this->preferStable;
    }
    public function setPreferStable(bool $preferStable) : void
    {
        $this->preferStable = $preferStable;
    }
    public function removePreferStable() : void
    {
        $this->preferStable = null;
    }
    /**
     * @return mixed[]
     */
    public function getExtra() : array
    {
        return $this->extra;
    }
    /**
     * @param mixed[] $extra
     */
    public function setExtra(array $extra) : void
    {
        $this->extra = $extra;
    }
    public function getName() : ?string
    {
        return $this->name;
    }
    public function getVendorName() : ?string
    {
        if ($this->name === null) {
            return null;
        }
        [$vendor] = \explode('/', $this->name);
        return $vendor;
    }
    public function getShortName() : ?string
    {
        if ($this->name === null) {
            return null;
        }
        return \RectorPrefix20220202\Nette\Utils\Strings::after($this->name, '/', -1);
    }
    /**
     * @return string[]
     */
    public function getReplace() : array
    {
        return $this->replace;
    }
    public function isReplacePackageSet(string $packageName) : bool
    {
        return isset($this->replace[$packageName]);
    }
    /**
     * @param string[] $replace
     */
    public function setReplace(array $replace) : void
    {
        \ksort($replace);
        $this->replace = $replace;
    }
    public function setReplacePackage(string $packageName, string $version) : void
    {
        $this->replace[$packageName] = $version;
    }
    /**
     * @return mixed[]
     */
    public function getJsonArray() : array
    {
        $array = \array_filter([\RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::NAME => $this->name, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::DESCRIPTION => $this->description, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::KEYWORDS => $this->keywords, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::HOMEPAGE => $this->homepage, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::LICENSE => $this->license, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTHORS => $this->authors, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::TYPE => $this->type, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE => $this->require, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REQUIRE_DEV => $this->requireDev, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD => $this->autoload, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::AUTOLOAD_DEV => $this->autoloadDev, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPOSITORIES => $this->repositories, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::EXTRA => $this->extra, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::BIN => $this->bin, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS => $this->scripts, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SCRIPTS_DESCRIPTIONS => $this->scriptsDescriptions, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::SUGGEST => $this->suggest, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFIG => $this->config, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::REPLACE => $this->replace, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::CONFLICT => $this->conflicts, \RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::VERSION => $this->version]);
        if ($this->minimumStability !== null) {
            $array[\RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY] = $this->minimumStability;
            $this->moveValueToBack(\RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::MINIMUM_STABILITY);
        }
        if ($this->preferStable !== null) {
            $array[\RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE] = $this->preferStable;
            $this->moveValueToBack(\RectorPrefix20220202\Symplify\ComposerJsonManipulator\ValueObject\ComposerJsonSection::PREFER_STABLE);
        }
        return $this->sortItemsByOrderedListOfKeys($array, $this->orderedKeys);
    }
    /**
     * @param array<string, string|string[]> $scripts
     */
    public function setScripts(array $scripts) : void
    {
        $this->scripts = $scripts;
    }
    /**
     * @param mixed[] $config
     */
    public function setConfig(array $config) : void
    {
        $this->config = $config;
    }
    /**
     * @return mixed[]
     */
    public function getConfig() : array
    {
        return $this->config;
    }
    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }
    public function getDescription() : ?string
    {
        return $this->description;
    }
    /**
     * @param string[] $keywords
     */
    public function setKeywords(array $keywords) : void
    {
        $this->keywords = $keywords;
    }
    /**
     * @return string[]
     */
    public function getKeywords() : array
    {
        return $this->keywords;
    }
    public function setHomepage(string $homepage) : void
    {
        $this->homepage = $homepage;
    }
    public function getHomepage() : ?string
    {
        return $this->homepage;
    }
    /**
     * @param string|string[]|null $license
     */
    public function setLicense($license) : void
    {
        $this->license = $license;
    }
    /**
     * @return string|string[]|null
     */
    public function getLicense()
    {
        return $this->license;
    }
    /**
     * @param mixed[] $authors
     */
    public function setAuthors(array $authors) : void
    {
        $this->authors = $authors;
    }
    /**
     * @return mixed[]
     */
    public function getAuthors() : array
    {
        return $this->authors;
    }
    public function hasPackage(string $packageName) : bool
    {
        if ($this->hasRequiredPackage($packageName)) {
            return \true;
        }
        return $this->hasRequiredDevPackage($packageName);
    }
    public function hasRequiredPackage(string $packageName) : bool
    {
        return isset($this->require[$packageName]);
    }
    public function hasRequiredDevPackage(string $packageName) : bool
    {
        return isset($this->requireDev[$packageName]);
    }
    public function addRequiredPackage(string $packageName, string $version) : void
    {
        if (!$this->hasPackage($packageName)) {
            $this->require[$packageName] = $version;
            $this->require = $this->sortPackagesIfNeeded($this->require);
        }
    }
    public function addRequiredDevPackage(string $packageName, string $version) : void
    {
        if (!$this->hasPackage($packageName)) {
            $this->requireDev[$packageName] = $version;
            $this->requireDev = $this->sortPackagesIfNeeded($this->requireDev);
        }
    }
    public function changePackageVersion(string $packageName, string $version) : void
    {
        if ($this->hasRequiredPackage($packageName)) {
            $this->require[$packageName] = $version;
        }
        if ($this->hasRequiredDevPackage($packageName)) {
            $this->requireDev[$packageName] = $version;
        }
    }
    public function movePackageToRequire(string $packageName) : void
    {
        if (!$this->hasRequiredDevPackage($packageName)) {
            return;
        }
        $version = $this->requireDev[$packageName];
        $this->removePackage($packageName);
        $this->addRequiredPackage($packageName, $version);
    }
    public function movePackageToRequireDev(string $packageName) : void
    {
        if (!$this->hasRequiredPackage($packageName)) {
            return;
        }
        $version = $this->require[$packageName];
        $this->removePackage($packageName);
        $this->addRequiredDevPackage($packageName, $version);
    }
    public function removePackage(string $packageName) : void
    {
        unset($this->require[$packageName], $this->requireDev[$packageName]);
    }
    public function replacePackage(string $oldPackageName, string $newPackageName, string $targetVersion) : void
    {
        if ($this->hasRequiredPackage($oldPackageName)) {
            unset($this->require[$oldPackageName]);
            $this->addRequiredPackage($newPackageName, $targetVersion);
        }
        if ($this->hasRequiredDevPackage($oldPackageName)) {
            unset($this->requireDev[$oldPackageName]);
            $this->addRequiredDevPackage($newPackageName, $targetVersion);
        }
    }
    public function getFileInfo() : ?\Symplify\SmartFileSystem\SmartFileInfo
    {
        return $this->fileInfo;
    }
    /**
     * @param array<string, string> $conflicts
     */
    public function setConflicts(array $conflicts) : void
    {
        $this->conflicts = $conflicts;
    }
    /**
     * @param mixed[] $bin
     */
    public function setBin(array $bin) : void
    {
        $this->bin = $bin;
    }
    /**
     * @return mixed[]
     */
    public function getBin() : array
    {
        return $this->bin;
    }
    /**
     * @return string[]
     */
    public function getPsr4AndClassmapDirectories() : array
    {
        $psr4Directories = \array_values($this->autoload['psr-4'] ?? []);
        $classmapDirectories = $this->autoload['classmap'] ?? [];
        return \array_merge($psr4Directories, $classmapDirectories);
    }
    /**
     * @return array<string, string|string[]>
     */
    public function getScripts() : array
    {
        return $this->scripts;
    }
    /**
     * @return array<string, string>
     */
    public function getScriptsDescriptions() : array
    {
        return $this->scriptsDescriptions;
    }
    /**
     * @return array<string, string>
     */
    public function getSuggest() : array
    {
        return $this->suggest;
    }
    /**
     * @return string[]
     */
    public function getAllClassmaps() : array
    {
        $autoloadClassmaps = $this->autoload[self::CLASSMAP_KEY] ?? [];
        $autoloadDevClassmaps = $this->autoloadDev[self::CLASSMAP_KEY] ?? [];
        return \array_merge($autoloadClassmaps, $autoloadDevClassmaps);
    }
    /**
     * @return array<string, string>
     */
    public function getConflicts() : array
    {
        return $this->conflicts;
    }
    /**
     * @api
     */
    public function getType() : ?string
    {
        return $this->type;
    }
    /**
     * @return string[]
     */
    public function getAutoloadDirectories() : array
    {
        $autoloadDirectories = \array_merge($this->getPsr4AndClassmapDirectories(), $this->getPsr4AndClassmapDevDirectories());
        return \RectorPrefix20220202\Nette\Utils\Arrays::flatten($autoloadDirectories);
    }
    /**
     * @return string[]
     */
    public function getPsr4AndClassmapDevDirectories() : array
    {
        $psr4Directories = \array_values($this->autoloadDev['psr-4'] ?? []);
        $classmapDirectories = $this->autoloadDev['classmap'] ?? [];
        return \array_merge($psr4Directories, $classmapDirectories);
    }
    /**
     * @param array<string, string> $scriptsDescriptions
     */
    public function setScriptsDescriptions(array $scriptsDescriptions) : void
    {
        $this->scriptsDescriptions = $scriptsDescriptions;
    }
    /**
     * @param array<string, string> $suggest
     */
    public function setSuggest(array $suggest) : void
    {
        $this->suggest = $suggest;
    }
    /**
     * @return string[]
     */
    public function getDuplicatedRequirePackages() : array
    {
        $requiredPackageNames = $this->require;
        $requiredDevPackageNames = $this->requireDev;
        return \array_intersect($requiredPackageNames, $requiredDevPackageNames);
    }
    /**
     * @return string[]
     */
    public function getRequirePackageNames() : array
    {
        return \array_keys($this->require);
    }
    private function moveValueToBack(string $valueName) : void
    {
        $key = \array_search($valueName, $this->orderedKeys, \true);
        if ($key !== \false) {
            unset($this->orderedKeys[$key]);
        }
        $this->orderedKeys[] = $valueName;
    }
    /**
     * 2. sort item by prescribed key order
     *
     * @see https://www.designcise.com/web/tutorial/how-to-sort-an-array-by-keys-based-on-order-in-a-secondary-array-in-php
     * @param array<string, mixed> $contentItems
     * @param string[] $orderedVisibleItems
     * @return mixed[]
     */
    private function sortItemsByOrderedListOfKeys(array $contentItems, array $orderedVisibleItems) : array
    {
        \uksort($contentItems, function ($firstContentItem, $secondContentItem) use($orderedVisibleItems) : int {
            $firstItemPosition = $this->findPosition($firstContentItem, $orderedVisibleItems);
            $secondItemPosition = $this->findPosition($secondContentItem, $orderedVisibleItems);
            if ($firstItemPosition === \false) {
                // new item, put in the back
                return -1;
            }
            if ($secondItemPosition === \false) {
                // new item, put in the back
                return -1;
            }
            return $firstItemPosition <=> $secondItemPosition;
        });
        return $contentItems;
    }
    private function resolveExistingAutoloadDirectory(string $autoloadDirectory) : string
    {
        if ($this->fileInfo === null) {
            throw new \RectorPrefix20220202\Symplify\SymplifyKernel\Exception\ShouldNotHappenException();
        }
        $filePathCandidates = [
            $this->fileInfo->getPath() . \DIRECTORY_SEPARATOR . $autoloadDirectory,
            // mostly tests
            \getcwd() . \DIRECTORY_SEPARATOR . $autoloadDirectory,
        ];
        foreach ($filePathCandidates as $filePathCandidate) {
            if (\file_exists($filePathCandidate)) {
                return $filePathCandidate;
            }
        }
        return $autoloadDirectory;
    }
    /**
     * @return array<string, string>
     */
    private function sortPackagesIfNeeded(array $packages) : array
    {
        $sortPackages = $this->config['sort-packages'] ?? \false;
        if ($sortPackages) {
            return $this->composerPackageSorter->sortPackages($packages);
        }
        return $packages;
    }
    /**
     * @return bool|int|string
     */
    private function findPosition(string $key, array $items)
    {
        return \array_search($key, $items, \true);
    }
}
/**
 * @api
 * @see \Symplify\ComposerJsonManipulator\Tests\ValueObject\ComposerJsonTest
 */
\class_alias('RectorPrefix20220202\\Symplify\\ComposerJsonManipulator\\ValueObject\\ComposerJson', 'Symplify\\ComposerJsonManipulator\\ValueObject\\ComposerJson', \false);
