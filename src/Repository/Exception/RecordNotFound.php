<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use OutOfBoundsException;
use Ramsey\Uuid\UuidInterface;

final class RecordNotFound extends OutOfBoundsException implements RepositoryException, IdentifyableEntityException
{
    use RepositoryExceptionTrait;
    use IdentifyableEntityExceptionTrait;

    public function __construct(string $entityClassname, UuidInterface $uuid)
    {
        parent::__construct(
            sprintf(
                'The requested record of entity "%s" with id "%s" does not exist.',
                $entityClassname,
                $uuid->toString()
            )
        );

        $this->entityClassname = $entityClassname;
        $this->uuid = $uuid;
    }
}
