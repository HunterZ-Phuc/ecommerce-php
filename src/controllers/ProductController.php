<?php
namespace Controllers;

use Services\ProductService;
use Exception;

class ProductController {
    private $productService;
    
    public function __construct() {
        $this->productService = new ProductService();
    }
    
    public function index() {
        try {
            $products = $this->productService->getAllProducts();
            require_once 'views/management/product/index.php';
        } catch (Exception $e) {
            $error = $e->getMessage();
            require_once 'views/error.php';
        }
    }

    public function create() {
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                // Thêm validate dữ liệu đầu vào
                $this->validateProductData($_POST);
                
                $this->productService->startTransaction();
                
                // Thêm xử lý upload ảnh sản phẩm
                $images = $this->handleProductImages($_FILES);
                
                $productData = array_merge($_POST, ['images' => $images]);
                $productId = $this->productService->createProduct($productData);
                
                // Thêm biến thể nếu có
                if (isset($_POST['hasVariants']) && $_POST['hasVariants'] === 'true') {
                    $this->productService->addProductVariants($productId, [
                        'combinations' => $_POST['variant_combinations'],
                        'prices' => $_POST['variant_prices'], 
                        'quantities' => $_POST['variant_quantities']
                    ]);
                }
                
                $this->productService->commitTransaction();
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'productId' => $productId
                ]);
                exit;
            }
            
            require_once 'views/management/product/create.php';
        } catch (Exception $e) {
            $this->productService->rollbackTransaction();
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
            exit;
        }
    }

    // Thêm các phương thức mới
    private function validateProductData($data) {
        // Thêm validation logic
    }

    private function handleProductImages($files) {
        // Thêm xử lý upload ảnh
    }
}
