<?php
namespace Models;

class OrderItem {
    private $id;
    private $productId;
    private $variantId;
    private $quantity;
    private $orderId;
    private $price;
    private $createdAt;
    private $updatedAt;
    
    // Các quan hệ
    private $product;
    private $variant;
    private $order;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->variantId = $data['variantId'] ?? null;
        $this->quantity = $data['quantity'] ?? 0;
        $this->orderId = $data['orderId'] ?? null;
        $this->price = $data['price'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getProductId() { return $this->productId; }
    public function getVariantId() { return $this->variantId; }
    public function getQuantity() { return $this->quantity; }
    public function getOrderId() { return $this->orderId; }
    public function getPrice() { return $this->price; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getProduct() { return $this->product; }
    public function getVariant() { return $this->variant; }
    public function getOrder() { return $this->order; }

    // Setters
    public function setProductId($value) { $this->productId = $value; }
    public function setVariantId($value) { $this->variantId = $value; }
    public function setQuantity($value) { $this->quantity = $value; }
    public function setOrderId($value) { $this->orderId = $value; }
    public function setPrice($value) { $this->price = $value; }
    public function setProduct($value) { $this->product = $value; }
    public function setVariant($value) { $this->variant = $value; }
    public function setOrder($value) { $this->order = $value; }

    // Tính tổng tiền của item
    public function getSubtotal() {
        return $this->price * $this->quantity;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'productId' => $this->productId,
            'variantId' => $this->variantId,
            'quantity' => $this->quantity,
            'orderId' => $this->orderId,
            'price' => $this->price,
            'subtotal' => $this->getSubtotal(),
            'product' => $this->product ? $this->product->toArray() : null,
            'variant' => $this->variant ? $this->variant->toArray() : null,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
        ];
    }
}
