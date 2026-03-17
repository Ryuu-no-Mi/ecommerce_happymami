<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/OrderDetail.php';

class OrderController extends BaseController
{
    private Order $model;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Order($db, new OrderDetail($db));
    }

    /**
     * Get all orders
     * @return void
     */
    public function getOrders(): void
    {
        try {
            $orders = $this->model->getAll();
            $this->json($orders, 200);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error fetching orders'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Get order by ID
     * @param int $id
     * @return void
     */
    public function getOrder(int $id): void
    {
        try {
            $order = $this->model->getById($id);

            if ($order === false) {
                $this->json(['error' => 'Order not found'], 404);
                return;
            }

            $this->json($order, 200);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error fetching order'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Create a new order
     * @return void
     */
    public function createOrder(): void
    {
        try {
            $body = $this->getJsonInput();

            $clientId = (int) ($body['client_id'] ?? 0);
            $notes = trim($body['notes'] ?? '');
            $items = $body['items'] ?? [];

            if ($clientId <= 0 || !is_array($items) || count($items) === 0) {
                $this->json(['error' => 'client_id and items array are required'], 422);
                return;
            }

            // Create order
            $id = $this->model->create($clientId, $notes, $items);
            $created = $this->model->getById($id);
            $this->json($created, 201);
        } catch (InvalidArgumentException $e) {
            // Business logic validation error
            $this->json(['error' => $e->getMessage()], 422);
        } catch (DomainException $e) {
            // Domain validation error (client not found, insufficient stock, etc.)
            $this->json(['error' => $e->getMessage()], 409);
        } catch (RuntimeException $e) {
            // System error (database error, etc.)
            $this->json(['error' => 'Error processing order'], 500);
        } catch (Exception $e) {
            // Unexpected error
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }
}
