<?php
namespace Controllers;

use Models\User;
use Models\Address;
use Exception;

class UserController {
    public function getProfile($userId) {
        try {
            // Logic lấy thông tin user
            $user = new User(['id' => $userId]);
            // Load thông tin từ database
            
            return [
                'success' => true,
                'data' => $user->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function updateProfile($userId, $data) {
        try {
            // Validate dữ liệu
            if (empty($data['fullName']) || empty($data['phone'])) {
                throw new Exception("Thiếu thông tin cần thiết");
            }

            // Cập nhật thông tin user
            $user = new User(['id' => $userId]);
            $user->setFullName($data['fullName']);
            $user->setPhone($data['phone']);
            // Cập nhật các trường khác
            
            // Lưu vào database
            
            return [
                'success' => true,
                'message' => 'Cập nhật thông tin thành công',
                'data' => $user->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function addAddress($userId, $data) {
        try {
            // Validate địa chỉ
            if (empty($data['address']) || empty($data['phone'])) {
                throw new Exception("Thiếu thông tin địa chỉ");
            }

            // Thêm địa chỉ mới
            $address = new Address($data['address'], $data['phone'], $data['fullName'], $userId);
            $user = new User(['id' => $userId]);
            $user->addAddress($address);
            
            // Lưu vào database

            return [
                'success' => true,
                'message' => 'Thêm địa chỉ thành công',
                'data' => $address->toArray()
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}
