<?php

require_once __DIR__ . '/../config/Database.php';

class OrderDetail {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create(int $orderId, int $productId, int $quantity, float $purchasePrice): void {
        $sql = 'INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES (:order_id, :product_id, :quantity, :purchase_price)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':order_id'      => $orderId,
            ':product_id'    => $productId,
            ':quantity'      => $quantity,
            ':purchase_price' => $purchasePrice,
        ]);
    }

    public function getByOrderId(int $orderId): array {
        $sql = 'SELECT * FROM order_items WHERE order_id = :order_id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':order_id' => $orderId]);
        return $stmt->fetchAll();
    }
}
