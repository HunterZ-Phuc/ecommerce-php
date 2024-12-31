<?php

$router = new Core\Router;

// Home routes
$router->add('/', ['controller' => 'Home', 'action' => 'index']);
$router->add('/home', ['controller' => 'Home', 'action' => 'index']);
$router->add('/about', ['controller' => 'Home', 'action' => 'about']);
$router->add('/contact', ['controller' => 'Home', 'action' => 'contact']);
$router->add('/product', ['controller' => 'Product', 'action' => 'index']);
$router->add("/product/{id:\d+}", ["controller" => "home", "action" => "productDetail"]);

// Tìm kiếm
$router->add('/search', ['controller' => 'Home', 'action' => 'search']);

// Cart routes
$router->add('/cart', ['controller' => 'Cart', 'action' => 'index']);
$router->add('/cart/add', ['controller' => 'Cart', 'action' => 'add']);
$router->add('/cart/update', ['controller' => 'Cart', 'action' => 'update']);
$router->add('/cart/remove', ['controller' => 'Cart', 'action' => 'remove']);
$router->add('/cart/clear', ['controller' => 'Cart', 'action' => 'clear']);

// Order routes
$router->add('/order/checkout', ['controller' => 'Order', 'action' => 'checkout']);
$router->add('/order/create', ['controller' => 'Order', 'action' => 'create']);
$router->add('/order/success/{id:\d+}', ['controller' => 'Order', 'action' => 'success']);
$router->add('/order/cancel/{id:\d+}', ['controller' => 'Order', 'action' => 'cancel']);
$router->add('/order/return/{id:\d+}', ['controller' => 'Order', 'action' => 'returnRequest']);
$router->add('/order/detail/{id:\d+}', ['controller' => 'Order', 'action' => 'detail']);
$router->add('/order/history[/]?[?page=\d+]?', ['controller' => 'Order', 'action' => 'history']);
$router->add('/order/payment/{id:\d+}', ['controller' => 'Order', 'action' => 'payment']);
$router->add('/order/confirmPayment/{id:\d+}', ['controller' => 'Order', 'action' => 'confirmPayment']);
$router->add('/order/confirm-delivery/{id:\d+}', ['controller' => 'Order', 'action' => 'confirmDelivery']);

// Payment routes
$router->add('/payment/banking/{id:\d+}', ['controller' => 'Payment', 'action' => 'banking']);
$router->add('/payment/upload/{id:\d+}', ['controller' => 'Payment', 'action' => 'upload']);
$router->add('/payment/verify/{id:\d+}', ['controller' => 'Payment', 'action' => 'verify']);


// User routes
$router->add("/user", ["controller" => "User", "action" => "index"]);
$router->add("/user/", ["controller" => "User", "action" => "index"]);
$router->add("/user/profile", ["controller" => "User", "action" => "profile"]);
$router->add("/user/profile/update", ["controller" => "User", "action" => "updateProfile"]);
$router->add("/user/profile/update-avatar", ["controller" => "User", "action" => "updateAvatar"]);
$router->add("/user/profile/update-email", ["controller" => "User", "action" => "updateEmail"]);
$router->add("/user/profile/update-phone", ["controller" => "User", "action" => "updatePhone"]);
$router->add("/user/change-password", ["controller" => "User", "action" => "changePassword"]);

    // Quản lý địa chỉ của người dùng
$router->add("/user/addresses", ["controller" => "User", "action" => "addresses"]);
$router->add("/user/address/{id:\d+}", ["controller" => "User", "action" => "getAddress"]);
$router->add("/user/address/create", ["controller" => "User", "action" => "createAddress"]);
$router->add("/user/address/update/{id:\d+}", ["controller" => "User", "action" => "updateAddress"]);
$router->add("/user/address/delete/{id:\d+}", ["controller" => "User", "action" => "deleteAddress"]);
$router->add("/user/address/set-default/{id:\d+}", ["controller" => "User", "action" => "setDefaultAddress"]);


// Admin routes
$router->add("/admin", ["controller" => "admin", "action" => "index"]);
$router->add("/admin/", ["controller" => "admin", "action" => "index"]);
$router->add("/admin/dashboard", ["controller" => "admin", "action" => "dashboard"]);
$router->add("/admin/admin-management", ["controller" => "admin", "action" => "adminManagement"]);
$router->add("/admin/admin-management/create", ["controller" => "admin", "action" => "create"]);
$router->add("/admin/admin-management/edit/{id:\d+}", ["controller" => "admin", "action" => "edit"]);
$router->add("/admin/admin-management/delete/{id:\d+}", ["controller" => "admin", "action" => "delete"]);
$router->add("/admin/change-password", ["controller" => "Admin", "action" => "changePassword"]);
// Quản lý nhân viên
$router->add('/admin/employee-management', ['controller' => 'employee', 'action' => 'employeeManagement']);
$router->add('/admin/employee-management/create', ['controller' => 'employee', 'action' => 'create']);
$router->add('/admin/employee-management/edit/{id:\d+}', ['controller' => 'employee', 'action' => 'edit']);
$router->add('/admin/employee-management/delete/{id:\d+}', ['controller' => 'employee', 'action' => 'delete']);

    // Quản lý người dùng
$router->add('/admin/users', ['controller' => 'Admin', 'action' => 'users']);
$router->add('/admin/users/export', ['controller' => 'Admin', 'action' => 'exportUsers']);
$router->add('/admin/users/toggle-status/{id:\d+}', ['controller' => 'Admin', 'action' => 'toggleUserStatus']);
$router->add('/admin/users/delete/{id:\d+}', ['controller' => 'Admin', 'action' => 'deleteUser']);

$router->add('/admin/stats/orders', ['controller' => 'Admin', 'action' => 'orderStats']);
$router->add('/admin/export/orders', ['controller' => 'Admin', 'action' => 'exportOrders']);

// Employee routes
$router->add('/employee', ['controller' => 'employee', 'action' => 'index']);
$router->add('/employee/', ['controller' => 'employee', 'action' => 'index']);
$router->add('/employee/dashboard', ['controller' => 'employee', 'action' => 'dashboard']);
$router->add('/employee/product-management', ['controller' => 'Product', 'action' => 'index']);
$router->add('/employee/product-management/create', ['controller' => 'Product', 'action' => 'create']);
$router->add('/employee/product-management/edit/{id:\d+}', ['controller' => 'Product', 'action' => 'edit']);
$router->add('/employee/product-management/delete/{id:\d+}', ['controller' => 'Product', 'action' => 'delete']);
    // Quản lý đơn hàng
$router->add('/employee/orders', ['controller' => 'Employee', 'action' => 'orders']);
$router->add('/employee/order/{id:\d+}', ['controller' => 'Employee', 'action' => 'orderDetail']);
$router->add('/employee/order/update-status', ['controller' => 'Employee', 'action' => 'updateOrderStatus']);
$router->add('/employee/order/confirm-payment', ['controller' => 'Employee', 'action' => 'confirmPayment']);
$router->add('/employee/stats/orders', ['controller' => 'Employee', 'action' => 'orderStats']);
$router->add('/employee/export/orders', ['controller' => 'Employee', 'action' => 'exportOrders']);
$router->add("/employee/change-password", ["controller" => "Employee", "action" => "changePassword"]);

// User auth routes
$router->add("/login", ["controller" => "Auth", "action" => "login"]);
$router->add("/register", ["controller" => "Auth", "action" => "register"]);
$router->add("/logout", ["controller" => "Auth", "action" => "logout"]);

// Admin auth routes
$router->add("/admin-login", ["controller" => "Auth", "action" => "adminLogin"]);
$router->add("/admin/logout", ["controller" => "Auth", "action" => "logout"]);

// Employee auth routes
$router->add("/employee-login", ["controller" => "Auth", "action" => "employeeLogin"]);
$router->add("/employee/logout", ["controller" => "Auth", "action" => "logout"]);

// Error routes
$router->add("/403", ["controller" => "Error", "action" => "forbidden"]);

return $router;