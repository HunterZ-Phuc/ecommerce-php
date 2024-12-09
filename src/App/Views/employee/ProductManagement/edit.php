<?php foreach ($products as $product): ?>
    <div class="modal fade" id="editModal-<?= $product['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Sản phẩm</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="editForm-<?= $product['id'] ?>" method="POST" action="/ecommerce-php/employee/product-management/edit/<?= $product['id'] ?>" enctype="multipart/form-data">
                        <!-- Thông tin cơ bản -->
                        <div class="mb-4">
                            <h6 class="mb-3">Thông tin cơ bản</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Tên sản phẩm</label>
                                        <input type="text" name="productName" class="form-control" value="<?= htmlspecialchars($product['productName']) ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Danh mục</label>
                                        <select name="category" class="form-control" required>
                                            <option value="FRUITS" <?= $product['category'] == 'FRUITS' ? 'selected' : '' ?>>Trái cây</option>
                                            <option value="VEGETABLES" <?= $product['category'] == 'VEGETABLES' ? 'selected' : '' ?>>Rau củ</option>
                                            <option value="GRAINS" <?= $product['category'] == 'GRAINS' ? 'selected' : '' ?>>Ngũ cốc</option>
                                            <option value="OTHERS" <?= $product['category'] == 'OTHERS' ? 'selected' : '' ?>>Khác</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phần trăm giảm giá (%)</label>
                                        <input type="number" 
                                               name="salePercent" 
                                               class="form-control" 
                                               value="<?= $product['salePercent'] ?? 0 ?>"
                                               min="0" 
                                               max="100">
                                        <small class="text-muted">Nhập số từ 0-100</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Trạng thái</label>
                                        <select name="status" class="form-control" required>
                                            <option value="ON_SALE" <?= $product['status'] == 'ON_SALE' ? 'selected' : '' ?>>Đang bán</option>
                                            <option value="SUSPENDED" <?= $product['status'] == 'SUSPENDED' ? 'selected' : '' ?>>Tạm ngưng</option>
                                            <option value="OUT_OF_STOCK" <?= $product['status'] == 'OUT_OF_STOCK' ? 'selected' : '' ?>>Hết hàng</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Mô tả</label>
                                        <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($product['description']) ?></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Ảnh chính sản phẩm -->
                        <div class="mb-4">
                            <h6 class="mb-3">Ảnh chính sản phẩm</h6>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label class="form-label">Ảnh hiện tại</label>
                                        <div class="d-flex gap-2 mb-2">
                                        <?php
                $mainImage = null;
                if (!empty($product['images'])) {
                    foreach ($product['images'] as $image) {
                        if ($image['variantId'] === null && $image['isThumbnail']) {
                            $mainImage = $image;
                            break;
                        }
                    }
                }
                ?>
                <?php if ($mainImage): ?>
                    <img src="<?= '/ecommerce-php/public' . $mainImage['imageUrl'] ?>" 
                         alt="Main Product Image" 
                         style="width: 200px; height: 200px; object-fit: cover;">
                <?php else: ?>
                    <p>Chưa có ảnh chính</p>
                <?php endif; ?>
            </div>
            <label class="form-label">Thay đổi ảnh chính</label>
            <input type="file" 
                   name="mainImage" 
                   class="form-control"
                   accept="image/jpeg,image/png,image/gif">
        </div>
                                </div>
                            </div>
                        </div>

                        <!-- Biến thể sản phẩm -->
                        <div class="mb-4">
                            <h6 class="mb-3">Biến thể sản phẩm</h6>
                            <?php if (isset($product['variants']) && is_array($product['variants'])): ?>
                                <?php foreach ($product['variants'] as $variant): ?>
                                    <div class="variant-item">
                                        <input type="hidden" name="variants[<?= $variant['id'] ?>][id]" value="<?= $variant['id'] ?>">
                                        
                                        <!-- Hiển thị tên biến thể -->
                                        <div class="variant-title mb-3">
                                            <strong>Biến thể: </strong>
                                            <?php 
                                            if (!empty($variant['combinations'])) {
                                                $variantParts = [];
                                                foreach ($variant['combinations'] as $combination) {
                                                    if (!is_numeric($combination['typeName'])) {
                                                        $variantParts[] = $combination['typeName'] . ': ' . $combination['value'];
                                                    }
                                                }
                                                echo htmlspecialchars(implode(' | ', $variantParts));
                                            } else {
                                                echo 'Mặc định';
                                            }
                                            ?>
                                        </div>

                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Số lượng</label>
                                                    <input type="number" 
                                                           name="variants[<?= $variant['id'] ?>][quantity]" 
                                                           class="form-control" 
                                                           value="<?= $variant['quantity'] ?>">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">Giá</label>
                                                    <input type="number" 
                                                           name="variants[<?= $variant['id'] ?>][price]" 
                                                           class="form-control" 
                                                           value="<?= $variant['price'] ?>">
                                                </div>
                                            </div>
                                            <!-- Phần hiển thị ảnh biến thể -->
                                            <div class="col-12">
                                                <div class="mb-3">
                                                    <label class="form-label">Hình ảnh biến thể hiện tại</label>
                                                    <div class="d-flex gap-2 mb-2">
                                                        <?php 
                                                        // Chỉ hiển thị ảnh của biến thể
                                                        $variantImages = array_filter($variant['images'], function($img) {
                                                            return $img['variantId'] !== null;
                                                        });
                                                        
                                                        if (!empty($variantImages)): 
                                                        ?>
                                                            <?php foreach ($variantImages as $image): ?>
                                                                <img src="<?= '/ecommerce-php/public' . $image['imageUrl'] ?>" 
                                                                     alt="Variant Image" 
                                                                     style="width: 100px; height: 100px; object-fit: cover;">
                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <p>Chưa có ảnh biến thể</p>
                                                        <?php endif; ?>
                                                    </div>
                                                    <label class="form-label">Thay đổi ảnh biến thể</label>
                                                    <input type="file" 
                                                           name="variants[<?= $variant['id'] ?>][image]" 
                                                           class="form-control"
                                                           accept="image/jpeg,image/png,image/gif">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
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
<?php endforeach; ?>

<script>
document.querySelectorAll('[id^="editForm-"]').forEach(form => {
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        try {
            const formData = new FormData(this);
            
            console.log('Form Data:');
            for (let pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            
            let result;
            const contentType = response.headers.get('content-type');
            if (contentType && contentType.includes('application/json')) {
                result = await response.json();
            } else {
                throw new Error('Invalid response format');
            }
            
            if (result.success) {
                alert(result.message || 'Cập nhật thành công!');
                window.location.reload();
            } else {
                alert(result.error || 'Có lỗi xảy ra!');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật! Vui lòng thử lại.');
        }
    });
});
</script>
