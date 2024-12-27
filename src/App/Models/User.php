<?php

namespace App\Models;

use PDO;
use PDOException;

class User extends BaseModel
{
    protected $table = 'users';

    // Tạo tài khoản người dùng
    public function create($data)
    {
        try {
            // Đảm bảo các trường bắt buộc
            $requiredFields = ['username', 'fullName', 'dateOfBirth', 'sex', 'phone', 'email', 'password'];

            // Set role mặc định
            $data['eRole'] = 'USER';

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

    // Cập nhật thông tin người dùng
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

    // Lấy người dùng theo username
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

    // Lấy người dùng theo email
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

    // Lấy người dùng theo số điện thoại
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

    // Cập nhật mật khẩu người dùng
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

    // Lấy địa chỉ người dùng
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

    // Lấy địa chỉ mặc định của người dùng
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

    // Đếm tổng số khách hàng
    public function getTotalCustomers()
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE eRole = 'USER'";
            return $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total customers: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy người dùng có thống kê đơn hàng và tổng số tiền chi tiêu
    public function getUsersWithStats($search = '', $limit = 10, $offset = 0)
    {
        try {
            $sql = "SELECT u.*, 
                    COUNT(DISTINCT o.id) as totalOrders,
                    COALESCE(SUM(o.totalAmount), 0) as totalSpent
                    FROM {$this->table} u
                    LEFT JOIN orders o ON u.id = o.userId
                    WHERE u.eRole = 'USER'";

            $params = [];

            if (!empty($search)) {
                $sql .= " AND (u.username LIKE :search OR u.fullName LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $sql .= " GROUP BY u.id
                     ORDER BY u.createdAt DESC
                     LIMIT :limit OFFSET :offset";

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->bindValue(':limit', (int) $limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int) $offset, PDO::PARAM_INT);

            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting users with stats: " . $e->getMessage());
            return [];
        }
    }

    // Đếm tổng số người dùng
    public function getTotalUsers($search = '')
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE eRole = 'USER'";
            $params = [];

            if (!empty($search)) {
                $sql .= " AND (username LIKE :search OR fullName LIKE :search)";
                $params[':search'] = "%$search%";
            }

            $stmt = $this->db->prepare($sql);

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting total users count: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy tất cả người dùng có thống kê đơn hàng và tổng số tiền chi tiêu
    public function getAllUsersWithStats()
    {
        try {
            $sql = "SELECT u.*, 
                    COUNT(DISTINCT o.id) as totalOrders,
                    COALESCE(SUM(o.totalAmount), 0) as totalSpent
                    FROM {$this->table} u
                    LEFT JOIN orders o ON u.id = o.userId
                    WHERE u.eRole = 'USER'
                    GROUP BY u.id
                    ORDER BY u.createdAt DESC";

            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting all users with stats: " . $e->getMessage());
            return [];
        }
    }
}