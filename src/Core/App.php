<?php
namespace Core;

class App
{
    private $router;

    // Khởi tạo router
    public function __construct()
    {
        $this->router = require ROOT_PATH . '/config/routes.php';
    }

    public function run($path)
    {
        try {
            // Loại bỏ tên thư mục gốc khỏi path
            $path = preg_replace('/^\/ecommerce-php/', '', $path);

            // Xử lý request, tìm route phù hợp
            $params = $this->router->match($path);

            if ($params === false) {
                $this->show404();
                return;
            }

            // Tạo tên controller đầy đủ
            $controller = ucfirst($params['controller']);
            // Tạo tên controller đầy đủ, tên controller phải viết hoa chữ cái đầu tiên
            $controllerClass = "App\\Controllers\\{$controller}Controller";

            // Kiểm tra xem controller có tồn tại không
            if (class_exists($controllerClass)) {
                $controllerInstance = new $controllerClass(); // Tạo instance của controller
                $action = $params['action']; // Tên action cần thực thi

                // Kiểm tra xem action có tồn tại trong controller không
                if (method_exists($controllerInstance, $action)) {
                    call_user_func_array([$controllerInstance, $action], $params['params'] ?? []); // Gọi action với các tham số từ params
                } else {
                    $this->show404(); // Nếu action không tồn tại, hiển thị trang 404
                }
            } else {
                $this->show404(); // Nếu controller không tồn tại, hiển thị trang 404
            }
        } catch (\Exception $e) {
            $this->show404();
        }
    }

    // Hiển thị trang 404
    protected function show404()
    {
        http_response_code(404);  // Set HTTP status code to 404
        require_once ROOT_PATH . "/src/App/Views/404.php";
        exit();
    }
}