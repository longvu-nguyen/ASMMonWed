<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

// Chỉ cho admin vào
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    die("<div class='alert alert-danger'>Bạn không có quyền truy cập trang này.</div>");
}

// Xử lý cập nhật vai trò
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = (int)$_POST['user_id'];
    $new_role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
    $stmt->bind_param("si", $new_role, $user_id);
    $stmt->execute();
    echo "<div class='alert alert-success'>Cập nhật quyền thành công!</div>";
}

// Lấy danh sách người dùng
$result = $conn->query("SELECT id, username, email, phone, role FROM users");
?>

<div class="container mt-4">
  <h2 class="mb-4">Phân quyền tài khoản</h2>
  <table class="table table-bordered">
    <thead>
      <tr>
        <th>Tên người dùng</th>
        <th>Email</th>
        <th>Số điện thoại</th>
        <th>Vai trò hiện tại</th>
        <th>Chọn vai trò mới</th>
        <th>Hành động</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
          <form method="post">
            <td><?= htmlspecialchars($row['username']) ?></td>
            <td><?= htmlspecialchars($row['email']) ?></td>
            <td><?= htmlspecialchars($row['phone']) ?></td>
            <td><?= htmlspecialchars($row['role']) ?></td>
            <td>
              <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
              <select name="role" class="form-select">
                <option value="customer" <?= $row['role'] === 'customer' ? 'selected' : '' ?>>Khách hàng</option>
                <option value="staff" <?= $row['role'] === 'staff' ? 'selected' : '' ?>>Nhân viên</option>
                <option value="admin" <?= $row['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
              </select>
            </td>
            <td>
              <button type="submit" class="btn btn-primary btn-sm">Cập nhật</button>
            </td>
          </form>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php include 'includes/footer.php'; ?>
