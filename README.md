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

## ---------- PREGUNTA TEORICA

Para agregar pedidos tipo bundle, agregaría una tabla `bundle_items` que relacione cada `bundle` con varios productos. 

Luego, los pedidos podrían tener un `bundle_id`, de modo que los pedidos normales sigan funcionando igual. Así se mantiene la compatibilidad con lo que ya existe y la integridad referencial entre bundles, pedidos y productos.

## Uso de IA / Copilot

Usé Copilot como apoyo para proponer la estructura de métodos, por ejemplo en `createClient()`, `createProduct()` y `createOrder()`, y también para validar datos obligatorios y emails. 

También me ayudó a acelerar la generación inicial de consultas SQL y rutas.

Revisando el código, tablas, endpoints y validaciones para que todo quede consistente con el proyecto y con el esquema final de la base de datos.



Creo que la IA ya forma parte de nuestro día a día, ayudándonos a acelerar nuestro trabajo y a resolver dudas rápidamente. 

Sin embargo, siempre debemos ser conscientes de que somos nosotros quienes dirigimos y orquestamos el proyecto, entendiendo lo que la IA nos devuelve y no limitándonos a copiar y pegar sin revisar.

