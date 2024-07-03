## Comandos utilizados para este proyecto

1.- Crear un modelo y controlador con metodos por defecto para realizar APIS por defecto
---php artisan make:controller UserController --model=User --api

2.- Crear un Middleware para porteger rutas con JWT

-- php artisan make:middleware JwtMiddleware
-- Activar el middleware en el kernel.php
-- 'jwt.verify' => JwtMiddleware::class
