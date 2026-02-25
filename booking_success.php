<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - BOOKING SUCCESS</title>
</head>

<body class="bg-light">

  <?php 
    require('inc/header.php');
    
    if(!isset($_GET['transaction_id'])){
      redirect('index.php');
    }
    
    $transaction_id = $_GET['transaction_id'];
    
    // Fetch payment and booking details
    $payment_res = select("SELECT p.*, b.*, r.name as room_name 
                          FROM `payments` p 
                          INNER JOIN `bookings` b ON p.booking_id = b.id 
                          INNER JOIN `rooms` r ON b.room_id = r.id 
                          WHERE p.transaction_id = ? AND b.user_id = ?", 
                          [$transaction_id, $_SESSION['uId']], "si");
    
    if(mysqli_num_rows($payment_res) == 0){
      redirect('index.php');
    }
    
    $data = mysqli_fetch_assoc($payment_res);
  ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-6">
        
        <div class="card shadow-sm text-center">
          <div class="card-body p-5">
            <div class="mb-4">
              <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
            </div>
            
            <h2 class="text-success mb-3">Payment Successful!</h2>
            <p class="text-muted mb-4">Your booking is pending admin approval</p>
            
            <div class="alert alert-success">
              <strong>Transaction ID:</strong> <?php echo $data['transaction_id']; ?>
            </div>
            
            <hr>
            
            <div class="text-start mt-4">
              <h5 class="mb-3">Booking Details</h5>
              <div class="row mb-2">
                <div class="col-6"><strong>Booking ID:</strong></div>
                <div class="col-6">#<?php echo $data['booking_id']; ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6"><strong>Room:</strong></div>
                <div class="col-6"><?php echo $data['room_name']; ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6"><strong>Guest Name:</strong></div>
                <div class="col-6"><?php echo $data['name']; ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6"><strong>Check-in:</strong></div>
                <div class="col-6"><?php echo date('d M Y', strtotime($data['checkin'])); ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6"><strong>Check-out:</strong></div>
                <div class="col-6"><?php echo date('d M Y', strtotime($data['checkout'])); ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6"><strong>Amount Paid:</strong></div>
                <div class="col-6 text-success fw-bold">₹<?php echo $data['amount']; ?></div>
              </div>
              <div class="row mb-2">
                <div class="col-6"><strong>Payment Method:</strong></div>
                <div class="col-6 text-capitalize"><?php echo $data['payment_method']; ?></div>
              </div>
            </div>
            
            <div class="mt-4">
              <a href="index.php" class="btn btn-primary me-2">Go to Home</a>
              <a href="rooms.php" class="btn btn-outline-secondary">Book Another Room</a>
            </div>
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

</body>
</html>
