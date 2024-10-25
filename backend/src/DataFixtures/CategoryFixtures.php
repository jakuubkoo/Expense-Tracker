<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Expense;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectManager;

class CategoryFixtures extends Fixture
{

    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        // init db transaction
        $this->connection->beginTransaction();

        // create a user
        $category = new Category();
        $category->setName('Food');
        $category->setDescription('Food category for test');

        $this->addReference('category-food', $category);

        // save test expense
        $manager->persist($category);
        $manager->flush();

        // commit and re-start new transaction
        $this->connection->commit();
        $manager->flush();
    }

}
