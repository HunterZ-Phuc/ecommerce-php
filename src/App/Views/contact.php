<head>
    <style>
        .contact-header {
            background: linear-gradient(rgba(40, 167, 69, 0.8), rgba(40, 167, 69, 0.8)), 
                        url('/ecommerce-php/public/assets/images/contact-banner.jpg');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            color: white;
            text-align: center;
            margin-bottom: 50px;
        }

        .contact-info {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 10px;
            margin-bottom: 30px;
        }

        .contact-info i {
            color: #28a745;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .contact-form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }

        .map {
            margin-top: 50px;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
        }

        .contact-method {
            transition: transform 0.3s;
            padding: 20px;
            text-align: center;
        }

        .contact-method:hover {
            transform: translateY(-5px);
        }

        .btn-success {
            padding: 10px 30px;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <!-- Header Banner -->
    <div class="contact-header">
        <div class="container">
            <h1 class="display-4">Liên Hệ Với Chúng Tôi</h1>
            <p class="lead">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
        </div>
    </div>

    <div class="container">
        <!-- Contact Methods -->
        <div class="row mb-5">
            <div class="col-md-4">
                <div class="contact-method">
                    <i class="fas fa-phone-alt fa-2x text-success mb-3"></i>
                    <h4>Điện Thoại</h4>
                    <p>+84 0378 627 156</p>
                    <p class="text-muted">Thứ 2 - Chủ nhật: 8:00 - 20:00</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-method">
                    <i class="fas fa-envelope fa-2x text-success mb-3"></i>
                    <h4>Email</h4>
                    <p>contact@nongsantaybac.com</p>
                    <p class="text-muted">Phản hồi trong vòng 24 giờ</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="contact-method">
                    <i class="fas fa-map-marker-alt fa-2x text-success mb-3"></i>
                    <h4>Địa Chỉ</h4>
                    <p>19 Lê Duẩn, phường Quyết Tâm</p>
                    <p class="text-muted">Thành phố Sơn La</p>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Contact Form -->
            <div class="col-md-7">
                <div class="contact-form">
                    <h2 class="mb-4">Gửi Tin Nhắn</h2>
                    <form id="contactForm">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="name" class="form-label">Họ và Tên</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Tiêu Đề</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Tin Nhắn</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-paper-plane me-2"></i>Gửi Tin Nhắn
                        </button>
                    </form>
                </div>
            </div>

            <!-- Contact Info -->
            <div class="col-md-5">
                <div class="contact-info">
                    <h2 class="mb-4">Thông Tin Liên Hệ</h2>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-building me-3 mt-1"></i>
                        <div>
                            <h5>Văn Phòng Chính</h5>
                            <p>Đường ABC, tổ 1, TP.Sơn La</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start mb-3">
                        <i class="fas fa-clock me-3 mt-1"></i>
                        <div>
                            <h5>Giờ Làm Việc</h5>
                            <p>Thứ 2 - Chủ nhật: 8:00 - 20:00<br>
                            Nghỉ các ngày lễ lớn</p>
                        </div>
                    </div>
                    <div class="d-flex align-items-start">
                        <i class="fas fa-comments me-3 mt-1"></i>
                        <div>
                            <h5>Hỗ Trợ Khách Hàng</h5>
                            <p>Hotline: +84 0378 627 156<br>
                            Email: support@nongsantaybac.com</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Section -->
        <div class="map">
            <h2 class="mb-4">Bản Đồ</h2>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3717.0438071760104!2d103.9391361753087!3d21.309285280411192!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31328b2de8124fc1%3A0x854e8fc5957d1f6c!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUw6J5IELhuq9j!5e0!3m2!1svi!2s!4v1734753253953!5m2!1svi!2s"
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</body>