<head>
    <style>
        .contact-info {
            margin-bottom: 30px;
        }

        .contact-form {
            margin-top: 30px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Liên Hệ Chúng Tôi</h1>

        <div class="contact-info">
            <h2>Thông Tin Liên Hệ</h2>
            <p><strong>Địa chỉ:</strong> 19 Lê Duẩn, phường Quyết Tâm, thành phố Sơn La.</p>
            <p><strong>Điện thoại:</strong> +84 0378 627 156</p>
            <p><strong>Email:</strong> contact@nongsantaybac.com</p>
        </div>

        <div class="contact-form">
            <h2>Gửi Tin Nhắn</h2>
            <form>
                <div class="mb-3">
                    <label for="name" class="form-label">Họ và Tên</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="message" class="form-label">Tin Nhắn</label>
                    <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                </div>
                <button class="btn btn-success">Gửi</button>
            </form>
        </div>

        <div class="map mt-5">
            <h2>Bản Đồ</h2>
            <iframe
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3717.0438071760104!2d103.9391361753087!3d21.309285280411192!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x31328b2de8124fc1%3A0x854e8fc5957d1f6c!2zVHLGsOG7nW5nIMSQ4bqhaSBo4buNYyBUw6J5IELhuq9j!5e0!3m2!1svi!2s!4v1734753253953!5m2!1svi!2s"
                width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"
                referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
    </div>
</body>