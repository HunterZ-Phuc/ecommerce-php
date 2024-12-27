<?php
namespace Core\Middleware;

class AuthMiddleware
{
    public function handle($request, $allowedRoles = [])
    {
        if (!isset($_SESSION['user_id'])) {
            $role = $allowedRoles[0] ?? '';
            switch ($role) {
                case 'ADMIN':
                    header('Location: /ecommerce-php/admin-login');
                    break;
                case 'EMPLOYEE':
                    header('Location: /ecommerce-php/employee-login');
                    break;
                default:
                    header('Location: /ecommerce-php/login');
            }
            exit;
        }

        if (!empty($allowedRoles)) {
            $userRole = $_SESSION['user_role'] ?? null;
            if (!in_array($userRole, $allowedRoles)) {
                header('Location: /ecommerce-php/403');
                exit;
            }
        }

        return true;
    }
}