<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

class UserControllerTest extends ApiTestCase
{
    // ------------------------------------------------------------------
    // POST /user — inscription
    // ------------------------------------------------------------------

    public function testRegisterUserReturns200(): void
    {
        $this->json('POST', '/user', [
            'email'    => 'test@example.com',
            'password' => 'motdepasse123',
        ]);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testRegisterUserReturnsSuccessMessage(): void
    {
        $this->json('POST', '/user', [
            'email'    => 'test@example.com',
            'password' => 'motdepasse123',
        ]);

        $data = $this->responseData();
        $this->assertSame('User created successfully', $data['message']);
        $this->assertSame('test@example.com', $data['user']['email']);
    }

    // ------------------------------------------------------------------
    // POST /login — connexion
    // ------------------------------------------------------------------

    public function testLoginWithValidCredentialsReturnsToken(): void
    {
        // Étape 1 : créer l'utilisateur
        $this->json('POST', '/user', [
            'email'    => 'test@example.com',
            'password' => 'motdepasse123',
        ]);

        // Étape 2 : se connecter
        $this->json('POST', '/login', [
            'email'    => 'test@example.com',
            'password' => 'motdepasse123',
        ]);

        $this->assertResponseStatusCodeSame(200);

        $data = $this->responseData();
        $this->assertArrayHasKey('token', $data);
        $this->assertNotEmpty($data['token']);
    }

    public function testLoginWithWrongPasswordReturns401(): void
    {
        $this->json('POST', '/user', [
            'email'    => 'test@example.com',
            'password' => 'motdepasse123',
        ]);

        $this->json('POST', '/login', [
            'email'    => 'test@example.com',
            'password' => 'mauvaismdp',
        ]);

        $this->assertResponseStatusCodeSame(401);
        $this->assertSame('Invalid credentials', $this->responseData()['message']);
    }

    public function testLoginWithUnknownEmailReturns401(): void
    {
        $this->json('POST', '/login', [
            'email'    => 'inconnu@example.com',
            'password' => 'nimportequoi',
        ]);

        $this->assertResponseStatusCodeSame(401);
    }
}
