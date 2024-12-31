<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['success'];
        unset($_SESSION['success']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?php
        echo $_SESSION['error'];
        unset($_SESSION['error']);
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý Sản phẩm</h1>
    <button type="button" class="btn btn-primary" id="toggleFormBtn">
        Thêm Sản phẩm
    </button>
</div>

<!-- Thêm form container có thể ẩn/hiện -->
<div id="addProductForm" class="card mb-4" style="display: none;">
    <div class="card-header">
        <ul class="nav nav-tabs card-header-tabs">
            <li class="nav-item">
                <a class="nav-link active" data-bs-toggle="tab" href="#basicInfo">
                    Thông tin cơ bản
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-bs-toggle="tab" href="#variants">
                    Phân loại hàng
                </a>
            </li>
        </ul>
    </div>
    
    <div class="card-body">
        <form action="/ecommerce-php/employee/product-management/create" method="POST" enctype="multipart/form-data" id="createProductForm">
            <div class="tab-content">
                <!-- Tab thông tin cơ bản -->
                <div class="tab-pane fade show active" id="basicInfo">
                    <div class="mb-3">
                        <label class="form-label">Danh mục sản phẩm</label>
                        <select name="category" class="form-control" required>
                            <option value="">Chọn danh mục</option>
                            <option value="FRUITS">Trái Cây</option>
                            <option value="VEGETABLES">Rau Củ</option>
                            <option value="GRAINS">Ngũ Cốc</option>
                            <option value="OTHERS">Khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Tên sản phẩm</label>
                        <input type="text" name="productName" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Xuất xứ</label>
                        <input type="text" name="origin" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3"></textarea>
                    </div>

                    <!-- Mở comment của thẻ div dưới đây khi muốn tạo nhiều sản phẩm để test chương trình -->

                    <!-- <div class="mb-3">
                        <label class="form-label">Số lượng sản phẩm muốn tạo</label>
                        <input type="number" name="numberOfProducts" class="form-control" value="1" min="1" max="20">
                        <small class="text-muted">Nhập số lượng sản phẩm giống hệt nhau bạn muốn tạo (tối đa 20)</small>
                    </div> -->

                    <div class="mb-3">
                        <label class="form-label">Ảnh sản phẩm</label>
                        <input type="file" name="images[]" class="form-control" multiple>
                    </div>

                    <button type="button" class="btn btn-primary" onclick="switchToVariants()">
                        Tiếp tục
                    </button>
                </div>

                <!-- Tab phân loại hàng -->
                <div class="tab-pane fade" id="variants">
                    <?php require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/addVariants.php'; ?>
                    
                    <div class="mt-3">
                        <button type="button" class="btn btn-secondary" onclick="switchToBasicInfo()">
                            Quay lại
                        </button>
                        <button type="submit" class="btn btn-primary">
                            Lưu sản phẩm
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Thêm form tìm kiếm -->
<div class="row mb-4">
    <div class="col-md-8">
        <form action="" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" name="search" class="form-control" placeholder="Tìm kiếm sản phẩm..."
                    value="<?= htmlspecialchars($search ?? '') ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm kiếm
                </button>
            </div>
            <?php if (!empty($search)): ?>
                <a href="?page=1" class="btn btn-outline-secondary d-flex align-items-center">
                    <i class="fas fa-undo me-1"></i> Đặt lại
                </a>
            <?php endif; ?>
        </form>
    </div>
</div>

<style>
    .input-group {
        flex: 1;
    }
</style>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Hình Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Danh mục</th>
            <th>Đã bán</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td>
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
                            alt="<?= htmlspecialchars($product['productName']) ?>" class="img-thumbnail"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                        <img src="/ecommerce-php/public/assets/images/no-image.png" alt="No Image" class="img-thumbnail"
                            style="width: 50px; height: 50px; object-fit: cover;">
                    <?php endif; ?>
                </td>
                <td><?= $product['productName'] ?></td>
                <td>
                    <?php
                    $categoryLabels = [
                        'FRUITS' => 'Trái cây',
                        'VEGETABLES' => 'Rau củ',
                        'GRAINS' => 'Ngũ cốc',
                        'OTHERS' => 'Khác'
                    ];
                    echo isset($categoryLabels[$product['category']]) ? $categoryLabels[$product['category']] : $product['category'];
                    ?>
                </td>
                <td><?= number_format($product['sold']) ?></td>
                <td><?= $product['status'] ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editModal-<?= $product['id'] ?>">
                        Sửa
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteModal-<?= $product['id'] ?>">
                        Xóa
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- Phân trang -->
<?php if ($totalPages > 1): ?>
    <nav aria-label="Page navigation" class="mt-4">
        <ul class="pagination justify-content-center">
            <?php if ($currentPage > 1): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $currentPage - 1 ?>&search=<?= urlencode($search) ?>">
                        Trước
                    </a>
                </li>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?page=<?= $i ?>&search=<?= urlencode($search) ?>">
                        <?= $i ?>
                    </a>
                </li>
            <?php endfor; ?>

            <?php if ($currentPage < $totalPages): ?>
                <li class="page-item">
                    <a class="page-link" href="?page=<?= $currentPage + 1 ?>&search=<?= urlencode($search) ?>">
                        Sau
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </nav>
<?php endif; ?>

<?php
require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/edit.php';
require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/delete.php';
?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const formContainer = document.getElementById('addProductForm');
    const toggleBtn = document.getElementById('toggleFormBtn');
    const createProductForm = document.getElementById('createProductForm');
    
    // Xử lý ẩn/hiện form
    toggleBtn.addEventListener('click', function() {
        if (formContainer.style.display === 'none') {
            formContainer.style.display = 'block';
            toggleBtn.textContent = 'Ẩn form';
        } else {
            formContainer.style.display = 'none';
            toggleBtn.textContent = 'Thêm Sản phẩm';
        }
    });

    // Xử lý form submit
    createProductForm.addEventListener('submit', async function(e) {
        e.preventDefault();

        const submitButton = this.querySelector('button[type="submit"]');
        submitButton.disabled = true;
        submitButton.innerHTML = 'Đang xử lý...';

        try {
            const formData = new FormData(this);
            const numberOfProducts = parseInt(formData.get('numberOfProducts')) || 1;

            if (numberOfProducts < 1 || numberOfProducts > 20) {
                throw new Error('Số lượng sản phẩm phải từ 1 đến 20');
            }

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
                window.location.reload();
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

    // Hàm chuyển tab
    window.switchToVariants = function() {
        const variantsTab = document.querySelector('a[href="#variants"]');
        new bootstrap.Tab(variantsTab).show();
    }

    window.switchToBasicInfo = function() {
        const basicInfoTab = document.querySelector('a[href="#basicInfo"]');
        new bootstrap.Tab(basicInfoTab).show();
    }
});
</script>