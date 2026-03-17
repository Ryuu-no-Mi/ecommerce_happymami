<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Client.php';

class ClientController extends BaseController
{
    private Client $model;

    public function __construct(PDO $db)
    {
        parent::__construct($db);
        $this->model = new Client($db);
    }

    public function getClients(): void
    {
        $clients = $this->model->getAll();
        $this->json($clients, 200);
    }

    public function getClient(int $id): void
    {
        $client = $this->model->getById($id);
        if ($client === false) {
            $this->json(['error' => 'Client not found'], 404);
            return;
        }
        $this->json($client, 200);
    }

    public function createClient(): void
    {
        $body = $this->getJsonInput();

        $name = trim($body['name'] ?? '');
        $email = trim($body['email'] ?? '');
        $phone = trim($body['phone'] ?? '');
        $address = trim($body['address'] ?? '');

        if ($name === '' || $email === '') {
            $this->json(['error' => 'name and email are required'], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['error' => 'invalid email'], 422);
            return;
        }

        $id = $this->model->create($name, $email, $phone, $address);
        $this->json(['id' => $id, 'message' => 'Client created'], 201);
    }
}
