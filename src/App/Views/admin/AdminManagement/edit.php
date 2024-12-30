<?php foreach ($admins as $admin): ?>
    <div class="modal fade" id="editModal<?= $admin['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa thông tin Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/ecommerce-php/admin/admin-management/edit/<?= $admin['id'] ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username" class="form-control" value="<?= $admin['username'] ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $admin['email'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password (để trống nếu không đổi)</label>
                            <input type="password" name="password" class="form-control">
                        </div>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-primary">Cập nhật</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>