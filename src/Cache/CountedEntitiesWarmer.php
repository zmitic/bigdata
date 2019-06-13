<?php

declare(strict_types=1);

namespace App\Cache;

use App\Annotation\Counted;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Symfony\Component\HttpKernel\CacheWarmer\CacheWarmer;
use Doctrine\Common\Annotations\Reader;
use function in_array;

class CountedEntitiesWarmer extends CacheWarmer
{
    public const FILENAME = '/wjb_entities_counter.php';

    /** @var EntityManagerInterface */
    private $em;

    /** @var Reader */
    private $annotationReader;

    public function __construct(EntityManagerInterface $em, Reader $annotationReader)
    {
        $this->em = $em;
        $this->annotationReader = $annotationReader;
    }

    public function isOptional(): bool
    {
        return false;
    }

    public function warmUp($cacheDir): void
    {
        $cache = [];
        /** @var ClassMetadata[] $allMetadata */
        $allMetadata = $this->em->getMetadataFactory()->getAllMetadata();
        foreach ($allMetadata as $metadata) {
            $className = $metadata->rootEntityName;
            $reflection = new \ReflectionClass($className);
            /** @var Counted $counted */
            $counted = $this->annotationReader->getClassAnnotation($reflection, Counted::class);
            if ($counted) {
                if (in_array($counted->name, $cache, true)) {
                    throw new \LogicException(sprintf('You have duplicate name for @Counted annotation: "%s".', $counted->name));
                }
                $cache[$metadata->rootEntityName] = $counted->name;
            }
        }

        $cacheValue = sprintf('<?php return %s;', var_export($cache, true));
        $this->writeCacheFile($cacheDir.self::FILENAME, $cacheValue);
    }
}
