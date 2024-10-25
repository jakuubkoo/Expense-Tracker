<?php

namespace App\Controller;

use App\Manager\UserManager;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

#[Route('/api', name: 'api_', methods: ['POST'])]
class UserStatusController extends AbstractController
{

    #[Route('/user/status', name: 'user_status')]
    public function getUserStatus(UserManager $userManager, Security $security): JsonResponse
    {
        // get user data
        $userData = $userManager->getUserData($security);

        // return user data
        return $this->json([
            'user_status' => [
                'firstName' => $userData->getFirstName(),
                'lastName' => $userData->getLastName(),
                'email' => $userData->getEmail(),
                'roles' => $userData->getRoles(),
            ],
            'stats' => [
                'someTestStats' => 'testStats'
            ]
        ], Response::HTTP_OK);
    }
}