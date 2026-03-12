<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Product.php';

class ProductController extends BaseController {
    private $model;

    public function __construct() {
        $this->model = new Product();
    }

    public function getProducts(): void {
        $products = $this->model->getAll();
        $this->json($products, 200);
    }

    public function getProduct(int $id): void {
        $product = $this->model->getById($id);
        if ($product === false) {
            $this->json(['error' => 'Producto no encontrado'], 404);
            return;
        }
        $this->json($product, 200);
    }

    public function createProduct(): void {
        $body = $this->getJsonInput();

        $nombre      = trim($body['nombre']      ?? '');
        $descripcion = trim($body['descripcion'] ?? '');
        $precio      = $body['precio']           ?? null;
        $stock       = $body['stock']            ?? null;

        if ($nombre === '' || $precio === null || $stock === null) {
            $this->json(['error' => 'nombre, precio y stock son obligatorios'], 422);
            return;
        }

        if (!is_numeric($precio) || $precio < 0) {
            $this->json(['error' => 'precio debe ser un número positivo'], 422);
            return;
        }

        if (!is_numeric($stock) || $stock < 0) {
            $this->json(['error' => 'stock debe ser un número entero positivo'], 422);
            return;
        }

        $id = $this->model->create($nombre, $descripcion, (float) $precio, (int) $stock);
        $this->json(['id' => $id, 'message' => 'Producto creado'], 201);
    }

    public function updateProduct(int $id): void {
        $existing = $this->model->getById($id);
        if ($existing === false) {
            $this->json(['error' => 'Producto no encontrado'], 404);
            return;
        }

        $body = $this->getJsonInput();

        $nombre      = trim($body['nombre']      ?? '');
        $descripcion = trim($body['descripcion'] ?? '');
        $precio      = $body['precio']           ?? null;
        $stock       = $body['stock']            ?? null;

        if ($nombre === '' || $precio === null || $stock === null) {
            $this->json(['error' => 'nombre, precio y stock son obligatorios'], 422);
            return;
        }

        if (!is_numeric($precio) || $precio < 0) {
            $this->json(['error' => 'precio debe ser un número positivo'], 422);
            return;
        }

        if (!is_numeric($stock) || $stock < 0) {
            $this->json(['error' => 'stock debe ser un número entero positivo'], 422);
            return;
        }

        $updated = $this->model->update($id, $nombre, $descripcion, (float) $precio, (int) $stock);
        $this->json(['updated' => $updated, 'message' => 'Producto actualizado'], 200);
    }

}
