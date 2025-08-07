<?php
// Bật báo lỗi để debug (chỉ dùng trong môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Khởi động session
if (session_status() === PHP_SESSION_NONE) session_start();

// Include file kết nối cơ sở dữ liệu
include 'includes/db.php';

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die('Lỗi kết nối cơ sở dữ liệu: ' . mysqli_connect_error());
}

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
    header("Location: cart.php");
    exit();
}

// Tìm kiếm và lọc sản phẩm
$search = $_GET['search'] ?? '';
$category = $_GET['category'] ?? '';
$sql = "SELECT * FROM products WHERE 1";
if ($search) $sql .= " AND name LIKE '%" . mysqli_real_escape_string($conn, $search) . "%'";
if ($category) $sql .= " AND category = '" . mysqli_real_escape_string($conn, $category) . "'";
$result = mysqli_query($conn, $sql);
if (!$result) {
    die('Truy vấn thất bại: ' . mysqli_error($conn));
}

// Sản phẩm bán chạy
$best_sellers = [];
$best_sellers_sql = "SELECT * FROM products ORDER BY sold_count DESC LIMIT 4";
$best_sellers_result = mysqli_query($conn, $best_sellers_sql);
if ($best_sellers_result && mysqli_num_rows($best_sellers_result) > 0) {
    while ($row = mysqli_fetch_assoc($best_sellers_result)) {
        $best_sellers[] = $row;
    }
}

include 'includes/head.php';
include 'includes/header.php';
?>

<div style="text-align: center; max-width: 1000px; margin: 40px auto;">
  <?php
    $dir = "uploads/banner/";
    $images = glob($dir . "*.{jpg,jpeg,png,gif,webp}", GLOB_BRACE);
    foreach ($images as $index => $img) {
      echo "<img src='$img' class='manual-slide' style='width: 100%; max-width: 1000px; height: auto; display:" . ($index === 0 ? 'block' : 'none') . "; margin: 0 auto; border-radius: 8px;'>";
    }
  ?>
    <div style="margin-top: 12px;">
    <a href="menu.php" style="display: inline-block; text-decoration: none; padding: 8px 16px; background-color: #f5f5f5; border: 1px solid #ccc; border-radius: 6px; color: #333;">
      Xem thêm
    </a>
  </div>
</div>

<!-- Phần ưu đãi đặc biệt -->
<div class="promo-box container p-4 mb-4 bg-light border rounded shadow-sm">
  <h3 class="text-center">💎 ƯU ĐÃI ĐẶC BIỆT - CHỈ CÓ TẠI JEWELRY STORE 💎</h3>
  <ul>
    <li>🔥 <strong>Giảm giá đến 30%</strong> cho tất cả sản phẩm vàng, bạc, đá quý</li>
    <li>🏱 <strong>Tặng hộp đựng cao cấp</strong> cho đơn hàng từ 1.000.000đ</li>
    <li>🚚 <strong>Miễn phí vận chuyển</strong> toàn quốc</li>
    <li>💖 Nhập mã <code>Vudeptrai</code> để <strong>giảm thêm 50%</strong></li>
    <li>⏰ Áp dụng từ <strong>14/07 – 16/07/2025</strong> – Đừng bỏ lỡ!</li>
  </ul>
</div>

<!-- Sản phẩm bán chạy -->
<div class="best-sellers-box container mb-5">
    <h2 class="best-sellers-header text-center mb-4">Sản phẩm bán chạy</h2>
    <div class="row">
        <?php foreach ($best_sellers as $product): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php
                    // Kiểm tra đường dẫn hình ảnh
                    $image_path = !empty($product['image']) && file_exists($product['image']) 
                        ? htmlspecialchars($product['image']) 
                        : 'default_image.jpg';
                    ?>
                    <img src="<?php echo $image_path; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="width: 200px; height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h5>
                        <p class="card-text text-danger fw-bold"><?php echo number_format($product['price'], 0, ',', '.') . ' ₫'; ?></p>
                        <a href="product_detail.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-outline-dark">Xem chi tiết</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- Danh sách sản phẩm -->
<div class="container">
  <div class="row">
    <!-- Sidebar danh mục -->
    <div class="col-md-3 mb-3">
      <h5>📁 Danh mục</h5>
      <div class="list-group">
        <a href="index.php?category=Nhẫn" class="list-group-item">Nhẫn</a>
        <a href="index.php?category=Vòng" class="list-group-item">Vòng</a>
        <a href="index.php?category=Sets" class="list-group-item">Sets</a>
        <a href="index.php" class="list-group-item">Tất cả</a>
      </div>
    </div>

    <!-- Sản phẩm -->
    <div class="col-md-9">
      <div class="row">
        <?php if (mysqli_num_rows($result) > 0): ?>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="col-md-4 mb-4">
              <div class="card h-100 shadow-sm border-0">
                <?php
                // Kiểm tra đường dẫn hình ảnh
                $image_path = !empty($row['image']) && file_exists($row['image']) 
                    ? htmlspecialchars($row['image']) 
                    : 'default_image.jpg';
                ?>
                <img src="<?php echo $image_path; ?>" style="width:200px; height:200px; object-fit:cover;" alt="<?php echo htmlspecialchars($row['name']); ?>">
                <div class="card-body d-flex flex-column">
                  <h6 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h6>
                  <p class="text-danger fw-bold"><?php echo number_format($row['price'], 0, ',', '.') . 'đ'; ?></p>
                  <a href="product_detail.php?id=<?php echo htmlspecialchars($row['id']); ?>" class="btn btn-info btn-sm">Chi tiết</a>
                  <form method="post" class="mt-2">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($row['id']); ?>">
                    <button name="add_to_cart" class="btn btn-success btn-sm w-100">🛒 Thêm vào giỏ</button>
                  </form>
                </div>
              </div>
            </div>
          <?php endwhile; ?>
        <?php else: ?>
          <div class="col-12">
            <div class="alert alert-warning text-center">Không tìm thấy sản phẩm phù hợp.</div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>