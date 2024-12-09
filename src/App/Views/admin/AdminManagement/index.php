<!-- Thông báo -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= $_SESSION['success']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= $_SESSION['error']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        <?php unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1>Quản lý Admin</h1>
    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createModal">
        Thêm Admin
    </button>
</div>

<table class="table">
    <thead>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Email</th>
            <th>Ngày tạo</th>
            <th>Thao tác</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($admins as $admin): ?>
            <tr>
                <td><?= $admin['id'] ?></td>
                <td><?= $admin['username'] ?></td>
                <td><?= $admin['email'] ?></td>
                <td><?= $admin['createdAt'] ?></td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal"
                        data-bs-target="#editModal<?= $admin['id'] ?>">
                        Sửa
                    </button>
                    <button type="button" class="btn btn-sm btn-danger" data-bs-toggle="modal"
                        data-bs-target="#deleteModal<?= $admin['id'] ?>">
                        Xóa
                    </button>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<?php
require_once ROOT_PATH . '/src/App/Views/admin/AdminManagement/create.php';
require_once ROOT_PATH . '/src/App/Views/admin/AdminManagement/edit.php';
require_once ROOT_PATH . '/src/App/Views/admin/AdminManagement/delete.php';
?>