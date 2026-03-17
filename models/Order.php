<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/OrderDetail.php';

class Order
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getAll(): array
    {
        $stmt = $this->db->query('SELECT * FROM orders ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function getById(int $id)
    {
        $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();

        if ($order === false) {
            return false;
        }

        $detailModel = new OrderDetail($this->db);
        $order['items'] = $detailModel->getByOrderId($id);
        return $order;
    }

    public function create(int $clientId, string $notes, array $items): int
    {
        if (count($items) === 0) {
            throw new InvalidArgumentException('The order must contain at least one item.');
        }

        $this->db->beginTransaction();

        try {
            $sqlOrder = 'INSERT INTO orders (client_id, notes) VALUES (:client_id, :notes)';
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':client_id' => $clientId,
                ':notes'     => $notes,
            ]);

            $orderId = (int) $this->db->lastInsertId();

            // Save the current product price in the order detail.
            $sqlPrice = 'SELECT price FROM products WHERE id = :product_id LIMIT 1 FOR UPDATE';
            $stmtPrice = $this->db->prepare($sqlPrice);

            $sqlDetail = 'INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES (:order_id, :product_id, :quantity, :purchase_price)';
            $stmtDetail = $this->db->prepare($sqlDetail);

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new InvalidArgumentException('Each item must include a valid product_id and quantity.');
                }

                $stmtPrice->execute([':product_id' => $productId]);
                $product = $stmtPrice->fetch();

                if ($product === false) {
                    throw new RuntimeException("Product not found: {$productId}");
                }

                $currentPrice = (float) $product['price'];
                $stmtDetail->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $productId,
                    ':quantity' => $quantity,
                    ':purchase_price' => $currentPrice,
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
