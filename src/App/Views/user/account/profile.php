<div class="profile-content">
    <h2 class="fs-4 fw-bold mb-3">Hồ Sơ Của Tôi</h2>
    <p class="text-muted mb-4">Quản lý thông tin hồ sơ để bảo mật tài khoản</p>

    <div class="row">
        <div class="col-md-8">
            <form id="profileForm" action="/ecommerce-php/user/profile/update" method="POST">
                <div class="mb-3">
                    <label class="form-label">Tên đăng nhập</label>
                    <input type="text" 
                           name="username" 
                           value="<?= isset($username) ? htmlspecialchars($username) : '' ?>" 
                           readonly />
                </div>

                <div class="mb-3">
                    <label class="form-label">Họ và tên</label>
                    <input type="text" 
                           name="fullName" 
                           value="<?= isset($fullName) ? htmlspecialchars($fullName) : '' ?>"
                           required />
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <div class="input-group">
                        <input type="email" 
                               name="email" 
                               value="<?= isset($email) ? htmlspecialchars($email) : '' ?>" 
                />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <div class="input-group">
                        <input type="tel" 
                               name="phone" 
                               value="<?= isset($phone) ? htmlspecialchars($phone) : '' ?>" />
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Giới tính</label>
                    <div class="d-flex gap-4">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="Male" 
                                <?= (isset($sex) && $sex == 'Male') ? 'checked' : '' ?> />
                            <label class="form-check-label">Nam</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="Female"
                                <?= (isset($sex) && $sex == 'Female') ? 'checked' : '' ?> />
                            <label class="form-check-label">Nữ</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sex" value="Other"
                                <?= (isset($sex) && $sex == 'Other') ? 'checked' : '' ?> />
                            <label class="form-check-label">Khác</label>
                        </div>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label">Ngày sinh</label>
                    <input type="date" 
                           name="dateOfBirth" 
                           value="<?= isset($dateOfBirth) ? htmlspecialchars($dateOfBirth) : '' ?>" 
                           required />
                </div>

                <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
            </form>
        </div>

        <div class="col-md-4">
            <div class="text-center">
                <div class="mb-3">
                    <img src="<?= '/ecommerce-php/public' . ($_SESSION['user']['avatar'] ?? '/assets/images/default-avatar.png') ?>" 
                         alt="Avatar" 
                         class="rounded-circle img-thumbnail user-avatar"
                         style="width: 200px; height: 200px; object-fit: cover;" />
                </div>
                
                <form id="avatarForm" action="/ecommerce-php/user/profile/update-avatar" method="POST" enctype="multipart/form-data">
                    <input type="file" 
                           name="avatar" 
                           id="avatarInput" 
                           class="form-control mb-2" 
                           accept="image/*" 
                           hidden />
                    <button type="button" 
                            class="btn btn-outline-secondary mb-2" 
                            onclick="document.getElementById('avatarInput').click()">
                        Chọn ảnh
                    </button>
                    <div id="avatarPreview" class="text-center">
                        <!-- Preview ảnh sẽ được thêm vào đây bằng JavaScript -->
                    </div>
                    <div class="text-muted small">
                        <p class="mb-0">Định dạng: JPEG, PNG</p>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('avatarInput').addEventListener('change', function() {
    if (this.files && this.files[0]) {
        // Kiểm tra định dạng file
        if (!['image/jpeg', 'image/png'].includes(this.files[0].type)) {
            alert('Chỉ chấp nhận file JPG hoặc PNG');
            this.value = '';
            return;
        }

        const form = document.getElementById('avatarForm');
        const formData = new FormData(form);
        
        fetch(form.action, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Cập nhật tất cả các ảnh avatar trên trang
                const newAvatarUrl = '/ecommerce-php/public' + data.avatar;
                
                // Cập nhật ảnh trong profile
                document.querySelector('.rounded-circle').src = newAvatarUrl;
                
                // Cập nhật ảnh trong header
                document.querySelector('#userDropdown img').src = newAvatarUrl;
                
                // Cập nhật preview ảnh trong form upload
                const previewImg = document.createElement('img');
                previewImg.src = newAvatarUrl;
                previewImg.style.width = '100px';
                previewImg.style.height = '100px';
                previewImg.style.objectFit = 'cover';
                previewImg.style.borderRadius = '50%';
                previewImg.style.marginTop = '10px';
                
                const previewContainer = document.querySelector('#avatarPreview');
                if (previewContainer) {
                    previewContainer.innerHTML = '';
                    previewContainer.appendChild(previewImg);
                } else {
                    const newPreviewContainer = document.createElement('div');
                    newPreviewContainer.id = 'avatarPreview';
                    document.getElementById('avatarForm').appendChild(newPreviewContainer);
                    newPreviewContainer.appendChild(previewImg);
                }
                
                alert('Cập nhật avatar thành công!');
            } else {
                alert(data.error || 'Có lỗi xảy ra khi cập nhật ảnh đại diện');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi xảy ra khi cập nhật ảnh đại diện');
        })
        .finally(() => {
            this.value = ''; // Reset input file
        });
    }
});

document.getElementById('profileForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    try {
        const response = await fetch(this.action, {
            method: 'POST',
            body: new FormData(this)
        });
        
        const data = await response.json();
        
        if (data.success) {
            alert('Cập nhật thông tin thành công!');
            window.location.reload();
        } else {
            alert(data.error || 'Có lỗi xảy ra khi cập nhật thông tin');
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi cập nhật thông tin');
    }
});
</script>
