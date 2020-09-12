<?php

declare(strict_types=1);

namespace App\Repository\Exception;

trait RepositoryExceptionTrait
{
    private string $entityClassname;

    public function getEntityClassname(): string
    {
        return $this->entityClassname;
    }
}
