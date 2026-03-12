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

    public function create(string $nombre, string $email, string $telefono, string $direccion): int {
        $sql  = 'INSERT INTO clients (nombre, email, telefono, direccion) VALUES (:nombre, :email, :telefono, :direccion)';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':nombre'    => $nombre,
            ':email'     => $email,
            ':telefono'  => $telefono,
            ':direccion' => $direccion,
        ]);
        return (int) $this->db->lastInsertId();
    }
}
