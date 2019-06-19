<?php

declare(strict_types=1);

namespace App\Service\EntitiesCounter;

use App\Cache\CountedEntitiesWarmer;
use Doctrine\Common\Util\ClassUtils;

class Storage
{
    private $config = [];

    public function __construct(string $cacheDir)
    {
        if (null !== $cacheDir && file_exists($cacheFilename = $cacheDir.CountedEntitiesWarmer::FILENAME)) {
            $this->config = require $cacheFilename;
        }
    }

    public function findIdForEntity(object $entity): ?string
    {
        $className = ClassUtils::getClass($entity);

        return $this->config[$className] ?? null;
    }

    public function findIdForClassName(string $className): ?string
    {
        $realClass = ClassUtils::getRealClass($className);

        return $this->config[$realClass] ?? null;
    }
}
