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
                throw new Exception('Không thể tạo sản phẩm');
            }

            return $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new Exception('Lỗi database khi tạo sản phẩm: ' . $e->getMessage());
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
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi cập nhật sản phẩm: ' . $e->getMessage());
        }
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

    public function getAllProducts()
    {
        $sql = "SELECT * FROM products WHERE deleted_at IS NULL";
        return $this->db->query($sql)->fetchAll();
    }

    public function findWithFilters($conditions = [], $params = [], $limit = null, $offset = null)
    {
        $sql = "SELECT p.*, 
                pv.price,
                pi.imageUrl as mainImage
                FROM products p
                LEFT JOIN product_variants pv ON p.id = pv.productId
                LEFT JOIN product_images pi ON p.id = pi.productId AND pi.isThumbnail = true";

        if (!empty($conditions)) {
            $sql .= " WHERE " . implode(' AND ', $conditions);
        }

        $sql .= " GROUP BY p.id ORDER BY p.createdAt DESC";

        if ($limit !== null) {
            $sql .= " LIMIT :limit";
            if ($offset !== null) {
                $sql .= " OFFSET :offset";
            }
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }
        }

        if ($limit !== null) {
            $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
            if ($offset !== null) {
                $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
            }
        }

        $this->lastQuery = $sql;
        $stmt->execute();
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function count($whereClause = '', $params = [])
    {
        $sql = "SELECT COUNT(DISTINCT p.id) as total
                FROM products p
                LEFT JOIN product_variants pv ON p.id = pv.productId";

        if (!empty($whereClause)) {
            $sql .= " $whereClause";
        }

        $stmt = $this->db->prepare($sql);

        if (!empty($params)) {
            foreach ($params as $key => $value) {
                $stmt->bindValue($key + 1, $value);
            }
        }

        $stmt->execute();
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
        return (int) $result['total'];
    }
}