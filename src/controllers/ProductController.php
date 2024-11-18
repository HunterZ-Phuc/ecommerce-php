<?php
namespace Controllers;

use Services\ProductService;
use Exception;

class ProductController {
    private $productService;
    private $uploadDir = '../../../assets/images/uploads/';
    
    public function __construct() {
        $this->productService = new ProductService();
    }
    
    publi                                                                    c function index(): array {
        return $this->productService->getAllProducts();
    }

    public function handleAction() {
        $action = $_POST['action'] ?? '';
        
        switch($action) {
            case 'add_product':
                return $this->addProduct();
            
            case 'update_product':
                return $this->updateProduct();
                
            case 'delete_product':
                return $this->deleteProduct();
                
            case 'get_product':
                return $this->getProduct();
                
            // Các case khác...
        }
    }

    private function addProduct() {
        try {
            $productId = $this->createProduct($_POST, $_FILES);
            echo json_encode([
                'success' => true,
                'productId' => $productId,
                'message' => 'Thêm sản phẩm thành công'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
        }
    }

    private function updateProduct() {
        try {
            $id = $_POST['id'];
            $productData = [
                'productName' => $_POST['productName'],
                'category' => $_POST['category'],
                'description' => $_POST['description'],
                'origin' => $_POST['origin'],
                'price' => $_POST['price'],
                'stockQuantity' => $_POST['stockQuantity'],
                'salePercent' => $_POST['salePercent'],
                'status' => 'ON_SALE'
            ];

            $result = $this->productService->updateProduct($id, $productData);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Cập nhật sản phẩm thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể cập nhật sản phẩm']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function deleteProduct() {
        try {
            $id = $_POST['id'];
            $result = $this->productService->deleteProduct($id);
            
            if ($result) {
                echo json_encode(['success' => true, 'message' => 'Xóa sản phẩm thành công']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không thể xóa sản phẩm']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function getProduct() {
        try {
            $id = $_POST['id'];
            $product = $this->productService->getProduct($id);
            
            if ($product) {
                echo json_encode(['success' => true, 'data' => $product]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    private function createProduct($data, $files) {
        $this->validateProductData($data);
        
        // Xử lý hasVariants
        $hasVariants = isset($data['hasVariants']) && $data['hasVariants'] === 'true';
        if ($hasVariants) {
            $data['price'] = 0;
            $data['stockQuantity'] = 0;
        }

        // Xử lý images nếu có
        $images = [];
        if (isset($files['images']) && !empty($files['images']['name'][0])) {
            $images = $this->handleProductImages($files['images']);
        }

        return $this->productService->createProduct(array_merge($data, ['images' => $images]));
    }

    private function addProductVariants($data, $files) {
        if (!isset($data['productId'])) {
            throw new Exception('Thiếu productId');
        }

        // Validate dữ liệu biến thể
        if (!isset($data['variant_combinations']) || 
            !isset($data['variant_prices']) || 
            !isset($data['variant_quantities'])) {
            throw new Exception('Thiếu thông tin biến thể');
        }

        // Xử lý ảnh biến thể
        $variantImages = [];
        foreach ($files as $key => $file) {
            if (strpos($key, 'variant_images_') === 0) {
                $index = substr($key, strlen('variant_images_'));
                $variantImages[$index] = $this->handleVariantImage($file);
            }
        }

        return $this->productService->addProductVariants(
            $data['productId'],
            [
                'combinations' => $data['variant_combinations'],
                'prices' => $data['variant_prices'],
                'quantities' => $data['variant_quantities'],
                'images' => $variantImages
            ]
        );
    }

    private function validateProductData($data) {
        $requiredFields = ['productName', 'category', 'origin'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception("Thiếu thông tin bắt buộc: $field");
            }
        }
    }

    private function handleProductImages($images) {
        $uploadedImages = [];
        $productUploadDir = $this->uploadDir . 'products/';
        
        if (!file_exists($productUploadDir)) {
            mkdir($productUploadDir, 0777, true);
        }

        foreach ($images['tmp_name'] as $key => $tmp_name) {
            if ($images['error'][$key] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '_' . basename($images['name'][$key]);
                $uploadPath = $productUploadDir . $fileName;

                if (move_uploaded_file($tmp_name, $uploadPath)) {
                    $uploadedImages[] = [
                        'path' => 'assets/images/uploads/products/' . $fileName,
                        'isThumbnail' => ($key === 0)
                    ];
                } else {
                    throw new Exception("Lỗi khi upload ảnh");
                }
            }
        }

        return $uploadedImages;
    }

    private function handleVariantImage($image) {
        $variantUploadDir = $this->uploadDir . 'variants/';
        
        if (!file_exists($variantUploadDir)) {
            mkdir($variantUploadDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($image['name']);
        $uploadPath = $variantUploadDir . $fileName;

        if (move_uploaded_file($image['tmp_name'], $uploadPath)) {
            return 'assets/images/uploads/variants/' . $fileName;
        }

        throw new Exception("Lỗi khi upload ảnh biến thể");
    }
}
