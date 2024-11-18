<?php
namespace Models;

class VariantType {
    private $id;
    private $productId;
    private $name;
    private $createdAt;
    private $updatedAt;
    private $values = []; // Quan hệ với VariantValue
    private $product; // Quan hệ với Product

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->name = $data['name'] ?? '';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getProductId() { return $this->productId; }
    public function getName() { return $this->name; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getValues() { return $this->values; }
    public function getProduct() { return $this->product; }

    // Setters
    public function setProductId($value) { $this->productId = $value; }
    public function setName($value) { $this->name = $value; }
    public function setProduct($value) { $this->product = $value; }

    // Quản lý variant values
    public function addValue($value) {
        $this->values[] = $value;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'name' => $this->name,
            'values' => array_map(fn($v) => $v->toArray(), $this->values),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
