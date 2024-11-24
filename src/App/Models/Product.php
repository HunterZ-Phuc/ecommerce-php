<?php

namespace App\Models;

use PDOException;

use Exception;

class Product extends BaseModel
{
    protected $table = 'products';

    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    public function create($productData)
    {
        try {
            $sql = "INSERT INTO products (productName, description, category, origin, status) 
                    VALUES (:productName, :description, :category, :origin, :status)";
            
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                'productName' => $productData['productName'],
                'description' => $productData['description'],
                'category' => $productData['category'],
                'origin' => $productData['origin'],
                'status' => 'ON_SALE'
            ]);

            if (!$result) {
                throw new \Exception('Không thể tạo sản phẩm');
            }

            return $this->db->lastInsertId();
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi database khi tạo sản phẩm: ' . $e->getMessage());
        }
    }

    public function update($id, $data)
    {
        $sql = "UPDATE {$this->table} SET productName = :productName, category = :category, price = :price, status = :status, thumbnail = :thumbnail WHERE id = :id";
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

    public function createVariant($variantData)
    {
        $sql = "INSERT INTO product_variants (product_id, variant_combination, price, stock_quantity, images) 
                VALUES (:productId, :combination, :price, :quantity, :images)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'productId' => $variantData['productId'],
            'combination' => json_encode($variantData['combination']),
            'price' => $variantData['price'],
            'quantity' => $variantData['quantity'],
            'images' => json_encode($variantData['images'])
        ]);
    }

    public function createBasicPricing($data)
    {
        $sql = "INSERT INTO product_pricing (product_id, price, stock_quantity) 
                VALUES (:productId, :price, :stockQuantity)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            'productId' => $data['productId'],
            'price' => $data['price'],
            'stockQuantity' => $data['stockQuantity']
        ]);
    }
} 