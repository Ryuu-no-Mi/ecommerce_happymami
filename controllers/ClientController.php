<?php

require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/Client.php';

class ClientController extends BaseController {
    private $model;

    public function __construct() {
        $this->model = new Client();
    }

    public function getClients(): void {
        $clients = $this->model->getAll();
        $this->json($clients, 200);
    }

    public function getClient(int $id): void {
        $client = $this->model->getById($id);
        if ($client === false) {
            $this->json(['error' => 'Cliente no encontrado'], 404);
            return;
        }
        $this->json($client, 200);
    }

    public function createClient(): void {
        $body = $this->getJsonInput();

        $nombre    = trim($body['nombre']    ?? '');
        $email     = trim($body['email']     ?? '');
        $telefono  = trim($body['telefono']  ?? '');
        $direccion = trim($body['direccion'] ?? '');

        if ($nombre === '' || $email === '') {
            $this->json(['error' => 'nombre y email son obligatorios'], 422);
            return;
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->json(['error' => 'email no válido'], 422);
            return;
        }

        $id = $this->model->create($nombre, $email, $telefono, $direccion);
        $this->json(['id' => $id, 'message' => 'Cliente creado'], 201);
    }

}
