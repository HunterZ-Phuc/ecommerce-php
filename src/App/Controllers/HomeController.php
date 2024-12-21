<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\VariantCombination;

class HomeController extends BaseController
{
    private $productModel;
    private $imageModel;
    private $variantModel;
    private $variantCombinationModel;

    public function __construct()
    {
        parent::__construct();
        $this->productModel = new Product();
        $this->imageModel = new ProductImage();
        $this->variantModel = new ProductVariant();
        $this->variantCombinationModel = new VariantCombination();
    }

    //sửa ở đây point 3
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
                'category' => '',
                'categories' => [
                    ['id' => 'FRUITS', 'name' => 'Trái cây'],
                    ['id' => 'VEGETABLES', 'name' => 'Rau củ'],
                    ['id' => 'GRAINS', 'name' => 'Ngũ cốc'],
                    ['id' => 'OTHERS', 'name' => 'Khác']
                ]
            ];

            // Xử lý phân trang
            $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
            $limit = 12;
            $offset = ($page - 1) * $limit;

            // Mặc định hiển thị tất cả sản phẩm đang bán
            $conditions = ["p.status = 'ON_SALE'"];
            $params = [];

            // Đếm tổng số sản phẩm và tính số trang
            $totalProducts = $this->productModel->count(!empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '', $params);
            $totalPages = max(1, ceil($totalProducts / $limit));
            $page = min($page, $totalPages);

            // Lấy danh sách sản phẩm với phân trang
            $products = $this->productModel->findWithFilters($conditions, $params, $limit, $offset);

            // Lấy thông tin biến thể cho mỗi sản phẩm
            foreach ($products as &$product) {
                $product['variants'] = $this->variantModel->findAllByProductId($product['id']);
                if (empty($product['mainImage'])) {
                    $product['mainImage'] = '/assets/images/no-image.png';
                }
            }

            // Cập nhật dữ liệu
            $data['products'] = $products;
            $data['totalPages'] = $totalPages;
            $data['currentPage'] = $page;

            // Render view với dữ liệu
            $this->view('home/index', $data);

        } catch (\Exception $e) {
            error_log("Home page error: " . $e->getMessage());
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
            $product['variants'] = $this->variantModel->findByProductId($id);

            // Lấy thông tin chi tiết cho mỗi biến thể
            foreach ($product['variants'] as &$variant) {
                // Sử dụng model để lấy thông tin tên biến thể
                $variant['combinations'] = $this->variantCombinationModel->findByVariantId($variant['id']);
            }

            // Lấy tất cả ảnh của sản phẩm
            $product['images'] = $this->imageModel->findByProductId($id);

            // Tìm ảnh chính (mainImage) - ảnh có variantId = null và isThumbnail = true
            $mainImage = null;
            if (!empty($product['images'])) {
                foreach ($product['images'] as $image) {
                    if ($image['variantId'] === null && $image['isThumbnail']) {
                        $mainImage = $image;
                        break;
                    }
                }
            }

            // Gán mainImage
            if ($mainImage) {
                $product['mainImage'] = $mainImage['imageUrl'];
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

    public function search()
    {
        try {
            $query = $_GET['query'] ?? '';
            $category = $_GET['category'] ?? '';
            $minPrice = $_GET['minPrice'] ?? '';
            $maxPrice = $_GET['maxPrice'] ?? '';
            $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
            $limit = 12;
            $offset = ($page - 1) * $limit;

            // Khởi tạo mảng categories
            $categories = [
                ['id' => 'FRUITS', 'name' => 'Trái cây'],
                ['id' => 'VEGETABLES', 'name' => 'Rau củ'],
                ['id' => 'GRAINS', 'name' => 'Ngũ cốc'],
                ['id' => 'OTHERS', 'name' => 'Khác']
            ];

            $conditions = [];
            $params = [];

            // Xử lý tìm kiếm theo tên sản phẩm
            if (!empty($query)) {
                $conditions[] = "p.productName LIKE :query";
                $params[':query'] = "%{$query}%";
            }

            // Lọc theo danh mục
            if (!empty($category)) {
                $conditions[] = "p.category = :category";
                $params[':category'] = $category;
            }

            // Lọc theo khoảng giá
            if (!empty($minPrice)) {
                $conditions[] = "pv.price >= :minPrice";
                $params[':minPrice'] = $minPrice;
            }
            if (!empty($maxPrice)) {
                $conditions[] = "pv.price <= :maxPrice";
                $params[':maxPrice'] = $maxPrice;
            }

            // Chỉ lấy sản phẩm đang bán
            $conditions[] = "p.status = 'ON_SALE'";

            // Thực hiện tìm kiếm
            $products = $this->productModel->findWithFilters($conditions, $params, $limit, $offset);
            $totalProducts = $this->productModel->count(!empty($conditions) ? 'WHERE ' . implode(' AND ', $conditions) : '', $params);
            $totalPages = ceil($totalProducts / $limit);

            // Lấy thông tin biến thể cho mỗi sản phẩm
            foreach ($products as &$product) {
                $product['variants'] = $this->variantModel->findAllByProductId($product['id']);
                if (empty($product['mainImage'])) {
                    $product['mainImage'] = '/assets/images/no-image.png';
                }
            }

            $data = [
                'title' => 'Kết quả tìm kiếm',
                'products' => $products,
                'totalPages' => $totalPages,
                'currentPage' => $page,
                'query' => $query,
                'category' => $category,
                'minPrice' => $minPrice,
                'maxPrice' => $maxPrice,
                'categories' => $categories
            ];

            $this->view('home/index', $data);

        } catch (\Exception $e) {
            error_log("Search error: " . $e->getMessage());
            // Trả về dữ liệu mặc định khi có lỗi
            $this->view('home/index', [
                'title' => 'Kết quả tìm kiếm',
                'error' => 'Có lỗi xảy ra khi tìm kiếm',
                'products' => [],
                'totalPages' => 1,
                'currentPage' => 1,
                'query' => '',
                'category' => '',
                'minPrice' => '',
                'maxPrice' => '',
                'categories' => [
                    ['id' => 'FRUITS', 'name' => 'Trái cây'],
                    ['id' => 'VEGETABLES', 'name' => 'Rau củ'],
                    ['id' => 'GRAINS', 'name' => 'Ngũ cốc'],
                    ['id' => 'OTHERS', 'name' => 'Khác']
                ]
            ]);
        }
    }

    public function about() {
        $this->view('about', [
            'title' => 'Giới thiệu',
            'error' => 'Có lỗi xảy ra khi tải dữ liệu'
        ]);
    }

    public function contact() {
        $this->view('contact', [
            'title' => 'Liên hệ',
            'error' => 'Có lỗi xảy ra khi tải dữ liệu'
        ]);
    }
}
