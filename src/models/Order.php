<?php
namespace Models;

use Exception;

class Order {
    private $id;
    private $userId;
    private $addressId;
    private $productList;
    private $totalAmount;
    private $orderDate;
    private $shippingDate;
    private $deliveryDate;
    private $status;
    private $paymentId;
    private $paymentStatus;
    private $note;
    private $createdAt;
    private $updatedAt;
    
    // Các quan hệ
    private $items = []; // Quan hệ 1-n với OrderItem
    private $user; // Quan hệ với User
    private $address; // Quan hệ với Address
    private $payment; // Quan hệ với Payment

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['userId'] ?? null;
        $this->addressId = $data['addressId'] ?? null;
        $this->productList = $data['productList'] ?? '';
        $this->totalAmount = $data['totalAmount'] ?? 0;
        $this->orderDate = $data['orderDate'] ?? null;
        $this->shippingDate = $data['shippingDate'] ?? null;
        $this->deliveryDate = $data['deliveryDate'] ?? null;
        $this->status = $data['status'] ?? 'PENDING';
        $this->paymentId = $data['paymentId'] ?? null;
        $this->paymentStatus = $data['paymentStatus'] ?? 'PENDING';
        $this->note = $data['note'] ?? '';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getUserId() { return $this->userId; }
    public function getAddressId() { return $this->addressId; }
    public function getTotalAmount() { return $this->totalAmount; }
    public function getOrderDate() { return $this->orderDate; }
    public function getShippingDate() { return $this->shippingDate; }
    public function getDeliveryDate() { return $this->deliveryDate; }
    public function getStatus() { return $this->status; }
    public function getPaymentId() { return $this->paymentId; }
    public function getPaymentStatus() { return $this->paymentStatus; }
    public function getNote() { return $this->note; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getItems() { return $this->items; }
    public function getUser() { return $this->user; }
    public function getAddress() { return $this->address; }
    public function getPayment() { return $this->payment; }

    // Setters
    public function setUserId($value) { $this->userId = $value; }
    public function setAddressId($value) { $this->addressId = $value; }
    public function setShippingDate($value) { $this->shippingDate = $value; }
    public function setDeliveryDate($value) { $this->deliveryDate = $value; }
    public function setNote($value) { $this->note = $value; }
    public function setUser($user) { $this->user = $user; }
    public function setAddress($address) { $this->address = $address; }
    public function setPayment($payment) { $this->payment = $payment; }

    // Thêm sản phẩm vào đơn hàng
    public function addItem(OrderItem $item) {
        $this->items[] = $item;
        $this->calculateTotal();
    }

    // Tính tổng tiền
    private function calculateTotal() {
        $this->totalAmount = array_reduce($this->items, function($total, $item) {
            return $total + ($item->getPrice() * $item->getQuantity());
        }, 0);
    }

    // Cập nhật trạng thái đơn hàng
    public function updateStatus($newStatus) {
        $validStatuses = [
            'PENDING', 'PROCESSING', 'CONFIRMED', 'READY_FOR_SHIPPING',
            'SHIPPING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED'
        ];
        
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception("Trạng thái không hợp lệ");
        }
        
        $this->status = $newStatus;
        
        // Cập nhật các ngày tương ứng
        switch ($newStatus) {
            case 'SHIPPING':
                $this->shippingDate = date('Y-m-d H:i:s');
                break;
            case 'DELIVERED':
                $this->deliveryDate = date('Y-m-d H:i:s');
                break;
        }
    }

    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus($newStatus) {
        $validStatuses = ['PENDING', 'COMPLETED', 'FAILED', 'REFUNDED'];
        
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception("Trạng thái thanh toán không hợp lệ");
        }
        
        $this->paymentStatus = $newStatus;
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'userId' => $this->userId,
            'addressId' => $this->addressId,
            'totalAmount' => $this->totalAmount,
            'orderDate' => $this->orderDate,
            'shippingDate' => $this->shippingDate,
            'deliveryDate' => $this->deliveryDate,
            'status' => $this->status,
            'paymentId' => $this->paymentId,
            'paymentStatus' => $this->paymentStatus,
            'note' => $this->note,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'items' => array_map(fn($item) => $item->toArray(), $this->items),
            'user' => $this->user ? $this->user->toArray() : null,
            'address' => $this->address ? $this->address->toArray() : null,
            'payment' => $this->payment ? $this->payment->toArray() : null
        ];
    }
}
