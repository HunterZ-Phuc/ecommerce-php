<?php foreach ($products as $product): ?>
    <div class="modal fade" id="deleteModal-<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa sản phẩm "<?= $product['productName'] ?>" không?</p>
                    <p class="text-danger">Lưu ý: Hành động này sẽ xóa tất cả thông tin liên quan đến sản phẩm bao gồm biến
                        thể, hình ảnh và không thể khôi phục.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="button" class="btn btn-danger" onclick="deleteProduct(<?= $product['id'] ?>)">Xóa</button>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>

<script>
    async function deleteProduct(id) {
        try {
            const response = await fetch(`/ecommerce-php/employee/product-management/delete/${id}`, {
                method: 'POST'
            });

            const result = await response.json();

            if (result.success) {
                alert(result.message);
                window.location.reload();
            } else {
                alert(result.error || 'Có lỗi xảy ra khi xóa sản phẩm');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi xóa sản phẩm');
        }
    }
</script>