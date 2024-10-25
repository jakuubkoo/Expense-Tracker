<?php

namespace App\Tests\Controller\Auth;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class LogoutControllerTest extends WebTestCase
{
    /**
     * @return void
     */
    public function testSuccessfulLogout(): void
    {
        $client = static::createClient();

        // Attempt to log in with the same credentials
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'password' => 'test'
            ])
        );
        $loginResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $loginResponse->getStatusCode());
        $loginData = json_decode($loginResponse->getContent(), true);
        $this->assertArrayHasKey('token', $loginData);
        $this->assertNotEmpty($loginData['token']);

        $client->request('POST', '/api/logout', [], [], ['HTTP_AUTHORIZATION' => 'Bearer ' . $loginData['token']]);
        $logoutResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $logoutResponse->getStatusCode());
        $logoutData = json_decode($logoutResponse->getContent(), true);
        $this->assertArrayHasKey('message', $logoutData);
        $this->assertNotEmpty($logoutData['message']);
        $this->assertEquals('Logout successful', $logoutData['message']);
    }

    /**
     * @return void
     */
    public function testLogoutWithoutToken(): void
    {
        $client = static::createClient();

        // Attempt to log in with the same credentials
        $client->request(
            'POST',
            '/api/login_check',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode([
                'email' => 'test@test.com',
                'password' => 'test'
            ])
        );
        $loginResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_OK, $loginResponse->getStatusCode());
        $loginData = json_decode($loginResponse->getContent(), true);
        $this->assertArrayHasKey('token', $loginData);
        $this->assertNotEmpty($loginData['token']);

        $client->request('POST', '/api/logout');
        $logoutResponse = $client->getResponse();

        $this->assertEquals(Response::HTTP_UNAUTHORIZED, $logoutResponse->getStatusCode());
    }
}
