<?php

namespace Core;

class Auth {
    private $userId = null;
    private $userName = null;
    private $avatar = null;
    private $userRole = null;

    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $this->userId = $_SESSION['user_id'] ?? null;
        $this->userName = $_SESSION['user_name'] ?? null;
        $this->avatar = $_SESSION['avatar'] ?? null;
        $this->userRole = $_SESSION['user_role'] ?? null;
    }

    public function isLoggedIn() {
        return $this->userId !== null;
    }

    public function getUserId() {
        return $this->userId;
    }

    public function getUserName() {
        return $this->userName;
    }

    public function getAvatar() {
        return $this->avatar;
    }

    public function getUserRole() {
        return $this->userRole;
    }

    public function login($userId, $userName, $avatar, $role) {
        $_SESSION['user_id'] = $userId;
        $_SESSION['user_name'] = $userName;
        $_SESSION['avatar'] = $avatar;
        $_SESSION['user_role'] = $role;
        $this->userId = $userId;
        $this->userName = $userName;
        $this->avatar = $avatar;
        $this->userRole = $role;
    }

    public function logout() {
        session_destroy();
        $this->userId = null;
        $this->userName = null;
        $this->avatar = null;
        $this->userRole = null;
    }
} 