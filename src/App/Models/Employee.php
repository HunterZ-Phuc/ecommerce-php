<?php

namespace App\Models;

class Employee extends BaseModel
{
    protected $table = 'employees';

    // Tạo nhân viên
    public function create($data)
    {
        $data['eRole'] = 'EMPLOYEE'; // Gán role cho nhân viên
        $data['createdAt'] = date('Y-m-d H:i:s'); // Gán ngày tạo
        $data['avatar'] = $data['avatar'] ?? 'default-avatar.jpg'; // Mặc định avatar
        $data['fullName'] = $data['fullName'] ?? $data['username']; // Mặc định fullName
        $data['dateOfBirth'] = $data['dateOfBirth'] ?? date('Y-m-d'); // Mặc định ngày sinh
        $data['sex'] = $data['sex'] ?? 'Other'; // Mặc định giới tính
        $data['phone'] = $data['phone'] ?? '';
        $data['address'] = $data['address'] ?? ''; // Mặc định địa chỉ
        return parent::create($data); // Gọi hàm create của BaseModel để lưu dữ liệu vào database
    }

    // Cập nhật nhân viên
    public function update($id, $data)
    {
        $data['updatedAt'] = date('Y-m-d H:i:s'); // Gán ngày cập nhật
        return parent::update($id, $data); // Gọi hàm update của BaseModel để cập nhật dữ liệu vào database
    }

        // Tìm nhân viên bằng username
        public function findByUsername($username)
        {
            $sql = "SELECT * FROM {$this->table} WHERE username = :username";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['username' => $username]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
    
        // Tìm nhân viên bằng email
        public function findByEmail($email)
        {
            $sql = "SELECT * FROM {$this->table} WHERE email = :email";
            $stmt = $this->db->prepare($sql);
            $stmt->execute(['email' => $email]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        }
}