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
            header('Content-Type: application/json; charset=utf-8');
            
            // Validate dữ liệu đầu vào
            $requiredFields = ['productName', 'category', 'origin'];
            foreach ($requiredFields as $field) {
                if (empty($data[$field])) {
                    throw new Exception("Thiếu thông tin bắt buộc: $field");
                }
            }

            // Chuẩn bị dữ liệu
            $productName = $data['productName'];
            $category = $data['category'];
            $description = $data['description'] ?? '';
            $origin = $data['origin'] ?? 'Việt Nam';
            $hasVariants = filter_var($data['hasVariants'], FILTER_VALIDATE_BOOLEAN);
            $price = $hasVariants ? 0 : (float)($data['price'] ?? 0);
            $stockQuantity = $hasVariants ? 0 : (int)($data['stockQuantity'] ?? 0);
            $salePercent = (int)($data['salePercent'] ?? 0);
            $status = 'ON_SALE';

            $sql = "INSERT INTO products (productName, category, description, price, stockQuantity, salePercent, origin, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("sssdiiss",
                $productName,
                $category,
                $description,
                $price,
                $stockQuantity,
                $salePercent,
                $origin,
                $status
            );
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm sản phẩm: " . $stmt->error);
            }
            
            $productId = $this->conn->insert_id;
            
            // Xử lý hình ảnh
            if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                $this->handleProductImages($productId, $_FILES['images']);
            }
            
            echo json_encode([
                'success' => true,
                'productId' => $productId,
                'message' => 'Thêm sản phẩm thành công'
            ]);
            exit;
            
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
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
            // Validate giá và số lượng
            if (!isset($variants['prices'][$index]) || !isset($variants['quantities'][$index])) {
                throw new Exception('Thiếu thông tin giá hoặc số lượng cho biến thể');
            }
            
            $price = floatval($variants['prices'][$index]);
            $quantity = intval($variants['quantities'][$index]);
            
            if ($price < 0 || $quantity < 0) {
                throw new Exception('Giá và số lượng không được âm');
            }
            
            $combination = json_decode($combinationJson, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new Exception('Dữ liệu biến thể không hợp lệ');
            }
            
            // Thêm biến thể vào database
            $sql = "INSERT INTO product_variants (productId, price, quantity) VALUES (?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("idi", $productId, $price, $quantity);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi thêm biến thể: " . $stmt->error);
            }
            
            $variantId = $this->conn->insert_id;
            
            // Xử lý ảnh biến thể nếu có
            if (isset($_FILES["variant_images_{$index}"]) && !empty($_FILES["variant_images_{$index}"]['name'])) {
                $this->handleVariantImage($variantId, $_FILES["variant_images_{$index}"]);
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
    
    public function addProductVariants($productId, $variantData) {
        try {
            // Validate dữ liệu đầu vào
            if (empty($variantData['combinations']) || 
                empty($variantData['prices']) || 
                empty($variantData['quantities'])) {
                throw new Exception('Thiếu thông tin biến thể');
            }

            // Parse combinations từ JSON string
            $combinations = json_decode($variantData['combinations'], true);
            $prices = explode(',', $variantData['prices']);
            $quantities = explode(',', $variantData['quantities']);

            // Tạo map để theo dõi variant_types và variant_values đã tồn tại
            $variantTypeMap = [];
            $variantValueMap = [];

            // Lưu variant_types và variant_values
            foreach ($combinations as $combo) {
                foreach ($combo as $variant) {
                    $typeName = $variant['type'];
                    
                    // Thêm variant_type nếu chưa tồn tại
                    if (!isset($variantTypeMap[$typeName])) {
                        $sql = "INSERT INTO variant_types (productId, name) VALUES (?, ?)";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->bind_param("is", $productId, $typeName);
                        $stmt->execute();
                        $variantTypeMap[$typeName] = $stmt->insert_id;
                    }

                    $typeId = $variantTypeMap[$typeName];
                    $value = $variant['value'];

                    // Thêm variant_value nếu chưa tồn tại
                    $valueKey = $typeId . '_' . $value;
                    if (!isset($variantValueMap[$valueKey])) {
                        $sql = "INSERT INTO variant_values (variantTypeId, value) VALUES (?, ?)";
                        $stmt = $this->conn->prepare($sql);
                        $stmt->bind_param("is", $typeId, $value);
                        $stmt->execute();
                        $variantValueMap[$valueKey] = $stmt->insert_id;
                    }
                }
            }

            // Thêm product_variants và variant_combinations
            foreach ($combinations as $index => $combo) {
                // Thêm product_variant
                $sql = "INSERT INTO product_variants (productId, price, quantity) VALUES (?, ?, ?)";
                $stmt = $this->conn->prepare($sql);
                $price = $prices[$index];
                $quantity = $quantities[$index];
                $stmt->bind_param("idi", $productId, $price, $quantity);
                $stmt->execute();
                $variantId = $stmt->insert_id;

                // Thêm variant_combinations
                foreach ($combo as $variant) {
                    $typeId = $variantTypeMap[$variant['type']];
                    $valueKey = $typeId . '_' . $variant['value'];
                    $valueId = $variantValueMap[$valueKey];

                    $sql = "INSERT INTO variant_combinations (productVariantId, variantValueId) VALUES (?, ?)";
                    $stmt = $this->conn->prepare($sql);
                    $stmt->bind_param("ii", $variantId, $valueId);
                    $stmt->execute();
                }

                // Xử lý ảnh biến thể nếu có
                if (isset($_FILES["variant_images_{$index}"]) && 
                    !empty($_FILES["variant_images_{$index}"]['name'])) {
                    $this->handleVariantImage($variantId, $_FILES["variant_images_{$index}"]);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Lỗi khi thêm biến thể: ' . $e->getMessage());
        }
    }
    
    // Các phương thức khác cho update, delete...
}
