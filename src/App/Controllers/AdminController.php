<?php

namespace App\Controllers;

use App\Models\Admin;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;

class AdminController extends BaseController
{
    private $model;
    private $userModel;
    private $orderModel;
    private $productModel;


    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['ADMIN']);
        $this->model = new Admin();
        $this->userModel = new User();
        $this->orderModel = new Order();
        $this->productModel = new Product();
    }

    
    public function index()
    {
        // Điều hướng từ /admin sang /admin/dashboard
        header('Location: /ecommerce-php/admin/dashboard');
    }

    // View dashboard
    public function dashboard()
    {
        // Lấy thống kê tổng quan
         $totalOrders = $this->orderModel->getTotalOrders();
         $monthlyRevenue = $this->orderModel->getCurrentMonthRevenue();
        $totalCustomers = $this->userModel->getTotalCustomers();
        $lowStockProducts = $this->productModel->getLowStockCount();

        // Lấy dữ liệu biểu đồ doanh thu
        $revenueData = $this->orderModel->getMonthlyRevenue();

        // Lấy dữ liệu trạng thái đơn hàng
        $orderStatusData = $this->orderModel->getOrderStatusDistribution();

        $this->view('admin/dashboard', [
            'title' => 'Dashboard',
            'totalOrders' => $totalOrders,
            'monthlyRevenue' => $monthlyRevenue,
            'totalCustomers' => $totalCustomers,
            'lowStockProducts' => $lowStockProducts,
            'revenueData' => $revenueData,
            'orderStatusData' => $orderStatusData
        ]);
    }

    // View quản lý admin
    public function adminManagement()
    {
        $admins = $this->model->findAll();
        $this->view('admin/AdminManagement/index', [
            'title' => 'Quản lý Admin',
            'admins' => $admins
        ]);
    }

    // Tạo admin
    public function create()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'password' => password_hash($_POST['password'], PASSWORD_DEFAULT),
                    'eRole' => 'ADMIN'
                ];

                $this->model->create($data);
                $_SESSION['success'] = 'Thêm admin thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/admin-management');
            exit;
        }
        header('Location: /ecommerce-php/admin/admin-management');
        exit;
    }

    // Sửa admin
    public function edit($id)
    {
        $admin = $this->model->findById($id);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'],
                    'email' => $_POST['email']
                ];

                if (!empty($_POST['password'])) {
                    $data['password'] = password_hash($_POST['password'], PASSWORD_DEFAULT);
                }

                $this->model->update($id, $data);
                $_SESSION['success'] = 'Cập nhật admin thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/admin-management');
            exit;
        }
        header('Location: /ecommerce-php/admin/admin-management');
        exit;
    }

    // Xóa admin
    public function delete($id)
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $this->model->delete($id);
                $_SESSION['success'] = 'Xóa admin thành công';
            } catch (\Exception $e) {
                $_SESSION['error'] = $e->getMessage();
            }
            header('Location: /ecommerce-php/admin/admin-management');
            exit;
        }
    }

    // View quản lý người dùng
    public function users()
    {
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';

        // Đảm bảo page không nhỏ hơn 1
        $page = max(1, $page);

        $users = $this->userModel->getUsersWithStats($search, $limit, ($page - 1) * $limit);
        $total = $this->userModel->getTotalUsers($search);
        $totalPages = ceil($total / $limit);

        // Đảm bảo page không vượt quá tổng số trang
        $page = min($page, $totalPages);

        $this->view('admin/UserManagement/index', [
            'title' => 'Quản lý người dùng',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'search' => $search
        ]);
    }

    // Xuất danh sách người dùng
    public function exportUsers()
    {
        $users = $this->userModel->getAllUsersWithStats();
        
        // Set headers for CSV download
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename=danh_sach_nguoi_dung_' . date('Y-m-d') . '.csv');
        
        // Create file pointer connected to output stream
        $output = fopen('php://output', 'w');
        
        // Add UTF-8 BOM for Excel to display Vietnamese correctly
        fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));
        
        // Add headers
        fputcsv($output, [
            'ID',
            'Username',
            'Họ tên',
            'Email',
            'Số điện thoại',
            'Ngày tham gia',
            'Số đơn hàng',
            'Tổng chi tiêu'
        ]);
        
        // Add data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['username'],
                $user['fullName'],
                $user['email'],
                $user['phone'],
                date('d/m/Y', strtotime($user['createdAt'])),
                $user['totalOrders'],
                number_format($user['totalSpent']) . 'đ'
            ]);
        }
        
        fclose($output);
        exit;
    }

    // View đổi mật khẩu
    public function changePassword()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/admin-login');
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
                $admin = $this->model->findById($this->auth->getUserId());
                if (!password_verify($currentPassword, $admin['password'])) {
                    throw new Exception('Mật khẩu hiện tại không đúng');
                }

                // Cập nhật mật khẩu
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->model->updatePassword($admin['id'], $hashedPassword);

                $_SESSION['success'] = 'Đổi mật khẩu thành công';
                $this->redirect('admin/change-password');

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('admin/change-password');
            }
        }

        $this->view('change_password', [
            'title' => 'Đổi mật khẩu'
        ], 'admin_layout');
    }
}
