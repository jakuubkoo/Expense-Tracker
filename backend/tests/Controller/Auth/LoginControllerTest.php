<?php

namespace App\Tests\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LoginControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testSuccessfulLogin()
    {
        $client = static::createClient();

        // Register a new user first
        $client->request('POST', '/api/register', [
            'firstName' => 'John',
            'lastName' => 'Doe',
            'email' => 'john.doe@example.com',
            'password' => 'password',
            'passwordConfirmation' => 'password'
        ]);
        $registerResponse = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $registerResponse->getStatusCode());

        // Attempt to log in with the same credentials
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'john.doe@example.com',
                'password' => 'password'
            ])
        );
        $loginResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $loginResponse->getStatusCode());
        $loginData = json_decode($loginResponse->getContent(), true);
        $this->assertArrayHasKey('token', $loginData);
        $this->assertNotEmpty($loginData['token']);
    }

    /**
     * @return void
     */
    public function testLoginWithIncorrectPassword()
    {
        $client = static::createClient();

        // Register a new user first
        $client->request('POST', '/api/register', [
            'firstName' => 'Jane',
            'lastName' => 'Doe',
            'email' => 'jane.doe@example.com',
            'password' => 'correct_password',
            'passwordConfirmation' => 'correct_password'
        ]);
        $registerResponse = $client->getResponse();
        $this->assertEquals(Response::HTTP_OK, $registerResponse->getStatusCode());

        // Attempt to log in with incorrect password
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'john.doe@example.com',
                'password' => 'wrong_password'
            ])
        );
        $loginResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $loginResponse->getStatusCode());
    }

    /**
     * @return void
     */
    public function testLoginWithNonExistentUser()
    {
        $client = static::createClient();

        // Attempt to log in with non-existent user
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'non.existent@example.com',
                'password' => 'wrong_password'
            ])
        );
        $loginResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $loginResponse->getStatusCode());
    }

//    public function testLoginWithMissingFields()
//    {
//        $client = static::createClient();
//
//        // Attempt to log in with missing username
//        $client->request(
//            'POST',
//            '/api/login_check',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            json_encode([
//                'password' => 'wrong_password'
//            ])
//        );
//        $response = $client->getResponse();
//        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
//
//        // Attempt to log in with missing password
//        $client->request(
//            'POST',
//            '/api/login_check',
//            [],
//            [],
//            ['CONTENT_TYPE' => 'application/json'],
//            json_encode([
//                'email' => 'john.doe@example.com'
//            ])
//        );
//        $response = $client->getResponse();
//        $this->assertEquals(Response::HTTP_BAD_REQUEST, $response->getStatusCode());
//    }
}
