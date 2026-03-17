<?php

class Order
{
    private PDO $db;
    private OrderDetail $orderDetail;

    public function __construct(PDO $db, ?OrderDetail $orderDetail = null)
    {
        $this->db = $db;
        $this->orderDetail = $orderDetail ?? new OrderDetail($db);
    }

    /**
     * Get all orders
     * @return array
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM orders ORDER BY id DESC');
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching orders: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get order by ID with items
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Order ID must be greater than 0');
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM orders WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);

            $order = $stmt->fetch();

            if (!$order) {
                return false;
            }

            $order['items'] = $this->orderDetail->getByOrderId($id);
            return $order;
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching order: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create order with items and stock deduction
     * Uses transactions to ensure atomic operations
     * @param int $clientId
     * @param string $notes
     * @param array $items
     * @return int Order ID
     * @throws InvalidArgumentException For business logic errors
     * @throws DomainException For domain validation errors
     * @throws RuntimeException For system errors
     */
    public function create(int $clientId, string $notes, array $items): int
    {
        // Validation
        if ($clientId <= 0) {
            throw new InvalidArgumentException('Client ID must be greater than 0');
        }

        if (count($items) === 0) {
            throw new InvalidArgumentException('The order must contain at least one item.');
        }

        $this->db->beginTransaction();

        try {
            // 1. Verify that client exists
            $stmtClient = $this->db->prepare('SELECT id FROM clients WHERE id = :id LIMIT 1');
            $stmtClient->execute([':id' => $clientId]);
            
            if (!$stmtClient->fetch()) {
                throw new DomainException("Client not found: {$clientId}");
            }

            // 2. Create order
            $sqlOrder = 'INSERT INTO orders (client_id, notes) VALUES (:client_id, :notes)';
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':client_id' => $clientId,
                ':notes'     => $notes,
            ]);

            $orderId = (int) $this->db->lastInsertId();

            // 3. Process items and validate stock
            $sqlPrice = 'SELECT id, price, stock, name FROM products WHERE id = :product_id LIMIT 1 FOR UPDATE';
            $stmtPrice = $this->db->prepare($sqlPrice);

            $sqlDetail = 'INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES (:order_id, :product_id, :quantity, :purchase_price)';
            $stmtDetail = $this->db->prepare($sqlDetail);

            $sqlUpdateStock = 'UPDATE products SET stock = stock - :qty WHERE id = :id';
            $stmtUpdateStock = $this->db->prepare($sqlUpdateStock);

            foreach ($items as $item) {
                $productId = (int) ($item['product_id'] ?? 0);
                $quantity = (int) ($item['quantity'] ?? 0);

                if ($productId <= 0 || $quantity <= 0) {
                    throw new InvalidArgumentException('Each item must include a valid product_id and quantity.');
                }

                // Fetch product with lock
                $stmtPrice->execute([':product_id' => $productId]);
                $product = $stmtPrice->fetch();

                if ($product === false) {
                    throw new DomainException("Product not found: {$productId}");
                }

                // Validate stock availability
                if ($product['stock'] < $quantity) {
                    throw new DomainException(
                        "Insufficient stock for the product: {$product['name']} (Available: {$product['stock']}, Requested: {$quantity})"
                    );
                }

                // Insert order item with current price
                $currentPrice = (float) $product['price'];
                $stmtDetail->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $productId,
                    ':quantity' => $quantity,
                    ':purchase_price' => $currentPrice,
                ]);

                // Deduct stock from product
                $stmtUpdateStock->execute([
                    ':qty' => $quantity,
                    ':id' => $productId
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (PDOException $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw new RuntimeException('Database error during order creation: ' . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
