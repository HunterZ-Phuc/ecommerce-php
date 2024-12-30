<?php

namespace App\Models;

class Payment extends BaseModel
{
    protected $table = 'payments';

    public function findByOrderId($orderId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE orderId = :orderId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);
        return $stmt->fetch();
    }

    public function create($data)
    {
        $data['createdAt'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }
}