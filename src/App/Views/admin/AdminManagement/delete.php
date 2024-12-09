<?php foreach ($admins as $admin): ?>
    <div class="modal fade" id="deleteModal<?= $admin['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận xóa Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/ecommerce-php/admin/admin-management/delete/<?= $admin['id'] ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $admin['id'] ?>">
                        <p>Bạn có chắc chắn muốn xóa admin này?</p>
                        <p><strong>Username:</strong> <?= $admin['username'] ?></p>
                        <p><strong>Email:</strong> <?= $admin['email'] ?></p>

                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xác nhận xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>