<?php

namespace App\DataFixtures;

use App\Entity\Expense;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectManager;

class ExpenseFixture extends Fixture implements DependentFixtureInterface
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function getDependencies(): array
    {
        return [CategoryFixtures::class, UserFixtures::class];
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        // init db transaction
        $this->connection->beginTransaction();

        // create a user
        $expense = new Expense();
        $expense->setTitle('Groceries');
        $expense->setDescription('Purchased groceries for the week');
        $expense->setDate(new DateTime('2024-05-21'));
        $expense->setCategory($this->getReference('category-food'));
        $expense->setAmount('50.25');

        // Get the user reference from UserFixtures and set this to your expense.
        $user = $this->getReference('test-user');
        $expense->setUser($user);

        // save test expense
        $manager->persist($expense);
        $manager->flush();

        // commit and re-start new transaction
        $this->connection->commit();
        $manager->flush();
    }

}
