<?php

namespace App\Manager;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Repository\ExpenseRepository;
use App\Utils\ErrorMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

class CategoryManager
{

    private EntityManagerInterface $entityManager;
    private CategoryRepository $categoryRepository;

    public function __construct(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository)
    {
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
    }

    /**
     * Adds a new category based on the provided request data.
     *
     * @param Request $request The request object containing the category data.
     *
     * @return JsonResponse Returns a JSON response with a success message if the category is added successfully,
     *                                      or a bad request response if the 'name' field is missing or empty.
     */
    public function addCategory(Request $request): JsonResponse
    {
        // Get the raw JSON content from the request
        $jsonContent = $request->getContent();

        // Decode the JSON data
        $data = json_decode($jsonContent, true);

        if (isset($data['name']) && trim($data['name']) !== '') {
            $category = new Category();
            $category->setName($data['name']);

            // Set description if it is provided in request data
            if (isset($data['description'])) {
                $category->setDescription($data['description']);
            }

            // Persist and flush the category to the database
            $this->entityManager->persist($category);
            $this->entityManager->flush();

            // Return a successful response
            return new JsonResponse(['message' => 'Category added successfully!'], Response::HTTP_OK);
        }

        // Return a bad request response if the 'name' key wasn't set or was empty
        return new JsonResponse(['message' => 'The field \'name\' is required.'], Response::HTTP_BAD_REQUEST);
    }

    /**
     * List all categories.
     *
     * This method retrieves all existing category entities and returns them as a JSON response.
     *
     * @return JsonResponse Returns a JSON response containing an array of categories. Each category is represented by an associative array
     *                     with the following keys: 'id', 'name', 'description'.
     */
    public function listCategories(): JsonResponse
    {
        $categoryRepository = $this->entityManager->getRepository(Category::class);

        // Get all categories
        $categories = $categoryRepository->findAll();

        // Prepare array to be returned
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName(),
                'description' => $category->getDescription(),
            ];
        }

        // Return categories as JSON
        return new JsonResponse($data);
    }


    /**
     * Edit a category.
     *
     * This method updates an existing category entity with new data if it has changed.
     * It checks if the required fields are present and not empty.
     *
     * @param Request $request The request object containing the category data in JSON format.
     * @return JsonResponse|Category Returns the updated category entity if it has been modified.
     *                              Otherwise, returns a JSON response with an error message.
     *
     * @throws HttpException If the category entity is not found.
     */
    public function editCategory(Request $request): JsonResponse|Category
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        foreach ($data as $key => $value) {
            if (empty($value)) {
                $errorMessageConstant = 'NO_' . strtoupper($key);
                return new JsonResponse([
                    'message' => constant("App\Utils\ErrorMessage::$errorMessageConstant")
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // Fetch the existing category entity
        $category = $this->categoryRepository->find($data['id']);
        if (!$category) {
            return new JsonResponse([
                'message' => 'No category found for id ' . $data['id']
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update the entity with the new data if it has changed
        $updated = false;

        if (isset($data['name']) && trim($data['name']) !== '' && $data['name'] !== $category->getName()) {
            $category->setName($data['name']);
            $updated = true;
        }

        if (isset($data['description']) && trim($data['description']) !== '' && $data['description'] !== $category->getDescription()) {
            $category->setDescription($data['description']);
            $updated = true;
        }

        if ($updated) {
            return $category;
        }

        return new JsonResponse([
            'message' => ErrorMessage::UNEXPECTED_ERROR
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Remove a category.
     *
     * This method removes an existing category entity from the database.
     * It also removes the association of the category with any expenses.
     *
     * @param int $id The ID of the category to be removed.
     * @return Response Returns a JSON response with a success message if the category is removed successfully.
     * @throws HttpException If the category entity is not found.
     */
    public function removeCategory(int $id): Response
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        if(!$category)
            return new JsonResponse(['message' => 'No category found for id ' . $id], Response::HTTP_BAD_REQUEST);

        foreach ($category->getExpenses() as $expense){
            $expense->setCategory(null);
        }

        // Remove the category and flush the changes
        $this->entityManager->remove($category);
        $this->entityManager->flush();

        // Return a successful response
        return new JsonResponse(['message' => 'Category removed successfully!'], Response::HTTP_OK);
    }

}