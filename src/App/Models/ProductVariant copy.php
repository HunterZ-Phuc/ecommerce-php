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
        $sql = "INSERT INTO product_variants (productId, price, quantity) 
                VALUES (:productId, :price, :quantity)";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            'productId' => $data['productId'],
            'price' => $data['price'],
            'quantity' => $data['quantity']
        ]);
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