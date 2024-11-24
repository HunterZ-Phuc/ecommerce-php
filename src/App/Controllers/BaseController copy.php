<?php
namespace App\Controllers;

abstract class BaseController {
    public function view($view, $data = []) {
        extract($data);
        
        // Bắt đầu output buffering
        ob_start();
        
        // Load view content
        require_once ROOT_PATH . "/src/App/Views/{$view}.php";
        $content = ob_get_clean();
        
        $layout = strpos($view, 'admin/') === 0 
            ? 'layouts/admin_layout.php'
            : 'layouts/default_layout.php';
        
        require_once ROOT_PATH . "/src/App/Views/" . $layout;
    }

    protected function json($data) {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    protected function error($message, $code = 400) {
        $this->json([
            'success' => false,
            'message' => $message
        ]);
    }

    protected function jsonResponse($data, $statusCode = 200) {
        ob_clean();
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}
