<?php

namespace App\Tests\Service;

use App\Entity\Page;
use App\Service\PageService;
use App\Tests\ServiceKernelTestCase;

class PageServiceTest extends ServiceKernelTestCase
{
    private const PAGE_ID = 1;
    private const PAGE_SLUG = '/register';
    private const SECOND_PAGE_ID = 3;
    private const SECOND_PAGE_SLUG = '/login';

    /**
     * @var PageService
     */
    private $pageService;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        parent::setUp();

        $this->pageService = self::$container->get(PageService::class);
    }

    /**
     * Test a valid call to get a page by id.
     */
    public function testGetPageById(): void
    {
        /** @var \App\Entity\Page $page */
        $page = $this->pageService->getPageById(self::PAGE_ID);
        $slug = $page->getSlug();

        $this->assertSame(self::PAGE_SLUG, $slug);
    }

    /**
     * Test a valid call to get a page by slug.
     */
    public function testGetPageBySlug(): void
    {
        /** @var \App\Entity\Page $page */
        $page = $this->pageService->getPageBySlug(self::PAGE_SLUG);
        $id = $page->getId();

        $this->assertSame(self::PAGE_ID, $id);
    }

    /**
     * Test a valid call to add a page.
     */
    public function testDefinePage(): void
    {
        $page = new Page();
        $page->setSlug(self::SECOND_PAGE_SLUG);
        $page->setContent('something');
        $page->setTitle('something');
        $this->pageService->definePage($page);
        $this->entityManager->flush();
        /** @var \App\Entity\Page $page */
        $page = $this->pageService->getPageBySlug(self::SECOND_PAGE_SLUG);
        $id = $page->getId();

        $this->assertSame(self::SECOND_PAGE_ID, $id);
    }

    /**
     * Test a valid call to remove a page.
     */
    public function testRemovePage(): void
    {
        $page = new Page();
        $page->setSlug(self::SECOND_PAGE_SLUG);
        $page->setContent('something');
        $page->setTitle('something');
        $this->pageService->definePage($page);
        $this->entityManager->flush();
        $this->pageService->removePage($page);
        $this->entityManager->flush();
        $page = $this->pageService->getPageBySlug(self::SECOND_PAGE_SLUG);

        $this->assertNull($page);
    }
}
