<?php

namespace App\Controller;

use App\Entity\Expense;
use App\Entity\User;
use App\Manager\ExpensesManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * Class ExpensesController
 *
 * This controller handles the expenses related endpoints.
 */
#[Route('/api', name: 'api_')]
class ExpensesController extends AbstractController
{
    /**
     * @var ExpensesManager
     */
    private ExpensesManager $expensesManager;

    /**
     * ExpensesController constructor.
     *
     * @param ExpensesManager $expensesManager The manager for handling expense operations.
     */
    public function __construct(ExpensesManager $expensesManager)
    {
        $this->expensesManager = $expensesManager;
    }

    /**
     * Handles the expenses endpoint.
     *
     * @param EntityManagerInterface $em The entity manager.
     * @return JsonResponse The JSON response containing expenses data.
     */
    #[Route('/expenses', name: 'expenses', methods: ['POST'])]
    public function expenses(EntityManagerInterface $em): JsonResponse
    {
        // Retrieve expenses data (you can replace this with your actual logic)
        $expensesData = $this->expensesManager->getAllExpenses(
            $em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()])
        );

        return new JsonResponse(['expenses' => $expensesData], Response::HTTP_OK);
    }


    /**
     * Adds a new expense.
     *
     * This endpoint verifies the request data and creates a new Expense entity
     * if the data is valid. It associates the new expense with the current user.
     *
     * @param Request $request The HTTP request containing the expense data.
     * @param EntityManagerInterface $em The entity manager for database operations.
     *
     * @return JsonResponse A JSON response indicating success or containing error messages.
     *
     * @throws Exception If there is an error during the entity creation process.
     */
    #[Route('/addExpense', name: 'add_expense', methods: ['POST'])]
    public function addExpense(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $verificationResult = $this->expensesManager->verifyExpensesData($request);

        if ($verificationResult instanceof JsonResponse) {
            return $verificationResult;
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        $expense = $this->expensesManager->makeEntity($request, $user);

        $em->persist($expense);
        $em->flush();

        return new JsonResponse([
            'message' => 'Expense added successfully!'
        ], Response::HTTP_OK);
    }

    /**
     * Handles the editExpense endpoint.
     *
     * @param Request $request The request object.
     * @param EntityManagerInterface $em The entity manager.
     * @return JsonResponse The JSON response indicating the success or failure of editing the expense.
     * @throws Exception
     */
    #[Route('/editExpense', name: 'edit_expense', methods: ['POST'])]
    public function editExpense(Request $request, EntityManagerInterface $em): JsonResponse
    {

        $expense = $this->expensesManager->editExpense($request);

        if ($expense instanceof JsonResponse) {
            return $expense;
        }

        $em->persist($expense);
        $em->flush();

        return new JsonResponse([
            'message' => 'Expense edited successfully!'
        ], Response::HTTP_OK);
    }

    /**
     * Handles the deleteExpense endpoint.
     *
     * @param Request $request The HTTP request.
     * @return JsonResponse The JSON response indicating success or failure of the delete operation.
     */
    #[Route('/deleteExpense', name: 'delete_expense', methods: ['POST'])]
    public function deleteExpense(Request $request): JsonResponse
    {
        return $this->expensesManager->deleteExpense($request);
    }

    /**
     * Filters expenses by category ID.
     *
     * @param int $id The category ID to filter expenses.
     * @return JsonResponse The JSON response containing filtered expenses data.
     */
    #[Route('/filterByCategory/{id}', name: 'filter_expense_by_category', methods: ['POST'])]
    public function filterExpensesByCategory(int $id): JsonResponse
    {
        return $this->expensesManager->filterExpenses($id);
    }
}
