<?php

require_once __DIR__ . '/../config/Database.php';

class OrderDetail {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function create(int $idPedido, int $idProducto, int $cantidad, float $precio): void {
        $sql = 'INSERT INTO detalle_pedidos (id_pedido, id_producto, cantidad, precio) VALUES (:id_pedido, :id_producto, :cantidad, :precio)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':id_pedido'   => $idPedido,
            ':id_producto' => $idProducto,
            ':cantidad'    => $cantidad,
            ':precio'      => $precio,
        ]);
    }

    public function getByOrderId(int $idPedido): array {
        $sql = 'SELECT * FROM detalle_pedidos WHERE id_pedido = :id_pedido';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id_pedido' => $idPedido]);
        return $stmt->fetchAll();
    }
}
