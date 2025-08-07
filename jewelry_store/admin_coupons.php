<?php
session_start();
require 'includes/db.php';
include 'includes/head.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    echo "<script>alert('Bạn không có quyền truy cập!'); window.location.href='index.php';</script>";
    exit();
}

// Thêm mã
if (isset($_POST['add'])) {
    $code = strtoupper(trim($_POST['code']));
    $type = $_POST['type'];
    $value = $_POST['value'];
    $expired_at = $_POST['expired_at'];

    $stmt = $conn->prepare("INSERT INTO coupons (code, type, value, expired_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssds", $code, $type, $value, $expired_at);
    $stmt->execute();
}

// Xoá mã
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $conn->query("DELETE FROM coupons WHERE id = $id");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý mã giảm giá</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="container mt-5">
    <h3 class="mb-4">🎁 Quản lý mã giảm giá</h3>

    <!-- Form thêm mã -->
    <form method="post" class="border p-4 mb-4 bg-light">
        <h5>➕ Thêm mã mới</h5>
        <div class="row">
            <div class="col-md-3 mb-2"><input type="text" name="code" class="form-control" placeholder="Mã (SAVE10)" required></div>
            <div class="col-md-2 mb-2">
                <select name="type" class="form-control">
                    <option value="percent">Phần trăm</option>
                    <option value="fixed">Giảm tiền</option>
                </select>
            </div>
            <div class="col-md-2 mb-2"><input type="number" step="0.01" name="value" class="form-control" placeholder="Giá trị" required></div>
            <div class="col-md-3 mb-2"><input type="date" name="expired_at" class="form-control" required></div>
            <div class="col-md-2 mb-2"><button type="submit" name="add" class="btn btn-success w-100">Thêm</button></div>
        </div>
    </form>

    <!-- Danh sách mã -->
    <table class="table table-bordered text-center">
        <thead class="table-warning">
            <tr>
                <th>ID</th>
                <th>Mã</th>
                <th>Loại</th>
                <th>Giá trị</th>
                <th>Hết hạn</th>
                <th>Xoá</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $coupons = $conn->query("SELECT * FROM coupons ORDER BY id DESC");
            while ($row = $coupons->fetch_assoc()):
            ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><strong><?= $row['code'] ?></strong></td>
                <td><?= $row['type'] === 'percent' ? 'Phần trăm' : 'Giảm tiền' ?></td>
                <td><?= $row['type'] === 'percent' ? $row['value'].'%' : number_format($row['value']).'đ' ?></td>
                <td><?= $row['expired_at'] ?></td>
                <td><a href="?delete=<?= $row['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá mã này?')">🗑</a></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>
</html>
