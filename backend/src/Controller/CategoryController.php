<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\User;
use App\Manager\CategoryManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bridge\Doctrine\Attribute\MapEntity;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api', name: 'api_')]
class CategoryController extends AbstractController
{
    /**
     * @var CategoryManager
     */
    private CategoryManager $categoryManager;

    /**
     * Class constructor
     *
     * @param CategoryManager $categoryManager The manager for category functions
     *
     * @return void
     */
    public function __construct(CategoryManager $categoryManager)
    {
        $this->categoryManager = $categoryManager;
    }

    /**
     * Handle the request to list categories
     *
     * @return JsonResponse The JSON response containing the list of categories
     */
    #[Route('/categories', name: 'categories', methods: ['POST'])]
    public function categories(): JsonResponse
    {
        return $this->categoryManager->listCategories();
    }

    /**
     * Adds a new category.
     *
     * @param Request $request The HTTP request object received from the client.
     *
     * @return JsonResponse The JSON response object containing the result of adding the category.
     *
     * @throws Exception If an error occurs while adding the category.
     */
    #[Route('/addCategory', name: 'add_category', methods: ['POST'])]
    public function addCategory(Request $request): JsonResponse
    {
        return $this->categoryManager->addCategory($request);
    }

    /**
     * Edit Expense method.
     *
     * This method edits an expense.
     *
     * @param Request $request The request object containing the expense data.
     * @return JsonResponse The JSON response containing the edited expense.
     *
     * @throws Exception
     */
    #[Route('/editCategory', name: 'edit_category', methods: ['PUT'])]
    public function editCategory(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $category = $this->categoryManager->editCategory($request);

        if ($category instanceof JsonResponse) {
            return $category;
        }

        $em->persist($category);
        $em->flush();

        return new JsonResponse([
            'message' => 'Category updated successfully!'
        ], Response::HTTP_OK);
    }

    /**
     * Delete a category by its ID
     *
     * Deletes a category based on its ID by making a DELETE request to the '/deleteCategory/{id}' route.
     *
     * @param int $id The ID of the category to delete
     *
     * @return Response The response from removing the category
     */
    #[Route('/deleteCategory/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function deleteCategory($id): Response
    {
        return $this->categoryManager->removeCategory($id);
    }

}