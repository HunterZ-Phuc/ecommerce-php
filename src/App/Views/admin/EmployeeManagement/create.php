<div class="modal fade" id="createModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Thêm Nhân viên Mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form action="/ecommerce-php/admin/employee-management/create" method="POST"> <!-- Định dạng URL và phương thức POST -->
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" name="username" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" name="fullName" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Ngày sinh</label>
                        <input type="date" name="dateOfBirth" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giới tính</label>
                        <select name="sex" class="form-control" required>
                            <option value="Male">Nam</option>
                            <option value="Female">Nữ</option>
                            <option value="Other">Khác</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" name="phone" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <input type="text" name="address" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Lương</label>
                        <input type="number" name="salary" class="form-control" required>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                        <button type="submit" name="create" class="btn btn-primary">Thêm nhân viên mới</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
