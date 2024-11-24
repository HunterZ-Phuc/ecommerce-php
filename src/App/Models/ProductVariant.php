<?php

namespace App\Models;

class ProductVariant extends BaseModel
{
    protected $table = 'product_variants';

    public function findAllByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function create($data)
    {
        try {
            // Validate dữ liệu
            if (!isset($data['price']) || floatval($data['price']) <= 0) {
                throw new \Exception('Giá sản phẩm phải lớn hơn 0');
            }

            if (!isset($data['quantity']) || intval($data['quantity']) < 0) {
                throw new \Exception('Số lượng sản phẩm không được âm');
            }

            $sql = "INSERT INTO product_variants (productId, price, quantity) 
                    VALUES (:productId, :price, :quantity)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'productId' => $data['productId'],
                'price' => floatval($data['price']),
                'quantity' => intval($data['quantity'])
            ]);

            if (!$result) {
                throw new \Exception('Không thể tạo biến thể sản phẩm');
            }

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi database khi tạo biến thể: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET variantName = :variantName, quantity = :quantity, price = :price WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $data['id'] = $id;
        return $stmt->execute($data);
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }
} 