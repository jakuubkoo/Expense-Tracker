<?php

namespace App\Controller;

use App\Entity\User;
use App\Manager\SubscriptionManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class SubscriptionController
 *
 * Handles endpoints related to subscription management.
 */
#[Route('/api', name: 'api_')]
class SubscriptionController extends AbstractController
{
    private SubscriptionManager $subscriptionManager;

    /**
     * Constructor
     */
    public function __construct(SubscriptionManager $subscriptionManager)
    {
        $this->subscriptionManager = $subscriptionManager;
    }

    /**
     * Retrieve all subscriptions for the authenticated user.
     *
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/subscriptions', name: 'subscriptions', methods: ['POST'])]
    public function subscriptions(EntityManagerInterface $em): JsonResponse
    {
        $user = $em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $subscriptionsData = $this->subscriptionManager->getAllSubscriptions($user);

        return new JsonResponse(['subscriptions' => $subscriptionsData], Response::HTTP_OK);
    }

    /**
     * Add a new subscription.
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/addSubscription', name: 'add_subscription', methods: ['POST'])]
    public function addSubscription(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $verificationResult = $this->subscriptionManager->verifySubscriptionData($request);

        if ($verificationResult instanceof JsonResponse) {
            return $verificationResult;
        }

        $user = $em->getRepository(User::class)->findOneBy(['email' => $this->getUser()->getUserIdentifier()]);
        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        try {
            $subscription = $this->subscriptionManager->makeEntity($request, $user);
            $em->persist($subscription);
            $em->flush();
            return new JsonResponse(['message' => 'Subscription added successfully!'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Failed to add subscription'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Edit an existing subscription.
     *
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     */
    #[Route('/editSubscription', name: 'edit_subscription', methods: ['POST'])]
    public function editSubscription(Request $request, EntityManagerInterface $em): JsonResponse
    {
        try {
            $subscription = $this->subscriptionManager->editSubscription($request);

            if ($subscription instanceof JsonResponse) {
                return $subscription;
            }

            $em->persist($subscription);
            $em->flush();
            return new JsonResponse(['message' => 'Subscription edited successfully!'], Response::HTTP_OK);
        } catch (Exception $e) {
            return new JsonResponse(['error' => 'Failed to edit subscription'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Delete a subscription.
     *
     * @param Request $request
     * @return JsonResponse
     */
    #[Route('/deleteSubscription', name: 'delete_subscription', methods: ['POST'])]
    public function deleteSubscription(Request $request): JsonResponse
    {
        return $this->subscriptionManager->deleteSubscription($request);
    }

}
