<?php
namespace Models;

use Exception;

class Payment {
    private $id;
    private $amount;
    private $paymentMethod;
    private $qrImage;
    private $refNo;
    private $paymentStatus;
    private $paymentDate;
    private $createdAt;
    private $updatedAt;
    private $order; // Quan hệ với Order

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->amount = $data['amount'] ?? 0;
        $this->paymentMethod = $data['paymentMethod'] ?? null;
        $this->qrImage = $data['qrImage'] ?? null;
        $this->refNo = $data['refNo'] ?? null;
        $this->paymentStatus = $data['paymentStatus'] ?? 'PENDING';
        $this->paymentDate = $data['paymentDate'] ?? null;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters
    public function getId() { return $this->id; }
    public function getAmount() { return $this->amount; }
    public function getPaymentMethod() { return $this->paymentMethod; }
    public function getQrImage() { return $this->qrImage; }
    public function getRefNo() { return $this->refNo; }
    public function getPaymentStatus() { return $this->paymentStatus; }
    public function getPaymentDate() { return $this->paymentDate; }
    public function getCreatedAt() { return $this->createdAt; }
    public function getUpdatedAt() { return $this->updatedAt; }
    public function getOrder() { return $this->order; }

    // Setters
    public function setAmount($value) { $this->amount = $value; }
    public function setPaymentMethod($value) { $this->paymentMethod = $value; }
    public function setQrImage($value) { $this->qrImage = $value; }
    public function setRefNo($value) { $this->refNo = $value; }
    public function setOrder($value) { $this->order = $value; }

    // Cập nhật trạng thái thanh toán
    public function updatePaymentStatus($newStatus) {
        $validStatuses = ['PENDING', 'COMPLETED', 'FAILED', 'REFUNDED'];
        
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception("Trạng thái thanh toán không hợp lệ");
        }
        
        $this->paymentStatus = $newStatus;
        if ($newStatus === 'COMPLETED') {
            $this->paymentDate = date('Y-m-d H:i:s');
        }
    }

    // Chuyển đổi thành mảng
    public function toArray() {
        return [
            'id' => $this->id,
            'amount' => $this->amount,
            'paymentMethod' => $this->paymentMethod,
            'qrImage' => $this->qrImage,
            'refNo' => $this->refNo,
            'paymentStatus' => $this->paymentStatus,
            'paymentDate' => $this->paymentDate,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt
        ];
    }
}
