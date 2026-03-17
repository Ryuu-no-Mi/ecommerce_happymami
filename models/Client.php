<?php

class Client
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all clients
     * @return array
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM clients ORDER BY id DESC');
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching clients: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get client by ID
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Client ID must be greater than 0');
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM clients WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            return $stmt->fetch() ?: false;
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching client: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create a new client
     * @param string $name
     * @param string $email
     * @param string $phone
     * @param string $address
     * @return int
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function create(string $name, string $email, string $phone, string $address): int
    {
        // Validation
        if (empty($name) || empty($email) || empty($phone) || empty($address)) {
            throw new InvalidArgumentException('All fields (name, email, phone, address) are required');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        try {
            $sql  = 'INSERT INTO clients (name, email, phone, address) VALUES (:name, :email, :phone, :address)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name'    => $name,
                ':email'   => $email,
                ':phone'   => $phone,
                ':address' => $address,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Error creating client: ' . $e->getMessage(), 0, $e);
        }
    }
}
