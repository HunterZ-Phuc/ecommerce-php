<?php

namespace Core;

class Auth
{
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    public function login($userId, $username, $avatar, $role)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $username; 
        $_SESSION['avatar'] = $avatar;
        $_SESSION['user_role'] = $role;
    }

    public function logout()
    {
        session_destroy();
    }

    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']);
    }

    public function getUserId()
    {
        return $_SESSION['user_id'] ?? null;
    }

    // Lấy vai trò của người dùng
    public function getUserRole()
    {
        return $_SESSION['user_role'] ?? null;
    }

    public function getAvatar() 
    {
        return $_SESSION['avatar'] ?? null;
    }
}