<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$cart = $_SESSION['cart'] ?? [];
$order_success = false;
$total = 0;
$discount = 0;
$coupon = '';
$discount_amount = 0;
$final_total = 0;

// If user is not logged in, redirect to login page
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Fetch user data from the database
$user_id = $_SESSION['user']['id'];
$stmt = $conn->prepare("SELECT * FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();

// If the cart is empty, do not allow checkout
if (empty($cart)) {
    echo "<script>alert('Giỏ hàng trống!'); window.location.href='index.php';</script>";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $address = $_POST['address'];
    $note = $_POST['note'];
    $payment_method = $_POST['payment_method'] ?? 'cod';
    $coupon = trim($_POST['coupon']);

    // Calculate the total price
    foreach ($cart as $product_id => $qty) {
        $p = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
        if (!$p) continue;
        $total += $p['price'] * $qty;
    }

    // Apply discount if a coupon is used
    if (!empty($coupon)) {
        $stmt = $conn->prepare("SELECT * FROM coupons WHERE code = ?");
        $stmt->bind_param("s", $coupon);
        $stmt->execute();
        $result = $stmt->get_result();
        $coupon_data = $result->fetch_assoc();
        if ($coupon_data && isset($coupon_data['discount_percent'])) {
            $discount = $coupon_data['discount_percent'];
            $discount_amount = $total * ($discount / 100);
            $final_total = $total - $discount_amount;
        } else {
            $discount = 0;
            $final_total = $total;
        }
    } else {
        $final_total = $total;
    }

    // Save the order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, name, phone, address, note, payment_method, total, discount, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("isssssdd", $user_id, $name, $phone, $address, $note, $payment_method, $total, $discount_amount);
    $stmt->execute();
    $order_id = $stmt->insert_id;

    // Save order details
    foreach ($cart as $product_id => $qty) {
        $p = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();
        if (!$p) continue;
        $price = $p['price'];

        // Save order details
        $stmt = $conn->prepare("INSERT INTO order_details (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $product_id, $qty, $price);
        $stmt->execute();

        // Update sold_count
        $update_sold = $conn->prepare("UPDATE products SET sold_count = sold_count + ? WHERE id = ?");
        $update_sold->bind_param("ii", $qty, $product_id);
        $update_sold->execute();
    }

    unset($_SESSION['cart']);
    $order_success = true;
}
?>

<div class="container mt-4">
<?php if (!$order_success): ?>
    <div class="promo-box">
        <h3>💎 ƯU ĐÃI ĐẶC BIỆT - CHỈ CÓ TẠI JEWELRY STORE 💎</h3>
        <ul>
            <li>🔥 <strong>Giảm giá đến 30%</strong> cho tất cả sản phẩm vàng, bạc, đá quý</li>
            <li>🎁 <strong>Tặng hộp đựng cao cấp</strong> cho đơn hàng từ 1.000.000đ</li>
            <li>🚚 <strong>Miễn phí vận chuyển</strong> toàn quốc</li>
            <li>💖 Nhập mã <code>Vudeptrai</code> để <strong>giảm thêm 50%</strong></li>
            <li>⏰ Áp dụng từ <strong>01/08 – 15/08/2025</strong> – Đừng bỏ lỡ!</li>
        </ul>
    </div>
    <h2 class="mb-4 text-center">🧾 Thông tin thanh toán</h2>
    <form method="post">
        <div class="mb-3">
            <label>Họ tên người nhận</label>
            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Số điện thoại</label>
            <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Địa chỉ</label>
            <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address']); ?>" required>
        </div>
        <div class="mb-3">
            <label>Ghi chú (không bắt buộc)</label>
            <!-- Ensure the value is safely handled -->
            <textarea name="note" class="form-control"><?php echo isset($_POST['note']) ? htmlspecialchars($_POST['note']) : ''; ?></textarea>
        </div>
        <div class="mb-3">
            <label>Phương thức thanh toán</label>
            <select name="payment_method" class="form-control" required>
                <option value="cod">💵 Tiền mặt khi nhận</option>
                <option value="bank">🏦 Chuyển khoản ngân hàng</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Mã giảm giá (nếu có)</label>
            <input type="text" name="coupon" class="form-control" placeholder="Ví dụ: SAVE10">
        </div>
        <button class="btn btn-success" type="submit">✅ Xác nhận đặt hàng</button>
    </form>
<?php else: ?>
    <h3 class="text-success text-center">✅ Đặt hàng thành công!</h3>
    <div class="text-center mt-4">
        <?php if ($payment_method === 'bank'): ?>
            <div id="qr-area">
                <h5>🔁 Vui lòng quét mã QR để chuyển khoản:</h5>
                <img src="images/qr_example.png" width="200" class="my-3">
            </div>
            <p id="paid-msg" class="text-success mt-3" style="display:none;">✅ Đã nhận được tiền (giả lập).</p>
            <script>
                setTimeout(() => {
                    document.getElementById("qr-area").style.display = "none";
                    document.getElementById("paid-msg").style.display = "block";
                }, 3000);
            </script>
        <?php else: ?>
            <p class="text-success">💵 Bạn sẽ thanh toán bằng tiền mặt khi nhận hàng.</p>
        <?php endif; ?>
        <a href="index.php" class="btn btn-primary mt-3">↩ Quay lại trang chủ</a>
    </div>
<?php endif; ?>
</div>


