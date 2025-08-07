<?php
// Bật báo lỗi để debug (chỉ dùng trong môi trường phát triển)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Khởi động session
if (session_status() === PHP_SESSION_NONE) session_start();

// Include file kết nối cơ sở dữ liệu (PDO)
include 'includes/db.php';

// Kiểm tra kết nối cơ sở dữ liệu
if (!$conn) {
    die('Lỗi kết nối cơ sở dữ liệu: ' . implode(" - ", $conn->errorInfo()));
}

// Xử lý thêm sản phẩm vào giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $product_id = (int)$_POST['product_id'];
    $_SESSION['cart'][$product_id] = ($_SESSION['cart'][$product_id] ?? 0) + 1;
    header("Location: menu.php");
    exit();
}

// Lấy tất cả sản phẩm
$sql = "SELECT * FROM products";
$result = mysqli_query($conn, $sql);

$products = [];
if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = $row;
    }
}

include 'includes/head.php';
include 'includes/header.php';
?>

<!-- Menu Section -->
<div class="container mt-5">
    <h2 class="text-center mb-4">Thực Đơn Sản Phẩm</h2>
    <div class="row">
        <?php if (!empty($products)): ?>
            <?php foreach ($products as $product): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <?php
                        $image_path = !empty($product['image']) && file_exists($product['image']) 
                            ? htmlspecialchars($product['image']) 
                            : 'default_image.jpg';
                        ?>
                        <img src="<?php echo $image_path; ?>" class="card-img-top" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title"><?php echo htmlspecialchars($product['name']); ?></h6>
                            <p class="card-text text-danger fw-bold"><?php echo number_format($product['price'], 0, ',', '.') . ' ₫'; ?></p>
                            <a href="product_detail.php?id=<?php echo htmlspecialchars($product['id']); ?>" class="btn btn-info btn-sm mt-auto">Chi tiết</a>
                            <form method="post" class="mt-2">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product['id']); ?>">
                                <a href="cart.php" class="btn btn-success btn-sm w-100">🛒 Thêm vào giỏ</a>
                            </form>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="col-12">
                <div class="alert alert-warning text-center">Không có sản phẩm nào để hiển thị.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>