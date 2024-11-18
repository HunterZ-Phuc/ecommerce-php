<?php
require_once 'C:/xampp/htdocs/nongsan/src/utils/db_connect.php';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = mysqli_real_escape_string($con, $_POST['id']);
    $name = mysqli_real_escape_string($con, $_POST['name']);
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $phone = mysqli_real_escape_string($con, $_POST['phone']);
    $department = mysqli_real_escape_string($con, $_POST['department']);
    $position = mysqli_real_escape_string($con, $_POST['position']);
    
    $query = "UPDATE employees SET 
              name = '$name',
              email = '$email',
              phone = '$phone',
              department = '$department',
              position = '$position'
              WHERE id = '$id'";
              
    if(mysqli_query($con, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => mysqli_error($con)]);
    }
}
?> 