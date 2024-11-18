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

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->sku = $data['sku'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->quantity = $data['quantity'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters & Setters

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
}
