<?php
include 'includes/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if (!$product) {
        echo "<p class='text-danger'>Không tìm thấy sản phẩm.</p>";
        exit;
    }
} else {
    echo "<p class='text-danger'>Không có ID sản phẩm.</p>";
    exit;
}
?>

<?php include 'includes/header.php'; ?>

<div class="container py-4">
    <div class="row">
        <div class="col-md-6">
            <?php if (isset($product['image']) && $product['image']): ?>
                <img src="<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="img-fluid rounded shadow-sm border" style="max-width: 100%; height: auto;">
            <?php else: ?>
                <div class="alert alert-warning">Không có hình ảnh cho sản phẩm này.</div>
            <?php endif; ?>
        </div>
        <div class="col-md-6">
            <h2><?php echo htmlspecialchars($product['name']); ?></h2>
            <p><strong>Giá:</strong> <?php echo number_format($product['price'], 0, ',', '.') . ' đ'; ?></p>
            <p><strong>Loại:</strong> <?php echo htmlspecialchars($product['category']); ?></p>
            <p><strong>Xuất xứ:</strong> <?php echo htmlspecialchars($product['origin']); ?></p>
            <p><strong>Trọng lượng:</strong> <?php echo htmlspecialchars($product['weight']); ?> gram</p>
            <p><strong>Mô tả:</strong> <?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

            <form action="cart.php" method="post">
                <input type="hidden" name="id" value="<?php echo $product['id']; ?>">
                <button type="submit" class="btn btn-success mt-3">Thêm vào giỏ hàng</button>
            </form>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>