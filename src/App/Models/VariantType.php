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

    public function findOrCreate($data)
    {
        // Tìm kiếm variant type hiện có
        $id = $this->getVariantTypeIdByName(
            $data['productId'],
            $data['name']
        );

        // Nếu đã tồn tại thì trả về id
        if ($id) {
            return $id;
        }

        // Nếu chưa tồn tại thì tạo mới
        return $this->createVariantType($data['productId'], $data['name']);
    }

    public function findByProductId($productId)
    {
        $sql = "SELECT * FROM variant_types WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll();
    }

    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM variant_types WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['productId' => $productId]);
    }
}
