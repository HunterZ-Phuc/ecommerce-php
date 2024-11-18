<?php
namespace Models;

use Exception;

class ProductVariant {
    private $id;
    private $productId;
    private $sku;
    private $price;
    private $quantity;
    private $createdAt;
    private $updatedAt;
    private $combinations = []; // Quan hệ với VariantCombination
    private $image; // Quan hệ 1-1 với ProductImage
    private $product; // Quan hệ với Product

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->sku = $data['sku'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->quantity = $data['quantity'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getProductId() { return $this->productId; }
    public function getSku() { return $this->sku; }
    public function getPrice() { return $this->price; }
    public function getQuantity() { return $this->quantity; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getCombinations() { return $this->combinations; }
    public function getImage() { return $this->image; }
    public function getProduct() { return $this->product; }

    // Setters
    public function setProductId($value) { $this->productId = $value; }
    public function setSku($value) { $this->sku = $value; }
    public function setPrice($value) { $this->price = $value; }
    public function setImage($value) { $this->image = $value; }
    public function setProduct($value) { $this->product = $value; }

    // Quản lý combinations
    public function addCombination($combination) {
        $this->combinations[] = $combination;
    }

    // Kiểm tra còn hàng
    public function isInStock() {
        return $this->quantity > 0;
    }

    // Cập nhật số lượng
    public function updateQuantity($quantity) {
        if ($quantity < 0) {
            throw new Exception("Số lượng không thể âm");
        }
        $this->quantity = $quantity;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'sku' => $this->sku,
            'price' => $this->price,
            'quantity' => $this->quantity,
            'inStock' => $this->isInStock(),
            'combinations' => array_map(fn($c) => $c->toArray(), $this->combinations),
            'image' => $this->image ? $this->image->toArray() : null,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
