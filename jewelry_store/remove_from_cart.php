<?php
session_start();

// Kiểm tra xem có tồn tại giỏ hàng và id sản phẩm cần xoá không
if (isset($_GET['id']) && isset($_SESSION['cart'])) {
    $product_id = $_GET['id'];

    // Xoá sản phẩm khỏi giỏ nếu tồn tại trong session
    if (array_key_exists($product_id, $_SESSION['cart'])) {
        unset($_SESSION['cart'][$product_id]);
    }

    // Nếu giỏ hàng rỗng sau khi xoá, thì huỷ luôn session giỏ hàng
    if (empty($_SESSION['cart'])) {
        unset($_SESSION['cart']);
    }
}

// Quay về trang giỏ hàng
header('Location: cart.php');
exit();
?>
