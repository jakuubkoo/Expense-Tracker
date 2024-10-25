<?php

namespace App\Controller\Auth;

use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Utils\ErrorMessage;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * Class RegisterController
 *
 * This class handles the registration functionality.
 */
#[Route('/api', name: 'api_')]
class RegisterController extends AbstractController
{

    /**
     * Register a new user.
     *
     * @param Request $request The HTTP request object.
     * @param UserRepository $repository The user repository to check if email already exists.
     * @param UserManager $manager The user manager to execute the registration.
     * @return JsonResponse The JSON response containing the registration success message or error message.
     */
    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $request, UserRepository $repository, UserManager $manager): JsonResponse
    {

        $firstName = $request->get('firstName');
        $lastName = $request->get('lastName');
        $email = $request->get('email');
        $password = $request->get('password');
        $passwordConfirmation = $request->get('passwordConfirmation');

        if(
            $firstName              == null ||
            $lastName               == null ||
            $email                  == null ||
            $password               == null ||
            $passwordConfirmation   == null
        ){
            if (empty($firstName)) {
                return new JsonResponse([
                    ['message' => ErrorMessage::NO_FIRST_NAME]
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($lastName)) {
                return new JsonResponse([
                    ['message' => ErrorMessage::NO_LAST_NAME]
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($email)) {
                return new JsonResponse([
                    ['message' => ErrorMessage::NO_EMAIL]
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($password)) {
                return new JsonResponse([
                    ['message' => ErrorMessage::NO_PASSWORD]
                ], Response::HTTP_BAD_REQUEST);
            }

            if (empty($passwordConfirmation)) {
                return new JsonResponse([
                    ['message' => ErrorMessage::NO_CONF_PASSWORD]
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        if($password !== $passwordConfirmation){
            return new JsonResponse([
                ['message' => ErrorMessage::PASSWORD_CONFIRMATION_MISMATCH]
            ], Response::HTTP_BAD_REQUEST);
        }

        if($repository->findOneBy(['email' => $email]) !== null){
            return new JsonResponse([
                ['message' => ErrorMessage::EMAIL_ALREADY_EXISTS]
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            // execute register
            $manager->registerUser($firstName, $lastName, $email, $password);

            return new JsonResponse([
                ['message' => 'Registration successful.']
            ], Response::HTTP_OK);
        } catch (\Exception) {
            return new JsonResponse([
                ['message' => ErrorMessage::UNEXPECTED_REGISTER_ERROR]
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
