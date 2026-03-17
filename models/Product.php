<?php

class Product
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Get all products
     * @return array
     */
    public function getAll(): array
    {
        try {
            $stmt = $this->db->query('SELECT * FROM products ORDER BY id DESC');
            return $stmt->fetchAll() ?: [];
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching products: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Get product by ID
     * @param int $id
     * @return array|false
     */
    public function getById(int $id): array|false
    {
        if ($id <= 0) {
            throw new InvalidArgumentException('Product ID must be greater than 0');
        }

        try {
            $stmt = $this->db->prepare('SELECT * FROM products WHERE id = :id LIMIT 1');
            $stmt->execute([':id' => $id]);
            return $stmt->fetch() ?: false;
        } catch (PDOException $e) {
            throw new RuntimeException('Error fetching product: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Create a new product
     * @param string $name
     * @param string $description
     * @param float $price
     * @param int $stock
     * @return int
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function create(string $name, string $description, float $price, int $stock): int
    {
        // Validation
        if (empty($name) || empty($description)) {
            throw new InvalidArgumentException('Product name and description are required');
        }

        if ($price < 0 || $stock < 0) {
            throw new InvalidArgumentException('Price and stock cannot be negative');
        }

        try {
            $sql  = 'INSERT INTO products (name, description, price, stock) VALUES (:name, :description, :price, :stock)';
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':name'        => $name,
                ':description' => $description,
                ':price'       => $price,
                ':stock'       => $stock,
            ]);
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new RuntimeException('Error creating product: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Update product
     * @param int $id
     * @param string $name
     * @param string $description
     * @param float $price
     * @param int $stock
     * @return bool
     * @throws InvalidArgumentException
     * @throws RuntimeException
     */
    public function update(int $id, string $name, string $description, float $price, int $stock): bool
    {
        // Validation
        if ($id <= 0) {
            throw new InvalidArgumentException('Product ID must be greater than 0');
        }

        if (empty($name) || empty($description)) {
            throw new InvalidArgumentException('Product name and description are required');
        }

        if ($price < 0 || $stock < 0) {
            throw new InvalidArgumentException('Price and stock cannot be negative');
        }

        try {
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
        } catch (PDOException $e) {
            throw new RuntimeException('Error updating product: ' . $e->getMessage(), 0, $e);
        }
    }
}
