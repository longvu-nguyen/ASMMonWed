<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}include 'includes/db.php';
include 'includes/header.php';
include 'includes/head.php';

if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'staff'])) {
    echo "<script>alert('Bạn không có quyền!'); window.location.href='index.php';</script>";
    exit();
}

// Thêm sản phẩm
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $desc = $_POST['description'];
    $image = $_POST['image']; // Link ảnh trực tiếp
    $category = $_POST['category'];
    mysqli_query($conn, "INSERT INTO products(name, price, description, image, category) VALUES('$name', '$price', '$desc', '$image', '$category')");
    echo "<div class='alert alert-success'>Đã thêm sản phẩm!</div>";
}

// Xoá sản phẩm
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM products WHERE id=$id");
    echo "<div class='alert alert-danger'>Đã xoá sản phẩm!</div>";
}

// Lấy danh sách sản phẩm
$result = mysqli_query($conn, "SELECT * FROM products");
?>

<div class="container mt-5">
    <h2>Quản lý sản phẩm</h2>
    <form method="post" class="row g-3 mb-4">
        <input type="hidden" name="add" value="1">
        <div class="col-md-4">
            <input type="text" name="name" class="form-control" placeholder="Tên sản phẩm" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="price" class="form-control" placeholder="Giá" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="image" class="form-control" placeholder="Link ảnh">
        </div>
        <div class="col-md-3">
            <input type="text" name="category" class="form-control" placeholder="Loại">
        </div>
        <div class="col-12">
            <textarea name="description" class="form-control" placeholder="Mô tả sản phẩm"></textarea>
        </div>
        <div class="col-12">
            <button class="btn btn-primary">Thêm sản phẩm</button>
        </div>
    </form>

    <h4>Danh sách sản phẩm</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Ảnh</th>
                <th>Tên</th>
                <th>Giá</th>
                <th>Mô tả</th>
                <th>Loại</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)) { ?>
            <tr>
                <td><img src="<?= $row['image'] ?>" width="60"></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= number_format($row['price']) ?>đ</td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['category']) ?></td>
                <td>
                    <a href="edit_product.php?id=<?= $row['id'] ?>" class="btn btn-sm btn-warning">Sửa</a>
                    <a href="?delete=<?= $row['id'] ?>" onclick="return confirm('Xoá sản phẩm này?')" class="btn btn-sm btn-danger">Xoá</a>
                </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php include 'includes/footer.php'; ?>