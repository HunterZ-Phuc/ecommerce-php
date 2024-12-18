<?php

namespace App\Models;

use PDOException;

use Exception;
//sửa ở đây point 2
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

    //sửa ở đây point 1
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

    public function getDashboardStats()
    {
        try {
            $sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock,
                    SUM(CASE WHEN stock > 0 AND stock <= 10 THEN 1 ELSE 0 END) as low_stock,
                    SUM(CASE WHEN status = 1 THEN 1 ELSE 0 END) as active
                    FROM {$this->table}";
            
            $stmt = $this->db->query($sql);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi lấy thống kê sản phẩm: ' . $e->getMessage());
        }
    }

    public function getLowStockProductsForDashboard($threshold = 10, $limit = 10)
    {
        try {
            $sql = "SELECT id, productName as name, stock, price, status 
                    FROM {$this->table}
                    WHERE stock <= :threshold 
                    ORDER BY stock ASC 
                    LIMIT :limit";
            
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':threshold', $threshold, \PDO::PARAM_INT);
            $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi lấy danh sách sản phẩm sắp hết hàng: ' . $e->getMessage());
        }
    }

    public function getTotalProducts()
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table}";
            return $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi đếm tổng số sản phẩm: ' . $e->getMessage());
        }
    }

    public function getOutOfStockCount()
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE stock = 0";
            return $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi đếm sản phẩm hết hàng: ' . $e->getMessage());
        }
    }

    public function getLowStockCount($threshold = 10)
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE stock > 0 AND stock <= :threshold";
            $stmt = $this->db->prepare($sql);
            $stmt->bindValue(':threshold', $threshold, \PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi đếm sản phẩm sắp hết hàng: ' . $e->getMessage());
        }
    }

    public function getActiveProductCount()
    {
        try {
            $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 1";
            return $this->db->query($sql)->fetchColumn();
        } catch (PDOException $e) {
            throw new Exception('Lỗi khi đếm sản phẩm đang bán: ' . $e->getMessage());
        }
    }

    public function getDashboardTotalProducts()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE deleted_at IS NULL";
        return $this->db->query($sql)->fetchColumn();
    }

    public function getDashboardOutOfStockProducts()
    {
        $sql = "SELECT COUNT(*) FROM product_variants WHERE quantity = 0";
        return $this->db->query($sql)->fetchColumn();
    }

    public function getDashboardLowStockProducts($threshold = 10)
    {
        $sql = "SELECT COUNT(*) FROM product_variants WHERE quantity > 0 AND quantity <= :threshold";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['threshold' => $threshold]);
        return $stmt->fetchColumn();
    }

    public function getDashboardActiveProducts()
    {
        $sql = "SELECT COUNT(*) FROM {$this->table} WHERE status = 'ON_SALE' AND deleted_at IS NULL";
        return $this->db->query($sql)->fetchColumn();
    }

    public function getDashboardLowStockProductsList($threshold = 10, $limit = 10)
    {
        $sql = "SELECT p.*, pv.quantity as stock, pv.price
                FROM {$this->table} p
                JOIN product_variants pv ON p.id = pv.productId
                WHERE p.deleted_at IS NULL 
                AND pv.quantity <= :threshold
                ORDER BY pv.quantity ASC
                LIMIT :limit";
        
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':threshold', $threshold, \PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function getVariantsByProductId($productId)
    {
        $sql = "SELECT * FROM product_variants WHERE productId = :productId";
        $stmt = $this->db->prepare($sql);
        $stmt->execute(['productId' => $productId]);
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }
}