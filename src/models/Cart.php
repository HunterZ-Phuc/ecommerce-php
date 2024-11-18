<?php
namespace Models;

use Exception;

class Cart {
    private $id;
    private $userId;
    private $variantId;
    private $quantity;
    private $createdAt;
    private $updatedAt;
    private $variant; // Quan hệ với ProductVariant
    private $user; // Quan hệ với User

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['userId'] ?? null;
        $this->variantId = $data['variantId'] ?? null;
        $this->quantity = $data['quantity'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getVariantId() { return $this->variantId; }
    public function getQuantity() { return $this->quantity; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getVariant() { return $this->variant; }
    public function getUser() { return $this->user; }

    // Setters
    public function setUserId($value) { $this->userId = $value; }
    public function setVariantId($value) { $this->variantId = $value; }
    public function setVariant($variant) { $this->variant = $variant; }
    public function setUser($user) { $this->user = $user; }

    // Cập nhật số lượng
    public function updateQuantity($quantity) {
        if ($quantity < 1) {
            throw new Exception("Số lượng phải lớn hơn 0");
        }
        $this->quantity = $quantity;
    }

    // Tính tổng tiền cho item trong giỏ hàng
    public function getSubtotal() {
        if ($this->variant) {
            return $this->quantity * $this->variant->getPrice();
        }
        return 0;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'variantId' => $this->variantId,
            'quantity' => $this->quantity,
            'subtotal' => $this->getSubtotal(),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'variant' => $this->variant ? $this->variant->toArray() : null
        ];
    }
}
