<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Sửa Sản phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editForm" method="POST" action="/ecommerce-php/employee/product-management/edit/<?= $product['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="productName" class="form-control" value="<?= $product['productName'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <input type="text" name="category" class="form-control" value="<?= $product['category'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Giá</label>
                        <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Trạng thái</label>
                        <select name="status" class="form-control">
                            <option value="ON_SALE" <?= $product['status'] == 'ON_SALE' ? 'selected' : '' ?>>Đang bán</option>
                            <option value="OUT_OF_STOCK" <?= $product['status'] == 'OUT_OF_STOCK' ? 'selected' : '' ?>>Hết hàng</option>
                        </select>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('editForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const formData = new FormData(this);
        const response = await fetch(this.action, {
            method: 'POST',
            body: formData
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('Cập nhật sản phẩm thành công!');
            window.location.reload();
        } else {
            alert(result.message || 'Có lỗi xảy ra!');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật sản phẩm!');
    }
});
</script>
