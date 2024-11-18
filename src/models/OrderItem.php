class OrderItem {
    private $id;
    private $productId;
    private $variantId;
    private $quantity;
    private $orderId;
    private $price;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->variantId = $data['variantId'] ?? null;
        $this->quantity = $data['quantity'] ?? 0;
        $this->orderId = $data['orderId'] ?? null;
        $this->price = $data['price'] ?? 0;
    }
}
