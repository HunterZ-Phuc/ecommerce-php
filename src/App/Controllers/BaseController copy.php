<?php
namespace App\Controllers;

abstract class BaseController
{
    public function __construct()
    {
    }

    public function view($view, $data = [], $layout = null)
    {
        extract($data);

        // Bắt đầu output buffering
        ob_start();

        // Load view content
        require_once ROOT_PATH . "/src/App/Views/{$view}.php";
        $content = ob_get_clean();

        if ($layout) {
            require_once ROOT_PATH . "/src/App/Views/layouts/{$layout}.php";
        } else {
            // Kiểm tra prefix của view để xác định layout
            if (strpos($view, 'admin/') === 0) {
                $defaultLayout = 'layouts/admin_layout.php';
            } else if (strpos($view, 'user/') === 0) {
                $defaultLayout = 'layouts/user_layout.php';
            } else {
                $defaultLayout = 'layouts/default_layout.php';
            }
            require_once ROOT_PATH . "/src/App/Views/" . $defaultLayout;
        }
    }

    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function error($message, $code = 400)
    {
        $this->json([
            'success' => false,
            'message' => $message
        ]);
    }

    protected function jsonResponse($data, $statusCode = 200)
    {
        ob_clean();
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }

    protected function redirect($path)
    {
        header("Location: /ecommerce-php/" . $path);
        exit;
    }
}
