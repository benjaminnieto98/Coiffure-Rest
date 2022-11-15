<?php
require_once 'libs/Router.php';
require_once 'app/controllers/api.product.controller.php';

//creo el router
$router = new Router();

//tabla de ruteo
$router->addRoute('products', 'GET', 'ApiProductController', 'getAll');
$router->addRoute('products/:ID', 'GET', 'ApiProductController', 'get');
$router->addRoute('products/:ID', 'DELETE', 'ApiProductController', 'remove');
$router->addRoute('products', 'POST', 'ApiProductController', 'insert'); 
$router->addRoute('products/:ID', 'PUT', 'ApiProductController', 'update');

// ejecuta la ruta (sea cual sea)
$router->route($_GET["resource"], $_SERVER['REQUEST_METHOD']);
