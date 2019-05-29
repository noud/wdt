<?php

namespace App\DataFixtures;

use App\Entity\Page;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class PageFixtures extends Fixture
{
    private function loadRegister(ObjectManager $manager): void
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
    }

    private function loadPassword(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setSlug('/wachtwoord-vergeten');
        $page->setTitle('Wachtwoord vergeten');
        $page->setMetaTitle('Vul je e-mail adres in om je wachtwoord te herstellen');
        $page->setContent('Vul je e-mail adres in om je wachtwoord te herstellen.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/wachtwoord-vergeten-bedankt');
        $page->setTitle('Wachtwoord vergeten link gestuurd');
        $page->setMetaTitle('Password reset link sent.');
        $page->setContent('Hier komt tekst te staan.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/wachtwoord-herstellen');
        $page->setTitle('Wachtwoord herstellen');
        $page->setMetaTitle('Vul je nieuwe wachtwoord in');
        $page->setContent('Vul je nieuwe wachtwoord in.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/wachtwoord-veranderd-bedankt');
        $page->setTitle('Wachtwoord is veranderd');
        $page->setMetaTitle('Wachtwoord is veranderd.');
        $page->setContent('Wachtwoord is veranderd.');
        $manager->persist($page);
    }

    private function loadLogin(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setSlug('/inloggen');
        $page->setTitle('Inloggen');
        $page->setMetaTitle('Vul je e-mail adres en wachtwoord in om in te loggen');
        $page->setContent('Vul je e-mail adres en wachtwoord in om in te loggen.');
        $manager->persist($page);
    }

    private function loadTicket(ObjectManager $manager): void
    {
        $page = new Page();
        $page->setSlug('/ticket/create');
        $page->setTitle('Ticket aanmaken');
        $page->setMetaTitle('Je kunt hier een ticket aanmaken');
        $page->setContent('Je kunt hier een ticket aanmaken.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/create-thanks');
        $page->setTitle('Ticket aangemaakt');
        $page->setMetaTitle('Je ticket is aangemaakt en wordt in behandeling genomen');
        $page->setContent('Je ticket is aangemaakt en wordt in behandeling genomen.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/overview');
        $page->setTitle('Tickets overzicht');
        $page->setMetaTitle('Dit zijn de uitstaande tickets van je organisatie');
        $page->setContent('Dit zijn de uitstaande tickets van je organisatie.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/view');
        $page->setTitle('Ticket');
        $page->setMetaTitle('Dit zijn de gegevens van het ticket');
        $page->setContent('Dit zijn de gegevens van het ticket.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/comment/create');
        $page->setTitle('Ticket commentaar aanmaken');
        $page->setMetaTitle('Je kunt hier ticket commentaar aanmaken');
        $page->setContent('Je kunt hier ticket commentaar aanmaken.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/comment/create/success');
        $page->setTitle('Ticket commentaar aangemaakt');
        $page->setMetaTitle('Je ticket commentaar is aangemaakt en wordt in behandeling genomen');
        $page->setContent('Je ticket commentaar is aangemaakt en wordt in behandeling genomen.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/reply/create');
        $page->setTitle('Ticket reactie aanmaken');
        $page->setMetaTitle('Je kunt hier ticket reactie aanmaken');
        $page->setContent('Je kunt hier ticket reactie aanmaken.');
        $manager->persist($page);

        $page = new Page();
        $page->setSlug('/ticket/reply/create/success');
        $page->setTitle('Ticket reactie aangemaakt');
        $page->setMetaTitle('Je ticket reactie is aangemaakt en wordt in behandeling genomen');
        $page->setContent('Je ticket reactie is aangemaakt en wordt in behandeling genomen.');
        $manager->persist($page);
    }

    public function load(ObjectManager $manager): void
    {
        $this->loadLogin($manager);
        $this->loadPassword($manager);
        $this->loadRegister($manager);
        $this->loadTicket($manager);

        $manager->flush();
    }
}
