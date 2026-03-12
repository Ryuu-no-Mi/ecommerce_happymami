<?php

require_once __DIR__ . '/../controller/ClientController.php';
require_once __DIR__ . '/../controller/ProductController.php';
require_once __DIR__ . '/../controller/OrderController.php';

header('Content-Type: application/json; charset=UTF-8');

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// -- Clientes
$clients = new ClientController();

if ($method === 'GET' && preg_match('#^/clients$#', $uri)) {
    $clients->getClients();

} elseif ($method === 'GET' && preg_match('#^/clients/(\d+)$#', $uri, $m)) {
    $clients->getClient((int) $m[1]);

} elseif ($method === 'POST' && preg_match('#^/clients$#', $uri)) {
    $clients->createClient();

// -- Productos
} elseif ($method === 'GET' && preg_match('#^/products$#', $uri)) {
    (new ProductController())->getProducts();

} elseif ($method === 'GET' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
    (new ProductController())->getProduct((int) $m[1]);

} elseif ($method === 'POST' && preg_match('#^/products$#', $uri)) {
    (new ProductController())->createProduct();

} elseif ($method === 'PUT' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
    (new ProductController())->updateProduct((int) $m[1]);

// -- Pedidos
} elseif ($method === 'GET' && preg_match('#^/orders$#', $uri)) {
    (new OrderController())->getOrders();

} elseif ($method === 'GET' && preg_match('#^/orders/(\d+)$#', $uri, $m)) {
    (new OrderController())->getOrder((int) $m[1]);

} elseif ($method === 'POST' && preg_match('#^/orders$#', $uri)) {
    (new OrderController())->createOrder();

} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada'], JSON_UNESCAPED_UNICODE);
}
