<?php
if (session_status() == PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Jewelry Store</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    .dropdown-menu a {
      display: flex;
      align-items: center;
      gap: 8px;
    }
    .dropdown-menu {
      min-width: 220px;
    }
    @media (max-width: 576px) {
      form.d-flex {
        flex-direction: column;
        gap: 5px;
      }
    }
  </style>
</head>
<body>

<nav class="navbar navbar-light bg-white shadow-sm px-3">
  <div class="container-fluid d-flex align-items-center justify-content-between">

    <!-- Logo -->
    <a class="navbar-brand fw-bold" href="index.php">
      <img src="https://cdn.haitrieu.com/wp-content/uploads/2023/02/Logo-Truong-cao-dang-Quoc-te-BTEC-FPT.png" width="30"> Jewelry Store
    </a>

    <!-- Tìm kiếm để bên ngoài menu -->
    <form class="d-flex mx-2 flex-grow-1" action="index.php" method="get" style="max-width: 400px;">
      <input class="form-control me-2" type="search" name="search" placeholder="Tìm kiếm trang sức...">
      <button class="btn btn-warning" type="submit">🔍</button>
    </form>

    <!-- Menu 3 gạch -->
    <div class="dropdown">
       <button class="btn bg-transparent border-0 p-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">
    <img src="<?php echo $_SESSION['user']['avatar'] ?? 'uploads/avatar/hehe.jpg'; ?>" 
         alt="Avatar" 
         class="rounded-circle" 
         style="width: 40px; height: 40px; object-fit: cover;">
  </button>
      <ul class="dropdown-menu dropdown-menu-end p-2 shadow">

        <li><a class="dropdown-item btn btn-outline-dark mb-1" href="index.php">🏠 Trang chủ</a></li>
        <li><a class="dropdown-item btn btn-outline-danger mb-1" href="contact.php">📞 Liên hệ</a></li>
        <li><a class="dropdown-item btn btn-outline-secondary mb-1" href="cart.php">🛒 Giỏ hàng</a></li>

       <?php if (isset($_SESSION['user'])): ?>
          <li><span class="dropdown-item btn btn-outline-primary mb-1 disabled">👤 <?= $_SESSION['user']['username'] ?></span></li>
          <li><a class="dropdown-item btn btn-outline-info mb-1" href="update_profile.php">📝 Thông tin cá nhân</a></li>
          
          <?php if (in_array($_SESSION['user']['role'], ['admin', 'staff'])): ?>
            <li><a class="dropdown-item btn btn-outline-success mb-1" href="manage_roles.php">⚙️ Phân quyền</a></li>
            <li><a class="dropdown-item btn btn-outline-warning mb-1" href="product_admin.php">🛠 Sản phẩm</a></li>
            <li><a class="dropdown-item btn btn-outline-info mb-1" href="admin_orders.php">📦 Đơn hàng</a></li>
            <li><a class="dropdown-item btn btn-outline-info mb-1" href="admin_coupons.php">📦 Mã giảm giá</a></li>
           <li><a class="dropdown-item btn btn-outline-info mb-1" href="admin_dashboard.php">📦 Thống kê</a></li>


          <?php endif; ?>

          <li><a class="dropdown-item btn btn-outline-dark mb-1" href="logout.php">🚪 Đăng xuất</a></li>
        <?php else: ?>
          <li><a class="dropdown-item btn btn-outline-primary mb-1" href="login.php">🔐 Đăng nhập</a></li>
          <li><a class="dropdown-item btn btn-outline-success mb-1" href="register.php">📝 Đăng ký</a></li>
        <?php endif; ?>

      </ul>
    </div>
  </div>
</nav>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
