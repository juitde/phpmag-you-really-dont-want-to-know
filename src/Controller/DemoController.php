<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Registration;
use App\Repository\RegistrationRepository;
use App\Request\NewRegistration;
use JMS\Serializer\SerializationContext;
use JMS\Serializer\SerializerInterface;
use Lcobucci\Clock\Clock;
use Ramsey\Uuid\Uuid;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class DemoController
{
    private RegistrationRepository $registrationRepository;
    private SerializerInterface $serializer;
    private Clock $clock;

    public function __construct(
        RegistrationRepository $registrationRepository,
        SerializerInterface $serializer,
        Clock $clock
    )
    {
        $this->registrationRepository = $registrationRepository;
        $this->serializer = $serializer;
        $this->clock = $clock;
    }

    /**
     * @Route("/")
     * @Template("demo/index.html.twig")
     */
    public function home()
    {
        return [
            'registrations' => $this->registrationRepository->findAll(),
        ];
    }

    /**
     * @Route("/register", methods={"POST"})
     */
    public function register(Request $request): Response
    {
        /** @var NewRegistration $registrationData */
        $registrationData = $this->serializer->deserialize(
            $request->getContent(),
            NewRegistration::class,
            'json'
        );

        $this->registrationRepository->saveNew(
            Registration::fromRegistrationRequest($this->clock->now(), $registrationData)
        );

        return new JsonResponse([
            'status' => 302,
            'location' => '/',
        ]);
    }

    /**
     * @Route("/registration/{id}", methods={"DELETE"})
     */
    public function delete(string $id): Response
    {
        $this->registrationRepository->deleteById(Uuid::fromString($id));

        return new JsonResponse([
            'status' => 302,
            'location' => '/',
        ]);
    }

    /**
     * @Route("/registration/{id}/checkout", methods={"POST"})
     */
    public function checkout(string $id): Response
    {
        $this->registrationRepository->saveExisting(
            $this->registrationRepository->findById(Uuid::fromString($id))->checkout($this->clock->now())
        );

        return new JsonResponse([
            'status' => 302,
            'location' => '/',
        ]);
    }

    /**
     * @Route("/registration/{id}", methods={"GET"})
     */
    public function read(string $id): Response
    {
        return new JsonResponse(
            $this->serializer->serialize(
                $this->registrationRepository->findById(Uuid::fromString($id)),
                'json'
            ),
            200,
            [],
            true
        );
    }
}
