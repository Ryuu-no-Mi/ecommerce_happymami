-- ============================================
-- Base de datos Happymami
-- ============================================

CREATE DATABASE IF NOT EXISTS ecomerce_happymami;
USE ecomerce_happymami;

-- ============================================
-- Tabla clientes
-- ============================================

CREATE TABLE clients (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    phone VARCHAR(20),
    address VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Tabla productos
-- ============================================

CREATE TABLE products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    description TEXT,
    price DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ============================================
-- Tabla pedidos
-- ============================================

CREATE TABLE orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) DEFAULT 'pending',
    notes TEXT,
    FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
);

-- ============================================
-- Tabla detalle de pedidos (items)
-- ============================================

CREATE TABLE order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    purchase_price DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id)
);

-- ============================================
-- Datos de prueba clientes
-- ============================================

INSERT INTO clients (name, email, phone, address) VALUES
('Ana García', 'ana@email.com', '600111222', 'Madrid'),
('Luis Pérez', 'luis@email.com', '600333444', 'Barcelona'),
('María López', 'maria@email.com', '600555666', 'Sevilla');

-- ============================================
-- Datos de prueba productos
-- ============================================

INSERT INTO products (name, description, price, stock) VALUES
('Body bebé algodón', 'Body suave para bebé', 12.90, 50),
('Pijama bebé', 'Pijama cómodo para dormir', 19.99, 30),
('Manta bebé', 'Manta térmica suave', 25.50, 20),
('Biberón anticólicos', 'Biberón 250ml', 8.75, 40);

-- ============================================
-- Datos de prueba pedidos
-- ============================================

INSERT INTO orders (client_id, status, notes) VALUES
(1, 'pending', 'Entrega rápida'),
(2, 'shipped', 'Entrega estándar'),
(1, 'pending', 'Sin comentarios');

-- ============================================
-- Datos de prueba detalle de pedidos (items)
-- ============================================

-- Pedido 1 (Ana García)
INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES
(1, 1, 2, 12.90),
(1, 4, 1, 8.75);

-- Pedido 2 (Luis Pérez)
INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES
(2, 2, 1, 19.99),
(2, 3, 1, 25.50);

-- Pedido 3 (Ana García)
INSERT INTO order_items (order_id, product_id, quantity, purchase_price) VALUES
(3, 4, 1, 8.75);    