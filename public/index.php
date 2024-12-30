<?php

declare(strict_types=1);

define("ROOT_PATH", dirname(__DIR__));

spl_autoload_register(function (string $class_name) {

    require ROOT_PATH . "/src/" . str_replace("\\", "/", $class_name) . ".php";

});

require_once ROOT_PATH . "/src/Core/Router.php";

$app = new Core\App();
$path = $_SERVER['REQUEST_URI'] ?? '/';
$app->run($path);