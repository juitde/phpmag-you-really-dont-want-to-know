<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Registration;
use App\Entity\RegistrationCollection;
use App\Repository\Exception\DuplicateRecord;
use App\Repository\Exception\RecordNotFound;
use Ramsey\Uuid\UuidInterface;

interface RegistrationRepository
{
    public function findAll(): RegistrationCollection;

    /**
     * @throws RecordNotFound if the given uuid does not exist in the persistence layer
     */
    public function findById(UuidInterface $id): Registration;

    /**
     * @throws DuplicateRecord if the given registration already exists in the persistence layer
     */
    public function saveNew(Registration $registration): void;

    public function deleteById(UuidInterface $id): void;

    /**
     * @throws RecordNotFound if the given registration does not exist in the persistence layer
     */
    public function saveExisting(Registration $registration): void;
}
