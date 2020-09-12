<?php

declare(strict_types=1);

namespace App\Entity;

use App\Request\NewRegistration;
use DateTimeImmutable;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;
use RangeException;

final class Registration
{
    /**
     * @JMS\Type("uuid")
     * @JMS\Groups({"Default", "API/Get"})
     */
    private UuidInterface $id;

    /**
     * @JMS\Type("DateTimeImmutable<'Y-m-d H:i:s', 'UTC'>")
     */
    private DateTimeImmutable $checkInTime;

    /**
     * @JMS\Type("DateTimeImmutable<'Y-m-d H:i:s', 'UTC'>")
     */
    private ?DateTimeImmutable $checkOutTime = null;

    /**
     * @JMS\Type("string")
     * @JMS\Groups({"Default", "API/Get"})
     */
    private string $payload;

    private function __construct(DateTimeImmutable $checkInTime, string $payload)
    {
        $this->id = Uuid::uuid4();
        $this->checkInTime = $checkInTime;
        $this->payload = $payload;
    }

    public static function fromRegistrationRequest(DateTimeImmutable $checkInTime, NewRegistration $request): self
    {
        return new self($checkInTime, $request->payload());
    }

    public function id(): UuidInterface
    {
        return $this->id;
    }

    public function payload(): string
    {
        return $this->payload;
    }

    public function checkInTime(): DateTimeImmutable
    {
        return $this->checkInTime;
    }

    public function checkOutTime(): ?DateTimeImmutable
    {
        return $this->checkOutTime;
    }

    public function hasCheckedOut(): bool
    {
        return $this->checkOutTime !== null;
    }

    public function checkout(DateTimeImmutable $checkOutTime): self
    {
        if ($checkOutTime < $this->checkInTime) {
            throw new RangeException('CheckOut Time must be after CheckIn Time');
        }

        $object = clone $this;
        $object->checkOutTime = $checkOutTime;

        return $object;
    }
}
