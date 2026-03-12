<?php

class Database {
    private string $host     = '127.0.0.1';
    private string $db       = 'ecomerce_happymami';
    private string $port    = '3306';
    private string $user     = 'root';
    private string $password = '';
    private string $charset  = 'utf8mb4';
 
    private ?PDO $conn = null;

    public function connect(): PDO {
        if ($this->conn === null) {
            $dsn = "mysql:host={$this->host};port={$this->port};dbname={$this->db};charset={$this->charset}";
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
