<?php
namespace Models;

class Employee {
    private $id;
    private $avatar;
    private $username;
    private $fullName;
    private $dateOfBirth;
    private $sex;
    private $phone;
    private $email;
    private $address;
    private $salary;
    private $password;
    private $eRole;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->avatar = $data['avatar'] ?? '';
        $this->username = $data['username'] ?? '';
        $this->fullName = $data['fullName'] ?? '';
        $this->dateOfBirth = $data['dateOfBirth'] ?? null;
        $this->sex = $data['sex'] ?? '';
        $this->phone = $data['phone'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->address = $data['address'] ?? '';
        $this->salary = $data['salary'] ?? 0;
        $this->password = $data['password'] ?? '';
        $this->eRole = $data['eRole'] ?? 'EMPLOYEE';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getAvatar() { return $this->avatar; }
    public function getUsername() { return $this->username; }
    public function getFullName() { return $this->fullName; }
    public function getDateOfBirth() { return $this->dateOfBirth; }
    public function getSex() { return $this->sex; }
    public function getPhone() { return $this->phone; }
    public function getEmail() { return $this->email; }
    public function getAddress() { return $this->address; }
    public function getSalary() { return $this->salary; }
    public function getERole() { return $this->eRole; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setAvatar($value) { $this->avatar = $value; }
    public function setUsername($value) { $this->username = $value; }
    public function setFullName($value) { $this->fullName = $value; }
    public function setDateOfBirth($value) { $this->dateOfBirth = $value; }
    public function setSex($value) { $this->sex = $value; }
    public function setPhone($value) { $this->phone = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setAddress($value) { $this->address = $value; }
    public function setSalary($value) { $this->salary = $value; }
    public function setPassword($value) { $this->password = password_hash($value, PASSWORD_DEFAULT); }

    // Kiểm tra mật khẩu
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
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
            'address' => $this->address,
            'salary' => $this->salary,
            'eRole' => $this->eRole,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
