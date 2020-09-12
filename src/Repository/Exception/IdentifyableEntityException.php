<?php

declare(strict_types=1);

namespace App\Repository\Exception;

use Ramsey\Uuid\UuidInterface;

interface IdentifyableEntityException
{
    public function getId(): UuidInterface;
}
