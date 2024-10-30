<?php
namespace App\Tests\Controller;

use App\Entity\Category;
use App\Entity\Subscription;
use App\Tests\CustomCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class SubscriptionControllerTest extends CustomCase
{

    /**
     * @var EntityManagerInterface $entityManager
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var KernelBrowser Instance for making requests.
     */
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
        parent::setUp();
    }

    public function testAddSubscriptionSuccess(): void
    {

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request with the JWT token
        $this->client->request('POST', '/api/addSubscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => 'Test Subscription',
            'amount' => 100,
            'billingPeriod' => 'monthly',
            'billingDate' => '2024-11-01',
            'status' => 'active',
            'category' => 'Test Category'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('Subscription added successfully!', $this->client->getResponse()->getContent());
    }

    public function testAddSubscriptionValidationError(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request with the JWT token and missing 'title' field
        $this->client->request('POST', '/api/addSubscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'amount' => 100,
            'billingPeriod' => 'monthly',
            'billingDate' => '2024-11-01',
            'status' => 'active',
            'category' => 'Test Category'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals("The field 'name' is missing.", $responseContent['message']);

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

    }

    public function testAddSubscriptionValidationError2(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request with the JWT token and empty 'title' field
        $this->client->request('POST', '/api/addSubscription', [], [], ['CONTENT_TYPE' => 'application/json'], json_encode([
            'name' => '',
            'amount' => 100,
            'billingPeriod' => 'monthly',
            'billingDate' => '2024-11-01',
            'status' => 'active',
            'category' => 'Test Category'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('Name is required.', $responseContent['message']);
    }

    public function testGetSubscriptionsSuccess(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request with the JWT token
        $this->client->request('POST', '/api/subscriptions', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('subscriptions', $responseContent);
        $this->assertIsArray($responseContent['subscriptions']);
        $this->assertNotEmpty($responseContent['subscriptions']);

        // Check the content of the first Subscription
        $subscription = $responseContent['subscriptions'][0];
        $this->assertEquals('Netflix Subscription', $subscription['name']);
        $this->assertEquals('monthly', $subscription['billingPeriod']);
        $this->assertEquals('2024-05-21', $subscription['billingDate']);
        $this->assertEquals('active', $subscription['status']);
        $this->assertEquals(15.99, $subscription['amount']);
        $this->assertEquals('Entertainment', $subscription['category']);
    }


    public function testEditSubscriptionSuccess(): void
    {
        // Get the fixture Subscription
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(['name' => 'Netflix Subscription']);

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Define the new data for the Subscription
        $newData = [
            'id' => $subscription->getId(),
            'name' => 'Updated Subscription',
            'amount' => 150,
            'billingDate' => '2024-12-01',
            'status' => 'active'
        ];

        // Send the request with the JWT token
        $this->client->request('POST', '/api/editSubscription', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($newData));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('Subscription edited successfully!', $this->client->getResponse()->getContent());

        // Fetch the updated Subscription from the database
        $updatedSubscription = $this->entityManager->getRepository(Subscription::class)->find($subscription->getId());

        // Assert that the Subscription was updated correctly
        $this->assertEquals('Updated Subscription', $updatedSubscription->getName());
        $this->assertEquals(150.00, $updatedSubscription->getAmount());
        $this->assertEquals('2024-12-01', $updatedSubscription->getBillingDate()->format('Y-m-d'));
        $this->assertEquals('active', $updatedSubscription->getStatus());
    }


    public function testEditSubscriptionNotFound(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Define the data for a non-existing Subscription
        $newData = [
            'id' => 99999,
            'name' => 'Updated Subscription',
            'amount' => 150,
            'billingDate' => '2024-12-01',
            'status' => 'active'
        ];

        // Send the request with the JWT token
        $this->client->request('POST', '/api/editSubscription', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($newData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('No subscription found for id 99999', $responseContent['message']);
    }


    public function testEditSubscriptionValidationErrorEmptyFields(): void
    {
        // Get the fixture Subscription
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(['name' => 'Netflix Subscription']);

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request with empty 'title' field
        $newData = [
            'id' => $subscription->getId(),
            'name' => '',
            'amount' => 150,
            'billingDate' => '2024-12-01',
            'status' => 'active'
        ];

        // Send the request with the JWT token
        $this->client->request('POST', '/api/editSubscription', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($newData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('Name is required.', $responseContent['message']);
    }


    public function testEditSubscriptionValidationErrorEmptyFieldId(): void
    {

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request with empty 'title' field
        $newData = [
            'id' => '',
            'name' => 'Updated Subscription',
            'amount' => 150,
            'billingDate' => '2024-12-01',
            'status' => 'active'
        ];

        // Send the request with the JWT token
        $this->client->request('POST', '/api/editSubscription', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($newData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('ID is required.', $responseContent['message']);
    }

    public function testDeleteSubscriptionSuccess(): void
    {
        // Get the fixture Subscription
        $subscription = $this->entityManager->getRepository(Subscription::class)->findOneBy(['name' => 'Netflix Subscription']);

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Define the data for the Subscription
        $data = [
            'id' => $subscription->getId(),
        ];

        // Send the request with the JWT token
        $this->client->request('POST', '/api/deleteSubscription', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($data));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('Subscription deleted', $this->client->getResponse()->getContent());

        // Fetch the deleted Subscription from the database
        $deletedSubscription = $this->entityManager->getRepository(Subscription::class)->find($data['id']);

        // Assert that the Subscription was deleted correctly
        $this->assertNull($deletedSubscription);
    }


    public function testDeleteSubscriptionNotFound(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Define the data for a non-existing Subscription
        $data = [
            'id' => 99999, // Assuming this ID does not exist
        ];

        // Send the request with the JWT token
        $this->client->request('POST', '/api/deleteSubscription', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($data));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('No subscription found for id 99999', $responseContent['message']);
    }

}
