<?php
if (session_status() === PHP_SESSION_NONE) session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $role = 'user';

    if ($username && $email && $phone && $password && $confirm_password) {
        if (strlen($username) < 5) {
            $error = "❌ Tên tài khoản phải có ít nhất 5 ký tự.";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = "❌ Email không hợp lệ.";
        } elseif (strlen($password) < 5) {
            $error = "❌ Mật khẩu phải có ít nhất 5 ký tự.";
        } elseif ($password !== $confirm_password) {
            $error = "❌ Mật khẩu và Nhập lại mật khẩu không trùng khớp.";
        } else {
            // Kiểm tra username hoặc email đã tồn tại
            $stmt = $conn->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $error = "❌ Tài khoản hoặc email đã tồn tại!";
            } else {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("INSERT INTO users (username, email, phone, password, role) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("sssss", $username, $email, $phone, $hashedPassword, $role);
                if ($stmt->execute()) {
                    $success = "✅ Đăng ký thành công! Bạn có thể đăng nhập.";
                } else {
                    $error = "❌ Có lỗi xảy ra khi đăng ký.";
                }
            }
        }
    } else {
        $error = "❌ Vui lòng nhập đầy đủ thông tin.";
    }
}
?>

<div class="container mt-5" style="max-width: 500px;">
    <div class="card shadow-sm">
        <div class="card-body">
            <h3 class="text-center mb-4">📝 Đăng ký tài khoản</h3>
            <?php if ($error): ?><div class="alert alert-danger"><?= $error ?></div><?php endif; ?>
            <?php if ($success): ?><div class="alert alert-success"><?= $success ?></div><?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Tài khoản</label>
                    <input type="text" name="username" class="form-control" required minlength="5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Số điện thoại</label>
                    <input type="text" name="phone" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mật khẩu</label>
                    <input type="password" name="password" class="form-control" required minlength="5">
                </div>
                <div class="mb-3">
                    <label class="form-label">Nhập lại mật khẩu</label>
                    <input type="password" name="confirm_password" class="form-control" required minlength="5">
                </div>
                <button class="btn btn-primary w-100">Đăng ký</button>
                <div class="text-center mt-3">
                    Đã có tài khoản? <a href="login.php">Đăng nhập</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
