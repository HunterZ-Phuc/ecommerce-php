<?php

namespace App\Models;

class ProductImage extends BaseModel
{
    protected $table = 'product_images';

    public function create($data)
    {
        try {
            $sql = "INSERT INTO product_images (productId, variantId, imageUrl, isThumbnail) 
                    VALUES (:productId, :variantId, :imageUrl, :isThumbnail)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'productId' => $data['productId'],
                'variantId' => $data['variantId'] ?? null,
                'imageUrl' => $data['imageUrl'],
                'isThumbnail' => $data['isThumbnail'] ?? false
            ]);

            if (!$result) {
                throw new \Exception('Không thể lưu ảnh sản phẩm');
            }
            
            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi database khi lưu ảnh: ' . $e->getMessage());
        }
    }

    public function findByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll();
    }

    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE productId = :productId";
        return $this->db->prepare($sql)->execute(['productId' => $productId]);
    }
}
