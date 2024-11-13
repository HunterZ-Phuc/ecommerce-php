<footer class="bg-success text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- Cột 1: Menu chính -->
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="fw-bold mb-3">Trang Chính</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <a href="../cart.php" class="text-white text-decoration-none">Giỏ Hàng</a>
                    </li>
                    <li class="mb-2">
                        <a href="../shop.php" class="text-white text-decoration-none">Cửa Hàng</a>
                    </li>
                    <li class="mb-2">
                        <a href="../contact.php" class="text-white text-decoration-none">Liên Hệ</a>
                    </li>
                    <li class="mb-2">
                        <a href="my_account.php" class="text-white text-decoration-none">Tài Khoản</a>
                    </li>
                </ul>
            </div>

            <!-- Cột 2: Danh mục sản phẩm -->
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="fw-bold mb-3">Danh Mục Sản Phẩm</h5>
                <ul class="list-unstyled">
                    <?php
                    $get_p_cats = "select * from product_categories";
                    $run_p_cats = mysqli_query($con, $get_p_cats);
                    while ($row_p_cat = mysqli_fetch_array($run_p_cats)) {
                        $p_cat_id = $row_p_cat['p_cat_id'];
                        $p_cat_title = $row_p_cat['p_cat_title'];
                        echo "<li class='mb-2'>
                            <a href='shop.php?p_cat=$p_cat_id' class='text-white text-decoration-none'>$p_cat_title</a>
                        </li>";
                    }
                    ?>
                </ul>
            </div>

            <!-- Cột 3: Thông tin liên hệ -->
            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="fw-bold mb-3">Thông Tin Liên Hệ</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><strong>Nông Sản Sạch Việt Nam</strong></li>
                    <li class="mb-2">Số 123 Đường ABC</li>
                    <li class="mb-2">Quận Hai Bà Trưng, Hà Nội</li>
                    <li class="mb-2">Email: contact@nongsansach.vn</li>
                    <li class="mb-2">Hotline: 1900 1234</li>
                </ul>
            </div>

            <div class="col-md-3 col-sm-6 mb-4">
                <h5 class="fw-bold mb-3">Kết Nối Với Chúng Tôi</h5>
                <div class="social-links">
                    <a href="#" class="me-3" style="color: #1877f2;"><i class="fab fa-facebook fa-2x"></i></a>
                    <a href="#" class="me-3" style="color: #ff0000;"><i class="fab fa-youtube fa-2x"></i></a>
                    <a href="#" class="me-3" style="color: #e4405f;"><i class="fab fa-instagram fa-2x"></i></a>
                    <a href="#" style="color: #000000;"><i class="fab fa-tiktok fa-2x"></i></a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Copyright -->
<div class="bg-dark text-white py-3">
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-0">&copy; 2024 Nông Sản Sạch Việt Nam. Tất cả quyền được bảo lưu.</p>
            </div>
            <div class="col-md-6 text-end">
                <p class="mb-0">www.nongsansach.vn</p>
            </div>
        </div>
    </div>
</div>