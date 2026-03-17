<?php

class BaseController
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    protected function json($data, $status = 200): void
    {
        http_response_code($status);
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function getJsonInput(): array
    {
        $body = json_decode(file_get_contents('php://input'), true);
        return is_array($body) ? $body : [];
    }
}
