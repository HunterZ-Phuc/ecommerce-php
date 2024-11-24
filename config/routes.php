<?php

$router = new Core\Router;

// Homepage
$router->add("/", ["controller" => "home", "action" => "index"]);

// Admin routes
$router->add("/admin", ["controller" => "dashboard", "action" => "index"]);
$router->add("/admin/admin-management", ["controller" => "admin", "action" => "index"]);
$router->add("/admin/admin-management/create", ["controller" => "admin", "action" => "create"]);
$router->add("/admin/admin-management/edit/{id:\d+}", ["controller" => "admin", "action" => "edit"]);
$router->add("/admin/admin-management/delete/{id:\d+}", ["controller" => "admin", "action" => "delete"]);

$router->add('/admin/employee-management', ['controller' => 'employee', 'action' => 'index']);
$router->add('/admin/employee-management/create', ['controller' => 'employee', 'action' => 'create']);
$router->add('/admin/employee-management/edit/{id:\d+}', ['controller' => 'employee', 'action' => 'edit']);
$router->add('/admin/employee-management/delete/{id:\d+}', ['controller' => 'employee', 'action' => 'delete']);

// Employee routes
$router->add('/employee/product-management', ['controller' => 'Product', 'action' => 'index']);
$router->add('/employee/product-management/create', ['controller' => 'Product', 'action' => 'create']);
$router->add('/employee/product-management/edit/{id:\d+}', ['controller' => 'Product', 'action' => 'edit']);
$router->add('/employee/product-management/delete/{id:\d+}', ['controller' => 'Product', 'action' => 'delete']);

// Logout route
$router->add("/logout", ["controller" => "auth", "action" => "logout"]);

return $router;