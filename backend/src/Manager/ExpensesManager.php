<?php

namespace App\Manager;

use App\Entity\Category;
use App\Entity\Expense;
use App\Entity\User;
use App\Repository\ExpenseRepository;
use App\Repository\UserRepository;
use App\Utils\ErrorMessage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Serializer\SerializerInterface;
use function PHPUnit\Framework\isEmpty;

/**
 * Class ExpensesManager
 *
 * This class is responsible for managing the expenses, including verifying
 * the data received in requests and creating Expense entities.
 */
class ExpensesManager
{
    private ExpenseRepository $expenseRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ExpenseRepository $expenseRepository, EntityManagerInterface $entityManager)
    {
        $this->expenseRepository = $expenseRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Verifies the data of the expense from the request.
     *
     * This method checks if the required fields for creating an expense are
     * present in the request. If any field is missing, it returns a JsonResponse
     * with an appropriate error message. If all fields are present, it returns
     * true. If any unexpected error occurs, it returns false.
     *
     * @param Request $request The HTTP request containing the expense data.
     *
     * @return JsonResponse|bool Returns a JsonResponse with an error message
     *                           if any field is missing, true if all fields
     *                           are present, and false if an unexpected error occurs.
     */
    public function verifyExpensesData(Request $request): JsonResponse|bool
    {
        // Get the raw JSON content from the request
        $jsonContent = $request->getContent();

        // Decode the JSON data
        $data = json_decode($jsonContent, true);

        // Define required fields
        $requiredFields = ['title', 'amount', 'date', 'category', 'description'];

        // Check if all required fields are present and not empty
        foreach ($requiredFields as $field) {
            if (!array_key_exists($field, $data)) {
                return new JsonResponse([
                    'message' => "The field '$field' is missing."
                ], Response::HTTP_BAD_REQUEST);
            } elseif (empty($data[$field])) {
                $errorMessageConstant = 'NO_' . strtoupper($field);
                return new JsonResponse([
                    'message' => constant("App\Utils\ErrorMessage::$errorMessageConstant")
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        return true;
    }

    /**
     * Creates an Expense entity from the request data and associates it with the user.
     *
     * This method extracts data from the request, decodes it from JSON format, and uses
     * it to populate a new Expense entity. It then associates the expense with the given
     * user.
     *
     * @param Request $request The HTTP request containing the JSON-encoded expense data.
     * @param User $user The user to associate with the new Expense entity.
     *
     * @return Expense The newly created Expense entity.
     *
     * @throws Exception If there is an error while parsing the date.
     */
    public function makeEntity(Request $request, User $user): Expense
    {
        // Get the raw JSON content from the request
        $jsonContent = $request->getContent();

        // Decode the JSON data
        $data = json_decode($jsonContent, true);

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $data['category']]);

        if(!$category){
            $category = new Category();
            $category->setName($data['category']);
            $category->setDescription('');
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        $expense = new Expense();
        $expense->setTitle($data['title']);
        $expense->setAmount($data['amount']);
        $expense->setDate(new DateTime($data['date']));
        $expense->setCategory($category);
        $expense->setDescription($data['description']);
        $expense->setUser($user);

        return $expense;
    }

    /**
     * Retrieves all expenses for a user.
     *
     * @param User $user The user for whom to retrieve expenses.
     * @return array<mixed> An array of Expense objects.
     */
    public function getAllExpenses(User $user): array
    {
        $expensesArray = [];

        foreach ($user->getExpenses() as $expense) {
            $expensesArray[] = $expense->getAsArray();
        }

        return $expensesArray;
    }

    /**
     * Updates an expense based on the provided request data.
     *
     * @param Request $request The request object containing the JSON data.
     *
     * @return JsonResponse|Expense A JsonResponse object if there was an error or the updated Expense object if successful.
     * @throws Exception
     */
    public function editExpense(Request $request): JsonResponse|Expense
    {
        // Get the raw JSON content from the request
        $jsonContent = $request->getContent();

        // Decode the JSON data
        $data = json_decode($jsonContent, true);

        // First find if some field is not empty
        // I hope I will find better way to handle this
        foreach ($data as $key => $value) {
            if (empty($value)) {
                $errorMessageConstant = 'NO_' . strtoupper($key);
                return new JsonResponse([
                    'message' => constant("App\Utils\ErrorMessage::$errorMessageConstant")
                ], Response::HTTP_BAD_REQUEST);
            }
        }

        // Fetch the existing expense entity
        $expense = $this->expenseRepository->find($data['id']);
        if (!$expense) {
            return new JsonResponse([
                'message' => 'No expense found for id ' . $data['id']
            ], Response::HTTP_BAD_REQUEST);
        }

        // Update the entity with the new data if it has changed
        $updated = false;

        if (isset($data['title']) && trim($data['title']) !== '' && $data['title'] !== $expense->getTitle()) {
            $expense->setTitle($data['title']);
            $updated = true;
        }

        if (isset($data['amount']) && trim($data['amount']) !== '' && $data['amount'] !== $expense->getAmount()) {
            $expense->setAmount($data['amount']);
            $updated = true;
        }

        if (isset($data['date']) && trim($data['date']) !== '' && $data['date'] !== $expense->getDate()->format('Y-m-d')) {
            $expense->setDate(new \DateTime($data['date']));
            $updated = true;
        }

        if (isset($data['category'])) {
            $categoryName = trim($data['category']);

            if ($categoryName !== '' && $categoryName !== $expense->getCategory()?->getName()) {
                $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $categoryName]);
                if ($category) {
                    $expense->setCategory($category);
                    $updated = true;
                } else {
                    return new JsonResponse([
                        'message' => 'No category found for name ' . $categoryName
                    ], Response::HTTP_BAD_REQUEST);
                }
            }
        }

        if (isset($data['description']) && trim($data['description']) !== '' && $data['description'] !== $expense->getDescription()) {
            $expense->setDescription($data['description']);
            $updated = true;
        }

        // Persist the changes to the database if there were any updates
        if ($updated) {
            return $expense;
        }

        return new JsonResponse([
            'message' => ErrorMessage::UNEXPECTED_ERROR
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Deletes an expense.
     *
     * @param Request $request The request object containing the JSON data.
     * @return JsonResponse The JSON response indicating the result of the deletion.
     */
    public function deleteExpense(Request $request): JsonResponse
    {
        // Get the raw JSON content from the request
        $jsonContent = $request->getContent();

        // Decode the JSON data
        $data = json_decode($jsonContent, true);

        // Check if an ID was provided
        if (empty($data['id'])) {
            return new JsonResponse([
                'message' => 'No id provided'
            ], Response::HTTP_BAD_REQUEST);
        }

        // Fetch the existing expense entity
        $expense = $this->expenseRepository->find($data['id']);
        if (!$expense) {
            return new JsonResponse([
                'message' => 'No expense found for id ' . $data['id']
            ], Response::HTTP_BAD_REQUEST);
        }

        // Delete the entity
        $this->entityManager->remove($expense);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Expense deleted'
        ], Response::HTTP_OK);
    }

    /**
     * Filters the expenses for a given category ID.
     *
     * @param int $id The ID of the category.
     * @return JsonResponse The JSON response containing the filtered expenses.
     */
    public function filterExpenses(int $id): JsonResponse
    {

        $category = $this->entityManager->getRepository(Category::class)->find($id);

        $expenses = [];

        foreach ($category->getExpenses() as $expense){
            $expenses = ['expense' => $expense->getAsArray()];
        }

        return new JsonResponse($expenses, Response::HTTP_OK);
    }

}
