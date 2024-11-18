class Product {
    private $id;
    private $productName;
    private $description;
    private $origin;
    private $category;
    private $price;
    private $salePercent;
    private $stockQuantity;
    private $sold;
    private $status;
    private $createdAt;
    private $updatedAt;
    private $variants = []; // Quan hệ 1-n với ProductVariant
    private $images = []; // Quan hệ 1-n với ProductImage

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productName = $data['productName'] ?? '';
        $this->description = $data['description'] ?? '';
        $this->origin = $data['origin'] ?? '';
        $this->category = $data['category'] ?? '';
        $this->price = $data['price'] ?? 0;
        $this->salePercent = $data['salePercent'] ?? 0;
        $this->stockQuantity = $data['stockQuantity'] ?? 0;
        $this->sold = $data['sold'] ?? 0;
        $this->status = $data['status'] ?? 'ON_SALE';
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }

    // Getters & Setters
    
    // Tính giá sau khuyến mãi
    public function getDiscountedPrice() {
        return $this->price * (1 - $this->salePercent / 100);
    }

    // Kiểm tra còn hàng
    public function isInStock() {
        return $this->stockQuantity > 0;
    }

    // Thêm biến thể
    public function addVariant(ProductVariant $variant) {
        $this->variants[] = $variant;
    }

    // Thêm ảnh
    public function addImage(ProductImage $image) {
        $this->images[] = $image;
    }
}
