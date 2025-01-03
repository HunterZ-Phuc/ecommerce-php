<?php

namespace App\Models;

class ProductImage extends BaseModel
{
    protected $table = 'product_images';

    // Tạo ảnh sản phẩm
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

    // Lấy ảnh sản phẩm theo ID sản phẩm
    public function findByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Xóa ảnh sản phẩm theo ID sản phẩm
    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM {$this->table} WHERE productId = :productId";
        return $this->db->prepare($sql)->execute(['productId' => $productId]);
    }

    // Lấy ảnh sản phẩm theo ID biến thể
    public function findByVariantId($variantId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE variantId = :variantId LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['variantId' => $variantId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Xóa ảnh sản phẩm theo ID biến thể
    public function deleteByVariantId($variantId)
    {
        $sql = "DELETE FROM {$this->table} WHERE variantId = :variantId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['variantId' => $variantId]);
    }

    // Lấy ảnh sản phẩm theo ID biến thể
    public function getImagesByVariantId($variantId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE variantId = :variantId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['variantId' => $variantId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy ảnh chính của sản phẩm
    public function findMainImage($productId)
    {
        $sql = "SELECT * FROM product_images 
                WHERE productId = :productId 
                AND variantId IS NULL 
                AND isThumbnail = true 
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }
}
