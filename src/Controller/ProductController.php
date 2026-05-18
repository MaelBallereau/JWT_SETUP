<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ProductController extends AbstractController
{
    private array $products = [
        ['id' => 1, 'name' => 'Laptop Pro 15', 'price' => 1299.99, 'category' => 'Electronics', 'stock' => 42],
        ['id' => 2, 'name' => 'Mechanical Keyboard', 'price' => 89.90, 'category' => 'Electronics', 'stock' => 150],
        ['id' => 3, 'name' => 'Ergonomic Chair', 'price' => 349.00, 'category' => 'Furniture', 'stock' => 18],
        ['id' => 4, 'name' => 'USB-C Hub 7-in-1', 'price' => 45.50, 'category' => 'Electronics', 'stock' => 200],
        ['id' => 5, 'name' => 'Standing Desk', 'price' => 599.99, 'category' => 'Furniture', 'stock' => 10],
    ];

    #[Route('/products', name: 'app_products', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json($this->products);
    }

    #[Route('/products/{id}', name: 'app_product_show', methods: ['GET'])]
    public function show(int $id): Response
    {
        $product = array_values(array_filter($this->products, fn($p) => $p['id'] === $id))[0] ?? null;

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }
}
