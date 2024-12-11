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
        try {
            // Tạo câu lệnh SQL động dựa trên dữ liệu cần cập nhật
            $updateFields = [];
            $params = [];

            foreach ($data as $key => $value) {
                $updateFields[] = "{$key} = :{$key}";
                $params[$key] = $value;
            }

            if (empty($updateFields)) {
                return true; // Không có gì để cập nhật
            }

            $sql = "UPDATE {$this->table} SET " . implode(', ', $updateFields) . " WHERE id = :id";
            $params['id'] = $id;

            $stmt = $this->db->prepare($sql);
            return $stmt->execute($params);
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi khi cập nhật biến thể: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    public function findByProductId($productId)
    {
        $sql = "SELECT * FROM {$this->table} WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function deleteByProductId($productId)
    {
        $sql = "DELETE FROM product_variants WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['productId' => $productId]);
    }

    public function updateStock($variantId, $newQuantity) 
    {
        $sql = "UPDATE product_variants 
                SET quantity = :quantity 
                WHERE id = :id";
                
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'quantity' => $newQuantity,
            'id' => $variantId
        ]);
    }
}