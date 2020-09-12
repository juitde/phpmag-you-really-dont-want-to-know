<?php

declare(strict_types=1);

namespace App\Persistence\FileSystem;

use App\Entity\Registration;
use App\Entity\RegistrationCollection;
use App\Repository\Exception\DuplicateRecord;
use App\Repository\Exception\RecordNotFound;
use App\Repository\RegistrationRepository;
use JMS\Serializer\SerializerInterface;
use League\Flysystem\FilesystemInterface;
use Ramsey\Uuid\UuidInterface;

final class FileSystemRegistrationRepository implements RegistrationRepository
{
    private const SUB_PATH = '/registrations';

    private FilesystemInterface $filesystem;
    private SerializerInterface $serializer;

    public function __construct(FilesystemInterface $filesystem, SerializerInterface $serializer)
    {
        $this->filesystem = $filesystem;
        $this->serializer = $serializer;
    }

    public function findAll(): RegistrationCollection
    {
        return RegistrationCollection::fromRegistrationList(
            ...array_map(
                fn($item) => $this->serializer->deserialize($this->filesystem->read($item['path']), Registration::class, 'json'),
                call_user_func(
                    function(array $a) {
                        usort($a, function ($a, $b) { return $a['timestamp'] < $b['timestamp']; });
                        return $a;
                    },
                    $this->filesystem->listContents(self::SUB_PATH)
                )
            )
        );
    }

    public function findById(UuidInterface $id): Registration
    {
        $filePathname = sprintf('%s/%s.json', self::SUB_PATH, $id->toString());
        if (!$this->filesystem->has($filePathname)) {
            throw new RecordNotFound(Registration::class, $id);
        }

        return $this->serializer->deserialize($this->filesystem->read($filePathname), Registration::class, 'json');
    }

    public function saveNew(Registration $registration): void
    {
        $filePathname = sprintf('%s/%s.json', self::SUB_PATH, $registration->id()->toString());
        if ($this->filesystem->has($filePathname)) {
            throw new DuplicateRecord(Registration::class, $registration->id());
        }

        $this->filesystem->put($filePathname, $this->serializer->serialize($registration, 'json'));
    }

    public function deleteById(UuidInterface $id): void
    {
        $filePathname = sprintf('%s/%s.json', self::SUB_PATH, $id->toString());
        if ($this->filesystem->has($filePathname)) {
            $this->filesystem->delete($filePathname);
        }
    }

    public function saveExisting(Registration $registration): void
    {
        $filePathname = sprintf('%s/%s.json', self::SUB_PATH, $registration->id()->toString());
        if (!$this->filesystem->has($filePathname)) {
            throw new RecordNotFound(Registration::class, $registration->id());
        }

        $this->filesystem->put($filePathname, $this->serializer->serialize($registration, 'json'));
    }
}
