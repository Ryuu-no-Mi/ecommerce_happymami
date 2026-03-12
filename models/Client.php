<?php

require_once __DIR__ . '/../config/Database.php';

class Client {
    private $db;

    public function __construct() {
        $this->db = (new Database())->connect();
    }

    public function getAll(): array {
        $stmt = $this->db->query('SELECT * FROM clients');
        return $stmt->fetchAll();
    }

    public function getById(int $id) {
        $stmt = $this->db->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    public function create(string $name, string $email, string $phone, string $address): int {
        $sql  = 'INSERT INTO clients (name, email, phone, address) VALUES (:name, :email, :phone, :address)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':name'    => $name,
            ':email'   => $email,
            ':phone'   => $phone,
            ':address' => $address,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
