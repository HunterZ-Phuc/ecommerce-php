<div class="modal fade" id="employeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm nhân viên mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="employeeForm">
                    <input type="hidden" id="employee_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phòng ban</label>
                        <select class="form-select" id="department" name="department" required>
                            <option value="">Chọn phòng ban</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Sales">Sales</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chức vụ</label>
                        <input type="text" class="form-control" id="position" name="position">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="saveEmployee()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="editEmployeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Chỉnh sửa thông tin nhân viên</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editEmployeeForm">
                    <input type="hidden" id="edit_employee_id" name="id">
                    <div class="mb-3">
                        <label class="form-label">Họ tên</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" id="edit_phone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Phòng ban</label>
                        <select class="form-select" id="edit_department" name="department" required>
                            <option value="">Chọn phòng ban</option>
                            <option value="IT">IT</option>
                            <option value="HR">HR</option>
                            <option value="Marketing">Marketing</option>
                            <option value="Sales">Sales</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Chức vụ</label>
                        <input type="text" class="form-control" id="edit_position" name="position">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="updateEmployee()">Cập nhật</button>
            </div>
        </div>
    </div>
</div>