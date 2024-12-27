<?php

namespace App\Models;
use \PDO;
use \PDOException;

class VariantCombination extends BaseModel
{
    protected $table = 'variant_combinations';

    // Xóa tổ hợp biến thể
    public function deleteByVariantId($variantId)
    {
        $sql = "DELETE FROM {$this->table} WHERE productVariantId = :productVariantId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['productVariantId' => $variantId]);
    }

    // Lấy tổ hợp biến thể theo ID biến thể
    public function findByVariantId($variantId)
    {
        $sql = "
            SELECT 
                vt.id as typeId,
                vt.name as typeName,
                vv.id as valueId,
                vv.value
            FROM variant_combinations vc
            JOIN variant_values vv ON vc.variantValueId = vv.id
            JOIN variant_types vt ON vv.variantTypeId = vt.id
            WHERE vc.productVariantId = ?
            ORDER BY vt.id
        ";
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$variantId]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error fetching variant combinations: " . $e->getMessage());
            return [];
        }
    }

    // Lấy tổ hợp biến thể theo ID biến thể
    public function getVariantCombinationsWithDetails($variantId) {
        $sql = "SELECT 
                vt.id as typeId,
                vt.name as typeName,
                vv.id as valueId,
                vv.value
            FROM variant_combinations vc
            JOIN variant_values vv ON vc.variantValueId = vv.id
            JOIN variant_types vt ON vv.variantTypeId = vt.id
            WHERE vc.productVariantId = :variantId
            ORDER BY vt.id";
            
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['variantId' => $variantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}
