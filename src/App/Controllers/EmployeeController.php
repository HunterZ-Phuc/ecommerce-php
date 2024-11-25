<?php

namespace App\Controllers;

use App\Models\Employee;

class EmployeeController extends BaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new Employee();
    }

    public function index()
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
}