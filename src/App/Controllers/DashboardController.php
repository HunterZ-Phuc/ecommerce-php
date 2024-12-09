<?php

namespace App\Controllers;

class DashboardController extends BaseController
{
    public function index()
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
}
