<?php

namespace App\Tests\Controller;

use App\Tests\ApiTestCase;

class ProductControllerTest extends ApiTestCase
{
    // ------------------------------------------------------------------
    // GET /products — liste tous les produits
    // ------------------------------------------------------------------

    public function testGetAllProductsReturns200(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products', $token);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetAllProductsReturnsAnArrayOf5(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products', $token);

        $this->assertIsArray($this->responseData());
        $this->assertCount(5, $this->responseData());
    }

    public function testEachProductHasRequiredFields(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products', $token);

        foreach ($this->responseData() as $product) {
            $this->assertArrayHasKey('id', $product);
            $this->assertArrayHasKey('name', $product);
            $this->assertArrayHasKey('price', $product);
            $this->assertArrayHasKey('category', $product);
            $this->assertArrayHasKey('stock', $product);
        }
    }

    // ------------------------------------------------------------------
    // GET /products/{id} — un produit précis
    // ------------------------------------------------------------------

    public function testGetExistingProductReturns200(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products/1', $token);

        $this->assertResponseStatusCodeSame(200);
    }

    public function testGetExistingProductReturnsCorrectData(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products/1', $token);

        $product = $this->responseData();
        $this->assertSame(1, $product['id']);
        $this->assertSame('Laptop Pro 15', $product['name']);
        $this->assertSame(1299.99, $product['price']);
    }

    public function testGetUnknownProductReturns404(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products/999', $token);

        $this->assertResponseStatusCodeSame(404);
    }

    public function testGetUnknownProductReturnsErrorMessage(): void
    {
        $token = $this->getToken();
        $this->authJson('GET', '/products/999', $token);

        $this->assertSame('Product not found', $this->responseData()['message']);
    }
}
