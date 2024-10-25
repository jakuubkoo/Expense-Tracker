<?php

namespace App\Tests\Controller\Auth;

use App\Controller\Auth\RegisterController;
use App\Manager\UserManager;
use App\Repository\UserRepository;
use App\Utils\ErrorMessage;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class RegisterControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testRegisterMissingFields()
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', []);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains(['message' => ErrorMessage::NO_FIRST_NAME], $responseData);
    }

    /**
     * @return void
     */
    public function testRegisterPasswordMismatch()
    {
        $client = static::createClient();
        $client->request('POST', '/api/register', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'passwordConfirmation' => 'differentPassword'
        ]);
        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains(['message' => ErrorMessage::PASSWORD_CONFIRMATION_MISMATCH], $responseData);
    }

    /**
     * @return void
     */
    public function testRegisterEmailAlreadyExists()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findOneBy')->willReturn(new \stdClass());

        $userManager = $this->createMock(UserManager::class);

        $controller = new RegisterController();
        $request = new Request([], [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'existing@example.com',
            'password' => 'password',
            'passwordConfirmation' => 'password'
        ]);

        $response = $controller->register($request, $userRepository, $userManager);

        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains(['message' => ErrorMessage::EMAIL_ALREADY_EXISTS], $responseData);
    }

    /**
     * @return void
     */
    public function testRegisterSuccessful()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findOneBy')->willReturn(null);

        $userManager = $this->createMock(UserManager::class);
        $userManager->expects($this->once())->method('registerUser');

        $controller = new RegisterController();
        $request = new Request([], [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'passwordConfirmation' => 'password'
        ]);

        $response = $controller->register($request, $userRepository, $userManager);

        $this->assertEquals(Response::HTTP_OK, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains(['message' => 'Registration successful.'], $responseData);
    }

    /**
     * @return void
     */
    public function testRegisterUnexpectedError()
    {
        $userRepository = $this->createMock(UserRepository::class);
        $userRepository->method('findOneBy')->willReturn(null);

        $userManager = $this->createMock(UserManager::class);
        $userManager->method('registerUser')->will($this->throwException(new \Exception()));

        $controller = new RegisterController();
        $request = new Request([], [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'passwordConfirmation' => 'password'
        ]);

        $response = $controller->register($request, $userRepository, $userManager);

        $this->assertEquals(Response::HTTP_INTERNAL_SERVER_ERROR, $response->getStatusCode());
        $responseData = json_decode($response->getContent(), true);
        $this->assertContains(['message' => ErrorMessage::UNEXPECTED_REGISTER_ERROR], $responseData);
    }
}
