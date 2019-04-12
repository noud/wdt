<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setSlug('/register');
        $page->setTitle('Account aanmaken');
        $page->setMetaTitle('Create Account');
        $page->setContent('Hier komt tekst te staan over de werkwijze van het aanmaken van een account. Deze moet te beheren zijn.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/register-thanks');
        $page->setTitle('Bedankt voor je registratie');
        $page->setMetaTitle('Thanks creating your Account');
        $page->setContent('Hier komt tekst te staan.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/register-activate');
        $page->setTitle('Activeer Account');
        $page->setMetaTitle('Activating an Account');
        $page->setContent('Hier komt tekst te staan.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/register-activate-thanks');
        $page->setTitle('Bedankt voor het activeren');
        $page->setMetaTitle('Thanks activating an Account');
        $page->setContent('Hier komt tekst te staan.');
        $manager->persist($page);

        $manager->flush();
    }
}
