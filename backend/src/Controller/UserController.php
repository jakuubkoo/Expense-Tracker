<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{

    private SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer)
    {
        $this->serializer = $serializer;
    }

    #[Route('/user', name: 'user', methods: ['GET'])]
    public function expenses(EntityManagerInterface $em): JsonResponse
    {
        $user = $this->getUser();
        $json = $this->serializer->serialize($user, 'json', ['groups' => 'user:read']);

        return new JsonResponse($json, Response::HTTP_OK, [], true);
    }

}
