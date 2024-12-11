<?php
namespace App\Controllers;

use Core\Auth;

abstract class BaseController
{
    protected $auth;

    public function __construct()
    {
        // Đảm bảo session được khởi tạo trước khi làm bất cứ điều gì
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $this->auth = new Auth();
    }

    public function view($view, $data = [], $layout = null)
    {
        // Thêm thông tin user vào data để view có thể sử dụng
        $data['user'] = [
            'isLoggedIn' => isset($_SESSION['user_id']),
            'id' => $_SESSION['user_id'] ?? null,
            'name' => $_SESSION['user_name'] ?? null,
            'avatar' => $_SESSION['avatar'] ?? null,
            'role' => $_SESSION['user_role'] ?? null
        ];

        // Extract data để sử dụng trong view
        extract($data);

        // Bắt đầu output buffering
        ob_start();

        // Load nội dung view
        require_once ROOT_PATH . "/src/App/Views/{$view}.php";
        $content = ob_get_clean();

        // Nếu layout được cung cấp, sử dụng layout đó
        if ($layout) {
            // Load layout đã cung cấp
            require_once ROOT_PATH . "/src/App/Views/layouts/{$layout}.php";
        } else {
            // Nếu không có layout được cung cấp, sử dụng layout mặc định
            // Kiểm tra prefix của view để xác định layout
            if (strpos($view, 'admin/') === 0) {
                // Nếu view có prefix 'admin/', sử dụng layout admin
                $defaultLayout = 'layouts/admin_layout.php';
            } else if (strpos($view, 'user/') === 0) {
                // Nếu view có prefix 'user/', sử dụng layout user
                $defaultLayout = 'layouts/user_layout.php';
            } else {
                // Nếu không có prefix nào, sử dụng layout mặc định
              $defaultLayout = 'layouts/default_layout.php';
            }
            // Load layout mặc định
            require_once ROOT_PATH . "/src/App/Views/" . $defaultLayout;
        }
    }

    // Trả về dữ liệu dạng JSON
    protected function json($data)
    {
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    // Trả về dữ liệu dạng JSON với status code 400
    protected function error($message, $code = 400)
    {
        $this->json([
            'success' => false,
            'message' => $message
        ]);
    }

    // Trả về dữ liệu dạng JSON với status code 200
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

    protected function checkRole($allowedRoles)
    {
        if (!$this->auth->isLoggedIn()) {
            $role = $allowedRoles[0] ?? '';
            switch($role) {
                case 'ADMIN':
                    $this->redirect('admin-login');
                    break;
                case 'EMPLOYEE':
                    $this->redirect('employee-login');
                    break;
                default:
                    $this->redirect('login');
            }
            return;
        }

        $userRole = $this->auth->getUserRole();
        if (!in_array($userRole, $allowedRoles)) {
            $this->redirect('403');
            exit;
        }
    }
}
