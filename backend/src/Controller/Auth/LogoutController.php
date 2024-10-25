<?php

namespace App\Controller\Auth;

use App\Manager\TokenManager;
use App\Utils\ErrorMessage;
use Lexik\Bundle\JWTAuthenticationBundle\Encoder\JWTEncoderInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * LogoutController
 *
 * Handles the logout functionality by blacklisting the JWT token.
 */
#[Route('/api', name: 'api_')]
class LogoutController extends AbstractController
{
    /**
     * @var TokenManager
     *
     * Manages operations related to JWT tokens.
     */
    private TokenManager $tokenManager;

    /**
     * LogoutController constructor.
     *
     * @param TokenManager $tokenManager Manages operations related to JWT tokens.
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Logs out the user by blacklisting their JWT token.
     *
     * @param Request $request The HTTP request.
     *
     * @return JsonResponse The response indicating the logout status.
     */
    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $token = $this->tokenManager->getTokenFromRequest($request);

        try {
            // Blacklist old token
            $this->tokenManager->blacklistToken($token);

            // Return response
            return new JsonResponse([
                'message' => 'Logout successful'
            ], Response::HTTP_OK);
        } catch (\Exception $e) {
            // TODO: Handle error

            return new JsonResponse([
                'message' => ErrorMessage::UNEXPECTED_LOGOUT_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
