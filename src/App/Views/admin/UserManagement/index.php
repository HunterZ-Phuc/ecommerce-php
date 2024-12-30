<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Quản lý người dùng</h2>
        <a href="/ecommerce-php/admin/users/export" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Xuất Excel
        </a>
    </div>

    <!-- Bộ lọc -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control"
                        placeholder="Tìm kiếm theo username hoặc họ tên..."
                        value="<?= htmlspecialchars($_GET['search'] ?? '') ?>">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">Tìm kiếm</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bảng danh sách -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Username</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày tham gia</th>
                            <th>Số đơn hàng</th>
                            <th>Tổng chi tiêu</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['fullName']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= htmlspecialchars($user['phone']) ?></td>
                                <td><?= date('d/m/Y', strtotime($user['createdAt'])) ?></td>
                                <td><?= $user['totalOrders'] ?></td>
                                <td><?= number_format($user['totalSpent']) ?>đ</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Phân trang - Luôn hiển thị -->
            <nav aria-label="Page navigation" class="mt-4">
                <ul class="pagination justify-content-center">
                    <!-- Nút Previous -->
                    <li class="page-item <?= ($currentPage <= 1) ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=<?= $currentPage - 1 ?><?= !empty($search) ? '&search=' . htmlspecialchars($search) : '' ?>">
                            Trước
                        </a>
                    </li>

                    <?php
                    // Hiển thị tối đa 5 trang
                    $start = max(1, $currentPage - 2);
                    $end = min($start + 4, $totalPages);

                    if ($end - $start < 4) {
                        $start = max(1, $end - 4);
                    }

                    // Hiển thị các trang
                    for ($i = $start; $i <= $end; $i++):
                        ?>
                        <li class="page-item <?= ($i == $currentPage) ? 'active' : '' ?>">
                            <a class="page-link"
                                href="?page=<?= $i ?><?= !empty($search) ? '&search=' . htmlspecialchars($search) : '' ?>">
                                <?= $i ?>
                            </a>
                        </li>
                    <?php endfor; ?>

                    <!-- Nút Next -->
                    <li class="page-item <?= ($currentPage >= $totalPages) ? 'disabled' : '' ?>">
                        <a class="page-link"
                            href="?page=<?= $currentPage + 1 ?><?= !empty($search) ? '&search=' . htmlspecialchars($search) : '' ?>">
                            Sau
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
</div>