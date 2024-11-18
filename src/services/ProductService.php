class ProductService {
    private $db;
    
    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function createProduct($productData) {
        try {
            // Validate dữ liệu
            $this->validateProductData($productData);
            
            // Tạo sản phẩm mới
            $product = new Product($productData);
            
            // Lưu vào database
            $result = $this->db->insert('products', $product->toArray());
            
            return $result;
        } catch (Exception $e) {
            throw new Exception("Lỗi khi tạo sản phẩm: " . $e->getMessage());
        }
    }
    
    public function updateProduct($id, $productData) {
        // Logic cập nhật sản phẩm
    }
    
    public function deleteProduct($id) {
        // Logic xóa sản phẩm
    }
    
    public function updateStock($productId, $quantity) {
        // Logic cập nhật tồn kho
    }
    
    private function validateProductData($data) {
        // Logic validate
    }
}
