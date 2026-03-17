<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../controllers/ClientController.php';
require_once __DIR__ . '/../controllers/ProductController.php';
require_once __DIR__ . '/../controllers/OrderController.php';

header('Content-Type: application/json; charset=UTF-8');

$method = $_SERVER['REQUEST_METHOD'];
$uri    = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri    = rtrim($uri, '/');

// Initialize database connection (Single Connection)
$database = new Database();
$db = $database->connect();

$clients = new ClientController($db);
$products = new ProductController($db);
$orders = new OrderController($db);

// -- Clients
if ($method === 'GET' && preg_match('#^/clients$#', $uri)) {
    $clients->getClients();
} elseif ($method === 'GET' && preg_match('#^/clients/(\d+)$#', $uri, $m)) {
    $clients->getClient((int) $m[1]);
} elseif ($method === 'POST' && preg_match('#^/clients$#', $uri)) {
    $clients->createClient();

    // -- Products
} elseif ($method === 'GET' && preg_match('#^/products$#', $uri)) {
    $products->getProducts();
} elseif ($method === 'GET' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
    $products->getProduct((int) $m[1]);
} elseif ($method === 'POST' && preg_match('#^/products$#', $uri)) {
    $products->createProduct();
} elseif ($method === 'PUT' && preg_match('#^/products/(\d+)$#', $uri, $m)) {
    $products->updateProduct((int) $m[1]);

    // -- Orders
} elseif ($method === 'GET' && preg_match('#^/orders$#', $uri)) {
    $orders->getOrders();
} elseif ($method === 'GET' && preg_match('#^/orders/(\d+)$#', $uri, $m)) {
    $orders->getOrder((int) $m[1]);
} elseif ($method === 'POST' && preg_match('#^/orders$#', $uri)) {
    $orders->createOrder();
} else {
    http_response_code(404);
    echo json_encode(['error' => 'Ruta no encontrada'], JSON_UNESCAPED_UNICODE);
}
