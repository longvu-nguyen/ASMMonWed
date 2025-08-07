<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}require 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'staff'])) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='index.php';</script>";
    exit();
}

$order_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$order_id) {
    echo "Đơn hàng không tồn tại.";
    exit;
}

// Lấy thông tin đơn hàng
$order = mysqli_query($conn, "
    SELECT o.*, u.username 
    FROM orders o 
    JOIN users u ON o.user_id = u.id 
    WHERE o.id = $order_id
")->fetch_assoc();

if (!$order) {
    echo "Không tìm thấy đơn hàng.";
    exit;
}

// Lấy chi tiết sản phẩm trong đơn hàng
$details = mysqli_query($conn, "
    SELECT d.*, p.name AS product_name 
    FROM order_details d 
    JOIN products p ON d.product_id = p.id 
    WHERE d.order_id = $order_id
");
?>

<div class="container mt-4">
    <h3>Chi tiết đơn hàng #<?= $order_id ?></h3>
    <p><strong>Người đặt:</strong> <?= $order['username'] ?></p>
    <p><strong>Người nhận:</strong> <?= $order['name'] ?></p>
    <p><strong>Địa chỉ:</strong> <?= $order['address'] ?></p>
    <p><strong>SĐT:</strong> <?= $order['phone'] ?></p>
    <p><strong>Ghi chú:</strong> <?= $order['note'] ?></p>
    <p><strong>Thời gian:</strong> <?= $order['created_at'] ?></p>

    <h4>Sản phẩm:</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tên sản phẩm</th>
                <th>Số lượng</th>
                <th>Giá</th>
                <th>Tổng</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $total = 0;
            while ($d = mysqli_fetch_assoc($details)) {
                $lineTotal = $d['quantity'] * $d['price'];
                $total += $lineTotal;
            ?>
            <tr>
                <td><?= $d['product_name'] ?></td>
                <td><?= $d['quantity'] ?></td>
                <td><?= number_format($d['price']) ?>đ</td>
                <td><?= number_format($lineTotal) ?>đ</td>
            </tr>
            <?php } ?>
            <tr>
                <td colspan="3" class="text-end"><strong>Tổng cộng:</strong></td>
                <td><strong><?= number_format($total) ?>đ</strong></td>
            </tr>
        </tbody>
    </table>
</div>
