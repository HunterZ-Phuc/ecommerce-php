<?php
namespace Models;

class VariantValue {
    private $id;
    private $variantTypeId;
    private $value;
    private $createdAt;
    private $updatedAt;
    private $variantType; // Quan hệ với VariantType

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->variantTypeId = $data['variantTypeId'] ?? null;
        $this->value = $data['value'] ?? '';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getVariantTypeId() { return $this->variantTypeId; }
    public function getValue() { return $this->value; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getVariantType() { return $this->variantType; }

    // Setters
    public function setVariantTypeId($value) { $this->variantTypeId = $value; }
    public function setValue($value) { $this->value = $value; }
    public function setVariantType($value) { $this->variantType = $value; }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'variantTypeId' => $this->variantTypeId,
            'value' => $this->value,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
} 