<?php

/**
 * BaseController - Base class for all controllers
 * Provides JSON response handling and HTTP utilities
 */
class BaseController
{
    protected PDO $db;

    /**
     * Constructor
     * @param PDO $db Database connection instance
     */
    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    /**
     * Send JSON response with status code
     * @param mixed $data
     * @param int $status HTTP status code
     * @return void
     */
    protected function json($data, int $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * Get JSON input from request body
     * @return array
     */
    protected function getJsonInput(): array
    {
        $body = json_decode(file_get_contents('php://input'), true);
        return is_array($body) ? $body : [];
    }
}
