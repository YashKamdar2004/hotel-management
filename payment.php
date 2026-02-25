<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - PAYMENT</title>
  <style>
    .payment-option {
      cursor: pointer;
      transition: all 0.3s;
      border: 2px solid #e0e0e0;
    }
    .payment-option:hover {
      border-color: #2ec1ac;
      transform: translateY(-2px);
    }
    .payment-option.active {
      border-color: #2ec1ac;
      background-color: #f0fffe;
    }
  </style>
</head>

<body class="bg-light">

  <?php 
    require('inc/header.php');
    
    if(!isset($_GET['booking_id']) || !isset($_SESSION['booking_id'])){
      redirect('rooms.php');
    }
    
    $booking_id = $_GET['booking_id'];
    
    // Fetch booking details
    $booking_res = select("SELECT b.*, r.name as room_name FROM `bookings` b 
                          INNER JOIN `rooms` r ON b.room_id = r.id 
                          WHERE b.id = ? AND b.user_id = ?", 
                          [$booking_id, $_SESSION['uId']], "ii");
    
    if(mysqli_num_rows($booking_res) == 0){
      redirect('rooms.php');
    }
    
    $booking_data = mysqli_fetch_assoc($booking_res);
  ?>

  <div class="container my-5">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        
        <div class="card shadow-sm mb-4">
          <div class="card-body">
            <h4 class="card-title mb-3">Booking Summary</h4>
            <div class="row">
              <div class="col-md-6">
                <p><strong>Room:</strong> <?php echo $booking_data['room_name']; ?></p>
                <p><strong>Guest Name:</strong> <?php echo $booking_data['name']; ?></p>
                <p><strong>Phone:</strong> <?php echo $booking_data['phonenum']; ?></p>
              </div>
              <div class="col-md-6">
                <p><strong>Check-in:</strong> <?php echo date('d M Y', strtotime($booking_data['checkin'])); ?></p>
                <p><strong>Check-out:</strong> <?php echo date('d M Y', strtotime($booking_data['checkout'])); ?></p>
                <p><strong>Total Amount:</strong> <span class="text-success fw-bold">₹<?php echo $booking_data['total_amount']; ?></span></p>
              </div>
            </div>
          </div>
        </div>

        <div class="card shadow-sm">
          <div class="card-body">
            <h4 class="card-title mb-4">Select Payment Method</h4>
            
            <div class="row mb-4">
              <div class="col-md-4 mb-3">
                <div class="payment-option card p-3 text-center" data-method="card">
                  <i class="bi bi-credit-card fs-1 text-primary"></i>
                  <h6 class="mt-2">Credit/Debit Card</h6>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <div class="payment-option card p-3 text-center" data-method="upi">
                  <i class="bi bi-phone fs-1 text-success"></i>
                  <h6 class="mt-2">UPI</h6>
                </div>
              </div>
              <div class="col-md-4 mb-3">
                <div class="payment-option card p-3 text-center" data-method="netbanking">
                  <i class="bi bi-bank fs-1 text-info"></i>
                  <h6 class="mt-2">Net Banking</h6>
                </div>
              </div>
            </div>

            <form id="payment_form">
              <input type="hidden" name="booking_id" value="<?php echo $booking_id; ?>">
              <input type="hidden" name="payment_method" id="payment_method" value="">
              
              <!-- Card Payment Fields -->
              <div id="card_fields" class="payment-fields d-none">
                <h6 class="mb-3">Enter Card Details</h6>
                <div class="mb-3">
                  <label class="form-label">Card Number</label>
                  <input type="text" class="form-control" placeholder="1234 5678 9012 3456" maxlength="19">
                </div>
                <div class="row">
                  <div class="col-md-6 mb-3">
                    <label class="form-label">Expiry Date</label>
                    <input type="text" class="form-control" placeholder="MM/YY" maxlength="5">
                  </div>
                  <div class="col-md-6 mb-3">
                    <label class="form-label">CVV</label>
                    <input type="text" class="form-control" placeholder="123" maxlength="3">
                  </div>
                </div>
              </div>

              <!-- UPI Fields -->
              <div id="upi_fields" class="payment-fields d-none">
                <h6 class="mb-3">Enter UPI ID</h6>
                <div class="mb-3">
                  <label class="form-label">UPI ID</label>
                  <input type="text" class="form-control" placeholder="yourname@upi">
                </div>
              </div>

              <!-- Net Banking Fields -->
              <div id="netbanking_fields" class="payment-fields d-none">
                <h6 class="mb-3">Select Your Bank</h6>
                <div class="mb-3">
                  <select class="form-select">
                    <option value="">Choose Bank</option>
                    <option>State Bank of India</option>
                    <option>HDFC Bank</option>
                    <option>ICICI Bank</option>
                    <option>Axis Bank</option>
                    <option>Punjab National Bank</option>
                  </select>
                </div>
              </div>

              <button type="submit" class="btn btn-success w-100 mt-3" id="pay_btn" disabled>
                <span id="btn_text">Select Payment Method</span>
                <span id="btn_loader" class="spinner-border spinner-border-sm d-none"></span>
              </button>
            </form>

          </div>
        </div>

      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  <script>
    let payment_method = '';
    
    // Payment option selection
    document.querySelectorAll('.payment-option').forEach(option => {
      option.addEventListener('click', function() {
        document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
        this.classList.add('active');
        
        payment_method = this.getAttribute('data-method');
        document.getElementById('payment_method').value = payment_method;
        
        // Hide all payment fields
        document.querySelectorAll('.payment-fields').forEach(field => field.classList.add('d-none'));
        
        // Show selected payment fields
        document.getElementById(payment_method + '_fields').classList.remove('d-none');
        
        // Enable pay button
        document.getElementById('pay_btn').disabled = false;
        document.getElementById('btn_text').innerText = 'Pay ₹<?php echo $booking_data['total_amount']; ?>';
      });
    });

    // Handle payment form submission
    document.getElementById('payment_form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      if(payment_method === '') {
        alert('Please select a payment method');
        return;
      }
      
      let pay_btn = document.getElementById('pay_btn');
      let btn_text = document.getElementById('btn_text');
      let btn_loader = document.getElementById('btn_loader');
      
      pay_btn.disabled = true;
      btn_text.classList.add('d-none');
      btn_loader.classList.remove('d-none');
      
      let formData = new FormData(this);
      
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "process_payment.php", true);
      
      xhr.onload = function() {
        console.log('Raw Response:', this.responseText);
        
        try {
          let response = JSON.parse(this.responseText);
          console.log('Parsed Response:', response);
          
          if(response.status === 'success') {
            window.location.href = 'booking_success.php?transaction_id=' + response.transaction_id;
          } else {
            window.location.href = 'booking_failed.php?booking_id=' + response.booking_id;
          }
        } catch(e) {
          console.error('Parse Error:', e);
          console.error('Response:', this.responseText);
          alert('error', 'Payment processing failed. Please try again.');
          pay_btn.disabled = false;
          btn_text.classList.remove('d-none');
          btn_loader.classList.add('d-none');
        }
      };
      
      xhr.onerror = function() {
        alert('error', 'Network error. Please check your connection.');
        pay_btn.disabled = false;
        btn_text.classList.remove('d-none');
        btn_loader.classList.add('d-none');
      };
      
      xhr.send(formData);
    });
  </script>

</body>
</html>
