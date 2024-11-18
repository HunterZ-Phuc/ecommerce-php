<?php
namespace Models;

class Admin {
    private $id;
    private $username;
    private $email;
    private $password;
    private $eRole;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->username = $data['username'] ?? '';
        $this->email = $data['email'] ?? '';
        $this->password = $data['password'] ?? '';
        $this->eRole = $data['eRole'] ?? 'ADMIN';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUsername() { return $this->username; }
    public function getEmail() { return $this->email; }
    public function getERole() { return $this->eRole; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }

    // Setters
    public function setUsername($value) { $this->username = $value; }
    public function setEmail($value) { $this->email = $value; }
    public function setPassword($value) { $this->password = password_hash($value, PASSWORD_DEFAULT); }

    // Kiểm tra mật khẩu
    public function verifyPassword($password) {
        return password_verify($password, $this->password);
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'eRole' => $this->eRole,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
