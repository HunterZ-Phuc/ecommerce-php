class OrderService {
    private $db;
    private $productService;
    
    public function __construct(Database $db, ProductService $productService) {
        $this->db = $db;
        $this->productService = $productService;
    }
    
    public function createOrder($userId, $items) {
        try {
            // Validate order
            $this->validateOrder($items);
            
            // Tính tổng tiền
            $total = $this->calculateTotal($items);
            
            // Tạo đơn hàng
            $order = new Order([
                'userId' => $userId,
                'totalAmount' => $total,
                'orderDate' => date('Y-m-d H:i:s')
            ]);
            
            // Lưu đơn hàng và items
            $this->db->beginTransaction();
            $orderId = $this->db->insert('orders', $order->toArray());
            
            foreach ($items as $item) {
                $orderItem = new OrderItem([
                    'orderId' => $orderId,
                    'productId' => $item->productId,
                    'quantity' => $item->quantity,
                    'price' => $item->price
                ]);
                $this->db->insert('order_items', $orderItem->toArray());
                
                // Cập nhật số lượng tồn kho
                $this->productService->updateStock($item->productId, -$item->quantity);
            }
            
            $this->db->commit();
            return $orderId;
            
        } catch (Exception $e) {
            $this->db->rollback();
            throw new Exception("Lỗi khi tạo đơn hàng: " . $e->getMessage());
        }
    }
}
