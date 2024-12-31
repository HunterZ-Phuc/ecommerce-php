<head>
    <style>
        .team-member {
            margin-bottom: 30px;
        }

        .team-member img {
            border-radius: 50%;
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        .about-section {
            padding: 60px 0;
            background-color: #f8f9fa;
        }

        .mission-section {
            padding: 40px 0;
            background-color: #ffffff;
        }

        .stats-section {
            padding: 50px 0;
            background-color: #28a745;
            color: white;
        }

        .team-section {
            padding: 60px 0;
        }

        .value-card {
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            height: 100%;
            transition: transform 0.3s;
        }

        .value-card:hover {
            transform: translateY(-5px);
        }
    </style>
</head>

<body>
    <div class="container">
        <h1 class="mt-5">Giới Thiệu Về Chúng Tôi</h1>

        <!-- Banner Section -->
        <div class="jumbotron text-center bg-success text-white mb-0">
            <div class="container">
                <h1 class="display-4">Nông Sản Tây Bắc</h1>
                <p class="lead">Kết nối tinh hoa nông sản Việt Nam đến mọi nhà</p>
            </div>
        </div>

        <!-- About Section -->
        <div class="about-section">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <h2>Về Chúng Tôi</h2>
                        <p>Nông Sản Tây Bắc là cửa hàng trực tuyến chuyên cung cấp các sản phẩm nông sản chất lượng cao từ các vùng
                            miền tại Việt Nam. Chúng tôi cam kết mang đến cho khách hàng những sản phẩm tươi ngon và an toàn nhất.
                        </p>
                        <p>Sứ mệnh của chúng tôi là kết nối người tiêu dùng với những sản phẩm nông sản tốt nhất, đồng thời hỗ trợ
                            nông dân địa phương phát triển kinh tế bền vững.</p>
                    </div>
                    <div class="col-md-6">
                        <img src="/ecommerce-php/public/assets/images/logo.png" class="img-fluid rounded" alt="Nông sản Tây Bắc">
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Section -->
        <div class="stats-section">
            <div class="container">
                <div class="row text-center">
                    <div class="col-md-4">
                        <h2 class="display-4">5000+</h2>
                        <p>Khách hàng tin tưởng</p>
                    </div>
                    <div class="col-md-4">
                        <h2 class="display-4">100+</h2>
                        <p>Nhà cung cấp đối tác</p>
                    </div>
                    <div class="col-md-4">
                        <h2 class="display-4">1000+</h2>
                        <p>Sản phẩm chất lượng</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Values Section -->
        <div class="mission-section">
            <div class="container">
                <h2 class="text-center mb-5">Giá Trị Cốt Lõi</h2>
                <div class="row">
                    <div class="col-md-4 mb-4">
                        <div class="value-card">
                            <i class="fas fa-leaf fa-3x text-success mb-3"></i>
                            <h3>Chất Lượng</h3>
                            <p>Cam kết cung cấp sản phẩm tươi ngon, an toàn vệ sinh thực phẩm</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="value-card">
                            <i class="fas fa-handshake fa-3x text-success mb-3"></i>
                            <h3>Uy Tín</h3>
                            <p>Xây dựng niềm tin với khách hàng qua từng giao dịch</p>
                        </div>
                    </div>
                    <div class="col-md-4 mb-4">
                        <div class="value-card">
                            <i class="fas fa-heart fa-3x text-success mb-3"></i>
                            <h3>Tận Tâm</h3>
                            <p>Luôn lắng nghe và phục vụ khách hàng với trái tim nhiệt thành</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="team-section">
            <h2>Đội Ngũ Của Chúng Tôi</h2>
            <div class="row">
                <div class="col-md-4 team-member text-center">
                    <img src="/ecommerce-php/public/assets/members/member-phuc.png" alt="Thành viên 1"
                        class="img-fluid rounded-circle">
                    <h3>Phạm Văn Phúc</h3>
                    <p>CEO</p>
                </div>
                <div class="col-md-4 team-member text-center">
                    <img src="https://via.placeholder.com/150" alt="Thành viên 2" class="img-fluid rounded-circle">
                    <h3>Quàng Trọng A</h3>
                    <p>Chuyên viên công nghệ</p>
                </div>
                <div class="col-md-4 team-member text-center">
                    <img src="https://via.placeholder.com/150" alt="Thành viên 3" class="img-fluid rounded-circle">
                    <h3>Tòng Văn Hải</h3>
                    <p>Chuyên viên marketing</p>
                </div>
            </div>
        </div>
    </div>
</body>