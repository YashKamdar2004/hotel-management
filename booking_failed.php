<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - PAYMENT FAILED</title>
</head>

<body class="bg-light">

  <?php require('inc/header.php'); ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        <div class="card shadow-sm text-center">
          <div class="card-body p-5">
            <i class="bi bi-x-circle text-danger" style="font-size: 5rem;"></i>
            <h2 class="mt-4 text-danger">Payment Failed!</h2>
            <p class="text-muted mt-3">Unfortunately, your payment could not be processed. Your booking has been cancelled.</p>
            
            <?php if(isset($_GET['booking_id'])): ?>
              <p class="mt-3"><strong>Booking ID:</strong> #<?php echo $_GET['booking_id']; ?></p>
            <?php endif; ?>
            
            <div class="mt-4">
              <a href="rooms.php" class="btn btn-primary me-2">Browse Rooms</a>
              <a href="my_bookings.php" class="btn btn-outline-secondary">My Bookings</a>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

</body>
</html>
