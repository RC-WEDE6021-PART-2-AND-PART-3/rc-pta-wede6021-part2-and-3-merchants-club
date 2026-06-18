<?php
// DBConn.php - Database connection file for ZA Thrift

$host = 'localhost';
$user = 'root';
$password = '';
$database = 'ClothingStore';

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set charset
mysqli_set_charset($conn, "utf8");
?>