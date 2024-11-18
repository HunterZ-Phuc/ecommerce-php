<?php
namespace Controllers;

use Models\User;
use Models\Admin;
use Exception;

class AuthController {
    public function login($data) {
        try {
            $username = $data['username'] ?? '';
            $password = $data['password'] ?? '';
            $role = $data['role'] ?? 'USER';

            if (empty($username) || empty($password)) {
                throw new Exception("Vui lòng nhập đầy đủ thông tin");
            }

            // Xác thực dựa vào role
            switch($role) {
                case 'USER':
                    // Logic xác thực user
                    break;
                case 'ADMIN':
                    // Logic xác thực admin
                    break;
                default:
                    throw new Exception("Role không hợp lệ");
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function register($data) {
        try {
            // Logic đăng ký user mới
            $user = new User($data);
            // Lưu user vào database
            
            return [
                'success' => true,
                'message' => 'Đăng ký thành công',
                'data' => $user->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false, 
                'message' => $e->getMessage()
            ];
        }
    }

    public function forgotPassword($email) {
        try {
            // Logic quên mật khẩu
            // Gửi OTP qua email
            
            return [
                'success' => true,
                'message' => 'Đã gửi mã OTP qua email'
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}