<?php
// Nếu có namespace thì phải khai báo use ở file gọi đến
namespace Controllers;

use Exception;

class ProductController {
    private $conn;
    private $transaction_started = false;
    
    public function __construct($connection) {
        $this->conn = $connection;
    }
    
    public function startTransaction() {
        if (!$this->transaction_started) {
            $this->conn->begin_transaction();
            $this->transaction_started = true;
            return true;
        }
        return false;
    }
    
    public function commitTransaction() {
        if ($this->transaction_started) {
            $this->conn->commit();
            $this->transaction_started = false;
            return true;
        }
        return false;
    }
    
    public function rollbackTransaction() {
        if ($this->transaction_started) {
            $this->conn->rollback();
            $this->transaction_started = false;
            return true;
        }
        return false;
    }
    
    public function getAllProducts() {
        $query = "SELECT p.*, 
                        (SELECT imageUrl FROM product_images 
                         WHERE productId = p.id AND isThumbnail = 1 
                         LIMIT 1) as thumbnail
                 FROM products p
                 ORDER BY p.createdAt DESC";
        
        $result = mysqli_query($this->conn, $query);
        $products = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        return $products;
    }
    
    public function addProduct($data) {
        try {
            // Đảm bảo set header JSON trước khi output bất kỳ nội dung nào
            header('Content-Type: application/json; charset=utf-8');
            
            // Validate dữ liệu đầu vào
            if (empty($data['productName']) || empty($data['category'])) {
                throw new Exception('Thiếu thông tin sản phẩm bắt buộc');
            }

            // Thêm sản phẩm vào database
            $sql = "INSERT INTO products (productName, category, description, price, stockQuantity, salePercent, origin, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, 'ON_SALE')";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssdiis",
                $data['productName'],
                $data['category'],
                $data['description'],
                $data['hasVariants'] ? 0 : ($data['price'] ?? 0),
                $data['hasVariants'] ? 0 : ($data['stockQuantity'] ?? 0),
                $data['salePercent'] ?? 0,
                $data['origin'] ?? 'Việt Nam'
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm sản phẩm: " . $stmt->error);
            }
            
            $productId = $this->conn->insert_id;
            
            // Xử lý hình ảnh sản phẩm
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $this->handleProductImages($productId, $_FILES['images']);
            }
            
            echo json_encode([
                'success' => true,
                'productId' => $productId,
                'message' => 'Thêm sản phẩm thành công'
            ]);
            exit; // Thêm exit để đảm bảo không có output khác
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit; // Thêm exit để đảm bảo không có output khác
        }
    }
    
    public function updateProduct($data) {
        try {
            $this->conn->begin_transaction();
            
            $sql = "UPDATE products SET 
                    productName = ?, 
                    category = ?,
                    description = ?,
                    price = ?,
                    salePercent = ?,
                    stockQuantity = ?,
                    status = ?
                    WHERE id = ?";
                    
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssddisi",
                $data['productName'],
                $data['category'],
                $data['description'],
                $data['price'],
                $data['salePercent'],
                $data['stockQuantity'],
                $data['status'],
                $data['id']
            );
            
            $stmt->execute();
            
            // Xử lý cập nhật hình ảnh nếu có
            if (isset($_FILES['images'])) {
                $this->handleProductImages($data['id'], $_FILES['images']);
            }
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    public function deleteProduct($id) {
        try {
            $this->conn->begin_transaction();
            
            // Xóa hình ảnh liên quan
            $sql = "DELETE FROM product_images WHERE productId = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            // Xóa sản phẩm
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            $this->conn->commit();
            return true;
        } catch (Exception $e) {
            $this->conn->rollback();
            throw $e;
        }
    }
    
    private function handleProductImages($productId, $images) {
        $uploadDir = 'uploads/products/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($images['tmp_name'] as $key => $tmp_name) {
            if ($images['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '_' . basename($images['name'][$key]);
                $uploadPath = $uploadDir . $fileName;
                
                if (move_uploaded_file($tmp_name, $uploadPath)) {
                    $sql = "INSERT INTO product_images (productId, imageUrl, isThumbnail) 
                            VALUES (?, ?, ?)";
                    $stmt = $this->conn->prepare($sql);
                    $isThumbnail = ($key === 0) ? 1 : 0;
                    $stmt->bind_param("isi", $productId, $uploadPath, $isThumbnail);
                    
                    if (!$stmt->execute()) {
                        throw new Exception("Lỗi khi lưu thông tin ảnh");
                    }
                } else {
                    throw new Exception("Lỗi khi upload ảnh");
                }
            }
        }
    }
    
    private function handleProductVariants($productId, $variants) {
        foreach ($variants['combinations'] as $index => $combinationJson) {
            $combination = json_decode($combinationJson, true);
            
            // Thêm biến thể sản phẩm
            $sql = "INSERT INTO product_variants (productId, price, quantity) 
                    VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("idi", 
                $productId,
                $variants['prices'][$index],
                $variants['quantities'][$index]
            );
            $stmt->execute();
            $variantId = $this->conn->insert_id;
            
            // Thêm các giá trị biến thể
            foreach ($combination as $variantInfo) {
                // Thêm loại biến thể nếu chưa có
                $sql = "INSERT INTO variant_types (productId, name) 
                        VALUES (?, ?) 
                        ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("is", $productId, $variantInfo['type']);
                $stmt->execute();
                $typeId = $stmt->insert_id;
                
                // Thêm giá trị biến thể
                $sql = "INSERT INTO variant_values (variantTypeId, value) 
                        VALUES (?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("is", $typeId, $variantInfo['value']);
                $stmt->execute();
                $valueId = $this->conn->insert_id;
                
                // Thêm kết hợp biến thể
                $sql = "INSERT INTO variant_combinations (productVariantId, variantValueId) 
                        VALUES (?, ?)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bind_param("ii", $variantId, $valueId);
                $stmt->execute();
            }
            
            // Xử lý ảnh biến thể nếu có
            if (isset($_FILES["variant_images_$index"]) && 
                $_FILES["variant_images_$index"]['error'] === UPLOAD_ERR_OK) {
                $this->handleVariantImage($variantId, $_FILES["variant_images_$index"]);
            }
        }
    }
    
    private function handleVariantImage($variantId, $image) {
        $uploadDir = 'uploads/variants/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($image['name']);
        $uploadPath = $uploadDir . $fileName;
        
        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            $sql = "INSERT INTO product_images (productId, variantId, imageUrl) 
                    VALUES ((SELECT productId FROM product_variants WHERE id = ?), ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iis", $variantId, $variantId, $uploadPath);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi lưu thông tin ảnh biến thể");
            }
        } else {
            throw new Exception("Lỗi khi upload ảnh biến thể");
        }
    }
    
    public function addProductVariants($productId, $variants) {
        try {
            $this->conn->begin_transaction();
            
            $this->handleProductVariants($productId, $variants);
            
            $this->conn->commit();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Thêm biến thể thành công'
            ]);
            exit;
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }
    
    // Các phương thức khác cho update, delete...
}
