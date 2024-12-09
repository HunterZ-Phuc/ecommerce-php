<?php

namespace App\Models;
use \PDO;
use \PDOException;

class VariantCombination extends BaseModel
{
    protected $table = 'variant_combinations';

    public function createVariantCombination($productVariantId, $variantValueId)
    {
        try {
            $sql = "INSERT INTO variant_combinations (productVariantId, variantValueId) 
                    VALUES (:productVariantId, :variantValueId)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'productVariantId' => $productVariantId,
                'variantValueId' => $variantValueId
            ]);

            if (!$result) {
                throw new \Exception('Không thể tạo combination');
            }

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi database khi tạo combination: ' . $e->getMessage());
        }
    }

    public function deleteByVariantId($variantId)
    {
        $sql = "DELETE FROM {$this->table} WHERE productVariantId = :productVariantId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['productVariantId' => $variantId]);
    }

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
}
