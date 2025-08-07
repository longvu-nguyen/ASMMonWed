<?php
if (session_status() == PHP_SESSION_NONE) session_start();

// Kiểm tra người dùng đã đăng nhập chưa
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Kết nối cơ sở dữ liệu
include 'includes/db.php';

// Cập nhật thông tin người dùng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_address = mysqli_real_escape_string($conn, $_POST['address']);
    $new_phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $new_email = mysqli_real_escape_string($conn, $_POST['email']);
    $user_id = $_SESSION['user']['id'];

    $sql = "UPDATE users SET address = '$new_address', phone = '$new_phone', email = '$new_email' WHERE id = $user_id";
    if (mysqli_query($conn, $sql)) {
        $_SESSION['user']['address'] = $new_address;
        $_SESSION['user']['phone'] = $new_phone;
        $_SESSION['user']['email'] = $new_email;
        $message = "Cập nhật thông tin thành công!";
    } else {
        $message = "Có lỗi xảy ra. Vui lòng thử lại!";
    }
}

// Lấy thông tin người dùng
$user_id = $_SESSION['user']['id'];
$sql = "SELECT * FROM users WHERE id = $user_id";
$result = mysqli_query($conn, $sql);
$user = mysqli_fetch_assoc($result);

?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Cập nhật thông tin cá nhân</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Cập nhật thông tin cá nhân</h2>
        <form method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Tên người dùng</label>
                <input type="text" class="form-control" id="username" value="<?= htmlspecialchars($user['username']) ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($user['phone']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" value="<?= htmlspecialchars($user['address']) ?>" required>
            </div>
            <button type="submit" class="btn btn-success">Cập nhật</button>
        </form>
        <?php if (isset($message)): ?>
            <div class="alert alert-info mt-3"><?= $message ?></div>
        <?php endif; ?>
    </div>
</body>
</html>
<?php include 'includes/footer.php'; ?>
