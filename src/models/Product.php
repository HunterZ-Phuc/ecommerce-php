<?php
namespace Models;

class Product {
    private $id;
    private $productName;
    private $description;
    private $origin;
    private $category;
    private $price;
    private $salePercent;
    private $stockQuantity;
    private $sold;
    private $status;
    private $createdAt;
    private $updatedAt;
    
    // Các quan hệ
    private $variants = [];
    private $variantTypes = [];
    private $images = [];

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productName = $data['productName'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->origin = $data['origin'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->salePercent = $data['salePercent'] ?? 0;
        $this->stockQuantity = $data['stockQuantity'] ?? 0;
        $this->sold = $data['sold'] ?? 0;
        $this->status = $data['status'] ?? 'ON_SALE';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters và Setters
    public function getId() { return $this->id; }
    public function getProductName() { return $this->productName; }
    public function getDescription() { return $this->description; }
    public function getOrigin() { return $this->origin; }
    public function getCategory() { return $this->category; }
    public function getPrice() { return $this->price; }
    public function getSalePercent() { return $this->salePercent; }
    public function getStockQuantity() { return $this->stockQuantity; }
    public function getSold() { return $this->sold; }
    public function getStatus() { return $this->status; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    
    public function setProductName($value) { $this->productName = $value; }
    public function setDescription($value) { $this->description = $value; }
    public function setOrigin($value) { $this->origin = $value; }
    public function setCategory($value) { $this->category = $value; }
    public function setPrice($value) { $this->price = $value; }
    public function setSalePercent($value) { $this->salePercent = $value; }
    public function setStockQuantity($value) { $this->stockQuantity = $value; }
    public function setSold($value) { $this->sold = $value; }
    public function setStatus($value) { $this->status = $value; }

    // Phương thức tính giá sau khuyến mãi
    public function getFinalPrice() {
        return $this->price * (1 - $this->salePercent / 100);
    }

    // Phương thức quản lý variants
    public function addVariant($variant) {
        $this->variants[] = $variant;
    }

    public function getVariants() {
        return $this->variants;
    }

    // Phương thức quản lý variant types
    public function addVariantType($variantType) {
        $this->variantTypes[] = $variantType;
    }

    public function getVariantTypes() {
        return $this->variantTypes;
    }

    // Phương thức quản lý images
    public function addImage($image) {
        $this->images[] = $image;
    }

    public function getImages() {
        return $this->images;
    }

    public function getThumbnail() {
        foreach ($this->images as $image) {
            if ($image->getIsThumbnail()) {
                return $image->getImageUrl();
            }
        }
        return null;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'productName' => $this->productName,
            'description' => $this->description,
            'origin' => $this->origin,
            'category' => $this->category,
            'price' => $this->price,
            'salePercent' => $this->salePercent,
            'stockQuantity' => $this->stockQuantity,
            'sold' => $this->sold,
            'status' => $this->status,
            'finalPrice' => $this->getFinalPrice(),
            'thumbnail' => $this->getThumbnail(),
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
