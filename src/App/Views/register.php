<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="text-center mb-4">Đăng ký tài khoản</h3>

                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger">
                                <?= $error ?>
                            </div>
                        <?php endif; ?>

                        <form action="/ecommerce-php/register" method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Tên đăng nhập</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="username" 
                                       name="username" 
                                       value="<?= isset($old['username']) ? htmlspecialchars($old['username']) : '' ?>"
                                       required>
                            </div>

                            <div class="mb-3">
                                <label for="fullName" class="form-label">Họ và tên</label>
                                <input type="text" 
                                       class="form-control" 
                                       id="fullName" 
                                       name="fullName"
                                       value="<?= isset($old['fullName']) ? htmlspecialchars($old['fullName']) : '' ?>"
                                       required>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="email" class="form-label">Email</label>
                                    <input type="email" 
                                           class="form-control" 
                                           id="email" 
                                           name="email"
                                           value="<?= isset($old['email']) ? htmlspecialchars($old['email']) : '' ?>"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label for="phone" class="form-label">Số điện thoại</label>
                                    <input type="tel" 
                                           class="form-control" 
                                           id="phone" 
                                           name="phone"
                                           value="<?= isset($old['phone']) ? htmlspecialchars($old['phone']) : '' ?>"
                                           required>
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <label for="dateOfBirth" class="form-label">Ngày sinh</label>
                                    <input type="date" 
                                           class="form-control" 
                                           id="dateOfBirth" 
                                           name="dateOfBirth"
                                           value="<?= isset($old['dateOfBirth']) ? $old['dateOfBirth'] : '' ?>"
                                           required>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Giới tính</label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="sex" 
                                                   value="Male"
                                                   <?= (isset($old['sex']) && $old['sex'] === 'Male') ? 'checked' : '' ?>
                                                   required>
                                            <label class="form-check-label">Nam</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="sex" 
                                                   value="Female"
                                                   <?= (isset($old['sex']) && $old['sex'] === 'Female') ? 'checked' : '' ?>
                                                   required>
                                            <label class="form-check-label">Nữ</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="radio" 
                                                   name="sex" 
                                                   value="Other"
                                                   <?= (isset($old['sex']) && $old['sex'] === 'Other') ? 'checked' : '' ?>
                                                   required>
                                            <label class="form-check-label">Khác</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Mật khẩu</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="password" 
                                       name="password"
                                       required>
                            </div>

                            <div class="mb-4">
                                <label for="confirmPassword" class="form-label">Xác nhận mật khẩu</label>
                                <input type="password" 
                                       class="form-control" 
                                       id="confirmPassword" 
                                       name="confirmPassword"
                                       required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100 mb-3">
                                Đăng ký
                            </button>

                            <div class="text-center">
                                <a href="/ecommerce-php/login" class="text-decoration-none">
                                    Đã có tài khoản? Đăng nhập ngay
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 