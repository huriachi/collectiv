<?php namespace collectiv\controllers;

use collectiv\core\Database;
use Klein\Klein;
use Klein\Request;
use Klein\Response;
use Klein\ServiceProvider;

abstract class BaseController {
    protected $database;
    protected $router;

    public function __construct(Database $database, Klein $router) {
        $this->database = $database;
        $this->router = $router;
    }

    /**
     * This registers all routes that are available within a child class. The child class can implement index(),
     * create(), store(), show(), edit(), update() and delete().
     */
    public function routes() {
        $baseRoute = '/' . $this->routeName();
        $availableRoutes = [
            ['call' => 'index', 'route' => $baseRoute, 'type' => 'GET'],
            ['call' => 'create', 'route' => "$baseRoute/create", 'type' => 'GET'],
            ['call' => 'store', 'route' => $baseRoute, 'type' => 'POST'],
            ['call' => 'show', 'route' => "$baseRoute/[:id]", 'type' => 'GET'],
            ['call' => 'edit', 'route' => "$baseRoute/[:id]/edit", 'type' => 'GET'],
            ['call' => 'update', 'route' => "$baseRoute/[:id]/edit", 'type' => 'POST'],
            ['call' => 'delete', 'route' => "$baseRoute/[:id]/delete", 'type' => 'POST']
        ];

        foreach ($availableRoutes as $route) {
            if (method_exists($this, $route['call'])) {
                $function = $route['call'];
                $this->router->respond(
                    $route['type'],
                    $route['route'],
                    function(Request $request, Response $response, ServiceProvider $service) use ($function) {
                        return $this->$function($request, $response, $service);
                    }
                );
            }
        }
    }

    protected abstract function routeName(): string;
}