<?php

namespace App\Controllers;

use App\Models\User;
use Exception;

class AuthController extends BaseController
{
    private $userModel;

    public function __construct() 
    {
        parent::__construct();
        $this->userModel = new User();
    }

    public function login()
    {
        // Kiểm tra xem đã có session nào đang hoạt động không
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Nếu đã đăng nhập thì chuyển hướng về trang tương ứng
        if ($this->auth->isLoggedIn()) {
            $userRole = $this->auth->getUserRole();
            if ($userRole === 'ADMIN') {
                header('Location: /ecommerce-php/admin');
            } else {
                header('Location: /ecommerce-php/');
            }
            exit();
        }

        // Xử lý đăng nhập
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $username = $_POST['username'] ?? '';
            $password = $_POST['password'] ?? '';
            $remember = isset($_POST['remember']);

            try {
                // Tìm user theo username
                $user = $this->userModel->findByUsername($username);
                
                if (!$user) {
                    throw new Exception('Tên đăng nhập hoặc mật khẩu không đúng');
                }

                // Kiểm tra mật khẩu
                if (!password_verify($password, $user['password'])) {
                    throw new Exception('Tên đăng nhập hoặc mật khẩu không đúng');
                }

                // Hủy session cũ nếu có
                if (session_status() === PHP_SESSION_ACTIVE) {
                    session_destroy();
                    session_start();
                }

                // Đăng nhập với role mới
                $avatar = $user['avatar'] ?? null;
                $role = $user['role'] ?? 'USER'; // Mặc định là USER nếu không có role
                $this->auth->login($user['id'], $user['username'], $avatar, $role);

                // Chuyển hướng dựa vào role
                if ($role === 'ADMIN') {
                    header('Location: /ecommerce-php/admin');
                } else {
                    header('Location: /ecommerce-php/');
                }
                exit();

            } catch (Exception $e) {
                $this->view('login', [
                    'error' => $e->getMessage()
                ]);
                return;
            }
        }

        // Hiển thị form đăng nhập
        $this->view('login');
    }

    public function register()
    {
        // Nếu đã đăng nhập thì chuyển hướng về trang chủ
        if ($this->auth->isLoggedIn()) {
            header('Location: /ecommerce-php/');
            exit();
        }

        // Xử lý đăng ký
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $data = [
                    'username' => $_POST['username'] ?? '',
                    'fullName' => $_POST['fullName'] ?? '',
                    'email' => $_POST['email'] ?? '',
                    'phone' => $_POST['phone'] ?? '',
                    'dateOfBirth' => $_POST['dateOfBirth'] ?? '',
                    'sex' => $_POST['sex'] ?? '',
                    'password' => $_POST['password'] ?? '',
                    'confirmPassword' => $_POST['confirmPassword'] ?? '',
                    'role' => 'USER' // Mặc định role là USER khi đăng ký
                ];

                // Validate dữ liệu
                $this->validateRegistrationData($data);

                // Kiểm tra username đã tồn tại
                if ($this->userModel->findByUsername($data['username'])) {
                    throw new Exception('Tên đăng nhập đã tồn tại');
                }

                // Kiểm tra email đã tồn tại
                if ($this->userModel->findByEmail($data['email'])) {
                    throw new Exception('Email đã được sử dụng');
                }

                // Kiểm tra số điện thoại đã tồn tại
                if ($this->userModel->findByPhone($data['phone'])) {
                    throw new Exception('Số điện thoại đã được sử dụng');
                }

                // Hash password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Bỏ confirmPassword
                unset($data['confirmPassword']);

                // Tạo tài khoản
                $userId = $this->userModel->create($data);

                // Đăng nhập luôn sau khi đăng ký thành công
                $avatar = null; // Mặc định avatar là null cho user mới
                $this->auth->login($userId, $data['username'], $avatar, 'USER');

                // Chuyển hướng về trang chủ
                header('Location: /ecommerce-php/');
                exit();

            } catch (Exception $e) {
                // Hiển thị form với thông báo lỗi và giữ lại dữ liệu cũ
                $this->view('register', [
                    'error' => $e->getMessage(),
                    'old' => $_POST
                ]);
                return;
            }
        }

        // Hiển thị form đăng ký
        $this->view('register');
    }

    public function logout()
    {
        // Hủy toàn bộ session
        $this->auth->logout();
        
        // Đảm bảo session được hủy hoàn toàn
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
        }
        
        // Khởi tạo session mới
        session_start();
        
        header('Location: /ecommerce-php/login');
        exit();
    }

    private function validateRegistrationData($data)
    {
        // Validate các trường bắt buộc
        $requiredFields = ['username', 'fullName', 'email', 'phone', 'dateOfBirth', 'sex', 'password', 'confirmPassword'];
        foreach ($requiredFields as $field) {
            if (empty($data[$field])) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }
        }

        // Validate username
        if (!preg_match('/^[a-zA-Z0-9_]{3,20}$/', $data['username'])) {
            throw new Exception('Tên đăng nhập chỉ được chứa chữ cái, số và dấu gạch dưới, độ dài 3-20 ký tự');
        }

        // Validate email
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            throw new Exception('Email không hợp lệ');
        }

        // Validate phone
        if (!preg_match('/^[0-9]{10,11}$/', $data['phone'])) {
            throw new Exception('Số điện thoại không hợp lệ');
        }

        // Validate password
        if (strlen($data['password']) < 6) {
            throw new Exception('Mật khẩu phải có ít nhất 6 ký tự');
        }

        // Validate confirm password
        if ($data['password'] !== $data['confirmPassword']) {
            throw new Exception('Xác nhận mật khẩu không khớp');
        }

        // Validate date of birth
        $dob = strtotime($data['dateOfBirth']);
        if (!$dob) {
            throw new Exception('Ngày sinh không hợp lệ');
        }

        // Validate sex
        if (!in_array($data['sex'], ['Male', 'Female', 'Other'])) {
            throw new Exception('Giới tính không hợp lệ');
        }
    }
} 