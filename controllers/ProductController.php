<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController extends BaseController
{
    private Product $model;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Product($db);
    }

    public function getProducts(): void
    {
        $products = $this->model->getAll();
        $this->json($products, 200);
    }

    public function getProduct(int $id): void
    {
        $product = $this->model->getById($id);
        if ($product === false) {
            $this->json(['error' => 'Product not found'], 404);
            return;
        }
        $this->json($product, 200);
    }

    public function createProduct(): void
    {
        $body = $this->getJsonInput();

        $name = trim($body['name'] ?? '');
        $description = trim($body['description'] ?? '');
        $price = $body['price'] ?? null;
        $stock = $body['stock'] ?? null;

        if ($name === '' || $price === null || $stock === null) {
            $this->json(['error' => 'name, price and stock are required'], 422);
            return;
        }

        if (!is_numeric($price) || $price < 0) {
            $this->json(['error' => 'price must be a positive number'], 422);
            return;
        }

        if (!is_numeric($stock) || $stock < 0) {
            $this->json(['error' => 'stock must be a positive integer'], 422);
            return;
        }

        $id = $this->model->create($name, $description, (float) $price, (int) $stock);
        $this->json(['id' => $id, 'message' => 'Product created'], 201);
    }

    public function updateProduct(int $id): void
    {
        $existing = $this->model->getById($id);
        if ($existing === false) {
            $this->json(['error' => 'Product not found'], 404);
            return;
        }

        $body = $this->getJsonInput();

        $name = trim($body['name'] ?? '');
        $description = trim($body['description'] ?? '');
        $price = $body['price'] ?? null;
        $stock = $body['stock'] ?? null;

        if ($name === '' || $price === null || $stock === null) {
            $this->json(['error' => 'name, price and stock are required'], 422);
            return;
        }

        if (!is_numeric($price) || $price < 0) {
            $this->json(['error' => 'price must be a positive number'], 422);
            return;
        }

        if (!is_numeric($stock) || $stock < 0) {
            $this->json(['error' => 'stock must be a positive integer'], 422);
            return;
        }

        $updated = $this->model->update($id, $name, $description, (float) $price, (int) $stock);
        $this->json(['updated' => $updated, 'message' => 'Product updated'], 200);
    }
}
