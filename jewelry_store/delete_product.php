<?php
// delete_product.php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'includes/db.php';

// Kiểm tra quyền admin
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'staff'])) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='index.php';</script>";
    exit();
}

// Kiểm tra có ID sản phẩm không
if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Xoá sản phẩm theo ID
    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: product.php?message=deleted");
        exit();
    } else {
        echo "Lỗi khi xoá sản phẩm: " . $conn->error;
    }
} else {
    echo "Không tìm thấy ID sản phẩm.";
}
?>
