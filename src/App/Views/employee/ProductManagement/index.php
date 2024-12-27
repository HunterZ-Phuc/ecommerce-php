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
    <button type="button" class="btn btn-primary" id="addProductButton">
        Thêm Sản phẩm
    </button>
</div>

<!-- Thêm form tìm kiếm -->
<div class="row mb-4">
    <div class="col-md-8">
        <form action="" method="GET" class="d-flex gap-2">
            <div class="input-group">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Tìm kiếm sản phẩm..." 
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

<!-- Thêm phân trang -->
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
require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/create.php';
require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/edit.php';
require_once ROOT_PATH . '/src/App/Views/employee/ProductManagement/delete.php';
?>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const categoryModal = new bootstrap.Modal(document.getElementById('categoryModal'));
        const createModal = new bootstrap.Modal(document.getElementById('createModal'));

        // Xử lý sự kiện khi modal bị ẩn
        document.getElementById('createModal').addEventListener('hidden.bs.modal', function () {
            // Xóa backdrop và class modal-open khỏi body
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        document.getElementById('categoryModal').addEventListener('hidden.bs.modal', function () {
            // Xóa backdrop và class modal-open khỏi body
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            document.body.classList.remove('modal-open');
            document.body.style.overflow = '';
            document.body.style.paddingRight = '';
        });

        // Chỉ để lại xử lý hiển thị modal
        document.getElementById('addProductButton').addEventListener('click', function () {
            categoryModal.show();
        });

        document.getElementById('confirmCategory').addEventListener('click', function () {
            const selectedCategory = document.getElementById('productCategory').value;
            if (selectedCategory) {
                document.getElementById('selectedCategoryInput').value = selectedCategory;
                categoryModal.hide();
                setTimeout(() => {
                    createModal.show();
                }, 500);
            } else {
                alert('Vui lòng chọn danh mục sản phẩm.');
            }
        });
    });
</script>