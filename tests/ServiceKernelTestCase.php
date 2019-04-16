<?php

namespace App\Tests;

use Doctrine\ORM\Tools\SchemaTool;
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
        unset($this->entityManager); // avoid memory leaks
    }
}
