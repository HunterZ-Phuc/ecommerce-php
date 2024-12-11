<?php

namespace App\Models;

use PDO;
use PDOException;
use Exception;

class Address extends BaseModel
{
    protected $table = 'addresses';

    public function getAllByUserId($userId)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE userId = :userId ORDER BY isDefault DESC";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting addresses: " . $e->getMessage());
            return [];
        }
    }

    public function getById($id, $userId)
    {
        try {
            $sql = "SELECT * FROM {$this->table} WHERE id = :id AND userId = :userId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id, 'userId' => $userId]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting address by id: " . $e->getMessage());
            return false;
        }
    }

    public function create($data)
    {
        try {
            // Kiểm tra các trường bắt buộc
            $requiredFields = ['userId', 'fullName', 'phoneNumber', 'address'];
            foreach ($requiredFields as $field) {
                if (!isset($data[$field])) {
                    throw new Exception("Missing required field: {$field}");
                }
            }

            // Kiểm tra xem có địa chỉ nào chưa
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE userId = :userId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $data['userId']]);
            $hasAddresses = $stmt->fetchColumn();

            // Nếu chưa có địa chỉ nào, set làm mặc định
            $data['isDefault'] = ($hasAddresses == 0);

            return parent::create($data);
        } catch (PDOException $e) {
            error_log("Error creating address: " . $e->getMessage());
            throw new Exception("Không thể tạo địa chỉ. Vui lòng thử lại sau.");
        }
    }

    public function update($id, $data)
    {
        try {
            // Đảm bảo userId khớp
            $currentAddress = $this->getById($id, $data['userId']);
            if (!$currentAddress) {
                throw new Exception("Địa chỉ không tồn tại hoặc không thuộc về người dùng này");
            }

            // Loại bỏ userId khỏi data trước khi cập nhật
            $updateData = array_filter($data, function ($key) {
                return $key !== 'userId';
            }, ARRAY_FILTER_USE_KEY);

            // Tạo câu lệnh SQL động dựa trên dữ liệu cần cập nhật
            $updateFields = [];
            $params = [];

            foreach ($updateData as $key => $value) {
                $updateFields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }

            if (empty($updateFields)) {
                return true; // Không có gì để cập nhật
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $params['id'] = $id;

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);

        } catch (PDOException $e) {
            error_log("Error updating address: " . $e->getMessage());
            throw new Exception("Không thể cập nhật địa chỉ. Vui lòng thử lại sau.");
        }
    }

    public function delete($id)
    {
        try {
            // Lấy userId từ địa chỉ hiện tại
            $address = $this->findById($id);
            if (!$address) {
                throw new Exception('Địa chỉ không tồn tại');
            }

            // Kiểm tra xem có phải địa chỉ mặc định không
            if ($address['isDefault']) {
                throw new Exception('Không thể xóa địa chỉ mặc định');
            }

            return parent::delete($id);
        } catch (PDOException $e) {
            error_log("Error deleting address: " . $e->getMessage());
            throw new Exception("Không thể xóa địa chỉ. Vui lòng thử lại sau.");
        }
    }

    public function deleteWithUserId($id, $userId)
    {
        try {
            // Kiểm tra xem địa chỉ có thuộc về user không
            $address = $this->getById($id, $userId);
            if (!$address) {
                throw new Exception('Địa chỉ không tồn tại hoặc không thuộc về người dùng này');
            }

            // Kiểm tra xem có phải địa chỉ mặc định không
            if ($this->isDefault($id, $userId)) {
                throw new Exception('Không thể xóa địa chỉ mặc định');
            }

            $sql = "DELETE FROM {$this->table} WHERE id = :id AND userId = :userId";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute(['id' => $id, 'userId' => $userId]);
        } catch (PDOException $e) {
            error_log("Error deleting address: " . $e->getMessage());
            throw new Exception("Không thể xóa địa chỉ. Vui lòng thử lại sau.");
        }
    }

    public function setDefault($id, $userId)
    {
        try {
            $this->db->beginTransaction();

            // Bỏ mặc định tất cả địa chỉ của user
            $sql = "UPDATE {$this->table} SET isDefault = false WHERE userId = :userId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);

            // Đặt địa chỉ mới làm mặc định
            $sql = "UPDATE {$this->table} SET isDefault = true WHERE id = :id AND userId = :userId";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute(['id' => $id, 'userId' => $userId]);

            if (!$result) {
                throw new Exception('Không thể thiết lập địa chỉ mặc định');
            }

            $this->db->commit();
            return true;
        } catch (PDOException $e) {
            $this->db->rollBack();
            error_log("Error setting default address: " . $e->getMessage());
            throw new Exception("Không thể thiết lập địa chỉ mặc định. Vui lòng thử lại sau.");
        }
    }

    public function isDefault($id, $userId)
    {
        try {
            $sql = "SELECT isDefault FROM {$this->table} WHERE id = :id AND userId = :userId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['id' => $id, 'userId' => $userId]);
            return (bool) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error checking default address: " . $e->getMessage());
            return false;
        }
    }

    public function countByUserId($userId)
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE userId = :userId";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['userId' => $userId]);
            return (int) $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error counting user addresses: " . $e->getMessage());
            return 0;
        }
    }

    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE userId = :userId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }
}