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
    private $items = []; // Quan hệ 1-n với OrderItem

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->userId = $data['userId'] ?? null;
        $this->addressId = $data['addressId'] ?? null;
        $this->productList = $data['productList'] ?? '';
        $this->totalAmount = $data['totalAmount'] ?? 0;
        $this->orderDate = $data['orderDate'] ?? null;
        $this->status = $data['status'] ?? 'PENDING';
        $this->paymentStatus = $data['paymentStatus'] ?? 'PENDING';
        // ... khởi tạo các thuộc tính khác
    }

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

    // Cập nhật trạng thái
    public function updateStatus($newStatus) {
        $validStatuses = [
            'PENDING', 'PROCESSING', 'CONFIRMED', 'READY_FOR_SHIPPING',
            'SHIPPING', 'SHIPPED', 'DELIVERED', 'CANCELLED', 'RETURNED'
        ];
        
        if (!in_array($newStatus, $validStatuses)) {
            throw new Exception("Trạng thái không hợp lệ");
        }
        
        $this->status = $newStatus;
    }
}
