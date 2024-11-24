<?php foreach ($employees as $employee): ?>
    <div class="modal fade" id="deleteModal<?= $employee['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Xác nhận Xóa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/php-mvc/admin/employee-management/delete/<?= $employee['id'] ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $employee['id'] ?>">
                        <p>Bạn có chắc muốn xóa nhân viên này?</p>
                        <p><strong>Họ tên:</strong> <?= $employee['fullName'] ?></p>
                        <p><strong>Email:</strong> <?= $employee['email'] ?></p>
                        
                        <div class="modal-footer px-0 pb-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                            <button type="submit" class="btn btn-danger">Xóa</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
