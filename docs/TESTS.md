# Guide des tests — Exercice 04

## Prérequis

```bash
composer require --dev symfony/test-pack
```

---

## Comment lancer les tests

```bash
php bin/phpunit                          # tous les tests
php bin/phpunit tests/Controller/        # un dossier
php bin/phpunit --filter testLogin       # un test précis
```

---

## Structure des fichiers

```
tests/
├── ApiTestCase.php                  ← classe de base partagée
└── Controller/
    ├── ProductControllerTest.php    ← tests des routes /products
    └── UserControllerTest.php       ← tests des routes /user et /login
```

---

## Point 1 — La classe de base `ApiTestCase`

Tous les tests héritent de cette classe. Elle gère trois choses :

**a) Démarrer le client HTTP**

```php
$this->client = static::createClient();
```

`createClient()` simule un navigateur qui fait des requêtes HTTP à l'application,
sans avoir besoin d'un vrai serveur web.

**b) Repartir d'une base de données vide avant chaque test**

```php
$tool = new SchemaTool($this->em);
$tool->dropSchema($classes);   // efface toutes les tables
$tool->createSchema($classes); // les recrée vides
```

Cela garantit que chaque test est **indépendant** : les données du test précédent
ne polluent pas le suivant.

**c) Des helpers pour envoyer des requêtes**

```php
// Requête JSON sans token
$this->json('POST', '/user', ['email' => '...', 'password' => '...']);

// Requête JSON avec le token JWT dans le header Authorization
$this->authJson('GET', '/products', $token);

// Lire la réponse JSON
$data = $this->responseData();
```

---

## Point 2 — Tester une route simple (`GET /products`)

```php
public function testGetAllProductsReturns200(): void
{
    $token = $this->getToken();                      // 1. s'authentifier
    $this->authJson('GET', '/products', $token);     // 2. faire la requête

    $this->assertResponseStatusCodeSame(200);        // 3. vérifier le résultat
}
```

Les **3 étapes** d'un test API :
1. Préparer (créer les données, obtenir un token si besoin)
2. Appeler la route
3. Vérifier le statut HTTP ET le contenu de la réponse

---

## Point 3 — Tester le contenu de la réponse

```php
public function testGetAllProductsReturnsAnArrayOf5(): void
{
    $token = $this->getToken();
    $this->authJson('GET', '/products', $token);

    $this->assertIsArray($this->responseData());   // la réponse est un tableau
    $this->assertCount(5, $this->responseData());  // le tableau contient 5 éléments
}
```

Assertions utiles pour les API JSON :

| Assertion                          | Ce qu'elle vérifie                      |
|------------------------------------|------------------------------------------|
| `assertResponseStatusCodeSame(200)`| Le code HTTP retourné                    |
| `assertIsArray($data)`             | La réponse est bien un tableau JSON      |
| `assertCount(5, $data)`            | Le tableau contient exactement 5 éléments|
| `assertArrayHasKey('id', $data)`   | La clé `id` existe dans le tableau       |
| `assertSame('Laptop', $data['name'])`| La valeur est exactement celle attendue |
| `assertNotEmpty($data['token'])`   | La valeur n'est pas vide                 |

---

## Point 4 — Tester un cas d'erreur (`404`)

```php
public function testGetUnknownProductReturns404(): void
{
    $token = $this->getToken();
    $this->authJson('GET', '/products/999', $token); // ID inexistant

    $this->assertResponseStatusCodeSame(404);
    $this->assertSame('Product not found', $this->responseData()['message']);
}
```

> Tester les erreurs est aussi important que tester le cas nominal.
> Si l'erreur est mal gérée côté contrôleur, le test échouera ici.

---

## Point 5 — Tester l'inscription et la connexion

Ces routes modifient la base de données. Chaque test repart d'une DB vide
(voir Point 1b), donc il faut créer l'utilisateur dans le test lui-même.

```php
public function testLoginWithValidCredentialsReturnsToken(): void
{
    // Étape 1 : créer l'utilisateur
    $this->json('POST', '/user', [
        'email'    => 'test@example.com',
        'password' => 'motdepasse123',
    ]);

    // Étape 2 : se connecter avec les mêmes identifiants
    $this->json('POST', '/login', [
        'email'    => 'test@example.com',
        'password' => 'motdepasse123',
    ]);

    // Étape 3 : vérifier qu'on a bien un token JWT
    $this->assertResponseStatusCodeSame(200);
    $this->assertArrayHasKey('token', $this->responseData());
    $this->assertNotEmpty($this->responseData()['token']);
}
```

---

## Point 6 — Tester l'authentification JWT

La méthode `getToken()` de `ApiTestCase` encapsule les étapes 1 et 2
du Point 5. Elle crée un utilisateur et retourne son token JWT.

```php
// Dans ProductControllerTest, chaque test l'utilise pour s'authentifier :
$token = $this->getToken();
$this->authJson('GET', '/products', $token);
```

Le header envoyé automatiquement :
```
Authorization: Bearer eyJ0eXAiOiJKV1QiLCJhb...
```

---

## Point 7 — Nommage des tests

Chaque nom de méthode suit le format :

```
test + [Ce qu'on teste] + [Comportement attendu]
```

Exemples :
- `testGetAllProductsReturns200`
- `testLoginWithWrongPasswordReturns401`
- `testRegisterUserReturnsSuccessMessage`

Ce format permet de lire le résultat comme une phrase :
```
✓ testGetAllProductsReturns200
✓ testLoginWithValidCredentialsReturnsToken
✗ testGetUnknownProductReturns404  ← on sait exactement ce qui a échoué
```

---

## Résultat attendu

```
PHPUnit 13.1.10

............                12 / 12 (100%)

OK (12 tests, 43 assertions)
```
