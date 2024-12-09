<?php foreach ($employees as $employee): ?>
    <div class="modal fade" id="editModal<?= $employee['id'] ?>" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Sửa Thông tin Nhân viên</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form action="/ecommerce-php/admin/employee-management/edit/<?= $employee['id'] ?>" method="POST">
                        <input type="hidden" name="id" value="<?= $employee['id'] ?>">

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?= $employee['email'] ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Họ và tên</label>
                            <input type="text" name="fullName" class="form-control" value="<?= $employee['fullName'] ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Ngày sinh</label>
                            <input type="date" name="dateOfBirth" class="form-control"
                                value="<?= $employee['dateOfBirth'] ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giới tính</label>
                            <select name="sex" class="form-select">
                                <option value="Male" <?= $employee['sex'] == 'Male' ? 'selected' : '' ?>>Nam</option>
                                <option value="Female" <?= $employee['sex'] == 'Female' ? 'selected' : '' ?>>Nữ</option>
                                <option value="Other" <?= $employee['sex'] == 'Other' ? 'selected' : '' ?>>Khác</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?= $employee['phone'] ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Địa chỉ</label>
                            <input type="text" name="address" class="form-control" value="<?= $employee['address'] ?>"
                                required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Lương</label>
                            <input type="number" name="salary" class="form-control" value="<?= $employee['salary'] ?>"
                                required>
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