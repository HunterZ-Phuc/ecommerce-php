class Order {
    private $id;
    private $userId;
    private $items;
    private $totalAmount;
    private $status;
    
    public function create() {
        // Logic tạo đơn hàng mới
    }
    
    public function updateStatus($newStatus) {
        // Cập nhật trạng thái đơn hàng
    }
    
    public function calculateTotal() {
        // Tính tổng giá trị đơn hàng
    }
}
