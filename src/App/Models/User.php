<?php

namespace App\Models;

use PDO;
use PDOException;

class User extends BaseModel
{
    protected $table = 'users';

    public function findByUsername($username)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding user by username: " . $e->getMessage());
            return false;
        }
    }

    public function findByEmail($email)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding user by email: " . $e->getMessage());
            return false;
        }
    }

    public function findByPhone($phone)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE phone = :phone";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['phone' => $phone]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error finding user by phone: " . $e->getMessage());
            return false;
        }
    }

    public function create($data)
    {
        try {
            // Đảm bảo các trường bắt buộc
            $requiredFields = ['username', 'fullName', 'dateOfBirth', 'sex', 'phone', 'email', 'password'];
            
            // Xóa role khỏi required fields và set mặc định
            $data['eRole'] = 'USER'; // Sửa từ 'role' thành 'eRole' để khớp với tên cột trong DB
            
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new \Exception("Missing required field: {$field}");
                }
            }

            // Thêm avatar mặc định nếu không có
            if (!isset($data['avatar'])) {
                $data['avatar'] = '/assets/images/default-avatar.png';
            }

            // Thêm timestamp
            $data['createdAt'] = date('Y-m-d H:i:s');
            $data['updatedAt'] = date('Y-m-d H:i:s');

            // Hash password nếu chưa được hash
            if (strlen($data['password']) < 60) { // Kiểm tra nếu password chưa được hash
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            }

            return parent::create($data);
        } catch (PDOException $e) {
            error_log("Error creating user: " . $e->getMessage());
            // Log chi tiết lỗi để debug
            error_log("SQL Error: " . print_r($e, true));
            throw new \Exception("Không thể tạo tài khoản. Vui lòng thử lại sau.");
        }
    }

    public function update($id, $data)
    {
        try {
            // Cập nhật timestamp
            $data['updatedAt'] = date('Y-m-d H:i:s');

            return parent::update($id, $data);
        } catch (PDOException $e) {
            error_log("Error updating user: " . $e->getMessage());
            throw new \Exception("Không thể cập nhật thông tin. Vui lòng thử lại sau.");
        }
    }

    public function updatePassword($id, $newPassword)
    {
        try {
            $sql = "UPDATE {$this->table} SET password = :password, updatedAt = :updatedAt WHERE id = :id";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                'id' => $id,
                'password' => $newPassword,
                'updatedAt' => date('Y-m-d H:i:s')
            ]);
        } catch (PDOException $e) {
            error_log("Error updating password: " . $e->getMessage());
            throw new \Exception("Không thể cập nhật mật khẩu. Vui lòng thử lại sau.");
        }
    }

    public function getAddresses($userId)
    {
        try {
            $sql = "SELECT * FROM addresses WHERE userId = :userId ORDER BY isDefault DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting user addresses: " . $e->getMessage());
            return [];
        }
    }

    public function getDefaultAddress($userId)
    {
        try {
            $sql = "SELECT * FROM addresses WHERE userId = :userId AND isDefault = true LIMIT 1";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting default address: " . $e->getMessage());
            return null;
        }
    }

    public function validateEmail($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public function validatePhone($phone)
    {
        return preg_match('/^[0-9]{10,11}$/', $phone) === 1;
    }
}