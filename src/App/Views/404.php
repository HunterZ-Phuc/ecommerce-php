<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Không tìm thấy trang</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Thêm Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background-color: #f8f9fa;
            height: 100vh;
            overflow: hidden;
            /* Ngăn scroll */
        }

        .error-template {
            padding: 40px 15px;
            text-align: center;
        }

        .error-code {
            font-size: 160px;
            font-weight: bold;
            color: #000000;
        }

        .error-message {
            font-size: 24px;
            margin: 20px 0;
            color: #343a40;
        }

        .error-details {
            color: #6c757d;
        }

        .btn-primary {
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-template d-flex flex-column justify-content-center align-items-center min-vh-100">
                    <div class="error-code">404</div>
                    <h2 class="error-message">Không tìm thấy trang!</h2>
                    <div class="error-details mb-4">
                        Xin lỗi, trang bạn yêu cầu không tồn tại hoặc đã bị di chuyển.
                    </div>
                    <div class="error-actions">
                        <a href="/ecommerce-php/" class="btn btn-primary btn-lg">
                            <i class="bi bi-house-door-fill me-2"></i>
                            Quay về trang chủ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap Bundle với Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>