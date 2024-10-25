<?php

namespace App\Tests\Controller;

use App\Entity\Category;
use App\Tests\CustomCase;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Component\HttpFoundation\Response;

class CategoryControllerTest extends CustomCase
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var KernelBrowser
     */
    private KernelBrowser $client;

    /**
     * Set up the test environment before each test method is run.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->entityManager = self::$kernel->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Test for listing categories successfully.
     *
     * @return void
     */
    public function testListCategoriesSuccess(): void
    {

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request
        $this->client->request('POST', '/api/categories', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertIsArray($responseContent);
        $this->assertNotEmpty($responseContent);

        // Check the content of the first category
        $category = $responseContent[0];
        $this->assertArrayHasKey('id', $category);
        $this->assertArrayHasKey('name', $category);
        $this->assertArrayHasKey('description', $category);
    }

    /**
     * Test for adding a category successfully.
     *
     * @return void
     */
    public function testAddCategorySuccess(): void
    {

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request
        $this->client->request('POST', '/api/addCategory', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'name' => 'Test Category',
            'description' => 'Test Description'
        ]));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('Category added successfully!', $this->client->getResponse()->getContent());

        // Clear the EntityManager to fetch the latest state
        $this->entityManager->clear();

        // Verify the category was added in the database
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);
        $this->assertNotNull($category);
        $this->assertEquals('Test Description', $category->getDescription());
    }

    /**
     * Test for adding a category with missing 'name' field.
     *
     * @return void
     */
    public function testAddCategoryValidationError(): void
    {

        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request
        $this->client->request('POST', '/api/addCategory', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode([
            'description' => 'Test Description'
        ]));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals("The field 'name' is required.", $responseContent['message']);
    }

    /**
     * Test for editing a category successfully.
     *
     * @return void
     */
    public function testEditCategorySuccess(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // First, add a category to edit
        $category = new Category();
        $category->setName('Test Category');
        $category->setDescription('Test Description');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // Clear the EntityManager to fetch the latest state
        $this->entityManager->clear();

        // Fetch the newly created category to get its ID
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);
        $this->assertNotNull($category);

        // Define the new data for the category
        $newData = [
            'id' => $category->getId(),
            'name' => 'Updated Category',
            'description' => 'Updated Description'
        ];

        // Send the request
        $this->client->request('PUT', '/api/editCategory', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($newData));

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('Category updated successfully!', $this->client->getResponse()->getContent());

        // Clear the EntityManager to fetch the latest state
        $this->entityManager->clear();

        // Fetch the updated category from the database
        $updatedCategory = $this->entityManager->getRepository(Category::class)->find($category->getId());

        // Assert that the category was updated correctly
        $this->assertEquals('Updated Category', $updatedCategory->getName());
        $this->assertEquals('Updated Description', $updatedCategory->getDescription());
    }

    /**
     * Test for editing a non-existing category.
     *
     * @return void
     */
    public function testEditCategoryNotFound(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Define the data for a non-existing category
        $newData = [
            'id' => 99999, // Assuming this ID does not exist
            'name' => 'Updated Category',
            'description' => 'Updated Description'
        ];

        // Send the request
        $this->client->request('PUT', '/api/editCategory', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ], json_encode($newData));

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('No category found for id 99999', $responseContent['message']);
    }

    /**
     * Test for deleting a category successfully.
     *
     * @return void
     */
    public function testDeleteCategorySuccess(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // First, add a category to delete
        $category = new Category();
        $category->setName('Test Category');
        $category->setDescription('Test Description');
        $this->entityManager->persist($category);
        $this->entityManager->flush();

        // Clear the EntityManager to fetch the latest state
        $this->entityManager->clear();

        // Fetch the newly created category to get its ID
        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => 'Test Category']);
        $this->assertNotNull($category);

        $categoryId = $category->getId();

        // Send the request
        $this->client->request('DELETE', '/api/deleteCategory/' . $category->getId(), [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseIsSuccessful();
        $this->assertResponseStatusCodeSame(Response::HTTP_OK);
        $this->assertJson($this->client->getResponse()->getContent());
        $this->assertStringContainsString('Category removed successfully!', $this->client->getResponse()->getContent());

        // Clear the EntityManager to fetch the latest state
        $this->entityManager->clear();

        // Verify the category was deleted from the database
        $deletedCategory = $this->entityManager->getRepository(Category::class)->find($categoryId);
        $this->assertNull($deletedCategory);
    }

    /**
     * Test for deleting a non-existing category.
     *
     * @return void
     */
    public function testDeleteCategoryNotFound(): void
    {
        // simulate user authentication
        $this->simulateUserAuthentication($this->client);

        // Send the request
        $this->client->request('DELETE', '/api/deleteCategory/99999', [], [], [
            'CONTENT_TYPE' => 'application/json',
        ]);

        $this->assertResponseStatusCodeSame(Response::HTTP_BAD_REQUEST);
        $this->assertJson($this->client->getResponse()->getContent());

        $responseContent = json_decode($this->client->getResponse()->getContent(), true);
        $this->assertArrayHasKey('message', $responseContent);
        $this->assertEquals('No category found for id 99999', $responseContent['message']);
    }

}
