class VariantType {
    private $id;
    private $productId;
    private $name;
    private $createdAt;
    private $updatedAt;
    private $values = []; // Quan hệ với VariantValue

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->name = $data['name'] ?? null;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }
}
