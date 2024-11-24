<?php

namespace App\Models;

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
}
