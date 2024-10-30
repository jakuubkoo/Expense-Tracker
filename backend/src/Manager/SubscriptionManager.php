<?php

namespace App\Manager;

use App\Entity\Category;
use App\Entity\Subscription;
use App\Entity\User;
use App\Repository\SubscriptionRepository;
use App\Utils\ErrorMessage;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class SubscriptionManager
 *
 * This class is responsible for managing the subscriptions, including verifying
 * the data received in requests and creating Subscription entities.
 */
class SubscriptionManager
{
    private SubscriptionRepository $subscriptionRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(SubscriptionRepository $subscriptionRepository, EntityManagerInterface $entityManager)
    {
        $this->subscriptionRepository = $subscriptionRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * Verifies the data of the subscription from the request.
     *
     * @param Request $request The HTTP request containing the subscription data.
     *
     * @return JsonResponse|bool Returns a JsonResponse with an error message
     *                           if any field is missing, true if all fields
     *                           are present, and false if an unexpected error occurs.
     */
    public function verifySubscriptionData(Request $request): JsonResponse|bool
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        $requiredFields = ['name', 'amount', 'billingPeriod', 'billingDate', 'status'];

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
     * Creates a Subscription entity from the request data and associates it with the user.
     *
     * @param Request $request The HTTP request containing the JSON-encoded subscription data.
     * @param User $user The user to associate with the new Subscription entity.
     *
     * @return Subscription The newly created Subscription entity.
     *
     * @throws Exception If there is an error while parsing the date.
     */
    public function makeEntity(Request $request, User $user): Subscription
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $data['category']]);
        if (!$category) {
            $category = new Category();
            $category->setName($data['category']);
            $category->setDescription('');
            $this->entityManager->persist($category);
            $this->entityManager->flush();
        }

        $subscription = new Subscription();
        $subscription->setName($data['name']);
        $subscription->setAmount($data['amount']);
        $subscription->setBillingPeriod($data['billingPeriod']);
        $subscription->setBillingDate(new DateTime($data['billingDate']));
        $subscription->setStatus($data['status']);
        $subscription->setAutoRenew($data['autoRenew'] ?? false);
        $subscription->setCategory($category);
        $subscription->setUser($user);

        return $subscription;
    }

    /**
     * Retrieves all subscriptions for a user.
     *
     * @param User $user The user for whom to retrieve subscriptions.
     * @return array<mixed> An array of Subscription objects.
     */
    public function getAllSubscriptions(User $user): array
    {
        $subscriptionsArray = [];

        foreach ($user->getSubscriptions() as $subscription) {
            $subscriptionsArray[] = $subscription->getAsArray();
        }

        return $subscriptionsArray;
    }

    /**
     * Updates a subscription based on the provided request data.
     *
     * @param Request $request The request object containing the JSON data.
     *
     * @return JsonResponse|Subscription A JsonResponse object if there was an error or the updated Subscription object if successful.
     * @throws Exception
     */
    public function editSubscription(Request $request): JsonResponse|Subscription
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

        $subscription = $this->subscriptionRepository->find($data['id']);
        if (!$subscription) {
            return new JsonResponse([
                'message' => 'No subscription found for id ' . $data['id']
            ], Response::HTTP_BAD_REQUEST);
        }

        $updated = false;

        if (isset($data['name']) && trim($data['name']) !== '' && $data['name'] !== $subscription->getName()) {
            $subscription->setName($data['name']);
            $updated = true;
        }

        if (isset($data['amount']) && trim($data['amount']) !== '' && $data['amount'] !== $subscription->getAmount()) {
            $subscription->setAmount($data['amount']);
            $updated = true;
        }

        if (isset($data['billingDate']) && trim($data['billingDate']) !== '' && $data['billingDate'] !== $subscription->getBillingDate()->format('Y-m-d')) {
            $subscription->setBillingDate(new DateTime($data['billingDate']));
            $updated = true;
        }

        if (isset($data['status']) && trim($data['status']) !== '' && $data['status'] !== $subscription->getStatus()) {
            $subscription->setStatus($data['status']);
            $updated = true;
        }

        if ($updated) {
            return $subscription;
        }

        return new JsonResponse([
            'message' => ErrorMessage::UNEXPECTED_ERROR
        ], Response::HTTP_BAD_REQUEST);
    }

    /**
     * Deletes a subscription.
     *
     * @param Request $request The request object containing the JSON data.
     * @return JsonResponse The JSON response indicating the result of the deletion.
     */
    public function deleteSubscription(Request $request): JsonResponse
    {
        $jsonContent = $request->getContent();
        $data = json_decode($jsonContent, true);

        if (empty($data['id'])) {
            return new JsonResponse([
                'message' => 'No id provided'
            ], Response::HTTP_BAD_REQUEST);
        }

        $subscription = $this->subscriptionRepository->find($data['id']);
        if (!$subscription) {
            return new JsonResponse([
                'message' => 'No subscription found for id ' . $data['id']
            ], Response::HTTP_BAD_REQUEST);
        }

        $this->entityManager->remove($subscription);
        $this->entityManager->flush();

        return new JsonResponse([
            'message' => 'Subscription deleted'
        ], Response::HTTP_OK);
    }

    /**
     * Filters the subscriptions for a given category ID.
     *
     * @param int $id The ID of the category.
     * @return JsonResponse The JSON response containing the filtered subscriptions.
     */
    public function filterSubscriptions(int $id): JsonResponse
    {
        $category = $this->entityManager->getRepository(Category::class)->find($id);

        $subscriptions = [];

        foreach ($category->getSubscriptions() as $subscription) {
            $subscriptions[] = ['subscription' => $subscription->getAsArray()];
        }

        return new JsonResponse($subscriptions, Response::HTTP_OK);
    }
}
