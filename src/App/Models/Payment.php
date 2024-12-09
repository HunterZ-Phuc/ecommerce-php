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
        $data['paymentStatus'] = $data['paymentStatus'] ?? 'PENDING';
        return parent::create($data);
    }

    public function updateStatus($id, $status)
    {
        $data = [
            'paymentStatus' => $status,
            'paymentDate' => date('Y-m-d H:i:s'),
            'updatedAt' => date('Y-m-d H:i:s')
        ];
        return parent::update($id, $data);
    }
}