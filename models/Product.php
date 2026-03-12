<?php

require_once __DIR__ . '/../config/Database.php';

class Product {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM products');
        return $stmt->fetchAll();
    }

    public function getById(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(string $nombre, string $descripcion, float $precio, int $stock): int {
        $sql  = 'INSERT INTO products (nombre, descripcion, precio, stock) VALUES (:nombre, :descripcion, :precio, :stock)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':precio'      => $precio,
            ':stock'       => $stock,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $nombre, string $descripcion, float $precio, int $stock): bool {
        $sql  = 'UPDATE products SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre'      => $nombre,
            ':descripcion' => $descripcion,
            ':precio'      => $precio,
            ':stock'       => $stock,
            ':id'          => $id,
        ]);
        return $stmt->rowCount() > 0;
    }
}
