<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use App\Entity\Admin;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\Attributes as PHPUnit;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * Test fonctionnel : scénario de connexion (login) et accès à l'admin.
 *
 * Les tests qui ont besoin d'un admin en BDD utilisent une méthode
 * createTestAdmin() qui ne crée l'utilisateur qu'une seule fois.
 */
#[PHPUnit\CoversNothing]
class LoginTest extends WebTestCase
{
    private static ?Admin $testAdmin = null;
    private KernelBrowser $client;

    protected function setUp(): void
    {
        $this->client = static::createClient();
    }

    /**
     * Crée l'admin de test une seule fois pour tous les tests.
     */
    private function createTestAdmin(): Admin
    {
        if (self::$testAdmin !== null) {
            return self::$testAdmin;
        }

        $container = self::getContainer();
        /** @var EntityManagerInterface $em */
        $em = $container->get(EntityManagerInterface::class);
        /** @var UserPasswordHasherInterface $passwordHasher */
        $passwordHasher = $container->get(UserPasswordHasherInterface::class);

        $admin = new Admin();
        $admin->setPrenom('Test');
        $admin->setNom('Admin');
        $admin->setEmail('admin-test@ferme.fr');
        $admin->setPasswordHash($passwordHasher->hashPassword($admin, 'testpass'));

        $em->persist($admin);
        $em->flush();

        self::$testAdmin = $admin;

        return $admin;
    }

    /**
     * La page d'accueil est publique.
     */
    #[PHPUnit\Test]
    public function testHomePageIsPublic(): void
    {
        $this->client->request('GET', '/');

        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Bienvenue');
    }

    /**
     * Connexion avec identifiants valides → accès au dashboard admin.
     */
    #[PHPUnit\Test]
    public function testLoginWithValidCredentials(): void
    {
        $this->createTestAdmin();

        $crawler = $this->client->request('GET', '/login');
        self::assertResponseIsSuccessful();

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin-test@ferme.fr',
            '_password' => 'testpass',
        ]);

        $this->client->submit($form);
        self::assertResponseRedirects();
        $this->client->followRedirect();

        $this->client->request('GET', '/admin');
        self::assertResponseIsSuccessful();
        self::assertSelectorTextContains('h1', 'Tableau de bord');
    }

    /**
     * Mauvais mot de passe → redirection /login + message d'erreur.
     */
    #[PHPUnit\Test]
    public function testLoginWithInvalidCredentials(): void
    {
        $this->createTestAdmin();

        $crawler = $this->client->request('GET', '/login');

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'admin-test@ferme.fr',
            '_password' => 'mauvais-mot-de-passe',
        ]);

        $this->client->submit($form);
        self::assertResponseRedirects('/login');

        $crawler = $this->client->followRedirect();

        $errorBlock = $crawler->filter('.bg-red-50');
        self::assertCount(1, $errorBlock);
        self::assertStringContainsString('Identifiants invalides', $errorBlock->text());
    }

    /**
     * Accès à /admin sans auth → redirigé vers /login.
     */
    #[PHPUnit\Test]
    public function testAdminRequiresAuthentication(): void
    {
        $this->client->request('GET', '/admin');
        self::assertResponseRedirects('/login');
    }
}
