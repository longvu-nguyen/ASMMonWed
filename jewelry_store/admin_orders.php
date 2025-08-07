<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

// ✅ Chỉ admin & nhân viên được xem đơn hàng
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'staff'])) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='index.php';</script>";
    exit();
}

$orders = $conn->query("
    SELECT orders.*, users.username 
    FROM orders 
    JOIN users ON orders.user_id = users.id 
    ORDER BY orders.created_at DESC
");
?>

<div class="container mt-4">
    <h3 class="mb-4">🧾 Danh sách đơn hàng</h3>

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Mã đơn</th>
                <th>Người đặt</th>
                <th>Tên người nhận</th>
                <th>SĐT</th>
                <th>Địa chỉ</th>
                <th>Ngày</th>
                <th>Chi tiết</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($order = $orders->fetch_assoc()): ?>
                <tr>
                    <td>#<?= $order['id'] ?></td>
                    <td><?= htmlspecialchars($order['username']) ?></td>
                    <td><?= htmlspecialchars($order['name']) ?></td>
                    <td><?= $order['phone'] ?></td>
                    <td><?= $order['address'] ?></td>
                    <td><?= $order['created_at'] ?></td>
                    <td>
                        <a href="order_detail.php?id=<?= $order['id'] ?>" class="btn btn-outline-info btn-sm">🔍 Xem chi tiết</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>
