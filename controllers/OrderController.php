<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';

class OrderController extends BaseController
{
    private Order $model;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Order($db);
    }

    public function getOrders(): void
    {
        $orders = $this->model->getAll();
        $this->json($orders, 200);
    }

    public function getOrder(int $id): void
    {
        $order = $this->model->getById($id);

        if ($order === false) {
            $this->json(['error' => 'Order not found'], 404);
            return;
        }

        $this->json($order, 200);
    }

    public function createOrder(): void
    {
        $body = $this->getJsonInput();

        $clientId = (int) ($body['client_id'] ?? 0);
        $notes = trim($body['notes'] ?? '');
        $items = $body['items'] ?? [];

        if ($clientId <= 0 || !is_array($items) || count($items) === 0) {
            $this->json(['error' => 'client_id and items are required'], 422);
            return;
        }

        try {
            $id = $this->model->create($clientId, $notes, $items);
            $created = $this->model->getById($id);
            $this->json($created, 201);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => $e->getMessage()], 404);
        } catch (Exception $e) {
            $this->json(['error' => 'Could not create order'], 422);
        }
    }
}
