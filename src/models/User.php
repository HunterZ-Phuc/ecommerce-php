<?php
namespace Models;

class User {
    private $id;
    private $avatar;
    private $username;
    private $fullName;
    private $dateOfBirth;
    private $sex;
    private $phone;
    private $email;
    private $password;
    private $eRole;
    private $createdAt;
    private $updatedAt;
    
    // Các quan hệ
    private $addresses = [];
    private $orders = [];
    private $cart = [];

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->avatar = $data['avatar'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->fullName = $data['fullName'] ?? '';
        $this->dateOfBirth = $data['dateOfBirth'] ?? null;
        $this->sex = $data['sex'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->eRole = $data['eRole'] ?? 'USER';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters và Setters
    public function getId() { return $this->id; }
    public function getAvatar() { return $this->avatar; }
    public function getUsername() { return $this->username; }
    public function getFullName() { return $this->fullName; }
    public function getDateOfBirth() { return $this->dateOfBirth; }
    public function getSex() { return $this->sex; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }
    public function getPassword() { return $this->password; }
    public function getERole() { return $this->eRole; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    public function setAvatar($value) { $this->avatar = $value; }
    public function setUsername($value) { $this->username = $value; }
    public function setFullName($value) { $this->fullName = $value; }
    public function setDateOfBirth($value) { $this->dateOfBirth = $value; }
    public function setSex($value) { $this->sex = $value; }
    public function setPhone($value) { $this->phone = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setPassword($value) { $this->password = $value; }
    public function setERole($value) { $this->eRole = $value; }

    // Phương thức quản lý địa chỉ
    public function addAddress($address) {
        $this->addresses[] = $address;
    }

    public function getAddresses() {
        return $this->addresses;
    }

    public function getDefaultAddress() {
        foreach ($this->addresses as $address) {
            if ($address->getIsDefault()) {
                return $address;
            }
        }
        return null;
    }

    // Phương thức quản lý đơn hàng
    public function addOrder($order) {
        $this->orders[] = $order;
    }

    public function getOrders() {
        return $this->orders;
    }

    // Phương thức quản lý giỏ hàng
    public function addToCart($cartItem) {
        $this->cart[] = $cartItem;
    }

    public function getCart() {
        return $this->cart;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'username' => $this->username,
            'fullName' => $this->fullName,
            'dateOfBirth' => $this->dateOfBirth,
            'sex' => $this->sex,
            'phone' => $this->phone,
            'email' => $this->email,
            'eRole' => $this->eRole,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
