<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <?php require('inc/links.php'); ?>
  <title><?php echo $settings_r['site_title'] ?> - MY BOOKINGS</title>
  <style>
    .h-line{
      width: 150px;
      margin: 0 auto;
      height: 1.7px;
      background-color: #000 !important;
    }
    .rating-stars {
      font-size: 2rem;
      color: #ddd;
      cursor: pointer;
    }
    .rating-stars .star.active {
      color: #ffc107;
    }
  </style>
</head>

<body class="bg-light d-flex flex-column min-vh-100">

  <?php 
    require('inc/header.php');
    
    if(!(isset($_SESSION['login']) && $_SESSION['login'] == true)){
      redirect('index.php');
    }
  ?>

  <div class="container flex-grow-1">
    <div class="row">

      <div class="col-12 my-5 px-4">
        <h2 class="fw-bold">MY BOOKINGS</h2>
        <div style="font-size: 14px;">
          <a href="index.php" class="text-secondary text-decoration-none">HOME</a>
          <span class="text-secondary"> > </span>
          <a href="#" class="text-secondary text-decoration-none">MY BOOKINGS</a>
        </div>
      </div>

      <div class="col-12 px-4">
        <div class="card border-0 shadow-sm mb-4">
          <div class="card-body">
            
            <div class="table-responsive">
              <table class="table table-hover border text-center" style="min-width: 1300px;">
                <thead>
                  <tr class="bg-dark text-light">
                    <th scope="col">#</th>
                    <th scope="col">Room Name</th>
                    <th scope="col">Check-in</th>
                    <th scope="col">Check-out</th>
                    <th scope="col">Amount</th>
                    <th scope="col">Booking Status</th>
                    <th scope="col">Payment Status</th>
                    <th scope="col">Date</th>
                    <th scope="col">Action</th>
                    <th scope="col">Invoice</th>
                  </tr>
                </thead>
                <tbody id="bookings-data">
                </tbody>
              </table>
            </div>

          </div>
        </div>
      </div>

    </div>
  </div>

  <!-- Review Modal -->
  <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <form id="review-form">
          <div class="modal-header">
            <h5 class="modal-title">Write Review</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
          </div>
          <div class="modal-body">
            <input type="hidden" name="booking_id" id="review_booking_id">
            <input type="hidden" name="room_id" id="review_room_id">
            
            <div class="mb-3">
              <label class="form-label">Rating</label>
              <div class="rating-stars">
                <i class="bi bi-star-fill star" data-rating="1"></i>
                <i class="bi bi-star-fill star" data-rating="2"></i>
                <i class="bi bi-star-fill star" data-rating="3"></i>
                <i class="bi bi-star-fill star" data-rating="4"></i>
                <i class="bi bi-star-fill star" data-rating="5"></i>
              </div>
              <input type="hidden" name="rating" id="rating_value" required>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Review</label>
              <textarea name="review" class="form-control shadow-none" rows="4" required></textarea>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="submit" class="btn btn-primary">Submit Review</button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <?php require('inc/footer.php'); ?>

  <script>
    function get_bookings() 
    {
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/my_bookings.php", true);
      xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

      xhr.onload = function () {
        document.getElementById('bookings-data').innerHTML = this.responseText;
      };

      xhr.send('get_bookings');
    }

    function request_refund(id) {
      if (confirm("Are you sure you want to request a refund? Admin will review your request.")) {
        console.log('Requesting refund for booking ID:', id);
        
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "ajax/my_bookings.php", true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

        xhr.onload = function () {
          console.log('Raw Refund Response:', this.responseText);
          console.log('Response length:', this.responseText.length);
          console.log('Response trimmed:', this.responseText.trim());
          
          let response = this.responseText.trim();
          
          if (response == '1') {
            alert('success', 'Refund request submitted successfully! Admin will review it.');
            get_bookings();
          } else if (response == 'already_requested') {
            alert('error', 'Refund already requested for this booking!');
          } else if (response == 'not_found') {
            alert('error', 'Booking not found!');
          } else if (response == 'payment_not_paid') {
            alert('error', 'Payment is not completed yet!');
          } else if (response == 'invalid_status') {
            alert('error', 'Booking status does not allow refund!');
          } else {
            console.error('Unexpected response:', response);
            alert('error', 'Refund request failed! Check console for details.');
          }
        }
        
        xhr.onerror = function() {
          console.error('Network error occurred');
          alert('error', 'Network error! Please try again.');
        }

        xhr.send('request_refund=' + id);
      }
    }

    function open_review_modal(booking_id, room_id) {
      document.getElementById('review_booking_id').value = booking_id;
      document.getElementById('review_room_id').value = room_id;
      document.getElementById('rating_value').value = '';
      document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
      document.querySelector('textarea[name="review"]').value = '';
      
      let modal = new bootstrap.Modal(document.getElementById('reviewModal'));
      modal.show();
    }

    // Star rating functionality
    document.querySelectorAll('.star').forEach(star => {
      star.addEventListener('click', function() {
        let rating = this.getAttribute('data-rating');
        document.getElementById('rating_value').value = rating;
        
        document.querySelectorAll('.star').forEach(s => s.classList.remove('active'));
        for(let i = 1; i <= rating; i++) {
          document.querySelector(`.star[data-rating="${i}"]`).classList.add('active');
        }
      });
    });

    // Submit review
    document.getElementById('review-form').addEventListener('submit', function(e) {
      e.preventDefault();
      
      if(!document.getElementById('rating_value').value) {
        alert('error', 'Please select a rating!');
        return;
      }
      
      let formData = new FormData(this);
      formData.append('submit_review', '');
      
      let xhr = new XMLHttpRequest();
      xhr.open("POST", "ajax/submit_review.php", true);
      
      xhr.onload = function() {
        if(this.responseText == 1) {
          alert('success', 'Review submitted successfully!');
          bootstrap.Modal.getInstance(document.getElementById('reviewModal')).hide();
          get_bookings();
        } else if(this.responseText == 'already_reviewed') {
          alert('error', 'You have already reviewed this booking!');
        } else {
          alert('error', 'Failed to submit review!');
        }
      }
      
      xhr.send(formData);
    });

    window.onload = function () {
      get_bookings();
    }
  </script>

</body>
</html>
