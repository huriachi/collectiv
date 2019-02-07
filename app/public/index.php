<?php

require dirname(__DIR__) . '/vendor/autoload.php';

// Initialize our essential classes.
$klein = new \Klein\Klein();
$database = new \collectiv\core\MySQL();
$userController = new \collectiv\controllers\UserController($database, $klein);
$homeController = new \collectiv\controllers\HomeController($database, $klein);

// Register all controller routes.
$userController->routes();
$homeController->routes();

// Register error routes.
$klein->onHttpError(function ($code, $router) {
    switch ($code) {
        case 404:
            echo \collectiv\core\View::render('404.twig');
            break;
        default:
            $router->response()->body("Something broke on our side... 500");
    }
});

// Very basic route to reset the database.
$klein->respond('POST', '/dangerous/database/reset', function () use ($database) {
    $database->reset();
});

// Finalize and dispatch our routes.
$klein->dispatch();