<?php

namespace App\Tests\Service;

use App\Service\PageService;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PageServiceTest extends KernelTestCase
{
    private const VALID_NAME = 'test@test.nl';

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        self::bootKernel();

        $this->pageService = self::$container->get(PageService::class);
    }

    /**
     * Test a valid call to create a ResetPasswordRequest for a username.
     */
    public function testGetPageById(): void
    {
        /** @var \App\Entity\Page $page */
        $page = $this->pageService->getPageById(1);
        $slug = $page->getSlug();

        $this->assertSame('/register', $slug);
    }
}
