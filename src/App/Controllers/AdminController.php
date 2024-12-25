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
        exit();
    }

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

    public function adminManagement()
    {
        $admins = $this->model->findAll();
        $this->view('admin/AdminManagement/index', [
            'title' => 'Quản lý Admin',
            'admins' => $admins
        ]);
    }


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

    public function users()
    {
        $page = $_GET['page'] ?? 1;
        $limit = 10;
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';

        $users = $this->userModel->getUsersWithStats($search, $status, $limit, ($page - 1) * $limit);
        $total = $this->userModel->getTotalUsers($search, $status);
        $totalPages = ceil($total / $limit);

        // Xây dựng query string cho phân trang
        $queryParams = $_GET;
        unset($queryParams['page']);
        $queryString = http_build_query($queryParams);
        if ($queryString) {
            $queryString = '&' . $queryString;
        }

        $this->view('admin/users/index', [
            'title' => 'Quản lý người dùng',
            'users' => $users,
            'currentPage' => $page,
            'totalPages' => $totalPages,
            'queryString' => $queryString
        ]);
    }

    public function exportUsers()
    {
        $users = $this->userModel->getAllUsersWithStats();
        
        // Tạo file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        
        // Thiết lập header
        $sheet->setCellValue('A1', 'ID');
        $sheet->setCellValue('B1', 'Họ tên');
        $sheet->setCellValue('C1', 'Email');
        $sheet->setCellValue('D1', 'Số điện thoại');
        $sheet->setCellValue('E1', 'Ngày tham gia');
        $sheet->setCellValue('F1', 'Số đơn hàng');
        $sheet->setCellValue('G1', 'Tổng chi tiêu');
        $sheet->setCellValue('H1', 'Trạng thái');
        
        // Đổ dữ liệu
        $row = 2;
        foreach ($users as $user) {
            $sheet->setCellValue('A' . $row, $user['id']);
            $sheet->setCellValue('B' . $row, $user['fullName']);
            $sheet->setCellValue('C' . $row, $user['email']);
            $sheet->setCellValue('D' . $row, $user['phone']);
            $sheet->setCellValue('E' . $row, date('d/m/Y', strtotime($user['createdAt'])));
            $sheet->setCellValue('F' . $row, $user['totalOrders']);
            $sheet->setCellValue('G' . $row, number_format($user['totalSpent']));
            $sheet->setCellValue('H' . $row, $user['isActive'] ? 'Hoạt động' : 'Đã khóa');
            $row++;
        }
        
        // Xuất file
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="users_export_' . date('Y-m-d') . '.xlsx"');
        header('Cache-Control: max-age=0');
        $writer->save('php://output');
        exit;
    }
}
