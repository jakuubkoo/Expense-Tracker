<?php

namespace App\Middleware;

use App\Manager\TokenManager;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

/**
 * TokenValidationMiddleware
 *
 * Middleware to validate JWT tokens in incoming requests.
 */
class TokenValidationMiddleware
{

    /**
     * @var TokenManager
     *
     * Manages operations related to JWT tokens, such as extraction and validation.
     */
    private TokenManager $tokenManager;

    /**
     * TokenValidationMiddleware constructor.
     *
     * @param TokenManager $tokenManager Manages operations related to JWT tokens.
     */
    public function __construct(TokenManager $tokenManager)
    {
        $this->tokenManager = $tokenManager;
    }

    /**
     * Handles the kernel request event to validate the JWT token.
     *
     * Extracts the token from the request and checks if it is blacklisted. If the token is blacklisted,
     * the request is terminated with an error message.
     *
     * @param RequestEvent $event The event object that contains the request.
     *
     * @return void
     */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $token = $this->tokenManager->getTokenFromRequest($request);

        if (!empty($token)) {
            if (!$this->tokenManager->isTokenValid($token)) {
                // Terminate the request with a 401 response for invalid or expired tokens
                $response = new JsonResponse(['error' => 'Invalid or expired JWT token'], 401);
                $event->setResponse($response);
            }
        } else {
            // Optional: handle requests without a token if they require authentication
            $response = new JsonResponse(['error' => 'Token missing'], 401);
            $event->setResponse($response);
        }
    }

}
