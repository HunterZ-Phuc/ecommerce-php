<?php

namespace App\Controllers;

use App\Models\Admin;

class AdminController extends BaseController
{
    private $model;
    
    public function __construct() {
        $this->model = new Admin();
    }
    
    public function index()
    {
        $admins = $this->model->findAll();
        $this->view('admin/AdminManagement/index', [
            'title' => 'Quản lý Admin',
            'admins' => $admins
        ]);
    }
    
    public function create() {
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
    
    public function edit($id) {
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
    
    public function delete($id) {
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
