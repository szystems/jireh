# 🔌 API Documentation - Sistema Jireh

## Visión General

El sistema Jireh proporciona una API REST para integración externa y operaciones programáticas. Actualmente en desarrollo, la API seguirá los estándares REST y proporcionará acceso controlado a las funcionalidades principales del sistema.

## Configuración Base

### Endpoints Base
- **Desarrollo**: `http://localhost:8000/api`
- **Producción**: `https://tu-dominio.com/api`

### Autenticación
```http
Authorization: Bearer {token}
Content-Type: application/json
Accept: application/json
```

### Versionado
- Versión actual: `v1`
- Headers: `Api-Version: v1`

## Endpoints Disponibles

### Authentication

#### POST /api/auth/login
Autenticación de usuario

**Request:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9...",
        "user": {
            "id": 1,
            "name": "Usuario Ejemplo",
            "email": "user@example.com"
        }
    }
}
```

#### POST /api/auth/logout
Cerrar sesión

**Headers:** `Authorization: Bearer {token}`

**Response:**
```json
{
    "success": true,
    "message": "Successfully logged out"
}
```

### Artículos/Productos

#### GET /api/articulos
Listar artículos

**Query Parameters:**
- `page` (int): Página (default: 1)
- `per_page` (int): Items por página (default: 15, max: 100)
- `search` (string): Búsqueda por nombre o código
- `categoria_id` (int): Filtrar por categoría

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "codigo": "ART001",
                "nombre": "Producto Ejemplo",
                "descripcion": "Descripción del producto",
                "precio": "100.00",
                "stock": 50,
                "categoria": {
                    "id": 1,
                    "nombre": "Categoría Ejemplo"
                },
                "unidad": {
                    "id": 1,
                    "nombre": "Unidad",
                    "simbolo": "UN"
                }
            }
        ],
        "total": 100,
        "per_page": 15,
        "last_page": 7
    }
}
```

#### GET /api/articulos/{id}
Obtener artículo específico

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "codigo": "ART001",
        "nombre": "Producto Ejemplo",
        "descripcion": "Descripción detallada",
        "precio": "100.00",
        "stock": 50,
        "stock_minimo": 10,
        "categoria_id": 1,
        "unidad_id": 1,
        "proveedor_id": 1,
        "categoria": {
            "id": 1,
            "nombre": "Categoría Ejemplo"
        },
        "unidad": {
            "id": 1,
            "nombre": "Unidad",
            "simbolo": "UN"
        },
        "proveedor": {
            "id": 1,
            "nombre": "Proveedor Ejemplo"
        },
        "created_at": "2025-09-16T10:00:00.000000Z",
        "updated_at": "2025-09-16T10:00:00.000000Z"
    }
}
```

#### POST /api/articulos
Crear nuevo artículo

**Request:**
```json
{
    "codigo": "ART002",
    "nombre": "Nuevo Producto",
    "descripcion": "Descripción del nuevo producto",
    "precio": "150.00",
    "stock": 25,
    "stock_minimo": 5,
    "categoria_id": 1,
    "unidad_id": 1,
    "proveedor_id": 1
}
```

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 2,
        "codigo": "ART002",
        "nombre": "Nuevo Producto",
        // ... resto de campos
    },
    "message": "Artículo creado exitosamente"
}
```

### Ventas

#### GET /api/ventas
Listar ventas

**Query Parameters:**
- `page`, `per_page`: Paginación
- `fecha_desde`, `fecha_hasta`: Filtro por fechas (YYYY-MM-DD)
- `cliente_id`: Filtro por cliente
- `trabajador_id`: Filtro por trabajador

**Response:**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "fecha": "2025-09-16",
                "subtotal": "1000.00",
                "descuento": "50.00",
                "total": "950.00",
                "cliente": {
                    "id": 1,
                    "nombre": "Cliente Ejemplo",
                    "documento": "12345678"
                },
                "trabajador": {
                    "id": 1,
                    "nombre": "Vendedor Ejemplo"
                },
                "detalles_count": 3
            }
        ]
    }
}
```

#### GET /api/ventas/{id}
Obtener venta específica con detalles

**Response:**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "fecha": "2025-09-16",
        "subtotal": "1000.00",
        "descuento": "50.00",
        "total": "950.00",
        "cliente": {
            "id": 1,
            "nombre": "Cliente Ejemplo",
            "documento": "12345678",
            "telefono": "987654321"
        },
        "trabajador": {
            "id": 1,
            "nombre": "Vendedor Ejemplo"
        },
        "detalles": [
            {
                "id": 1,
                "cantidad": 2,
                "precio": "100.00",
                "subtotal": "200.00",
                "articulo": {
                    "id": 1,
                    "codigo": "ART001",
                    "nombre": "Producto Ejemplo"
                }
            }
        ],
        "pagos": [
            {
                "id": 1,
                "monto": "950.00",
                "fecha": "2025-09-16",
                "tipo": "efectivo"
            }
        ]
    }
}
```

### Trabajadores

#### GET /api/trabajadores
Listar trabajadores

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nombre": "Juan Pérez",
            "documento": "12345678",
            "telefono": "987654321",
            "email": "juan@example.com",
            "tipo_trabajador": {
                "id": 1,
                "nombre": "Vendedor",
                "porcentaje_comision": "5.00"
            },
            "comisiones_total": "1500.00",
            "ventas_count": 25
        }
    ]
}
```

### Clientes

#### GET /api/clientes
Listar clientes

#### POST /api/clientes
Crear cliente

**Request:**
```json
{
    "nombre": "Cliente Nuevo",
    "documento": "87654321",
    "telefono": "123456789",
    "email": "cliente@example.com",
    "direccion": "Dirección del cliente"
}
```

### Reportes

#### GET /api/reportes/ventas
Reporte de ventas

**Query Parameters:**
- `fecha_desde`, `fecha_hasta`: Período del reporte
- `trabajador_id`: Filtro por trabajador
- `formato`: `json`, `excel`, `pdf`

**Response:**
```json
{
    "success": true,
    "data": {
        "periodo": {
            "desde": "2025-09-01",
            "hasta": "2025-09-16"
        },
        "resumen": {
            "total_ventas": 150,
            "total_monto": "45000.00",
            "promedio_venta": "300.00"
        },
        "ventas_por_dia": [
            {
                "fecha": "2025-09-16",
                "cantidad": 8,
                "monto": "2400.00"
            }
        ],
        "top_productos": [
            {
                "articulo_id": 1,
                "nombre": "Producto Top",
                "cantidad_vendida": 50,
                "monto_total": "5000.00"
            }
        ]
    }
}
```

#### GET /api/reportes/comisiones
Reporte de comisiones

**Query Parameters:**
- `mes`: Mes del reporte (YYYY-MM)
- `trabajador_id`: Filtro por trabajador

## Códigos de Respuesta

### Códigos de Éxito
- `200 OK`: Operación exitosa
- `201 Created`: Recurso creado exitosamente
- `204 No Content`: Operación exitosa sin contenido

### Códigos de Error
- `400 Bad Request`: Request malformado
- `401 Unauthorized`: No autenticado
- `403 Forbidden`: Sin permisos
- `404 Not Found`: Recurso no encontrado
- `422 Unprocessable Entity`: Errores de validación
- `500 Internal Server Error`: Error del servidor

### Formato de Errores

#### Error de Validación (422)
```json
{
    "success": false,
    "message": "The given data was invalid.",
    "errors": {
        "nombre": [
            "The nombre field is required."
        ],
        "email": [
            "The email field must be a valid email address."
        ]
    }
}
```

#### Error General
```json
{
    "success": false,
    "message": "Error description",
    "error_code": "SPECIFIC_ERROR_CODE"
}
```

## Rate Limiting

- **Límite**: 60 requests por minuto por IP
- **Headers de respuesta**:
  - `X-RateLimit-Limit`: Límite total
  - `X-RateLimit-Remaining`: Requests restantes
  - `X-RateLimit-Reset`: Timestamp de reset

## Paginación

Todos los endpoints que retornan listas incluyen paginación:

```json
{
    "data": [...],
    "current_page": 1,
    "per_page": 15,
    "total": 100,
    "last_page": 7,
    "from": 1,
    "to": 15,
    "links": {
        "first": "http://api.example.com/resource?page=1",
        "last": "http://api.example.com/resource?page=7",
        "prev": null,
        "next": "http://api.example.com/resource?page=2"
    }
}
```

## Webhooks (Futuro)

### Eventos Disponibles
- `venta.creada`: Nueva venta registrada
- `pago.recibido`: Nuevo pago procesado
- `stock.bajo`: Stock por debajo del mínimo
- `comision.calculada`: Nueva comisión calculada

### Formato de Webhook
```json
{
    "event": "venta.creada",
    "timestamp": "2025-09-16T10:00:00Z",
    "data": {
        "venta_id": 123,
        "total": "1000.00",
        "cliente_id": 45
    }
}
```

## SDKs y Bibliotecas

### PHP
```bash
composer require jireh/api-client
```

### JavaScript
```bash
npm install jireh-api-client
```

### Python
```bash
pip install jireh-api-client
```

---

**Versión API**: v1  
**Última Actualización**: Septiembre 16, 2025  
**Estado**: En desarrollo  
**Contacto**: dev@jireh.com