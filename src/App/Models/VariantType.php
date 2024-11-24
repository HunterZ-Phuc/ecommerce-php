<?php

namespace App\Models;

class VariantType extends BaseModel
{
    protected $table = 'variant_types';

    public function createVariantType($productId, $name)
    {
        $sql = "INSERT INTO variant_types (productId, name) VALUES (:productId, :name)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'productId' => $productId,
            'name' => $name
        ]);
        return $this->db->lastInsertId();
    }

    public function getVariantTypeIdByName($productId, $name)
    {
        $sql = "SELECT id FROM variant_types WHERE productId = :productId AND name = :name";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'productId' => $productId,
            'name' => $name
        ]);
        return $stmt->fetchColumn();
    }
}
