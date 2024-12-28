<?php

namespace App\Controllers;

use App\Models\Employee;
use App\Models\Product;
use App\Models\Order;
use Exception;
use Core\Database;

class EmployeeController extends BaseController
{
    private $model;
    private $productModel;
    private $orderModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['ADMIN', 'EMPLOYEE']);
        $this->model = new Employee();
        $this->productModel = new Product();
        $this->orderModel = new Order();
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        header('Location: /ecommerce-php/employee/dashboard');
    }

    // View dashboard
    public function dashboard()
    {
        try {
            // Lấy tổng số sản phẩm và các thống kê
            $products = $this->productModel->findAll();
            $totalProducts = count($products);

            $outOfStockProducts = 0;
            $lowStockProducts = $this->productModel->getLowStockCount();
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
            usort($lowStockProductsList, function ($a, $b) {
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

    // View quản lý nhân viên của admin
    public function employeeManagement()
    {
        $employees = $this->model->findAll();
        $this->view('admin/EmployeeManagement/index', [
            'title' => 'Quản lý Nhân viên',
            'employees' => $employees
        ]);
    }

    // Tạo nhân viên mới
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Tạo mảng dữ liệu từ input
                $data = [
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

                $this->model->create($data);
                $_SESSION['success'] = 'Thêm nhân viên thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/employee-management'); //Reload lại trang cho đúng với dữ liệu mới
            exit;
        }
        // Khi không phải POST request, chuyển hướng đến trang quản lý nhân viên
        header('Location: /ecommerce-php/admin/employee-management');
        exit; // Kết thúc chương trình
    }

    // Cập nhật thông tin nhân viên
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

    // Xóa nhân viên
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

    // View quản lý đơn hàng của nhân viên
    public function orders()
    {
        try {
            $orders = $this->orderModel->getAllOrders();

            $this->view('employee/OrdersManagement/orders', [
                'title' => 'Quản lý đơn hàng',
                'orders' => $orders
            ]);
        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('employee/dashboard');
        }
    }

    // View chi tiết đơn hàng
    public function orderDetail($id)
    {
        try {
            $order = $this->orderModel->getOrderById($id);
            if (!$order) {
                throw new Exception('Không tìm thấy đơn hàng');
            }
            return $this->view('employee/OrdersManagement/order-detail', [
                'title' => 'Chi tiết đơn hàng #' . $id,
                'order' => $order
            ]);

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('employee/orders');
        }
    }

    // Cập nhật trạng thái đơn hàng
    public function updateOrderStatus()
    {
        try {
            $this->db->beginTransaction();

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

            // Xử lý các trường hợp đặc biệt trước
            if ($status === 'RETURN_APPROVED') {
                $this->orderModel->updateOrderStatus($orderId, $status, 'Đã chấp nhận yêu cầu hoàn trả', $userId);
            } 
            else if ($status === 'RETURNED') {
                $this->orderModel->processReturn($orderId);
                $this->orderModel->updateOrderStatus($orderId, $status, 'Đã hoàn trả thành công', $userId);
            }
            else {
                // Cập nhật trạng thái đơn hàng thông thường
                $this->orderModel->updateOrderStatus($orderId, $status, $note, $userId);

                // Xử lý thanh toán COD khi giao hàng thành công
                if ($status === 'DELIVERED') {
                    $order = $this->orderModel->getOrderById($orderId);
                    if ($order['paymentMethod'] === 'CASH_ON_DELIVERY') {
                        $this->orderModel->updatePaymentStatus($orderId, 'PAID');
                    }
                }
            }

            $this->db->commit();
            $_SESSION['success'] = 'Cập nhật trạng thái đơn hàng thành công';
            
            // Chuyển hướng sau khi xử lý thành công
            $this->redirect("employee/order/{$orderId}");

        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("employee/order/{$orderId}");
        }
    }

    // Cập nhật trạng thái thanh toán
    public function confirmPayment()
    {
        try {
            $orderId = $_POST['orderId'];
            $status = $_POST['status'];
            $userId = $this->auth->getUserId();
            // Cập nhật trạng thái thanh toán
            $this->orderModel->updatePaymentStatus($orderId, $status, $userId);

            $_SESSION['success'] = 'Đã cập nhật trạng thái thanh toán';
            $this->redirect("employee/order/{$orderId}");

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect("employee/order/{$orderId}");
        }
    }

    // Xuất danh sách đơn hàng
    public function exportOrders()
    {
        try {
            // Lấy trạng thái từ query parameter nếu có
            $status = $_GET['status'] ?? null;

            // Lấy danh sách đơn hàng
            $orders = $status ?
                $this->orderModel->getOrdersByStatus($status) :
                $this->orderModel->getPendingOrders();

            // Tên file
            $filename = 'orders_' . date('Y-m-d_H-i-s') . '.csv';

            // Header cho file CSV
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename="' . $filename . '"');

            // Tạo file pointer để ghi
            $output = fopen('php://output', 'w');

            // Thêm BOM để Excel hiển thị tiếng Việt
            fprintf($output, chr(0xEF) . chr(0xBB) . chr(0xBF));

            // Header của các cột
            fputcsv($output, [
                'Mã đơn',
                'Khách hàng',
                'SĐT',
                'Địa chỉ',
                'Tổng tiền',
                'Thanh toán',
                'Trạng thái',
                'Ngày đặt'
            ]);

            // Ghi dữ liệu từng dòng
            foreach ($orders as $order) {
                $status = OrderHelper::getOrderStatusText($order['orderStatus']);
                $paymentMethod = $order['paymentMethod'] === 'CASH_ON_DELIVERY' ? 'Tiền mặt' : 'Chuyển khoản';

                fputcsv($output, [
                    $order['id'],
                    $order['fullName'],
                    $order['phone'],
                    $order['address'],
                    number_format($order['totalAmount']) . 'đ',
                    $paymentMethod,
                    $status,
                    date('d/m/Y H:i', strtotime($order['createdAt']))
                ]);
            }

            fclose($output);
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = $e->getMessage();
            $this->redirect('employee/orders');
        }
    }

    // View đổi mật khẩu
    public function changePassword()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/employee-login');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $currentPassword = $_POST['currentPassword'];
                $newPassword = $_POST['newPassword'];
                $confirmPassword = $_POST['confirmPassword'];

                // Validate input
                if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                    throw new Exception('Vui lòng điền đầy đủ thông tin');
                }

                if ($newPassword !== $confirmPassword) {
                    throw new Exception('Mật khẩu mới không khớp');
                }

                if (strlen($newPassword) < 6) {
                    throw new Exception('Mật khẩu mới phải có ít nhất 6 ký tự');
                }

                // Verify mật khẩu hiện tại
                $employee = $this->model->findById($this->auth->getUserId());
                if (!password_verify($currentPassword, $employee['password'])) {
                    throw new Exception('Mật khẩu hiện tại không đúng');
                }

                // Cập nhật mật khẩu
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->model->updatePassword($employee['id'], $hashedPassword);

                $_SESSION['success'] = 'Đổi mật khẩu thành công';
                $this->redirect('employee/change-password');

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('employee/change-password');
            }
        }

        $this->view('change_password', [
            'title' => 'Đổi mật khẩu'
        ], 'employee_layout');
    }
}