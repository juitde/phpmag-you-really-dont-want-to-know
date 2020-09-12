<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use Ramsey\Uuid\UuidInterface;
use UnexpectedValueException;

final class DuplicateRecord extends UnexpectedValueException implements RepositoryException, IdentifyableEntityException
{
    use RepositoryExceptionTrait;
    use IdentifyableEntityExceptionTrait;

    public function __construct(string $entityClassname, UuidInterface $uuid)
    {
        parent::__construct(
            sprintf(
                'The given record of entity "%s" with id "%s" already exists.',
                $entityClassname,
                $uuid->toString()
            )
        );

        $this->entityClassname = $entityClassname;
        $this->uuid = $uuid;
    }
}
