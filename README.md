# Ecommerce HappyMami - Backend PHP

API REST simple en PHP sin frameworks, con estructura MVC basica.

## Estructura

- `config/` configuracion de base de datos
- `controllers/` controladores
- `models/` modelos
- `routes/` rutas de la API
- `index.php` punto de entrada

## Requisitos

- PHP 8+
- MySQL

## Configuracion

1. Crea la base de datos y tablas con `script.sql`.
2. Revisa credenciales en `config/Database.php`.
3. Asegura que tu servidor web apunte al proyecto (o usa el server embebido).

## Ejecutar en local

```bash
php -S localhost:8000
```

La API queda en `http://localhost:8000`.

## Endpoints

### Clients

- `GET /clients`
- `GET /clients/{id}`
- `POST /clients`

Body ejemplo:

```json
{
  "name": "Juan Perez",
  "email": "juan@email.com",
  "phone": "600000000",
  "address": "Madrid"
}
```

### Products

- `GET /products`
- `GET /products/{id}`
- `POST /products`
- `PUT /products/{id}`

Body ejemplo (POST/PUT):

```json
{
  "name": "Body bebe",
  "description": "Algodon 100%",
  "price": 12.9,
  "stock": 20
}
```

### Orders

- `GET /orders`
- `GET /orders/{id}`
- `POST /orders`

Body ejemplo (POST):

```json
{
  "client_id": 1,
  "notes": "Entrega por la manana",
  "items": [
    { "product_id": 1, "quantity": 2 },
    { "product_id": 4, "quantity": 1 }
  ]
}
```

## Resumen rapido

| Recurso | Metodo | Endpoint |
|---|---|---|
| Clients | `GET` | `/clients` |
| Clients | `GET` | `/clients/{id}` |
| Clients | `POST` | `/clients` |
| Products | `GET` | `/products` |
| Products | `GET` | `/products/{id}` |
| Products | `POST` | `/products` |
| Products | `PUT` | `/products/{id}` |
| Orders | `GET` | `/orders` |
| Orders | `GET` | `/orders/{id}` |
| Orders | `POST` | `/orders` |

## Notas

- Las respuestas son JSON.
- Codigos comunes: `200`, `201`, `404`, `422`.
- Se usan transacciones al crear pedidos con detalle de productos.