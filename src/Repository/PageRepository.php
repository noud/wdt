<?php

namespace App\Repository;

use App\Entity\Page;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Page|null find($id, $lockMode = null, $lockVersion = null)
 * @method Page|null findOneBy(array $criteria, array $orderBy = null)
 * @method Page[]    findAll()
 * @method Page[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Page::class);
    }

    public function get(int $id): ?Page
    {
        return $this->find($id);
    }

    public function getBySlug(string $slug): ?Page
    {
        return $this->findOneBy([
            'slug' => $slug,
        ]);
    }

    /**
     * Add a new page.
     */
    public function add(Page $page): void
    {
        $this->getEntityManager()->persist($page);
    }

    /**
     * Remove a page.
     */
    public function remove(Page $page): void
    {
        $this->getEntityManager()->remove($page);
    }
}
