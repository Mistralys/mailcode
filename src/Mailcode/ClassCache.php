<?php
/**
 * @package Mailcode
 * @subpackage ClassCache
 */

declare(strict_types=1);

namespace Mailcode;

use AppUtils\ClassHelper\Repository\ClassRepositoryManager;
use AppUtils\FileHelper\FolderInfo;
use AppUtils\FileHelper_Exception;

/**
 * Utility that handles the dynamic class loading and cache.
 *
 * @package Mailcode
 * @subpackage ClassCache
 */
class ClassCache
{
    /**
     * @param string|FolderInfo $folder
     * @param bool $recursive
     * @param string|null $instanceOf
     * @return class-string[]
     * @throws FileHelper_Exception
     */
    public static function findClassesInFolder($folder, bool $recursive=false, ?string $instanceOf=null) : array
    {
        return self::createCache()
            ->findClassesInFolder(
                FolderInfo::factory($folder),
                $recursive,
                $instanceOf
            )
            ->getClasses();
    }

    private static ?ClassRepositoryManager $cache = null;

    private static function createCache() : ClassRepositoryManager
    {
        if(!isset(self::$cache)) {
            self::$cache = ClassRepositoryManager::create(Mailcode::getCacheFolder());
        }

        return self::$cache;
    }
}
