## Comandos utilizados para este proyecto

1.- Crear un modelo y controlador con metodos por defecto para realizar APIS por defecto
---php artisan make:controller UserController --model=User --api
---php artisan make:controller mercados/PuestoController --api

2.- Comando para crear Request
---php artisan make:request Persona/PersonaStoreRequest

3.- Crear un Middleware para porteger rutas con JWT

-- php artisan make:middleware JwtMiddleware
-- Activar el middleware en el kernel.php
-- 'jwt.verify' => JwtMiddleware::class

4.- Comando para crear Seeder
-- php artisan make:seeder PersonasTableSeeder
-- php artisan db:seed --class=PersonasTableSeeder (Ejecutar seeder)

Mercados
php 8.0.2
Mysql 10

Agentes
php 8.2.18
