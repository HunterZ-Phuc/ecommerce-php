<?php

namespace App\Models;

class Item extends BaseModel
{
    protected $table = 'items';

    public function findByOrderId($orderId)
    {
        $sql = "SELECT i.*, p.productName, pv.variantName 
                FROM {$this->table} i
                JOIN products p ON i.productId = p.id
                LEFT JOIN product_variants pv ON i.variantId = pv.id
                WHERE i.orderId = :orderId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['orderId' => $orderId]);
        return $stmt->fetchAll();
    }

    public function create($data)
    {
        $data['createdAt'] = date('Y-m-d H:i:s');
        return parent::create($data);
    }
}
