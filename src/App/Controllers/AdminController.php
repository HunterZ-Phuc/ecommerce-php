<?php

namespace App\Controllers;

use App\Models\Admin;

class AdminController extends BaseController
{
    private $model;

    public function __construct()
    {
        parent::__construct();
        $this->checkRole(['ADMIN']);
        $this->model = new Admin();
    }

    
    public function index()
    {
        // Điều hướng từ /admin sang /admin/dashboard
        header('Location: /ecommerce-php/admin/dashboard');
        exit();
    }

    public function dashboard()
    {
        // TODO: nao sửa thống kê ở đây
        $content = '<div class="container mt-4">
            <h1>Chào mừng đến với Trang quản trị</h1>
            <div class="row mt-4">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quản lý Admin</h5>
                            <p class="card-text">Quản lý tài khoản admin của hệ thống</p>
                            <a href="/ecommerce-php/admin/admin-management" class="btn btn-primary">Truy cập</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body">
                            <h5 class="card-title">Quản lý Nhân viên</h5>
                            <p class="card-text">Quản lý thông tin nhân viên</p>
                            <a href="/ecommerce-php/admin/employee-management" class="btn btn-primary">Truy cập</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>';

        // Render view với layout dashboard
        $this->view('admin/dashboard', [
            'title' => 'Admin Dashboard',
            'content' => $content
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
}
