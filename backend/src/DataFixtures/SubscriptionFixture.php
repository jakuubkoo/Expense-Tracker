<?php

namespace App\DataFixtures;

use App\Entity\Expense;
use App\Entity\Subscription;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\Persistence\ObjectManager;

class SubscriptionFixture extends Fixture implements DependentFixtureInterface
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
        $subscription = new Subscription();
        $subscription->setName('Netflix Subscription');
        $subscription->setDescription('Just netflix lol');
        $subscription->setBillingDate(new DateTime('2024-05-21'));
        $subscription->setCategory($this->getReference('category-sub'));
        $subscription->setAmount(15.99);
        $subscription->setBillingPeriod('monthly');
        $subscription->setStatus('active');

        // Get the user reference from UserFixtures and set this to your expense.
        $user = $this->getReference('test-user');
        $subscription->setUser($user);

        // save test expense
        $manager->persist($subscription);
        $manager->flush();

        // commit and re-start new transaction
        $this->connection->commit();
        $manager->flush();
    }

}
