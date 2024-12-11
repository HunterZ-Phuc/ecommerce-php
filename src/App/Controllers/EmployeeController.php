<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Order;
use Exception;

class EmployeeController extends BaseController
{
    private $model;
    private $productModel;
    private $orderModel;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['ADMIN', 'EMPLOYEE']);
        $this->model = new Employee();
        $this->productModel = new Product();
        $this->orderModel = new Order();
    }

    public function index()
    {
        $this->redirect('employee/dashboard');
    }

    public function dashboard()
    {
        try {
            // Lấy tổng số sản phẩm và các thống kê
            $products = $this->productModel->findAll();
            $totalProducts = count($products);
            
            $outOfStockProducts = 0;
            $lowStockProducts = 0;
            $activeProducts = 0;
            $lowStockProductsList = [];
            
            foreach ($products as $product) {
                // Lấy tất cả biến thể của sản phẩm
                $variants = $this->productModel->getVariantsByProductId($product['id']);
                
                $totalStock = 0;
                foreach ($variants as $variant) {
                    $totalStock += $variant['quantity'];
                }
                
                // Kiểm tra trạng thái stock
                if ($totalStock == 0) {
                    $outOfStockProducts++;
                } elseif ($totalStock <= 10) {
                    $lowStockProducts++;
                    // Thêm vào danh sách sản phẩm sắp hết hàng
                    $lowStockProductsList[] = [
                        'id' => $product['id'],
                        'name' => $product['productName'],
                        'stock' => $totalStock,
                        'price' => $variants[0]['price'] ?? 0,
                        'status' => $product['status']
                    ];
                }
                
                // Đếm sản phẩm đang bán
                if ($product['status'] === 'ON_SALE') {
                    $activeProducts++;
                }
            }
            
            // Sắp xếp sản phẩm sắp hết hàng theo số lượng tăng dần
            usort($lowStockProductsList, function($a, $b) {
                return $a['stock'] <=> $b['stock'];
            });
            
            // Giới hạn chỉ lấy 10 sản phẩm
            $lowStockProductsList = array_slice($lowStockProductsList, 0, 10);

            $this->view('employee/dashboard', [
                'title' => 'Dashboard',
                'totalProducts' => $totalProducts,
                'outOfStockProducts' => $outOfStockProducts,
                'lowStockProducts' => $lowStockProducts,
                'activeProducts' => $activeProducts,
                'lowStockProductsList' => $lowStockProductsList
            ], 'employee_layout');
            
        } catch (\Exception $e) {
            // Log lỗi và hiển thị dashboard với thông báo lỗi
            error_log($e->getMessage());
            $this->view('employee/dashboard', [
                'title' => 'Dashboard',
                'error' => 'Có lỗi xảy ra khi tải dữ liệu dashboard'
            ], 'employee_layout');
        }
    }

    public function employeeManagement()
    {
        $employees = $this->model->findAll();
        $this->view('admin/EmployeeManagement/index', [
            'title' => 'Quản lý Nhân viên',
            'employees' => $employees
        ]);
    }

    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') { //Kiểm tra phương thức request
            try {
                $data = [  // Tạo mảng dữ liệu từ input
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT), // Mã hóa password
                    'fullName' => $_POST['fullName'],
                    'dateOfBirth' => $_POST['dateOfBirth'] ?? date('Y-m-d'),
                    'sex' => $_POST['sex'] ?? 'Other',
                    'phone' => $_POST['phone'],
                    'address' => $_POST['address'],
                    'salary' => $_POST['salary'],
                    'avatar' => 'default-avatar.jpg' // Mặc định avatar
                ];

                $this->model->create($data); //Gọi hàm create của model để lưu dữ liệu vào database
                $_SESSION['success'] = 'Thêm nhân viên thành công';
            } catch (\Exception $e) { // Xử lý ngoại lệ
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/employee-management'); //Reload lại trang cho đúng với dữ liệu mới
            exit;
        }
        // Khi không phải POST request, chuyển hướng đến trang quản lý nhân viên
        header('Location: /ecommerce-php/admin/employee-management'); // Chuyển hướng đến trang quản lý nhân viên
        exit; // Kết thúc chương trình
    }

    public function edit($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'email' => $_POST['email'],
                    'fullName' => $_POST['fullName'],
                    'dateOfBirth' => $_POST['dateOfBirth'],
                    'sex' => $_POST['sex'],
                    'phone' => $_POST['phone'],
                    'address' => $_POST['address'],
                    'salary' => $_POST['salary']
                ];

                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $this->model->update($id, $data);
                $_SESSION['success'] = 'Sửa thông tin nhân viên thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/employee-management');
            exit;
        }
        header('Location: /ecommerce-php/admin/employee-management');
        exit;
    }

    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->delete($id);
                $_SESSION['success'] = 'Xóa nhân viên thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/employee-management');
            exit;
        }
        header('Location: /ecommerce-php/admin/employee-management');
        exit;
    }

    public function orders()
    {
        $orders = $this->orderModel->getPendingOrders();

        $this->view('employee/orders', [
            'title' => 'Quản lý đơn hàng',
            'orders' => $orders
        ]);
    }

    public function orderDetail($orderId)
    {
        $order = $this->orderModel->getOrderDetails($orderId);

        if (!$order) {
            $this->redirect('404');
            return;
        }

        $this->view('employee/order-detail', [
            'title' => 'Chi tiết đơn hàng #' . $orderId,
            'order' => $order
        ]);
    }

    public function updateOrderStatus()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $orderId = $_POST['orderId'] ?? null;
            $status = $_POST['status'] ?? null;
            $note = $_POST['note'] ?? '';

            if (!$orderId || !$status) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            $userId = $this->auth->getUserId();
            $this->orderModel->updateOrderStatus($orderId, $status, $note, $userId);

            // Nếu đơn hàng đã giao thành công và thanh toán COD
            $order = $this->orderModel->getOrderDetails($orderId);
            if ($status === 'DELIVERED' && $order['paymentMethod'] === 'COD') {
                $this->orderModel->updatePaymentStatus($orderId, 'PAID');
            }

            $this->redirect("employee/order-detail/{$orderId}");

        } catch (Exception $e) {
            $this->view('employee/order-detail', [
                'error' => $e->getMessage(),
                'order' => $order ?? null
            ]);
        }
    }

    public function confirmPayment()
    {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $orderId = $_POST['orderId'] ?? null;
            $status = $_POST['status'] ?? null;

            if (!$orderId || !$status) {
                throw new Exception('Thiếu thông tin cần thiết');
            }

            $this->orderModel->updatePaymentStatus($orderId, $status);

            // Nếu xác nhận thanh toán thành công
            if ($status === 'PAID') {
                $userId = $this->auth->getUserId();
                $this->orderModel->updateOrderStatus(
                    $orderId, 
                    'PROCESSING', 
                    'Thanh toán đã được xác nhận',
                    $userId
                );
            }

            $this->redirect("employee/order-detail/{$orderId}");

        } catch (Exception $e) {
            $this->view('employee/order-detail', [
                'error' => $e->getMessage(),
                'order' => $order ?? null
            ]);
        }
    }

    public function getOrderStats()
    {
        $startDate = $_GET['startDate'] ?? null;
        $endDate = $_GET['endDate'] ?? null;

        $stats = $this->orderModel->getRevenueStats($startDate, $endDate);
        
        $this->jsonResponse([
            'success' => true,
            'data' => $stats
        ]);
    }

    public function exportOrders()
    {
        $status = $_GET['status'] ?? null;
        $orders = $status ? 
            $this->orderModel->getOrdersByStatus($status) : 
            $this->orderModel->getPendingOrders();

        // Tạo file Excel
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="orders.xls"');
        header('Cache-Control: max-age=0');

        echo "Mã đơn\tKhách hàng\tSĐT\tĐịa chỉ\tTổng tiền\tThanh toán\tTrạng thái\tNgày đặt\n";
        
        foreach ($orders as $order) {
            echo "{$order['id']}\t";
            echo "{$order['fullName']}\t";
            echo "{$order['phone']}\t";
            echo "{$order['address']}\t";
            echo number_format($order['totalAmount']) . "đ\t";
            echo ($order['paymentMethod'] === 'COD' ? 'COD' : 'Chuyển khoản') . "\t";
            
            $status = '';
            switch ($order['orderStatus']) {
                case 'PENDING':
                    $status = 'Chờ xác nhận';
                    break;
                case 'PROCESSING':
                    $status = 'Đang xử lý';
                    break;
                case 'SHIPPING':
                    $status = 'Đang giao';
                    break;
                case 'DELIVERED':
                    $status = 'Đã giao';
                    break;
                case 'CANCELLED':
                    $status = 'Đã hủy';
                    break;
            }
            echo "{$status}\t";
            
            echo date('d/m/Y H:i', strtotime($order['createdAt'])) . "\n";
        }

        exit;
    }

    public function printOrder($orderId)
    {
        $order = $this->orderModel->getOrderDetails($orderId);

        if (!$order) {
            $this->redirect('404');
            return;
        }

        $this->view('order/print', [
            'order' => $order
        ]);
    }
}