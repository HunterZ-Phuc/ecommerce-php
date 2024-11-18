<?php
namespace Models;

class ProductImage {
    private $id;
    private $productId;
    private $variantId;
    private $imageUrl;
    private $isThumbnail;
    private $displayOrder;
    private $createdAt;
    private $updatedAt;
    
    // Các quan hệ
    private $product;
    private $variant;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->variantId = $data['variantId'] ?? null;
        $this->imageUrl = $data['imageUrl'] ?? '';
        $this->isThumbnail = $data['isThumbnail'] ?? false;
        $this->displayOrder = $data['displayOrder'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getProductId() { return $this->productId; }
    public function getVariantId() { return $this->variantId; }
    public function getImageUrl() { return $this->imageUrl; }
    public function getIsThumbnail() { return $this->isThumbnail; }
    public function getDisplayOrder() { return $this->displayOrder; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getProduct() { return $this->product; }
    public function getVariant() { return $this->variant; }

    // Setters
    public function setProductId($value) { $this->productId = $value; }
    public function setVariantId($value) { $this->variantId = $value; }
    public function setImageUrl($value) { $this->imageUrl = $value; }
    public function setIsThumbnail($value) { $this->isThumbnail = $value; }
    public function setDisplayOrder($value) { $this->displayOrder = $value; }
    public function setProduct($value) { $this->product = $value; }
    public function setVariant($value) { $this->variant = $value; }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'variantId' => $this->variantId,
            'imageUrl' => $this->imageUrl,
            'isThumbnail' => $this->isThumbnail,
            'displayOrder' => $this->displayOrder,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
