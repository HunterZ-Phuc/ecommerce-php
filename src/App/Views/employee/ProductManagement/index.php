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
                             alt="<?= htmlspecialchars($product['productName']) ?>"
                             class="img-thumbnail" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                    <?php else: ?>
                        <img src="/ecommerce-php/public/assets/images/no-image.png" 
                             alt="No Image" 
                             class="img-thumbnail" 
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
                    <button type="button" 
                            class="btn btn-sm btn-warning" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal-<?= $product['id'] ?>">
                        Sửa
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal-<?= $product['id'] ?>">
                        Xóa
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

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

