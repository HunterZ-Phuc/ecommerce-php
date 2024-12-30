<div class="p-4 bg-white rounded shadow">
    <h2 class="fs-5 fw-semibold">Địa chỉ của tôi</h2>
    <h3 class="mt-4 fs-6 fw-medium">Địa chỉ</h3>

    <?php foreach ($addresses as $address): ?>
        <div class="mt-2 p-4 border rounded">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="fw-bold"><?= htmlspecialchars($address['fullName']) ?></h4>
                    <p>(+84) <?= htmlspecialchars($address['phoneNumber']) ?></p>
                    <p><?= htmlspecialchars($address['address']) ?></p>
                </div>
                <?php if ($address['isDefault']): ?>
                    <span class="text-danger fw-semibold">Mặc định</span>
                <?php endif; ?>
            </div>
            <div class="mt-2 d-flex justify-content-end">
                <button class="btn btn-link text-primary p-0" onclick="editAddress(<?= $address['id'] ?>)">
                    Cập nhật
                </button>
                <?php if (!$address['isDefault']): ?>
                    <button class="btn btn-link text-primary p-0 ms-2" onclick="deleteAddress(<?= $address['id'] ?>)">
                        Xóa
                    </button>
                    <button class="btn btn-link text-primary p-0 ms-2" onclick="setDefaultAddress(<?= $address['id'] ?>)">
                        Thiết lập mặc định
                    </button>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>

    <button class="mt-4 btn btn-danger text-white px-4 py-2" data-bs-toggle="modal" data-bs-target="#addressModal">
        + Thêm địa chỉ mới
    </button>
</div>

<!-- Modal for Add/Edit Address -->
<div class="modal fade" id="addressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Thêm địa chỉ mới</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="addressForm">
                    <input type="hidden" id="addressId" name="id">
                    <div class="mb-3">
                        <label class="form-label">Họ và tên</label>
                        <input type="text" class="form-control" name="fullName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Số điện thoại</label>
                        <input type="tel" class="form-control" name="phoneNumber" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Địa chỉ</label>
                        <textarea class="form-control" name="address" rows="3" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
                <button type="button" class="btn btn-primary" onclick="saveAddress()">Lưu</button>
            </div>
        </div>
    </div>
</div>

<script>
    const addressModal = new bootstrap.Modal(document.getElementById('addressModal'));

    function editAddress(id) {
        fetch(`/ecommerce-php/user/address/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.getElementById('modalTitle').textContent = 'Cập nhật địa chỉ';
                    const form = document.getElementById('addressForm');
                    form.id.value = data.address.id;
                    form.fullName.value = data.address.fullName;
                    form.phoneNumber.value = data.address.phoneNumber;
                    form.address.value = data.address.address;
                    addressModal.show();
                }
            });
    }

    function saveAddress() {
        const form = document.getElementById('addressForm');
        const formData = new FormData(form);
        const id = form.id.value;
        const url = id ?
            `/ecommerce-php/user/address/update/${id}` :
            '/ecommerce-php/user/address/create';

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            });
    }

    function deleteAddress(id) {
        if (confirm('Bạn có chắc muốn xóa địa chỉ này?')) {
            fetch(`/ecommerce-php/user/address/delete/${id}`, {
                method: 'POST'
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert(data.error || 'Có lỗi xảy ra');
                    }
                });
        }
    }

    function setDefaultAddress(id) {
        fetch(`/ecommerce-php/user/address/set-default/${id}`, {
            method: 'POST'
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.error || 'Có lỗi xảy ra');
                }
            });
    }
</script>