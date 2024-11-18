<?php
function getPCates() {
    global $con;
    
    // Lấy thông tin về cấu trúc của cột category
    $query = "SHOW COLUMNS FROM products WHERE Field = 'category'";
    $result = mysqli_query($con, $query);
    
    if (!$result) {
        die("Lỗi truy vấn: " . mysqli_error($con));
    }
    
    $row = mysqli_fetch_assoc($result);
    // Lấy các giá trị enum từ chuỗi type
    preg_match("/^enum\(\'(.*)\'\)$/", $row['Type'], $matches);
    $enum_values = explode("','", $matches[1]);
    
    // Hiển thị danh mục cho mỗi giá trị enum
    foreach ($enum_values as $category) {
        // Chuyển đổi tên hiển thị
        $display_name = ucfirst(strtolower($category));
        
        // Tạo icon tương ứng cho từng danh mục
        $icon_class = '';
        switch(strtoupper($category)) {
            case 'FRUITS':
                $icon_class = 'fas fa-apple-alt';
                $display_name = 'Trái cây';
                break;
            case 'VEGETABLES':
                $icon_class = 'fas fa-carrot';
                $display_name = 'Rau củ';
                break;
            case 'GRAINS':
                $icon_class = 'fas fa-seedling';
                $display_name = 'Ngũ cốc';
                break;
            case 'OTHERS':
                $icon_class = 'fas fa-ellipsis-h';
                $display_name = 'Khác';
                break;
        }
        
        echo "<li>
                <a href='shop.php?category=" . strtolower($category) . "' class='text-dark'>
                    <i class='$icon_class me-2'></i>$display_name
                </a>
              </li>";
    }
}

// Xóa function getCat() vì chúng ta sẽ dùng categories từ bảng products
?>

<div class="sidebar-wrapper p-3">
   <div class="card mb-4 shadow-sm">
      <div class="card-header bg-primary text-white">
         <h5 class="card-title mb-0">
            <i class="fas fa-boxes me-2"></i>Danh Mục Sản Phẩm
         </h5>
      </div>

      <div class="card-body">
         <ul class="sidebar-nav-list">
            <?php getPCates(); ?>
         </ul>
      </div>
   </div>
</div>

<style>
   .sidebar-wrapper {
      background-color: #f8f9fa;
      border-radius: 8px;
   }

   .sidebar-wrapper .card {
      border: none;
      transition: transform 0.2s;
   }

   .sidebar-wrapper .card:hover {
      transform: translateY(-2px);
   }

   .sidebar-wrapper .card-header {
      border-radius: 6px 6px 0 0 !important;
   }

   .sidebar-nav-list {
      list-style: none;
      padding: 0;
      margin: 0;
   }

   .sidebar-nav-list li {
      padding: 12px 15px;
      margin: 2px 0;
      border-radius: 4px;
      transition: all 0.2s;
      background-color: transparent;
   }

   .sidebar-nav-list a {
      text-decoration: none;
      font-family: 'Arial', sans-serif;
   }

   .sidebar-nav-list li:hover {
      background-color: #e9ecef;
      padding-left: 20px;
      color: #0056b3;
      font-weight: bold;
   }
</style>