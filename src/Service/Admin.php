<?php

namespace App\Service;

use App\Model\AdminInterface;

class Admin
{
    /** @var AdminInterface[] */
    private $definitions;

    public function __construct(array $adminDefinitions)
    {
        $this->definitions = $adminDefinitions;
    }

    public function getSegmentNames(): array
    {
        return array_map(function (AdminInterface $admin) {
            return $admin->getName();
        }, $this->definitions);
    }

    public function getConfigForSegment(string $segment): AdminInterface
    {
        foreach ($this->definitions as $definition) {
            if ($definition->getName() === $segment) {
                return $definition;
            }
        }

        throw new \InvalidArgumentException(sprintf('Segment "%s" is not defined.', $segment));
    }
}
