<?php

declare(strict_types=1);

namespace App\Entity;

use ArrayIterator;
use IteratorAggregate;
use SplFixedArray;
use Traversable;

final class RegistrationCollection implements IteratorAggregate
{
    private SplFixedArray $registrations;

    private function __construct(SplFixedArray $registrations)
    {
        $this->registrations = $registrations;
    }

    public static function fromRegistrationList(Registration ...$registrations): self
    {
        return new self(SplFixedArray::fromArray($registrations));
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->registrations->toArray());
    }
}
