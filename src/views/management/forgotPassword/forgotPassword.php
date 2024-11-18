<?php
session_start();
?>
<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quên Mật Khẩu</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,800" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="forgotPassword.css">
</head>

<body>
    <div class="container">
        <h1>Quên Mật Khẩu</h1>
        <p>Vui lòng nhập địa chỉ email của bạn. Chúng tôi sẽ gửi link đặt lại mật khẩu vào email của bạn.</p>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?php
                echo $_SESSION['error'];
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <?php
                echo $_SESSION['success'];
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>

        <form action="process_forgot_password.php" method="POST">
            <input type="email" name="email" placeholder="Nhập email của bạn" required />
            <button type="submit">Gửi Link Đặt Lại</button>
        </form>

        <a href="login.php" class="back-link">
            <i class="fas fa-arrow-left"></i> Quay lại trang đăng nhập
        </a>
    </div>
</body>

</html>l