<?php

use Phalcon\Mvc\Router;

// Create the router
$router = new Router();

// Define a route
$router->addPost('/wa', [
        "controller" => "wa",
        "action"     => "post",
    ]
);

// $router->handle();