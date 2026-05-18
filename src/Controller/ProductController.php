<?php

namespace App\Controller;

use OpenApi\Attributes as OA;
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
    #[OA\Get(
        path: '/products',
        summary: 'Liste tous les produits',
        responses: [
            new OA\Response(
                response: 200,
                description: 'Liste des produits',
                content: new OA\JsonContent(
                    type: 'array',
                    items: new OA\Items(
                        properties: [
                            new OA\Property(property: 'id', type: 'integer', example: 1),
                            new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 15'),
                            new OA\Property(property: 'price', type: 'number', example: 1299.99),
                            new OA\Property(property: 'category', type: 'string', example: 'Electronics'),
                            new OA\Property(property: 'stock', type: 'integer', example: 42),
                        ]
                    )
                )
            ),
        ]
    )]
    #[OA\Tag(name: 'Produits')]
    public function index(): Response
    {
        return $this->json($this->products);
    }

    #[Route('/products/{id}', name: 'app_product_show', methods: ['GET'])]
    #[OA\Get(
        path: '/products/{id}',
        summary: 'Détails d\'un produit',
        parameters: [
            new OA\Parameter(
                name: 'id',
                in: 'path',
                required: true,
                description: 'Identifiant du produit',
                schema: new OA\Schema(type: 'integer', example: 1)
            ),
        ],
        responses: [
            new OA\Response(
                response: 200,
                description: 'Produit trouvé',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(property: 'id', type: 'integer', example: 1),
                        new OA\Property(property: 'name', type: 'string', example: 'Laptop Pro 15'),
                        new OA\Property(property: 'price', type: 'number', example: 1299.99),
                        new OA\Property(property: 'category', type: 'string', example: 'Electronics'),
                        new OA\Property(property: 'stock', type: 'integer', example: 42),
                    ]
                )
            ),
            new OA\Response(response: 404, description: 'Produit non trouvé'),
        ]
    )]
    #[OA\Tag(name: 'Produits')]
    public function show(int $id): Response
    {
        $product = array_values(array_filter($this->products, fn($p) => $p['id'] === $id))[0] ?? null;

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product);
    }
}
