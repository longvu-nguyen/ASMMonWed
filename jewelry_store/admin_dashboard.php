
     <?php
     session_start();
     include 'includes/db.php';
     include 'includes/head.php';
     include 'includes/header.php';

   

     // Lấy dữ liệu thống kê với kiểm tra lỗi
     $total_users = 0;
     $result = $conn->query("SELECT COUNT(*) FROM users");
     if ($result && $result->num_rows > 0) {
         $total_users = $result->fetch_row()[0];
     }

     $total_products = 0;
     $result = $conn->query("SELECT COUNT(*) FROM products");
     if ($result && $result->num_rows > 0) {
         $total_products = $result->fetch_row()[0];
     }

     $total_orders = 0;
     $result = $conn->query("SELECT COUNT(*) FROM orders");
     if ($result && $result->num_rows > 0) {
         $total_orders = $result->fetch_row()[0];
     }

     $total_revenue = 0;
     $result = $conn->query("SELECT SUM(total) FROM orders");
     if ($result && $result->num_rows > 0) {
         $row = $result->fetch_row();
         $total_revenue = $row[0] ?? 0;
     }

     $total_sold = 0;
     $result = $conn->query("SELECT SUM(sold_count) FROM products");
     if ($result && $result->num_rows > 0) {
         $total_sold = $result->fetch_row()[0] ?? 0;
     }
     ?>

     <div class="container mt-5">
         <h2 class="mb-4">📈 Thống kê tổng quan</h2>
         <div class="row">
             <div class="col-md-3">
                 <div class="card bg-primary text-white">
                     <div class="card-body">
                         👥 Người dùng<br>
                         <h3><?= $total_users ?></h3>
                     </div>
                 </div>
             </div>
             <div class="col-md-3">
                 <div class="card bg-success text-white">
                     <div class="card-body">
                         💎 Sản phẩm<br>
                         <h3><?= $total_products ?></h3>
                     </div>
                 </div>
             </div>
             <div class="col-md-3">
                 <div class="card bg-warning text-white">
                     <div class="card-body">
                         🧾 Đơn hàng<br>
                         <h3><?= $total_orders ?></h3>
                     </div>
                 </div>
             </div>
             <div class="col-md-3">
                 <div class="card bg-danger text-white">
                     <div class="card-body">
                         💰 Doanh thu<br>
                         <h3><?= number_format($total_revenue, 0, ',', '.') ?> đ</h3>
                     </div>
                 </div>
             </div>
             <div class="col-md-3 mt-4">
                 <div class="card bg-info text-white">
                     <div class="card-body">
                         📦 Tổng số bán<br>
                         <h3><?= number_format($total_sold, 0, ',', '.') ?></h3>
                     </div>
                 </div>
             </div>
         </div>
     </div>

     <?php include 'includes/footer.php'; ?>
     