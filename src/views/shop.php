<?php
include("C:/xampp/htdocs/ecommerce-php/src/utils/db_connect.php");
?>

<!DOCTYPE html>
<html>

<head>
    <title>Online Shopping</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" type="text/css" href="styles/style.css">
</head>


<div class="container py-5">
    <div class="row">
        <div class="col-lg-3">
            <?php include("C:/xampp/htdocs/ecommerce-php/src/layout/sidebar.php"); ?>
        </div>

        <div class="col-lg-9">
            <div class="row mb-4">
                <div class="col">
                    <h2 class="fw-bold">Sản phẩm của chúng tôi</h2>
                    <p class="text-muted">Khám phá các sản phẩm chất lượng cao của chúng tôi</p>
                </div>
            </div>

            <div class="row">
                <?php
                require_once 'C:/xampp/htdocs/ecommerce-php/src/components/product_card.php';

                $per_page = 3;
                $page = isset($_GET['page']) ? $_GET['page'] : 1;
                $start_from = ($page - 1) * $per_page;

                $category_filter = "";
                if(isset($_GET['category'])) {
                    $category = strtoupper($_GET['category']);
                    $category_filter = "WHERE category = '$category'";
                }

                $get_product = "SELECT * FROM products $category_filter ORDER BY 1 DESC LIMIT $start_from, $per_page";
                $run_pro = mysqli_query($con, $get_product);

                while ($row = mysqli_fetch_array($run_pro)) {
                    echo renderProductCard(
                        $row['product_id'],
                        $row['product_title'],
                        $row['product_price'],
                        $row['product_img1']
                    );
                }
                ?>
            </div>

            <!-- Phân trang -->
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php
                    $query = "SELECT * FROM products";
                    $result = mysqli_query($con, $query);
                    $total_record = mysqli_num_rows($result);
                    $total_pages = ceil($total_record / $per_page);

                    echo "<li class='page-item " . ($page == 1 ? 'disabled' : '') . "'><a class='page-link' href='shop.php?page=1'>Trang đầu</a></li>";

                    for ($i = 1; $i <= $total_pages; $i++) {
                        echo "<li class='page-item " . ($page == $i ? 'active' : '') . "'><a class='page-link' href='shop.php?page=$i'>$i</a></li>";
                    }

                    echo "<li class='page-item " . ($page == $total_pages ? 'disabled' : '') . "'><a class='page-link' href='shop.php?page=$total_pages'>Trang cuối</a></li>";
                    ?>
                </ul>
            </nav>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS và Popper.js -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>

</html>