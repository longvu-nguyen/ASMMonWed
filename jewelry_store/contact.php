<?php
include 'includes/header.php';
include 'includes/head.php';

?>

<div class="container mt-5 mb-5">
  <h2 class="text-center mb-4">📞 Liên hệ với chúng tôi</h2>

  <div class="row">
    <div class="col-md-6 mb-4">
      <h5 class="fw-bold">Thông tin liên hệ</h5>
      <p>🏢 Địa chỉ: TDP Phú Đa, Thị Xã Mỹ Hào, Tỉnh Hưng Yên</p>
      <p>📞 Số điện thoại: 0385225005</p>
      <p>✉️ Email: longvu14090@gmail.com</p>
      <p>🕐 Giờ làm việc: 8h - 18h (T2 - T7)</p>

      <iframe class="w-100" height="250" style="border:0;" loading="lazy" allowfullscreen
        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3724.0992044479975!2d105.78003607471502!3d21.02851138723588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3135ab4ef03ce89f%3A0x9fe7bdc31690eae2!2zMTIzIFRyYW5nIFPhur9jLCBIw6AgTuG7mWksIEjDoCBO4buZaQ!5e0!3m2!1sen!2s!4v1623423412345">
      </iframe>
    </div>

    <div class="col-md-6">
      <h5 class="fw-bold">Gửi phản hồi</h5>
      <form method="post" action="">
        <div class="mb-3">
          <label for="name" class="form-label">Họ tên</label>
          <input type="text" class="form-control" id="name" name="name" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email của bạn</label>
          <input type="email" class="form-control" id="email" name="email" required>
        </div>
        <div class="mb-3">
          <label for="message" class="form-label">Nội dung</label>
          <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Gửi liên hệ</button>
      </form>

      <?php
      // Phản hồi sau khi gửi
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
          echo '<div class="alert alert-success mt-3">Cảm ơn bạn đã liên hệ! Chúng tôi sẽ phản hồi sớm nhất.</div>';
      }
      ?>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
