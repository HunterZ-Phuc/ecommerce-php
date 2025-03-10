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
        $this->redirect('user/profile');
    }

    // View hồ sơ của người dùng
    public function profile()
    {
        // Kiểm tra đăng nhập
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
        }

        $userId = $this->auth->getUserId();

        // Lấy thông tin user từ database
        $user = $this->userModel->findById($userId);

        // Render view
        $this->view('user/account/profile', [
            'title' => 'Hồ sơ của tôi',
            'user' => $user,
            'username' => $user['username'],
            'fullName' => $user['fullName'],
            'email' => $user['email'],
            'phone' => $user['phone'],
            'sex' => $user['sex'],
            'dateOfBirth' => $user['dateOfBirth'],
            'active_page' => 'profile'
        ]);
    }

    // Cập nhật thông tin người dùng
    public function updateProfile()
    {
        try {
            if (!$this->auth->isLoggedIn()) {
                throw new Exception('Vui lòng đăng nhập');
            }

            $userId = $this->auth->getUserId();

            // Validate dữ liệu
            $fullName = $_POST['fullName'] ?? '';
            $email = $_POST['email'] ?? '';
            $phone = $_POST['phone'] ?? '';
            $sex = $_POST['sex'] ?? '';
            $dateOfBirth = $_POST['dateOfBirth'] ?? '';

            if (empty($fullName) || empty($sex) || empty($dateOfBirth)) {
                throw new Exception('Vui lòng điền đầy đủ thông tin');
            }

            // Validate email nếu có
            if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                throw new Exception('Email không hợp lệ');
            }

            // Validate số điện thoại nếu có
            if (!empty($phone) && !preg_match('/^[0-9]{10,11}$/', $phone)) {
                throw new Exception('Số điện thoại không hợp lệ');
            }

            // Kiểm tra email đã tồn tại (nếu có thay đổi)
            if (!empty($email)) {
                $existingUser = $this->userModel->findByEmail($email);
                if ($existingUser && $existingUser['id'] != $userId) {
                    throw new Exception('Email đã được sử dụng');
                }
            }

            // Kiểm tra số điện thoại đã tồn tại (nếu có thay đổi)
            if (!empty($phone)) {
                $existingUser = $this->userModel->findByPhone($phone);
                if ($existingUser && $existingUser['id'] != $userId) {
                    throw new Exception('Số điện thoại đã được sử dụng');
                }
            }

            // Cập nhật thông tin
            $updateData = [
                'fullName' => $fullName,
                'sex' => $sex,
                'dateOfBirth' => $dateOfBirth
            ];

            // Thêm email và phone vào dữ liệu cập nhật nếu có
            if (!empty($email)) {
                $updateData['email'] = $email;
            }
            if (!empty($phone)) {
                $updateData['phone'] = $phone;
            }

            // Cập nhật database
            if (!$this->userModel->update($userId, $updateData)) {
                throw new Exception('Không thể cập nhật thông tin');
            }

            // Cập nhật session
            $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], [
                'fullName' => $fullName,
                'email' => $email,
                'phone' => $phone
            ]);

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

    // Cập nhật avatar
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

            // Kiểm tra file upload
            if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
                throw new Exception('Không có file được upload');
            }

            $file = $_FILES['avatar'];

            // Kiểm tra định dạng file
            $allowedTypes = ['image/jpeg', 'image/png'];
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception('Chỉ chấp nhận file JPG hoặc PNG');
            }

            // Lấy avatar cũ từ database
            $user = $this->userModel->findById($userId);
            $oldAvatarPath = $user['avatar'] ?? null;

            // Tạo thư mục uploads nếu chưa tồn tại
            $uploadDir = ROOT_PATH . '/public/uploads/avatars/';
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            // Tạo tên file mới
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFileName = 'avatar_' . $userId . '_' . time() . '.' . $extension;
            $uploadPath = $uploadDir . $newFileName;

            // Di chuyển file
            if (!move_uploaded_file($file['tmp_name'], $uploadPath)) {
                throw new Exception('Không thể lưu file');
            }

            // Cập nhật DB
            $avatarPath = '/uploads/avatars/' . $newFileName;
            if (!$this->userModel->update($userId, ['avatar' => $avatarPath])) {
                // Nếu cập nhật DB thất bại, xóa file mới upload
                if (file_exists($uploadPath)) {
                    unlink($uploadPath);
                }
                throw new Exception('Không thể cập nhật avatar trong database');
            }

            // Xóa avatar cũ nếu tồn tại và không phải avatar mặc định
            if (
                $oldAvatarPath &&
                $oldAvatarPath !== '/assets/images/default-avatar.png' &&
                file_exists(ROOT_PATH . '/public' . $oldAvatarPath)
            ) {
                unlink(ROOT_PATH . '/public' . $oldAvatarPath);
            }

            // Cập nhật session
            $_SESSION['user'] = array_merge($_SESSION['user'] ?? [], ['avatar' => $avatarPath]);

            echo json_encode([
                'success' => true,
                'message' => 'Cập nhật avatar thành công',
                'avatar' => $avatarPath
            ]);

        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    // Cập nhật email
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

    // Cập nhật số điện thoại
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

    // View địa chỉ của người dùng
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

    // Lấy địa chỉ của người dùng
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

    // Tạo địa chỉ mới
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

    // Cập nhật địa chỉ
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

    // Xóa địa chỉ
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

    // Thiết lập địa chỉ mặc định
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

    // View đổi mật khẩu
    public function changePassword()
    {
        if (!$this->auth->isLoggedIn()) {
            $this->redirect('/login');
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

                // Verify current password
                $user = $this->userModel->findById($this->auth->getUserId());
                if (!password_verify($currentPassword, $user['password'])) {
                    throw new Exception('Mật khẩu hiện tại không đúng');
                }

                // Update password
                $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                $this->userModel->updatePassword($user['id'], $hashedPassword);

                $_SESSION['success'] = 'Đổi mật khẩu thành công';
                $this->redirect('user/change-password');

            } catch (Exception $e) {
                $_SESSION['error'] = $e->getMessage();
                $this->redirect('user/change-password');
            }
        }

        $this->view('change_password', [
            'title' => 'Đổi mật khẩu',
            'active_page' => 'change-password'
        ], 'user_layout');
    }
}