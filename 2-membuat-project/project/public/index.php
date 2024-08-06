<?php

require_once __DIR__ . "/../vendor/autoload.php";

use AriefKarditya\LocalDomainPhp\App\Router;
use AriefKarditya\LocalDomainPhp\Controller\HomeController;
use AriefKarditya\LocalDomainPhp\Controller\ProductController;
use AriefKarditya\LocalDomainPhp\Middleware\AuthMiddleware;

Router::add('GET', "/products/([0-9a-zA-Z]*)/categories/([0-9a-zA-Z]*)", ProductController::class, 'categories');

Router::add('GET', '/', HomeController::class, 'index');
Router::add('GET', '/hello', HomeController::class, 'hello', [AuthMiddleware::class]);
Router::add('GET', '/world', HomeController::class, 'world', [AuthMiddleware::class]);
Router::add('GET', '/about', HomeController::class, 'about');
Router::add('GET', '/login', HomeController::class, 'login');

Router::run();

# jika URL = localhost:8080/login, maka path-nya
# require __DIR__ . '/../app/View/login.php";

# localhost:8080/index.php/category = localhost:8080/category. $_SERVER['PATH_INFO'] = /category
# localhost:8080/index.php/user/login = localhost:8080/user/login. $_SERVER['PATH_INFO'] = /user/login

# localhost:8080/index.php/category?name=gadget = localhost:8080/category?name=gadget. $_SERVER['PATH_INFO'] = /category
# localhost:8080/index.php/product?name=gadget = localhost:8080/product?name=gadget. $_SERVER['PATH_INFO'] = /product
# query parameter ga masuk ke path info.