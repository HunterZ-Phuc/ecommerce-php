<?php

namespace App\Models;

class VariantValue extends BaseModel
{
    protected $table = 'variant_values';

    public function createVariantValue($typeId, $value)
    {
        try {
            $sql = "INSERT INTO variant_values (variantTypeId, value) VALUES (:type_id, :value)";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'type_id' => $typeId,
                'value' => $value
            ]);
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi khi tạo variant value: ' . $e->getMessage());
        }
    }

    public function getVariantValueIdByValue($typeId, $value)
    {
        $sql = "SELECT id FROM variant_values WHERE variantTypeId = :type_id AND value = :value";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'type_id' => $typeId,
            'value' => $value
        ]);
        return $stmt->fetchColumn();
    }

    public function findByTypeAndValue($typeId, $value) {
        $sql = "SELECT id FROM variant_values 
                WHERE variantTypeId = ? AND value = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$typeId, $value]);
        $result = $stmt->fetch();
        return $result ? $result['id'] : null;
    }

    public function deleteByTypeId($typeId)
    {
        $sql = "DELETE FROM variant_values WHERE variantTypeId = ?";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([$typeId]);
    }

    public function findByTypeId($typeId)
    {
        $sql = "SELECT * FROM variant_values WHERE variantTypeId = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$typeId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
