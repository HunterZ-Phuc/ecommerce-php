<?php

namespace Core;

class Auth
{
    private $userId = null;
    private $userName = null;
    private $avatar = null;
    private $userRole = null;

    // Khởi tạo session Auth
    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $this->userId = $_SESSION['user_id'] ?? null;
        $this->userName = $_SESSION['user_name'] ?? null;
        $this->avatar = $_SESSION['avatar'] ?? null;
        $this->userRole = $_SESSION['user_role'] ?? null;
    }

    // Kiểm tra xem người dùng đã đăng nhập hay chưa
    public function isLoggedIn()
    {
        return $this->userId !== null;
    }

    // Lấy ID của người dùng
    public function getUserId()
    {
        return $this->userId;
    }

    // Lấy tên người dùng
    public function getUserName()
    {
        return $this->userName;
    }

    // Lấy avatar của người dùng
    public function getAvatar()
    {
        return $this->avatar;
    }

    // Lấy vai trò của người dùng
    public function getUserRole()
    {
        return $this->userRole;
    }

    // Đăng nhập cho người dùng
    public function login($userId, $userName, $avatar, $role)
    {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['avatar'] = $avatar;
        $_SESSION['user_role'] = $role;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->avatar = $avatar;
        $this->userRole = $role;
    }

    // Đăng xuất cho người dùng
    public function logout()
    {
        session_destroy();
        $this->userId = null;
        $this->userName = null;
        $this->avatar = null;
        $this->userRole = null;
    }
}