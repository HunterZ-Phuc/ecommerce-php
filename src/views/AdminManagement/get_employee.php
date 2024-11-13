<?php
require_once 'C:/xampp/htdocs/nongsan/src/utils/db_connect.php';

if(isset($_GET['id'])) {
    $id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM employees WHERE id = '$id'";
    $result = mysqli_query($con, $query);
    
    if($employee = mysqli_fetch_assoc($result)) {
        echo json_encode($employee);
    } else {
        echo json_encode(['error' => 'Không tìm thấy nhân viên']);
    }
}
?> 