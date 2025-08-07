<?php
if (session_status() == PHP_SESSION_NONE) session_start();
require_once 'includes/db.php';
include 'includes/header.php';
include 'includes/head.php';


$cart = $_SESSION['cart'] ?? [];
$total = 0;
?>

<div class="container mt-4">
  <h3 class="mb-4">🛒 Giỏ hàng của bạn</h3>

  <?php if (empty($cart)): ?>
    <div class="alert alert-info">Chưa có sản phẩm nào trong giỏ hàng.</div>
    <a href="index.php" class="btn btn-primary">← Quay lại mua hàng</a>
  <?php else: ?>
    <form action="update_cart.php" method="post">
      <table class="table table-bordered text-center align-middle">
        <thead class="table-warning">
          <tr>
            <th>Ảnh</th>
            <th>Sản phẩm</th>
            <th>Giá</th>
            <th>Số lượng</th>
            <th>Tổng</th>
            <th>Hành động</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($cart as $productId => $quantity):
              $query = mysqli_query($conn, "SELECT * FROM products WHERE id = $productId");
              $product = mysqli_fetch_assoc($query);
              $itemTotal = $product['price'] * $quantity;
              $total += $itemTotal;
          ?>
            <tr>
              <td><img src="assets/images/<?= $product['image'] ?>" width="60" height="60" style="object-fit: cover;"></td>
              <td><?= $product['name'] ?></td>
              <td><?= number_format($product['price']) ?>đ</td>
              <td>
                <input type="number" name="quantities[<?= $productId ?>]" value="<?= $quantity ?>" min="1" class="form-control" style="width: 70px; margin: auto;">
              </td>
              <td><?= number_format($itemTotal) ?>đ</td>
              <td>
                <a href="remove_from_cart.php?id=<?= $productId ?>" class="btn btn-sm btn-danger">Xoá</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>

      <div class="d-flex justify-content-between align-items-center">
        <h5 class="text-success">Tổng cộng: <?= number_format($total) ?>đ</h5>
        <div>
          <a href="index.php" class="btn btn-success">Mua tiếp</a>
          <a href="checkout.php" class="btn btn-success">🧾 Thanh toán</a>
        </div>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php include 'includes/footer.php'; ?>
