<?php
// config/ketnoi.php

$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "managetkb"; 

// Tạo kết nối
$conn = new mysqli($servername, $username, $password, $dbname);

// Kiểm tra kết nối
if ($conn->connect_error) {
  die("Kết nối CSDL thất bại: " . $conn->connect_error);
}


$conn->set_charset("utf8mb4");

?>