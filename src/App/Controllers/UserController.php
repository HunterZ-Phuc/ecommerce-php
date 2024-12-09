<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Address;
use Core\Database;

use Exception;

class UserController extends BaseController
{
    private $userModel;
    private $addressModel;
    private $db;

    public function __construct()
    {
        parent::__construct();
        $this->userModel = new User();
        $this->addressModel = new Address();
        $this->db = Database::getInstance()->getConnection();
    }

    public function index()
    {
        // Điều hướng từ /user sang /user/profile
        header('Location: /ecommerce-php/user/profile');
        exit();
    }

    public function profile()
    {
        // Kiểm tra đăng nhập
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }

        $userId = $this->auth->getUserId();
        error_log("User ID: " . $userId);

        // Lấy thông tin user từ database
        $user = $this->userModel->findById($userId);

        // Truyền đầy đủ dữ liệu vào view
        $this->view('user/account/profile', [
            'title' => 'Hồ sơ của tôi',
            'user' => $user,
            'username' => $user['username'],
            'fullName' => $user['fullName'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'sex' => $user['sex'],
            'dateOfBirth' => $user['dateOfBirth']
        ]);
    }

    public function updateProfile()
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Vui lòng đăng nhập');
            }

            $userId = $this->auth->getUserId();

            // Validate dữ liệu
            $fullName = $_POST['fullName'] ?? '';
            $sex = $_POST['sex'] ?? '';
            $dateOfBirth = $_POST['dateOfBirth'] ?? '';

            if (empty($fullName) || empty($sex) || empty($dateOfBirth)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            // Cập nhật thông tin
            $updateData = [
                'fullName' => $fullName,
                'sex' => $sex,
                'dateOfBirth' => $dateOfBirth
            ];

            $this->userModel->update($userId, $updateData);

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật thông tin thành công'
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function updateAvatar()
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Unauthorized');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();

            // Xử lý upload file
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Không có file được upload');
            }

            $file = $_FILES['avatar'];
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];

            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Chỉ chấp nhận file ảnh (JPG, PNG, GIF)');
            }

            // Tạo tên file mới
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = ROOT_PATH . '/public/uploads/avatars/' . $newFileName;

            // Di chuyển file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Không thể lưu file');
            }

            // Cập nhật DB
            $data = ['avatar' => '/uploads/avatars/' . $newFileName];
            $this->userModel->update($userId, $data);

            // Cập nhật session
            $_SESSION['avatar'] = $data['avatar'];

            $this->json([
                'success' => true,
                'message' => 'Cập nhật avatar thành công',
                'avatar' => $data['avatar']
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    protected function maskEmail($email)
    {
        if (!$email)
            return '';

        $parts = explode('@', $email);
        if (count($parts) !== 2)
            return $email;

        $name = $parts[0];
        $domain = $parts[1];
        $maskedName = substr($name, 0, 2) . str_repeat('*', max(strlen($name) - 2, 0));
        return $maskedName . '@' . $domain;
    }

    protected function maskPhone($phone)
    {
        if (!$phone)
            return '';
        return substr($phone, 0, 3) . str_repeat('*', max(strlen($phone) - 5, 0)) . substr($phone, -2);
    }

    public function updateEmail()
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Unauthorized');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $newEmail = $_POST['email'] ?? '';

            // Validate email
            if (!filter_var($newEmail, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ');
            }

            // Kiểm tra email đã tồn tại
            if ($this->userModel->findByEmail($newEmail)) {
                throw new Exception('Email đã được sử dụng');
            }

            // Cập nhật DB
            $this->userModel->update($userId, ['email' => $newEmail]);

            // Cập nhật session
            $_SESSION['user_email'] = $newEmail;

            $this->json([
                'success' => true,
                'message' => 'Cập nhật email thành công',
                'email' => $newEmail
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function updatePhone()
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Unauthorized');
            }

            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }

            $userId = $this->auth->getUserId();
            $newPhone = $_POST['phone'] ?? '';

            // Validate phone
            if (!preg_match('/^[0-9]{10,11}$/', $newPhone)) {
                throw new Exception('Số điện thoại không hợp lệ');
            }

            // Kiểm tra phone đã tồn tại
            if ($this->userModel->findByPhone($newPhone)) {
                throw new Exception('Số điện thoại đã được sử dụng');
            }

            // Cập nhật DB
            $this->userModel->update($userId, ['phone' => $newPhone]);

            // Cập nhật session
            $_SESSION['user_phone'] = $newPhone;

            $this->json([
                'success' => true,
                'message' => 'Cập nhật số điện thoại thành công',
                'phone' => $newPhone
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function addresses()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('login');
        }

        $userId = $this->auth->getUserId();
        $addresses = $this->addressModel->getAllByUserId($userId);

        $this->view('user/account/addresses', [
            'title' => 'Địa chỉ của tôi',
            'addresses' => $addresses,
            'active_page' => 'addresses'
        ], 'user_layout');
    }

    public function getAddress($id)
    {
        if (!$this->auth->isLoggedIn()) {
            $this->error('Unauthorized');
        }

        $userId = $this->auth->getUserId();
        $address = $this->addressModel->getById($id, $userId);

        if (!$address) {
            $this->error('Địa chỉ không tồn tại');
        }

        $this->json([
            'success' => true,
            'address' => $address
        ]);
    }

    public function createAddress()
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Unauthorized');
            }

            $userId = $this->auth->getUserId();
            $data = [
                'userId' => $userId,
                'fullName' => $_POST['fullName'] ?? '',
                'phoneNumber' => $_POST['phoneNumber'] ?? '',
                'address' => $_POST['address'] ?? ''
            ];

            if (empty($data['fullName']) || empty($data['phoneNumber']) || empty($data['address'])) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            $this->addressModel->create($data);

            $this->json([
                'success' => true,
                'message' => 'Thêm địa chỉ thành công'
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function updateAddress($id)
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Vui lòng đăng nhập');
            }

            $userId = $this->auth->getUserId();

            // Validate dữ liệu
            $fullName = $_POST['fullName'] ?? '';
            $phoneNumber = $_POST['phoneNumber'] ?? '';
            $address = $_POST['address'] ?? '';

            if (empty($fullName) || empty($phoneNumber) || empty($address)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            // Cập nhật địa chỉ
            $updateData = [
                'userId' => $userId, // Cần thiết cho việc kiểm tra quyền sở hữu
                'fullName' => $fullName,
                'phoneNumber' => $phoneNumber,
                'address' => $address
            ];

            if (!$this->addressModel->update($id, $updateData)) {
                throw new Exception('Không thể cập nhật địa chỉ');
            }

            $this->json([
                'success' => true,
                'message' => 'Cập nhật địa chỉ thành công'
            ]);

        } catch (Exception $e) {
            $this->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function deleteAddress($id)
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Unauthorized');
            }

            $userId = $this->auth->getUserId();
            $this->addressModel->deleteWithUserId($id, $userId);

            $this->json([
                'success' => true,
                'message' => 'Xóa địa chỉ thành công'
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }

    public function setDefaultAddress($id)
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Unauthorized');
            }

            $userId = $this->auth->getUserId();
            $this->addressModel->setDefault($id, $userId);

            $this->json([
                'success' => true,
                'message' => 'Đã thiết lập địa chỉ mặc định'
            ]);

        } catch (Exception $e) {
            $this->error($e->getMessage());
        }
    }
}