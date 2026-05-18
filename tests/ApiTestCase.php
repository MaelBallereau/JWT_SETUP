<?php

namespace App\Tests;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Tools\SchemaTool;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Classe de base partagée par tous les tests qui ont besoin
 * d'un client HTTP, d'une base de données propre, ou de JWT.
 */
abstract class ApiTestCase extends WebTestCase
{
    protected KernelBrowser $client;
    protected EntityManagerInterface $em;

    protected function setUp(): void
    {
        // createClient() démarre le kernel une seule fois par test
        $this->client = static::createClient();
        $this->em     = static::getContainer()->get(EntityManagerInterface::class);

        // Repart d'un schéma vide avant chaque test
        $tool    = new SchemaTool($this->em);
        $classes = $this->em->getMetadataFactory()->getAllMetadata();
        $tool->dropSchema($classes);
        $tool->createSchema($classes);
    }

    protected function tearDown(): void
    {
        // Arrête le kernel pour que le prochain setUp puisse relancer createClient()
        parent::tearDown();
    }

    // ------------------------------------------------------------------
    // Helpers réutilisables dans les tests enfants
    // ------------------------------------------------------------------

    /** Crée un utilisateur puis retourne son token JWT. */
    protected function getToken(string $email = 'test@example.com', string $password = 'motdepasse123'): string
    {
        $this->json('POST', '/user', ['email' => $email, 'password' => $password]);
        $this->json('POST', '/login', ['email' => $email, 'password' => $password]);

        return json_decode($this->client->getResponse()->getContent(), true)['token'];
    }

    /** Envoie une requête JSON sans authentification. */
    protected function json(string $method, string $url, array $body = []): void
    {
        $this->client->request(
            $method,
            $url,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            $body ? json_encode($body) : null
        );
    }

    /** Envoie une requête JSON avec le header Authorization: Bearer <token>. */
    protected function authJson(string $method, string $url, string $token, array $body = []): void
    {
        $this->client->request(
            $method,
            $url,
            [],
            [],
            [
                'CONTENT_TYPE'      => 'application/json',
                'HTTP_AUTHORIZATION' => 'Bearer ' . $token,
            ],
            $body ? json_encode($body) : null
        );
    }

    /** Retourne le corps JSON de la dernière réponse sous forme de tableau. */
    protected function responseData(): array
    {
        return json_decode($this->client->getResponse()->getContent(), true) ?? [];
    }
}
