# API IoT para Prevención de Incendios

## Descripción General

Esta es una robusta API REST desarrollada con Laravel 11 para recopilar y gestionar datos de dispositivos IoT orientados a la prevención de incendios. El sistema procesa lecturas de sensores, administra dispositivos y maneja alertas para prevenir posibles incidentes de incendio.

## Funcionalidades Principales

- **Autenticación JWT** para un acceso seguro a la API.
- **Control de Acceso Basado en Roles** (roles de Administrador y Usuario).
- **Gestión de Dispositivos en Tiempo Real**.
- **Recopilación de Datos de Sensores**.
- **Arquitectura RESTful**.

## Tecnología Utilizada

- Laravel 11
- Autenticación JWT
- MySQL/PostgreSQL (configurable)
- PHP 8.2+

## Autenticación

La API utiliza JSON Web Tokens (JWT) para la autenticación. Incluye el token en el encabezado Authorization:

```
Authorization: Bearer {tu_jwt_token}
```

### Endpoints de Autenticación


| Método | Endpoint         | Descripción                        | Acceso      |
| ------- | ---------------- | ----------------------------------- | ----------- |
| POST    | `/auth/login`    | Inicio de sesión de usuario        | Público    |
| POST    | `/auth/register` | Registro de nuevo usuario           | Público    |
| POST    | `/auth/logout`   | Cierre de sesión de usuario        | Autenticado |
| GET     | `/auth/user`     | Obtener detalles del usuario actual | Autenticado |

## Gestión de Dispositivos

Administra los dispositivos IoT y sus estados a través de estos endpoints:

### Endpoints de Dispositivos


| Método | Endpoint                      | Descripción                       | Acceso   |
| ------- | ----------------------------- | ---------------------------------- | -------- |
| POST    | `/devices/activate/{token}`   | Activar dispositivo                | Público |
| POST    | `/devices/deactivate/{token}` | Desactivar dispositivo             | Público |
| GET     | `/devices/status/{token}`     | Obtener estado del dispositivo     | Público |
| GET     | `/devices`                    | Listar todos los dispositivos      | Admin    |
| GET     | `/devices/{id}`               | Obtener un dispositivo específico | Admin    |
| POST    | `/devices`                    | Crear dispositivo                  | Admin    |
| PUT     | `/devices/{id}`               | Actualizar dispositivo             | Admin    |
| DELETE  | `/devices/{id}`               | Eliminar dispositivo               | Admin    |

## Lecturas de Sensores

Recopila y administra datos de sensores a través de estos endpoints:

### Endpoints de Lecturas de Sensores


| Método | Endpoint         | Descripción                | Acceso   |
| ------- | ---------------- | --------------------------- | -------- |
| POST    | `/readings`      | Crear nueva lectura         | Público |
| GET     | `/readings`      | Listar todas las lecturas   | Admin    |
| GET     | `/readings/{id}` | Obtener lectura específica | Admin    |
| PUT     | `/readings/{id}` | Actualizar lectura          | Admin    |
| DELETE  | `/readings/{id}` | Eliminar lectura            | Admin    |

## Sistema de Alertas

Administra las alertas del sistema a través de estos endpoints:

### Endpoints de Alertas


| Método | Endpoint       | Descripción               | Acceso   |
| ------- | -------------- | -------------------------- | -------- |
| POST    | `/alerts`      | Crear alerta               | Público |
| GET     | `/alerts`      | Listar todas las alertas   | Admin    |
| GET     | `/alerts/{id}` | Obtener alerta específica | Admin    |
| PUT     | `/alerts/{id}` | Actualizar alerta          | Admin    |
| DELETE  | `/alerts/{id}` | Eliminar alerta            | Admin    |

## Instalación

1. Clona el repositorio

```bash
git clone https://github.com/fer-gc05/API-REST
```

2. Instala las dependencias

```bash
composer install
```

3. Copia el archivo de entorno

```bash
cp .env.example .env
```

4. Configura la base de datos en `.env`

```
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tu_base_de_datos
DB_USERNAME=tu_usuario
DB_PASSWORD=tu_contraseña
```

5. Genera la clave de la aplicación

```bash
php artisan key:generate
```

6. Genera el secreto para JWT

```bash
php artisan jwt:secret
```

7. Ejecuta las migraciones

```bash
php artisan migrate
```

## Manejo de Errores

La API devuelve códigos de estado HTTP estándar y respuestas en formato JSON:

- 200: Éxito
- 201: Creado
- 401: No Autorizado
- 403: Prohibido
- 404: No Encontrado
- 422: Error de Validación
- 500: Error del Servidor

## Consideraciones de Seguridad

- Contraseñas cifradas con bcrypt.
- Validación de entradas en todos los endpoints.

# Próximos Pasos

## 1. Desarrollo del Dashboard Administrativo con Filament

### Objetivos

- Crear una interfaz intuitiva para que los administradores gestionen dispositivos, lecturas y alertas.
- Integrar Filament para facilitar la creación de CRUDs y pantallas de administración.
- Añadir funcionalidades para visualizar y gestionar alertas registradas en la base de datos.

### Tareas

- Instalar y configurar Filament.
- Crear el CRUD de alertas en el dashboard.
- Integrar filtros de búsqueda y ordenación para las alertas.

## 2. Gestión de Alertas

### Objetivos

- Implementar un sistema para enviar notificaciones (WhatsApp o Telegram).
- Crear una interfaz en el dashboard para gestionar el estado de las alertas.

### Tareas

- Integrar la capacidad de enviar notificaciones a través de WhatsApp o Telegram.
  - Para **WhatsApp**: Usar la API de Twilio o WhatsApp Business API.
  - Para **Telegram**: Usar el Bot API de Telegram.
- Actualizar el estado de las alertas en la base de datos después de que se envíen las notificaciones.
