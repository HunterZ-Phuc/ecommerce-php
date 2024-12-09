<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;

class HomeController extends BaseController
{
    private $productModel;
    private $imageModel;
    private $variantModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->imageModel = new ProductImage();
        $this->variantModel = new ProductVariant();
    }

    public function index()
    {
        try {
            // Khởi tạo dữ liệu mặc định
            $data = [
                'title' => 'Trang chủ',
                'products' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'selectedCategories' => [],
                'minPrice' => '',
                'maxPrice' => '',
                'queryString' => '',
                // Thêm danh sách categories từ enum trong database
                'categories' => [
                    ['id' => 'FRUITS', 'name' => 'Trái cây'],
                    ['id' => 'VEGETABLES', 'name' => 'Rau củ'],
                    ['id' => 'GRAINS', 'name' => 'Ngũ cốc'],
                    ['id' => 'OTHERS', 'name' => 'Khác']
                ]
            ];

            // Xử lý phân trang
            $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
            $limit = 10;
            $offset = ($page - 1) * $limit;

            // Mặc định hiển thị tất cả sản phẩm đang bán
            $conditions = ["p.status = 'ON_SALE'"];
            $params = [];

            // Log điều kiện truy vấn
            echo '<script>console.log("Query conditions:", ' . json_encode($conditions) . ');</script>';

            // Đếm tổng số sản phẩm và tính số trang
            $whereClause = !empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '';
            $totalProducts = $this->productModel->count($whereClause, $params);

            // Log tổng số sản phẩm
            echo '<script>console.log("Total products:", ' . $totalProducts . ');</script>';

            // Tính tổng số trang
            $totalPages = max(1, ceil($totalProducts / $limit));
            $page = min($page, $totalPages); // Đảm bảo page không vượt quá totalPages

            // Lấy danh sách sản phẩm với phân trang
            $products = $this->productModel->findWithFilters($conditions, $params, $limit, $offset);

            // Log chi tiết sản phẩm
            echo '<script>console.log("Products data:", ' . json_encode($products) . ');</script>';

            // Lấy thông tin biến thể và ảnh cho mỗi sản phẩm
            foreach ($products as &$product) {
                $product['variants'] = $this->variantModel->findAllByProductId($product['id']);
                if (empty($product['mainImage'])) {
                    $product['mainImage'] = '/assets/images/no-image.png';
                }
            }

            // Log dữ liệu sau khi thêm variants
            echo '<script>console.log("Products with variants:", ' . json_encode($products) . ');</script>';

            // Cập nhật dữ liệu
            $data['products'] = $products;
            $data['totalPages'] = $totalPages;
            $data['currentPage'] = $page;

            // Log dữ liệu cuối cùng
            echo '<script>console.log("Final data:", ' . json_encode($data) . ');</script>';

            // Render view với dữ liệu
            $this->view('home/index', $data);

        } catch (\Exception $e) {
            // Log lỗi chi tiết
            echo '<script>console.error("Error:", ' . json_encode($e->getMessage()) . ');</script>';
            error_log("Home page error: " . $e->getMessage());

            // Render view với thông báo lỗi
            $this->view('home/index', [
                'title' => 'Trang chủ',
                'error' => 'Có lỗi xảy ra khi tải dữ liệu'
            ]);
        }
    }

    public function productDetail($id)
    {
        try {
            // Lấy thông tin sản phẩm
            $product = $this->productModel->findById($id);

            if (!$product) {
                throw new \Exception("Không tìm thấy sản phẩm");
            }

            // Debug thông tin sản phẩm
            error_log("Product data: " . json_encode($product));

            // Lấy các biến thể của sản phẩm
            $product['variants'] = $this->variantModel->findAllByProductId($id);

            // Lấy tất cả ảnh của sản phẩm
            $product['images'] = $this->imageModel->findByProductId($id);

            // Tìm ảnh thumbnail (ảnh chính) từ mảng images
            $mainImage = array_filter($product['images'], function ($img) {
                return $img['isThumbnail'] == '1';
            });

            // Nếu có ảnh thumbnail, sử dụng nó làm ảnh chính
            if (!empty($mainImage)) {
                $product['mainImage'] = reset($mainImage)['imageUrl'];
            } else {
                $product['mainImage'] = '/assets/images/no-image.png';
            }

            $data = [
                'title' => $product['productName'],
                'product' => $product
            ];

            // Log dữ liệu để debug
            echo '<script>console.log("Product data:", ' . json_encode($product) . ');</script>';

            // Render view
            $this->view('home/productDetail', $data);

        } catch (\Exception $e) {
            error_log("Product detail error: " . $e->getMessage());
            header('Location: /ecommerce-php/public/?error=' . urlencode('Không thể tải thông tin sản phẩm'));
            exit;
        }
    }
}