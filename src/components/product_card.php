<?php
function renderProductCard($pro_id, $pro_title, $pro_price, $pro_img1) {
    return "
    <div class='col-lg-4 col-md-6 col-sm-12 mb-4'>
        <div class='card h-100 product-card'>
            <img src='admin_area/product_images/$pro_img1' class='card-img-top product-img' alt='$pro_title'>
            <div class='card-body d-flex flex-column'>
                <h5 class='card-title'>$pro_title</h5>
                <p class='card-text text-primary fw-bold'>৳ $pro_price</p>
                <div class='mt-auto'>
                    <a href='details.php?pro_id=$pro_id' class='btn btn-outline-secondary w-100 mb-2'>
                        <i class='bi bi-eye'></i> Chi tiết
                    </a>
                    <a href='details.php?pro_id=$pro_id' class='btn btn-primary w-100'>
                        <i class='bi bi-cart-plus'></i> Thêm vào giỏ
                    </a>
                </div>
            </div>
        </div>
    </div>";
}
?>