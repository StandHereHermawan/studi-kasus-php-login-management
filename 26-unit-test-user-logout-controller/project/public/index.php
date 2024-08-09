<?php

require_once __DIR__ . "/../vendor/autoload.php";

use AriefKarditya\LocalDomainPhp\App\Router;
use AriefKarditya\LocalDomainPhp\Config\Database;
use AriefKarditya\LocalDomainPhp\Controller\HomeController;
use AriefKarditya\LocalDomainPhp\Controller\UserController;

Database::getConnection('prod');

# Home Controller
Router::add('GET', '/', HomeController::class, 'index', []);

# User Controller
Router::add('GET', '/users/register', UserController::class, 'register', []);
Router::add('POST', '/users/register', UserController::class, 'postRegister', []);
Router::add('GET', '/users/login', UserController::class, 'login', []);
Router::add('POST', '/users/login', UserController::class, 'postLogin', []);
Router::add('GET', '/users/logout', UserController::class, 'logout', []);

Router::run();

# jika URL = localhost:8080/login, maka path-nya
# require __DIR__ . '/../app/View/login.php";

# localhost:8080/index.php/category = localhost:8080/category. $_SERVER['PATH_INFO'] = /category
# localhost:8080/index.php/user/login = localhost:8080/user/login. $_SERVER['PATH_INFO'] = /user/login

# localhost:8080/index.php/category?name=gadget = localhost:8080/category?name=gadget. $_SERVER['PATH_INFO'] = /category
# localhost:8080/index.php/product?name=gadget = localhost:8080/product?name=gadget. $_SERVER['PATH_INFO'] = /product
# query parameter ga masuk ke path info.