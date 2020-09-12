<?php

declare(strict_types=1);

namespace App\Repository\Exception;

interface RepositoryException
{
    public function getEntityClassname(): string;
}
