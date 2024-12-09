<?php

$router = new Core\Router;

// Homepage
$router->add("/", ["controller" => "home", "action" => "index"]);
$router->add("/product/{id:\d+}", ["controller" => "home", "action" => "productDetail"]);

// User base route - redirect to profile
$router->add("/user", ["controller" => "User", "action" => "index"]);

// User routes
$router->add("/user/profile", ["controller" => "User", "action" => "profile"]);
$router->add("/user/profile/update", ["controller" => "User", "action" => "updateProfile"]);
$router->add("/user/profile/update-avatar", ["controller" => "User", "action" => "updateAvatar"]);
$router->add("/user/profile/update-email", ["controller" => "User", "action" => "updateEmail"]);
$router->add("/user/profile/update-phone", ["controller" => "User", "action" => "updatePhone"]);

// Thêm routes cho quản lý địa chỉ
$router->add("/user/addresses", ["controller" => "User", "action" => "addresses"]);
$router->add("/user/address/{id:\d+}", ["controller" => "User", "action" => "getAddress"]);
$router->add("/user/address/create", ["controller" => "User", "action" => "createAddress"]);
$router->add("/user/address/update/{id:\d+}", ["controller" => "User", "action" => "updateAddress"]);
$router->add("/user/address/delete/{id:\d+}", ["controller" => "User", "action" => "deleteAddress"]);
$router->add("/user/address/set-default/{id:\d+}", ["controller" => "User", "action" => "setDefaultAddress"]);

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

// Auth routes
$router->add("/login", ["controller" => "Auth", "action" => "login"]);
$router->add("/register", ["controller" => "Auth", "action" => "register"]);
$router->add("/logout", ["controller" => "Auth", "action" => "logout"]);

return $router;