<?php
ini_set('display_errors', 1);
require_once 'vendor/autoload.php';
require_once 'src/config/app.php';
require_once 'src/config/helpers.php';

use Illuminate\Container\Container;
use Illuminate\Events\Dispatcher;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Database\Capsule\Manager as Capsule;

$container = new Container;
$capsule = new Capsule;

$capsule->addConnection([
    "driver" => DB_DRIVER,
    "host" => DB_HOST,
    "database" => DB_NAME,
    "username" => DB_USER,
    "password" => DB_PASSWORD,
    'charset' => DB_CHARSET,
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

$request = Request::capture();
$container->instance('Illuminate\Http\Request', $request);

$events = new Dispatcher($container);
$router = new Router($events, $container);

require_once 'src/config/routes.php';
$response = $router->dispatch($request);
$response->send();
