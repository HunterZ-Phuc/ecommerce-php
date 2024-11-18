class ProductImage {
    private $id;
    private $productId;
    private $variantId;
    private $imageUrl;
    private $isThumbnail;
    private $displayOrder;
    private $createdAt;
    private $updatedAt;

    public function __construct($data = []) {
        $this->id = $data['id'] ?? null;
        $this->productId = $data['productId'] ?? null;
        $this->variantId = $data['variantId'] ?? null;
        $this->imageUrl = $data['imageUrl'] ?? null;
        $this->isThumbnail = $data['isThumbnail'] ?? false;
        $this->displayOrder = $data['displayOrder'] ?? 0;
        $this->createdAt = $data['createdAt'] ?? null;
        $this->updatedAt = $data['updatedAt'] ?? null;
    }
}
