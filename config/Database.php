<?php

class Database {
    private string $host     = 'localhost';
    private string $db       = 'happymami';
    private string $user     = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';

    private ?PDO $conn = null;

    public function connect(): PDO {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};dbname={$this->db};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $this->conn = new PDO($dsn, $this->user, $this->password, $options);
        }
        return $this->conn;
    }
}
