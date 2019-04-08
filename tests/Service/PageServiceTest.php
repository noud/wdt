<?php

namespace App\Tests\Service;

use App\Service\PageService;
use App\Tests\ServiceKernelTestCase;

class PageServiceTest extends ServiceKernelTestCase
{
    private const SLUG = '/register';

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
        $page = $this->pageService->getPageById(1);
        $slug = $page->getSlug();

        $this->assertSame(self::SLUG, $slug);
    }

    /**
     * Test a valid call to get a page by slug.
     */
    public function getPageBySlug(): void
    {
        /** @var \App\Entity\Page $page */
        $page = $this->pageService->getPageBySlug(self::SLUG);
        $id = $page->getid();

        $this->assertSame(1, $id);
    }
}
