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

    // public function index()
    // {
    //     $products = $this->productModel->findAll();
    //     $this->view('employee/ProductManagement/index', [
    //         'title' => 'Quản lý Sản phẩm',
    //         'products' => $products
    //     ]);
    // }
    public function index()
    {
        $products = $this->productModel->findAll();

        foreach ($products as &$product) {
            // Lấy tất cả ảnh của sản phẩm, bao gồm cả ảnh chính
            $product['images'] = $this->productImageModel->findByProductId($product['id']);

            // Lấy các biến thể
            $product['variants'] = $this->variantModel->findByProductId($product['id']);

            foreach ($product['variants'] as &$variant) {
                $variant['images'] = $this->productImageModel->getImagesByVariantId($variant['id']);

                $stmt = $this->db->prepare("
                    SELECT 
                        vt.id as typeId,
                        vt.name as typeName,
                        vv.id as valueId,
                        vv.value
                    FROM variant_combinations vc
                    JOIN variant_values vv ON vc.variantValueId = vv.id
                    JOIN variant_types vt ON vv.variantTypeId = vt.id
                    WHERE vc.productVariantId = :variantId
                    ORDER BY vt.id
                ");
                $stmt->execute(['variantId' => $variant['id']]);
                $variant['combinations'] = $stmt->fetchAll();
            }
        }

        $this->view('employee/ProductManagement/index', [
            'title' => 'Quản lý Sản phẩm',
            'products' => $products,
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
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

                $productId = $this->productModel->create($productData);
                if (!$productId) {
                    throw new Exception("Không thể tạo sản phẩm");
                }

                // 2. Xử lý ảnh sản phẩm chung
                if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
                    $uploadedImages = $this->uploadMultipleImages($_FILES['images']);
                    foreach ($uploadedImages as $imageUrl) {
                        $this->productImageModel->create([
                            'productId' => $productId,
                            'variantId' => null,
                            'imageUrl' => $imageUrl,
                            'isThumbnail' => true
                        ]);
                    }
                }

                // 3. Xử lý biến thể và ảnh biến thể
                if (!isset($_POST['variant_combinations']) || empty($_POST['variant_combinations'])) {
                    throw new Exception("Sản phẩm phải có ít nhất một biến thể");
                }

                foreach ($_POST['variant_combinations'] as $index => $combination) {
                    // Kiểm tra dữ liệu biến thể
                    if (empty($combination['price']) || empty($combination['quantity'])) {
                        throw new Exception("Giá và số lượng của biến thể không được để trống");
                    }

                    // Tạo biến thể
                    $variantId = $this->variantModel->create([
                        'productId' => $productId,
                        'price' => $combination['price'],
                        'quantity' => $combination['quantity']
                    ]);

                    // Xử lý ảnh cho biến thể
                    if (
                        isset($_FILES['variant_combinations']['name'][$index]['image'])
                        && !empty($_FILES['variant_combinations']['name'][$index]['image'])
                    ) {

                        $variantImage = [
                            'name' => $_FILES['variant_combinations']['name'][$index]['image'],
                            'type' => $_FILES['variant_combinations']['type'][$index]['image'],
                            'tmp_name' => $_FILES['variant_combinations']['tmp_name'][$index]['image'],
                            'error' => $_FILES['variant_combinations']['error'][$index]['image'],
                            'size' => $_FILES['variant_combinations']['size'][$index]['image']
                        ];

                        $imageUrl = $this->uploadSingleImage($variantImage);

                        if ($imageUrl) {
                            $this->productImageModel->create([
                                'productId' => $productId,
                                'variantId' => $variantId,
                                'imageUrl' => $imageUrl,
                                'isThumbnail' => false
                            ]);
                        }
                    }

                    // Xử lý các giá trị biến thể
                    foreach ($combination as $type => $value) {
                        if ($type !== 'price' && $type !== 'quantity' && $type !== 'image') {
                            $variantTypeId = $this->variantTypeModel->findOrCreate([
                                'productId' => $productId,
                                'name' => $type
                            ]);

                            $variantValueId = $this->variantValueModel->createVariantValue(
                                $variantTypeId,
                                $value
                            );

                            $this->variantCombinationModel->create([
                                'productVariantId' => $variantId,
                                'variantValueId' => $variantValueId
                            ]);
                        }
                    }
                }

                $this->db->commit();

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Thêm sản phẩm thành công'
                ]);
                exit; // Đảm bảo dừng thực thi ngay lập tức
            } catch (Exception $e) {
                $this->db->rollBack();
                header('Content-Type: application/json');
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
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
        try {
            if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
                throw new Exception("Lỗi upload file");
            }

            // Kiểm tra kích thước file (giới hạn 5MB)
            if ($file['size'] > 5 * 1024 * 1024) {
                throw new Exception("File quá lớn (giới hạn 5MB)");
            }

            // Kiểm tra loại file
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Chỉ chấp nhận file ảnh (JPG, PNG, GIF)");
            }

            $targetDir = __DIR__ . '/../../../public/uploads/products/';
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $fileName = uniqid() . '_' . time() . '.' . $extension;
            $targetPath = $targetDir . $fileName;

            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception("Không thể lưu file");
            }

            return '/uploads/products/' . $fileName;
        } catch (Exception $e) {
            error_log("Upload error: " . $e->getMessage());
            throw new Exception("Lỗi khi upload ảnh: " . $e->getMessage());
        }
    }

    private function handleImageUpload($file)
    {
        try {
            // Kiểm tra file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                return false;
            }

            // Tạo thư mục nếu chưa tồn tại
            $uploadDir = __DIR__ . '/../../../public/uploads/products/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo tên file mới
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $extension;
            $targetPath = $uploadDir . $newFileName;

            // Upload file
            if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                return '/uploads/products/' . $newFileName;
            }

            return false;
        } catch (Exception $e) {
            error_log("Upload error: " . $e->getMessage());
            return false;
        }
    }

    public function edit($id)
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            header('Content-Type: application/json');
            $this->db->beginTransaction();

            // 1. Khởi tạo mảng productData trước
            $productData = [];

            // 2. Kiểm tra và thêm các trường vào mảng cập nhật
            if (isset($_POST['productName'])) {
                $productData['productName'] = $_POST['productName'];
            }

            if (isset($_POST['category'])) {
                $productData['category'] = $_POST['category'];
            }

            if (isset($_POST['salePercent'])) {
                $productData['salePercent'] = $_POST['salePercent'];
            }

            if (isset($_POST['description'])) {
                $productData['description'] = $_POST['description'];
            }

            // 3. Kiểm tra status và thêm vào mảng productData
            if (isset($_POST['status'])) {
                $status = $_POST['status'];
                $validStatuses = ['ON_SALE', 'SUSPENDED', 'OUT_OF_STOCK'];

                if (!in_array($status, $validStatuses)) {
                    throw new Exception("Invalid status value: " . $status);
                }

                $productData['status'] = $_POST['status'];
            }

            // 4. Cập nhật nếu có dữ liệu
            if (!empty($productData)) {
                $result = $this->productModel->update($id, $productData);
                if (!$result) {
                    throw new Exception("Không thể cập nhật thông tin sản phẩm");
                }
            }

            // Xử lý ảnh chính của sản phẩm
            if (isset($_FILES['mainImage']) && $_FILES['mainImage']['error'] === UPLOAD_ERR_OK) {
                // Xóa ảnh chính cũ nếu có
                $oldMainImage = $this->productImageModel->findMainImage($id);
                if ($oldMainImage) {
                    $oldPath = __DIR__ . '/../../../public' . $oldMainImage['imageUrl'];
                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                    }
                    $this->productImageModel->delete($oldMainImage['id']);
                }

                // Upload ảnh mới
                $mainImageFile = $_FILES['mainImage'];
                $mainImageUrl = $this->uploadSingleImage($mainImageFile);

                // Lưu thông tin ảnh mới
                $this->productImageModel->create([
                    'productId' => $id,
                    'variantId' => null, // Đây là ảnh chính nên variantId = null
                    'imageUrl' => $mainImageUrl,
                    'isThumbnail' => true
                ]);
            }

            // 2. Cập nhật biến thể
            if (!empty($_POST['variants'])) {
                foreach ($_POST['variants'] as $variantId => $variantData) {
                    // Cập nhật giá và số lượng
                    $updateData = [];
                    if (isset($variantData['price'])) {
                        $updateData['price'] = floatval($variantData['price']);
                    }
                    if (isset($variantData['quantity'])) {
                        $updateData['quantity'] = intval($variantData['quantity']);
                    }

                    if (!empty($updateData)) {
                        $this->variantModel->update($variantId, $updateData);
                    }

                    // 3. Xử lý upload ảnh
                    if (
                        isset($_FILES['variants']['name'][$variantId]['image'])
                        && $_FILES['variants']['error'][$variantId]['image'] === UPLOAD_ERR_OK
                    ) {

                        $file = [
                            'name' => $_FILES['variants']['name'][$variantId]['image'],
                            'type' => $_FILES['variants']['type'][$variantId]['image'],
                            'tmp_name' => $_FILES['variants']['tmp_name'][$variantId]['image'],
                            'error' => $_FILES['variants']['error'][$variantId]['image'],
                            'size' => $_FILES['variants']['size'][$variantId]['image']
                        ];

                        // Kiểm tra và tạo thư mục nếu chưa tồn tại
                        $uploadDir = __DIR__ . '/../../../public/uploads/products/';
                        if (!file_exists($uploadDir)) {
                            mkdir($uploadDir, 0777, true);
                        }

                        // Tạo tên file mới
                        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
                        $newFileName = uniqid() . '.' . $extension;
                        $targetPath = $uploadDir . $newFileName;

                        // Upload file mới
                        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
                            // Xóa ảnh cũ
                            $oldImage = $this->productImageModel->findByVariantId($variantId);
                            if ($oldImage) {
                                $oldPath = __DIR__ . '/../../../public' . $oldImage['imageUrl'];
                                if (file_exists($oldPath)) {
                                    unlink($oldPath);
                                }
                                // Xóa record cũ trong database
                                $this->productImageModel->delete($oldImage['id']);
                            }

                            // Thêm ảnh mới vào database
                            $this->productImageModel->create([
                                'productId' => $id,
                                'variantId' => $variantId,
                                'imageUrl' => '/uploads/products/' . $newFileName,
                                'isThumbnail' => false
                            ]);
                        }
                    }
                }
            }

            $this->db->commit();

            die(json_encode([
                'success' => true,
                'message' => 'Cập nhật sản phẩm thành công'
            ]));

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Edit error: " . $e->getMessage());
            http_response_code(500);
            die(json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]));
        }
    }

    public function delete($id)
    {
        try {
            $this->db->beginTransaction();

            // 1. Xóa tất cả ảnh sản phẩm và biến thể
            $images = $this->productImageModel->findByProductId($id);
            foreach ($images as $image) {
                $imagePath = __DIR__ . '/../../../public' . $image['imageUrl'];
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
                $this->productImageModel->delete($image['id']);
            }

            // 2. Xóa tất cả variant combinations
            $variants = $this->variantModel->findByProductId($id);
            foreach ($variants as $variant) {
                $this->variantCombinationModel->deleteByVariantId($variant['id']);
            }

            // 3. Xóa tất cả variant values và variant types
            $variantTypes = $this->variantTypeModel->findByProductId($id);
            foreach ($variantTypes as $type) {
                $this->variantValueModel->deleteByTypeId($type['id']);
            }
            $this->variantTypeModel->deleteByProductId($id);

            // 4. Xóa tất cả variants
            $this->variantModel->deleteByProductId($id);

            // 5. Cuối cùng xóa sản phẩm
            $this->productModel->delete($id);

            $this->db->commit();
            echo json_encode([
                'success' => true,
                'message' => 'Xóa sản phẩm thành công'
            ]);
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
            exit;
        }
    }
}
