<?php
$host = "localhost";
$user = "root";
$pass = "";
$db = "ecom";

$con = mysqli_connect($host, $user, $pass, $db);

if (!$con) {
    die("Ket noi that bai: " . mysqli_connect_error());
}

?>