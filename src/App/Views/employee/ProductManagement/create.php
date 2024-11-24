<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Sản phẩm Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/php-mvc/employee/product-management/create" method="POST" enctype="multipart/form-data" id="createProductForm">
                    <!-- Thông tin cơ bản -->
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="productName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Danh mục</label>
                        <select name="category" class="form-control" required>
                            <option value="FRUITS">Trái Cây</option>
                            <option value="VEGETABLES">Rau Củ</option>
                            <option value="GRAINS">Ngũ Cốc</option>
                            <option value="OTHERS">Khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Xuất xứ</label>
                        <input type="text" name="origin" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Checkbox biến thể -->
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="hasVariants" name="hasVariants">
                            <label class="form-check-label" for="hasVariants">
                                Sản phẩm có phân loại hàng
                            </label>
                        </div>
                    </div>

                    <!-- Phần không có biến thể -->
                    <div id="basicPricing">
                        <div class="mb-3">
                            <label class="form-label">Giá</label>
                            <input type="number" name="price" class="form-control" min="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Số lượng</label>
                            <input type="number" name="stockQuantity" class="form-control" min="0">
                        </div>
                    </div>

                    <!-- Include phần biến thể -->
                    <?php require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/addVariants.php'; ?>

                    <!-- Ảnh sản phẩm -->
                    <div class="mb-3">
                        <label class="form-label">Ảnh sản phẩm</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hasVariantsCheckbox = document.getElementById('hasVariants');
    const variantSection = document.getElementById('variantSection');
    const basicPricing = document.getElementById('basicPricing');

    hasVariantsCheckbox.addEventListener('change', function() {
        variantSection.style.display = this.checked ? 'block' : 'none';
        basicPricing.style.display = this.checked ? 'none' : 'block';
    });

    // Thêm event listener cho form
    document.getElementById('createProductForm').addEventListener('submit', async function(e) {
        e.preventDefault(); // Ngăn form tự động submit
        
        if (!validateForm()) {
            return false;
        }

        // Log dữ liệu form trước khi submit
        const formData = new FormData(this);

        // Thêm files của biến thể vào FormData
        const variantImageInputs = document.querySelectorAll('input[name="variant_images[]"]');
        variantImageInputs.forEach((input, index) => {
            if (input.files[0]) {
                formData.append(`variant_combinations[${index}][image]`, input.files[0]);
            }
        });

        try {
            // Submit form bằng AJAX
            const response = await fetch(this.action, {
                method: 'POST',
                body: formData
            });

            const data = await response.text();
            console.log("=== Phản hồi từ server ===");
            console.log(data);

            // Hiển thị thông báo thành công
            alert('Thêm sản phẩm thành công!');
            
            // Đóng modal (nếu muốn)
            const modal = bootstrap.Modal.getInstance(document.getElementById('createModal'));
            modal.hide();
            
            // Reset form (nếu muốn)
            // this.reset();
            
            // KHÔNG chuyển hướng trang
            // window.location.href = '/php-mvc/employee/product-management';

            const result = await response.json();
            
            if (result.success) {
                alert('Thêm sản phẩm thành công!');
                window.location.reload();
            } else {
                alert(result.error || 'Có lỗi xảy ra khi lưu sản phẩm');
            }
        } catch (error) {
            console.error('=== Lỗi khi gửi dữ liệu ===');
            console.error(error);
            alert('Có lỗi xảy ra khi lưu sản phẩm');
        }
    });
});

function validateForm() {
    const hasVariants = document.getElementById('hasVariants').checked;
    
    if (hasVariants) {
        // Kiểm tra có ít nhất một loại biến thể
        const variantTypes = document.querySelectorAll('input[name="variant_types[]"]');
        let hasValidType = false;
        variantTypes.forEach(type => {
            if (type.value.trim()) hasValidType = true;
        });
        
        if (!hasValidType) {
            alert('Vui lòng thêm ít nhất một loại biến thể');
            return false;
        }
        
        // Kiểm tra có giá và số lượng cho mỗi biến thể
        const variantPrices = document.querySelectorAll('input[name^="variant_prices"]');
        const variantQuantities = document.querySelectorAll('input[name^="variant_quantities"]');
        
        if (variantPrices.length === 0 || variantQuantities.length === 0) {
            alert('Vui lòng hoàn tất thông tin biến thể');
            return false;
        }

        // Log dữ liệu biến thể
        console.log("=== Kiểm tra dữ liệu biến thể trước khi submit ===");
        console.log("Số lượng giá biến thể:", variantPrices.length);
        console.log("Giá biến thể:", Array.from(variantPrices).map(input => input.value));
        console.log("Số lượng biến thể:", Array.from(variantQuantities).map(input => input.value));
        
    } else {
        // Kiểm tra giá và số lượng cho sản phẩm không có biến thể
        const price = document.querySelector('input[name="price"]').value;
        const quantity = document.querySelector('input[name="stockQuantity"]').value;
        
        if (!price || !quantity || price < 0 || quantity < 0) {
            alert('Vui lòng nhập đầy đủ và chính xác giá và số lượng');
            return false;
        }
    }
    
    // Thêm kiểm tra ảnh
    if (!validateImages()) {
        return false;
    }
    
    return true;
}

// Thêm hàm kiểm tra file ảnh
function validateImages() {
    const variantImages = document.querySelectorAll('input[name="variant_images[]"]');
    const validTypes = ['image/jpeg', 'image/png', 'image/gif'];

    for (let input of variantImages) {
        if (input.files[0]) {
            const file = input.files[0];
            if (!validTypes.includes(file.type)) {
                alert('Chỉ chấp nhận file ảnh định dạng JPG, PNG hoặc GIF');
                return false;
            }
        }
    }
    return true;
}
</script>


