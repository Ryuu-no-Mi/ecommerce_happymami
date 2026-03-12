<?php

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/OrderDetail.php';

class Order {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM pedidos ORDER BY id DESC');
        return $stmt->fetchAll();
    }

    public function getById(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM pedidos WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $order = $stmt->fetch();

        if ($order === false) {
            return false;
        }

        $detailModel = new OrderDetail();
        $order['productos'] = $detailModel->getByOrderId($id);
        return $order;
    }

    public function create(int $client_id, string $notas, array $productos): int {
        if (count($productos) === 0) {
            throw new InvalidArgumentException('El pedido debe tener al menos un producto.');
        }

        $this->db->beginTransaction();

        try {
            $sqlOrder = 'INSERT INTO pedidos (client_id, notas) VALUES (:client_id, :notas)';
            $stmtOrder = $this->db->prepare($sqlOrder);
            $stmtOrder->execute([
                ':client_id' => $client_id,
                ':notas'     => $notas,
            ]);

            $idPedido = (int) $this->db->lastInsertId();

            // Guardamos el precio actual del producto en el detalle del pedido.
            $sqlPrice = 'SELECT precio FROM products WHERE id = :id_producto LIMIT 1 FOR UPDATE';
            $stmtPrice = $this->db->prepare($sqlPrice);

            $sqlDetail = 'INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio) VALUES (:id_pedido, :id_producto, :cantidad, :precio)';
            $stmtDetail = $this->db->prepare($sqlDetail);

            foreach ($productos as $item) {
                $idProducto = (int) ($item['id_producto'] ?? 0);
                $cantidad = (int) ($item['cantidad'] ?? 0);

                if ($idProducto <= 0 || $cantidad <= 0) {
                    throw new InvalidArgumentException('Cada item debe incluir id_producto y cantidad válidos.');
                }

                $stmtPrice->execute([':id_producto' => $idProducto]);
                $product = $stmtPrice->fetch();

                if ($product === false) {
                    throw new RuntimeException("Producto no encontrado: {$idProducto}");
                }

                $precioActual = (float) $product['precio'];
                $stmtDetail->execute([
                    ':id_pedido' => $idPedido,
                    ':id_producto' => $idProducto,
                    ':cantidad' => $cantidad,
                    ':precio' => $precioActual,
                ]);
            }

            $this->db->commit();
            return $idPedido;

        } catch (Exception $e) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $e;
        }
    }
}
