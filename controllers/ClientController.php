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

    /**
     * Get all clients
     * @return void
     */
    public function getClients(): void
    {
        try {
            $clients = $this->model->getAll();
            $this->json($clients, 200);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error fetching clients'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Get client by ID
     * @param int $id
     * @return void
     */
    public function getClient(int $id): void
    {
        try {
            $client = $this->model->getById($id);
            if ($client === false) {
                $this->json(['error' => 'Client not found'], 404);
                return;
            }
            $this->json($client, 200);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error fetching client'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }

    /**
     * Create a new client
     * @return void
     */
    public function createClient(): void
    {
        try {
            $body = $this->getJsonInput();

            $name = trim($body['name'] ?? '');
            $email = trim($body['email'] ?? '');
            $phone = trim($body['phone'] ?? '');
            $address = trim($body['address'] ?? '');

            if ($name === '' || $email === '' || $phone === '' || $address === '') {
                $this->json(['error' => 'name, email, phone and address are required'], 422);
                return;
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $this->json(['error' => 'Invalid email format'], 422);
                return;
            }

            $id = $this->model->create($name, $email, $phone, $address);
            $this->json(['id' => $id, 'message' => 'Client created successfully'], 201);
        } catch (InvalidArgumentException $e) {
            $this->json(['error' => $e->getMessage()], 422);
        } catch (RuntimeException $e) {
            $this->json(['error' => 'Error creating client'], 500);
        } catch (Exception $e) {
            $this->json(['error' => 'Unexpected error'], 500);
        }
    }
}
