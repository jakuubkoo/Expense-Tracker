<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IndexControllerTest extends WebTestCase
{

    /**
     * @return void
     */
    public function testIndex()
    {
        $client = static::createClient();

        // Make a GET request to the index route
        $client->request('GET', '/');

        // Assert that the response is successful (HTTP status code 200)
        $this->assertSame(200, $client->getResponse()->getStatusCode());

        // Assert that the response is JSON
        $this->assertJson($client->getResponse()->getContent());

        // Decode the JSON response
        $responseData = json_decode($client->getResponse()->getContent(), true);

        // Assert that the response contains the 'message' key
        $this->assertArrayHasKey('message', $responseData);

        // Assert that the message is as expected
        $this->assertSame('ET API loaded successfully', $responseData['message']);
    }

    public function tearDown(): void
    {
        parent::tearDown();

        // Restore the original exception handler
        restore_exception_handler();
    }

}
