<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductImage;
use App\Models\VariantType;
use App\Models\VariantValue;
use App\Models\VariantCombination;
use Core\Database;

use Exception;

class ProductController extends BaseController
{
    private $productModel;
    private $variantModel;
    private $productImageModel;
    private $variantTypeModel;
    private $variantValueModel;
    private $variantCombinationModel;
    private $db;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->variantModel = new ProductVariant();
        $this->productImageModel = new ProductImage();
        $this->variantTypeModel = new VariantType();
        $this->variantValueModel = new VariantValue();
        $this->variantCombinationModel = new VariantCombination();
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        $products = $this->productModel->findAll();
        $this->view('employee/ProductManagement/index', [
            'title' => 'Quản lý Sản phẩm',
            'products' => $products
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Log dữ liệu nhận được
                error_log("Received POST data in controller:");
                error_log(print_r($_POST, true));

                $this->db->beginTransaction();

                // Kiểm tra dữ liệu biến thể
                $hasVariants = isset($_POST['hasVariants']) && $_POST['hasVariants'] === 'on';

                // 1. Lưu thông tin cơ bản sản phẩm
                $productData = [
                    'productName' => $_POST['productName'],
                    'description' => $_POST['description'] ?? '',
                    'category' => $_POST['category'],
                    'origin' => $_POST['origin'],
                    'status' => 'ON_SALE'
                ];

                // Nếu có biến thể, set giá và số lượng mặc định là 0
                if ($hasVariants) {
                    $productData['price'] = 0;
                    $productData['stockQuantity'] = 0;
                } else {
                    // Validate giá và số lượng cho sản phẩm không có biến thể
                    if (empty($_POST['price']) || empty($_POST['stockQuantity'])) {
                        throw new Exception("Giá và số lượng không được để trống");
                    }
                    $productData['price'] = $_POST['price'];
                    $productData['stockQuantity'] = $_POST['stockQuantity'];
                }

                // Debug: In ra dữ liệu sản phẩm
                error_log("Product Data: " . print_r($productData, true));

                $productId = $this->productModel->create($productData);
                if (!$productId) {
                    throw new Exception("Không thể tạo sản phẩm");
                }

                // Xử lý ảnh sản phẩm chung
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $uploadedImages = $this->uploadMultipleImages($_FILES['images']);
                    foreach ($uploadedImages as $imageUrl) {
                        $this->productImageModel->create([
                            'productId' => $productId,
                            'variantId' => null,
                            'imageUrl' => $imageUrl,
                            'isThumbnail' => false
                        ]);
                    }
                }

                // 2. Xử lý biến thể nếu có
                if ($hasVariants) {
                    if (empty($_POST['variant_types']) || empty($_POST['variant_values'])) {
                        throw new Exception("Thiếu thông tin biến thể");
                    }

                    // Xử lý variant types và values
                    foreach ($_POST['variant_types'] as $index => $typeName) {
                        if (empty($typeName))
                            continue;

                        $typeId = $this->variantTypeModel->create([
                            'productId' => $productId,
                            'name' => $typeName
                        ]);

                        // Xử lý variant values
                        if (!empty($_POST['variant_values'][$index])) {
                            foreach ($_POST['variant_values'][$index] as $value) {
                                if (empty($value))
                                    continue;

                                $variantValueId = $this->variantValueModel->createVariantValue($typeId, $value);
                            }
                        }
                    }

                    // Xử lý combinations
                    if (!empty($_POST['variant_combinations'])) {
                        foreach ($_POST['variant_combinations'] as $combination) {
                            // Validate giá và số lượng của biến thể
                            $price = $combination['price'] ?? null;
                            $quantity = $combination['quantity'] ?? null;

                            if (!$price || !$quantity || $price < 0 || $quantity < 0) {
                                throw new Exception("Giá và số lượng biến thể không hợp lệ");
                            }

                            // Tạo variant
                            $variantId = $this->variantModel->create([
                                'productId' => $productId,
                                'price' => $price,
                                'quantity' => $quantity
                            ]);

                            if (!$variantId) {
                                throw new Exception("Không thể tạo biến thể sản phẩm");
                            }

                            // Xử lý ảnh cho biến thể
                        if (isset($_FILES['variant_combinations']['name'][$index]['image'])) {
                            $variantImage = [
                                'name' => $_FILES['variant_combinations']['name'][$index]['image'],
                                'type' => $_FILES['variant_combinations']['type'][$index]['image'],
                                'tmp_name' => $_FILES['variant_combinations']['tmp_name'][$index]['image'],
                                'error' => $_FILES['variant_combinations']['error'][$index]['image'],
                                'size' => $_FILES['variant_combinations']['size'][$index]['image']
                            ];

                            $imageUrl = $this->uploadSingleImage($variantImage);
                            
                            // Debug: In ra đường dẫn ảnh
                            error_log("Uploaded image URL: " . $imageUrl);

                            if ($imageUrl) {
                                $this->productImageModel->create([
                                    'productId' => $productId,
                                    'variantId' => $variantId,
                                    'imageUrl' => $imageUrl,
                                    'isThumbnail' => false
                                ]);
                                }
                            }

                            $this->variantCombinationModel->createVariantCombination($variantId, $variantValueId);
                        }
                    }
                }

                // Xử lý ảnh và commit transaction
                // ... (phần code xử lý ảnh giữ nguyên)

                if ($this->db->commit()) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Thêm sản phẩm thành công',
                        'data' => $_POST
                    ]);
                    exit;
                }
            } catch (Exception $e) {
                $this->db->rollBack();
                error_log("Lỗi khi tạo sản phẩm: " . $e->getMessage());

                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage(),
                    'data' => $_POST
                ]);
                exit;
            }
        }
    }

    private function uploadMultipleImages($files)
    {
        $uploadedFiles = [];
        $targetDir = __DIR__ . '/../../../public/uploads/products/';

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        // Kiểm tra và xử lý từng file
        for ($i = 0; $i < count($files['name']); $i++) {
            if ($files['error'][$i] === UPLOAD_ERR_OK) {
                $fileName = uniqid() . '_' . basename($files['name'][$i]);
                $targetPath = $targetDir . $fileName;

                if (move_uploaded_file($files['tmp_name'][$i], $targetPath)) {
                    $uploadedFiles[] = '/uploads/products/' . $fileName;
                }
            }
        }

        return $uploadedFiles;
    }

    private function uploadSingleImage($file)
    {
        $targetDir = __DIR__ . '/../../../public/uploads/products/';

        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0777, true);
        }

        $fileName = uniqid() . '_' . basename($file['name']);
        $targetPath = $targetDir . $fileName;

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            return '/uploads/products/' . $fileName;
        }

        return null;
    }

    public function edit($id)
    {
        $product = $this->productModel->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'productName' => $_POST['productName'],
                'category' => $_POST['category'],
                'price' => $_POST['price'],
                'status' => $_POST['status']
            ];
            $this->productModel->update($id, $data);
            header('Location: /php-mvc/employee/product-management');
            exit;
        }

        $this->view('employee/ProductManagement/edit', [
            'title' => 'Sửa Sản phẩm',
            'product' => $product
        ]);
    }

    public function delete($id)
    {
        $this->productModel->delete($id);
        header('Location: /php-mvc/employee/product-management');
        exit;
    }
}