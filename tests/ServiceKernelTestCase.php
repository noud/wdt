<?php

namespace App\Tests;

use Doctrine\Common\DataFixtures\Executor\ORMExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bridge\Doctrine\DataFixtures\ContainerAwareLoader;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceKernelTestCase extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    protected function setUp()
    {
        self::bootKernel();

        /** @var \Doctrine\ORM\EntityManagerInterface $om */
        $om = self::$container
            ->get('doctrine')
            ->getManager();
        $this->entityManager = $om;

        $this->dropAndCreateDatabaseSchema();
        $this->loadFixtures();
    }

    /**
     * @throws \Doctrine\ORM\Tools\ToolsException
     */
    private function dropAndCreateDatabaseSchema(): void
    {
        $metadatas = $this->entityManager->getMetadataFactory()->getAllMetadata();
        $schemaTool = new SchemaTool($this->entityManager);
        $schemaTool->dropSchema($metadatas);
        $schemaTool->createSchema($metadatas);
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->entityManager->close();
    }

    private function loadFixtures(): void
    {
        // clear
        /** @var \Symfony\Component\DependencyInjection\ContainerInterface $container */
        $container = self::$kernel->getContainer();
        /** @var \Doctrine\ORM\EntityManagerInterface|null $em */
        $em = $container->get('doctrine')->getManager();
        $purger = new ORMPurger($em);
        $purger->purge();

        // load
        $loader = new ContainerAwareLoader($container);
        $loader->loadFromDirectory(__DIR__.'/../src/DataFixtures');
        $executor = new ORMExecutor($container->get('doctrine.orm.entity_manager'));
        $executor->execute($loader->getFixtures(), true);
    }
}
