<?php

class OrderDetail
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Create order detail (Internal method - used within transaction)
     * @param int $orderId
     * @param int $productId
     * @param int $quantity
     * @param float $purchasePrice
     * @return void
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function create(int $orderId, int $productId, int $quantity, float $purchasePrice): void
    {
        // Validation
        if ($orderId <= 0 || $productId <= 0 || $quantity <= 0 || $purchasePrice < 0) {
            throw new InvalidArgumentException('Invalid order detail parameters');
        }

        try {
            $sql = 'INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES (:order_id, :product_id, :quantity, :purchase_price)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':order_id'      => $orderId,
                ':product_id'    => $productId,
                ':quantity'      => $quantity,
                ':purchase_price' => $purchasePrice,
            ]);
        } catch (PDOException $e) {
            throw new RuntimeException('Error creating order detail: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get order items by order ID
     * @param int $orderId
     * @return array
     */
    public function getByOrderId(int $orderId): array
    {
        if ($orderId <= 0) {
            throw new InvalidArgumentException('Order ID must be greater than 0');
        }

        try {
            $sql = 'SELECT * FROM order_items WHERE order_id = :order_id';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([':order_id' => $orderId]);
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching order items: ' . $e->getMessage(), 0, $e);
        }
    }
}
