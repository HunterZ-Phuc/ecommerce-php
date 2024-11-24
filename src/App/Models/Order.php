<?php

namespace App\Models;

class Order extends BaseModel
{
    protected $table = 'orders';

    public function findByUserId($userId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE userId = :userId ORDER BY createdAt DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['userId' => $userId]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $data['createdAt'] = date('Y-m-d H:i:s');
        $data['status'] = $data['status'] ?? 'PENDING';
        return parent::create($data);
    }

    public function updateStatus($id, $status)
    {
        $data = [
            'status' => $status,
            'updatedAt' => date('Y-m-d H:i:s')
        ];
        return parent::update($id, $data);
    }
}
