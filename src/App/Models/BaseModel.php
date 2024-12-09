<?php
namespace App\Models;

use Core\Database;

class BaseModel
{
    // Database    
    protected $db;
    // Tên bảng
    protected $table;
    // Khóa chính
    protected $primaryKey = 'id';

    // Khởi tạo kết nối database
    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    // Lấy tất cả các bản ghi
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy bản ghi theo ID
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Tạo bản ghi mới
    public function create($data)
    {
        $fields = array_keys($data); // Lấy tên cột từ dữ liệu
        $values = array_map(fn($field) => ":$field", $fields); // Tạo mảng các giá trị cột

        $sql = "INSERT INTO {$this->table} (" . implode(',', $fields) . ") 
                VALUES (" . implode(',', $values) . ")"; // Tạo câu truy vấn SQL

        $stmt = $this->db->prepare($sql); // Chuẩn bị câu truy vấn
        $stmt->execute($data); // Thực thi câu truy vấn
        return $this->db->lastInsertId(); // Trả về ID của bản ghi vừa tạo
    }

    // Cập nhật bản ghi
    public function update($id, $data)
    {
        $fields = array_map(fn($field) => "$field = :$field", array_keys($data));

        $sql = "UPDATE {$this->table} 
                SET " . implode(',', $fields) . "
                WHERE {$this->primaryKey} = :id";

        $data['id'] = $id;
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($data);
    }

    // Xóa bản ghi
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE {$this->primaryKey} = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
}
