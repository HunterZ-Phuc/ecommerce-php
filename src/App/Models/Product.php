<?php

namespace App\Models;

use PDOException;
use Exception;

class Product extends BaseModel
{
    protected $table = 'products';

    // Lấy tất cả sản phẩm
    public function findAll()
    {
        $sql = "SELECT * FROM {$this->table}";
        return $this->db->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo ID 
    public function findById($id)
    {
        $sql = "SELECT * FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch(\PDO::FETCH_ASSOC);
    }

    // Tạo sản phẩm
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

    // Cập nhật sản phẩm
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

    // Xóa sản phẩm
    public function delete($id)
    {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute(['id' => $id]);
    }

    // Lấy sản phẩm theo các điều kiện
    public function findWithFilters($conditions = [], $params = [], $limit = null, $offset = null)
    {
        try {
            $sql = "SELECT DISTINCT p.*, 
                    MIN(pv.price) as price,
                    SUM(pv.quantity) as totalQuantity,
                    pi.imageUrl as mainImage,
                    COALESCE(SUM(oi.quantity), 0) as sold
                    FROM products p
                    LEFT JOIN product_variants pv ON p.id = pv.productId
                    LEFT JOIN product_images pi ON p.id = pi.productId AND pi.isThumbnail = true
                    LEFT JOIN order_items oi ON pv.id = oi.variantId";

            if (!empty($conditions)) {
                $sql .= " WHERE " . implode(' AND ', $conditions);
            }

            $sql .= " GROUP BY p.id";
            
            // Thêm ORDER BY để sắp xếp kết quả
            $sql .= " ORDER BY p.createdAt DESC";

            if ($limit !== null) {
                $sql .= " LIMIT :limit";
                if ($offset !== null) {
                    $sql .= " OFFSET :offset";
                }
            }

            $stmt = $this->db->prepare($sql);

            if ($limit !== null) {
                $stmt->bindValue(':limit', (int) $limit, \PDO::PARAM_INT);
                if ($offset !== null) {
                    $stmt->bindValue(':offset', (int) $offset, \PDO::PARAM_INT);
                }
            }

            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi lấy danh sách sản phẩm: ' . $e->getMessage());
        }
    }

    // Đếm sản phẩm theo điều kiện
    public function count($whereClause = '', $params = [])
    {
        try {
            $sql = "SELECT COUNT(DISTINCT p.id) as total
                    FROM products p
                    LEFT JOIN product_variants pv ON p.id = pv.productId";

            if (!empty($whereClause)) {
                $sql .= " $whereClause";
            }

            $stmt = $this->db->prepare($sql);

            if (!empty($params)) {
                foreach ($params as $key => $value) {
                    $stmt->bindValue($key, $value);
                }
            }

            $stmt->execute();
            $result = $stmt->fetch(\PDO::FETCH_ASSOC);
            return (int) $result['total'];
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi đếm sản phẩm: ' . $e->getMessage());
        }
    }

    // Đếm sản phẩm có ít nhất một biến thể dưới ngưỡng tồn kho (10 sản phẩm)
    public function getLowStockCount($threshold = 10)
    {
        try {
            $sql = "SELECT COUNT(DISTINCT p.id) 
                    FROM products p
                    JOIN product_variants pv ON p.id = pv.productId
                    WHERE pv.quantity > 0 
                    AND pv.quantity <= :threshold
                    AND p.status = 'ON_SALE'";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':threshold', $threshold, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Error getting low stock count: " . $e->getMessage());
            return 0;
        }
    }

    // Lấy biến thể theo ID sản phẩm
    public function getVariantsByProductId($productId)
    {
        $sql = "SELECT * FROM product_variants WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    // Lấy sản phẩm theo trang và tìm kiếm
    public function findWithPagination($page = 1, $limit = 10, $search = '')
    {
        try {
            $offset = ($page - 1) * $limit;
            $params = [];
            
            $sql = "SELECT DISTINCT p.* 
                    FROM products p
                    LEFT JOIN product_variants pv ON p.id = pv.productId";

            if (!empty($search)) {
                $sql .= " WHERE p.productName LIKE :search";
                $params[':search'] = "%$search%";
            }

            $sql .= " ORDER BY p.createdAt DESC LIMIT :limit OFFSET :offset";
            $params[':limit'] = $limit;
            $params[':offset'] = $offset;

            $stmt = $this->db->prepare($sql);
            foreach ($params as $key => $value) {
                if ($key === ':limit' || $key === ':offset') {
                    $stmt->bindValue($key, $value, \PDO::PARAM_INT);
                } else {
                    $stmt->bindValue($key, $value);
                }
            }
            
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\PDOException $e) {
            throw new \Exception('Lỗi khi tìm kiếm sản phẩm: ' . $e->getMessage());
        }
    }
}