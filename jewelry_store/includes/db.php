<?php
$conn = mysqli_connect("localhost", "root", "", "jewelry_store");
if (!$conn) {
    die("Kết nối thất bại: " . mysqli_connect_error());
}
?>