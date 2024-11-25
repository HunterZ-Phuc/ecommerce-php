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
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        Thêm Sản phẩm
    </button>
</div>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Hình Ảnh</th>
            <th>Tên sản phẩm</th>
            <th>Danh mục</th>
            <th>Giá</th>
            <th>Trạng thái</th>
            <th>Hành động</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td>
                    <?php if (!empty($product['image_url'])): ?>
                        <img src="<?= $product['image_url'] ?>" alt="<?= $product['productName'] ?>"
                            class="img-thumbnail" style="width: 50px; height: 50px;">
                    <?php else: ?>
                        <img src="/ecommerce-php/public/assets/images/no-image.png" alt="No Image" 
                            class="img-thumbnail" style="width: 50px; height: 50px;">
                    <?php endif; ?>
                </td>
                <td><?= $product['productName'] ?></td>
                <td><?= $product['category'] ?></td>
                <td><?= number_format($product['price']) ?> VNĐ</td>
                <td><?= $product['status'] ?></td>
                <td>
                    <button type="button" 
                            class="btn btn-sm btn-warning" 
                            data-bs-toggle="modal" 
                            data-bs-target="#editModal<?= $product['id'] ?>">
                        Sửa
                    </button>
                    <button type="button" 
                            class="btn btn-sm btn-danger" 
                            data-bs-toggle="modal" 
                            data-bs-target="#deleteModal<?= $product['id'] ?>">
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