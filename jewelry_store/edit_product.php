<?php
if (session_status() == PHP_SESSION_NONE) session_start();
include 'includes/db.php';
include 'includes/header.php';
include 'includes/head.php';

// Chỉ cho admin truy cập
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'staff'])) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='index.php';</script>";
    exit();
}

if (!isset($_GET['id'])) {
    die("Không tìm thấy ID sản phẩm.");
}

$id = (int)$_GET['id'];
$product = mysqli_query($conn, "SELECT * FROM products WHERE id = $id")->fetch_assoc();

if (!$product) {
    die("Sản phẩm không tồn tại.");
}

// Xử lý cập nhật
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $category = $_POST['category'];
    $image = $product['image']; // Giữ ảnh cũ mặc định

    // Nếu có ảnh mới
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) mkdir($uploadDir);
        $fileName = time() . '_' . basename($_FILES['image']['name']);
        $targetPath = $uploadDir . $fileName;

        if (move_uploaded_file($_FILES['image']['tmp_name'], $targetPath)) {
            $image = $targetPath;
        }
    }

    // Cập nhật DB
    $query = "UPDATE products SET 
        name='$name', 
        price='$price', 
        description='$desc', 
        category='$category', 
        image='$image' 
        WHERE id=$id";
    mysqli_query($conn, $query);

    echo "<script>alert('✅ Cập nhật sản phẩm thành công!'); window.location.href='product_admin.php';</script>";
    exit();
}
?>

<div class="container mt-5">
    <h2 class="mb-4">Chỉnh sửa sản phẩm</h2>
    <form method="post" enctype="multipart/form-data" class="row g-3">
        <div class="col-md-4">
            <label>Tên sản phẩm</label>
            <input type="text" name="name" value="<?= htmlspecialchars($product['name']) ?>" class="form-control" required>
        </div>
        <div class="col-md-2">
            <label>Giá</label>
            <input type="number" name="price" value="<?= $product['price'] ?>" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Loại</label>
            <input type="text" name="category" value="<?= htmlspecialchars($product['category']) ?>" class="form-control" required>
        </div>
        <div class="col-md-3">
            <label>Ảnh hiện tại</label><br>
            <img src="<?= $product['image'] ?>" width="80">
        </div>
        <div class="col-md-12">
            <label>Chọn ảnh mới (nếu muốn thay)</label>
            <input type="file" name="image" class="form-control">
        </div>
        <div class="col-md-12">
            <label>Mô tả</label>
            <textarea name="description" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
        </div>

        <div class="form-group">
            <label>Trọng lượng</label>
            <input type="text" name="weight" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Xuất xứ</label>
            <input type="text" name="origin" class="form-control" required>
        </div>
        <div class="col-12">
            <button class="btn btn-success">💾 Lưu thay đổi</button>
            <a href="product_admin.php" class="btn btn-secondary">🔙 Quay lại</a>
        </div>
    </form>
</div>

<?php include 'includes/footer.php'; ?>
