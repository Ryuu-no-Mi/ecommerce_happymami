<?php

require_once __DIR__ . '/../models/Order.php';

class OrderController {
    private Order $model;

    public function __construct() {
        $this->model = new Order();
    }

    public function getOrders(): void {
        $orders = $this->model->getAll();
        $this->json($orders, 200);
    }

    public function getOrder(int $id): void {
        $order = $this->model->getById($id);

        if ($order === false) {
            $this->json(['error' => 'Pedido no encontrado'], 404);
            return;
        }

        $this->json($order, 200);
    }

    public function createOrder(): void {
        $body = json_decode(file_get_contents('php://input'), true);

        $clientId = (int) ($body['client_id'] ?? 0);
        $notas = trim($body['notas'] ?? '');
        $productos = $body['productos'] ?? [];

        if ($clientId <= 0 || !is_array($productos) || count($productos) === 0) {
            $this->json(['error' => 'client_id y productos son obligatorios'], 422);
            return;
        }

        try {
            $id = $this->model->create($clientId, $notas, $productos);
            $created = $this->model->getById($id);
            $this->json($created, 201);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => $e->getMessage()], 404);
        } catch (Throwable $e) {
            $this->json(['error' => 'No se pudo crear el pedido'], 500);
        }
    }

    private function json(mixed $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
