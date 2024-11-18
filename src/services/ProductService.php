<?php
namespace Services;

use Models\Product;
use Models\ProductVariant;
use Models\VariantType;
use Exception;

class ProductService {
    private $db;
    private $transaction_started = false;

    public function __construct() {
        global $con;
        $this->db = $con;
    }
    
    public function createProduct($productData) {
        try {
            // Validate dữ liệu
            $this->validateProductData($productData);
            
            // Tạo sản phẩm mới
            $product = new Product($productData);
            
            // Lưu vào database
            $result = $this->db->insert('products', $product->toArray());
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi tạo sản phẩm: " . $e->getMessage());
        }
    }
    
    public function updateProduct($id, $productData) {
        // Logic cập nhật sản phẩm
    }
    
    public function deleteProduct($id) {
        // Logic xóa sản phẩm
    }
    
    public function updateStock($productId, $quantity) {
        // Logic cập nhật tồn kho
    }
    
    private function validateProductData($data) {
        // Logic validate
    }
}
