<?php

namespace App\Manager;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Class UserManager
 *
 * Manages user-related operations such as updating user data on login.
 *
 * @package App\Manager
 */
class UserManager
{
    private EntityManagerInterface $entityManager;
    private UserPasswordHasherInterface $passwordHasherInterface;

    public function __construct(
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasherInterface
    ) {
        $this->entityManager = $entityManager;
        $this->passwordHasherInterface = $passwordHasherInterface;
    }

    /**
     * Get user by email.
     *
     * @param string $email User email
     * @return User|null
     */
    public function getUserRepo(string $email): ?User
    {
        return $this->entityManager->getRepository(User::class)->findOneBy(['email' => $email]);
    }

    /**
     * Register a new user.
     *
     * @param string $firstName First name of the user
     * @param string $lastName Last name of the user
     * @param string $email Email of the user
     * @param string $password Plain text password
     * @return void
     */
    public function registerUser(string $firstName, string $lastName, string $email, string $password): void
    {
        // Check if user exists
        if ($this->getUserRepo($email) == null) {
            try {
                // Initialize user entity
                $user = new User();

                // Hash password
                $hashedPassword = $this->passwordHasherInterface->hashPassword($user, $password);

                // Set user properties
                $user->setFirstName($firstName);
                $user->setLastName($lastName);
                $user->setEmail($email);
                $user->setPassword($hashedPassword);
                $user->setRoles(['ROLE_USER']);

                // Save user to database
                $this->entityManager->persist($user);
                $this->entityManager->flush();

                // TODO: Log registration
            } catch (\Exception $e) {
                // TODO: Handle error gracefully
            }
        }
    }

    /**
     * Get user data based on security context.
     *
     * @param Security $security Symfony security component
     * @return User|null
     */
    public function getUserData(Security $security): ?User
    {
        return $this->getUserRepo($security->getUser()->getUserIdentifier());
    }
}
