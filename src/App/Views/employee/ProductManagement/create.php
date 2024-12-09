<div class="modal fade" id="categoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chọn Danh Mục Sản Phẩm</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <select id="productCategory" class="form-control" required>
                    <option value="">Chọn danh mục</option>
                    <option value="FRUITS">Trái Cây</option>
                    <option value="VEGETABLES">Rau Củ</option>
                    <option value="GRAINS">Ngũ Cốc</option>
                    <option value="OTHERS">Khác</option>
                </select>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-primary" id="confirmCategory">Xác nhận</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Sản phẩm Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/ecommerce-php/employee/product-management/create" method="POST"
                    enctype="multipart/form-data" id="createProductForm">
                    <!-- Thông tin cơ bản -->
                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="productName" class="form-control" required>
                    </div>

                    <!-- Thêm vào phần đầu của form, sau phần tên sản phẩm -->
                    <div class="mb-3">
                        <label class="form-label">Số lượng sản phẩm muốn tạo</label>
                        <input type="number" name="numberOfProducts" class="form-control" value="1" min="1" max="10">
                        <small class="text-muted">Nhập số lượng sản phẩm giống hệt nhau bạn muốn tạo (tối đa 10)</small>
                    </div>

                    <!-- Ẩn select danh mục và thay bằng input hidden -->
                    <input type="hidden" name="category" id="selectedCategoryInput">

                    <div class="mb-3">
                        <label class="form-label">Xuất xứ</label>
                        <input type="text" name="origin" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Phần biến thể -->
                    <div id="variantSection">
                        <?php require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/addVariants.php'; ?>
                    </div>

                    <!-- Ảnh sản phẩm -->
                    <div class="mb-3">
                        <label class="form-label">Ảnh sản phẩm</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" class="btn btn-primary">Lưu sản phẩm</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    // Thêm kiểm tra để đảm bảo script chỉ chạy một lần
    if (!window.createProductFormInitialized) {
        window.createProductFormInitialized = true;

        document.addEventListener('DOMContentLoaded', function () {
            const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
            const createModal = new bootstrap.Modal(document.getElementById('createModal'));
            const createProductForm = document.getElementById('createProductForm');

            // Xử lý nút thêm sản phẩm
            document.getElementById('addProductButton')?.addEventListener('click', function () {
                categoryModal.show();
            });

            // Xử lý nút xác nhận danh mục
            document.getElementById('confirmCategory')?.addEventListener('click', function () {
                const selectedCategory = document.getElementById('productCategory').value;
                if (selectedCategory) {
                    // Đóng modal categoryModal
                    categoryModal.hide();

                    // Cập nhật giá trị danh mục trong form
                    document.getElementById('selectedCategoryInput').value = selectedCategory;
                    createModal.show();
                } else {
                    alert('Vui lòng chọn danh mục sản phẩm.');
                }
            });

            // Xử lý form submit - chỉ add listener một lần
            if (createProductForm && !createProductForm.hasAttribute('data-initialized')) {
                createProductForm.setAttribute('data-initialized', 'true');

                createProductForm.addEventListener('submit', async function (e) {
                    e.preventDefault();

                    const submitButton = this.querySelector('button[type="submit"]');
                    submitButton.disabled = true;
                    submitButton.innerHTML = 'Đang xử lý...';

                    try {
                        const formData = new FormData(this);
                        const numberOfProducts = parseInt(formData.get('numberOfProducts')) || 1;

                        // Thu thập dữ liệu biến thể
                        document.querySelectorAll('#variantCombinations tr').forEach((row, index) => {
                            const variant = {
                                price: row.querySelector('input[name^="variant_combinations"][name$="[price]"]').value,
                                quantity: row.querySelector('input[name^="variant_combinations"][name$="[quantity]"]').value
                            };

                            // Thu thập các thuộc tính biến thể
                            row.querySelectorAll('input[type="hidden"]').forEach(input => {
                                const matches = input.name.match(/\[([^\]]+)\]/g);
                                if (matches && matches.length >= 2) {
                                    const type = matches[matches.length - 1].replace(/[\[\]]/g, '');
                                    variant[type] = input.value;
                                }
                            });

                            // Thu thập ảnh biến thể
                            const imageInput = row.querySelector('input[type="file"]');
                            if (imageInput && imageInput.files[0]) {
                                formData.append(`variant_combinations[${index}][image]`, imageInput.files[0]);
                            }

                            // Thêm thông tin biến thể vào formData
                            Object.entries(variant).forEach(([key, value]) => {
                                formData.append(`variant_combinations[${index}][${key}]`, value);
                            });
                        });



                        // Tạo mảng promises để xử lý nhiều request cùng lúc
                        const createPromises = [];

                        for (let i = 0; i < numberOfProducts; i++) {
                            const clonedFormData = new FormData(this);
                            // Xóa trường numberOfProducts khỏi formData gửi đi
                            clonedFormData.delete('numberOfProducts');

                            createPromises.push(
                                fetch(this.action, {
                                    method: 'POST',
                                    body: clonedFormData
                                }).then(response => response.json())
                            );
                        }

                        // Chờ tất cả requests hoàn thành
                        const results = await Promise.all(createPromises);

                        // Kiểm tra kết quả
                        const hasError = results.some(result => !result.success);

                        if (!hasError) {
                            alert(`Đã tạo thành công ${numberOfProducts} sản phẩm!`);
                            window.location.href = window.location.href;
                        } else {
                            const errors = results
                                .filter(result => !result.success)
                                .map(result => result.error)
                                .join('\n');
                            throw new Error(`Có lỗi xảy ra:\n${errors}`);
                        }

                    } catch (error) {
                        console.error('Error:', error);
                        alert('Có lỗi xảy ra khi thêm sản phẩm!');
                    } finally {
                        submitButton.disabled = false;
                        submitButton.innerHTML = 'Lưu sản phẩm';
                    }
                });
            }
        });
    }
</script>