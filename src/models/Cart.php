class Cart {
    private $id;
    private $userId;
    private $variantId;
    private $quantity;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['userId'] ?? null;
        $this->variantId = $data['variantId'] ?? null;
        $this->quantity = $data['quantity'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Cập nhật số lượng
    public function updateQuantity($quantity) {
        if ($quantity < 1) {
            throw new Exception("Số lượng phải lớn hơn 0");
        }
        $this->quantity = $quantity;
    }
}
