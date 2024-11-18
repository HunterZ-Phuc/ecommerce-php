<?php

namespace App\Views\AdminManagement;

require_once 'C:/xampp/htdocs/nongsan/src/utils/db_connect.php';

class EmployeeManagement
{
    private $con;
    
    public function __construct() {
        global $con;
        $this->con = $con;
    }

    public function index()
    {
        ob_start();
?>
        <div class="container-fluid p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>Quản lý nhân viên</h2>
                <div>
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addEmployeeModal">
                        <i class="bi bi-plus-lg"></i> Thêm mới
                    </button>
                </div>
            </div>

            <!-- Bảng nhân viên -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Họ tên</th>
                                    <th>Email</th>
                                    <th>Số điện thoại</th>
                                    <th>Phòng ban</th>
                                    <th>Chức vụ</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM employees";
                                $result = mysqli_query($this->con, $query);
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                        <td>{$row['id']}</td>
                                        <td>{$row['name']}</td>
                                        <td>{$row['email']}</td>
                                        <td>{$row['phone']}</td>
                                        <td>{$row['department']}</td>
                                        <td>{$row['position']}</td>
                                        <td>
                                            <button class='btn btn-sm btn-primary me-1' onclick='editEmployee({$row['id']})'>
                                                <i class='bi bi-pencil'></i>
                                            </button>
                                            <button class='btn btn-sm btn-danger' onclick='confirmDelete({$row['id']})'>
                                                <i class='bi bi-trash'></i>
                                            </button>
                                        </td>
                                    </tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal thêm/sửa nhân viên -->
            <?php include 'employee_modal.php'; ?>
        </div>

        <!-- Thêm SweetAlert2 -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        
        <script>
            function editEmployee(id) {
                // Lấy thông tin nhân viên qua AJAX
                fetch(`get_employee.php?id=${id}`)
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('edit_employee_id').value = data.id;
                        document.getElementById('edit_name').value = data.name;
                        document.getElementById('edit_email').value = data.email;
                        document.getElementById('edit_phone').value = data.phone;
                        document.getElementById('edit_department').value = data.department;
                        document.getElementById('edit_position').value = data.position;
                        
                        // Hiển thị modal
                        new bootstrap.Modal(document.getElementById('editEmployeeModal')).show();
                    });
            }

            function updateEmployee() {
                const formData = new FormData(document.getElementById('editEmployeeForm'));
                
                fetch('update_employee.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Đã cập nhật thông tin nhân viên',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi!', data.message, 'error');
                    }
                });
            }

            function confirmDelete(id) {
                Swal.fire({
                    title: 'Xác nhận xóa?',
                    text: "Bạn không thể hoàn tác sau khi xóa!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Xóa',
                    cancelButtonText: 'Hủy'
                }).then((result) => {
                    if (result.isConfirmed) {
                        deleteEmployee(id);
                    }
                });
            }

            function deleteEmployee(id) {
                fetch('delete_employee.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success) {
                        Swal.fire({
                            title: 'Thành công!',
                            text: 'Đã xóa nhân viên',
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Lỗi!', data.message, 'error');
                    }
                });
            }
        </script>
<?php
        return ob_get_clean();
    }
}
