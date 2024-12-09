<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>500 - Lỗi Máy Chủ</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8eb 100%);
            height: 100vh;
            overflow: hidden;
        }

        .error-template {
            padding: 40px 15px;
            text-align: center;
        }

        .error-code {
            font-size: 180px;
            font-weight: bold;
            color: #dc3545;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.1);
            animation: pulse 2s infinite;
        }


        .error-icon {
            font-size: 80px;
            color: #dc3545;
            margin: 20px 0;
        }

        .error-message {
            font-size: 28px;
            color: #343a40;
            margin: 20px 0;
            font-weight: 600;
        }

        .error-details {
            color: #6c757d;
            max-width: 600px;
            margin: 0 auto;
            line-height: 1.6;
        }

        .error-actions {
            margin-top: 30px;
        }

        .btn {
            transition: all 0.3s ease;
            margin: 10px;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <div class="error-template d-flex flex-column justify-content-center align-items-center min-vh-100">
                    <div class="error-code">500</div>
                    <div class="error-icon">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h1 class="error-message">Lỗi Máy Chủ Nội Bộ</h1>
                    <div class="error-details mb-4">
                        Xin lỗi, đã xảy ra lỗi trong quá trình xử lý yêu cầu của bạn.
                        Chúng tôi đã ghi nhận sự cố này và đang khắc phục.
                        Vui lòng thử lại sau hoặc liên hệ với đội ngũ hỗ trợ.
                    </div>

                    <div class="error-actions">
                        <div class="d-flex flex-column">
                            <button onclick="window.location.reload()" class="btn btn-danger btn-lg mb-2">
                                <i class="bi bi-arrow-clockwise me-2"></i>
                                Tải lại trang
                            </button>
                            <a href="/" class="btn btn-outline-secondary btn-lg mb-2">
                                <i class="bi bi-house-fill me-2"></i>
                                Về trang chủ
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>