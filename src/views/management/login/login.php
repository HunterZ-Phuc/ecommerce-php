<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GYM Management - Đăng nhập</title>
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;700&display=swap" rel="stylesheet">
    
    <!-- Custom CSS -->
    <style>
        <?php include 'login.css'; ?>
    </style>
</head>
<body>

<div class="container" id="container">
    <div class="form-container sign-up-container">
        <form method="POST" action="register">
            <h1>Tạo tài khoản</h1>
            <input type="text" name="username" placeholder="Tên đăng nhập" required />
            <input type="email" name="email" placeholder="Email" required />
            <input type="password" name="password" placeholder="Mật khẩu" required />
            <input type="password" name="confirm_password" placeholder="Xác nhận mật khẩu" required />
            <button type="submit">Đăng ký</button>
        </form>
    </div>
    <div class="form-container sign-in-container">
        <form method="POST" action="login">
            <h1>Đăng nhập</h1>
            <?php if(isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            <input type="text" name="username" placeholder="Tên đăng nhập" required />
            <input type="password" name="password" placeholder="Mật khẩu" required />
            <div class="checkbox-container">
                <label>
                    <input type="checkbox" name="remember" />
                    Ghi nhớ đăng nhập
                </label>
            </div>
            <a href="#">Quên mật khẩu?</a>
            <button type="submit">Đăng nhập</button>
        </form>
    </div>
    <div class="overlay-container">
        <div class="overlay">
            <div class="overlay-panel overlay-left">
                <h1>Chào mừng trở lại!</h1>
                <p>Đăng nhập để kết nối với chúng tôi</p>
                <button class="ghost" id="signIn">Đăng nhập</button>
            </div>
            <div class="overlay-panel overlay-right">
                <h1>Xin chào!</h1>
                <p>Đăng ký tài khoản để bắt đầu hành trình với chúng tôi</p>
                <button class="ghost" id="signUp">Đăng ký</button>
            </div>
        </div>
    </div>
</div>

<script>
    <?php include 'login.js'; ?>
</script>

</body>
</html>