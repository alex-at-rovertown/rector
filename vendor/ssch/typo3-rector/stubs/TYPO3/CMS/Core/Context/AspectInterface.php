<?php

namespace RectorPrefix20210618\TYPO3\CMS\Core\Context;

if (\class_exists('TYPO3\\CMS\\Core\\Context\\AspectInterface')) {
    return;
}
interface AspectInterface
{
    /**
     * @param string $name
     */
    public function get($name);
}
