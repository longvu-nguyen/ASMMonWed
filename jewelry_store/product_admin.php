<?php
session_start();
include 'includes/db.php';
include 'includes/head.php';
include 'includes/header.php';

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["name"]);
    $price = intval($_POST["price"]);
    $description = trim($_POST["description"]);
    $weight = trim($_POST["weight"]);
    $origin = trim($_POST["origin"]);

    // Xử lý ảnh
    $imageName = $_FILES["image"]["name"];
    $imageTmp = $_FILES["image"]["tmp_name"];
    $targetDir = "uploads/";
    $targetFile = $targetDir . basename($imageName);

    if (move_uploaded_file($imageTmp, $targetFile)) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, weight, origin, image) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sissss", $name, $price, $description, $weight, $origin, $imageName);
        if ($stmt->execute()) {
            $success = "Thêm sản phẩm thành công!";
        } else {
            $error = "Lỗi thêm sản phẩm: " . $stmt->error;
        }
    } else {
        $error = "Lỗi tải ảnh lên.";
    }
}

// Lấy danh sách sản phẩm
$productResult = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>

<div class="container mt-5">
    <h3>Thêm sản phẩm mới</h3>
    <?php if ($success): ?>
        <div class="alert alert-success"><?php echo $success; ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo $error; ?></div>
    <?php endif; ?>

    <form action="" method="post" enctype="multipart/form-data">
        <div class="form-group">
            <label>Tên sản phẩm</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Giá</label>
            <input type="number" name="price" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Mô tả</label>
            <textarea name="description" class="form-control" rows="3" required></textarea>
        </div>
        <div class="form-group">
            <label>Trọng lượng</label>
            <input type="text" name="weight" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Xuất xứ</label>
            <input type="text" name="origin" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Ảnh sản phẩm</label>
            <input type="file" name="image" class="form-control-file" accept="image/*" required>
        </div>
        <button type="submit" class="btn btn-primary mt-3">Thêm sản phẩm</button>
    </form>

    <hr class="my-5">

    <h4>Danh sách sản phẩm</h4>
    <div class="row">
        <?php while($row = $productResult->fetch_assoc()): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <?php
$imagePath = 'uploads/' . $row['image'];
if (!file_exists($imagePath) || empty($row['image'])) {
    $imagePath = 'uploads/default.jpg'; // bạn nhớ thêm file ảnh mặc định này
}
?>
<img src="<?php echo $imagePath; ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($row['name']); ?></h5>
                        <p class="card-text"><?php echo number_format($row['price']); ?> VNĐ</p>
                        <p class="card-text"><small><?php echo htmlspecialchars($row['origin']); ?></small></p>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-warning">Sửa</a>
                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Xoá sản phẩm này?')">Xoá</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
