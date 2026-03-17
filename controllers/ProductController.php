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

    /**
     * Get all products
     * @return void
     */
    public function getProducts(): void
    {
        try {
            $products = $this->model->getAll();
            $this->json($products, 200);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error fetching products'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Get product by ID
     * @param int $id
     * @return void
     */
    public function getProduct(int $id): void
    {
        try {
            $product = $this->model->getById($id);
            if ($product === false) {
                $this->json(['error' => 'Product not found'], 404);
                return;
            }
            $this->json($product, 200);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error fetching product'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Create a new product
     * @return void
     */
    public function createProduct(): void
    {
        try {
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
            $this->json(['id' => $id, 'message' => 'Product created successfully'], 201);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error creating product'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Update an existing product
     * @param int $id
     * @return void
     */
    public function updateProduct(int $id): void
    {
        try {
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
            $this->json(['updated' => $updated, 'message' => 'Product updated successfully'], 200);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error updating product'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }
}
