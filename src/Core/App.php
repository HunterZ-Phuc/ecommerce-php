<?php
namespace Core;

class App
{
    private $router;
    
    public function __construct()
    {
        $this->router = require ROOT_PATH . '/config/routes.php';
    }

    public function run($path)
    {
        try {
            // Loại bỏ tên thư mục gốc khỏi path
            $path = preg_replace('/^\/php-mvc/', '', $path);
            
            // Tìm route phù hợp
            $params = $this->router->match($path);
            
            if ($params === false) {
                $this->show404();
                return;
            }
            
            // Tạo tên controller đầy đủ
            $controller = ucfirst($params['controller']);
            $controllerClass = "App\\Controllers\\{$controller}Controller";
            
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass();
                $action = $params['action'];
                
                if (method_exists($controllerInstance, $action)) {
                    call_user_func_array([$controllerInstance, $action], $params['params'] ?? []);
                } else {
                    $this->show404();
                }
            } else {
                $this->show404(); 
            }
        } catch (\Exception $e) {
            $this->show404();
        }
    }

    protected function show404()
    {
        http_response_code(404);
        require_once ROOT_PATH . "/src/App/Views/404.php";
        exit();
    }
} 