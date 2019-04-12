<?php

namespace App\Service;

use App\Entity\Page;
use App\Repository\PageRepository;

class PageService
{
    /**
     * @var PageRepository
     */
    private $pageRepository;

    public function __construct(PageRepository $pageRepository)
    {
        $this->pageRepository = $pageRepository;
    }

    public function getPageById(int $id): ?Page
    {
        return $this->pageRepository->get($id);
    }

    public function getPageBySlug(string $slug): ?Page
    {
        return $this->pageRepository->getBySlug($slug);
    }

    public function definePage(Page $page): void
    {
        $this->pageRepository->add($page);
    }

    public function removePage(Page $page): void
    {
        $this->pageRepository->remove($page);
    }
}
