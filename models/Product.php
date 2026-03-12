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

    public function create(string $name, string $description, float $price, int $stock): int {
        $sql  = 'INSERT INTO products (name, description, price, stock) VALUES (:name, :description, :price, :stock)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'        => $name,
            ':description' => $description,
            ':price'       => $price,
            ':stock'       => $stock,
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $description, float $price, int $stock): bool {
        $sql  = 'UPDATE products SET name = :name, description = :description, price = :price, stock = :stock WHERE id = :id';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'        => $name,
            ':description' => $description,
            ':price'       => $price,
            ':stock'       => $stock,
            ':id'          => $id,
        ]);
        return $stmt->rowCount() > 0;
    }
}
