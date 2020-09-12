<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use Ramsey\Uuid\UuidInterface;

trait IdentifyableEntityExceptionTrait
{
    private UuidInterface $uuid;

    public function getId(): UuidInterface
    {
        return $this->uuid;
    }
}
