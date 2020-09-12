<?php

declare(strict_types=1);

namespace App\Request;

use JMS\Serializer\Annotation as JMS;

final class NewRegistration
{
    /**
     * @JMS\Type("string")
     */
    private string $payload;

    public function payload(): string
    {
        return $this->payload;
    }
}
