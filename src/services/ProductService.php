<?php
namespace Services;

use Exception;

class ProductService {
    private $db;
    private $transaction_started = false;

    public function __construct() {
        global $con;
        $this->db = $con;
    }

    public function startTransaction() {
        if (!$this->transaction_started) {
            $this->db->begin_transaction();
            $this->transaction_started = true;
            return true;
        }
        return false;
    }

    public function commitTransaction() {
        if ($this->transaction_started) {
            $this->db->commit();
            $this->transaction_started = false;
            return true;
        }
        return false;
    }

    public function rollbackTransaction() {
        if ($this->transaction_started) {
            $this->db->rollback();
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
        
        $result = mysqli_query($this->db, $query);
        $products = [];
        
        while ($row = mysqli_fetch_assoc($result)) {
            $products[] = $row;
        }
        
        return $products;
    }

    public function createProduct($data) {
        try {
            // Validate dữ liệu đầu vào
            $this->validateProductData($data);

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

            $sql = "INSERT INTO products (productName, category, description, price, 
                    stockQuantity, salePercent, origin, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = $this->db->prepare($sql);
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
            
            $productId = $this->db->insert_id;

            // Xử lý hình ảnh nếu có
            if (isset($data['images']) && !empty($data['images'])) {
                $this->saveProductImages($productId, $data['images']);
            }
            
            return $productId;
            
        } catch (Exception $e) {
            throw new Exception("Không thể thêm sản phẩm: " . $e->getMessage());
        }
    }

    public function addProductVariants($productId, $variantData) {
        try {
            $combinations = json_decode($variantData['combinations'], true);
            $prices = explode(',', $variantData['prices']);
            $quantities = explode(',', $variantData['quantities']);

            // Map để theo dõi variant_types và variant_values
            $variantTypeMap = [];
            $variantValueMap = [];

            // Lưu variant_types và variant_values
            foreach ($combinations as $combo) {
                foreach ($combo as $variant) {
                    $typeName = $variant['type'];
                    
                    if (!isset($variantTypeMap[$typeName])) {
                        $sql = "INSERT INTO variant_types (productId, name) VALUES (?, ?)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bind_param("is", $productId, $typeName);
                        $stmt->execute();
                        $variantTypeMap[$typeName] = $stmt->insert_id;
                    }

                    $typeId = $variantTypeMap[$typeName];
                    $value = $variant['value'];
                    $valueKey = $typeId . '_' . $value;

                    if (!isset($variantValueMap[$valueKey])) {
                        $sql = "INSERT INTO variant_values (variantTypeId, value) VALUES (?, ?)";
                        $stmt = $this->db->prepare($sql);
                        $stmt->bind_param("is", $typeId, $value);
                        $stmt->execute();
                        $variantValueMap[$valueKey] = $stmt->insert_id;
                    }
                }
            }

            // Thêm product_variants và variant_combinations
            foreach ($combinations as $index => $combo) {
                $sql = "INSERT INTO product_variants (productId, price, quantity) VALUES (?, ?, ?)";
                $stmt = $this->db->prepare($sql);
                $price = $prices[$index];
                $quantity = $quantities[$index];
                $stmt->bind_param("idi", $productId, $price, $quantity);
                $stmt->execute();
                $variantId = $stmt->insert_id;

                foreach ($combo as $variant) {
                    $typeId = $variantTypeMap[$variant['type']];
                    $valueKey = $typeId . '_' . $variant['value'];
                    $valueId = $variantValueMap[$valueKey];

                    $sql = "INSERT INTO variant_combinations (productVariantId, variantValueId) VALUES (?, ?)";
                    $stmt = $this->db->prepare($sql);
                    $stmt->bind_param("ii", $variantId, $valueId);
                    $stmt->execute();
                }

                // Xử lý ảnh biến thể
                if (isset($variantData['images'][$index])) {
                    $this->saveVariantImage($variantId, $variantData['images'][$index]);
                }
            }

            return true;
        } catch (Exception $e) {
            throw new Exception('Lỗi khi thêm biến thể: ' . $e->getMessage());
        }
    }

    private function validateProductData($data) {
        $requiredFields = ['productName', 'category', 'origin'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Thiếu thông tin bắt buộc: $field");
            }
        }
    }

    private function saveProductImages($productId, $images) {
        foreach ($images as $index => $image) {
            $sql = "INSERT INTO product_images (productId, imageUrl, isThumbnail) VALUES (?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            $isThumbnail = ($index === 0) ? 1 : 0;
            $stmt->bind_param("isi", $productId, $image['path'], $isThumbnail);
            
            if (!$stmt->execute()) {
                throw new Exception("Lỗi khi lưu thông tin ảnh");
            }
        }
    }

    private function saveVariantImage($variantId, $imagePath) {
        $sql = "INSERT INTO product_images (productId, variantId, imageUrl) 
                VALUES ((SELECT productId FROM product_variants WHERE id = ?), ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("iis", $variantId, $variantId, $imagePath);
        
        if (!$stmt->execute()) {
            throw new Exception("Lỗi khi lưu thông tin ảnh biến thể");
        }
    }

    public function updateProduct($id, $data) {
        try {
            $sql = "UPDATE products SET 
                    productName = ?, 
                    category = ?,
                    description = ?,
                    origin = ?,
                    price = ?,
                    stockQuantity = ?,
                    salePercent = ?
                    WHERE id = ?";
                    
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                $data['productName'],
                $data['category'],
                $data['description'],
                $data['origin'],
                $data['price'],
                $data['stockQuantity'],
                $data['salePercent'],
                $id
            ]);
            
            return true;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi cập nhật sản phẩm: " . $e->getMessage());
        }
    }

    public function deleteProduct($id) {
        try {
            $sql = "DELETE FROM products WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return true;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi xóa sản phẩm: " . $e->getMessage());
        }
    }

    public function getProduct($id) {
        try {
            $sql = "SELECT * FROM products WHERE id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            throw new Exception("Lỗi khi lấy thông tin sản phẩm: " . $e->getMessage());
        }
    }
}
