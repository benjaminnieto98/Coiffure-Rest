<?php
require_once 'libs/Router.php';
require_once 'app/controllers/api.product.controller.php';
require_once 'app/controllers/user-api.controller.php';

//creo el router
$router = new Router();

//tabla de ruteo
$router->addRoute('products', 'GET', 'ApiProductController', 'get'); //Listar todos los productos
$router->addRoute('products/:ID', 'GET', 'ApiProductController', 'get'); //Obtener un producto. Le debo pasar un parámetro
$router->addRoute('products/:ID', 'DELETE', 'ApiProductController', 'remove'); //Eliminar un producto. Le debo pasar un parámetro.
$router->addRoute('products', 'POST', 'ApiProductController', 'insert'); //Insertar un producto.
$router->addRoute('products/:ID', 'PUT', 'ApiProductController', 'update'); //Editar un producto. Le debo pasar un parámetro

//validar user mediante token
$router->addRoute('users/token', 'GET', 'UserApiController', 'getToken');
$router->addRoute('users/:ID', 'GET', 'UserApiController', 'getUser');

//ejecuta la ruta (sea cual sea)
$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);
