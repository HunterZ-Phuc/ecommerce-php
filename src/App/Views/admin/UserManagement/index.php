<div class="container-fluid mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Quản lý người dùng</h1>
        <div>
            <a href="/admin/users/export" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
        </div>
    </div>

    <!-- Bộ lọc -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <input type="text" class="form-control" name="search" 
                           placeholder="Tìm theo tên, email, SĐT" 
                           value="<?= $_GET['search'] ?? '' ?>">
                </div>
                <div class="col-md-2">
                    <select class="form-control" name="status">
                        <option value="">-- Trạng thái --</option>
                        <option value="active" <?= isset($_GET['status']) && $_GET['status'] == 'active' ? 'selected' : '' ?>>
                            Hoạt động
                        </option>
                        <option value="inactive" <?= isset($_GET['status']) && $_GET['status'] == 'inactive' ? 'selected' : '' ?>>
                            Khóa
                        </option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Danh sách người dùng -->
    <div class="card shadow">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày tham gia</th>
                            <th>Số đơn hàng</th>
                            <th>Tổng chi tiêu</th>
                            <th>Trạng thái</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= $user['id'] ?></td>
                            <td><?= htmlspecialchars($user['fullName']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td><?= htmlspecialchars($user['phone']) ?></td>
                            <td><?= date('d/m/Y', strtotime($user['createdAt'])) ?></td>
                            <td><?= $user['totalOrders'] ?></td>
                            <td><?= number_format($user['totalSpent']) ?> đ</td>
                            <td>
                                <span class="badge badge-<?= $user['isActive'] ? 'success' : 'danger' ?>">
                                    <?= $user['isActive'] ? 'Hoạt động' : 'Đã khóa' ?>
                                </span>
                            </td>
                            <td>
                                <div class="btn-group">
                                    <a href="/admin/users/view/<?= $user['id'] ?>" 
                                       class="btn btn-sm btn-info" title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <button type="button" 
                                            class="btn btn-sm <?= $user['isActive'] ? 'btn-warning' : 'btn-success' ?>"
                                            onclick="toggleUserStatus(<?= $user['id'] ?>, <?= $user['isActive'] ?>)"
                                            title="<?= $user['isActive'] ? 'Khóa tài khoản' : 'Mở khóa' ?>">
                                        <i class="fas fa-<?= $user['isActive'] ? 'lock' : 'unlock' ?>"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang -->
            <?php if ($totalPages > 1): ?>
            <nav class="mt-4">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                    <li class="page-item <?= $currentPage == $i ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?><?= $queryString ?>"><?= $i ?></a>
                    </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function toggleUserStatus(userId, currentStatus) {
    if (!confirm('Bạn có chắc muốn ' + (currentStatus ? 'khóa' : 'mở khóa') + ' tài khoản này?')) {
        return;
    }

    fetch('/admin/users/toggle-status/' + userId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '<?= $_SESSION['csrf_token'] ?>'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message || 'Có lỗi xảy ra');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra');
    });
}
</script>
